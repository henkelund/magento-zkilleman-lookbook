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

class Zkilleman_Lookbook_Block_Adminhtml_Image_Grid
        extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('lookbookImageGrid');
        $this->setDefaultSort('image_id');
        $this->setDefaultDir('DESC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('lookbook/image')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('image_id', array(
            'header'    => Mage::helper('lookbook')->__('ID'),
            'type'      => 'number',
            'index'     => 'image_id',
        ));
        
        $this->addColumn('file', array(
            'header'            => Mage::helper('lookbook')->__('Image'),
            'align'             => 'center',
            'index'             => 'file',
            'filter'            => false,
            'frame_callback'    => array(get_class($this), 'renderImageColumn')
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('lookbook')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('caption', array(
            'header'    => Mage::helper('lookbook')->__('Caption'),
            'align'     => 'left',
            'index'     => 'caption',
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('lookbook')->__('Creation time'),
            'type'      => 'datetime',
            'index'     => 'created_at',
        ));

        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('lookbook')->__('Update time'),
            'type'      => 'datetime',
            'index'     => 'updated_at',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('lookbook')->__('Is Active'),
            'type'      => 'options',
            'options'   => array(
                '1' => Mage::helper('lookbook')->__('Yes'),
                '0' => Mage::helper('lookbook')->__('No')
            ),
            'index'     => 'is_active',
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('image_id' => $row->getId()));
    }

    public static function renderImageColumn($renderedValue, $row, $column, $isExport)
    {
        if ($isExport) {
            return $renderedValue;
        } else {
            echo 'TODO';
        }
    }
}