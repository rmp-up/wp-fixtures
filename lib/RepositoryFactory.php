<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RepositoryFactory.php
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
 * @package    pretzlaw/wp-fixtures
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/pretzlaw/wp-fixtures
 * @since      2019-02-03
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures;

use RmpUp\WordPress\Fixtures\Repository\RepositoryInterface;

/**
 * RepositoryFactory
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class RepositoryFactory
{
    /**
     * @var array
     */
    private $mapping;
    private $objectPool;

    public function __construct($mapping = [])
    {
        $this->mapping = $mapping;
        $this->objectPool = new \ArrayObject();
    }

    /**
     * @param string $entityType
     * @return RepositoryInterface
     */
    public function forEntityType(string $entityType)
    {
        if ($this->objectPool->offsetExists($entityType)) {
            // Reuse existing repository
            return $this->objectPool->offsetGet($entityType);
        }

        if (array_key_exists($entityType, $this->mapping)) {
            return $this->createRepo($entityType, $this->mapping[$entityType]);
        }

        // Try to automatically resolve repo class.
        $repoClass = str_replace('\\Entity\\', '\\Repository\\', $entityType);

        if (!class_exists($repoClass)) {
            // Allow singular but use at least plural forms.
            $repoClass .= 's';
        }

        return $this->createRepo($entityType, $repoClass);
    }

    /**
     * @param object $object
     * @return RepositoryInterface
     */
    public function forEntity($object)
    {
        return $this->forEntityType(get_class($object));
    }

    /**
     * @param string $entityType Entity type
     * @param callable|string|object $repository Repository
     * @return mixed
     */
    private function createRepo(string $entityType, $repository)
    {
        if ($repository instanceof \Closure) {
            // Allow the type to be resolved lazy.
            $repository = $repository();
        }

        if (is_string($repository)) {
            if (!class_exists($repository)) {
                throw new \RuntimeException('Unknown repo class: ' . $repository);
            }

            $repository = new $repository();
        }

        if (!is_object($repository)) {
            throw new \RuntimeException('Repository could not be resolved to an object.');
        }

        if (false === $repository instanceof RepositoryInterface) {
            throw new \RuntimeException(
                sprintf('Repository "%s" needs to fulfill "%s".', get_class($repository), RepositoryInterface::class)
            );
        }

        // Store (per entity) to skip this resolve and keep memory free from duplicates.
        $this->objectPool->offsetSet($entityType, $repository);

        return $repository;
    }
}
