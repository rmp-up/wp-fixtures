<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Taxonomies.php
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

namespace RmpUp\WordPress\Fixtures\Entity;

/**
 * Taxonomies
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-03-13
 */
trait ManageTaxonomies
{
    public function resolveTerms(string $fixtureName)
    {
        if (!is_array($this->tax_input)) {
            return;
        }

        foreach ($this->tax_input as $taxonomy => $terms) {
            if (!is_array($terms)) {
                throw new \DomainException(
                    sprintf('Syntax error in tax_input for term/taxonomy "%s" in %s', (string)$terms, $fixtureName)
                );
            }

            foreach ($terms as $key => $slug) {
                $label = $slug;
                if (!is_numeric($key)) {
                    $slug = $key;
                }

                $slug = $this->assertTerm($taxonomy, $slug, $label);

                // Sanitize to term ID to cover hierarchical terms (see `wp_insert_post()` behaviour about "tax_input")
                $this->tax_input[$taxonomy][$key] = $slug->term_id;
            }
        }
    }

    /**
     * @param $taxonomy
     * @param $slug
     *
     * @return \WP_Term
     */
    private function assertTerm(string $taxonomy, string $slug, string $name): \WP_Term
    {
        $term = get_term_by('slug', $slug, $taxonomy);

        if ($term instanceof \WP_Term) {
            return $term;
        }

        $termData = wp_insert_term($name, $taxonomy, ['slug' => $slug]);

        if (!is_array($termData) || !array_key_exists('term_id', $termData)) {
            throw new \RuntimeException(
                sprintf('Could not create term "%s" in taxonomy "%s"', $slug, $taxonomy)
            );
        }

        return get_term($termData['term_id']);
    }
}
