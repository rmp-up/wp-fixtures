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

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Provider\WpPost;

use Faker\Factory;
use Pretzlaw\PHPUnit\DocGen\DocComment\Parser;
use Pretzlaw\WPInt\Filter\FilterAssertions;
use RmpUp\WordPress\Fixtures\Faker\WordPress\WpPostProvider;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;
use RuntimeException;

/**
 * Fetching error during inserts
 *
 * When `wp_insert_post` raises an error then we delegate this
 * as an `RuntimeException`.
 *
 * @internal
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class ErrorDuringInsertTest extends AbstractTestCase
{
    use FilterAssertions;
    use Parser;

    public function testThrowsException()
    {
        $exceptionClass = (string) $this->classComment()->xpath('//code[2]/text()', 0);

        static::assertTrue(
            $exceptionClass == RuntimeException::class
            || in_array(RuntimeException::class, class_parents($exceptionClass)),
            'Exception should be subtype of RuntimeException'
        );

        $this->mockFilter('wp_insert_post_empty_content')->expects($this->atLeastOnce())->willReturn(true);
        $this->expectException($exceptionClass);

        (new WpPostProvider(Factory::create()))->wpPost();
    }
}