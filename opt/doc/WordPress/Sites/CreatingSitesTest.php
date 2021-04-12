<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * CreatingSites.php
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

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Sites;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_Site;

/**
 * Create one or more new sites
 *
 * ```yaml
 * WP_Site:
 *   british_market:
 *     domain: example.com
 *     path: /gb/
 *   portuguese_market:
 *     domain: example.com
 *     path: /pt/
 *   french_market:
 *     domain: example.fr
 *     public: 0
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class CreatingSitesTest extends TestCase
{
    /**
     * @var WP_Site[]
     */
    protected $sites;

	public function setUp()
    {
        parent::setUp();

        $this->sites = $this->loadEntities();

        foreach ($this->sites as $site) {
            if (domain_exists($site->domain, $site->path)) {
                $this->fixtures()->delete($site, '');
            }
        }
    }

    protected function tearDown()
    {
        foreach ($this->sites as $site) {
            // Perhaps deleted during some test?
            if (domain_exists($site->domain, $site->path) || $this->isSiteInitialized((int) $site->blog_id)) {
                $this->fixtures()->delete($site, '');
            }
        }

        parent::tearDown();
    }

    public function testAllSitesInitialized()
    {
        foreach ($this->sites as $site) {
            static::assertNull(
                domain_exists($site->domain, $site->path),
                'Domain exists: ' . $site->domain . $site->path
            );

            $this->fixtures()->persist($site, (string) $site->siteurl);

            self::assertNotNull($site->blog_id);
            self::assertTrue($this->isSiteInitialized((int) $site->blog_id));
            static::assertIsType('int', domain_exists($site->domain, $site->path));
        }
    }
}
