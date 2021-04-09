<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpObjectCacheTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Other;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_Object_Cache;
use WP_Post;

/**
 * WP_Object_Cache
 *
 * To prepare/fill an object cache with some data (for testing purposes)
 * you can create your own object-cache like this:
 *
 * ```yaml
 * WP_Post:
 *   test_1_post:
 *     ID: 1337
 *     title: <word()>
 *
 * WP_Object_Cache:
 *   test_1_object_cache:
 *     cache:
 *       multisite: 0
 *       posts:
 *         1337: "@test_1_post"
 * ```
 *
 * This way you just faked the post with the ID 1337
 * so that `get_post(1337)` would return this post,
 * if you use "test_1_object_cache" for the global `$wp_object_cache`.
 *
 * For a multisite this would
 * Please note that "1:1337" is the key when you are running a multi-site.
 * If you are dealing with a single-site installation then using
 * "1337" as key would be enough.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class WpObjectCacheTest extends TestCase
{
    public function testObjectCacheFakesPost()
    {
        /** @var WP_Object_Cache $cache */
        $cache = $this->fixtures['test_1_object_cache'];

        static::assertInstanceOf(WP_Object_Cache::class, $cache);
        static::assertEquals(0, $cache->multisite);

        /** @var WP_Post $post */
        $post = $cache->get(1337, 'posts');

        static::assertInstanceOf(WP_Post::class, $post);
        self::assertEquals(1337, (int) $post->ID);
    }
}