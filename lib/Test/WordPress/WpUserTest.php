<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpUserTest.php
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

/**
 * Users
 *
 * When creating fixtures you often need to specify a user
 * like a post or comment author.
 * When you don't care about the details of a user then
 * use `wpUser()` to generate a random one.
 *
 * Example for thousands of comments on a hot post:
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\Comment:
 *   little_shitstorm_{1..10}:
 *     comment_content: '<text()>'
 *     comment_author: '<wpUser()>'
 * ```
 *
 * So now we have 10 comments from 10 different users to look up.
 * If you need more details for the user
 * then we need to define the user in detail as follows.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class WpUserTest extends AbstractTestCase
{
    /**
     * @group integration
     */
    public function testCommentsCreatedWithUser()
    {
        /** @var Comment[] $comments */
        $comments = $this->loadFromDocComment(0);

        foreach ($comments as $comment) {
            static::assertInstanceOf(Comment::class, $comment);
        }
    }

    protected function setUp()
    {
        parent::setUp();

        wp_cache_flush();
    }
}