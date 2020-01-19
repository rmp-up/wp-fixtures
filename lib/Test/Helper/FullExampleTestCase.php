<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FullExampleTestCase.php
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
 * @internal
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\Helper;

use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\Yaml\Yaml;

/**
 * FullExampleTestCase
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
trait FullExampleTestCase
{
    public function assertExampleContainsAllFields(string $expectedClass, string $exampleKey = null, int $exampleIndex = 0)
    {
        static::assertTrue(class_exists($expectedClass), 'Unknown class: ' . $expectedClass);

        $yaml = $this->getYamlFromDocComment($exampleIndex);
        static::assertNotEmpty($yaml, 'Could not load YAML');

        $data = Yaml::parse($yaml);
        static::assertNotEmpty($data, 'No example found');
        static::assertArrayHasKey($expectedClass, $data, 'No example for class found: ' . $expectedClass);
        static::assertNotEmpty($data[$expectedClass], 'Please add at least one example');

        if (null === $exampleKey) {
            // No key provided so we use the first one.
            $exampleKey = key($data[$expectedClass]);
        }

        static::assertArrayHasKey($exampleKey, $data[$expectedClass], 'Example not found: ' . $exampleKey);

        $example = $data[$expectedClass][$exampleKey];
        static::assertNotEmpty($example, 'Please fill example with fields');

        $definition = new ReflectionClass($expectedClass);
        $remainder = $example;
        foreach ($definition->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $name = $property->getName();
            static::assertArrayHasKey($name, $remainder, 'Missing field');
            static::assertNotNull($remainder[$name], 'Please add a value to the example');
            unset($remainder[$name]);
        }

        // Array should not contain fields that are not in the definition.
        static::assertEquals([], $remainder, 'There are more fields in the example than defined in the class');
    }
}