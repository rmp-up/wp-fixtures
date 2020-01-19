<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WordPressProvider.php
 *
 * LICENSE: This source file is created by the company around Mike Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package    wp-fixtures
 * @copyright  2020 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @since      2020-01-18
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Faker;

use Faker\Generator;
use RmpUp\WordPress\Fixtures\Faker\WordPress\Comments;

/**
 * WordPressProvider
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class WordPressProvider
{
    use WordPress;

    /**
     * @var Generator
     */
    protected $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @inheritDoc
     */
    protected function generator(): Generator
    {
        return $this->generator;
    }
}