<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RolesTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\WordPress;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_Role;

/**
 * Roles
 *
 * WordPress uses a concept of Roles,
 * designed to give the site owner the ability to control what users can
 * and cannot do within the site.
 * A site owner can manage the user access to such tasks as writing
 * and editing posts, creating Pages, creating categories, moderating comments,
 * managing plugins, managing themes, and managing other users,
 * by assigning a specific role to each of the users.
 *
 * The following section will describe how to extend the given roles with new
 * capabilities
 * and how to define completely new roles.
 * In general a role description can be done like this:
 *
 * ```yaml
 * WP_Role:
 *   participant:
 *     capabilities:
 *       - create_post
 * ```
 *
 * This is the most abbreviated definition.
 * A new role with the name "participant"
 * and the capability to create posts would be created.
 * Read on to know more about the details.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class RolesTest extends TestCase
{
    /**
     * @var mixed
     */
    private $participant;

    protected function reloadRoles()
    {
        global $wp_roles;
        unset($wp_roles);
        wp_cache_flush();

        $roles = wp_roles();

        if (method_exists($roles, 'for_site')) {
        	$roles->for_site();
		}
    }

    protected function compatSetUp()
    {
        parent::compatSetUp();

        $this->participant = $this->loadEntities(0, 'participant');

        $this->fixtures()->delete($this->participant); // just to have things clean
        $this->reloadRoles();
    }

    public function testCoreRolesExistForFurtherTesting()
    {
        static::assertArraySubset(
            [
                'administrator',
                'editor',
                'author',
                'contributor',
                'subscriber',
            ],
            array_keys(wp_roles()->get_names())
        );
    }

    public function testCreatesWPRoleEntity()
    {
        $this->assertEntityMatchesDefinition(0, WP_Role::class, 'participant');
    }

    public function testRolesObjects()
    {
        static::assertInstanceOf(WP_Role::class, $this->participant);
        static::assertEquals(['create_post'], $this->participant->capabilities);
    }

    public function testRolesPersistance()
    {
        $this->reloadRoles();
        static::assertNull($this->fixtures()->find($this->participant));
        static::assertNull(wp_roles()->get_role('participant'));

        $this->fixtures()->persist($this->participant);

        $this->reloadRoles();
        static::assertEquals('participant', $this->fixtures()->find($this->participant));
        static::assertNotNull(wp_roles()->get_role('participant'));

        $this->fixtures()->delete($this->participant);

        $this->reloadRoles();
        static::assertNull($this->repo()->find($this->participant, ''));
        static::assertNull(wp_roles()->get_role('participant'));
    }
}
