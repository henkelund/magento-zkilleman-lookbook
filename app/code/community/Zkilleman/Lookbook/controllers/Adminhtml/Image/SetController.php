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

class Zkilleman_Lookbook_Adminhtml_Image_SetController
        extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function newAction()
    {
        $this->_forward('edit');
    }
    
    public function editAction()
    {
        $id = $this->getRequest()->getParam('set_id');
        $model = Mage::getModel('lookbook/image_set');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                        $this->__('This set no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Set'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('lookbook_image_set', $model);
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $id = $this->getRequest()->getParam('set_id');
            $model = Mage::getModel('lookbook/image_set')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(
                        $this->__('This set no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            $images = '';
            if (isset($data['images'])) {
                $images = $data['images'];
                // $data['images'] is not intended for the set model so remove it.
                unset($data['images']);
            }
            $images = array_filter(
                        preg_split(
                                '/\s*,\s*/', $images, null, PREG_SPLIT_NO_EMPTY
                        ), 'is_numeric');
            
            $model->setData($data);
            
            try {
                $model->save();
                $model->setImageIds($images);
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('The set has been saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
                
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('set_id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                    'set_id' => $this->getRequest()->getParam('set_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('set_id')) {
            try {
                $model = Mage::getModel('lookbook/image_set');
                $model->load($id);
                $title = $model->getTitle();
                $model->delete(); // cascade db rule should clean up relations

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__(sprintf(
                                'The set \'%s\' has been deleted.', $title)));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('set_id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Unable to find a set to delete.'));
        $this->_redirect('*/*/');
    }
}
