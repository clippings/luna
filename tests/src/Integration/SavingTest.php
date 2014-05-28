<?php

namespace CL\Luna\Test\Integration;

use CL\Luna\Test\AbstractTestCase;
use CL\Luna\Test\Repo;
use CL\Luna\Test\Model;
use CL\Atlas\SQL\SQL;

/**
 * @group integration
 */
class SavingTest extends AbstractTestCase {

    public function testBasic()
    {
        $user = Repo\User::get()->find(1);
        $user->name = 'New Name';
        $user->isBlocked = true;

        Repo\User::get()->save($user);

        $this->assertQueries([
            'SELECT User.* FROM User WHERE (User.id = 1) AND (User.deletedAt IS NULL) LIMIT 1',
            'UPDATE User SET name = "New Name", isBlocked = 1 WHERE (id = 1)',
        ]);
    }

    public function testRels()
    {
        $user = Repo\User::get()->find(1);
        $user->name = 'New Name';
        $user->isBlocked = true;

        $address = $user->getAddress();
        $address->location = 'Somewhere else';
        $address->zipCode = '1234';

        $posts = $user->getPosts();

        $post = $posts->getFirst();
        $post->body = 'Changed Body';

        $post = new Model\Post([
            'title' => 'new post',
            'body' => 'Lorem Ipsum',
            'price' => 123.23,
        ]);

        $posts->add($post);

        $tags = Repo\Tag::get()->findAll()->whereIn('id', [1, 2])->load();

        $post->getTags()->addModels($tags);

        $this->assertQueries([
            'SELECT User.* FROM User WHERE (User.id = 1) AND (User.deletedAt IS NULL) LIMIT 1',
            'SELECT Address.* FROM Address WHERE (id IN (1))',
            'SELECT Post.class, Post.* FROM Post WHERE (userId IN (1))',
            'SELECT Tag.* FROM Tag WHERE (id IN (1, 2))',
            'SELECT Tag.*, postTags.postId AS tagsKey FROM Tag JOIN PostTag AS postTags ON postTags.tagId = Tag.id WHERE (postTags.postId IN (NULL))',
        ]);

        Repo\User::get()->save($user);

        $this->assertQueries([
            'SELECT User.* FROM User WHERE (User.id = 1) AND (User.deletedAt IS NULL) LIMIT 1',
            'SELECT Address.* FROM Address WHERE (id IN (1))',
            'SELECT Post.class, Post.* FROM Post WHERE (userId IN (1))',
            'SELECT Tag.* FROM Tag WHERE (id IN (1, 2))',
            'SELECT Tag.*, postTags.postId AS tagsKey FROM Tag JOIN PostTag AS postTags ON postTags.tagId = Tag.id WHERE (postTags.postId IN (NULL))',
            'INSERT INTO Post (id, title, body, price, tags, createdAt, updatedAt, publishedAt, userId, class) VALUES (NULL, "new post", "Lorem Ipsum", "123.23", NULL, NULL, NULL, NULL, NULL, "CL\\Luna\\Test\\Model\\Post")',
            'INSERT INTO PostTag (id, postId, tagId) VALUES (NULL, NULL, 1), (NULL, NULL, 2)',
            'UPDATE User SET name = "New Name", isBlocked = 1 WHERE (id = 1)',
            'UPDATE Address SET zipCode = "1234", location = "Somewhere else" WHERE (id = 1)',
            'UPDATE Post SET body = CASE id WHEN 1 THEN "Changed Body" ELSE body END, userId = CASE id WHEN 5 THEN 1 ELSE userId END WHERE (id IN (1, 5))',
            'UPDATE PostTag SET postId = CASE id WHEN 4 THEN "5" WHEN 5 THEN "5" ELSE postId END WHERE (id IN (4, 5))',
        ]);

        Repo\User::get()->getIdentityMap()->clear();
        Repo\Address::get()->getIdentityMap()->clear();
        Repo\Post::get()->getIdentityMap()->clear();
        Repo\Tag::get()->getIdentityMap()->clear();

        $user = Repo\User::get()->find(1);
        $this->assertEquals('New Name', $user->name);
        $this->assertEquals(true, $user->isBlocked);

        $address = $user->getAddress();
        $this->assertEquals('Somewhere else', $address->location);
        $this->assertEquals('1234', $address->zipCode);

        $posts = $user->getPosts();
        $post = $posts->getFirst();

        $this->assertEquals('Changed Body', $post->body);

        $newPost = Repo\Post::get()->findAll()->where('title', 'new post')->loadFirst();

        $this->assertTrue($posts->has($newPost));

        $this->assertEquals([1, 2], $newPost->getTags()->get()->getIds());
    }
}