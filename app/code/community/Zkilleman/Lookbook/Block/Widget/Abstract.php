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
    protected $_imageCollection = null;
    
    public function getImageCollection()
    {
        if ($this->_imageCollection === null) {
            if ($handle = $this->getSetHandle()) {
                $set = Mage::getModel('lookbook/image_set')->loadByHandle($handle);
                if ($set->getId()) {
                    $this->_imageCollection = $set->getImageCollection();
                } else {
                    // if set specified but doesn't exist
                    // return false & don't render widget
                    $this->_imageCollection = false;
                    return false;
                }
            }
            if (!$this->_imageCollection) {
                $this->_imageCollection = 
                            Mage::getModel('lookbook/image')
                                    ->getCollection()
                                    ->addFieldToFilter('is_active', 1)
                                    ->setOrder('main_table.created_at');
            }
            if ($tags = $this->getTags()) {
                $tagTable = Mage::getModel('lookbook/image_tag')
                                    ->getResource()->getMainTable();
                $this->_imageCollection->getSelect()
                            ->joinInner(
                                array('t' => $tagTable),
                                'main_table.image_id = t.image_id',
                                array()
                            )
                            ->where('t.name IN (?)', $tags)
                            ->distinct();
            }
        }
        return $this->_imageCollection;
    }
    
    public function getTags()
    {
        //@todo There should be a limit on the number of request param tags
        $tagString = trim(
                ((string) $this->getData('tags')) . ',' .
                ((string) $this->getRequest()->getParam('tags')), ', ');
        return array_unique(preg_split(
                '/\s*,\s*/', 
                $tagString,
                null,
                PREG_SPLIT_NO_EMPTY));
    }
    
    protected function _toHtml()
    {
        if ($this->getImageCollection()) {
            return parent::_toHtml();
        } else {
            return '<!-- Colud not load image collection -->';
        }
    }
}
