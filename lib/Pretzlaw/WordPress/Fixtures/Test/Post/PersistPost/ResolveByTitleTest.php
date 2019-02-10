<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FindByTitleTest.php
 *
 * LICENSE: This source file is created by the company around Mike Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://mike-pretzlaw.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@mike-pretzlaw.de so we can mail you a copy.
 *
 * @package    wp-fixtures
 * @copyright  2018 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-02-02
 */

declare(strict_types=1);

namespace Pretzlaw\WordPress\Fixtures\Test\Post\PersistPost;

use Pretzlaw\WordPress\Fixtures\Entity\Post;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Persist
 *
 * Usually a new unique post will be created all the time.
 * After some tests the database may be filled with lots of clutter.
 * So to control how often and what posts will be recreated
 * you can try reusing posts.
 *
 * Instead of creating new posts all the time existing posts will be reused
 * by resolving one of the following:
 *
 * * ID
 * * GUID
 * * Post title
 *
 * So this yaml only creates three posts
 * and reuses them with every run:
 *
 * ```yaml
 * \Pretzlaw\WordPress\Fixtures\Entity\Post
 *   post_1:
 *     post_title: Foo
 *   post_2:
 *     post_title: Bar
 *   post_3:
 *     post_title: Baz
 * ```
 *
 * @copyright  2018 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-02
 */
class ResolveByTitleTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $someTitle = uniqid('', true);

        $this->fixtures = [
            Post::class => [
                'post_1' => [
                    'post_title' => $someTitle,
                    'post_excerpt' => 'foo',
                ],
                'post_2' => [
                    'post_title' => $someTitle,
                    'post_excerpt' => 'bar',
                ]
            ]
        ];
    }

    public function testExistingPostWillBeUpdated()
    {
        /** @var Post $first , $second */
        $first = $this->fixture('post_1');
        $second = $this->fixture('post_2');

        static::assertNull(get_page_by_title($first->post_title, OBJECT, 'post'));

        $this->posts()->persist($first, uniqid('', true));
        $this->posts()->persist($second, uniqid('', true));

        $actual = get_page_by_title($first->post_title, OBJECT, 'post');
        static::assertInstanceOf(\WP_Post::class, $actual);
        static::assertEquals($second->post_excerpt, $actual->post_excerpt);
    }

    public function testNonExistentWillBeCreated()
    {
        /** @var Post $post */
        $post = $this->fixture('post_1');

        static::assertNull(get_page_by_title($post->post_title, OBJECT, 'post'));

        $this->posts()->persist($post, uniqid('', true));

        $created = get_page_by_title($post->post_title, OBJECT, 'post');

        static::assertInstanceOf(\WP_Post::class, $created);
        static::assertEquals($post->post_title, $created->post_title);
        static::assertEquals('foo', $created->post_excerpt);
    }
}