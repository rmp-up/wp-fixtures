<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PostCouldNotBeLoadedTest.php
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
 * @package    wp-fixtures
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-12-15
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Provider\WpPost;

use DomainException;
use Faker\Factory;
use Faker\Generator;
use Pretzlaw\PHPUnit\DocGen\DocComment\Parser;
use Pretzlaw\WPInt\Filter\FilterAssertions;
use Pretzlaw\WPInt\Traits\PostAssertions;
use RmpUp\WordPress\Fixtures\Faker\WordPress\WpPostProvider;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Fetch problems while loading the post
 *
 * After creating the post it could happen
 * that the post can not be loaded properly.
 * In that case we throw a `DomainException`.
 *
 * @internal
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class PostCouldNotBeLoadedTest extends AbstractTestCase
{
    use Parser;
    use FilterAssertions;

    protected $exceptionType;

    public function testThrowsDomainExceptionOrSimilar()
    {
        static::assertTrue(
            DomainException::class === $this->exceptionType
            || in_array(DomainException::class, class_implements($this->exceptionType)),
            $this->exceptionType
        );
    }

    public function testThrowsException()
    {
        $this->mockFilter('wp_insert_post')->expects($this->atLeastOnce())->willReturnCallback(
            function ($postId, $post) {
                if ($post->post_type === 'testing') {
                    global $wpdb;
                    $wpdb->delete($wpdb->posts, ['ID' => $post->ID]);
                    wp_cache_flush();
                }
            }
        );

        $this->expectException(DomainException::class);

        (new WpPostProvider(Factory::create()))->wpPost('testing');
    }

    protected function setUp()
    {
        $this->exceptionType = (string) $this->classComment()->xpath('//code[1]/text()', 0);

        parent::setUp();
    }
}