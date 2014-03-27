<?php namespace CL\Luna\Field;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractField
{
    protected $name;

    public function __construct($name, array $properties = NULL)
    {
        $this->name = $name;

        if ($properties)
        {
            foreach ($properties as $propertyName => $value)
            {
                $this->$propertyName = $value;
            }
        }
    }

    public function getName()
    {
        return $this->name;
    }

    public function load($value)
    {
        return $value;
    }

    public function save($value)
    {
        return $value;
    }
}
