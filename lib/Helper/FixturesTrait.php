<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FixturesTrait.php
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

namespace RmpUp\WordPress\Fixtures\Helper;

use RmpUp\WordPress\Fixtures\Faker\WordPressFixtureLoader;

/**
 * FixturesTrait
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
trait FixturesTrait
{
    protected $loaderToFixture = [];

    public function fixtures($loader = null): Fixtures
    {
        $hash = null;
        if (is_object($loader)) {
            $hash = spl_object_hash($loader);
        }

        if (null === $loader) {
            $hash = WordPressFixtureLoader::class;
        }

        if (false === array_key_exists($hash, $this->loaderToFixture)) {
            if (null === $loader) {
                $loader = new WordPressFixtureLoader();
            }

            $this->loaderToFixture[$hash] = new Fixtures($loader);
        }

        return $this->loaderToFixture[$hash];
    }
}