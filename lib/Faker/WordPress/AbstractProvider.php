<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbstractProvider.php
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
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Faker\WordPress;

use Closure;
use Faker\Generator;
use ReflectionClass;

/**
 * AbstractProvider
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
abstract class AbstractProvider
{
    /**
     * Generating fake data
     *
     * @var Generator
     */
    protected $generator;

    /**
     * Create a new provider
     *
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param string $className
     * @param array $fieldToValue
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function createObject(string $className, array $fieldToValue)
    {
        $instance = (new ReflectionClass($className))->newInstanceWithoutConstructor();
        $hydrator = $this->hydrator();

        (Closure::bind($hydrator, $instance, $className))($fieldToValue);

        return $instance;
    }

    public function hydrator()
    {
        return function ($map) {
            foreach ($map as $field => $value) {
                $this->$field = $value;
            }
        };
    }
}