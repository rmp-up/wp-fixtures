<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbbreviationsTest.php
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

namespace Pretzlaw\WordPress\Fixtures\Test\User;

use Pretzlaw\WordPress\Fixtures\Entity\User;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Abbreviations
 *
 * As WordPress keeps prefixing things (even in classes) we offer a way to
 * shorten things in the fixture configuration:
 *
 * * login = user_login
 * * email = user_email
 * * pass = user_pass
 *
 * Which means that following configs are equal:
 *
 * ```yaml
 * \Pretzlaw\WordPress\Fixtures\Entity\User:
 *   user_long:
 *     user_login: Spartacus
 *     user_pass: Sura
 *     user_email: mira@batiatus.tld
 *     first_name: Spartacus
 *
 *   user_short:
 *     login: Spartacus
 *     pass: Sura
 *     email: mira@batiatus.tld
 *     first_name: Spartacus
 * ```
 *
 * As you can see only fields with the obsolete prefix ("user_") have a short version
 * while other fields don't.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class AbbreviationsTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->fixtures = [
            User::class => [
                'abbrev_user' => [
                    'login' => uniqid('', true),
                    'email' => uniqid('', true) . '@example.org',
                    'pass' => uniqid('', true)
                ]
            ],
        ];
    }

    public function testUserPrefixedFieldsCanBeAbbreviated()
    {
        /** @var User $user */
        $user = $this->fixture('abbrev_user');

        static::assertInstanceOf(User::class, $user);

        $fixtureData = $this->fixtureData('abbrev_user');
        $sanitized = clone $user;
        $sanitized->sanitize('abbrev_user');

        foreach (get_object_vars($user) as $field => $value) {
            if (0 !== strpos($field, User::PREFIX)) {
                // Not a field with the obsolete prefix.
                continue;
            }

            $short = str_replace(User::PREFIX, '', $field);

            static::assertArrayHasKey($short, $fixtureData);
            static::assertEquals($user->$short, $sanitized->$field);
        }
    }

    /**
     * Abbreviations
     *
     * If you accidentally use the long (e.g. "user_login")
     * and the short version ("login") within the same entity
     * then an exception will be thrown.
     * Except if both values are the same
     * because we just want you to know about really bad misconfiguration.
     *
     * ```yaml
     * \Pretzlaw\WordPress\Fixtures\Entity\User:
     *   this_is_okay:
     *     login: Foo
     *     user_login: Foo
     *
     *   this_is_wrong:
     *     login: Foo
     *     user_login: Bar
     * ```
     *
     *
     */
    public function testThrowsExceptionWhenLongFieldExistsAlready()
    {
        /** @var User $user */
        $user = $this->fixture('abbrev_user');

        $user->user_login = 'foo';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageRegExp('/Value would be overwritten/');

        $user->sanitize('abbrev_user');
    }
}