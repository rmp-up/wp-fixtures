<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AssignUserToPostTest.php
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
 * @package   wp-fixtures
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Users;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_Post;
use WP_User;

/**
 * Assign user to post
 *
 * ```yaml
 * WP_User:
 *   some_user_for_post:
 *     roles: editor
 *     user_login: autpt
 *     user_email: autpt@example.org
 *
 * WP_Post:
 *   post_with_a_user:
 *     post_title: This post has a author assigned
 *     author: "@some_user_for_post"
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class AssignUserToPostTest extends TestCase
{
    /**
     * @var WP_Post
     */
    private $post;

    /**
     * @var WP_User
     */
    private $user;

    protected function compatSetUp()
    {
        parent::compatSetUp();

        $this->post = $this->fixtures['post_with_a_user'];
        $this->user = $this->fixtures['some_user_for_post'];
    }

    public function testUserIdIsReferenced()
    {
        static::assertEquals(0, $this->user->ID);
        static::assertEquals(0, $this->post->post_author);

        $this->user->ID = 7;

        static::assertEquals(7, $this->post->post_author);
    }

    public function testPersistanceOfUserForPost()
    {
        static::assertEquals(0, $this->post->post_author);
        $this->fixtures()->persist($this->user);

        static::assertNotEquals(0, $this->post->post_author);
    }
}
