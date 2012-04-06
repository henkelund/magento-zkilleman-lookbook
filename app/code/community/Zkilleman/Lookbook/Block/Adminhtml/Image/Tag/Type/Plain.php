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

class Zkilleman_Lookbook_Block_Adminhtml_Image_Tag_Type_Plain
        extends Mage_Adminhtml_Block_Template
{
    protected $_tag;
    
    protected function _construct()
    {
        parent::_construct();
        $this->_template = 'lookbook/image/tag/type/plain.phtml';
        $this->_tag = null;
    }
    
    public function getValue()
    {
        return $this->getType() == $this->getTag()->getType() ?
                $this->getTag()->getReference() : 0;
    }
    
    public function getTag()
    {
        if ($this->_tag == null) {
            $this->_tag = Mage::getModel('lookbook/image_tag')
                                ->load($this->getTagId());
        }
        return $this->_tag;
    }
}