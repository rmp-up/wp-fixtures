<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * OptionsTest.php
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
 * @package   wp-fixtures
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\WordPress;

use RmpUp\WordPress\Fixtures\Test\TestCase;

/**
 * Options
 *
 * Plenty things are stored in options like what the site-url is,
 * which plugin is active, all the rewrite rules and many more things.
 * When setting up a blog (for testing / CI) we may want to have some options
 * in a particular state like this:
 *
 * ```yaml
 * options:
 *   default_options:
 *     active_plugins:
 *       - akismet/akismet.php
 *       - hello.php
 *       - woocommerce/woocommerce.php
 *     blogname: Yet another shop
 *     template: twentynineteen
 *     stylesheet: twentynineteen
 *     ping_sites: ''
 * ```
 *
 * With this the active plugins would be set to have Akismet,
 * Hello Dolly and WooCommerce activated.
 * Also the blogname would be changed,
 * the active theme set
 * and the ability to ping sites removed.
 * Any option can be changed that way.
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class OptionsTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        update_option('active_plugins', []);
    }

    public function testSetsOption()
    {
        static::assertEquals([], get_option('active_plugins'));

        $this->repo()->persist($this->loadEntities(0, 'default_options'), '');

        wp_cache_flush();

        static::assertEquals(
            [
                'akismet/akismet.php',
                'hello.php',
                'woocommerce/woocommerce.php'
            ],
            get_option('active_plugins')
        );

        static::assertEquals('Yet another shop', get_option('blogname'));
        static::assertEquals('twentynineteen', get_option('template'));
        static::assertEquals('twentynineteen', get_option('stylesheet'));
        static::assertEmpty(get_option('ping_sites'));
    }
}