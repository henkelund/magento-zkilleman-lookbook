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

class Zkilleman_Lookbook_Adminhtml_Widget_Image_RendererController
        extends Mage_Adminhtml_Controller_Action
{
    
    /**
     * Return fieldset html for given image renderer type
     *
     */
    public function fieldsetAction()
    {
        Varien_Data_Form::setElementRenderer(
            $this->getLayout()->createBlock(
                    'adminhtml/widget_form_renderer_element')
        );
        Varien_Data_Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                    'adminhtml/widget_form_renderer_fieldset')
        );
        Varien_Data_Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                    'adminhtml/widget_form_renderer_fieldset_element')
        );
        
        $type   = $this->getRequest()->getParam('type');
        $htmlId = $this->getRequest()->getParam('html_id');
        $values = $this->getRequest()->getParam('values');
        
        $fieldset = Mage::getModel('lookbook/adminhtml_widget')
                            ->getImageRendererFieldset(
                                    $type,
                                    $htmlId,
                                    is_array($values) ? $values : array());
        
        $this->getResponse()->setBody($fieldset ? $fieldset->toHtml() : '');
    }
}
