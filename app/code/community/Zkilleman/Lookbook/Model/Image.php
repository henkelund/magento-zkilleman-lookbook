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

class Zkilleman_Lookbook_Model_Image extends Mage_Core_Model_Abstract
{
    /**
     *
     * @var Zkilleman_Lookbook_Helper_Data 
     */
    protected $_helper;
    
    protected function _construct()
    {
        $this->_helper = Mage::helper('lookbook');
        $this->_init('lookbook/image');
    }
    
    /**
     *
     * @return mixed Varien_Image|null 
     */
    public function createImageObject()
    {
        $image = null;
        if ($this->getFile() != '') {
            try {
                $image = new Varien_Image(
                    $this->_helper->getMediaBaseDir() . DS . $this->getFile());
            } catch (Exception $e) {
                $image = null;
            }
        }
        return $image;
    }
    
    public function isLandscape()
    {
        return $this->getRatio() < 1;
    }
    
    public function isPortrait()
    {
        return !$this->isLandscape();
    }
    
    public function getUrl(
                            $width   = null,
                            $height  = null,
                            $crop    = false,
                            &$bounds = null)
    {
        $bounds = array(0, 0, 1, 1); // top, left, right, bottom
        $original = $this->createImageObject();
        if (!$original) {
            $bounds = array(0, 0, 0, 0);
            return false;
        } else if (!$width && !$height) {
            return $this->_helper->getMediaBaseUrl() . DS . $this->getFile();
        }
        if ($width && $height) {
            $ratio = $height/$width;
            if ($ratio > $this->getRatio()) {
                if ($crop) {
                    $fullWidth = round(($height/$original->getOriginalHeight()) *
                                    $original->getOriginalWidth());
                    $widthToCrop = $fullWidth - $width;
                    $leftCrop = round($widthToCrop * $this->getFocusX());
                    $crop = array(0, $leftCrop, $widthToCrop - $leftCrop, 0);
                    $bounds[1] = number_format($crop[1]/$fullWidth, 4);
                    $bounds[2] = number_format(1.0 - $crop[2]/$fullWidth, 4);
                }
                $width = null;
            } else {
                if ($crop) {
                    $fullHeight = round(($width/$original->getOriginalWidth()) *
                                    $original->getOriginalHeight());
                    $heightToCrop = $fullHeight - $height;
                    $topCrop = round($heightToCrop * $this->getFocusY());
                    $crop = array($topCrop, 0, 0, $heightToCrop - $topCrop);
                    $bounds[0] = number_format($crop[0]/$fullHeight, 4);
                    $bounds[3] = number_format(1.0 - $crop[3]/$fullHeight, 4);
                }
                $height = null;
            }
        } else {
            // Can't crop if not both width & height specified
            $crop = false;
        }
        $fileName = DS .
                ($width ? 'w' . $width : 'h' . $height) .
                (is_array($crop) ? ('t' . $crop[0] .
                                    'l' . $crop[1] .
                                    'r' . $crop[2] .
                                    'b' . $crop[3]) : '') .
                $this->getFile();
        
        if (!file_exists($this->_helper->getCachedMediaBaseDir() . $fileName)) {
            $original->keepTransparency(true);
            $original->resize($width, $height);
            if (is_array($crop)) {
                call_user_func_array(array($original, 'crop'), $crop);
            }
            $original->save($this->_helper->getCachedMediaBaseDir() . $fileName);
        }
        
        return $this->_helper->getCachedMediaBaseUrl() . $fileName;
    }
    
    public function getHtml(
                            $width           = null,
                            $height          = null,
                            $attributes      = array(),
                            $styleAttributes = array(),
                            &$bounds         = null)
    {
        $tag = 'img';
        $content = '';
        if (!is_array($attributes)) {
            $attributes = array();
        }
        if (!is_array($styleAttributes)) {
            $styleAttributes = array();
        }
        if (isset($attributes['_tag'])) {
            $tag = strtolower($attributes['_tag']);
            unset($attributes['_tag']);
        }
        if (isset($attributes['_content'])) {
            $content = $attributes['_content'];
            unset($attributes['_content']);
        }
        $defaultStyleAttributes = array();
        if ($tag == 'img') {
            if ($width && !isset($attributes['width'])) {
                $attributes['width'] = round($width);
            }
            if ($height && !isset($attributes['height'])) {
                $attributes['height'] = round($height);
            }
            $attributes['src'] = $this->getUrl($width, $height, true, $bounds);
        } else {
            if ($width) {
                $defaultStyleAttributes['width'] = round($width) . 'px';
            }
            if ($height) {
                $defaultStyleAttributes['height'] = round($height) . 'px';
            }
            $defaultStyleAttributes['background-image'] = 
                            sprintf('url(\'%s\')', 
                                    $this->getUrl($width, $height, false, $bounds));
            $defaultStyleAttributes['background-position'] =
                            sprintf('%d%% %d%%',
                                    $this->getFocusX() * 100,
                                    $this->getFocusY() * 100);
        }
        $styleAttributes = array_merge($defaultStyleAttributes, $styleAttributes);
        $styleAttributePairs = array();
        foreach ($styleAttributes as $key => $value) {
            if ($value) {
                $styleAttributePairs[] = sprintf('%s: %s;', $key, $value);
            }
        }
        if (!empty($styleAttributePairs)) {
            $attributes['style'] = implode(' ', $styleAttributePairs);
        }
        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $attributeString .= sprintf(' %s="%s"', $key, htmlspecialchars($value));
        }
        $tagstring = ($tag == 'img') ? '<img%s />' :
                        sprintf('<%s%%s>%s</%s>', $tag, $content, $tag);
        return sprintf($tagstring, $attributeString);
    }
    
    public function getTags(array $bounds = array())
    {
        if (!$this->getId()) {
            return null;
        }
        $tags = Mage::getModel('lookbook/image_tag')->getCollection()
                ->addFieldToFilter('image_id', $this->getId());
        if (count($bounds) == 4) {
            $tags->addFieldToFilter('y', array('gteq' => $bounds[0]))
                 ->addFieldToFilter('x', array('gteq' => $bounds[1]))
                 ->addFieldToFilter('x', array('lteq' => $bounds[2]))
                 ->addFieldToFilter('y', array('lteq' => $bounds[3]));
        }
        return $tags;
    }
}