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

class Zkilleman_Lookbook_Block_Adminhtml_Image_Set_Edit_Form
        extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('image_set_form');
        $this->setTitle($this->__('Image Set Information'));
    }

    protected function _prepareForm()
    {   
        $model = Mage::registry('lookbook_image_set');

        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getData('action'),
            'method'  => 'post'
        ));
        $form->setHtmlIdPrefix('image_set_');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => $this->__('General Information'),
            'class'  => 'fieldset-wide'
        ));
        $this->_addElementTypes($fieldset);

        if ($model->getSetId()) {
            $fieldset->addField('set_id', 'hidden', array(
                'name' => 'set_id',
            ));
        }

        $fieldset->addField('title', 'text', array(
            'name'      => 'title',
            'label'     => $this->__('Set Title'),
            'title'     => $this->__('Set Title'),
            'required'  => true,
        ));

        $fieldset->addField('handle', 'text', array(
            'name'      => 'handle',
            'label'     => $this->__('Handle'),
            'title'     => $this->__('Handle'),
            'required'  => true,
            'class'     => 'validate-xml-identifier',
            'note'      => $this->__('Used by widgets to identify this set.')
        ));

        $fieldset->addField('description', 'text', array(
            'name'      => 'description',
            'label'     => $this->__('Description'),
            'title'     => $this->__('Description')
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

        $fieldset->addField('images', 'images', array(
            'name'      => 'images',
            'label'     => $this->__('Images'),
            'title'     => $this->__('Images')
        ));
        
        $data = $model->getData();
        $data['images'] = implode(',', $model->getImageIds());
        
        $form->setValues($data);
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'images' => Mage::getConfig()
                ->getBlockClassName('lookbook/adminhtml_image_set_helper_images')
        );
    }
}