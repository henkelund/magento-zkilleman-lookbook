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

class Zkilleman_Lookbook_Model_Image_Set extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('lookbook/image_set');
    }
    
    public function loadByHandle($handle)
    {
        $this->load($handle, 'handle');
        return $this;
    }
    
    public function getImageCollection()
    {
        $images = Mage::getModel('lookbook/image')->getCollection();
        $si     = $this->getResource()->getTable('lookbook/image_set_image');
        $images->getSelect()
                ->joinInner(
                    array('si' => $si),
                    'main_table.image_id = si.image_id',
                    array()
                )
                ->where('main_table.is_active = 1')
                ->where('si.set_id = ?', $this->getId())
                ->order('si.order ASC');
        return $images;
    }
    
    public function getImageIds()
    {
        return $this->getResource()->getImageIds($this);
    }
    
    public function setImageIds(array $ids = array())
    {
        $this->getResource()->setImageIds($this, $ids);
        return $this;
    }
}