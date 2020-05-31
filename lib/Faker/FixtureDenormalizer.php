<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FixtureDenormalizer.php
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

use Nelmio\Alice\Definition\FlagBag;
use Nelmio\Alice\FixtureBag;
use Nelmio\Alice\FixtureBuilder\Denormalizer\Fixture\FixtureDenormalizerInterface;
use ReflectionClass;
use RmpUp\WordPress\Fixtures\Entity\Option;
use WP_Comment;
use WP_Post;
use WP_Term;

/**
 * FixtureDenormalizer
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class FixtureDenormalizer implements FixtureDenormalizerInterface
{
    /**
     * @var FixtureDenormalizerInterface
     */
    protected $denormalizer;

    /**
     * @var string[]
     */
    protected $prefixes = [
        WP_Comment::class => 'comment_',
        WP_Post::class => 'post_',
        WP_Term::class => 'term_',
    ];

    /**
     * @var ReflectionClass[]
     */
    protected $reflectionCache = [];

    /**
     * @var string[]
     */
    protected $translations = [
        'options' => Option::class,
    ];

    /**
     * FixtureDenormalizer constructor.
     *
     * @param FixtureDenormalizerInterface $denormalizer All cases will be forwarded to this denormalizing.
     */
    public function __construct(FixtureDenormalizerInterface $denormalizer)
    {
        $this->denormalizer = $denormalizer;
    }

    public function denormalize(FixtureBag $builtFixtures, string $className, string $fixtureId, array $specs, FlagBag $flags): FixtureBag
    {
        return $this->denormalizer->denormalize(
            $builtFixtures,
            ($this->translations[$className] ?? $className),
            $fixtureId,
            $this->resolvePrefix($specs, $className),
            $flags
        );
    }

    /**
     * @param array  $specs
     * @param string $className
     *
     * @return array
     * @throws \ReflectionException
     */
    protected function resolvePrefix(array $specs, string $className): array
    {
        if (false === array_key_exists($className, $this->prefixes)) {
            return $specs;
        }

        if (false === array_key_exists($className, $this->reflectionCache)) {
            $this->reflectionCache[$className] = new ReflectionClass($className);
        }

        $reflect = $this->reflectionCache[$className];

        $resolved = $specs;
        foreach ($specs as $name => $value) {
            $prefixedName = $this->prefixes[$className] . $name;

            if (false === $reflect->hasProperty($prefixedName)) {
                continue;
            }

            // Use prefixed name instead of abberviated name
            $resolved[$prefixedName] = $value;
            unset($resolved[$name]);
        }

        return $resolved;
    }
}