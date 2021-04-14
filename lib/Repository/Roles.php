<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Roles.php
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

use WP_Role;

/**
 * Roles
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Roles extends AbstractRepository
{
    /**
     * @param WP_Role $object
     *
     * @return int
     */
    protected function create($object): int
    {
		wp_roles()->add_role(
			$object->name,
			$object->name,
			$object->capabilities
		);

        return 0;
    }

    /**
     * @param WP_Role $object
     * @param string $fixtureName Name as in the Yaml-Fixture-Config
	 *                            (deprecated, will be removed)
     */
    public function delete($object, string $fixtureName = '')
    {
    	wp_roles()->remove_role($object->name);
    }

    /**
     * @param WP_Role $object
     * @param string $fixtureName Name as in the Yaml-Fixture-Config
	 *                            (deprecated, will be removed)
     *
     * @return string|null
     */
    public function find($object, string $fixtureName = '')
    {
        if (array_key_exists($object->name, $this->getCurrentValue())) {
            return $object->name;
        }

        return null;
    }

    private function getCurrentValue()
    {
        $prepare = $this->db->prepare(
            'SELECT `option_value`
                    FROM `' . $this->db->options . '`
                    WHERE option_name = %s',
            $this->getOptionName()
        );

        if (!is_string($prepare)) {
            return null;
        }

        return maybe_unserialize(
            (string) $this->db->get_var($prepare)
        );
    }

    private function getOptionName(): string
    {
    	$roles = wp_roles();
    	if ($roles instanceof \WP_Roles) {
    		return $roles->role_key;
		}

        return $this->db->get_blog_prefix(get_current_blog_id()) . 'user_roles';
    }

    /**
     * @param WP_Role $object
     */
    protected function update($object)
    {
        $this->create($object);
    }

    /**
     * @param mixed $current
     */
    protected function updateValue($current)
    {
        $this->db->update(
            $this->db->options,
            ['option_value' => maybe_serialize($current)],
            ['option_name' => $this->getOptionName()]
        );
    }
}
