<?php namespace CL\Luna\Test;

use CL\Luna\Model\Model;
use CL\Luna\Mapper\Repo;
use CL\Luna\Schema\Schema;
use CL\Luna\Schema\SchemaTrait;
use CL\Luna\Field;
use CL\Luna\Rel;

/**
 * @author     Ivan Kerin
 * @copyright  (c) 2014 Clippings Ltd.
 * @license    http://www.opensource.org/licenses/isc-license.txt
 */
class PostTag extends Model {

    use SchemaTrait;

    public $id;
    public $postId;
    public $tagId;

    public function getTag()
    {
        return Repo::get()->loadLink($this, 'tag')->get();
    }

    public function setTag(Tag $tag)
    {
        return Repo::get()->loadLink($this, 'tag')->set($tag);
    }

    public function getPost()
    {
        return Repo::get()->loadLink($this, 'post')->get();
    }

    public function setPost(Post $post)
    {
        return Repo::get()->loadLink($this, 'post')->set($post);
    }

    public static function initialize(Schema $schema)
    {
        $schema
            ->setRels([
                new Rel\BelongsTo('post', $schema, User::getSchema()),
                new Rel\BelongsTo('tag', $schema, User::getSchema()),
            ]);

        $schema
            ->setFields([
                new Field\Integer('id'),
                new Field\Integer('postId'),
                new Field\Integer('tagId'),
            ]);
    }

}
