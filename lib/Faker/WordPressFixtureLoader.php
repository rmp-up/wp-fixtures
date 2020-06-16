<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WordPressFixtureLoader.php
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

use Faker\Generator as FakerGenerator;
use Nelmio\Alice\FixtureBuilder\Denormalizer\Fixture\FixtureDenormalizerInterface;
use Nelmio\Alice\Generator\Instantiator\Chainable\NoCallerMethodCallInstantiator;
use Nelmio\Alice\Generator\Instantiator\Chainable\NoMethodCallInstantiator;
use Nelmio\Alice\Generator\Instantiator\Chainable\NullConstructorInstantiator;
use Nelmio\Alice\Generator\Instantiator\Chainable\StaticFactoryInstantiator;
use Nelmio\Alice\Generator\Instantiator\ExistingInstanceInstantiator;
use Nelmio\Alice\Generator\Instantiator\InstantiatorRegistry;
use Nelmio\Alice\Generator\Instantiator\InstantiatorResolver;
use Nelmio\Alice\Generator\InstantiatorInterface;
use Nelmio\Alice\Generator\ObjectGeneratorInterface;
use Nelmio\Alice\Loader\NativeLoader;
use RmpUp\WordPress\Fixtures\Faker\Generator\AutoFillIdIntoField;
use RmpUp\WordPress\Fixtures\Faker\Generator\DateTimeFormat;
use RmpUp\WordPress\Fixtures\Faker\Generator\ExpandPrefix;
use RmpUp\WordPress\Fixtures\Faker\Generator\ReduceToReference;
use RmpUp\WordPress\Fixtures\Faker\Instantiator\AssertPropertiesAreAccessible;
use RmpUp\WordPress\Fixtures\Faker\WordPress\WpPostProvider;
use RmpUp\WordPress\Fixtures\Faker\WordPress\WpUserProvider;
use stdClass;
use WP_Comment;
use WP_Object_Cache;
use WP_Post;
use WP_Role;
use WP_Site;
use WP_Term;
use WP_User;

/**
 * WordPressFixtureLoader
 *
 * @method BypassConstructorInstantiator getBypassConstructorInstantiator()
 * @method array getObjectGeneratorExtensions()
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class WordPressFixtureLoader extends NativeLoader
{
    public function __construct(FakerGenerator $fakerGenerator = null)
    {
        parent::__construct($fakerGenerator);

        $generator = $this->getFakerGenerator();

        $generator->addProvider(new WpUserProvider($generator));
        $generator->addProvider(new WpPostProvider($generator));
    }

    /**
     * @return BypassConstructorInstantiator
     *
     * @see getBypassConstructorInstantiator
     */
    protected function createBypassConstructorInstantiator(): BypassConstructorInstantiator
    {
        return new BypassConstructorInstantiator(
            [
                WP_Comment::class,
                WP_Object_Cache::class => [
                    new AssertPropertiesAreAccessible([
                        'blog_prefix',
                        'global_groups',
                        'multisite',
                    ])
                ],
                WP_Post::class => static function ($instance) {
                    $instance->tax_input = [];
                },
                WP_Role::class,
                WP_Site::class,
                WP_Term::class,
                WP_User::class => static function ($instance) {
                    $instance->data = new stdClass();
                },
            ]
        );
    }

    /**
     * Override denormalizing to allow aliases for entities
     *
     * @return FixtureDenormalizerInterface
     */
    protected function createFixtureDenormalizer(): FixtureDenormalizerInterface
    {
        return new FixtureDenormalizer(parent::createFixtureDenormalizer());
    }

    protected function createInstantiator(): InstantiatorInterface
    {
        return new ExistingInstanceInstantiator(
            new InstantiatorResolver(
                new InstantiatorRegistry([
                    $this->getBypassConstructorInstantiator(),
                    new NoCallerMethodCallInstantiator(),
                    new NullConstructorInstantiator(),
                    new NoMethodCallInstantiator(),
                    new StaticFactoryInstantiator(),
                ])
            )
        );
    }

    protected function createObjectGenerator(): ObjectGeneratorInterface
    {
        return new ExtensibleObjectGenerator(parent::createObjectGenerator(), $this->getObjectGeneratorExtensions());
    }

    protected function createObjectGeneratorExtensions(): array
    {
        return [
            WP_Comment::class => [
                new ExpandPrefix('comment_'),
                new DateTimeFormat('comment_date_gmt', DateTimeFormat::WP_DB_FORMAT, 'GMT'),
                new DateTimeFormat(
                    'comment_date',
                    DateTimeFormat::WP_DB_FORMAT,
                    get_option('timezone_string') ?: null
                ),
                new ReduceToReference('comment_post_ID', [WP_Post::class => 'ID']),
            ],
            WP_Post::class => [
                new ExpandPrefix('post_'),
                new DateTimeFormat('post_date_gmt', DateTimeFormat::WP_DB_FORMAT, 'GMT'),
                new DateTimeFormat(
                    'post_date',
                    DateTimeFormat::WP_DB_FORMAT,
                    get_option('timezone_string') ?: null
                ),
                new ReduceToReference('post_author', [WP_User::class => 'ID']),
            ],
            WP_Role::class => [new AutoFillIdIntoField('name')],
        ];
    }
}