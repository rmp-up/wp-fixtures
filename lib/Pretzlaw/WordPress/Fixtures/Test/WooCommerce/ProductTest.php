<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ProductTest.php
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

namespace Pretzlaw\WordPress\Fixtures\Test\WooCommerce;

use Pretzlaw\WordPress\Fixtures\Entity\WooCommerce\Product;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Product
 *
 * Sample configuration:
 *
 * ```yaml
 * \Pretzlaw\WordPress\Fixtures\Entity\WooCommerce\Product:
 *   product_1:
 *     title: Cheese
 *     sku: cheese-2019
 *     price: 5
 * ```
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class ProductTest extends AbstractTestCase
{
    public function testSampleProduct()
    {
        /** @var Product $product */
        $product = $this->loadFromDocComment(1, 'product_1');

        $this->repo()->persist($product);

        static::assertIsInt($product->ID);

        $actual = wc()->product_factory->get_product($product->ID);

        static::assertEquals('Cheese', $actual->get_title());
        static::assertEquals(5.0, $actual->get_price('edit'));
        static::assertEquals('cheese-2019', $actual->get_sku());
    }
}