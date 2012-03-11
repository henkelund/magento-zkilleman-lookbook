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

class Zkilleman_Lookbook_Block_Adminhtml_Image_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('image_form');
        $this->setTitle($this->__('Image Information'));
    }

    protected function _prepareForm()
    {   
        $model = Mage::registry('lookbook_image');

        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getData('action'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));
        $form->setHtmlIdPrefix('image_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $this->__('General Information'),
            'class'  => 'fieldset-wide'
        ));
        $this->_addElementTypes($fieldset);

        if ($model->getImageId()) {
            $fieldset->addField('image_id', 'hidden', array(
                'name' => 'image_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => $this->__('Image Title'),
            'title'     => $this->__('Image Title'),
            'required'  => true,
        ));

        $fieldset->addField('caption', 'text', array(
            'name'      => 'caption',
            'label'     => $this->__('Caption'),
            'title'     => $this->__('Caption')
        ));

        $fieldset->addField('file', 'image', array(
            'name'      => 'file',
            'label'     => $this->__('File'),
            'title'     => $this->__('File')
        ));

        $fieldset->addField('is_active', 'select', array(
            'name'      => 'is_active',
            'label'     => $this->__('Is Active'),
            'title'     => $this->__('Is Active'),
            'options'   => array(
                '1' => $this->__('Yes'),
                '0' => $this->__('No')
            )
        ));
        
        $data = $model->getData();
        $data['file'] = array(
            'value'   => isset($data['file']) ? $data['file'] : null,
            'focus_x' => $model->getFocusX(),
            'focus_y' => $model->getFocusY()
        );
        
        $form->setValues($data);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()
                ->getBlockClassName('lookbook/adminhtml_image_helper_image')
        );
    }
}