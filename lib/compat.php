<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * compat.php
 *
 * Add missing functions
 * and normalize differences per plugin, theme or WordPress version.
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
 * @since      2019-06-25
 */

declare(strict_types=1);

/**
 * WooCommerce
 */
if (function_exists('wc') && 'WooCommerce' === get_class(wc())) {
    /**
     * WooCommerce <= 3.5
     */
    if (1 === version_compare('3.6.0', wc()->version)) {
        /**
         * Add missing function
         */
        function wc_update_product_lookup_tables()
        {

        }
    }
}
