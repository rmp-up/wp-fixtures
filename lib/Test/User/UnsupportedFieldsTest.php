<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * UnsupportedFieldsTest.php
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

use RmpUp\WordPress\Fixtures\Entity\InvalidFieldException;
use RmpUp\WordPress\Fixtures\Entity\User;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Unsupported fields
 *
 * It is not possible to define such fields in the Yaml:
 *
 * * ID (due to wp_insert_user)
 *
 * You will be noted about those problems during the persistence .
 * So please keep in mind that a part of the data may have already made it
 * into the database until an invalid field occurs.
 *
 * @method User fixture(string $key)
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class UnsupportedFieldsTest extends AbstractTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->fixtures = [
            User::class => [
                'id' => [
                    'ID' => 123,
                ]
            ]
        ];

        $this->expectException(InvalidFieldException::class);
    }

    public function testId()
    {
        $this->expectExceptionMessage('Using ID is not allowed due to wp_insert_user');
        $this->fixture('id')->validate(uniqid('', true));
    }
}
