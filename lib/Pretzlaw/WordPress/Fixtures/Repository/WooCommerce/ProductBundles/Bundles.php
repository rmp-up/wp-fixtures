<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Bundles.php
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
 * @since      2019-02-04
 */

declare(strict_types=1);

namespace Pretzlaw\WordPress\Fixtures\Repository\WooCommerce\ProductBundles;

use Pretzlaw\WordPress\Fixtures\Entity\Sanitizable;
use Pretzlaw\WordPress\Fixtures\Entity\WooCommerce\Product;
use Pretzlaw\WordPress\Fixtures\Entity\WooCommerce\ProductBundles\Bundle;
use Pretzlaw\WordPress\Fixtures\Repository\RepositoryInterface;
use Pretzlaw\WordPress\Fixtures\Repository\WooCommerce\Products;

/**
 * Bundles
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-04
 */
class Bundles extends Products
{
    /**
     * @param Bundle $object
     */
    protected function update($object)
    {
        parent::update($object);
        $this->setProducts($object->ID, $object->products);
    }


    /**
     * @param Bundle $double
     *
     * @return int
     *
     * @throws \Pretzlaw\WordPress\Fixtures\Repository\PersistException
     */
    protected function create($double): int
    {
        $bundleId = parent::create($double);
        $this->setProducts($double->ID, $double->products);

        return $bundleId;
    }

    private function setProducts($bundleId, array $products)
    {
        $bundle = wc_get_product($bundleId);

        $bundledDataItems = [];
        foreach ($products as $product) {
            if ($product instanceof Product) {
                $bundledDataItems[$product->ID] = ['product_id' => $product->ID];
                continue;
            }

            throw new \RuntimeException( 'Unkown data type can not be added to bundle' );
        }

        if ($bundledDataItems) {
            $bundle->set_bundled_data_items($bundledDataItems);
            $bundle->save();
        }
    }
}