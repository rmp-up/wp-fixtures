<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * UseFixtureNameAsLogin.php
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
 * @since      2019-02-10
 */

declare(strict_types=1);

namespace Pretzlaw\WordPress\Fixtures\Test\User;

use Pretzlaw\WordPress\Fixtures\Entity\User;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Defaults
 *
 * Leaving the user_login and user_email empty like this:
 *
 * ```yaml
 * \Pretzlaw\WordPress\Fixtures\Entity\User:
 *   some_new_admin:
 *     user_pass: secureDeluxe!123
 * ```
 *
 * Will be fixed automatically by using the fixture name
 * turning the configuration internally into this:
 *
 * ```yaml
 * \Pretzlaw\WordPress\Fixtures\Entity\User:
 *   some_new_admin:
 *     user_login: some_new_admin
 *     user_email: some_new_admin@example.org
 *     user_pass: secureDeluxe!123
 * ```
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-10
 */
class UseFixtureNameForFields extends AbstractTestCase
{
    /**
     * @group unit
     */
    public function testSanitizeUsesNameAsLogin()
    {
        $user = new User();

        static::assertEmpty($user->user_login);

        $someFixtureName = uniqid('', true);
        $user->sanitize($someFixtureName);

        static::assertEquals($someFixtureName, $user->user_login);
    }

    /**
     * @group unit
     */
    public function testSanitizeUsesNameAsEmail()
    {
        $user = new User();

        static::assertEmpty($user->user_login);

        $someFixtureName = uniqid('', true);
        $user->sanitize($someFixtureName);

        static::assertEquals($someFixtureName . '@example.org', $user->user_email);
    }

    /**
     * @group integration
     */
    public function testUserLoginStoredWithFixtureName()
    {
        $user = $this->loadFromDocComment(0, 'some_new_admin');

        $this->repo()->delete($user, 'some_new_admin');
        static::assertFalse(get_user_by('login', 'some_new_admin'));

        $this->assertPersistance($user, 'login', 'some_new_admin');
    }

    /**
     * @group integration
     */
    public function testUserEmailStoredWithFixtureName()
    {
        $user = $this->loadFromDocComment(0, 'some_new_admin');

        $this->repo()->delete($user, 'some_new_admin');
        static::assertFalse(get_user_by('login', 'some_new_admin'));

        $this->assertPersistance($user, 'email', 'some_new_admin@example.org');
    }

    private function assertPersistance(User $user, string $field, string $value)
    {
        $this->repo()->persist($user, 'some_new_admin');

        $found = get_user_by($field, $value);
        static::assertInstanceOf(\WP_User::class, $found);
        static::assertEquals($user->ID, $found->ID);
    }
}