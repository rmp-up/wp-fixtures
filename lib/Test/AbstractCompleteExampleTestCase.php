<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbstractCompleteExampleTest.php
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

namespace RmpUp\WordPress\Fixtures\Test;

use ReflectionClass;
use ReflectionProperty;
use RmpUp\WordPress\Fixtures\Entity\Post;
use Symfony\Component\Yaml\Yaml;

/**
 * AbstractCompleteExampleTest
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
abstract class AbstractCompleteExampleTestCase extends AbstractTestCase
{
    /**
     * @var Post
     */
    private $fullExample;

    /**
     * @var ReflectionClass
     */
    private $definition;
    protected $fieldListIndex = 0;

    public function testListOfFieldsIsComplete()
    {
        $remainder = $this->fullExample;

        foreach ($this->definition->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            // Assert that field exists and has an example.
            $name = $property->getName();
            static::assertArrayHasKey($name, $remainder, 'Missing field');
            static::assertNotNull($remainder[$name], 'Please add a value to the example');
            unset($remainder[$name]);
        }

        // Array should not contain fields that are not in the definition.
        static::assertEquals([], $remainder);
    }

    protected function setUp()
    {
        $targetClassName = $this->getTargetClassName();

        $data = Yaml::parse($this->getYamlFromDocComment($this->fieldListIndex));

        static::assertNotEmpty($data, 'No example found');
        static::assertArrayHasKey($targetClassName, $data, 'No example for class found: ' . $targetClassName);
        static::assertNotEmpty($data[$targetClassName], 'Please add at least one example');

        $this->fullExample = current($data[$targetClassName]);

        static::assertNotEmpty($this->fullExample, 'Please fill example with details');

        $this->definition = new ReflectionClass($targetClassName);

        parent::setUp();
    }

    /**
     * @return string
     */
    abstract protected function getTargetClassName(): string;
}