<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ResolveByGuidTest.php
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

namespace Pretzlaw\WordPress\Fixtures\Test\Post\PersistPost;

use Pretzlaw\WordPress\Fixtures\Entity\Post;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * ResolveByGuidTest
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class ResolveByIdTest extends AbstractTestCase
{
    /**
     * @var string
     */
    private $guid1;

    /**
     * @var string
     */
    private $sameTitle;

    protected function setUp()
    {
        parent::setUp();

        $this->sameTitle = uniqid('', true);

        $this->fixtures = [
            Post::class => [
                'post_1' => [
                    'post_title' => $this->sameTitle,
                    'post_excerpt' => 'foo',
                ],
                'post_2' => [
                    'post_title' => uniqid('', true),
                    'post_excerpt' => 'bar',
                ]
            ]
        ];
    }

    public function testCreatesWhenSameGuidNotThere()
    {
        $post1 = $this->fixture('post_1');
        $post2 = $this->fixture('post_2');

        $this->posts()->persist($post1);

        static::assertNotNull($post1->ID);
        $post2->ID = $post1->ID;

        $this->posts()->persist($post2);

        $actual = get_post($post1->ID);

        // This tells us that the second updates the first.
        static::assertEquals($actual->post_excerpt, $post2->post_excerpt);
        static::assertEquals($actual->ID, $post2->ID);
        static::assertNotEquals($actual->post_title, $this->sameTitle);
        static::assertNotEquals($actual->post_excerpt, $post1->post_excerpt);
    }
}