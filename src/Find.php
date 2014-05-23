<?php

namespace CL\LunaJsonStore;

use CL\LunaCore\Model\AbstractModel;
use CL\LunaCore\Model\State;
use CL\LunaCore\Save\AbstractFind;
use PDO;

/*
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Find extends AbstractFind
{
    /**
     * @var Select
     */
    private $select;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->select = new Save\Select($repo);

        parent::__construct($repo);
    }

    /**
     * @return array
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public function where($property, $value)
    {
        $this->select->where($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  mixed  $value
     * @return Find   $this
     */
    public function whereNot($property, $value)
    {
        $this->select->whereNot($property, $value);

        return $this;
    }

    /**
     * @param  string $property
     * @param  array  $value
     * @return Find   $this
     */
    public function whereIn($property, array $value)
    {
        $this->select->whereIn($property, $value);

        return $this;
    }

    /**
     * @param  int  $limit
     * @return Find $this
     */
    public function limit($limit)
    {
        $this->select->limit($limit);

        return $this;
    }

    /**
     * @param  int  $offset
     * @return Find $this
     */
    public function offset($offset)
    {
        $this->select->offset($offset);

        return $this;
    }

    /**
     * @return AbstractModel[]
     */
    public function execute()
    {
        if ($this->getRepo()->getInherited()) {
            $this->select->prependColumn($this->getRepo()->getTable().'.class');
            return $this->select->execute()->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE);
        } else {
            return $this->select->execute()->fetchAll(PDO::FETCH_CLASS, $this->getRepo()->getModelClass());
        }
    }

    public function loadIds()
    {
        $statement = ->select->execute();

        return $statement->fetchAll(PDO::FETCH_COLUMN, ''.$this->getRepo()->getPrimaryKey());
    }

    public function loadCount()
    {
        $repo = $this->getRepo();

        $this->select
            ->clearColumns()
            ->column("COUNT({$repo->getTable()}.{$repo->getPrimaryKey()})", 'countAll');

        return $this->select->execute()->fetchColumn();
    }
}
