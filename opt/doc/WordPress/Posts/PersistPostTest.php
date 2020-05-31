<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PersistPost.php
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

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Posts;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_Post;

/**
 * Persist post
 *
 * Storing a post programatically can be done using the `FixturesTrait`:
 *
 * ```
 * $this->fixtures()->persist($some_wp_post);
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class PersistPostTest extends TestCase
{
    public function testPostReceivesAnId() {
        /** @var \WP_Post $post */
        $post = $this->fixtures['wp_posts_persist_test'];
        $post->post_title = uniqid('', true);

        static::assertInstanceOf(WP_Post::class, $post);
        static::assertNull($post->ID);

        $this->fixtures()->persist($post);

        static::assertNotNull($post);
    }

    /**
     * Posts with the same title
     * will be stored using the same ID,
     * which allows reusing posts or changing one to a different state.
     */
    public function testPostReusesIdByTitle()
    {
        /** @var \WP_Post $post */
        $post = $this->fixtures['wp_posts_persist_test'];
        $again = clone $post;

        static::assertInstanceOf(WP_Post::class, $post);
        static::assertNull($post->ID);

        $this->fixtures()->persist($post);

        static::assertNotNull($post);

        static::assertNull($again->ID);
        $this->fixtures()->persist($again);
        static::assertNotNull($post);

        static::assertEquals($post->ID, $again->ID);
    }
}