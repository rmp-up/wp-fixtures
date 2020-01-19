<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PostTest.php
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
 * @since     2020-01-20
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Comments;

use RmpUp\WordPress\Fixtures\Entity\Comment;
use RmpUp\WordPress\Fixtures\Entity\Post;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Setting the post
 *
 * A comment is often related to a post
 * so it needs the specific ID of that post.
 * When creating a post via fixtures the ID is not known
 * or not of interest for the developer.
 * In such case you can simply reference to the whole post:
 *
 * ```yaml
 * RmpUp\WordPress\Fixtures\Entity\Post:
 *   some_post_1:
 *     post_title: "500"
 *     post_content: "Rare footage of a black rainbow"
 *
 * RmpUp\WordPress\Fixtures\Entity\Comment:
 *   first_comment:
 *     post: "@some_post_1"
 *     content: "If only you know now how lovely you glow <3"
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class PostTest extends AbstractTestCase
{
    /**
     * @var Comment
     */
    private $comment;

    protected function setUp()
    {
        $this->comment = $this->loadFromDocComment(0, 'first_comment');

        parent::setUp();
    }

    public function testHasForeignObjectsAttached()
    {
        static::assertInstanceOf(Post::class, $this->comment->post);
        static::assertSame('Rare footage of a black rainbow', $this->comment->post->post_content);
    }

    public function testSanitizerReducesObject()
    {
        // Assume post has been created
        $expectedId = random_int(42, 1337);
        $this->comment->post->ID = $expectedId;

        $this->comment->sanitize('');

        static::assertSame($expectedId, $this->comment->post);
    }
}