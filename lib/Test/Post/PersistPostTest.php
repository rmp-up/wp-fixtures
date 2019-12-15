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

use RmpUp\WordPress\Fixtures\Entity\Post;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * PersistPost
 *
 * @internal
 *
 * @copyright  2018 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-02
 */
class PersistPostTest extends AbstractTestCase
{
    /**
     * @dataProvider postsValidUnique
     * @param Post $post
     */
    public function testPersistAllData($post)
    {
        $this->posts()->persist($post, uniqid('', true));
        $actual = get_post($post->ID);

        foreach (['post_title', 'post_content'] as $key) {
            static::assertEquals($post->$key, $actual->$key, $key . ' has different value');
        }
    }

    public function postsValidUnique()
    {
        return $this->objectsToDataProvider(parent::postsValidUnique());
    }
}
