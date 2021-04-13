<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AssignTermsToPostTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Terms;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_Term;

/**
 * Assign terms to post
 *
 * ```yaml
 * WP_Post:
 *   post_to_assign_terms_to:
 *     title: Why this world is full of fakes
 *     tax_input:
 *       package:
 *         - nelmio-alice
 *
 * WP_Term:
 *   nelmio_alice_term:
 *     name: Nelmio Alice
 *     slug: nelmio-alice
 *     taxonomy: package
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class AssignTermsToPostTest extends TestCase
{
    protected function compatSetUp()
    {
        parent::compatSetUp();

        register_taxonomy('package', 'post');
    }

    public function testTermsAssignedToPost()
    {
        /** @var WP_Term $term */
        $term = $this->fixtures['nelmio_alice_term'];

        $this->fixtures()->persist($term);

        $post = $this->fixtures['post_to_assign_terms_to'];

        $this->fixtures()->persist($post);

        $terms = get_the_terms($post, $term->taxonomy);

        wp_cache_flush();

        static::assertCount(1, $terms);
    }

    protected function compatTearDown()
    {
        unregister_taxonomy('package');

        parent::compatTearDown();
    }
}
