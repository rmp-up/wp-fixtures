<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Fixtures.php
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

namespace RmpUp\WordPress\Fixtures\Helper;

use InvalidArgumentException;
use Nelmio\Alice\DataLoaderInterface;
use Nelmio\Alice\FileLoaderInterface;
use Nelmio\Alice\FilesLoaderInterface;
use Nelmio\Alice\Loader\NativeLoader;
use Nelmio\Alice\ObjectSet;
use RmpUp\WordPress\Fixtures\RepositoryFacade;
use RmpUp\WordPress\Fixtures\RepositoryFactory;
use stdClass;
use Symfony\Component\Finder\Finder;
use Traversable;

/**
 * Fixtures
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Fixtures
{
    /**
     * @var NativeLoader
     */
    private $loader;
    private $repository;

    /**
     * Fixtures constructor.
     *
     * @param NativeLoader $loader
     */
    public function __construct($loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param object[] $objects
     *
     * @return mixed
     */
    public function delete($objects)
    {
        $repo = $this->repo();
        foreach ($this->normalizeObjects($objects) as $fixtureName => $object) {
            $repo->delete($object, (string) $fixtureName);
        }

        return $objects;
    }

    /**
     * @param string|Traversable|array $fileDirectoryOrIterator
     *
     * @param array                    $parameters
     * @param array                    $objects Already existing objects.
     *
     * @return ObjectSet
     */
    public function load($fileDirectoryOrIterator, $parameters = [], $objects = []): ObjectSet
    {
        if (is_string($fileDirectoryOrIterator)) {
            return $this->loadFiles($fileDirectoryOrIterator, $parameters, $objects);
        }

        if (is_array($fileDirectoryOrIterator)) {
            return $this->loader->loadFiles($fileDirectoryOrIterator);
        }

        if ($fileDirectoryOrIterator instanceof Traversable) {
            return $this->loader->loadFiles(iterator_to_array($fileDirectoryOrIterator), $parameters, $objects);
        }

        throw new InvalidArgumentException('Please provide a directory, file or iterator');
    }

    public function loadData($definition): ObjectSet
    {
        return $this->loader->loadData($definition);
    }

    private function loadFiles(string $directoryOrFilePattern, $parameters = [], $objects = [])
    {
        if (is_file($directoryOrFilePattern)) {
            return $this->loader->loadFile($directoryOrFilePattern, $parameters, $objects);
        }

        if (is_dir($directoryOrFilePattern)) {
            return $this->loader->loadFiles(
                iterator_to_array((new Finder())->in($directoryOrFilePattern)->name('*.yaml')->files()),
                $parameters,
                $objects
            );
        }

        if (false === glob($directoryOrFilePattern)){
            return null;
        }

        // Assuming pattern
        return $this->loader->loadFiles(glob($directoryOrFilePattern), $parameters, $objects);
    }

    /**
     * @param ObjectSet|array|object $objects
     *
     * @return array|object[]
     */
    private function normalizeObjects($objects): array
    {
        if ($objects instanceof ObjectSet) {
            $objects = $objects->getObjects();
        }

        if (is_object($objects)) {
            $objects = [$objects];
        }

        return $objects;
    }

    /**
     * Resolve possible ID to a fake object
     *
     * @param stdClass $object
     *
     * @return int|null ID for an object
     */
    public function find($object)
    {
        return $this->repo()->find($object, '');
    }

    /**
     * Store objects
     *
     * @param ObjectSet|array $objects
     *
     * @return array|ObjectSet
     */
    public function persist($objects)
    {
        $repo = $this->repo();
        foreach ($this->normalizeObjects($objects) as $fixtureName => $object) {
            $repo->persist($object, (string) $fixtureName);
        }

        return $objects;
    }

    /**
     * Persistence adapter for generated entities
     *
     * @return RepositoryFacade
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
}