<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpPostTest.php
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
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-12-15
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Provider;

use RmpUp\WordPress\Fixtures\Entity\Comment;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;
use WP_Post;

/**
 * Creating a random post via `<wpPost()>`
 *
 * Imagine you like to test things on a comment
 * or need different random post parents.
 * Instead of writing all of those posts with all their data
 * we provide a shortcut:
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\Comment:
 *   comment_1:
 *     comment_karma: 9001
 *     comment_post_ID: '<wpPost()>'
 * ```
 *
 * In this case you care about the karma of a comment
 * but don't care what the contents of the post are.
 * Same when the post-parent is of no interest
 * but need to be there for testing purposes:
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\Post:
 *   child_1:
 *     post_title: Dylan
 *     post_parent: '<wpPost()>'
 * ```
 *
 * Later on you can still give the post a name
 * and put reference on it:
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\Post:
 *   mother:
 *     post_title: Kee
 *
 *   child_1:
 *     post_title: Dylan
 *     post_parent: '@kee'
 * ```
 *
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class WpPostTest extends AbstractTestCase
{
    public function testCommentHasPost()
    {
        /** @var Comment $comment */
        $comment = $this->loadFromDocComment(0, 'comment_1');
        $randomPost = $comment->comment_post_ID;
        $comment->sanitize('');

        $postId = $comment->comment_post_ID;
        static::assertGreaterThan(0, $postId);

        $post = get_post($postId);
        static::assertInstanceOf(WP_Post::class, $post);

        static::assertEquals($randomPost->ID, $postId);
        static::assertEquals($randomPost->ID, $post->ID);
        static::assertEquals($randomPost->post_title, $post->post_title);
    }

    protected function setUp()
    {
        parent::setUp();

        wp_cache_flush();
    }
}