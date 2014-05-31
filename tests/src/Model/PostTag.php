<?php

namespace Harp\Harp\Test\Model;

use Harp\Core\Model\AbstractModel;
use Harp\Harp\Test\Repo;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PostTag extends AbstractModel {

    public function getRepo()
    {
        return Repo\PostTag::get();
    }

    public $id;
    public $postId;
    public $tagId;

    public function getTag()
    {
        return Repo\PostTag::get()->loadLink($this, 'tag')->get();
    }

    public function setTag(Tag $tag)
    {
        return Repo\PostTag::get()->loadLink($this, 'tag')->set($tag);
    }

    public function getPost()
    {
        return Repo\PostTag::get()->loadLink($this, 'post')->get();
    }

    public function setPost(Post $post)
    {
        return Repo\PostTag::get()->loadLink($this, 'post')->set($post);
    }
}
