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

class Zkilleman_Lookbook_Model_Resource_Indexer_Tag
    extends Mage_Index_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('lookbook/image_tag_index', 'name');
        $this->_isPkAutoIncrement = false;
        $this->_useIsObjectNew = false;
    }
    
    public function reindexTag(Zkilleman_Lookbook_Model_Image_Tag $tag)
    {
        $this->reindexTagName($tag->getName());
        if ($tag->dataHasChangedFor('name') && !$tag->isObjectNew()) {
            $this->reindexTagName($tag->getOrigData('name'));
        }
    }
    
    public function reindexTagName($name)
    {
        $idxTag = Mage::getModel('lookbook/indexer_tag')->load($name);
        if (!$idxTag->getId()) {
            $idxTag->setName($name);
        }

        $write = $this->_getWriteAdapter();

        $select = $write->select()
            ->from($this->getTable('lookbook/image_tag'), array('name'))
            ->where('name = ?', $name);

        $idxTag->setCount($select->query()->rowCount());

        if ($idxTag->getCount() > 0) {
            $idxTag->save();
        } else {
            $idxTag->delete();
        }
    }

    public function reindexAll()
    {
        $this->useIdxTable(false);
        $this->clearTemporaryIndexTable();
        $write = $this->_getWriteAdapter();
        $select = $write->select()
            ->from($this->getTable('lookbook/image_tag'), array(
                'name',
                'count' => 'COUNT(*)'
            ))
            ->group('name');
        $this->insertFromSelect(
                $select, $this->getIdxTable(), array('name', 'count'));
        $this->syncData();
        return $this;
    }
}