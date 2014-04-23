<?php namespace CL\Luna\Rel;

use CL\Luna\Mapper;
use CL\Luna\Util\Arr;
use CL\Luna\Util\Storage;
use CL\Luna\Model\Model;
use CL\Luna\ModelQuery\RelJoinInterface;
use CL\Atlas\Query\AbstractQuery;
use CL\Luna\Schema\Schema;
use Closure;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class HasOne extends Mapper\AbstractRelOne implements RelJoinInterface
{
    protected $foreignKey;

    public function __construct($name, Schema $schema, Schema $foreignSchema, array $options = array())
    {
        $this->foreignKey = $schema->getName().'Id';

        parent::__construct($name, $schema, $foreignSchema, $options);
    }

    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    public function getKey()
    {
        return $this->getPrimaryKey();
    }

    public function getForeignSchema()
    {
        return $this->foreignSchema;
    }

    public function hasForeign(array $models)
    {
        return ! empty(Arr::extractUnique($models, $this->foreignKey));
    }

    public function loadForeign(array $models)
    {
        return $this
            ->getForeignSchema()
            ->select([
                $this->getKey() => Arr::extractUnique($models, $this->foreignKey)
            ]);
    }

    public function linkToForeign(array $models, array $foreign)
    {
        return Storage::combineArrays($models, $foreign, function($model, $foreign){
            return $model->{$this->getKey()} == $foreign->{$this->getForeignKey()};
        });
    }

    public function update(Mapper\AbstractNode $model, Mapper\AbstractLink $link)
    {
        if ($link->isChanged())
        {
            $link->get()->{$this->getForeignKey()} = $model->{$this->getKey()};
            $link->getOriginal()->{$this->getForeignKey()} = NULL;
        }
    }

    public function joinRel(AbstractQuery $query, $parent)
    {
        $columns = [$this->getForeignKey() => $this->getForeignSchema()->getPrimaryKey()];

        $condition = new RelJoinCondition($parent, $this->getName(), $columns, $this->getForeignSchema());

        $query->joinAliased($this->getForeignTable(), $this->getName(), $condition);
    }
}
