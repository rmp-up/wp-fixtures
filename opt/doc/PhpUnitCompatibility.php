<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TestCompatibility.php
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

namespace RmpUp\WordPress\Fixtures\Test;

/**
 * TestCompatibility
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
trait PhpUnitCompatibility
{
	/**
	 * Backward compatible type-check
	 *
	 * @param $type
	 * @param $other
	 *
	 * @return bool|void
	 *
	 * @deprecated This should be part of rmp-up/phpunit-compat
	 */
	protected static function assertIsType($type, $other)
	{
		$message = 'Value is not of type ' . $type;

		switch ($type) {
			case 'numeric':
				static::assertTrue(\is_numeric($other), $message);
				return;

			case 'integer':
			case 'int':
				static::assertTrue(\is_int($other), $message);
				return;

			case 'double':
			case 'float':
			case 'real':
				static::assertTrue(\is_float($other), $message);
				return;

			case 'string':
				static::assertTrue(\is_string($other), $message);
				return;

			case 'boolean':
			case 'bool':
				static::assertTrue(\is_bool($other), $message);
				return;

			case 'null':
				static::assertTrue(null === $other, $message);
				return;

			case 'array':
				static::assertTrue(\is_array($other), $message);
				return;

			case 'object':
				static::assertTrue(\is_object($other), $message);
				return;

			case 'resource':
				static::assertTrue(\is_resource($other) || \is_string(@\get_resource_type($other)), $message);
				return;

			case 'scalar':
				static::assertTrue(\is_scalar($other), $message);
				return;

			case 'callable':
				static::assertTrue(\is_callable($other), $message);
				return;
		}
	}

	/**
	 * Cross-PHP-Unit-Compatibility to check if string is in string.
	 *
	 * @param string $expected
	 * @param string $other
	 *
	 * @deprecated Should be part of "rmp-up/phpunit-compat"-package instead.
	 */
	protected static function assertStringInString($expected, $other)
	{
		static::assertNotFalse(
			strpos($other, $expected),
			sprintf('Could not find "%s" in "%s".', $expected, $other)
		);
	}
}
