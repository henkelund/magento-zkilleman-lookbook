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

class Zkilleman_Lookbook_Model_Config
{
    const XML_PATH_TAG_TYPES = 'global/lookbook/image/tag/types';
    
    /**
     *
     * @var array
     */
    protected static $_tagTypes = null;
    
    public function _loadTagTypes()
    {
        if (self::$_tagTypes === null) {
            self::$_tagTypes = array();
            $helper = Mage::helper('lookbook');
            $typesNode = Mage::getConfig()->getNode(self::XML_PATH_TAG_TYPES);
            foreach ($typesNode->children() as $type => $info) {
                $translate = preg_split(
                        '/\s+/', $info->getAttribute('translate'),
                        null, PREG_SPLIT_NO_EMPTY);
                $data = $info->asArray();
                unset($data['@']);
                foreach ($translate as $key) {
                    if (isset($data[$key]) && is_scalar($data[$key])) {
                        $data[$key] = $helper->__($data[$key]);
                    }
                }
                $data['key'] = $type;
                self::$_tagTypes[$type] = $data;
            }
        }
    }
    
    /**
     *
     * @return array
     */
    public function getTagTypes()
    {
        $this->_loadTagTypes();
        return self::$_tagTypes;
    }


    /**
     *
     * @param  string $key
     * @return mixed  Varien_Object|null 
     */
    public function getTypeInfo($key)
    {
        $this->_loadTagTypes();
        return isset(self::$_tagTypes[$key]) ? self::$_tagTypes[$key] : null;
    }
}