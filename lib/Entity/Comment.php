<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Comment.php
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
 * @package   pretzlaw/wp-fixtures
 * @copyright 2019 Mike Pretzlaw
 * @license   https://mike-pretzlaw.de/license-generic.txt
 * @link      https://project.mike-pretzlaw.de/pretzlaw/wp-fixtures
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Entity;

use DateTime;
use WP_Post;

/**
 * Comment
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class Comment extends \stdClass implements Sanitizable
{
    use AbbreviationTrait;
    use ReduceTrait;

    public $id;
    public $post;
    public $content;
    public $date;
    public $approved;
    public $author;
    public $author_email;
    public $author_ip;
    public $author_url;

    public function __construct()
    {
        $this->abbreviations = [
            'comment_post_ID' => 'post',
            'comment_post' => 'post',
            'post_id' => 'post',
        ];
    }

    public function sanitize(string $fixtureName)
    {
        $this->applyAbbreviations(['comment_']);

        $this->reduce(
            [
                'post' => [
                    Post::class => 'ID',
                    WP_Post::class => 'ID',
                ],
                'date' => [
                    DateTime::class => function (DateTime $date) {
                        return $date->format(DateTime::ATOM);
                    }
                ]
            ]
        );
    }
}
