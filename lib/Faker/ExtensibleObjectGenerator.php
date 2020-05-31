<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ExtensibleHydrator.php
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
use Nelmio\Alice\Generator\GenerationContext;
use Nelmio\Alice\Generator\ObjectGeneratorInterface;
use Nelmio\Alice\Generator\ResolvedFixtureSet;
use Nelmio\Alice\ObjectBag;
use Nelmio\Alice\ObjectInterface;
use ReflectionObject;

/**
 * ExtensibleHydrator
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class ExtensibleObjectGenerator implements ObjectGeneratorInterface
{
    /**
     * @var array
     */
    private $extensions;
    /**
     * @var ObjectGeneratorInterface
     */
    private $proxyTarget;

    /**
     * ExtensibleObjectGenerator constructor.
     *
     * @param ObjectGeneratorInterface $proxyTarget
     * @param array                    $extensions
     */
    public function __construct(ObjectGeneratorInterface $proxyTarget, array $extensions = [])
    {
        $this->proxyTarget = $proxyTarget;
        $this->extensions = $extensions;
    }

    protected function applyExtensions(ObjectInterface $container)
    {
        $instance = $container->getInstance();
        $reflect = new ReflectionObject($instance);

        $classes = array_merge(
            [get_class($instance)],
            class_parents(get_class($instance)),
            $reflect->getTraitNames()
        );

        foreach ($classes as $class) {
            $this->applyForType($class, $container);
        }

        foreach ($reflect->getInterfaceNames() as $interfaceName) {
            $this->applyForType($interfaceName, $container);
        }
    }

    /**
     * @param string          $interfaceName
     * @param ObjectInterface $container
     */
    private function applyForType($interfaceName, ObjectInterface $container)
    {
        if (false === array_key_exists($interfaceName, $this->extensions)) {
            return;
        }

        foreach ($this->extensions[$interfaceName] as $extension) {
            $extension($container);
        }
    }

    public function generate(FixtureInterface $fixture, ResolvedFixtureSet $fixtureSet, GenerationContext $context): ObjectBag
    {
        $objectBag = $this->proxyTarget->generate($fixture, $fixtureSet, $context);

        foreach ($objectBag as $item) {
            $this->applyExtensions($item);
        }

        return $objectBag;
    }
}