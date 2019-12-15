<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PostProvider.php
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
 * @since      2019-12-14
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Faker\WordPress;

use Faker\Generator;
use WP_Post;

/**
 * Create random posts
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-14
 */
class WpPostProvider
{
    /**
     * @var Generator
     */
    private $generator;

    /**
     * PostProvider constructor.
     *
     * @param Generator $generator To generate fake content
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    public function wpPost($postType = 'post'): WP_Post
    {
        $postId = wp_insert_post(
            [
                'post_author' => 1,
                'post_content' => $this->generator->text(),
                'post_title' => $this->generator->sentence(),
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
}