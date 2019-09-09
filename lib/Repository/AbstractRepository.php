<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbstractRepository.php
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

namespace RmpUp\WordPress\Fixtures\Repository;

use RmpUp\WordPress\Fixtures\Entity\Post;
use RmpUp\WordPress\Fixtures\Entity\Sanitizable;
use RmpUp\WordPress\Fixtures\Entity\Validatable;

/**
 * AbstractRepository
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @param Post $object
     * @param string $fixtureName
     */
    public function persist($object, string $fixtureName)
    {
        $sanitized = $this->parse($object, $fixtureName);

        $id = $this->find($sanitized, $fixtureName);
        if ($id !== null) {
            $object->ID = $id;
            $sanitized->ID = $id;
            $this->update($sanitized);

            return;
        }

        $object->ID = $this->create($sanitized);
    }

    protected function toArray($source)
    {
        return get_object_vars($source);
    }

    /**
     * @param object $object
     * @param string|null $fixtureName
     * @return Sanitizable|object
     */
    protected function parse($object, string $fixtureName = null)
    {
        $double = clone $object;

        if ($double instanceof Sanitizable) {
            $double->sanitize((string) $fixtureName);
        }

        if ($double instanceof Validatable) {
            $double->validate((string) $fixtureName);
        }

        return $double;
    }

    /**
     * Set meta-data for given entity
     *
     * Stores data for an entity in the database with special cases:
     *
     * * Array values will consecutively use `add_metadata`
     *   so that each entry represents one row in the database.
     *
     * @param string $type     Type of the entity (e.g. "post").
     * @param int    $entityId ID of the post, user or other entity.
     * @param array  $metaData Key-Value-Store of meta data.
     */
    protected function updateMetaData(string $type, int $entityId, array $metaData)
    {
        foreach ($metaData as $metaKey => $metaValue) {
            // We always replace the current meta-data because we are a fixture.
            delete_metadata($type, $entityId, $metaKey);

            if (is_array($metaValue)
                && count(array_filter(array_keys($metaValue), 'is_string')) <= 0
            ) {
                // Got sequential / non-assoc array which we will split and replace.
                foreach ($metaValue as $rowNum => $item) {
                    $success = add_metadata($type, $entityId, $metaKey, $item);

                    if (false === $success) {
                        throw new \RuntimeException(
                            sprintf(
                                'Could not set meta-data "%s" (%d. row) for %s "%d"',
                                $metaKey,
                                $rowNum,
                                $type,
                                $entityId
                            )
                        );
                    }
                }

                continue;
            }

            update_metadata($type, $entityId, $metaKey, $metaValue);
        }
    }

    abstract protected function create($object): int;

    abstract protected function update($object);
}
