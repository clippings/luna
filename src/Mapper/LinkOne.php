<?php namespace CL\Luna\Mapper;

use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class LinkOne extends AbstractLink
{
    protected $current;
    protected $original;

    public function __construct(AbstractRel $rel, AbstractNode $current)
    {
        parent::__construct($rel);

        $this->current = $current;
        $this->original = $current;
    }

    public function set(AbstractNode $current)
    {
        $this->current = $current;

        return $this;
    }

    public function get()
    {
        return $this->current;
    }

    public function getOriginal()
    {
        return $this->original;
    }

    public function isChanged()
    {
        return $this->current !== $this->original;
    }

    public function getAll()
    {
        $all = new SplObjectStorage();
        $all->attach($this->current);
        $all->attach($this->original);

        return $all;
    }
}
