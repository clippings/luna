<?php

namespace Harp\Harp\Rel;

use Harp\Harp\Repo;
use Harp\Harp\Config;
use Harp\Harp\AbstractModel;
use Harp\Harp\Model\Models;
use Harp\Harp\Repo\LinkOne;
use Harp\Query\AbstractWhere;

/**
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class BelongsTo extends AbstractRelOne implements UpdateOneInterface, FindModelsInterface
{
    use LoadModelsTrait;

    /**
     * @var string
     */
    protected $key;

    public function __construct($name, Config $config, $foreignModel, array $options = array())
    {
        $this->key = $name.'Id';

        parent::__construct($name, $config, $foreignModel, $options);
    }

    /**
     * @param  AbstractModel $model
     * @param  AbstractModel $foreign
     * @return boolean
     */
    public function areLinked(AbstractModel $model, AbstractModel $foreign)
    {
        return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->getRepo()->getConfig()->getPrimaryKey();
    }

    /**
     * @param  LinkOne       $link
     */
    public function update(LinkOne $link)
    {
        $link->getModel()->{$this->getKey()} = $link->get()->getId();
    }

    /**
     * @param  AbstractWhere $query
     * @param  string        $parent
     */
    public function join(AbstractWhere $query, $parent)
    {
        $conditions = [
            "{$this->getName()}.{$this->getForeignKey()}" => "$parent.{$this->getKey()}",
        ];
        $conditions += $this->getSoftDeleteConditions();

        $query->joinAliased($this->getRepo()->getTable(), $this->getName(), $conditions);
    }

}
