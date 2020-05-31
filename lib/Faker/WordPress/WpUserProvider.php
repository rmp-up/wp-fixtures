<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpUserProvider.php
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
 * @package    wp-fixtures
 * @copyright  2020 M. Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-fixtures
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Faker\WordPress;

use stdClass;
use WP_User;

/**
 * WpUserProvider
 *
 * @copyright  2020 M. Pretzlaw (https://rmp-up.de)
 */
class WpUserProvider extends AbstractProvider
{
    /**
     * Create a new WP_User with random data.
     *
     * @return WP_User
     * @throws \ReflectionException
     */
    public function WP_User(): WP_User
    {
        /** @var WP_User $data */
        $data = new stdClass();

        $data->user_login = uniqid('wp_fixture_user', true);
        $data->user_pass = md5(uniqid('wp_fixture_user', true));
        $data->user_email = uniqid('', true) . '@' . $this->generator->safeEmailDomain;

        return $this->createObject(
            WP_User::class,
            [
                'data' => $data,
            ]
        );
    }
}