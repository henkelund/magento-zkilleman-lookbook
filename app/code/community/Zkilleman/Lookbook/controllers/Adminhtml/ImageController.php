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

class Zkilleman_Lookbook_Adminhtml_ImageController
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
        $id = $this->getRequest()->getParam('image_id');
        $model = Mage::getModel('lookbook/image');

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(
                        $this->__('This image no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_title($model->getId() ? $model->getTitle() : $this->__('New Image'));

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('lookbook_image', $model);
        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            $helper = Mage::helper('lookbook');

            $id = $this->getRequest()->getParam('image_id');
            $model = Mage::getModel('lookbook/image')->load($id);
            if (!$model->getId() && $id) {
                Mage::getSingleton('adminhtml/session')->addError(
                        $this->__('This image no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }

            if (isset($data['file']['delete'])) {
                $helper->removeImageFile($model);
                $data['file'] = '';
            } elseif (!empty($_FILES['file']['name'])) {
                $helper->removeImageFile($model);
                $uploader = new Mage_Core_Model_File_Uploader('file');
                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                $uploader->setAllowRenameFiles(true);
                $uploader->setFilesDispersion(true);
                $result = $uploader->save($helper->getMediaBaseDir());
                $data['file'] = $result['file'];
            } else {
                unset($data['file']);
            }
            
            $oldTags = array();
            $newTags = array();
            if (isset($data['file_tags']) && is_array($data['file_tags'])) {
                foreach ($data['file_tags'] as $tag) {
                    $tag['name'] = trim($tag['name'], '" ');
                    if ($tag['x'] < 0 || $tag['x'] > 1 ||
                            $tag['y'] < 0 || $tag['y'] > 1) {
                        $tag['x'] = -1;
                        $tag['y'] = -1;
                    }
                    if (isset($tag['tag_id']) && $tag['tag_id'] > 0) {
                        $oldTags[$tag['tag_id']] = $tag;
                    } else {
                        $newTags[] = $tag;
                    }
                }
            }
            unset($data['file_tags']);

            $model->setData($data);

            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__('The image has been saved.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($tags = $model->getTags()) {
                    foreach ($tags as $tag) {
                        if (array_key_exists($tag->getId(), $oldTags)) {
                            $tag->setData($oldTags[$tag->getId()])->save();
                        } else {
                            $tag->delete();
                        }
                    }
                }

                foreach ($newTags as $tag) {
                    $newTag = Mage::getModel('lookbook/image_tag');
                    $newTag->setData($tag)->setImageId($model->getId())->save();
                }
                
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('image_id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array(
                    'image_id' => $this->getRequest()->getParam('image_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }
    
    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('image_id')) {
            try {
                $model = Mage::getModel('lookbook/image');
                $model->load($id);
                Mage::helper('lookbook')->removeImageFile($model);
                $title = $model->getTitle();
                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                        $this->__(sprintf(
                                'The image \'%s\' has been deleted.', $title)));
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('image_id' => $id));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
                $this->__('Unable to find an image to delete.'));
        $this->_redirect('*/*/');
    }
}
