<?php namespace CL\Luna\Repo;

use CL\Luna\Schema\Schema;
use CL\Luna\Rel\AbstractRel;
use CL\Luna\Model\Model;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractLink
{
    protected $rel;

    public function __construct(AbstractRel $rel)
    {
        $this->rel = $rel;
    }

    public function getRel()
    {
        return $this->rel();
    }

    public function update(Model $parent)
    {
        $this->rel->update($parent, $this);

        return $this;
    }

    abstract public function getAll();
}
