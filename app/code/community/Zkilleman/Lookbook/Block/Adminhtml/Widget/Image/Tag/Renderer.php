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

class Zkilleman_Lookbook_Block_Adminhtml_Widget_Image_Tag_Renderer
        extends Zkilleman_Lookbook_Block_Adminhtml_Widget_Image_Renderer
{
    
    /**
     *
     * @return string 
     */
    public function getAjaxUrl()
    {
        return $this->getUrl(
                        'lookbook/adminhtml_widget_image_tag_renderer/fieldset');
    }
    
    /**
     *
     * @return array 
     */
    public function getOptionValues()
    {
        $optionsJson = $this->getRequest()->getParam('widget');
        $options = Mage::helper('core')->jsonDecode($optionsJson);
        if (!is_array($options) ||
                !isset($options['values']) ||
                !is_array($options['values'])) {
            return array();
        }
        $tagValues = array();
        $prefix = Zkilleman_Lookbook_Model_Config::TAG_RENDERER_OPTION_PREFIX;
        foreach ($options['values'] as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $tagValues[$key] = $value;
            }
        }
        return $tagValues;
    }
    
    /**
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return Varien_Data_Form_Element_Abstract 
     */
    public function prepareElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $rendererBlock = $this->getLayout()
                ->createBlock('lookbook/adminhtml_widget_image_tag_renderer')
                ->setElement($element);
        $element->setData('after_element_html', $rendererBlock->toHtml());
        return $element;
    }
}
