<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * UserRoleTest.php
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
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-12-15
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\WordPress\WpUser;

use RmpUp\WordPress\Fixtures\Entity\User;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;
use WP_User;

/**
 * User role
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\User:
 *   user_role_test_editor:
 *     role: editor
 *   user_role_test_administrator:
 *     role: administrator
 * ```
 *
 * @internal
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class UserRoleTest extends AbstractTestCase
{
    public function testEditorRoleAssigned()
    {
        $this->assertUserReceivesRole('user_role_test_editor', 'editor');
    }

    public function testAdministratorRoleAssigned()
    {
        $this->assertUserReceivesRole('user_role_test_administrator', 'administrator');
    }

    protected function assertUserReceivesRole($fixtureName, $role): void
    {
        /** @var User $user */
        $user = $this->loadFromDocComment(0, $fixtureName);
        $this->repo()->persist($user, $fixtureName);

        wp_cache_flush(); // to force fresh read from persistence

        $wpUser = get_user_by('id', $user->ID);

        static::assertInstanceOf(WP_User::class, $wpUser);
        static::assertEquals([$role], $wpUser->roles);
    }
}