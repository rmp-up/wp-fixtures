<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ErrorDuringInsertTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\WordPress\WpUser;

use Faker\Factory;
use Pretzlaw\WPInt\Filter\FilterAssertions;
use RmpUp\WordPress\Fixtures\Faker\WordPress\WpUserProvider;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;
use RuntimeException;

/**
 * Something went wrong while inserting the user
 *
 * @internal
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class ErrorDuringInsertTest extends AbstractTestCase
{
    use FilterAssertions;

    public function testUserCouldNotBeCreated()
    {
        // Invalid user login leads to WP_Error
        $this->mockFilter('pre_user_login')->expects($this->atLeastOnce())->willReturn('');

        $this->expectException(RuntimeException::class);

        (new WpUserProvider(Factory::create()))->wpUser();
    }
}