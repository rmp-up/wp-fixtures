<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Users.php
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

use RmpUp\WordPress\Fixtures\Entity\User;

/**
 * Users
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class Users extends AbstractRepository
{
    public function __construct()
    {
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
        $id = wp_insert_user($this->toArray($object));

        if ($id instanceof \WP_Error) {
            throw new PersistException($object, $id);
        }

        return (int)$id;
    }

    protected function update($object)
    {
        $result = wp_update_user((object)$this->toArray($object));

        if ($result instanceof \WP_Error) {
            throw new PersistException($object, $result);
        }
    }

    /**
     * @param User $object
     * @param string|null $fixtureName
     * @return int|null ID or null when not found.
     */
    public function find($object, string $fixtureName = null)
    {
        $found = null;

        foreach (self::FIND_ORDER as $wpKey => $objectField) {
            if (empty($object->$objectField)) {
                continue;
            }

            $found = get_user_by($wpKey, $object->$objectField);

            if ($found instanceof \WP_User) {
                return $found->ID;
            }
        }

        return null;
    }

    /**
     * @param User $object
     * @param string $fixtureName
     */
    public function delete($object, string $fixtureName)
    {
        $id = $this->find($object, $fixtureName);

        if (null === $id) {
            return;
        }

        if (!wp_delete_user($id)) {
            throw new \RuntimeException('Could not delete user ' . $id);
        }
    }
}
