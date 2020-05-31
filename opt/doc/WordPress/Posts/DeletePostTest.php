<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DeletePostTest.php
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
 * Delete post
 *
 * Deleting a post programatically can be done using this:
 *
 * ```php
 * $this->fixtures()->delete($some_wp_post);
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class DeletePostTest extends TestCase
{
    public function testDeletesPost()
    {
        $post = $this->fixtures['wp_posts_persist_test'];

        $this->fixtures()->persist($post);

        static::assertNotNull($post->ID);
        static::assertInstanceOf(WP_Post::class, get_post($post->ID));

        $this->fixtures()->delete($post);
    }

    /**
     * Even without an ID given a matching post will be
     * looked up by using the post-title
     * and the first match will be deleted.
     */
    public function testDeleteWithoutId()
    {
        /** @var WP_Post $post */
        $post = $this->fixtures['wp_posts_persist_test'];
        $post->post_title = uniqid('', true);
        $noId = clone $post;

        $this->fixtures()->persist($post);

        static::assertNotNull($post->ID);
        static::assertInstanceOf(WP_Post::class, get_post($post->ID));

        $this->fixtures()->delete($noId);

        wp_cache_flush();

        static::assertNull(get_post($post->ID));
    }
}