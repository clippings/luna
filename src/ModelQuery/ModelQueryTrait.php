<?php namespace CL\Luna\ModelQuery;

use CL\Luna\Schema\Schema;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Log;
use CL\Atlas\DB;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
trait ModelQueryTrait {

    protected $schema;

    public function setSchema(Schema $schema)
    {
        $this->schema = $schema;
        $this->db = DB::instance($schema->getDb());

        return $this;
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getRel($name)
    {
        return $this->schema->getRel($name);
    }

    public function addToLog()
    {
        if (Log::getEnabled())
        {
            Log::add($this->humanize());
        }
    }

    public function scope($scope)
    {
        call_user_func(array($this->getSchema()->getModelClass(), 'scope'.ucfirst($scope)), $this);

        return $this;
    }

    public function whereKey($key)
    {
        return $this->where([$this->getSchema()->getPrimaryKey() => $key]);
    }

    public function joinRels($rels)
    {
        $rels = Arr::toAssoc((array) $rels);

        $this->joinNestedRels($this->getSchema(), $rels);

        return $this;
    }

    public function joinNestedRels($schema, array $rels, $parent = NULL)
    {
        foreach ($rels as $name => $childRels)
        {
            $rel = $schema->getRel($name);
            $rel->joinRel($this, $parent);

            if ($childRels)
            {
                $this->joinNestedRels($rel->getForeignSchema(), $childRels, $name);
            }
        }
    }
}