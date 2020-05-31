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
class TestCase extends \PHPUnit\Framework\TestCase
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

    protected function setUp()
    {
        parent::setUp();

        // Always load the very first example
        $entities = $this->loadEntities();

        if ($entities) {
            $this->entity = current($entities);
            $this->fixtures = array_merge((array) $this->fixtures, $entities);
        }
    }

    protected function tearDown()
    {
        if (false === empty($this->fixtures)) {
            try {
                $this->fixtures()->delete($this->fixtures);
            } catch (\Exception $e) {

            }
        }

        parent::tearDown();
    }
}