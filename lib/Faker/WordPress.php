<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WordPress.php
 *
 * LICENSE: This source file is created by the company around Mike Pretzlaw
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

namespace RmpUp\WordPress\Fixtures\Faker;

use Faker\Generator;
use RmpUp\WordPress\Fixtures\Faker\WordPress\Comments;
use RuntimeException;
use WP_Post;

/**
 * WordPress
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
trait WordPress
{
    /**
     * Possible comment status
     *
     * @var string[]
     *
     * @see \wp_set_comment_status()
     */
    protected $commentStatus = [
        'hold',
        'approve',
        'spam',
        'trash',
    ];

    /**
     * Fetch one random status from the list
     *
     * @return string
     */
    public function commentStatus(): string
    {
        $key = array_rand((array) $this->commentStatus, 1);

        if (false === is_scalar($key)) {
            // Fallback in case of problems.
            return 'hold';
        }

        return $this->commentStatus[$key];
    }

    /**
     * @return Generator
     */
    abstract protected function generator(): Generator;

    /**
     * Generate a random post.
     *
     * @param string $postType
     *
     * @return WP_Post
     */
    public function wpPost($postType = 'post'): WP_Post
    {
        $postId = wp_insert_post(
            [
                'post_author' => 1,
                'post_content' => $this->generator()->text(),
                'post_title' => $this->generator()->sentence(),
                'post_type' => $postType,
            ],
            true
        );

        if ($postId instanceof \WP_Error) {
            throw new \RuntimeException('Could not create random post: ' . $postId->get_error_message());
        }

        $post = get_post($postId);

        if (false === $post instanceof \WP_Post) {
            throw new \DomainException(
                sprintf('Could not load random post')
            );
        }

        return $post;
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
            uniqid('', true) . '@' . $this->generator()->safeEmailDomain
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