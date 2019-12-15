<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ${SHORT}
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
 * @copyright  2018 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-02-02
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test;

use Nelmio\Alice\Loader\NativeLoader;
use PHPUnit\Framework\TestCase;
use RmpUp\WordPress\Fixtures\Entity\Post;
use RmpUp\WordPress\Fixtures\Entity\User;
use RmpUp\WordPress\Fixtures\Repository\Posts;
use RmpUp\WordPress\Fixtures\Repository\Users;
use RmpUp\WordPress\Fixtures\RepositoryFacade;
use RmpUp\WordPress\Fixtures\RepositoryFactory;
use Symfony\Component\Yaml\Yaml;

/**
 * AbstractTestCase
 *
 * @copyright  2018 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-02
 */
abstract class AbstractTestCase extends TestCase
{
    /**
     * @var NativeLoader
     */
    private $alice;

    /**
     * @var Posts
     */
    private $posts;

    /**
     * @var Users
     */
    private $users;

    protected $fixtures = [];
    protected $compiledFixtures;

    protected function setUp()
    {
        parent::setUp();

        $this->posts = new Posts();
    }

    /**
     * @param string $fixtureKey
     * @param User $expected
     */
    protected function assertFixtureObject($expected, string $fixtureKey)
    {
        $message = $fixtureKey . ' invalid';

        static::assertIsArray($this->fixtures[get_class($expected)][$fixtureKey], $message);

        $fixtureData = $this->fixtures[get_class($expected)][$fixtureKey];
        foreach (get_object_vars($expected) as $field => $expectedValue) {
            static::assertArrayHasKey($field, $fixtureData, $message);
            static::assertEquals($expectedValue, $fixtureData[$field], $message);
        }
    }

    /**
     * @param string $fixtureKey
     * @param \stdClass $actual
     * @param null $class
     */
    protected function assertFixtureValues(string $fixtureKey, $actual, $class = null)
    {
        if (null === $class) {
            $class = get_class($actual);
        }

        static::assertArrayHasKey($class, $this->fixtures);
        static::assertArrayHasKey($fixtureKey, $this->fixtures[$class]);

        $message = $fixtureKey . ' invalid';

        foreach ($this->fixtures[$class][$fixtureKey] as $field => $expectedValue) {
            static::assertNotEmpty($actual->$field, $message);
            static::assertEquals($expectedValue, $actual->$field, $message);
        }
    }

    /**
     * @param string $key
     * @return \stdClass
     */
    protected function fixture(string $key)
    {
        if (null === $this->compiledFixtures) {
            $this->compiledFixtures = $this->alice()->loadData($this->fixtures)->getObjects();
        }

        if (!array_key_exists($key, $this->compiledFixtures)) {
            throw new \RuntimeException(
                sprintf('Fixture "%s" is not defined.', $key)
            );
        }

        return $this->compiledFixtures[$key];
    }

    protected function posts(): Posts
    {
        if (!$this->posts) {
            $this->posts = new Posts();
        }

        return $this->posts;
    }

    protected function alice()
    {
        if (false === $this->alice instanceof NativeLoader) {
            $this->alice = new NativeLoader();
        }

        return $this->alice;
    }

    protected function users(): Users
    {
        if (null === $this->users) {
            $this->users = new Users();
        }

        return $this->users;
    }

    /**
     * @return \Nelmio\Alice\ObjectSet|Post[]
     */
    public function postsValidUnique()
    {
        return $this->alice()->loadData(
            [
                Post::class => [
                    'post_1' => [
                        'post_title' => uniqid('Title ', true),
                        'post_content' => uniqid('Content ', true)
                    ]
                ]
            ]
        )->getObjects();
    }

    protected function objectsToDataProvider($objects)
    {
        foreach ($objects as $key => $value) {
            yield $key => [$value];
        }
    }

    protected function fixtureData(string $index)
    {
        foreach ($this->fixtures as $fixture) {
            if (array_key_exists($index, $fixture)) {
                return $fixture[$index];
            }
        }

        throw new \RuntimeException('Could not find fixture: ' . $index);
    }

    /**
     * @param int $int
     * @return array|\stdClass
     */
    protected function loadFromDocComment(int $int = 0, $key = null)
    {
        try {
            $objects = $this->alice()->loadData(Yaml::parse($this->getYamlFromDocComment($int)))->getObjects();

            if (null === $key) {
                return $objects;
            }

            if (!array_key_exists($key, $objects)) {
                return null;
            }

            return $objects[$key];
        } catch (\ReflectionException $e) {
            return [];
        }
    }

    /**
     * @param int $index
     * @return string
     * @throws \ReflectionException
     */
    protected function getYamlFromDocComment(int $index): string
    {
        $class = new \ReflectionObject($this);
        $docComment = $class->getDocComment();

        if ($index < 0) {
            // From method
            $index = abs($index);
            $docComment = $class->getMethod($this->getName())->getDocComment();
        }

        $matches = [];
        preg_match_all('/(?<=\`\`\`yaml)[^`]*(?=\`\`\`)/su', $docComment, $matches, PREG_SET_ORDER);

        foreach ($matches as $key => $match) {
            if ($key === $index) {
                return trim(preg_replace('/^\s\*\s/mu', '', reset($match)));
            }
        }

        return '';
    }

    protected function query(string $query, array $params = []): array
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        if ($params) {
            $query = $wpdb->prepare($query, $params);
        }

        return (array) $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * WordPress Database connection
     *
     * @return \wpdb
     */
    protected function wpdb(): \wpdb
    {
        /** @var \wpdb $wpdb */
        global $wpdb;

        return $wpdb;
    }

    private $factory;

    public function factory()
    {
        if (null === $this->factory) {
            $this->factory = new RepositoryFactory();
        }

        return $this->factory;
    }

    private $repoFacade;

    /**
     * @return RepositoryFacade
     */
    public function repo()
    {
        if (null === $this->repoFacade) {
            $this->repoFacade = new RepositoryFacade($this->factory());
        }

        return $this->repoFacade;
    }
}
