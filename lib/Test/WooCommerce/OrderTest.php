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

namespace RmpUp\WordPress\Fixtures\Test\WooCommerce;

use RmpUp\WordPress\Fixtures\Entity\User;
use RmpUp\WordPress\Fixtures\Entity\WooCommerce\Order;
use RmpUp\WordPress\Fixtures\Entity\WooCommerce\Product;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Product
 *
 * Sample configuration:
 *
 * ```yaml
 * \RmpUp\WordPress\Fixtures\Entity\WooCommerce\Order:
 *   order_1:
 *     user: "@user_1"
 *     products:
 *     - "@product_1"
 *
 * \RmpUp\WordPress\Fixtures\Entity\User:
 *   user_1:
 *     email: some_random_person@example.org
 *
 * \RmpUp\WordPress\Fixtures\Entity\WooCommerce\Product:
 *   product_1:
 *     title: Animals - Original Mix
 * ```
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class OrderTest extends AbstractTestCase
{
    /**
     * @var Order
     */
    private $expectedOrder;

    protected function setUp()
    {
        parent::setUp();

        $this->expectedOrder = $this->loadFromDocComment(0, 'order_1');
    }

    public function testDataParsed()
    {
        static::assertInstanceOf(Order::class, $this->expectedOrder);
        static::assertInstanceOf(Product::class, reset($this->expectedOrder->products));
        static::assertInstanceOf(User::class, $this->expectedOrder->user);
    }

    public function testPersists()
    {
        $this->persistOrder($this->expectedOrder);

        $storedOrder = $this->fetchOrder($this->expectedOrder->id);

        static::assertEquals($this->expectedOrder->id, $storedOrder->get_id());
        self::assertEquals($this->expectedOrder->user->ID, $storedOrder->get_customer_id());
        self::assertCount(1, $storedOrder->get_items());

        $items = $storedOrder->get_items();
        /** @var \WC_Order_Item_Product $firstItem */
        $firstItem = reset($items);

        self::assertEquals($this->expectedOrder->products[0]->ID, $firstItem->get_product_id());
    }

    /**
     * @return \WC_Order
     */
    private function fetchOrder($id)
    {
        /** @var \WC_Order_Factory $factory */
        $factory = wc()->order_factory;

        return $factory->get_order($id);
    }

    private function persistOrder(Order $order)
    {
        $this->repo()->persist($order->user, 'user_1');
        $this->repo()->persist(reset($order->products), 'product_1');
        $this->repo()->persist($order, 'order_1');
    }
}
