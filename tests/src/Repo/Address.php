<?php

namespace CL\Luna\Test\Repo;

use CL\Luna\Model\AbstractDbRepo;
use CL\Luna\Field;
use CL\Luna\Rel;
use CL\Carpo\Assert;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class Address extends AbstractDbRepo {

    private static $instance;

    /**
     * @return PostRepo
     */
    public static function get()
    {
        if (! self::$instance) {
            self::$instance = new Address('CL\Luna\Test\Model\Address');
        }

        return self::$instance;
    }

    public function initialize()
    {
        $this
            ->setRels([
                new Rel\HasMany('users', $this, User::get()),
            ])
            ->setAsserts([
                new Assert\Present('location'),
            ])
            ->setFields([
                new Field\Integer('id'),
                new Field\String('zipCode'),
                new Field\String('location'),
            ]);
    }
}
