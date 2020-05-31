<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * CreateRandomPost.php
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
 * Create random user
 *
 * Whenever you need a random user you can use the `WP_Post()` provider:
 *
 * ```yaml
 * SomeThing:
 *   a_thing_with_a_post:
 *     thingy: dingy
 *     post: <WP_Post()>
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class CreateRandomPost extends TestCase
{
    public function testRandomUserIsCreated()
    {
        /** @var WP_Post $post */
        $post = $this->fixtures['a_thing_with_a_post']->post;

        static::assertInstanceOf(WP_Post::class, $post);
        static::assertEquals(1, $post->post_author);
        static::assertNotEmpty($post->post_content);
        static::assertNotEmpty($post->post_title);
        static::assertEquals('post', $post->post_type);
    }
}