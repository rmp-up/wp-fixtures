<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpCommentQueryTest.php
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
use WP_Comment_Query;
use WP_Date_Query;

/**
 * WP_Comment_Query
 *
 * The comment query finds and loads multiple comments from the database.
 * If you develop a plugin to optimize or change such queries
 * you surely want to have a big bunch of test scenarios like this:
 *
 * ```yaml
 * WP_Comment_Query:
 *   some_comment_query_{1..10}:
 *     query_vars:
 *       include_unapproved: 1
 *       search: <word()>
 *       date_query:
 *         after: 4 weeks ago
 * ```
 *
 * This will give you 10 comment queries searching for some random word
 * in all unapproved comments.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class WpCommentQueryTest extends TestCase
{
    public function testGetComments()
    {
        /** @var WP_Comment_Query $query */
        $query = $this->fixtures['some_comment_query_1'];

        self::assertInstanceOf(WP_Comment_Query::class, $query);
    }
}