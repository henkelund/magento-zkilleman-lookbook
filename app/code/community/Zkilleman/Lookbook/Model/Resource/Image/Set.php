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

class Zkilleman_Lookbook_Model_Resource_Image_Set
        extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('lookbook/image_set', 'set_id');
    }
    
    public function getImageIds(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            return array();
        }
        $conn   = $this->_getReadAdapter();
        $table  = $this->getTable('lookbook/image_set_image');
        $select = $conn->select()
                        ->from($table, array('image_id'))
                        ->where('set_id = ?', $object->getId())
                        ->order('order ASC');
        return $select->query()->fetchAll(Zend_Db::FETCH_COLUMN);
    }
    
    public function setImageIds(Mage_Core_Model_Abstract $object, array $ids = array())
    {
        if (!$object->getId()) {
            return $this;
        }
        
        $conn  = $this->_getWriteAdapter();
        $table = $this->getTable('lookbook/image_set_image');

        // remove old relations
        $conn->delete($table, array('set_id = ?' => $object->getId()));
        
        // insert new if provided
        if (!empty($ids)) {
            $order = 0;
            $conn->beginTransaction();
            foreach ($ids as $image) {
                $conn->insert($table, array(
                    'set_id'   => $object->getId(),
                    'image_id' => $image,
                    'order'    => $order++
                ));
            }
            $conn->commit();
        }
        return $this;
    }
    
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if (!$object->getId()) {
            $object->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());
        }
        $object->setUpdatedAt(Mage::getSingleton('core/date')->gmtDate());
        return $this;
    }
}