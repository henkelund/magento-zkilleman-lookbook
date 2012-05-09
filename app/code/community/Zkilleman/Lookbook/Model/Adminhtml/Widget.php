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

class Zkilleman_Lookbook_Model_Adminhtml_Widget
{
    /**
     *
     * @param  string $type
     * @param  string $htmlId
     * @param  array  $values
     * @return Varien_Data_Form_Element_Fieldset|false 
     */
    public function getImageRendererFieldset($type, $htmlId = '', $values = array())
    {
        $typeInfo = Mage::getSingleton('lookbook/config')->getImageRenderer($type);
        if (!is_array($typeInfo) ||
                !isset($typeInfo['parameters']) ||
                !is_array($typeInfo['parameters'])) {
            return false;
        }
        
        $helper = Mage::helper($typeInfo['module']);

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset($htmlId, array(
            'legend' => sprintf(
                            $helper->__('Image Renderer \'%s\' Options'),
                            $typeInfo['title']),
            'class'  => 'image-renderer-fieldset'
        ));
        
        $parameters = $typeInfo['parameters'];
        $prefix = Zkilleman_Lookbook_Model_Config::IMAGE_RENDERER_OPTION_PREFIX;
        foreach ($parameters as $key => $parameter) {
            if (is_array($parameter)) {
                $parameter['key'] = $prefix . $key;
                $field = $this->_addField(
                        $fieldset,
                        new Varien_Object($parameter),
                        $helper);
                if (isset($values[$parameter['key']])) {
                    $field->setValue($values[$parameter['key']]);
                }
            }
        }
        
        return $fieldset;
    }
    
    /**
     *
     * @param  string $type
     * @param  string $htmlId
     * @param  array  $values
     * @return Varien_Data_Form_Element_Fieldset|false 
     */
    public function getTagRendererFieldset($type, $htmlId = '', $values = array())
    {
        $typeInfo = Mage::getSingleton('lookbook/config')->getTagRenderer($type);
        if (!is_array($typeInfo) ||
                !isset($typeInfo['parameters']) ||
                !is_array($typeInfo['parameters'])) {
            return false;
        }
        
        $helper = Mage::helper($typeInfo['module']);

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset($htmlId, array(
            'legend' => sprintf(
                            $helper->__('Tag Renderer \'%s\' Options'),
                            $typeInfo['title']),
            'class'  => 'tag-renderer-fieldset'
        ));
        
        $parameters = $typeInfo['parameters'];
        $prefix = Zkilleman_Lookbook_Model_Config::TAG_RENDERER_OPTION_PREFIX;
        foreach ($parameters as $key => $parameter) {
            if (is_array($parameter)) {
                $parameter['key'] = $prefix . $key;
                $field = $this->_addField(
                        $fieldset,
                        new Varien_Object($parameter),
                        $helper);
                if (isset($values[$parameter['key']])) {
                    $field->setValue($values[$parameter['key']]);
                }
            }
        }
        
        return $fieldset;
    }
    
    /**
     *
     * @param  Varien_Data_Form_Element_Fieldset $fieldset
     * @param  Varien_Object                     $parameter
     * @param  Mage_Core_Helper_Abstract         $helper
     * @return Varien_Data_Form_Element_Abstract 
     */
    protected function _addField(
                                 $fieldset,
                                 Varien_Object $parameter,
                                 $helper = null)
    {
        $helper = $helper ? $helper : Mage::helper('lookbook');
        $form = $fieldset->getForm();
        $fieldName = $parameter->getKey();
        
        // general data
        $data = array(
            'name'     => $form->addSuffixToName($fieldName, 'parameters'),
            'label'    => $helper->__($parameter->getLabel()),
            'required' => $parameter->getRequired(),
            'class'    => 'widget-option',
            'note'     => $helper->__($parameter->getDescription()),
            'value'    => $parameter->getValue()
        );
        
        // dropdown values
        if ($values  = $parameter->getValues()) {
            $data['values'] = array();
            foreach ($values as $option) {
                $data['values'][] = array(
                    'label' => $helper->__($option['label']),
                    'value' => $option['value']
                );
            }
        } else if ($sourceModel = $parameter->getSourceModel()) {
            $data['values'] = Mage::getModel($sourceModel)->toOptionArray();
        }
        
        // field type
        $fieldRenderer = null;
        $fieldType = $parameter->getType();
        if (!$parameter->getVisible()) {
            $fieldType = 'hidden';
        } else if (false !== strpos($fieldType, '/')) {
            $fieldRenderer = Mage::app()->getLayout()->createBlock($fieldType);
            $fieldType = 'text';
        }
        
        // create field
        $field = $fieldset->addField($fieldName, $fieldType, $data);
        if ($fieldRenderer) {
            $field->setRenderer($fieldRenderer);
        }
        
        return $field;
    }
}
