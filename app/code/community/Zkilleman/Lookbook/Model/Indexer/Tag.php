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

class Zkilleman_Lookbook_Model_Indexer_Tag extends Mage_Index_Model_Indexer_Abstract
{
    /**
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Zkilleman_Lookbook_Model_Image_Tag::ENTITY => array(
            Mage_Index_Model_Event::TYPE_SAVE,
            Mage_Index_Model_Event::TYPE_DELETE
        )
    );

    protected function _construct()
    {
        $this->_init('lookbook/indexer_tag');
    }

    public function getName()
    {
        return Mage::helper('lookbook')->__('Lookbook Image Tags');
    }

    public function getDescription()
    {
        return Mage::helper('lookbook')->__('Index Lookbook image tags');
    }

    /**
     * Register data required by process in event object
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _registerEvent(Mage_Index_Model_Event $event)
    {
        if ($event->getEntity() == Zkilleman_Lookbook_Model_Image_Tag::ENTITY) {
            $tag = $event->getDataObject();
            if ($tag->dataHasChangedFor('name') || 
                    $event->getType() == Mage_Index_Model_Event::TYPE_DELETE) {
                $event->addNewData('reindex_tags', array($tag));
            }
        }
        return $this;
    }

    /**
     * Process event
     *
     * @param Mage_Index_Model_Event $event
     */
    protected function _processEvent(Mage_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        
        if(isset($data['reindex_tags'])) {
            foreach ($data['reindex_tags'] as $tag) {
                $this->_getResource()->reindexTag($tag);
            }
        }
    }
}