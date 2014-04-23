<?php namespace CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Integer extends AbstractField
{
    public function load($value)
    {
        return is_numeric($value) ? (int) $value : null;
    }
}
