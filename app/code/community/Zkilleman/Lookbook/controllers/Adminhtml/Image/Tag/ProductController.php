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

class Zkilleman_Lookbook_Adminhtml_Image_Tag_ProductController
        extends Mage_Adminhtml_Controller_Action
{
    public function autocompleteAction()
    {
        $result = array();
        $q = $this->getRequest()->getParam('q');
        if (!empty($q)) {
            $collection = Mage::helper('catalogsearch')
                ->getQuery()->getSearchCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('description')
                ->addSearchFilter($q)
                ->setPageSize(5)
                ->load();
            
            foreach ($collection as $product) {
                $description = strip_tags($product->getDescription());
                $result[] = sprintf(
                        '<li rel="%s"><strong>%s</strong><br /><span class="informal">%s</span></li>', 
                        $product->getId(), 
                        $product->getName(), 
                        Mage::helper('core/string')->substr($description, 0, 30));
            }
        }
        $this->getResponse()
                ->setBody(sprintf('<ul>%s</ul>', implode('', $result)));
    }
}