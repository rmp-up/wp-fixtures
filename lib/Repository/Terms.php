<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Terms.php
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

namespace RmpUp\WordPress\Fixtures\Repository;

use RmpUp\WordPress\Fixtures\Helper\QueryStack;
use WP_Error;
use WP_Term;

/**
 * Terms
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Terms extends AbstractRepository
{
    /**
     * @param WP_Term $object
     *
     * @return int
     */
    protected function create($object): int
    {
        $temporaryTaxonomy = $this->temporaryTaxonomy($object->taxonomy);

        $insertData = wp_insert_term($object->name, $object->taxonomy, $object->to_array());

        if ($temporaryTaxonomy) {
            $this->removeTaxonomy($object->taxonomy);
        }

        if ($insertData instanceof WP_Error) {
            throw new PersistException($object, $insertData);
        }

        return (int) $insertData['term_id'];
    }

    /**
     * @param WP_Term $object
     * @param string  $fixtureName
     */
    public function delete($object, string $fixtureName)
    {
        $temporaryTaxonomy = $this->temporaryTaxonomy($object->taxonomy);

        $object->term_id = $this->find($object, $fixtureName);
        if (!$object->term_id) {
            return;
        }

        $success = wp_delete_term($object->term_id, $object->taxonomy);

        if ($temporaryTaxonomy) {
            $this->removeTaxonomy($object->taxonomy);
        }

        if ($success instanceof WP_Error) {
            throw new RepositoryException($object, $success);
        }
    }

    /**
     * @param WP_Term $object
     * @param string  $fixtureName
     *
     * @return int|mixed|null
     */
    public function find($object, string $fixtureName)
    {
        if ($object->term_id) {
            return $object->term_id;
        }

        $taxonomy = $object->taxonomy ?: 'category';
        $query = [
            [
                'taxonomy' => $taxonomy,
                'name' => $object->name,
                'fields' => 'ids',
                'suppress_filter' => true,
                'hide_empty' => false,
            ],
            [
                'taxonomy' => $taxonomy,
                'slug' => $object->slug,
                'fields' => 'ids',
                'suppress_filter' => true,
                'hide_empty' => false,
            ]
        ];

        foreach ((new QueryStack('get_terms'))($query) as $result) {
            if ($result) {
                return current($result);
            }
        }

        return null;
    }

    /**
     * @param WP_Term $object
     * @param string  $fixtureName
     */
    public function persist($object, string $fixtureName)
    {
        $temporaryTaxonomy = $this->temporaryTaxonomy($object->taxonomy);

        parent::persist($object, $fixtureName);

        if ($temporaryTaxonomy) {
            $this->removeTaxonomy($object->taxonomy);
        }
    }

    /**
     * @param WP_Term $object
     */
    protected function update($object)
    {
        if (empty($object->term_id)) {
            throw new RepositoryException(
                $object,
                sprintf(
                    'Term ("%s") needs an ID to be updated',
                    $object->slug ?: $object->name
                )
            );
        }

        $termData = wp_update_term($object->term_id, $object->taxonomy, $object->to_array());

        if ($termData instanceof WP_Error) {
            throw new RepositoryException($object, $termData);
        }
    }
}