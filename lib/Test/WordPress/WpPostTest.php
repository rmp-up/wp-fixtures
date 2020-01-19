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

namespace RmpUp\WordPress\Fixtures\Test\WordPress;

use RmpUp\WordPress\Fixtures\Entity\Comment;
use RmpUp\WordPress\Fixtures\Entity\Post;
use RmpUp\WordPress\Fixtures\Test\AbstractAllFieldsTestCase;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;
use WP_Post;

/**
 * Posts
 *
 * Imagine you write a anti-spam plugin
 * that should detect spam spread across different posts.
 * Instead of defining several posts with all its data
 * you can use `wpPost()` to have new random posts:
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\Comment:
 *   spam_{1..10}:
 *     post_comment: Cold or hot... it hits the spot!
 *     post: '<wpPost()>'
 * ```
 *
 * This way we have 10 comments on 10 newly generated random posts.
 *
 * Another example where the post-parent is something random:
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
 *     post_parent: '@mother'
 * ```
 *
 * The following examples show how to define a post in detail.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class WpPostTest extends AbstractTestCase
{
    public function testCommentHasPost()
    {
        /** @var Comment $comment */
        $comment = $this->loadFromDocComment(0, 'spam_1');
        $randomPost = $comment->post;
        $comment->sanitize('');

        $postId = $comment->post;
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