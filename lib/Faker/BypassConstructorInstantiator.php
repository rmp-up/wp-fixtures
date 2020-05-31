<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WordPressInstantiator.php
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

namespace RmpUp\WordPress\Fixtures\Faker;

use Nelmio\Alice\FixtureInterface;
use Nelmio\Alice\Generator\Instantiator\Chainable\AbstractChainableInstantiator;
use ReflectionClass;

/**
 * WordPressInstantiator
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class BypassConstructorInstantiator extends AbstractChainableInstantiator
{
    /**
     * Class names that shall be instantiated without using the constructor
     *
     * @var callable[] class-names to callables for further manipulation
     */
    protected $classNames = [];

    public function __construct(array $classNameList)
    {
        foreach ($classNameList as $key => $value) {
            if (is_int($key)) {
                $key = $value;
                $value = null;
            }

            $this->addClass($key, $value);
        }
    }

    /**
     * @param string $className
     * @param callable $callback for further processing
     */
    public function addClass(string $className, $callback)
    {
        $this->classNames[$className] = $callback;
    }

    public function canInstantiate(FixtureInterface $fixture): bool
    {
        return array_key_exists($fixture->getClassName(), $this->classNames);
    }

    /**
     * @param FixtureInterface $fixture
     *
     * @return object
     * @throws \ReflectionException
     */
    protected function createInstance(FixtureInterface $fixture)
    {
        $instance = (new ReflectionClass($fixture->getClassName()))->newInstanceWithoutConstructor();

        if (is_callable($this->classNames[$fixture->getClassName()])) {
            $this->classNames[$fixture->getClassName()]($instance);
        }

        return $instance;
    }
}