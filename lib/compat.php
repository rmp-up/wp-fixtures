<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * compat.php
 *
 * Add missing functions
 * and normalize differences per plugin, theme or WordPress version.
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
 * @package    rmp-up/wp-fixtures
 * @copyright  2020 M. Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://rmp-up.de/wp-fixtures
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
