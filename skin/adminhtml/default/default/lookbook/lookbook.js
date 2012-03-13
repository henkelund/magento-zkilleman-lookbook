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

/**
 * There is a bug in script.aculo.us dragdrop.js v1.8.2 so we need to
 * overwrite the register function (taken from v1.9.0)
 * 
 */
Draggables.register = function(draggable) {
    if(this.drags.length == 0) {
        this.eventMouseUp   = this.endDrag.bindAsEventListener(this);
        this.eventMouseMove = this.updateDrag.bindAsEventListener(this);
        this.eventKeypress  = this.keyPress.bindAsEventListener(this);

        Event.observe(document, "mouseup", this.eventMouseUp);
        Event.observe(document, "mousemove", this.eventMouseMove);
        Event.observe(document, "keypress", this.eventKeypress);
    }
    this.drags.push(draggable);
};

/**
 * Class LookbookCrosshair
 * 
 */
var LookbookCrosshair = Class.create();
LookbookCrosshair.prototype = {
    
    /**
     *
     * @var _config Default config values
     */
    _config: {
        imageId:  'lookbookImage',
        focusXId: 'lookbookImageFocusX',
        focusYId: 'lookbookImageFocusY',
        title:    'Double click to center focus'
    },
    
    x: 0.5,
    
    y: 0.5,
    
    _elem: null,
    
    _draggable: null,
    
    /**
     * Constructor
     *
     * @param config Configuration options object
     */
    initialize: function(config)
    {
        if (typeof config == 'object') {
            for (var key in config) {
                this._config[key] = config[key];
            }
        }
        this.x = parseFloat($(this._config.focusXId).value);
        this.y = parseFloat($(this._config.focusYId).value);
        this._elem = new Element('div', {title: this._config.title});
        this._elem.addClassName('focusCrosshair');
        this._elem.ondblclick = this.center.bind(this);
        this.getImageElem().up().insert(this._elem);
        this._draggable = 
            new Draggable(this._elem, {onEnd: this.dragEnded.bind(this)});
        Event.observe(window, 'resize', this.draw.bind(this));
        this.draw();
    },
    
    getImageElem: function()
    {
        return $(this._config.imageId);
    },
    
    _getMetrics: function(elem)
    {
        var elemPos = elem.cumulativeOffset();
        var elemDim = elem.getDimensions();
        return {
            top:    elemPos.top,
            left:   elemPos.left,
            width:  elemDim.width,
            height: elemDim.height
        }
    },
    
    draw: function()
    {
        var image = this._getMetrics(this.getImageElem());
        var self = this._getMetrics(this._elem);
        var targetPos = {
            x: (this.x*image.width) - (self.width/2),
            y: (this.y*image.height) - (self.height/2)
        };
        this._elem.style.left = parseInt(targetPos.x) + 'px';
        this._elem.style.top = parseInt(targetPos.y) + 'px';
    },
    
    dragEnded: function()
    {
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
    
    center: function()
    {
        this.x = 0.5;
        this.y = 0.5;
        $(this._config.focusXId).value = this.x.toFixed(4);
        $(this._config.focusYId).value = this.y.toFixed(4);
        this.draw();
    }
};

/**
 * Class LookbookTag
 * 
 */
var LookbookTag = Class.create();
LookbookTag.instanceCount = 0;
LookbookTag.prototype = {
    
    /**
     *
     * @var _config Default config values
     */
    _config: {
        tagListId:   'lookbookTagList',
        imageId:     'lookbookImage',
        fieldName:   'file',
        removeTitle: 'Remove tag'
    },
    
    _data: {x: -1, y: -1},
    
    _elem: null,
    
    _image: null,
    
    _inputElems: {},
    
    _draggable: null,
    
    /**
     * Constructor
     *
     * @param data   Tag data object
     * @param config Configuration options object
     */
    initialize: function(data, config)
    {
        if (typeof data == 'object') {
            for (var dkey in data) {
                this._data[dkey] = data[dkey];
            }
        }
        if (typeof config == 'object') {
            for (var ckey in config) {
                this._config[ckey] = config[ckey];
            }
        }
        this._config.index = LookbookTag.instanceCount++;
        this._createElement();
        this._createInputElements();
        this._image = $(this._config.imageId);
        $(this._config.tagListId)
            .select('li.input-container').first()
            .insert({before: this._elem});
        this._draggable = new Draggable(this._elem, {
            onStart: this.dragStarted.bind(this),
            onEnd: this.dragEnded.bind(this)
        });
        this._initPosition();
    },
    
    _createElement: function()
    {
        // Create root LI element
        this._elem = new Element('li');
        this._elem.addClassName('entry');
        // Add remove button
        var removeButton = new Element('span', {title: this._config.removeTitle});
        removeButton.addClassName('remove');
        removeButton.observe('click', this.remove.bind(this));
        this._elem.appendChild(removeButton);
        // Add name label
        var nameLabel = new Element('span');
        nameLabel.addClassName('name');
        nameLabel.insert(this._data.name);
        this._elem.appendChild(nameLabel);
    },
    
    _getFieldId: function(name)
    {
        return this._config.fieldName + '_' + this._config.index + '_' + name;
    },
    
    _createInputElements: function()
    {
        for (var name in this._data) {
            this._inputElems[name] = new Element('input', {
                id:    this._getFieldId(name),
                type:  'hidden',
                name:  this._config.fieldName + 
                            '[' + this._config.index + '][' + name + ']',
                value: this._data[name]
            });
        }
        for (var elem in this._inputElems) {
            this._elem.insert({bottom: this._inputElems[elem]});
        }
    },
    
    _getMetrics: function(elem)
    {
        var elemPos = elem.cumulativeOffset();
        var elemDim = elem.getDimensions();
        return {
            top:    elemPos.top,
            left:   elemPos.left,
            width:  elemDim.width,
            height: elemDim.height
        }
    },
    
    _initPosition: function()
    {
        if (this._data.x < 0 || this._data.x > 1 ||
                this._data.y < 0 || this._data.y > 1) {
            return;
        }
        this._elem.style.position = 'absolute';
        var image = this._getMetrics(this._image);
        var tag = this._getMetrics(this._elem);
        var targetPos = {
            x: (this._data.x*image.width) + image.left - (tag.width/2),
            y: (this._data.y*image.height) + image.top - (tag.height/2)
        };
        var delta = {
            x: targetPos.x - tag.left,
            y: targetPos.y - tag.top
        };
        this._elem.style.left = parseInt(delta.x) + 'px';
        this._elem.style.top = parseInt(delta.y) + 'px';
    },
    
    dragStarted: function()
    {
        this._elem.style.position = 'absolute';
    },
    
    dragEnded: function()
    {
        var image = this._getMetrics(this._image);
        var tag = this._getMetrics(this._elem);
        var x = (((tag.left + tag.width/2) - image.left)/image.width);
        var y = (((tag.top + tag.height/2) - image.top)/image.height);
        if (x < 0 || x > 1 || y < 0 || y > 1) {
            this._elem.style.position = 'relative';
            this._elem.style.top = '0px';
            this._elem.style.left = '0px';
        } else {
            // update fields
        }
    },
    
    remove: function()
    {
        this._draggable.destroy();
        this._elem.remove();
    }
};

/**
 * Class LookbookTagInput
 * 
 */
var LookbookTagInput = Class.create();
LookbookTagInput.prototype = {
    
    /**
     *
     * @var _config Default config values
     */
    _config: {
        tagListId: 'lookbookTagList',
        imageId:   'lookbookImage',
        fieldName: 'file'
    },
    
    _listElem: null,
    
    _inputElem: null,
    
    _suggestions: new Array(),
    
    _suggestionMenuElem: null,
    
    _suggestionRequest: null,
    
    /**
     * Constructor
     *
     * @param config Configuration options object
     */
    initialize: function(config)
    {
        if (typeof config == 'object') {
            for (var key in config) {
                this._config[key] = config[key];
            }
        }
        this._listElem = $(this._config.tagListId);
        this._inputElem = new Element('input', {size: '1'});
        this._inputElem.observe('keyup', this.keyReleased.bind(this));
        var containerElem = new Element('li');
        containerElem.addClassName('input-container');
        containerElem.insert(this._inputElem);
        this._suggestionMenuElem = new Element('ul');
        this._suggestionMenuElem.addClassName('suggestion-menu');
        containerElem.insert(this._suggestionMenuElem);
        this._listElem.insert(containerElem);
    },
    
    keyReleased: function(event)
    {
        if (this._suggestionRequest && !this._suggestionRequest._complete) {
            this._suggestionRequest.transport.abort();
        }
        var text = this._inputElem.value.replace(/^\s+/, ''); // ltrim
        if (text.length == 0) {
            this._inputElem.value = '';
            return;
        }
        switch (event.keyCode) {
            case Event.KEY_DOWN:
            case Event.KEY_UP:
                //alert('scroll');
                break;
            case Event.KEY_RETURN:
                this.createTag();
                break;
            case Event.KEY_LEFT:
            case Event.KEY_RIGHT:
                // no special action
                break;
            default:
                var openStringPattern = /^((\"[^\"]*)|([^\"]\S*))$/;
                if (!openStringPattern.test(text)) {
                    this.createTag();
                } else {
                    this._inputElem.value = text;
                    this._inputElem.setAttribute('size', Math.max(1, text.length));
                    var url = this._config.suggestionUrl;
                    var self = this;
                    this._suggestionRequest = new Ajax.Request(url, {
                        parameters: {q: text},
                        onSuccess: function(transport) {
                            if (transport.responseText.isJSON()) {
                                var response = transport.responseText.evalJSON();
                                self._suggestions = response;
                                self.buildSuggestionMenu().bind(self);
                            }
                        },
                        onComplete: function() {
                            self._suggestionRequest = null;
                        }
                    });
                }
                break;
        }
    },
    
    createTag: function()
    {
        var text = this._inputElem.value.replace(/^[\s\"]+|[\s\"]+$/g, '');
        new LookbookTag({
            name: text
        }, {
            tagListId: this._config.tagListId,
            imageId:   this._config.imageId,
            fieldName: this._config.fieldName
        });
        this._inputElem.value = '';
        this._inputElem.focus();
    },
    
    buildSuggestionMenu: function()
    {
        self = this;
        var menu = this._suggestionMenuElem;
        menu.update('');
        for (var i = 0; i < this._suggestions.length; ++i) {
            var li = new Element('li', {rel: this._suggestions[i].name});
            li.update(this._suggestions[i].html);
            li.observe('click', function() {
                self._suggestions = new Array();
                self.buildSuggestionMenu();
                self._inputElem.value = this.getAttribute('rel');
                self.createTag();
            });
            menu.insert(li);
        }
    }
};
