<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ${SHORT}
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
 * @copyright  2018 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-02-02
 */

declare( strict_types=1 );

namespace Pretzlaw\WordPress\Fixtures\Repository;

use Pretzlaw\WordPress\Fixtures\Entity\Option;

class Options extends AbstractRepository {

	/**
	 * @param Option $object
	 *
	 * @return int
	 */
	protected function create( $object ): int {
		foreach ( get_object_vars( $object ) as $option => $value ) {
			update_option( $option, $value );
		}

		return 0; // The option container shall never have an ID.
	}

	/**
	 * @param Option $object
	 */
	protected function update( $object ) {
		foreach ( get_object_vars( $object ) as $option => $value ) {
			update_option( $option, $value );
		}
	}

	/**
	 * @param Option $object Fixture to lookup.
	 * @param string $fixtureName
	 *
	 * @return int|null ID or null when not found
	 */
	public function find( $object, string $fixtureName ) {
		// There is no such thing.
		return null;
	}

	/**
	 * @param Option $object
	 * @param string $fixtureName
	 */
	public function delete( $object, string $fixtureName ) {
		foreach ( array_keys( (array) $object ) as $option ) {
			delete_option( $option );
		}
	}
}