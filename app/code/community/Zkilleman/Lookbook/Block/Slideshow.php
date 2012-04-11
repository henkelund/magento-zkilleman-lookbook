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
     * Arbitrary min height value
     *
     * @var int 
     */
    protected $_minHeight = 64;
    
    /**
     * Arbitrary max height value
     *
     * @var int 
     */
    protected $_maxHeight = 4096;
    
    /**
     * Approximation of the golden ratio
     *
     * @var int 
     */
    protected $_defaultHeight = '610:377';
    
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
    
    protected static $_instanceCount = 0;
    
    protected function _construct()
    {
        $this->setHtmlId('slideshow_' . self::$_instanceCount++);
        parent::_construct();
    }
    
    public function getHeight()
    {
        $height = $this->hasData('height') ?
                        $this->getData('height') : $this->_defaultHeight;
        
        if (is_numeric($height)) {
            $height = intval($height);
        } else {
            $matches = array();
            if (preg_match('/^(\d+(\.\d+)?):(\d+(\.\d+)?)$/', $height, $matches)) {
                $w = floatval($matches[1]);
                $h = floatval($matches[3]);
                if ($w > 0 && $h > 0) {
                    $height = $this->getWidth()*($h/$w);
                } else {
                    $height = 0;
                }
            } else {
                $height = 0;
            }
        }
        return (int) min($this->_maxHeight, max($this->_minHeight, $height));
    }
    
    public function getInterval()
    {
        $interval = $this->hasData('interval') ?
                        $this->getData('interval') : $this->_defaultInterval;
        return max(floatval($interval), $this->_minInterval)*1000;
    }
}