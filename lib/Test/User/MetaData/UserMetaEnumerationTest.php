<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * UserMetaEnumerationTest.php
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
 * @since      2019-09-09
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\User\MetaData;

use RmpUp\WordPress\Fixtures\Entity\User;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Enumerations in meta-data
 *
 * Usually WordPress would store an array of data as one single serialized value:
 *
 * ```php
 * <?php
 *
 * $data = [ 42, 1337, 13 ];
 * update_user_meta( 1, 'multi_vendor_customers', $data );
 * ```
 *
 * Such statements would end in a single database row like
 *
 * user_id  | meta_key                  | meta_value
 * -------- | ----------                | ------------
 * 1        | multi_vendor_customers    | `a:3:{i:0;i:42;i:1;i:1337;i:2;i:13;}`
 *
 * But no developer would mistreat data that way
 * because it is not very searchable for `get_users()` or other SQL queries.
 * As long as the provided data is an enumeration like this
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\User:
 *   user_meta_enums:
 *     meta_input:
 *       multi_vendor_customers:
 *         - 42
 *         - 1337
 *         - 13
 * ```
 *
 * we take over and store the data in a more usable way:
 *
 * user_id  | meta_key                  | meta_value
 * -------- | ----------                | ------------
 * 1        | multi_vendor_customers    | 42
 * 1        | multi_vendor_customers    | 1337
 * 1        | multi_vendor_customers    | 13
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-09-09
 */
class UserMetaEnumerationTest extends AbstractTestCase
{
    /**
     * @var User[]
     */
    private $users;

    public function testEnums()
    {
        $this->repo()->persist($this->users['user_meta_enums'], 'user_meta_enums');
        $userId = $this->users['user_meta_enums']->ID;

        static::assertIsInt($userId);

        static::assertEquals([42, 1337, 13], get_user_meta($userId, 'multi_vendor_customers'));

        $data = $this->query(
            'SELECT meta_value FROM ' . $this->wpdb()->usermeta . ' WHERE user_id = %d AND meta_key = %s',
            [$userId, 'multi_vendor_customers']
        );

        static::assertEquals(
            [
                ['meta_value' => 42],
                ['meta_value' => 1337],
                ['meta_value' => 13],
            ],
            $data
        );
    }

    protected function setUp()
    {
        parent::setUp();

        $this->users = $this->loadFromDocComment(0);

        foreach ($this->users as $userLogin => $user) {
            $this->repo()->delete($user, $userLogin);

            static::assertNull($this->repo()->find($user, $userLogin));
        }

        wp_cache_flush();
    }
}
