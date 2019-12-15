<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * LoaderFactory.php
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
 * @since      2019-12-14
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Faker;

use Faker\Factory;
use Faker\Generator;
use Nelmio\Alice\Loader\NativeLoader;
use RmpUp\WordPress\Fixtures\Faker\WordPress\WpPostProvider;

/**
 * LoaderFactory
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-14
 */
class LoaderFactory
{
    const FAKER_PROVIDER = [
        WpPostProvider::class,
    ];

    /**
     * List of provider class names
     *
     * @var string[]
     */
    private $fakerProvider;
    /**
     * @var string
     */
    private $locale;

    /**
     * LoaderFactory constructor.
     *
     * @param string[]|null $fakerProvider List of classes or null to use default (`::FAKER_PROVIDER`).
     */
    public function __construct(array $fakerProvider = null, string $locale = null)
    {
        if (null === $fakerProvider) {
            $fakerProvider = static::FAKER_PROVIDER;
        }

        $this->fakerProvider = $fakerProvider;

        if (null === $locale) {
            $locale = Factory::DEFAULT_LOCALE;
        }

        $this->locale = $locale;
    }

    /**
     * Data loader with WP specific generator added
     *
     * @return NativeLoader
     */
    public function createNativeLoader(): NativeLoader
    {
        return new NativeLoader($this->createGenerator());
    }

    /**
     * Create new generator with custom provider
     *
     * @return Generator
     */
    public function createGenerator(): Generator
    {
        $generator = Factory::create($this->locale);

        $this->addProvider($generator);

        return $generator;
    }

    /**
     * Add WP provider to existing generator
     *
     * @param Generator $generator Add provider (see __construct) to this Generator.
     */
    public function addProvider(Generator $generator)
    {
        foreach ($this->fakerProvider as $providerClass) {
            $generator->addProvider(new $providerClass($generator));
        }
    }
}