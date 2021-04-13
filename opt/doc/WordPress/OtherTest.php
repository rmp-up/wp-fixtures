<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OtherTest.php
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

/**
 * Other
 *
 * There are more entities that can be created
 * but not persisted so far.
 * Either because it is not yet implemented
 * or because WordPress just uses these classes internally.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class OtherTest extends TestCase
{
    public function testOther()
    {
        static::assertTrue(true);
    }
}