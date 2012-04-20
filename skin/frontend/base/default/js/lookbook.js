/**
 * Zkilleman_Lookbook
 *
 * Copyright (C) 2012 Henrik Hedelund (henke.hedelund@gmail.com)
 *
 * This file is part of Zkilleman_Lookbook.
 *
 * Zkilleman_Lookbook is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Zkilleman_Lookbook is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Zkilleman_Lookbook. If not, see <http://www.gnu.org/licenses/>.
 *
 * @category Zkilleman
 * @package Zkilleman_Lookbook
 * @author Henrik Hedelund <henke.hedelund@gmail.com>
 * @copyright 2012 Henrik Hedelund (henke.hedelund@gmail.com)
 * @license http://www.gnu.org/licenses/gpl.html GNU GPL
 * @link https://github.com/henkelund/magento-zkilleman-lookbook
 */

Element.addMethods({
    bounds: function(element) {
        element = $(element);
        var elemPos  = element.cumulativeOffset();
        var elemRPos = element.positionedOffset();
        var elemDim  = element.getDimensions();
        return {
            top:    elemPos.top,
            left:   elemPos.left,
            rTop:   elemRPos.top,
            rLeft:  elemRPos.left,
            width:  elemDim.width,
            height: elemDim.height
        };
    }
});

var LookbookSlideshow = Class.create();
LookbookSlideshow.prototype = {
    _elem:     null,
    _items:    null,
    _numFakes: null,
    _current:  -1,
    _interval: null,
    _options:  null,
    initialize: function(id, options)
    {
        if (!(this._elem = $(id))) {
            return;
        }
        this._items = this._elem.select('.item');
        if (!this._items.length) {
            return;
        }
        this._numFakes = this._elem.select('.item.fake').length;
        this._initOptions(options);
        this._initEvents();
        this.slide('right', 0);
        this.start();
    },
    _initOptions: function(options)
    {
        this._options = {
            interval       : 5000,
            direction      : 'right',
            effectDuration : 0.5
        };
        if (typeof options == 'object') {
            for (var key in options) {
                this._options[key] = options[key];
            }
        }
    },
    _initEvents: function()
    {
        this._elem.observe('mouseover', this.stop.bind(this));
        this._elem.observe('mouseout', this.start.bind(this));
        var self = this;
        this._elem.next('button.next').observe('click', function() {
            self.stop();
            self.slide('right');
            self.start();
        });
        this._elem.next('button.previous').observe('click', function() {
            self.stop();
            self.slide('left');
            self.start();
        });
    },
    _targetOffset: function(item)
    {
        var iBounds = item.bounds();
        var iCenter = iBounds.left + (iBounds.width/2);
        var eBounds = this._elem.bounds();
        var eCenter = eBounds.left + (eBounds.width/2);
        return parseInt(eCenter - iCenter);
    },
    slide: function(direction, duration)
    {
        if (typeof direction == 'undefined' || direction != 'left') {
            direction = 'right';
        }
        if (typeof duration == 'undefined') {
            duration = this._options.effectDuration;
        }
        var numItems = this._items.length - this._numFakes;
        var expectedCurrent =
                        this._numFakes/2 +
                        (this._current - this._numFakes/2 + numItems)%numItems;
        if (this._current != expectedCurrent) { // on a fake
            this._current = expectedCurrent;
            var realItem = this._items[this._current];
            this._elem.style.left = this._targetOffset(realItem) + 'px';
        }
        var item;
        if (direction == 'right') {
            item = this._items[this._current++].next('.item');
        } else {
            item = this._items[this._current--].previous('.item');
        }
        var offset = this._targetOffset(item);
        var elem = this._elem;
        new Effect.Move(elem, {
            queue: {scope: elem.identify()},
            duration: duration,
            x: offset,
            mode: 'absolute'
        });
    },
    start: function()
    {
        var intervalFunction = this.slide.bind(this);
        var direction        = this._options.direction;
        var interval         = this._options.interval;
        this._interval       = window.setInterval(
                                    function() {
                                        intervalFunction(direction);
                                    }, interval);
    },
    stop: function()
    {
        if (this._interval) {
            window.clearInterval(this._interval);
            this._interval = null;
        }
    }
};

