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

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'lookbook/image'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('lookbook/image'))
    ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ), 'Image ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => ''
        ), 'Title')
    ->addColumn('caption', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Caption')
    ->addColumn('file', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => ''
        ), 'Title')
    ->addColumn('ratio', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000'
        ), 'Ratio')
    ->addColumn('focus_x', Varien_Db_Ddl_Table::TYPE_DECIMAL, '5,4', array(
        'nullable'  => false,
        'default'   => '0.5000'
        ), 'Horizontal Focus Point')
    ->addColumn('focus_y', Varien_Db_Ddl_Table::TYPE_DECIMAL, '5,4', array(
        'nullable'  => false,
        'default'   => '0.5000'
        ), 'Vertical Focus Point')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Update Time')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
        ), 'Is Active')
    ->addIndex($installer->getIdxName('lookbook/image', array('is_active')),
        array('is_active'))
    ->setComment('Lookbook Image Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'lookbook/image_tag'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('lookbook/image_tag'))
    ->addColumn('tag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ), 'Tag ID')
    ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
        ), 'Image ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => ''
        ), 'Name')
    ->addColumn('x', Varien_Db_Ddl_Table::TYPE_DECIMAL, '5,4', array(
        'nullable'  => false,
        'default'   => '-1.0000'
        ), 'Horizontal Position')
    ->addColumn('y', Varien_Db_Ddl_Table::TYPE_DECIMAL, '5,4', array(
        'nullable'  => false,
        'default'   => '-1.0000'
        ), 'Vertical Position')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => ''
        ), 'Tag Type')
    ->addColumn('reference', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
        ), 'Reference')
    ->addIndex($installer->getIdxName('lookbook/image_tag', array('image_id')),
        array('image_id'))
    ->addIndex($installer->getIdxName('lookbook/image_tag', array('type')),
        array('type'))
    ->addIndex($installer->getIdxName('lookbook/image_tag', array('reference')),
        array('reference'))
    ->addForeignKey(
        $installer->getFkName(
            'lookbook/image_tag',
            'image_id',
            'lookbook/image',
            'image_id'
        ),
        'image_id', $installer->getTable('lookbook/image'), 'image_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Lookbook Image Tag Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'lookbook/image_tag_index'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('lookbook/image_tag_index'))
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
        'primary'   => true
        ), 'Name')
    ->addColumn('count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'Count')
    ->setComment('Lookbook Image Tag Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'lookbook/image_tag_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('lookbook/image_tag_indexer_tmp'))
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => '',
        'primary'   => true
        ), 'Name')
    ->addColumn('count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false
        ), 'Count')
    ->setComment('Lookbook Image Tag Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'lookbook/image_set'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('lookbook/image_set'))
    ->addColumn('set_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true
        ), 'Set ID')
    ->addColumn('handle', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => ''
        ), 'Handle')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        'default'   => ''
        ), 'Name')
    ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Description')
    ->addColumn('cover_image', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true
        ), 'Cover Image')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Update Time')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
        ), 'Is Active')
    ->addIndex(
        $installer->getIdxName(
            'lookbook/image_set',
            array('handle'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('handle'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('lookbook/image_set', array('cover_image')),
        array('cover_image'))
    ->addForeignKey(
        $installer->getFkName(
            'lookbook/image_set',
            'cover_image',
            'lookbook/image',
            'image_id'
        ),
        'cover_image', $installer->getTable('lookbook/image'), 'image_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_SET_NULL)
    ->addIndex($installer->getIdxName('lookbook/image_set', array('is_active')),
        array('is_active'))
    ->setComment('Lookbook Image Set Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'lookbook/image_set_image'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('lookbook/image_set_image'))
    ->addColumn('set_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
        ), 'Set Id')
    ->addColumn('image_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
        ), 'Image Id')
    ->addColumn('order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0'
        ), 'Sort Order')
    ->addIndex($installer->getIdxName('lookbook/image_set_image', array('set_id')),
        array('set_id'))
    ->addForeignKey(
        $installer->getFkName(
            'lookbook/image_set_image',
            'set_id',
            'lookbook/image_set',
            'set_id'
        ),
        'set_id', $installer->getTable('lookbook/image_set'), 'set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addIndex($installer->getIdxName('lookbook/image_set_image', array('image_id')),
        array('image_id'))
    ->addForeignKey(
        $installer->getFkName(
            'lookbook/image_set_image',
            'image_id',
            'lookbook/image',
            'image_id'
        ),
        'image_id', $installer->getTable('lookbook/image'), 'image_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addIndex($installer->getIdxName('lookbook/image_set_image', array('order')),
        array('order'))
    ->setComment('Lookbook Image Set Image Table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
