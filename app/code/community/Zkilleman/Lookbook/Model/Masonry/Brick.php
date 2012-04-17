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

class Zkilleman_Lookbook_Model_Masonry_Brick extends Varien_Object
{
    
    /**
     * An array of Zkilleman_Lookbook_Model_Masonry_Brick objects representing
     * the child bricks of this brick.
     *
     * @var array
     */
    protected $_children = array();
    
    /**
     * Returns the width of this brick.
     *
     * @return int
     */
    public function getWidth()
    {
        return (int) $this->getData('width');
    }
    
    /**
     * Returns the height of this brick
     *
     * @return int
     */
    public function getHeight()
    {
        return (int) $this->getData('height');
    }

    /**
     * Returns the y coordinate of this brick.
     *
     * @return int 
     */
    public function getTop()
    {
        return (int) $this->getData('top');
    }
    
    /**
     * Sets the y coordinate of this brick.
     * If $resize is true the height is changed so the bottom side doesn't move.
     *
     * @param  int  $x
     * @param  bool $resize
     * @return Zkilleman_Lookbook_Model_Masonry_Brick 
     */
    public function setTop($y, $resize = true)
    {
        $oldY = $this->getTop();
        $this->setData('top', (int) $y);
        if ($resize) {
            $this->setHeight($this->getHeight() + $oldY - $this->getTop());
        }
        return $this;
    }

    /**
     * Returns the right side x coordinate of this brick.
     *
     * @return int 
     */
    public function getRight()
    {
        return $this->getLeft() + $this->getWidth();
    }

    /**
     * Sets the right position of this brick.
     * If $resize is true the width is changed so the x coordinate doesn't change.
     *
     * @param  int  $y
     * @param  bool $resize
     * @return Zkilleman_Lookbook_Model_Masonry_Brick 
     */
    public function setRight($x, $resize = true)
    {
        if ($resize) {
            $this->setWidth(intval($x) - $this->getLeft());
        } else {
            $this->setLeft(intval($x) - $this->getRight(), false);
        }
        return $this;
    }

    /**
     * Returns the bottom side y coordinate of this brick.
     *
     * @return int 
     */
    public function getBottom()
    {
        return $this->getTop() + $this->getHeight();
    }

    /**
     * Sets the bottom position of this brick.
     * If $resize is true the height is changed so the y coordinate doesn't change.
     *
     * @param  int  $y
     * @param  bool $resize
     * @return Zkilleman_Lookbook_Model_Masonry_Brick 
     */
    public function setBottom($y, $resize = true)
    {
        if ($resize) {
            $this->setHeight(intval($y) - $this->getTop());
        } else {
            $this->setTop(intval($y) - $this->getBottom(), false);
        }
        return $this;
    }
    
    /**
     * Returns the x coordinate of this brick.
     *
     * @return int 
     */
    public function getLeft()
    {
        return (int) $this->getData('left');
    }
    
    /**
     * Sets the x coordinate of this brick.
     * If $resize is true the width is changed so the right side doesn't move.
     *
     * @param  int  $x
     * @param  bool $resize
     * @return Zkilleman_Lookbook_Model_Masonry_Brick 
     */
    public function setLeft($x, $resize = true)
    {
        $oldX = $this->getLeft();
        $this->setData('left', (int) $x);
        if ($resize) {
            $this->setWidth($this->getWidth() + $oldX - $this->getLeft());
        }
        return $this;
    }

    /**
     * Returns the bounds of this brick (top, left, right, bottom).
     *
     * @return array 
     */
    public function getBounds()
    {
        return array(
            $this->getTop(),
            $this->getLeft(),
            $this->getRight(),
            $this->getBottom()
        );
    }

    /**
     * Sets the position of this brick.
     *
     * @param  int $x
     * @param  int $y
     * @return Zkilleman_Lookbook_Model_Masonry_Brick 
     */
    public function setPosition($x = false, $y = false)
    {
        if ($x !== false) {
            $this->setLeft($x, false);
        }
        if ($y !== false) {
            $this->setTop($y, false);
        }
        return $this;
    }

