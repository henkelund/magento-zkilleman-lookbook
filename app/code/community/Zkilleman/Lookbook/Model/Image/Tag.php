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

class Zkilleman_Lookbook_Model_Image_Tag extends Mage_Core_Model_Abstract
{
    const ENTITY = 'lookbook_image_tag';
    
    const DEFAULT_TYPE = 'plain';
    
    protected $_typeInstance;

    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_init('lookbook/image_tag');
        $this->_typeInstance = null;
    }
    
    public function getTypeInstance()
    {
        if ($this->_typeInstance == null) {
            $info = Mage::getModel('lookbook/config')->getTypeInfo($this->getType());
            if (!$info) {
                $info = Mage::getModel('lookbook/config')
                            ->getTypeInfo(self::DEFAULT_TYPE);
            }
            //if (!is_array($info)) {
            //    throw new Exception('No info');
            //}
            $model = isset($info['model']) ? 
                        $info['model'] : 'lookbook/image_tag_type_' . $info['key'];
            $modelData = array('tag' => $this);
            $modelInstance = Mage::getModel($model, $modelData);
            if (!is_object($modelInstance) || 
                    !($modelInstance instanceof 
                        Zkilleman_Lookbook_Model_Image_Tag_Type_Abstract)) {
                $modelInstance = Mage::getModel(
                        'lookbook/image_tag_type_' . self::DEFAULT_TYPE, $modelData);
            }
            $this->_typeInstance = $modelInstance;
        }
        return $this->_typeInstance;
    }
    
    public function isInBounds(array $bounds)
    {
        if (!$this->getId() || count($bounds) != 4) {
            return false;
        } else {
            return $this->getY() >= $bounds[0] &&
                   $this->getX() >= $bounds[1] &&
                   $this->getX() <= $bounds[2] &&
                   $this->getY() <= $bounds[3];
        }
    }
    
    public function getRelativeX(array $bounds)
    {
        if (!$this->isInBounds($bounds)) {
            return -1;
        } else {
            $scale = 1.0/($bounds[2] - $bounds[1]);
            return number_format($this->getX()*$scale - $bounds[1]*$scale, 4);
        }
    }
    
    public function getRelativeY(array $bounds)
    {
        if (!$this->isInBounds($bounds)) {
            return -1;
        } else {
            $scale = 1.0/($bounds[3] - $bounds[0]);
            return number_format($this->getY()*$scale - $bounds[0]*$scale, 4);
        }
    }
    
    public function afterCommitCallback()
    {
        parent::afterCommitCallback();
        Mage::getSingleton('index/indexer')->processEntityAction(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
        return $this;
    }
    
    protected function _beforeDelete()
    {
        Mage::getSingleton('index/indexer')->logEvent(
            $this, self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE
        );
        return parent::_beforeDelete();
    }

    protected function _afterDeleteCommit()
    {
        parent::_afterDeleteCommit();
        Mage::getSingleton('index/indexer')->indexEvents(
            self::ENTITY, Mage_Index_Model_Event::TYPE_DELETE
        );
    }
}