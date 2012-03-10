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
}