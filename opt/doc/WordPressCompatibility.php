<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WordPressCompatibility.php
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

trait WordPressCompatibility
{
	protected function isSiteInitialized(int $site_id): bool
	{
		if (function_exists('wp_is_site_initialized')) {
			return wp_is_site_initialized($site_id);
		}

		global $wpdb;

		if ( is_object( $site_id ) ) {
			$site_id = $site_id->blog_id;
		}
		$site_id = (int) $site_id;

		$switch = false;
		if ( get_current_blog_id() !== $site_id ) {
			$switch = true;
			remove_action( 'switch_blog', 'wp_switch_roles_and_user', 1 );
			switch_to_blog( $site_id );
		}

		$suppress = $wpdb->suppress_errors();
		$result   = (bool) $wpdb->get_results( "DESCRIBE {$wpdb->posts}" );
		$wpdb->suppress_errors( $suppress );

		if ( $switch ) {
			restore_current_blog();
			if (function_exists('wp_switch_roles_and_user')) {
				add_action( 'switch_blog', 'wp_switch_roles_and_user', 1, 2 );
			}
		}

		return $result;
	}
}