    /**
     * Checks whether the given brick intersects with this brick.
     * If true the given brick is returned.
     * If given multiple bricks (an array) the first intersecting brick is rerturned.
     *
     * @param  mixed array|Zkilleman_Lookbook_Model_Masonry_Brick $other
     * @return mixed bool|Zkilleman_Lookbook_Model_Masonry_Brick
     */
    public function intersects($other)
    {
        if (is_array($other)) {
            foreach ($other as $o) {
                if ($this->intersects($o)) {
                    return $o;
                }
            }
            return false;
        }
        $mb = $this->getBounds();
        $ob = $other->getBounds();
        return !(
            $ob[0] >= $mb[3] || // other top is below this bottom or
            $ob[1] >= $mb[2] || // other left is right of this right or
            $ob[2] <= $mb[1] || // other right is left of this left or
            $ob[3] <= $mb[0]    // other bottom is above this top
        ) ? $other : false;
    }
    
    /**
     * Returns true if the given brick can fit inside this brick.
     *
     * @param  Zkilleman_Lookbook_Model_Masonry_Brick $other
     * @return bool 
     */
    public function canContain(Zkilleman_Lookbook_Model_Masonry_Brick $other)
    {
        return $this->getWidth() >= $other->getWidth() &&
                    $this->getHeight() >= $other->getHeight();
    }
    
    /**
     * Returns all children.
     *
     * @return array 
     */
    public function getChildren()
    {
        return $this->_children;
    }
    
    /**
     * Returns all children that spans below the given y coordinate.
     * 
     * @param  int   $y
     * @return array 
     */
    public function getChildrenBelow($y)
    {
        $lower = array();
        foreach ($this->_children as $child) {
            if ($child->getBottom() >= $y) {
                $lower[] = $child;
            }
        }
        return $lower;
    }
    
    /**
     * Adds a child brick to this brick and positions it as close
     * to the top left corner as possible.
     *
     * @param  Zkilleman_Lookbook_Model_Masonry_Brick $brick
     * @return Zkilleman_Lookbook_Model_Masonry_Brick 
     */
    public function append(Zkilleman_Lookbook_Model_Masonry_Brick $brick)
    {
        if (in_array($brick, $this->_children, true)) {
            // a brick instance may only be added once inside another brick
            return $this;
        }
        
        $brick->setPosition(0, 0);
        $highestRowBottom = pow(2, 16);

        // Loop backwars through the children to find the most recently added brick
        // that is smaller than $brick. A brick can't be positioned above an already
        // positioned smaller brick so there's no need to try.
        for ($i = count($this->_children) - 1; $i >= 0; --$i) {
            if ($brick->canContain($this->_children[$i])) {
                $brick->setPosition(0, $this->_children[$i]->getTop());
                $highestRowBottom = $this->_children[$i]->getHighestRowBottom();
                break;
            }
        }
        
        // There's no need to do collision detection with bricks that are above
        // the initial position of $brick
        $lowerSiblings = $this->getChildrenBelow($brick->getTop());
        
        // Move $brick down and right until it no longer intersects with siblings
        while (false !== ($intersector = $brick->intersects($lowerSiblings))) {
            // Move brick to the right of the colliding brick
            $brick->setLeft($intersector->getRight(), false);
            // If the bottom of the colliding brick is higher than previous colliding
            // bricks on the same row, thats where we have to start if we need
            // to break into another row.
            $highestRowBottom =
                        min($highestRowBottom, $intersector->getBottom());
            // If $brick is out of bounds, move it to a new row
            if ($brick->getRight() > $this->getRight()) {
                $brick->setPosition(0, $highestRowBottom);
                $highestRowBottom = pow(2, 16);
            }
        }
        
        // Save highest (closest to the top) brick bottom coordinate of $brick's
        // "row" so that future bricks can continue where we left off
        $brick->setHighestRowBottom($highestRowBottom);
        $this->_children[] = $brick;
        
        // Update the size of this brick
        $this->setBottom(max($this->getBottom(), $brick->getBottom()));
        return $this;
    }
    
    /**
     * Returns the metrics of this as a CSS style string.
     *
     * @return string 
     */
    public function __toString()
    {
        $padding = 2*((int) $this->getPadding());
        return sprintf(
                'top: %spx; left: %spx; width: %spx; height: %spx;',
                $this->getTop(),
                $this->getLeft(),
                $this->getWidth() - $padding,
                $this->getHeight() - $padding);
    }
}