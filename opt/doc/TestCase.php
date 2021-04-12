<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TestCase.php
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
 * @package   WPFixtures
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt proprietary
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test;

use Nelmio\Alice\Loader\NativeLoader;
use RmpUp\Doc\DocParser;
use RmpUp\WordPress\Fixtures\Faker\LoaderFactory;
use RmpUp\WordPress\Fixtures\Helper\FixturesAutoloadTrait;
use RmpUp\WordPress\Fixtures\Helper\FixturesTrait;
use RmpUp\WordPress\Fixtures\Repository\RepositoryInterface;
use RmpUp\WordPress\Fixtures\RepositoryFacade;
use RmpUp\WordPress\Fixtures\RepositoryFactory;
use Symfony\Component\Yaml\Yaml;

/**
 * TestCase
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class TestCase extends \RmpUp\PHPUnitCompat\TestCase
{
    use DocParser;
    use FixturesTrait;
    use FixturesAutoloadTrait;

    /**
     * Faker creator / Stub generator
     *
     * @var NativeLoader
     *
     * @see ::alice()
     */
    private $alice;

    /**
     * @var object
     */
    protected $entity;

    /**
     * Adapter to the persistence
     *
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * Faker creator / Stub generator
     *
     * @return NativeLoader
     * @deprecated 0.8.0 Use ::fixtures() instead
     *
     */
    protected function alice()
    {
        if (false === $this->alice instanceof NativeLoader) {
            $this->alice = (new LoaderFactory())->createNativeLoader();
        }

        return $this->alice;
    }

    protected function assertEntityMatchesDefinition(int $index, string $className, $entityOrName, $expectedClass = null)
    {
        if (null === $expectedClass) {
            $expectedClass = $className;
        }

        if (is_string($entityOrName)) {
            // Warning: This may turn dates into numbers (unix timestamp)
            $data = $this->loadYaml($index, $className, $entityOrName);

            static::assertNotNull($data, sprintf('Failed loading "%s" from %d. YAML', $entityOrName, $index));

            $entityOrName = $this->loadEntities($index, $entityOrName);
        }

        static::assertInstanceOf($expectedClass, $entityOrName);

        foreach ($data as $key => $value) {
            static::assertEquals($entityOrName->$key, $value);
        }
    }

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

	protected function loadEntities($index = 0, $entityName = null)
    {
        $data = $this->loadYaml($index);

        if (!$data) {
            return null;
        }

        $entities = $this->fixtures()->loadData($data)->getObjects();

        if (null === $entityName) {
            return $entities;
        }

        return $entities[$entityName] ?? null;
    }

    /**
     * @param int $index
     *
     * @return mixed
     */
    protected function loadYaml(int $index = 0, $className = null, $entityName = null)
    {
        try {
            $yaml = $this->classComment()->code($index, '[@class="yaml"]');
        } catch (\InvalidArgumentException $e) {
            // Node was not found.
            return null;
        }

        $data = Yaml::parse((string) $yaml);

        if (null !== $className) {
            if (null !== $entityName) {
                return $data[$className][$entityName] ?? null;
            }

            return $data[$className] ?? null;
        }

        return $data;
    }

    /**
     * Persistence adapter for generated entities
     *
     * @return RepositoryFacade
     * @deprecated 0.8.0 Use ::fixtures() instead
     */
    protected function repo(): RepositoryFacade
    {
        if (null === $this->repository) {
            $this->repository = new RepositoryFacade(
                new RepositoryFactory()
            );
        }

        return $this->repository;
    }

    protected function compatSetUp()
    {
        parent::compatSetUp();

        // Always load the very first example
        $entities = $this->loadEntities();

        if ($entities) {
            $this->entity = current($entities);
            $this->fixtures = array_merge((array) $this->fixtures, $entities);
        }
    }

    protected function compatTearDown()
    {
        if (false === empty($this->fixtures)) {
            try {
                $this->fixtures()->delete($this->fixtures);
            } catch (\Exception $e) {

            }
		}

		error_log(
			'TEST '
			. get_class($this) . '::' . $this->getName()
			. $this->getDataSetAsString()
		);

		parent::compatTearDown();
	}

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
