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

namespace Pretzlaw\WordPress\Fixtures\Test\Post\PersistPost;

use Pretzlaw\WordPress\Fixtures\Entity\Post;
use Pretzlaw\WordPress\Fixtures\Repository\PersistException;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * # FAQ
 *
 * **What if my post is empty?**
 * When some fields are still missing,
 * then WordPress refuses to create a post.
 * In that case you will see an error or exception thrown
 * with a hint about missing fields (e.g. "Content, title, and excerpt are empty.").
 * Please provide such fields to solve this.
 * Keep in mind that updates on existing posts won't show that warning
 * because updates don't need to be complete.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-02
 */
class PersistInvalidPostTest extends AbstractTestCase
{
    public function testMissingEverything()
    {
        $this->expectException(PersistException::class);
        $this->expectExceptionMessage(
            'Could not persist "Pretzlaw\WordPress\Fixtures\Entity\Post": Content, title, and excerpt are empty.'
        );

        $post = new Post();

        $this->posts()->persist($post, uniqid('', true));
    }
}