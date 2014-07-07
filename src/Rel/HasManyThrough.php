<?php

namespace Harp\Harp\Rel;

use Harp\Harp\Repo;
use Harp\Core\Model\AbstractModel;
use Harp\Core\Model\Models;
use Harp\Core\Repo\LinkMany;
use Harp\Core\Rel\AbstractRelMany;
use Harp\Core\Rel\DeleteManyInterface;
use Harp\Core\Rel\InsertManyInterface;
use Harp\Query\AbstractWhere;
use Harp\Query\SQL\SQL;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class HasManyThrough extends AbstractRelMany implements RelInterface, DeleteManyInterface, InsertManyInterface
{
    protected $key;
    protected $foreignKey;
    protected $through;

    public function __construct($name, Repo $repo, Repo $foreignRepo, $through, array $options = array())
    {
        $this->through = $through;

        parent::__construct($name, $repo, $foreignRepo, $options);
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        if (! $this->foreignKey) {
            $this->foreignKey = lcfirst($this->getForeignRepo()->getTable()).'Id';
        }

        return $this->foreignKey;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        if (! $this->key) {
            $this->key = lcfirst($this->getRepo()->getTable()).'Id';
        }

        return $this->key;
    }

    /**
     * @return RelInterface
     */
    public function getThroughRel()
    {
        return $this->getRepo()->getRel($this->through);
    }

    /**
     * @return Repo
     */
    public function getThroughRepo()
    {
        return $this->getThroughRel()->getForeignRepo();
    }

    /**
     * @return string
     */
    public function getThroughTable()
    {
        return $this->getThroughRel()->getName();
    }

    /**
     * @return string
     */
    public function getThroughKey()
    {
        return $this->getName().'Key';
    }

    /**
     * @param  Models  $models
     * @return boolean
     */
    public function hasForeign(Models $models)
    {
        return ! $models->isEmptyProperty($this->getRepo()->getPrimaryKey());
    }

    /**
     * @param  Models $models
     * @param  int $flags
     * @return AbstractModel[]
     */
    public function loadForeign(Models $models, $flags = null)
    {
        $throughKey = $this->getThroughTable().'.'.$this->getThroughRel()->getForeignKey();
        $throughForeignKey = $this->getThroughTable().'.'.$this->getKey();
        $repo = $this->getForeignRepo();

        $keys = $models->getIds();

        $select = $repo->findAll()
            ->column($throughKey, $this->getThroughKey())
            ->joinRels([$this->through])
            ->whereIn($throughForeignKey, $keys);

        return $select->loadRaw($flags);
    }

    /**
     * @param  AbstractModel $model
     * @param  AbstractModel $foreign
     * @return boolean
     */
    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->getId() == $foreign->{$this->getThroughKey()};
    }

    /**
     * @param  AbstractWhere $query
     * @param  strng         $parent
     */
    public function join(AbstractWhere $query, $parent)
    {
        $alias = $this->getName();
        $conditions = [
            "$alias.{$this->getForeignRepo()->getPrimaryKey()}" => "{$this->through}.{$this->getForeignKey()}"
        ];

        if ($this->getForeignRepo()->getSoftDelete()) {
            $conditions["$alias.deletedAt"] = new SQL('IS NULL');
        }

        $this->getThroughRel()->join($query, $parent);

        $query
            ->joinAliased($this->getForeignRepo()->getTable(), $alias, $conditions);
    }

    public function delete(LinkMany $link)
    {
        $through = $link->getModel()->getLink($this->through);
        $removedIds = $link->getRemoved()->getIds();

        $removedItems = $through->get()->filter(function ($item) use ($removedIds) {
            return in_array($item->{$this->getForeignKey()}, $removedIds);
        });

        $through->get()->removeAll($removedItems);

        foreach ($removedItems as $item) {
            $item->delete();
        }

        return $removedItems;
    }

    public function insert(LinkMany $link)
    {
        $inserted = new Models();

        if (count($link->getAdded()) > 0) {
            $through = $link->getModel()->getLink($this->through);
            $repo = $this->getThroughRepo();

            foreach ($link->getAdded() as $added) {
                $inserted->add($repo->newModel([
                    $this->getKey() => $link->getModel()->getId(),
                    $this->getForeignKey() => $added->getId(),
                ]));
            }

            $through->get()->addAll($inserted);
        }

        return $inserted;
    }
}
