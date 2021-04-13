<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpUserQueryTest.php
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
use WP_User_Query;

/**
 * WP_User_Query
 *
 * The user query finds and loads multiple users from the database.
 * If you develop a plugin to optimize or change such queries
 * you surely want to have a big bunch of test scenarios like this:
 *
 * ```yaml
 * WP_User_Query:
 *   some_user_query_{1..10}:
 *     query_vars:
 *       has_published_posts: 0
 *       search: <word()>
 * ```
 *
 * This will give you 10 queries searching for some random word
 * among all user without a published post.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class WpUserQueryTest extends TestCase
{
    public function testGetUserQueries()
    {
        /** @var WP_User_Query $query */
        $query = $this->fixtures['some_user_query_1'];

        self::assertInstanceOf(WP_User_Query::class, $query);
    }
}