<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * CreateRandomUserTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Users;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_User;

/**
 * Create random user
 *
 * Whenever you need a random user you can use the `WP_User()` provider:
 *
 * ```yaml
 * SomeThing:
 *   a_thing_with_a_user:
 *     thingy: dingy
 *     user: <WP_User()>
 * ```
 *
 * This will attach a random `WP_User` to the user field.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class CreateRandomUserTest extends TestCase
{
    public function testRandomUserIsCreated()
    {
        /** @var WP_User $user */
        $user = $this->fixtures['a_thing_with_a_user']->user;

        static::assertInstanceOf(WP_User::class, $user);
        static::assertStringInString('wp_fixture_user', $user->user_login);
        static::assertEquals(32, strlen($user->user_pass));
        static::assertEquals($user->user_email, is_email($user->user_email));
    }
}
