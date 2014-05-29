<?php

namespace Harp\Db\Test\Unit\Query;

use Harp\Db\Test\Repo;
use Harp\Db\Test\Model;
use Harp\Core\Model\Models;
use Harp\Query\SQL;
use Harp\Db\Query\Delete;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass Harp\Db\Query\Delete
 */
class DeleteTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getRepo
     */
    public function testConstruct()
    {
        $repo = new Repo\City('Harp\Db\Test\Model\City');

        $delete = new Delete($repo);

        $this->assertSame($repo, $delete->getRepo());
        $this->assertEquals([new SQL\Aliased('City')], $delete->getFrom());
    }

    /**
     * @covers ::models
     */
    public function testModels()
    {
        $repo = new Repo\City('Harp\Db\Test\Model\City');

        $delete = new Delete($repo);

        $models = new Models([new Model\City(['id' => 5]), new Model\City(['id' => 12])]);

        $delete->models($models);

        $this->assertEquals('DELETE FROM City WHERE (id IN (5, 12))',$delete->humanize());
    }
}
