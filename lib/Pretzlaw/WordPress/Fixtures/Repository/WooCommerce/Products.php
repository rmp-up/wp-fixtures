<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Products.php
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

namespace Pretzlaw\WordPress\Fixtures\Repository\WooCommerce;

use Pretzlaw\WordPress\Fixtures\Entity\WooCommerce\Product;
use Pretzlaw\WordPress\Fixtures\Repository\Posts;

/**
 * Products
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class Products extends Posts
{
    /**
     * @param Product $object
     */
    protected function update($object)
    {
        if (empty($object->ID)) {
            throw new \RuntimeException('Fixture has not ID: ' . $object->post_title);
        }

        $product = wc()->product_factory->get_product($object->ID);

        if (false === $product instanceof \WC_Product) {
            throw new \RuntimeException('Product could not be loaded: ' . $object->ID);
        }

        wp_update_post($object);
    }

    /**
     * @param Product $object
     * @param string|null $fixtureName
     * @return int|null
     */
    public function find($object, string $fixtureName = null)
    {
        $found = wc_get_product_id_by_sku($fixtureName);

        if (0 !== $found) {
            return $found;
        }

        return parent::find($object, $fixtureName);
    }
}