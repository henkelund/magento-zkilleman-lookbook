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

class Zkilleman_Lookbook_Block_Image_Renderer_Popup
            extends Zkilleman_Lookbook_Block_Image_Renderer_Abstract
{
    /**
     *
     * @var string
     */
    protected $_template = 'lookbook/image/renderer/popup.phtml';
    
    /**
     * Prepare image attributes
     * 
     */
    protected function _beforeGetImageHtml()
    {
        $this->_imageAttributes = array(
            'id'  => $this->getHtmlId(),
            'alt' => $this->getCaption()
        );
    }
    
    /**
     *
     * @return string 
     */
    public function getTagsImageHtmlId()
    {
        return $this->getPopupHtmlId() . '_image';
    }
    
    /**
     *
     * @return string 
     */
    public function getPopupHtmlId()
    {
        return $this->getHtmlId() . '_popup';
    }
    
    /**
     *
     * @return string 
     */
    public function getPopupImageHtml()
    {
        $image = $this->getImage();
        if (!$image) {
            return '';
        }

        return $image->getHtml(
                    $this->getPopupWidth(false),
                    $this->getPopupHeight(false),
                    array('id' => $this->getTagsImageHtmlId()));
    }
    
    /**
     *
     * @param  bool $explicit
     * @return int 
     */
    public function getPopupWidth($explicit = false)
    {
        $width = $this->getWidth();
        if ($width) {
            $width = round($width*$this->getZoom());
        } else if ($explicit) {
            $image = $this->getImage();
            if ($image) {
                $height = $this->getPopupHeight(false);
                if ($height) {
                    $width = round($height/$image->getRatio());
                } else {
                    $width = $image->createImageObject()->getOriginalWidth();
                }
            } else {
                $width = 0;
            }
        }
        return $width;
    }
    
    /**
     *
     * @param  bool $explicit
     * @return int
     */
    public function getPopupHeight($explicit = false)
    {
        $height = $this->getHeight();
        if ($height) {
            $height = round($height*$this->getZoom());
        } else if ($explicit) {
            $image = $this->getImage();
            if ($image) {
                $width = $this->getPopupWidth(false);
                if ($width) {
                    $height = round($width*$image->getRatio());
                } else {
                    $height = $image->createImageObject()->getOriginalHeight();
                }
            } else {
                $height = 0;
            }
        }
        return $height;
    }
    
    /**
     * Get Zoom ratio
     * 
     * @return float 
     */
    public function getZoom()
    {
        return max(1.0, (float) $this->getData('zoom'));
    }
    
    /**
     * Popup delay in milliseconds
     *
     * @return int 
     */
    public function getDelay()
    {
        return $this->hasData('delay') ? max(0, (int) $this->getData('delay')) : 100;
    }
}
            