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

class Zkilleman_Lookbook_Block_Slideshow
    extends Zkilleman_Lookbook_Block_Widget_Abstract
{
    protected $_template = 'lookbook/slideshow.phtml';
    
    /**
     * Minimum interval in seconds
     *
     * @var float 
     */
    protected $_minInterval = 0.5;
    
    /**
     * Default interval in seconds
     *
     * @var float 
     */
    protected $_defaultInterval = 5.0;
    
    /**
     * Returns the slide interval in milliseconds
     *
     * @return int 
     */
    public function getInterval()
    {
        $interval = $this->hasData('interval') ?
                        $this->getData('interval') : $this->_defaultInterval;
        return intval(max(floatval($interval), $this->_minInterval)*1000);
    }
    
    /**
     * Returns the items for this slideshow
     *
     * @return array
     */
    public function getItems()
    {
        $items = $this->getImageBlocks();
        $numFakes = max(1, (int) $this->getNumFakes());
        if (count($items) > 0) {
            for ($i = 0; $i < $numFakes; ++$i) {
                $first = clone $items[$i*2];
                $last  = clone $items[count($items) - (($i*2) + 1)];
                $first->setIsFake(true)
                            ->setHtmlId($first->getHtmlId() . '_after_' . $i)
                            ->setNoImageHtmlCache(true);
                if ($first->getChild('tags')) {
                    $first->setChild('tags', clone $first->getChild('tags'));
                }
                $last->setIsFake(true)
                            ->setHtmlId($last->getHtmlId() . '_before_' . $i)
                            ->setNoImageHtmlCache(true);
                if ($last->getChild('tags')) {
                    $last->setChild('tags', clone $last->getChild('tags'));
                }
                array_unshift($items, $last);
                array_push($items, $first);
            }
        }
        return $items;
    }
}