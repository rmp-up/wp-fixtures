<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * CommentsTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\WordPress;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_Comment;

/**
 * Comments
 *
 * ```yaml
 * WP_Comment:
 *   first_comment:
 *     comment_author: Nessi
 *     comment_author_email: nessi@world.universe
 *     comment_author_IP: 10.42.13.37
 *     comment_date: "2020-05-20 11:15:19"
 *     comment_content: Silence!
 *     comment_approved: 1
 * ```
 *
 * Those common field known from the `WP_Comment` class can be used to create
 * lots of comments.
 * But this is a lot to write so ther is an alias
 * for each field that has the "comment_" prefix.
 *
 * ```yaml
 * WP_Comment:
 *   abbreviated_comment_fields:
 *     author: Nessi
 *     author_email: nessi@world.universe
 *     author_IP: 10.42.13.37
 *     date: "2020-05-20 11:15:19"
 *     content: Silence!
 *     approved: 1
 * ```
 *
 * Which results in the same `WP_Comment` object as above.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class CommentsTest extends TestCase
{
    public function testAbbreviation()
    {
        static::assertEquals(
            $this->loadEntities(0, 'first_comment'),
            $this->loadEntities(1, 'abbreviated_comment_fields')
        );
    }

    public function testCreateWpCommentInstance()
    {
        $this->assertEntityMatchesDefinition(0, WP_Comment::class, 'first_comment');
    }
}