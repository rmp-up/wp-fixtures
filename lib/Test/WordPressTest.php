<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WordPressTest.php
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

use Nelmio\Alice\ObjectSet;
use Pretzlaw\PHPUnit\DocGen\DocComment\Parser;
use ReflectionClass;
use stdClass;

/**
 * Basic WordPress entities
 *
 * Faker brings functions to generate numbers, text, names, dates and so on:
 *
 * * `<text()>` produces some text
 * * `<firstName()>` generates some first name
 * * `<numberBetween(1, 200)>` is an obvious example
 *
 * For WordPress we bring similar things:
 *
 * * `<wpPost()>` creates a `WP_Post` with random data (mostly)
 *
 * Read on to learn how to specify entities in detail like ...
 *
 * * `\RmpUp\WordPress\Fixtures\Entity\Comment` for adding comments
 * * `\RmpUp\WordPress\Fixtures\Entity\Option` to set options for the site
 * * `\RmpUp\WordPress\Fixtures\Entity\Page` to create pages
 * * `\RmpUp\WordPress\Fixtures\Entity\Post` to define posts
 * * `\RmpUp\WordPress\Fixtures\Entity\User` to add users
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class WordPressTest extends AbstractTestCase
{
    use Parser;

    public function testAllEntitiesListed()
    {
        $remainder = array_flip($this->fetchEntityList()); // to easier check and remove classes
        static::assertNotEmpty($remainder, 'Could not fetch list of entities');

        foreach ($this->classComment()->xpath('//ul[3]/li/code/text()') as $classNameNode) {
            $mentionedClass = ltrim((string) $classNameNode, '\\');

            static::assertTrue(class_exists($mentionedClass), 'Class does not exist: ' . $mentionedClass);
            static::assertArrayHasKey($mentionedClass, $remainder, 'Possibly named class to often: ' . $mentionedClass);

            unset($remainder[$mentionedClass]);
        }

        static::assertEquals([], $remainder, 'Missed those entities: ' . implode(', ', array_keys($remainder)));
    }

    /**
     * @return array
     */
    protected function fetchEntityList(string $namespaceInfix = null): array
    {
        $basePath = $this->fullPath('Entity') . '/';
        $namespace = 'RmpUp\\WordPress\\Fixtures\\Entity\\';

        if (null !== $namespaceInfix) {
            $basePath .= trim(str_replace('\\', '/', $namespaceInfix), '/') . '/';
            $namespace .= trim($namespaceInfix, '\\') . '\\';
        }

        $files = glob($basePath . '*.php', GLOB_NOSORT);

        $completeList = [];
        foreach ($files as $classFile) {
            $className = $namespace . basename($classFile, '.php');

            if (1 === preg_match('/Trait$/', $className)) {
                continue;
            }

            $ref = new ReflectionClass($className);

            if ($ref->isInterface()
                || $ref->isAbstract()
                || false !== strpos((string) $ref->getDocComment(), "\n * @internal\n")) {
                // Skip non-public helper classes
                continue;
            }

            $completeList[$classFile] = $className;
        }

        return $completeList;
    }

    public function testValidProviderList()
    {
        foreach ($this->classComment()->xpath('//ul[2]/li') as $li) {
            list($providerName, $className) = $li->xpath('code');

            $providerName = html_entity_decode(strip_tags($providerName->asXML()));
            $className = strip_tags($className->asXML());

            $allProvider = $this->alice()->getFakerGenerator()->getProviders();
            $data = $this->alice()->loadData(
                [
                    stdClass::class => [
                        'foo' => [
                            'bar' => $providerName
                        ],
                    ]
                ]
            );

            static::assertInstanceOf(ObjectSet::class, $data, 'Alice could not parse data');
            $compiled = $data->getObjects();

            static::assertArrayHasKey('foo', $compiled, 'Could not create object');
            static::assertInstanceOf($className, $compiled['foo']->bar);
        }
    }
}