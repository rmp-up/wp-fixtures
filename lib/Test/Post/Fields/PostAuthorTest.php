<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PostAuthor.php
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
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/pretzlaw/wp-fixtures
 * @since      2019-02-03
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\Post\Fields;

use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Author
 *
 * The post_author usually is an ID but can also refer to another
 * entity:
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\User:
 *   user_1:
 *     user_email: flint@sto.ne
 *
 * \RmpUp\WordPress\Fixtures\Entity\Post:
 *   post_1:
 *     post_title: Yabadabadoooh!
 *     post_author: '@user_1'
 * ```
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class PostAuthorTest extends AbstractTestCase
{
    public function testUserIsSet()
    {
        $objects = $this->loadFromDocComment(0);

        $this->users()->persist($objects['user_1'], 'user_1');
        $this->posts()->persist($objects['post_1'], 'post_1');

        $post = get_post($objects['post_1']->ID);

        static::assertEquals($objects['user_1']->ID, $post->post_author);
    }
}
