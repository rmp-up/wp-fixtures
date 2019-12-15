<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PostParentTest.php
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

use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;
use WP_Post;
use function WP_CLI\Utils\wp_clear_object_cache;

/**
 * Post parent
 *
 * Just like any other reference you can point to other entities:
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\Post:
 *   tyler:
 *     post_title: Jerry
 *   peter:
 *     post_title: Ben
 *     post_parent: '@tyler'
 * ```
 *
 * This way "peter" / "Ben" has become a child of "tyler" / "Jerry".
 *
 * But sometimes you don't want to describe the parent or child that much.
 * In that case use a provider
 * and shrink it down to this:
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\Post:
 *   peter:
 *     post_parent: '<wpPost()>'
 * ```
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class PostParentTest extends AbstractTestCase
{
    public function testCreatesParentChildRelation()
    {
        static::assertNotEmpty($this->compiledFixtures['peter']->ID, 'Failed persist peter');
        static::assertNotEmpty($this->compiledFixtures['tyler']->ID, 'Failed persist tyler');

        $peter = get_post($this->compiledFixtures['peter']->ID);
        static::assertInstanceOf(WP_Post::class, $peter, 'Could not load peter');

        $tyler = get_post($peter->post_parent);
        static::assertInstanceOf(WP_Post::class, $tyler, 'Could not load tyler');

        static::assertSame($this->compiledFixtures['tyler']->ID, $tyler->ID, 'Tyler should not have different ID');
    }

    public function testCreatesRandomParent()
    {
        $data = $this->loadFromDocComment(1);
        static::assertCount(1, $data); // assert that we loaded the correct one

        $this->repo()->persist($data['peter'], 'peter');
        wp_cache_flush();

        $peter = get_post($data['peter']->ID);

        static::assertInstanceOf(WP_Post::class, $peter);
        static::assertNotEmpty($peter->post_parent);
        static::assertInstanceOf(WP_Post::class, get_post($peter->post_parent));

        $this->repo()->delete($data['peter'], 'peter');
    }

    protected function setUp()
    {
        parent::setUp();

        $this->compiledFixtures = $this->loadFromDocComment();
        $this->repo()->persist($this->compiledFixtures['tyler'], 'tyler');
        $this->repo()->persist($this->compiledFixtures['peter'], 'peter');

        wp_cache_flush();
    }

    protected function tearDown()
    {
        $this->repo()->delete($this->compiledFixtures['peter'], 'peter');
        $this->repo()->delete($this->compiledFixtures['tyler'], 'tyler');

        parent::tearDown();
    }
}