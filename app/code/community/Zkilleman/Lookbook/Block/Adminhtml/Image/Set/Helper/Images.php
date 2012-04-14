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

class Zkilleman_Lookbook_Block_Adminhtml_Image_Set_Helper_Images
        extends Varien_Data_Form_Element_Abstract
{
    /**
     *
     * @var string Template file 
     */
    protected $_template;
    
    /**
     * Constructor
     *
     * @param array $data 
     */
    public function __construct($data)
    {
        parent::__construct($data);
        $this->_template = 'lookbook/image/set/helper/images.phtml';
    }
    
    /**
     *
     * @return string
     */
    public function getElementHtml()
    {
        return Mage::app()
                ->getLayout()
                ->createBlock('adminhtml/template')
                ->setTemplate($this->_template)
                ->setElement($this)
                ->toHtml();
    }
    
    /**
     * Maybe not the prettiest possibility but it's said to be portable.
     * 
     * @see    http://stackoverflow.com/questions/4011056/order-of-where-field-in-sql-query#4011088
     * @param  array $ids
     * @return string 
     */
    protected function _getOrderByCaseSql($field, array $ids)
    {
        $whenPairs = array();
        $i = 1;
        foreach ($ids as $id) {
            $whenPairs[] = sprintf('WHEN %s THEN %s', $id, $i++);
        }
        return sprintf('CASE %s %s END', $field, implode(' ', $whenPairs));
    }
    
    /**
     *
     * @return mixed false|Zkilleman_Lookbook_Model_Resource_Image_Collection
     */
    public function getImageCollection()
    {
        $imageIds = Mage::helper('lookbook')->strToIntArray($this->getValue());
        if (empty($imageIds)) {
            return false;
        }
        return Mage::getModel('lookbook/image')->getCollection()
                        ->addFieldToFilter('image_id', array('in' => $imageIds))
                        ->setOrder(
                            $this->_getOrderByCaseSql('image_id', $imageIds),
                            'asc');
    }
}