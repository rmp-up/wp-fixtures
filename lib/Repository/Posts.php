<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ${SHORT}
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
 * @package    wp-fixtures
 * @copyright  2020 M. Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-fixtures
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Repository;

use RuntimeException;
use WP_Error;
use WP_Post;

/**
 * Posts
 *
 * @copyright  2020 M. Pretzlaw (https://rmp-up.de)
 */
class Posts extends AbstractRepository
{
    /**
     * @param WP_Post $double
     *
     * @return int
     */
    protected function create($double): int
    {
        $tempTax = null;
        if (false === empty($double->tax_input)) {
            $tempTax = $this->temporaryTaxonomy(array_keys($double->tax_input));
        }

        $postId = wp_insert_post($this->toArray($double), true);

        if ($tempTax) {
            $this->removeTaxonomy($tempTax);
        }

        if ($postId instanceof WP_Error) {
            throw new PersistException($double, $postId);
        }

        return $postId;
    }

    /**
     * @param WP_Post $object
     * @param string  $fixtureName
     */
    public function delete($object, string $fixtureName)
    {
        $id = $this->find($object, $fixtureName);

        if (null === $id) {
            return;
        }

        $success = wp_delete_post($id, true);

        if (false === $success instanceof WP_Post) {
            throw new RepositoryException(
                $object,
                sprintf('Could not delete post "%s" (ID %d)', $fixtureName, $id)
            );
        }
    }

    /**
     * @param WP_Post     $object
     * @param string|null $fixtureName
     *
     * @return int|null
     */
    public function find($object, string $fixtureName = null)
    {
        // By ID
        if (!empty($object->ID)) {
            return $object->ID;
        }

        $found = null;

        // By title
        if (!empty($object->post_title)) {
            $found = get_page_by_title($object->post_title, OBJECT, $object->post_type ?: 'page');
        }

        if ($found instanceof WP_Post) {
            // Found exactly one which is nice
            return $found->ID;
        }

        return null;
    }

    /**
     * @param WP_Post $object
     */
    protected function update($object)
    {
        if (empty($object->ID)) {
            throw new RuntimeException('ID needed for updates');
        }

        $id = wp_update_post($this->toArray($object));

        if ($id instanceof WP_Error) {
            throw new RepositoryException($object, $id);
        }
    }
}
