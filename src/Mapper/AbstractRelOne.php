<?php

namespace CL\Luna\Mapper;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
abstract class AbstractRelOne extends AbstractRel
{
    public function newForeignNotLoaded()
    {
        return $this->getForeignSchema()->newInstance(null, AbstractNode::NOT_LOADED);
    }

    public function newLink(AbstractNode $foreign, IdentityMap $map)
    {
        $foreign = $map->get($foreign);

        return new LinkOne($this, $foreign);
    }

    public function newEmptyLink()
    {
        return new LinkOne($this, $this->newForeignNotLoaded());
    }
}
