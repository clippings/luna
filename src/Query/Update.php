<?php

namespace Harp\Harp\Query;

use Harp\Harp\AbstractDbRepo;
use Harp\Core\Model\Models;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Update extends \Harp\Query\Update {

    use JoinRelTrait;

    /**
     * @var AbstractDbRepo
     */
    private $repo;

    public function __construct(AbstractDbRepo $repo)
    {
        $this->repo = $repo;
        $this->table($repo->getTable());

        parent::__construct($repo->getDbInstance());
    }

    /**
     * @return AbstractDbRepo
     */
    public function getRepo()
    {
        return $this->repo;
    }

    public function models(Models $models)
    {
        $changes = array();

        foreach ($models as $model) {
            $changes[$model->getId()] = $model->saveData()->getChanges();
        }

        $key = $this->repo->getPrimaryKey();

        $this
            ->setMultiple($changes, $key)
            ->whereIn($key, array_keys($changes));

        return $this;
    }
}
