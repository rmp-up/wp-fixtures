<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * CommentsTest.php
 *
 * LICENSE: This source file is created by the company around Mike Pretzlaw
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
 * @since     2020-01-18
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\WordPress;

use PHPUnit\Framework\Constraint\IsAnything;
use RmpUp\WordPress\Fixtures\Entity\Comment;
use RmpUp\WordPress\Fixtures\Faker\WordPress;
use RmpUp\WordPress\Fixtures\Test\AbstractAllFieldsTestCase;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;
use RmpUp\WordPress\Fixtures\Test\Helper\FullExampleTestCase;
use RuntimeException;
use WP_Comment;

/**
 * Comments
 *
 * Creating random comments can be done like this:
 *
 * ```yaml
 * RmpUp\WordPress\Fixtures\Entity\Comment:
 *   full_example_comment:
 *     id: 1987
 *     post: 1
 *     date: "2014-03-03 05:04:27"
 *     content: "Come on over and do the twist"
 *     approved: 1
 *     author: Brain
 *     author_email: krist@example.org
 *     author_ip: 10.65.110.101
 *     author_url: https://wp-fixtures.org/
 * ```
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
class CommentsTest extends AbstractTestCase implements FullExampleTestCase
{
    /**
     * @var \wpdb
     */
    private $wpdb;

    public function testCompleteExample()
    {
        $this->assertExampleContainsAllFields(Comment::class, 'full_example_comment');
    }

    public function testCompletePersistence()
    {
        $comment = $this->loadFromDocComment(0, 'full_example_comment');
        $this->repo()->delete($comment, 'full_example_comment');
        wp_cache_flush();
        $this->repo()->persist($comment, 'full_example_comment');

        wp_cache_flush();

        /** @var WP_Comment $wpComment */
        $wpComment = get_comment($comment->id);

        static::assertInstanceOf(WP_Comment::class, $wpComment);

        $this->assertMatchesExample(
            Comment::class,
            'full_example_comment',
            [
                'id' => (int) $wpComment->comment_ID,
                'author_email' => $wpComment->comment_author_email,
                'author' => $wpComment->comment_author,
                'date' => $wpComment->comment_date,
                'content' => $wpComment->comment_content,
                'approved' => (int) $wpComment->comment_approved,
                'author_ip' => $wpComment->comment_author_IP,
                'author_url' => $wpComment->comment_author_url,
                'post' => $wpComment->comment_post_ID,
            ]
        );
    }

    public function testDoesNotExistAlready()
    {
        $comment = $this->loadFromDocComment(0, 'full_example_comment');
        $newFixtureName = uniqid('', true);

        $comment->id = null;
        $this->repo()->delete($comment, $newFixtureName);
        wp_cache_flush();


        static::assertNull($comment->id);

        $this->repo()->persist($comment, $newFixtureName);
        static::assertIsInt($comment->id);
        static::assertInstanceOf(WP_Comment::class, get_comment($comment->id));
    }

    public function testNewButDifferentId()
    {
        $comment = $this->loadFromDocComment(0, 'full_example_comment');
        $newFixtureName = uniqid('', true);

        // Different ID to assure comment does not exist already
        $comment->id = $comment->id + (int) date('YmdHis');

        $this->repo()->delete($comment, $newFixtureName);
        wp_cache_flush();

        $this->repo()->persist($comment, $newFixtureName);
        static::assertInstanceOf(WP_Comment::class, get_comment($comment->id));
    }

    public function testFindsByFixtureName()
    {
        $fixtureName = uniqid('', true);

        $comment = $this->loadFromDocComment(0, 'full_example_comment');
        $comment->id = null;
        $this->repo()->persist($comment, $fixtureName);
        static::assertNotNull($comment->id, 'Comment has not been created');

        $commentId = $this->repo()->find(new Comment(), $fixtureName);

        static::assertEquals($comment->id, $commentId, 'Comment found but ID mismatches');
    }

    protected function setUp()
    {
        parent::setUp();

        global $wpdb;
        $this->wpdb = $wpdb;
    }

    protected function tearDown()
    {
        global $wpdb;

        $wpdb = $this->wpdb;

        parent::tearDown();
    }

    public function testExceptionWhenCreationFails()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Could not insert comment');

        $comment = $this->loadFromDocComment(0, 'full_example_comment');
        $comment->id = null; // Pass to ::create

        global $wpdb;
        $mock = $this->createMock(\wpdb::class);
        $mock->expects($this->atLeastOnce())->method('insert')->willReturn(false);
        $wpdb = $mock;

        $this->repo()->persist($comment, uniqid('', true));
    }

    public function testHasTrait()
    {
        static::assertTrue(trait_exists(WordPress::class));
    }
}