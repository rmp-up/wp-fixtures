<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbstractRepository.php
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

namespace RmpUp\WordPress\Fixtures\Repository;

use RmpUp\WordPress\Fixtures\Entity\Sanitizable;
use RmpUp\WordPress\Fixtures\Entity\Validatable;
use RuntimeException;
use WP_Taxonomy;

/**
 * AbstractRepository
 *
 * @copyright  2020 M. Pretzlaw (https://rmp-up.de)
 */
abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var \wpdb
     */
    protected $db;

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * Create new repository
     *
     * @param \wpdb|null $db Uses global available $wpdb when none provided.
     */
    public function __construct($db = null)
    {
        if (null === $db) {
            global $wpdb;
            $db = $wpdb;
        }

        $this->db = $db;
    }

    /**
     * Add new data to the persistence
     *
     * @param object $object The entity to save
     *
     * @return int
     */
    abstract protected function create($object): int;

    /**
     * @param object $object
     * @param string $fixtureName Name as in the Yaml-Fixture-Config
	 *                            (deprecated, will be removed)
     *
     * @return Sanitizable|object
     */
    protected function parse($object, string $fixtureName = '')
    {
        $double = clone $object;

        if ($double instanceof Sanitizable) {
            $double->sanitize($fixtureName);
        }

        if ($double instanceof Validatable) {
            $double->validate($fixtureName);
        }

        return $double;
    }

    /**
     * @param mixed  $object
     * @param string $fixtureName Name as in the Yaml-Fixture-Config
	 *                            (deprecated, will be removed)
     */
    public function persist($object, string $fixtureName = '')
    {
        $sanitized = $this->parse($object, $fixtureName);

        $id = $this->find($sanitized, $fixtureName);
        if ($id !== null) {
            $this->updatePrimaryKey((int) $id, $object, $sanitized);
            $this->update($sanitized);

            return;
        }

        $id = $this->create($sanitized);
        $this->updatePrimaryKey($id, $object, $sanitized);
    }

    protected function removeTaxonomy($taxonomies)
    {
        global $wp_taxonomies;

        foreach ((array) $taxonomies as $taxonomy) {
            if (is_array($wp_taxonomies) && array_key_exists($taxonomy, $wp_taxonomies)) {
                unset($wp_taxonomies[$taxonomy]);
            }
        }

    }

    protected function temporaryTaxonomy($taxonomies): array
    {
        global $wp_taxonomies;

        $taxonomies = (array) $taxonomies;
        $temporary = [];

        foreach ($taxonomies as $tax) {
            if (false === taxonomy_exists($tax)) {
                $temporary[] = $tax;
                $wp_taxonomies[$tax] = new WP_Taxonomy($tax, 'any');
            }
        }

        return $temporary;
    }

    protected function toArray($source)
    {
        $vars = get_object_vars($source);

        if (false === empty($vars['data'])) {
            $vars = array_merge($vars, (array) $vars['data']);
            unset($vars['data']);
        }

        return $vars;
    }

    /**
     * Change data in the persistence
     *
     * @param object $object The entity to update (with some identifier)
     */
    abstract protected function update($object);

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
                        throw new RuntimeException(
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

    /**
     * @param int    $id
     * @param object $object
     * @param object $sanitized
     */
    protected function updatePrimaryKey(int $id, $object, $sanitized)
    {
        $object->{$this->primaryKey} = $id;
        $sanitized->{$this->primaryKey} = $id;
    }
}
