<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ComplexUserMetaDataTest.php
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
 * Complex user meta-data
 *
 * Associative and more complex user meta-data will be forwarded to WordPress as it is
 * which then stores it in a serialized way.
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\User:
 *   complex_user_meta:
 *     meta_input:
 *       research:
 *         F00:
 *           - F04
 *           - F07
 *         F42: true
 *         F80:
 *           "0": true
 *           "2": true
 * ```
 *
 * With this fixture `get_user_meta( $id, 'research', true )` will return the complete array.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-09-09
 */
class ComplexUserMetaDataTest extends AbstractTestCase
{
    /**
     * @var User[]
     */
    private $users;

    public function testStoreComplexUserData()
    {
        $user = $this->users['complex_user_meta'];

        $this->repo()->persist($user, 'complex_user_meta');
        static::assertIsInt($user->ID);

        $storedData = get_user_meta($user->ID, 'research');

        static::assertCount(1, $storedData);
        static::assertSame(
            [
                'F00' => [
                    'F04',
                    'F07'
                ],
                'F42' => true,
                'F80' => [
                    '0' => true,
                    '2' => true,
                ],
            ],
            current($storedData)
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
