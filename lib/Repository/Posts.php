<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ${SHORT}
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
 * @package    wp-fixtures
 * @copyright  2018 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-02-02
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Repository;

use RmpUp\WordPress\Fixtures\Entity\Post;

/**
 * Posts
 *
 * @copyright  2018 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-02
 */
class Posts extends AbstractRepository
{
    public $ID;
    public $tax_input = [];
    public $meta_input = [];


    /**
     * @param Post        $object
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

        if ($found instanceof \WP_Post) {
            // Found exactly one which is nice
            return $found->ID;
        }

        return null;
    }

    /**
     * @param Post $object
     */
    protected function update($object)
    {
        if (empty($object->ID)) {
            throw new \RuntimeException('ID needed for updates');
        }

        wp_update_post($this->toArray($object));
    }

    /**
     * @param object $double
     *
     * @return int
     */
    protected function create($double): int
    {
        $postId = wp_insert_post($this->toArray($double), true);

        if ($postId instanceof \WP_Error) {
            throw new PersistException($double, $postId);
        }

        return $postId;
    }

    /**
     * @param Post   $object
     * @param string $fixtureName
     */
    public function delete($object, string $fixtureName)
    {
        $id = $this->find($object, $fixtureName);

        if (null !== $id) {
            wp_delete_post($id);
        }
    }
}
