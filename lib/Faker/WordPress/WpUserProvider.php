<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WpUserProvider.php
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

namespace RmpUp\WordPress\Fixtures\Faker\WordPress;

use Faker\Generator;
use RuntimeException;

/**
 * WpUserProvider
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class WpUserProvider
{
    /**
     * @var Generator
     */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Create a new WP_User with random data.
     *
     * @return \WP_User
     */
    public function wpUser(): \WP_User
    {
        $id = wp_create_user(
            uniqid('wp_fixture_user', true),
            md5(uniqid('wp_fixture_user', true)),
            uniqid('', true) . '@' . $this->generator->safeEmailDomain
        );

        if ($id instanceof \WP_Error) {
            throw new RuntimeException('Could not create random user: ' . $id->get_error_message());
        }

        wp_cache_flush();
        $user = get_user_by('id', $id);

        if (false === $user instanceof \WP_User) {
            throw new \DomainException('Could not fetch random user: ' . $id);
        }

        return $user;
    }
}