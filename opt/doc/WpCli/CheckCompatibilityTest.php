<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * CheckCompatibilityTest.php
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
 * @copyright 2021 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\WpCli;

use DomainException;
use RmpUp\WordPress\Fixtures\Helper\WpCompat;
use RmpUp\WordPress\Fixtures\Test\TestCase;

/**
 * CheckCompatibilityTest
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class CheckCompatibilityTest extends TestCase
{
	const COMPATIBLE = 'compatible';
	const INCOMPATIBLE = 'incompatible';

	/**
	 * Checking against combinations near the limits
	 *
	 * 1. High compatible versions
	 * 2. Grinding the compatible superior
	 * 3. One below the superior it becomes incompatible
	 * 4. Even when grinding the inferior
	 * 5. Below the inferior it becomes compatible again
	 *
	 * @return \string[][]
	 */
	public function getCombinations()
	{
		return [
			// PHP 7.4 needs WP >= 4.9
			['7.4.10-debian0.1.2.3', '4.9.0', self::COMPATIBLE],
			['7.4.0', '4.9.0', self::COMPATIBLE],
			['7.4.12', '4.8.20', self::INCOMPATIBLE],
			['7.4.0', '4.8.20', self::INCOMPATIBLE],
			['7.3.9', '4.8.0', self::COMPATIBLE],

			// PHP 8.0 needs WP >= 5.1
			['8.0.10-debian0.1.2.3', '5.1.0', self::COMPATIBLE],
			['8.0.0', '5.1.0', self::COMPATIBLE],
			['8.0.12', '5.0.20', self::INCOMPATIBLE],
			['8.0.0', '5.0.20', self::INCOMPATIBLE],
			['7.9.9', '5.0.0', self::COMPATIBLE],
		];
	}

	/**
	 * @param $phpVersion
	 * @param $wpVersion
	 * @param $compatible
	 *
	 * @dataProvider getCombinations
	 */
	public function testReflectsIfIncomaptibleWithEnvironment($phpVersion, $wpVersion, $compatible)
	{
		if (self::INCOMPATIBLE === $compatible) {
			$this->expectException(DomainException::class);
			$this->expectExceptionMessage('does not work well together');
		}

		static::assertTrue(WpCompat::check($phpVersion, $wpVersion));
	}

	/**
	 * Needs to be compatible with current environment
	 *
	 * @internal
	 */
	public function testShouldBeCompatibleWithCurrentEnvironment()
	{
		static::assertTrue(WpCompat::check());
	}
}
