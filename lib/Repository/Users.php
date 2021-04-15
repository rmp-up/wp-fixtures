<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Users.php
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

use stdClass;
use WP_Error;
use WP_User;

/**
 * Users
 *
 * @copyright  2020 M. Pretzlaw (https://rmp-up.de)
 */
class Users extends AbstractRepository
{
    public function __construct()
    {
        parent::__construct();

        // Disable sending mails when changing password.
        add_filter('send_password_change_email', '__return_false');
    }

    /**
     * WordPress "find_by" to object field.
     *
     * @var string[]
     */
    const FIND_ORDER = [
        'login' => 'user_login',
        'email' => 'user_email',
        'slug' => 'user_nicename',
    ];

    protected function create($object): int
    {
        $data = $this->toArray($object);

        $id = wp_insert_user($data);

        if ($id instanceof WP_Error) {
            throw new PersistException($object, $id);
        }

        $this->updateMetaData('user', $id, $data['meta_input'] ?? []);

        return (int) $id;
    }

    protected function update($object)
    {
        $data = $this->toArray($object);

        $userId = wp_update_user((object) $data);

        if (false === is_numeric($userId)) {
            throw new PersistException($object, $userId);
        }

        $this->updateMetaData('user', (int) $userId, $data['meta_input'] ?? []);
    }

    /**
     * @param WP_User|stdClass $object
     * @param string $fixtureName Name as in the Yaml-Fixture-Config
	 *                            (deprecated, will be removed)
	 *
     * @return int|null ID or null when not found.
     */
    public function find($object, string $fixtureName = '')
    {
        $found = null;

        foreach (self::FIND_ORDER as $wpKey => $objectField) {
            if (empty($object->$objectField)) {
                continue;
            }

            $found = get_user_by($wpKey, $object->$objectField);

            if ($found instanceof WP_User) {
                return $found->ID;
            }
        }

        return null;
    }

    /**
     * @param WP_User|stdClass $object
     * @param string $fixtureName Name as in the Yaml-Fixture-Config
	 *                            (deprecated, will be removed)
     */
    public function delete($object, string $fixtureName = '')
    {
        $id = $this->find($object, $fixtureName);

        if (null === $id) {
            return;
        }

        if (!wp_delete_user($id)) {
            throw new RepositoryException($object, 'Could not delete user ' . $id);
        }
    }
}
