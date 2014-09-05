<?php

namespace Harp\Harp\Test\Repo;

use Harp\Harp\Test\TestModel\City;
use Harp\Harp\Config;
use Harp\Harp\Repo;
use Harp\Harp\Test\AbstractTestCase;
use Harp\Harp\Repo\Container;

/**
 * @coversDefaultClass Harp\Harp\Repo\Container
 *
 * @author     Ivan Kerin <ikerin@gmail.com>
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://spdx.org/licenses/BSD-3-Clause
 */
class ContainerTest extends AbstractTestCase
{
    /**
     * @covers ::get
     * @covers ::has
     * @covers ::set
     * @covers ::clear
     */
    public function testGetterSetter()
    {
        $class = 'Harp\Harp\Test\TestModel\City';

        $this->assertFalse(Container::has($class));
        $repo = Container::get($class);

        $this->assertTrue(Container::has($class));

        $this->assertInstanceOf('Harp\Harp\Repo', $repo);
        $this->assertEquals($class, $repo->getModelClass());

        $this->assertSame($repo, Container::get($class));

        $repo2 = new Repo(new Config($class));

        Container::set($class, $repo2);

        $this->assertSame($repo2, Container::get($class));

        Container::clear();

        $this->assertFalse(Container::has($class));
    }

    /**
     * @covers ::newRepo
     * @covers ::setConfigClass
     */
    public function testNewRepo()
    {
        $repo = Container::newRepo('Harp\Harp\Test\TestModel\City');

        $this->assertInstanceOf('Harp\Harp\Repo', $repo);
        $this->assertInstanceOf('Harp\Harp\Config', $repo->getConfig());

        $this->assertEquals('Harp\Harp\Test\TestModel\City', $repo->getModelClass());

        Container::setConfigClass('Harp\Harp\Test\Repo\TestConfig');

        $repo = Container::newRepo('Harp\Harp\Test\TestModel\City');

        $this->assertInstanceOf('Harp\Harp\Repo', $repo);
        $this->assertInstanceOf('Harp\Harp\Test\Repo\TestConfig', $repo->getConfig());

        $this->setExpectedException('InvalidArgumentException', 'Config class Harp\Harp\Test\TestModel\City must be a subclass of Harp\Harp\Config');

        Container::setConfigClass('Harp\Harp\Test\TestModel\City');
    }

    /**
     * @covers ::getActualClass
     * @covers ::setActualClass
     * @covers ::hasActualClass
     */
    public function testActualClass()
    {
        $class = 'Harp\Harp\Test\TestModel\City';
        $actual = 'Harp\Harp\Test\TestModel\Country';

        $this->assertFalse(Container::hasActualClass($class));

        Container::setActualClass($class, $actual);

        $this->assertTrue(Container::hasActualClass($class));

        $this->assertEquals($actual, Container::getActualClass($class));

        Container::clear();

        $this->assertFalse(Container::hasActualClass($class));
    }

    /**
     * @covers ::setActualClasses
     */
    public function testActualClasses()
    {
        $class = 'Harp\Harp\Test\TestModel\City';
        $actual = 'Harp\Harp\Test\TestModel\Country';

        $this->assertFalse(Container::hasActualClass($class));

        Container::setActualClasses([$class => $actual]);

        $this->assertTrue(Container::hasActualClass($class));

        $this->assertEquals($actual, Container::getActualClass($class));

        Container::clear();

        $this->assertFalse(Container::hasActualClass($class));
    }

    /**
     * @covers ::get
     */
    public function testGetActualClasses()
    {
        $class = 'Harp\Harp\Test\TestModel\Country';
        $actual = 'Harp\Harp\Test\TestModel\City';

        $this->assertEquals($class, Container::get($class)->getModelClass());

        Container::setActualClass($class, $actual);

        $this->assertEquals($class, Container::get($class)->getModelClass());

        Container::clear();
        Container::setActualClass($class, $actual);

        $this->assertEquals($actual, Container::get($class)->getModelClass());

        Container::clear();

        $this->assertEquals($class, Container::get($class)->getModelClass());
    }

    /**
     * @covers ::get
     */
    public function testGet()
    {
        $class1 = 'Harp\Harp\Test\TestModel\Country';
        $actual1 = 'Harp\Harp\Test\TestModel\City';

        $class2 = 'Harp\Harp\Test\TestModel\User';
        $actual2 = 'Harp\Harp\Test\TestModel\Profile';

        Container::setActualClass($class1, $actual1);
        Container::setActualClass($class2, $actual2);

        $this->assertSame(Container::get($class1), Container::get($actual1));
        $this->assertSame(Container::get($actual2), Container::get($class2));
    }
}
