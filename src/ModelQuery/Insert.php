<?php

namespace CL\Luna\ModelQuery;

use CL\Atlas\Query;
use CL\Luna\Model\AbstractDbRepo;
use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Util\Objects;
use SplObjectStorage;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Insert extends Query\Insert implements SetInterface {

    use ModelQueryTrait;

    private $insertModels;

    public function __construct(AbstractDbRepo $repo)
    {
        $this
            ->setRepo($repo)
            ->into($repo->getTable());
    }

    public function setMultiple(array $values)
    {
        $columns = $this->getRepo()->getFields()->getNames();

        $this->columns($columns);

        $defaultValues = $this->getRepo()->getFieldDefaults();

        foreach ($values as $value) {
            $this->values(array_values(array_merge($defaultValues, $value)));
        }

        return $this;
    }

    public function setModels(SplObjectStorage $models)
    {
        $this->insertModels = $models;
        $changes = Objects::invoke($models, 'getChanges');

        $this->setMultiple($changes);

        return $this;
    }

    public function execute()
    {
        $result = parent::execute();

        if ($this->insertModels) {
            $lastInsertId = $this->getDb()->lastInsertId();

            foreach ($this->insertModels as $model) {
                $model
                    ->setId($lastInsertId)
                    ->resetOriginals()
                    ->setState(AbstractNode::PERSISTED);

                $lastInsertId += 1;
            }

            $this->insertModels = NULL;
        }

        return $result;
    }
}
