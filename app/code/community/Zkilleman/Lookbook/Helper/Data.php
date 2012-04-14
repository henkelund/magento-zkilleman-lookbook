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

class Zkilleman_Lookbook_Helper_Data extends Mage_Core_Helper_Abstract
{
    const ORIGINAL_PATH = 'lookbook';
    const CACHE_PATH    = 'lookbook/cache';
    const TAG_SEPARATOR = '|';
    
    public function getMediaBaseDir()
    {
        return Mage::getBaseDir('media') . DS . self::ORIGINAL_PATH;
    }
    
    public function getMediaBaseUrl()
    {
        return Mage::getBaseUrl('media') . self::ORIGINAL_PATH;
    }
    
    public function getCachedMediaBaseDir()
    {
        return Mage::getBaseDir('media') . DS . self::CACHE_PATH;
    }
    
    public function getCachedMediaBaseUrl()
    {
        return Mage::getBaseUrl('media') . self::CACHE_PATH;
    }
    
    public function removeImageFile(Zkilleman_Lookbook_Model_Image $image)
    {
        if (!$image || !$image->getId() || $image->getFile() == '') {
            return false;
        }
        $file = $this->getMediaBaseDir() . $image->getFile();
        if (file_exists($file) && unlink($file)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getRequestTags($limit = true)
    {
        $config = Mage::getSingleton('lookbook/config');
        $tagString = Mage::app()->getRequest()->getParam($config->getTagParamName());
        $limit = $limit === true ?
                    $config->getRequestTagLimit() :
                    (is_int($limit) ? $limit : false);
        $tags = array_unique(preg_split(
                sprintf('/\s*%s\s*/', preg_quote(self::TAG_SEPARATOR)), 
                trim(Mage::helper('core/string')->cleanString($tagString)),
                null,
                PREG_SPLIT_NO_EMPTY));
        if ($limit !== false && count($tags) > $limit) {
            $tags = array_slice($tags, 0, $limit);
        }
        return $tags;
    }
    
    public function strToLower($string)
    {
        return mb_strtolower($string, 'UTF-8');
    }
    
    public function arrayToLower($array)
    {
        return array_map(array($this, 'strToLower'), $array);
    }
    
    /**
     *
     * @param  mixed  $tag
     * @param  bool   $toggle
     * @return string
     */
    public function getTagUrl($tag, $toggle = true)
    {
        if (!is_array($tag)) {
            $tag = array($tag);
        }
        $tag = array_unique($tag);

        $config      = Mage::getSingleton('lookbook/config');
        $urlHelper   = Mage::helper('core/url');
        $paramName   = $config->getTagParamName();
        $paramLimit  = $config->getRequestTagLimit();
        $requestTags = $this->getRequestTags(false);

        $map = array();
        if (!empty($requestTags)) {
            $map = array_combine(
                        $this->arrayToLower($requestTags),
                        $requestTags);
        }

        foreach ($tag as $t) {
            if ($t instanceof Varien_Object) {
                $t = $t->getName();
            }
            $tl = $this->strToLower($t);
            if ($toggle && isset($map[$tl])) {
                unset($map[$tl]);
            } else {
                array_unshift($map, $t);
            }
        }

        if ($paramLimit !== false && count($map) > $paramLimit) {
            $map = array_slice($map, 0, $paramLimit);
        }

        $url = $urlHelper->getCurrentUrl();
        $url = $urlHelper->removeRequestParam($url, $paramName);
        if (count($map) > 0) {
            $url = $urlHelper->addRequestParam($url,
                        array($paramName => implode(self::TAG_SEPARATOR, $map)));
        }

        return $url;
    }
}
