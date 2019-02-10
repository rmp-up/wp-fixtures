<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * UseFixtureNameAsSku.php
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
 * @since      2019-02-10
 */

declare(strict_types=1);

namespace Pretzlaw\WordPress\Fixtures\Test\WooCommerce\Product;

use Pretzlaw\WordPress\Fixtures\Entity\WooCommerce\Product;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;
use function WP_CLI\Utils\wp_clear_object_cache;

/**
 * Defaults
 *
 * Leaving the SKU blank or providing no data at all like this:
 *
 * ```yaml
 * \Pretzlaw\WordPress\Fixtures\Entity\WooCommerce\Product:
 *   where_is_the_sku:
 *     price: 21
 *
 *   ah_ok: ~
 * ```
 *
 * Will result in products that use the fixture name as their SKU.
 * So the above will be expanded to this:
 *
 * ```yaml
 * \Pretzlaw\WordPress\Fixtures\Entity\WooCommerce\Product:
 *   where_is_the_sku:
 *     price: 21
 *     # ++
 *     sku: "where_is_the_sku"
 *
 *   ah_ok:
 *     # ++
 *     sku: "ah_ok"
 * ```
 *
 * Take a look at the behaviour of posts
 * to find out which additional fields will be prefilled with this identifier.
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-10
 */
class UseFixtureNameAsSkuTest extends AbstractTestCase
{
    public function testProductSanitizeSku()
    {
        $product = new Product();
        $product->meta_input = [];

        $sku = uniqid('', true);
        $product->sanitize($sku);

        static::assertEquals($sku, $product->meta_input['_sku']);
    }

    public function testSkuPrefilled()
    {
        foreach ($this->loadFromDocComment(0) as $key => $product) {
            $this->repo()->delete($product, $key);

            static::assertEmpty(wc_get_product_id_by_sku($key));

            $this->repo()->persist($product, $key);

            $id = wc_get_product_id_by_sku($key);
            static::assertNotEmpty($id);
            static::assertEquals($product->ID, $id);
        }
    }
}