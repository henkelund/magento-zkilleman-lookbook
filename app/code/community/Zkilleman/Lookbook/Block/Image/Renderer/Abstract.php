<?php
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

class Zkilleman_Lookbook_Block_Image_Renderer_Abstract
                                extends Mage_Core_Block_Template
{
    /**
     *
     * @var mixed array|null 
     */
    protected $_imageAttributes = null;
    /**
     *
     * @var mixed array|null 
     */
    protected $_imageStyleAttributes = null;
    
    /**
     *
     * @var string 
     */
    protected $_imageHtml = null;
    
    /**
     *
     * @var array 
     */
    protected $_imageBounds = null;
    
    /**
     * Give subclasses a chance to update image attributes before render
     *
     * @return void 
     */
    protected function _beforeGetImageHtml() {}
    
    /**
     *
     * @param  bool $nocache
     * @return string 
     */
    public function getImageHtml($nocache = false)
    {
        if ($this->_imageHtml === null || $nocache || $this->getNoImageHtmlCache()) {
            $this->_beforeGetImageHtml();
            $image = $this->getImage();
            $this->_imageHtml = $image ? 
                    $image->getHtml(
                            $this->getWidth(),
                            $this->getHeight(),
                            $this->_imageAttributes,
                            $this->_imageStyleAttributes,
                            $this->_imageBounds) : '';
        }
        if ($this->getNoImageHtmlCache()) {
            $html = $this->_imageHtml;
            $this->_imageHtml = null;
            return $html;
        }
        return $this->_imageHtml;
    }
    
    /**
     * The crop bounds of self::getImageHtml()
     *
     * @return array 
     */
    public function getBounds()
    {
        if (!$this->_imageBounds) {
            $this->getImageHtml(true);
            if (!$this->_imageBounds) {
                // If $this->getImage() returns null in self::getImageHtml()
                return array(0, 0, 1, 1);
            }
        }
        return $this->_imageBounds;
    }
    
    /**
     *
     * @return bool 
     */
    public function hasTags()
    {
        return count($this->getTags()) > 0;
    }
    
    /**
     * Returns the tags that are positioned and visiblie
     * within the cropped image of self::getImageHtml()
     *
     * @return array 
     */
    public function getVisibleTags()
    {
        $positionedTags = array();
        $bounds = $this->getBounds();
        foreach ($this->getTags() as $tag) {
            if ($tag->isInBounds($bounds)) {
                $tag->setRelX($tag->getRelativeX($bounds));
                $tag->setRelY($tag->getRelativeY($bounds));
                $positionedTags[] = $tag;
            }
        }
        return $positionedTags;
    }
    
    /**
     *
     * @return string 
     */
    public function getTagsImageHtmlId()
    {
        return $this->getHtmlId();
    }
    
    /**
     *
     * @return string 
     */
    public function getHtmlId()
    {
        return $this->hasData('html_id') ?
                    $this->getData('html_id') : $this->getNameInLayout();
    }
    
    /**
     *
     * @return string 
     */
    public function getTitle()
    {
        if ($image = $this->getImage()) {
            return $image->getTitle();
        }
        return '';
    }
    
    /**
     *
     * @return string 
     */
    public function getCaption()
    {
        if ($image = $this->getImage()) {
            return $image->getCaption();
        }
        return '';
    }
}
            