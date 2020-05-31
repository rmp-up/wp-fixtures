<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PostsTest.php
 *
 * LICENSE: This source file is created by the company around M. Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package   wp-fixtures
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\WordPress;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_Post;

/**
 * Posts
 *
 * ```yaml
 * WP_Post:
 *   yet_another_blog_post:
 *     post_title: Yay, I got my own blog
 *     post_content: |
 *       Hey, I know that each blog pollutes our environment a little bit more
 *       and that most problems would be solved if we stop bringing up
 *       so many pages per person.
 *       But anyway here I am because I am a free person in a free country
 *       and can do whatever I want with this planet!
 *     post_date: "2012-12-05 12:34:56"
 *     post_excerpt: I am allowed to put my garbage wherever I want!
 *     post_status: publish
 *     comment_status: closed
 * ```
 *
 * Most of those fields can be abbreviated so that you don't have to write so much
 * obvious stuff:
 *
 * ```yaml
 * WP_Post:
 *   yet_another_blog_post:
 *     title: Yay, I got my own blog
 *     content: |
 *       Hey, I know that each blog pollutes our environment a little bit more
 *       and that most problems would be solved if we stop bringing up
 *       so many pages per person.
 *       But anyway here I am because I am a free person in a free country
 *       and can do whatever I want with this planet!
 *     date: "2012-12-05 12:34:56"
 *     excerpt: I am allowed to put my garbage wherever I want!
 *     status: publish
 *     comment_status: closed
 * ```
 *
 * This would create the very same WP_Post-object as the YAML above.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class PostsTest extends TestCase
{
    public function testAbbreviation()
    {
        self::assertEquals(
            $this->loadEntities(0, 'yet_another_blog_post'),
            $this->loadEntities(1, 'yet_another_blog_post')
        );
    }

    public function testCreatesWpPostObject()
    {
        $this->assertEntityMatchesDefinition(0, WP_Post::class, 'yet_another_blog_post');
    }


    public function testPostReceivesIdOnPersist()
    {
        /** @var \WP_Post $post */
        $post = $this->loadEntities(0, 'yet_another_blog_post');

        $this->repo()->persist($post, '');

        static::assertNotNull($post->ID);
    }
}