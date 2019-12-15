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

namespace RmpUp\WordPress\Fixtures\Test\WordPress\WpPost;

use RmpUp\WordPress\Fixtures\Entity\Post;
use RmpUp\WordPress\Fixtures\Test\AbstractAllFieldsTestCase;

/**
 * Complete example
 *
 * When you want to define specific fields of a post
 * then you'll need to use the post entity as in this complete example:
 *
 * ```yaml
 * RmpUp\WordPress\Fixtures\Entity\Post:
 *   full_example:
 *     ID: 5
 *     post_type: diary
 *
 *     post_date: '2019-08-07'
 *     post_modified: '@self->post_date'
 *     post_author: '<@wpUser()>'
 *     post_status: publish
 *
 *     post_title: WYSIWYG Editors
 *     post_name: wysiwyg
 *     post_content: |
 *       Hello world!
 *       Today I feel pretty and I think I fit right in.
 *       I think that you're so great because being great is great.
 *     post_excerpt: Santa seemed to hit my chimney
 *
 *     tax_input:
 *       category:
 *         - talented
 *         - 2010
 *         - 2011
 *     comment_status: closed
 *
 *
 *     ping_status: closed
 *     to_ping:
 *       - 'https://example.org'
 *     pinged:
 *       - 'https://www.example.com'
 *
 *     post_parent: '@another_post'
 *     menu_order: 5
 *
 *     post_password: lineandsinker
 *     post_mime_type: 'text/markdown'
 *
 *     meta_input:
 *       _difficulty: hard
 *       _duration: 2
 *       _myown_fields: get a grip
 *
 *
 *   another_post: ~
 * ```
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class AllFieldsTest extends AbstractAllFieldsTestCase
{
    protected function getTargetClassName(): string
    {
        return Post::class;
    }
}