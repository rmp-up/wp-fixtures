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

namespace RmpUp\WordPress\Fixtures\Test;

use RmpUp\WordPress\Fixtures\Entity\User;

/**
 * User
 *
 * Possible (direct) fields:
 *
 * * user_email
 * * user_login
 * * user_pass
 *
 * @copyright  2018 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-02
 */
class UserTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->fixtures = [
            User::class => [
                'user_1' => [
                    'user_email' => uniqid('', true) . '@example.org',
                    'user_login' => uniqid('', true),
                    'user_pass' => uniqid('', true),
                ]
            ]
        ];
    }

    public function testCompleteUserDefinition()
    {
        /** @var User $user */
        $user = $this->fixture('user_1');

        static::assertInstanceOf(User::class, $user);

        unset($user->ID);
        $this->assertFixtureObject($user, 'user_1');
    }

    public function testCompleteDefinitionPersists()
    {
        /** @var User $user */
        $user = $this->fixture('user_1');
        static::assertInstanceOf(User::class, $user);

        $this->users()->persist($user, 'user_1');
        static::assertNotEmpty($user->ID);

        $actual = get_user_by('id', $user->ID);

        static::assertInstanceOf(\WP_User::class, $actual);

        if (!is_wp_error(wp_authenticate($user->user_login, $user->user_pass))) {
            // password seems to be stored correctly
            $actual->user_pass = $user->user_pass;
        }

        $this->assertFixtureValues('user_1', $actual, User::class);
    }

    protected function tearDown()
    {
        // Just in case for the above wp_authenticate.
        global $current_user;
        $current_user = null;

        parent::tearDown();
    }
}
