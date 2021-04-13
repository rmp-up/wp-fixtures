<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpCompat.php
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

namespace RmpUp\WordPress\Fixtures\Helper;

use DomainException;
use WP_Error;
use WP_Site;
use wpdb;

/**
 * WpCompat
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class WpCompat
{
	public static function check($phpVersion = null, $wpVersion = null): bool
	{
		if (null === $phpVersion) {
			$phpVersion = PHP_VERSION;
		}

		if (null === $wpVersion) {
			global $wp_version;
			$wpVersion = $wp_version;
		}

		$incompatibilities = [
			[
				['7.4', '>='], // PHP
				['4.9', '<'], // WP
			],
			[
				['8.0', '>='], // PHP
				['5.1', '<'], // WP
			]
		];

		foreach ($incompatibilities as $incompatibility) {
			$php = $incompatibility[0];
			array_unshift($php, $phpVersion);

			$wp = $incompatibility[1];
			array_unshift($wp, $wpVersion);

			if (
				call_user_func_array('version_compare', $php)
				&& call_user_func_array('version_compare', $wp)
			) {
				throw new DomainException(
					sprintf(
						'Sorry, PHP %s %s'
						. ' and WP %s %s does not work well together'
						. ' so wp-fixture doesn\'t run in this environment.'
						. ' Please upgrade WordPress to %s or higher.',
						$php[2],
						$php[1],
						$wp[2],
						$wp[1],
						$wp[1]
					)
				);
			}
		}

		return true;
	}
}
