<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MandatoryFields.php
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

namespace RmpUp\WordPress\Fixtures\Test\User;

use RmpUp\WordPress\Fixtures\Entity\User;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Mandatory but pre-filled fields
 *
 * Working with users in WordPress require some fields to be set.
 * While you may know user_login is the most mandatory one
 * we like to show up some more to suppress known warnings and notices.
 * But there is no need for those fields to be defined in the configuration.
 * We will take care of missing mandatory fields
 * and fill them with semi-random data:
 *
 * * user_email (default "fixture-{timestamp}.{microseconds}@example.org")
 * * user_login (default: same as email)
 * * user_pass (default: some random string)
 *
 * This shall help you keep away the mandatory clutter
 * and focus on the things you really need:
 *
 * ```yaml
 *\RmpUp\WordPress\Fixtures\Entity\User:
 *   much_clutter:
 *     user_login: foo
 *     user_email: bar@baz.qux
 *     user_pass: 00000
 *     roles: ['administrator']
 *
 *   less_clutter:
 *     roles: ['administrator']
 * ```
 *
 * @method User fixture(string $key)
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class MandatoryFieldsTest extends AbstractTestCase
{
    private $mandatory = [
        'user_email' => 'foo@bar.baz',
        'user_login' => 'qux',
        'user_pass' => 123,
    ];

    protected function setUp()
    {
        parent::setUp();

        $userConfigs = [
            'nothing' => [
                'roles' => ['administrator'],
            ],
        ];

        // Have a config missing one single entry
        foreach (array_keys($this->mandatory) as $field) {
            $userConfigs['no_' . $field] = $this->mandatory;
            unset($userConfigs['no_' . $field][$field]);
        }

        $this->fixtures = [User::class => $userConfigs];
    }

    public function testFillsNothing()
    {
        $user = $this->fixture('nothing');
        $user->sanitize(uniqid('', true));

        foreach (array_keys($this->mandatory) as $field) {
            static::assertNotEmpty($user->$field, $field);
        }
    }

    /**
     * @dataProvider mandatoryFields
     */
    public function testFillsMissingWithData(string $mandatoryField)
    {
        $user = $this->fixture('no_' . $mandatoryField);

        static::assertEmpty($user->$mandatoryField);

        $user->sanitize(uniqid('', true));

        static::assertNotEmpty($user->$mandatoryField);
    }

    public function mandatoryFields()
    {
        foreach (array_keys($this->mandatory) as $fieldName) {
            yield [$fieldName];
        }
    }

    public function testLoginInheritsFromMail()
    {
        $user = $this->fixture('no_user_login');

        static::assertEmpty($user->user_login);

        $user->sanitize(uniqid('', true));

        static::assertNotEmpty($user->user_login);
        static::assertEquals($user->user_email, $user->user_login);
    }
}
