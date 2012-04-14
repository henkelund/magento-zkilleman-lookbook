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

abstract class Zkilleman_Lookbook_Block_Widget_Abstract
    extends Mage_Core_Block_Template
    implements Mage_Widget_Block_Interface
{
    const TAG_SEPARATOR = '|';
    
    /**
     * Arbitrary min width value
     *
     * @var int 
     */
    protected $_minWidth = 64;
    
    /**
     * Arbitrary max width value
     *
     * @var int 
     */
    protected $_maxWidth = 4096;
    
    /**
     * Default theme 1column width
     *
     * @var int 
     */
    protected $_defaultWidth = 900;
    
    /**
     * Arbitrary min height value
     *
     * @var int 
     */
    protected $_minHeight = 64;
    
    /**
     * Arbitrary max height value
     *
     * @var int 
     */
    protected $_maxHeight = 4096;
    
    /**
     * Approximation of the golden ratio
     *
     * @var int 
     */
    protected $_defaultHeight = '610:377';
    
    /**
     * Counter used to produce unique identifiers for each widget instance
     *
     * @var int 
     */
    protected static $_instanceCount = 0;
    
    /**
     * Internal constructor
     * 
     */
    protected function _construct()
    {
        $this->setHtmlId('lookbook_widget_' . self::$_instanceCount++);
        parent::_construct();
    }
    
    /**
     *
     * @return mixed Zkilleman_Lookbook_Model_Resource_Image_Collection|false
     */
    public function getImageCollection()
    {
        if (!$this->hasData('image_collection')) {
            $collection = false;
            if ($handle = $this->getSetHandle()) {
                $set = Mage::getModel('lookbook/image_set')->loadByHandle($handle);
                if ($set->getId()) {
                    $collection = $set->getImageCollection();
                } else {
                    // if set specified but doesn't exist
                    // return false & don't render widget
                    $this->setData('image_collection', false);
                    return false;
                }
            }
            if (!$collection) {
                $collection = Mage::getModel('lookbook/image')
                                    ->getCollection()
                                    ->addFieldToFilter('is_active', 1)
                                    ->setOrder('created_at', 'desc');
            }
            if ($tags = $this->getTags()) {
                $collection->join(
                                array('t' => 'lookbook/image_tag'),
                                'main_table.image_id = t.image_id',
                                array())
                        ->addFieldToFilter('t.name', array('in' => array($tags)))
                        ->distinct(true);
            }
            $this->setData('image_collection', $collection);
        }
        return $this->getData('image_collection');
    }
    
    /**
     * Widget config tags merged with user provided tags
     *
     * @return array
     */
    public function getTags()
    {
        $tags = preg_split(
                    sprintf('/\s*%s\s*/', preg_quote(self::TAG_SEPARATOR)), 
                    (string) trim($this->getData('tags')),
                    null,
                    PREG_SPLIT_NO_EMPTY);
        if (Mage::getSingleton('lookbook/config')->isRequestTagsAllowed()) {
            $tags = array_merge($tags, Mage::helper('lookbook')->getRequestTags());
        }
        return array_unique($tags);
    }
    
    /**
     * Whether given tag was used in current query
     *
     * @param  mixed Varien_Object|string
     * @return bool 
     */
    public function tagIsActive($tag)
    {
        if ($tag instanceof Varien_Object) {
            $tag = $tag->getName();
        }
        $helper = Mage::helper('lookbook');
        return in_array(
                $helper->strToLower($tag),
                $helper->arrayToLower($this->getTags()));
    }
    
    /**
     *
     * @return int 
     */
    public function getWidth()
    {
        $width = $this->hasData('width') ?
                    intval($this->getData('width')) : $this->_defaultWidth;
        return (int) min($this->_maxWidth, max($this->_minWidth, $width));
    }
    
    /**
     *
     * @return int 
     */
    public function getHeight()
    {
        $height = $this->hasData('height') ?
                        $this->getData('height') : $this->_defaultHeight;
        
        if (is_numeric($height)) {
            $height = intval($height);
        } else {
            $matches = array();
            if (preg_match('/^(\d+(\.\d+)?):(\d+(\.\d+)?)$/', $height, $matches)) {
                $w = floatval($matches[1]);
                $h = floatval($matches[3]);
                if ($w > 0 && $h > 0) {
                    $height = $this->getWidth()*($h/$w);
                } else {
                    $height = 0;
                }
            } else {
                $height = 0;
            }
        }
        return (int) min($this->_maxHeight, max($this->_minHeight, $height));
    }
    
    /**
     * Renders widget html if image collection exists
     * 
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getImageCollection()) {
            return parent::_toHtml();
        } else {
            return '<!-- Colud not load image collection -->';
        }
    }
}
