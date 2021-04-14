<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AppendsNewFixturesTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\Alice;

use Nelmio\Alice\Throwable\Exception\Generator\Resolver\UnresolvableValueException;
use PHPUnit\Framework\Constraint\IsInstanceOf;
use RmpUp\WordPress\Fixtures\Faker\WordPressFixtureLoader;
use RmpUp\WordPress\Fixtures\Test\BasicTestCase;
use Throwable;
use UnexpectedValueException;
use WP_Post;
use WP_Term;

/**
 * Loading further fixtures
 *
 * ```yaml
 * WP_Term:
 *   qux:
 *     name: doom
 *     taxonomy: guy
 * ```
 *
 * ```yaml
 * WP_Post:
 *   foo:
 *     tax_input:
 *       bar:
 *         - "@qux"
 * ```
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 * @internal
 */
class LoadingMultipleFixturesTest extends BasicTestCase
{
	/**
	 * @var WordPressFixtureLoader
	 */
	private $loader;

	/**
	 * @param \Throwable $e
	 */
	private function assertIsUnresolvableException($e)
	{
		$constraint = static::logicalOr();
		$constraint->setConstraints([
			new IsInstanceOf(UnresolvableValueException::class),
			new IsInstanceOf(UnexpectedValueException::class),
		]);

		static::assertThat($e, $constraint);
	}

	protected function compatSetUp()
	{
		parent::compatSetUp();

		$this->loader = new WordPressFixtureLoader();
	}

	/**
	 * Referencing unknown fixtures
	 *
	 * Loading fixtures that reference unknown fixtures should fail.
	 * This way loading the requirements becomes mandatory
	 * and fulfills that all required entities exist beforehand.
	 * What we try to achieve is that the whole fixture-object
	 * can be referenced instead of just the ID.
	 *
	 * Example:
	 *
	 * ```yaml
	 * WP_User:
	 *   BramS: ~
	 *
	 * WP_Post:
	 *   dracula:
	 *     title: Dracula
	 *     user: "@BramS->ID" # this is not possible
	 * ```
	 *
	 * The nelmio/alice package is not capable of referencing primitive values
	 * or fields of other objects.
	 * When the user "BramS" is persisted then the Post-Field "dracula.user"
	 * won't be updated with the new user ID and stay NULL or 0 (zero).
	 * Referencing the whole user can make it work
	 * because it references the whole object:
	 *
	 * ```yaml
	 * WP_Post:
	 *   dracula:
	 *     title: Dracula
	 *     user: "@BramS" # this is not possible
	 * ```
	 *
	 * Now the repository can normalize the "user"-field
	 * (reference to the user-object) the the actual user ID.
	 */
	public function testLoadingUnknownRequirementsWillFail()
	{
		try {
			$this->loader->loadData($this->loadYaml(1));
		} catch (Throwable $e) {
			$this->assertIsUnresolvableException($e);

			return;
		}

		self::fail('Expected exception were not thrown');
	}

	/**
	 * Loading new fixtures appends objects
	 *
	 * It is very important that new fixture-objects are appended to the
	 * list of objects.
	 * This way requirements fulfill itself,
	 * because the needed/used fixtures are first in the list
	 * and will be created first.
	 * Following objects that need those fixtures find them already present,
	 * which is how requirements fulfill themselves.
	 *
	 * Example:
	 *
	 * A WP_Post should be part of a taxonomy/term.
	 * So the WP_Term fixture needs to be loaded first.
	 * Otherwise the nelmio/alice loader would fail
	 * and WordPress would too due to unknown taxonomy/term.
	 */
	public function testLoadingShouldAppend()
	{
		// First loading the term (without the post)
		$data = $this->loader->loadData($this->loadYaml(0))->getObjects();
		static::assertArrayHasKey('qux', $data);
		static::assertArrayNotHasKey('foo', $data);

		// Now loading the post
		$data = $this->loader->loadData($this->loadYaml(1), [], $data)->getObjects();
		static::assertArrayHasKey('foo', $data);

		static::assertInstanceOf(WP_Term::class, reset($data));
		static::assertEquals('qux', key($data));

		static::assertInstanceOf(WP_Post::class, end($data));
		static::assertEquals('foo', key($data));
	}
}
