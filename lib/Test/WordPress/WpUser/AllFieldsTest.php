<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AllFieldsTest.php
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
use RmpUp\WordPress\Fixtures\Test\AbstractAllFieldsTestCase;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Complete example
 *
 * When you want to define specific fields of a user
 * then you'll need to use the user entity as in this complete example:
 *
 * ```yaml
 * RmpUp\WordPress\Fixtures\Entity\User:
 *   full_user_example:
 *     ID: 42
 *     user_email: foo@bar.baz
 *     user_login: userkare
 *     user_pass: tetipepi
 *     role: administrator
 * ```
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class AllFieldsTest extends AbstractAllFieldsTestCase
{
    protected function getTargetClassName(): string
    {
        return User::class;
    }
}