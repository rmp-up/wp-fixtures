<?php

namespace Pretzlaw\WordPress\Fixtures\Test\Option;

use Pretzlaw\WordPress\Fixtures\Entity\Option;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Options
 *
 * Setting options in WordPress can be done using a simple table like this:
 *
 * ```yaml
 * \Pretzlaw\WordPress\Fixtures\Entity\Option:
 *   default:
 *     home_url_perhaps: 'https://example.org'
 *     some_plugin_api_token: afbdec456ddebdc84
 * ```
 *
 * The "default" is just a necessary placeholder while the actual options are listed below.
 *
 * @package Pretzlaw\WordPress\Fixtures\Test\Option
 */
class PrimitiveValueTest extends AbstractTestCase {
	/**
	 * @var Option
	 */
	private $options;

	protected function setUp() {
		parent::setUp();

		$this->options = $this->loadFromDocComment( 0 );
	}

	public function testOptionsLoaded() {
		static::assertInstanceOf( Option::class, $this->options['default'] );
		static::assertEquals( 'https://example.org', $this->options['default']->home_url_perhaps );
		static::assertEquals( 'afbdec456ddebdc84', $this->options['default']->some_plugin_api_token );
	}

	public function testOptionsAreStored() {
		// Flush all those options.
		$this->removeOptions();

		static::assertInstanceOf( Option::class, $this->options['default'] );
		$this->repo()->persist( $this->options['default'], 'default' );

		// Flush cache after writing option to ensure that options are loaded from database again.
		wp_cache_flush();

		static::assertEquals( 'https://example.org', get_option( 'home_url_perhaps' ) );
		static::assertEquals( 'afbdec456ddebdc84', get_option( 'some_plugin_api_token' ) );
	}

	public function testOptionsWillBeRemoved() {
		$this->testOptionsAreStored();

		$this->repo()->delete( $this->options['default'], 'default' );

		// Flush cache after writing option to ensure that options are looked up in the database again.
		wp_cache_flush();

		static::assertFalse( get_option( 'home_url_perhaps' ) );
		static::assertFalse( get_option( 'some_plugin_api_token' ) );
	}

	private function removeOptions() {
		foreach ( $this->options['default'] as $option => $value ) {
			delete_option( $option );
		}
	}

	protected function tearDown() {
		$this->removeOptions();

		parent::tearDown();
	}
}