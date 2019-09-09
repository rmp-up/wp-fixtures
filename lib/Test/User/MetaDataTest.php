<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MetaDataTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\User;

use RmpUp\WordPress\Fixtures\Entity\User;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Meta-Data
 *
 * To set meta-data for a user you can use the "meta_input" field
 * as known from posts:
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\User:
 *   some_meta_data:
 *     user_email: some_meta_data@example.org
 *     meta_input:
 *       locale: fr_FR
 *       show_welcome_panel: 0
 *       dismissed_wp_pointers: wp496_privacy
 *       admin_color: fresh
 *       nobility: prince
 * ```
 *
 * This sets internal meta-data like
 *
 * * `locale` which is handy to test for correct translations
 * * `show_welcome_panel = 0` to hide big backend welcome panels on automated screenshots
 * * `dismissed_wp_pointers` to dismiss another panel that would be shown in the backend
 *
 * But it can also add custom meta-data like `nobility = prince`
 * so that `get_user_meta( $id, 'nobility', true )` will return the string "prince".
 *
 * Note: Previously meta-data stored for the user will be overwritten.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-09-09
 */
class MetaDataTest extends AbstractTestCase
{
    /**
     * @var User[]
     */
    private $users;

    public function testSetUserMetadata()
    {
        // Persist user
        $this->repo()->persist($this->users['some_meta_data'], 'some_meta_data');

        wp_cache_flush();

        static::assertIsInt($this->users['some_meta_data']->ID);
        $storedUserMeta = get_user_meta($this->users['some_meta_data']->ID);

        static::assertArraySubset(
            [
                // The values are an array because that's what get_user_meta does
                // no matter if there is just one value or not.
                // Basically we proved that the data has been stored.
                'locale' => ['fr_FR'],
                'show_welcome_panel' => [0],
                'admin_color' => ['fresh'],
                'dismissed_wp_pointers' => ['wp496_privacy'],
                'nobility' => ['prince'],
            ],
            $storedUserMeta
        );
    }

    public function testOverwriteExistingData()
    {
        // Persist user
        $user = $this->users['some_meta_data'];
        $this->repo()->persist($user, 'some_meta_data');
        $originalUserId = $user->ID;
        static::assertIsInt($originalUserId);

        // Change meta-value
        update_user_meta($originalUserId, 'admin_color', 'big beige');
        wp_cache_flush();
        static::assertEquals('big beige', get_user_meta($originalUserId, 'admin_color', true));

        // Reset and persist fixture
        wp_cache_flush();
        $user->ID = null;
        $this->repo()->persist($user, 'some_meta_data');

        // Fixture has overwritten data
        static::assertEquals(['fresh'], get_user_meta($originalUserId, 'admin_color'));
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
