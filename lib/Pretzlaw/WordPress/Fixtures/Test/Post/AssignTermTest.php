<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AssignTermTest.php
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
 * @since      2019-03-13
 */

declare(strict_types=1);

namespace Pretzlaw\WordPress\Fixtures\Test\Post;

use Pretzlaw\WordPress\Fixtures\Entity\Post;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Taxonomies and categories
 *
 * Assigning tags (either flat or hierarchical) can be done using the slug:
 *
 * ```yaml
 * Pretzlaw\WordPress\Fixtures\Entity\Post:
 *   post_1:
 *     title: Make it so
 *     tax_input:
 *       post_tag:
 *         - Engage
 * ```
 *
 * This will lookup the term "Engage" (slug: engage)
 * and assign the post to it.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-03-13
 */
class AssignTermTest extends AbstractTestCase
{
    /**
     * @var Post
     */
    private $post;

    protected function setUp()
    {
        $this->post = $this->loadFromDocComment(0, 'post_1');
        parent::setUp();
    }

    public function testAssignBySlug()
    {
        $this->repo()->persist($this->post, 'post_1');

        $terms = wp_get_post_terms($this->post->ID);

        static::assertCount(1, $terms);
        static::assertInstanceOf(\WP_Term::class, $terms[0]);
        static::assertEquals('engage', $terms[0]->slug);
        static::assertEquals('Engage', $terms[0]->name);
    }

    protected function tearDown()
    {
        parent::tearDown();

        if ($this->post->ID) {
            wp_delete_post($this->post->ID);
        }

        $term = get_term_by('slug', 'engage', 'post_tag');
        if ($term instanceof \WP_Term) {
            wp_delete_term($term->term_id, $term->taxonomy);
        }
    }
}