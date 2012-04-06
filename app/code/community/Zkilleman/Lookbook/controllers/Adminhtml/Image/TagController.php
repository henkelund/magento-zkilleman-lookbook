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

class Zkilleman_Lookbook_Adminhtml_Image_TagController
        extends Mage_Adminhtml_Controller_Action
{
    public function autocompleteAction()
    {
        $result = array();
        $q = $this->getRequest()->getParam('q');
        if (!empty($q)) {
            $escQ = str_replace(array('=', '%', '_'), array('==', '=%', '=_'), $q);
            $tags = Mage::getModel('lookbook/indexer_tag')->getCollection();
            $tags->getSelect()
                    ->where('name LIKE ? ESCAPE "="', '%' . $escQ . '%')
                    ->order('count DESC')
                    ->limit(5);
            foreach ($tags as $tag) {
                $data = $tag->getData();
                $data['html'] = preg_replace(
                        sprintf('/(%s)/i', preg_quote($q, '/')),
                        '<strong>$1</strong>',
                        htmlspecialchars($data['name'])) .
                        sprintf(' (%s)', $data['count']);
                $result[] = sprintf(
                        '<li rel="%s">%s</li>', $data['name'], $data['html']);
            }
        }
        $this->getResponse()
                ->setBody(sprintf('<ul>%s</ul>', implode('', $result)));
    }
    
    public function typeAction()
    {
        $response = array();
        $helper = Mage::helper('lookbook');
        $type = $this->getRequest()->getParam('type');
        $typeInfo = Mage::getModel('lookbook/config')->getTypeInfo($type);
        if ($typeInfo) {
            $renderer = 'lookbook/adminhtml_image_tag_type_' . $typeInfo['key'];
            if (isset($typeInfo['admin_renderer'])) {
                $renderer = $typeInfo['admin_renderer'];
            }
            $block = $this->getLayout()->createBlock($renderer);
            if (!$block) {
                $response['error'] = sprintf(
                        $helper->__('No such block "%s".'), $renderer);
            } else {
                $block->setData($this->getRequest()->getParams());
                $response['html'] = $block->toHtml();
            }
        } else {
            $response['error'] = sprintf($helper->__('Invalid type "%s".'), $type);
        }
        $this->getResponse()
                ->setHeader('Content-type', 'application/json')
                ->setBody(Mage::helper('core')->jsonEncode($response));
    }
}
