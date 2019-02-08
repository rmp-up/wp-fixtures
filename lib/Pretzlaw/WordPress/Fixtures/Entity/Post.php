<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Post representation
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
 * @copyright  2018 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-02-02
 */

declare(strict_types=1);

namespace Pretzlaw\WordPress\Fixtures\Entity;

/**
 * Post
 *
 * @copyright  2018 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-02
 *
 */
class Post extends \stdClass implements Sanitizable
{
    use AbbreviationTrait;
    use DefaultsTrait;

    /**
     * Post ID.
     *
     * @since 3.5.0
     * @var int
     */
    public $ID;

    /**
     * ID of post author.
     *
     * A numeric string, for compatibility reasons.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_author = 0;

    /**
     * The post's local publication time.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_date;

    /**
     * The post's GMT publication time.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_date_gmt;

    /**
     * The post's content.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_content;

    /**
     * The post's title.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_title;

    /**
     * The post's excerpt.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_excerpt;

    /**
     * The post's status.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_status = 'publish';

    /**
     * Whether comments are allowed.
     *
     * @since 3.5.0
     * @var string
     */
    public $comment_status;

    /**
     * Whether pings are allowed.
     *
     * @since 3.5.0
     * @var string
     */
    public $ping_status;

    /**
     * The post's password in plain text.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_password;

    /**
     * The post's slug.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_name;

    /**
     * URLs queued to be pinged.
     *
     * @since 3.5.0
     * @var string
     */
    public $to_ping;

    /**
     * URLs that have been pinged.
     *
     * @since 3.5.0
     * @var string
     */
    public $pinged;

    /**
     * The post's local modified time.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_modified;

    /**
     * The post's GMT modified time.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_modified_gmt;

    /**
     * ID of a post's parent post.
     *
     * @since 3.5.0
     * @var int
     */
    public $post_parent;

    /**
     * A field used for ordering posts.
     *
     * @since 3.5.0
     * @var int
     */
    public $menu_order = 0;

    /**
     * The post's type, like post or page.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_type = 'post';

    /**
     * An attachment's mime type.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_mime_type;

    public $tax_input;

    public function __construct()
    {
        $this->defaultFixtures = [
            'post_title' => uniqid('Mandatory field with random data created by fixture', true),
            'post_content' => uniqid('Mandatory content with random data created by fixture', true),
            'post_excerpt' => uniqid('Mandatory excerpt with random data created by fixture', true),
        ];
    }

    public function sanitize()
    {
        $this->applyAbbreviations(['post_']);
        $this->applyDefaults();

        if (empty($this->post_content)) {
            $this->post_content = uniqid('Random content made by fixture', true);
        }

        if (empty($this->post_title)) {
            $this->post_content = uniqid('Random title made by fixture', true);
        }

        if ($this->post_author instanceof User) {
            $this->post_author = $this->post_author->ID;
        }
    }
}