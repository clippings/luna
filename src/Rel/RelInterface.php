<?php

namespace Harp\Harp\Rel;

use Harp\Query\AbstractWhere;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
interface RelInterface
{
    /**
     * @return void
     */
    public function join(AbstractWhere $query, $parent);
}