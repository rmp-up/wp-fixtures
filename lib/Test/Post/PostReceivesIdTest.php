<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ${SHORT}
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
 * @copyright  2018 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-02-02
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\Post;

use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Persistance
 *
 * When a post is created or updated
 * then it's representative object will receive the ID.
 *
 * @internal
 *
 * @copyright  2018 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-02
 */
class PostReceivesIdTest extends AbstractTestCase
{
    /**
     * @dataProvider postsValidUnique
     * @param $postData
     */
    public function testCreatePost($post)
    {
        static::assertEquals('publish', $post->post_status);
        static::assertEmpty($post->ID);

        $this->posts()->persist($post, uniqid('', true));

        static::assertIsInt($post->ID);
        static::assertEquals($post->post_title, get_the_title($post->ID));
    }

    public function postsValidUnique()
    {
        return $this->objectsToDataProvider(parent::postsValidUnique());
    }


}
