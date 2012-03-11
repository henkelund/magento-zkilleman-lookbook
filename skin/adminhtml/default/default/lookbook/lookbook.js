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

var LookbookCrosshair = Class.create();
LookbookCrosshair.prototype = {
    _config: {
        imageId: 'lookbookImage',
        focusXId: 'lookbookImageFocusX',
        focusYId: 'lookbookImageFocusY'
    },
    x: 0.5,
    y: 0.5,
    _elem: null,
    _draggable: null,
    initialize: function(config)
    {
        if (typeof config == 'object') {
            for (var key in config) {
                this._config[key] = config[key];
            }
        }
        this.x = parseFloat($(this._config.focusXId).value);
        this.y = parseFloat($(this._config.focusYId).value);
        this._elem = new Element('div');
        this._elem.addClassName('focusCrosshair');
        this._elem.ondblclick = this.center.bind(this);
        this.getImageElem().up().insert(this._elem);
        if (this.getImageElem().complete) {
            this.draw();
        } else {
            this.getImageElem().onload = this.draw.bind(this);
        }
        this._draggable = 
            new Draggable(this._elem, {onEnd: this.dragEnded.bind(this)});
        Event.observe(window, 'resize', this.draw.bind(this));
    },
    getImageElem: function() {
        return $(this._config.imageId);
    },
    _getMetrics: function(elem) {
        var elemPos = elem.cumulativeOffset();
        var elemDim = elem.getDimensions();
        return {
            top:    elemPos.top,
            left:   elemPos.left,
            width:  elemDim.width,
            height: elemDim.height
        }
    },
    draw: function() {
        var image = this._getMetrics(this.getImageElem());
        var self = this._getMetrics(this._elem);
        var targetPos = {
            x: (this.x*image.width) - (self.width/2),
            y: (this.y*image.height) - (self.height/2)
        };
        this._elem.style.left = parseInt(targetPos.x) + 'px';
        this._elem.style.top = parseInt(targetPos.y) + 'px';
    },
    dragEnded: function() {
        var image = this._getMetrics(this.getImageElem());
        var self = this._getMetrics(this._elem);
        this.x = (((self.left + self.width/2) - image.left)/image.width);
        this.y = (((self.top + self.height/2) - image.top)/image.height);
        this.x = Math.min(1, Math.max(0, this.x));
        this.y = Math.min(1, Math.max(0, this.y));
        $(this._config.focusXId).value = this.x.toFixed(4);
        $(this._config.focusYId).value = this.y.toFixed(4);
        this.draw();
    },
    center: function() {
        this.x = 0.5;
        this.y = 0.5;
        $(this._config.focusXId).value = this.x.toFixed(4);
        $(this._config.focusYId).value = this.y.toFixed(4);
        this.draw();
    }
};