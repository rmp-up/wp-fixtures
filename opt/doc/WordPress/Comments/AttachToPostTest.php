<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AttachToPostTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Comments;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_Comment;
use WP_Post;

/**
 * Attach comment to post
 *
 * Usually a comment is made for a post.
 * To attach a comment to a post you can use the post_id field:
 *
 * ```yaml
 * WP_Post:
 *   commented_post_here:
 *     post_title: Rotating
 *
 * WP_Comment:
 *   very_first_comment:
 *     post_ID: "@commented_post_here"
 * ```
 *
 * This will link the WP_Post to the comment.
 * So the post_id now holds the complete WP_Post object.
 * After persisting it turns into the post_id.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class AttachToPostTest extends TestCase
{
    /**
     * @var WP_Comment
     */
    private $comment;

    /**
     * @var WP_Post
     */
    private $post;

    protected function setUp()
    {
        parent::setUp();

        $this->fixtures = $this->loadEntities();

        /** @var \WP_Post $post */
        $this->post = $this->fixtures['commented_post_here'];
        /** @var WP_Comment $post */
        $this->comment = $this->fixtures['very_first_comment'];
    }

    public function testAttachOneCommentToOnePost()
    {
        static::assertInstanceOf(WP_Comment::class, $this->comment);
        static::assertNull($this->comment->comment_post_ID);

        $this->post->ID = 5;

        static::assertEquals(5, $this->comment->comment_post_ID);
    }

    public function testAttachedPostIdIsReferenced()
    {
        /** @var WP_Comment $comment */
        $comment = $this->fixtures['very_first_comment'];

        static::assertInstanceOf(WP_Comment::class, $comment);
        static::assertNull($comment->comment_post_ID);
    }

    public function testCommentAttachedToPostInDatabase()
    {
        $this->fixtures()->persist($this->post);
        $this->fixtures()->persist($this->comment);
        static::assertIsInt($this->post->ID);

        wp_cache_flush();
        $stored = get_comments(['post_id' => $this->post->ID]);

        static::assertEquals([$this->comment], $stored);
    }

    public function testPersistCommentAfterPost()
    {
        $this->fixtures()->persist($this->post);

        static::assertIsInt($this->post->ID);

        static::assertNull($this->comment->comment_ID);

        $this->fixtures()->persist($this->comment);

        static::assertIsInt($this->comment->comment_ID);

        wp_cache_flush();
        $stored = get_comment($this->comment->comment_ID);

        static::assertEquals($this->comment, $stored);
    }
}