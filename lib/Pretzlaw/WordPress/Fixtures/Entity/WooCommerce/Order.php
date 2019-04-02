<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Order.php
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

namespace Pretzlaw\WordPress\Fixtures\Entity\WooCommerce;

use Pretzlaw\WordPress\Fixtures\Entity\User;

/**
 * Order
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-25
 */
class Order extends \stdClass
{
    public $id;
    /**
     * @var Product[]
     */
    public $products;

    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $customer_note;
}