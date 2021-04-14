<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * CRUD for options
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
 * @copyright 2020 M. Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 * @link      https://project.rmp-up.de/wp-fixtures
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Repository;

use RmpUp\WordPress\Fixtures\Entity\Option;

class Options extends AbstractRepository
{

    /**
     * @param Option $object Fixture to lookup.
     * @param string $fixtureName Name as in the Yaml-Fixture-Config
	 *                            (deprecated, will be removed)
     *
     * @return int|null ID or null when not found
     */
    public function find($object, string $fixtureName = '')
    {
        // There is no such thing.
        return null;
    }

    /**
     * Remove options
     *
     * @param Option $object      Associative property-value store.
     * @param string $fixtureName Name as in the Yaml-Fixture-Config
	 *                            (deprecated, will be removed)
     */
    public function delete($object, string $fixtureName = '')
    {
        foreach (array_keys((array) $object) as $option) {
            delete_option($option);
        }
    }

    /**
     * Store given options.
     *
     * @param Option $object Associative property to value store.
     *
     * @return int
     */
    protected function create($object): int
    {
        foreach (get_object_vars($object) as $option => $value) {
            update_option($option, $value);
        }

        // The option container shall never have an ID.
        return 0;
    }

    /**
     * Update option values.
     *
     * @param Option $object Associative property to value store.
     */
    protected function update($object)
    {
        foreach (get_object_vars($object) as $option => $value) {
            update_option($option, $value);
        }
    }
}
