<?php

namespace CL\Luna\ModelQuery;

use CL\Luna\Model\Store;
use CL\Luna\Mapper\Repo;
use CL\Luna\Mapper\AbstractNode;
use CL\Luna\Util\log(§);
use CL\Luna\Util\Arr;
use CL\Atlas\Query;
use CL\Atlas\SQL\SQL;
use PDO;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Union extends Query\Union {

    use ModelQueryTrait;

    public function load()
    {
        $models = $this->loadRaw();

        return Repo::get()->getCanonicalArray($models);
    }

    public function loadRaw()
    {
        if ($this->getStore()->getPolymorphic()) {
            $this->prependColumn($this->getStore()->getTable().'.polymorphicClass');
        }

        $pdoStatement = $this
            ->addToLog()
            ->execute();

        if ($this->getStore()->getPolymorphic()) {
            $pdoStatement->setFetchMode(
                PDO::FETCH_CLASS | PDO::FETCH_CLASSTYPE
            );
        } else {
            $pdoStatement->setFetchMode(
                PDO::FETCH_CLASS,
                $this->getStore()->getModelClass()
            );
        }

        return $pdoStatement->fetchAll();
    }

    public function execute()
    {
        $this->addToLog();

        return parent::execute();
    }
}
