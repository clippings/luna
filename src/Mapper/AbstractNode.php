<?php namespace CL\Luna\Mapper;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractNode
{
    const PENDING = 1;
    const DELETED = 2;
    const PERSISTED = 3;
    const NOT_LOADED = 4;

    abstract public function getId();
    abstract public function isChanged();
    abstract public function getSchema();

    public $state;

    public function __construct($state = self::PENDING)
    {
        $this->state = $state;
    }

    public function setStateLoaded()
    {
        $this->state = $this->getId() ? self::PERSISTED : self::PENDING;

        return $this;
    }

    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    public function getState()
    {
        return $this->state;
    }

    public function isPersisted()
    {
        return $this->state === self::PERSISTED;
    }

    public function isPending()
    {
        return $this->state === self::PENDING;
    }

    public function isDeleted()
    {
        return $this->state === self::DELETED;
    }

    public function isNotLoaded()
    {
        return $this->state === self::NOT_LOADED;
    }


}
