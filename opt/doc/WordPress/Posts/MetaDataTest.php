<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * MetaDataTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Posts;

use RmpUp\WordPress\Fixtures\Test\TestCase;
use WP_Post;

/**
 * Meta-Data
 *
 * To insert Meta-Data you can add `meta_input` field as follows:
 *
 * ```yaml
 * WP_Post:
 *   # Common way
 *   ten_speed:
 *     content: <text()>
 *     meta_input:
 *       hello: Drop its O
 *       left: in a sudden rush
 *       goodbyes: 0
 * ```
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class MetaDataTest extends TestCase
{
    /**
     * @var WP_Post
     */
    private $tenSpeed;

    public function testWpPostWithMetaDataCreated()
    {
        static::assertEquals(
            [
                'hello' => 'Drop its O',
                'left' => 'in a sudden rush',
                'goodbyes' => 0,
            ],
            $this->tenSpeed->meta_input
        );
    }

    protected function compatSetUp()
    {
        parent::compatSetUp();

        $this->tenSpeed = $this->loadEntities(0, 'ten_speed');
        static::assertInstanceOf(WP_Post::class, $this->tenSpeed);

        $currentId = $this->fixtures()->find($this->tenSpeed);

        if ($currentId) {
            wp_delete_post($currentId, true);
        }
    }

    public function testWpMetaPersists()
    {
        $this->fixtures()->persist($this->tenSpeed);

        wp_cache_flush();

        static::assertArraySubset(
            [
                'hello' => ['Drop its O'],
                'left' => ['in a sudden rush'],
                'goodbyes' => [0],
            ],
            get_post_meta($this->tenSpeed->ID)
        );
    }
}