var LookbookOverlayBarImage = Class.create();
LookbookOverlayBarImage.prototype = {
    _options: null,
    _elem:    null,
    _img:     null,
    _bar:     null,
    initialize: function(elem, options)
    {
        this._initOptions(options);
        this._elem = $(elem);
        this._elem.style = 'relative';
        this._img = this._elem.down('img');
        this._bar = this._elem.down('.bar');
        this._initTags();
        this._initEvents();
    },
    _initOptions: function(options)
    {
        this._options = {
            effectDuration: 0.25
        };
        if (typeof options == 'object') {
            for (var key in options) {
                this._options[key] = options[key];
            }
        }
    },
    _initTags: function()
    {
        var imgBounds = this._img.bounds();
        this._elem.select('.positioned-tag').each(function(tag) {
            var y = parseFloat(tag.getAttribute('data-y'))*imgBounds.height;
            var x = parseFloat(tag.getAttribute('data-x'))*imgBounds.width;
            var bounds = tag.bounds();
            y -= bounds.height/2;
            x -= bounds.width/2;
            tag.style.top  = parseInt(y) + 'px';
            tag.style.left = parseInt(x) + 'px';
            tag.hide();
        });
    },
    _initEvents: function()
    {
        var elem = this._elem;
        var bar = this._bar;
        var tagsEffectScope = elem.identify() + '_tags';
        var barEffectScope = elem.identify() + '_bar';
        var duration = parseFloat(this._options.effectDuration);
        this._img.observe('mouseenter', function() {
            Effect.Queues.get(tagsEffectScope).invoke('cancel');
            elem.select('.positioned-tag').each(function(tag) {
                new Effect.Appear(tag, {
                    queue: {scope: tagsEffectScope},
                    duration: duration
                });
            });
            Effect.Queues.get(barEffectScope).invoke('cancel');
            new Effect.Morph(bar, {
                queue: {scope: barEffectScope},
                duration: duration,
                style: 'bottom: -' + bar.bounds().height + 'px'
            });
        });
        elem.observe('mouseleave', function() {
            Effect.Queues.get(tagsEffectScope).invoke('cancel');
            elem.select('.positioned-tag').each(function(tag) {
                new Effect.Fade(tag, {
                    queue: {scope: tagsEffectScope},
                    duration: duration
                });
            });
            Effect.Queues.get(barEffectScope).invoke('cancel');
            new Effect.Morph(bar, {
                queue: {scope: barEffectScope},
                duration: duration,
                style: 'bottom: 0px'
            });
        });
    }
}

var LookbookMasonry = Class.create();
LookbookMasonry.prototype = {
    _canvas: null,
    _bricks: null,
    initialize: function(canvas)
    {
        this._canvas = $(canvas);
        this._canvas.style.position = 'relative';
        this._bricks = [];
        self = this;
        this._canvas.select('div.brick').each(function(elem) {
            self._bricks.push(self._createBrick(elem));
        });
    },
    _createBrick: function(elem)
    {
        elem = $(elem);
        var bounds = elem.bounds();
        var hrb = elem.hasAttribute('data-hrb') ?
                    parseInt(elem.getAttribute('data-hrb')) : Math.pow(2, 16);
        return {
            elem:   elem,
            top:    bounds.rTop,
            left:   bounds.rLeft,
            width:  bounds.width,
            height: bounds.height,
            hrb:    hrb
        };
    },
    _intersects: function(a, b)
    {
        if (b instanceof Array) {
            for (var i = 0; i < b.length; ++i) {
                if (this._intersects(a, b[i])) {
                    return b[i];
                }
            }
            return false;
        }
        return !(
            b.top  >= a.top  + a.height  ||
            b.left >= a.left + a.width   ||
            b.left + b.width  <= a.left  ||
            b.top  + b.height <= a.top
        ) ? b : false;
    },
    _getBricksBelow: function(y)
    {
        var lower = [];
        for (var i = 0; i < this._bricks.length; ++i) {
            if (this._bricks[i].top + this._bricks[i].height >= y) {
                lower.push(this._bricks[i]);
            }
        }
        return lower;
    },
    getBrickCount: function()
    {
        return this._bricks.length;
    },
    append: function(elem, animate)
    {
        elem = $(elem);
        elem.setStyle({position: 'absolute'});
        this._canvas.insert(elem);
        
        var brick = this._createBrick(elem),
            lowerBricks,
            intersector,
            canvasWidth = this._canvas.bounds().width;
            
        elem.hide();
            
        for (var i = this._bricks.length - 1; i >= 0; --i) {
            if (brick.width >= this._bricks[i].width &&
                        brick.height >= this._bricks[i].height) {
                brick.top = this._bricks[i].top;
                brick.hrb = this._bricks[i].hrb;
                break;
            }
        }
        
        lowerBricks = this._getBricksBelow(brick.top);
        
        while (false !== (intersector = this._intersects(brick, lowerBricks))) {
            brick.left = intersector.left + intersector.width;
            brick.hrb  = Math.min(brick.hrb, intersector.top + intersector.height);
            
            if (brick.left + brick.width > canvasWidth) {
                brick.top  = brick.hrb;
                brick.left = 0;
                brick.hrb  = Math.pow(2, 16);
            }
        }
        
        elem.setStyle({
            top:  brick.top + 'px',
            left: brick.left + 'px'
        });
        elem.setAttribute('data-hrb', brick.hrb);
        
        var canvasHeight = parseInt(this._canvas.style.maxHeight) || 0;
        var brickBottom  = brick.top + brick.height;
        if (brickBottom > canvasHeight) {
            this._canvas.style.maxHeight = brickBottom + 'px';
            Effect.Queues.get(this._canvas.identify()).invoke('cancel');
            if (animate) {
                new Effect.Morph(this._canvas, {
                    duration: 0.5,
                    queue: {scope: this._canvas.identify()},
                    style: 'height: ' + brickBottom + 'px'
                });
            } else {
                this._canvas.style.height = brickBottom + 'px';
            }
        }
        
        this._bricks.push(brick);
        
        if (animate) {
            elem.appear();
        } else {
            elem.show();
        }
    }
};