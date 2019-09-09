<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Orders.php
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
 * @since      2019-02-25
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Repository\WooCommerce;

use RmpUp\WordPress\Fixtures\Entity\WooCommerce\Order;
use RmpUp\WordPress\Fixtures\Repository\RepositoryInterface;
use WC_Product;

/**
 * Orders
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-25
 */
class Orders implements RepositoryInterface
{

    /**
     * @param Order  $object      Fixture data.
     * @param string $fixtureName Key as provided in fixture config
     *
     * @throws \WC_Data_Exception|\RuntimeException
     */
    public function persist($object, string $fixtureName)
    {
        $data = $this->toArray($object);
        $order = wc_create_order($data);

        if ($order instanceof \WP_Error) {
            throw new \RuntimeException('Could not create order');
        }

        foreach ($object->products as $product) {
            $wcProduct = wc()->product_factory->get_product($product->ID);

            if (false === $wcProduct instanceof WC_Product) {
                throw new \RuntimeException(
                    sprintf('Could not add product "%s to order "#%s"', $product->ID, $order->get_id())
                );
            }

            $order->add_product($wcProduct);
        }

        $order->save();

        $object->id = $order->get_id();
    }

    /**
     * @param \stdClass $object Fixture to lookup.
     * @param string    $fixtureName
     *
     * @return int|null ID or null when not found
     */
    public function find($object, string $fixtureName)
    {
        return null;
    }

    /**
     * @param \stdClass $object
     * @param string    $fixtureName
     */
    public function delete($object, string $fixtureName)
    {
        return;
    }

    private function toArray(Order $order): array
    {
        return [
            'status' => $order->status,
            'customer_id' => $order->user->ID,
            'customer_note' => $order->customer_note,
            // TODO 'parent' => null,
            // TODO 'created_via' => null,
            // TODO 'cart_hash' => null,
            'order_id' => (int) $order->id,
        ];
    }
}
