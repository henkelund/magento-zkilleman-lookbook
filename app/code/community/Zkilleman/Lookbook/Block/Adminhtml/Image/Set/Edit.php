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

class Zkilleman_Lookbook_Block_Adminhtml_Image_Set_Edit
        extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'set_id';
        $this->_blockGroup = 'lookbook';
        $this->_controller = 'adminhtml_image_set';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('lookbook')->__('Save Set'));
        $this->_updateButton('delete', 'label', Mage::helper('lookbook')->__('Delete Set'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save and Continue Edit'),
            'onclick'   => 'editForm.submit($(\'edit_form\').action+\'back/edit/\');',
            'class'     => 'save',
        ), -100);
    }
    
    public function getHeaderText()
    {
        if (Mage::registry('lookbook_image_set')->getId()) {
            return $this->__(
                    'Edit Set \'%s\'',
                    $this->htmlEscape(Mage::registry('lookbook_image_set')->getTitle()));
        }
        else {
            return $this->__('New Set');
        }
    }
}