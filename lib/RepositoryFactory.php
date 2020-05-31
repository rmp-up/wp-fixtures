<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RepositoryFactory.php
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
 * @package    rmp-up/wp-fixtures
 * @copyright  2020 M. Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://rmp-up.de/wp-fixtures
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures;

use ArrayObject;
use Closure;
use RmpUp\WordPress\Fixtures\Entity\Option;
use RmpUp\WordPress\Fixtures\Repository\Comments;
use RmpUp\WordPress\Fixtures\Repository\Options;
use RmpUp\WordPress\Fixtures\Repository\Posts;
use RmpUp\WordPress\Fixtures\Repository\RepositoryInterface;
use RmpUp\WordPress\Fixtures\Repository\Roles;
use RmpUp\WordPress\Fixtures\Repository\Sites;
use RmpUp\WordPress\Fixtures\Repository\Terms;
use RmpUp\WordPress\Fixtures\Repository\Users;
use RuntimeException;
use WP_Comment;
use WP_Post;
use WP_Role;
use WP_Site;
use WP_Term;
use WP_User;

/**
 * RepositoryFactory
 *
 * @copyright  2020 M. Pretzlaw (https://rmp-up.de)
 */
class RepositoryFactory
{
    const DEFAULT_MAPPING = [
        Option::class => Options::class,
        WP_Comment::class => Comments::class,
        WP_Post::class => Posts::class,
        WP_Role::class => Roles::class,
        WP_Site::class => Sites::class,
        WP_Term::class => Terms::class,
        WP_User::class => Users::class,
    ];

    /**
     * @var array
     */
    private $mapping;
    private $objectPool;

    public function __construct($mapping = null)
    {
        if (null === $mapping) {
            $mapping = static::DEFAULT_MAPPING;
        }

        $this->mapping = $mapping;
        $this->objectPool = new ArrayObject();
    }

    /**
     * @param string                 $entityType Entity type
     * @param callable|string|object $repository Repository
     *
     * @return mixed
     */
    private function createRepo(string $entityType, $repository)
    {
        if ($repository instanceof Closure) {
            // Allow the type to be resolved lazy.
            $repository = $repository();
        }

        if (is_string($repository)) {
            if (!class_exists($repository)) {
                throw new RuntimeException('Unknown repo class: ' . $repository);
            }

            $repository = new $repository();
        }

        if (!is_object($repository)) {
            throw new RuntimeException('Repository could not be resolved to an object.');
        }

        if (false === $repository instanceof RepositoryInterface) {
            throw new RuntimeException(
                sprintf('Repository "%s" needs to fulfill "%s".', get_class($repository), RepositoryInterface::class)
            );
        }

        // Store (per entity) to skip this resolve and keep memory free from duplicates.
        $this->objectPool->offsetSet($entityType, $repository);

        return $repository;
    }

    /**
     * @param object $object
     *
     * @return RepositoryInterface
     */
    public function forEntity($object)
    {
        return $this->forEntityType(get_class($object));
    }

    /**
     * Create a repository for an entity-type
     *
     * @param string $entityType Class-name of the entity
     *
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
}
