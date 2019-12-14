<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FixtureCommand.php
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
 * @package    pretzlaw/wp-fixtures
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/pretzlaw/wp-fixtures
 * @since      2019-02-08
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Cli;

use Exception;
use Nelmio\Alice\FileLoaderInterface;
use Nelmio\Alice\Loader\NativeLoader;
use RmpUp\WordPress\Fixtures\Repository\RepositoryInterface;
use RmpUp\WordPress\Fixtures\RepositoryFacade;
use RmpUp\WordPress\Fixtures\RepositoryFactory;
use RuntimeException;
use WP_CLI;

/**
 * Fixture command for wp-cli .
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-08
 */
class FixtureCommand
{
    /**
     * Force overwriting existing data (in case of collisions)
     *
     * @var bool
     */
    private $force = false;

    /**
     * @var FileLoaderInterface
     */
    private $loader;

    /**
     * @var RepositoryInterface
     */
    private $repo;

    /**
     * Creating a new FixtureCommand
     *
     * @param FileLoaderInterface|null $loader Usually the \Nelmio\Alice\Loader\NativeLoader .
     * @param RepositoryInterface|null $repo   Taking care of read and write (in WordPress).
     */
    public function __construct(FileLoaderInterface $loader = null, RepositoryInterface $repo = null)
    {
        if (null === $loader) {
            $loader = new NativeLoader();
        }

        if (null === $repo) {
            $repo = new RepositoryFacade(new RepositoryFactory());
        }

        $this->loader = $loader;
        $this->repo = $repo;
    }

    /**
     * Assert fake contents in database
     *
     * [--force]
     * : Force overwriting existing data (in case of collisions).
     *
     * <targets>...
     * : One or more files/directories to look for *.yaml files.
     *
     */
    public function __invoke($yamlFiles, $options)
    {
        $this->force = $options['force'] ?? false;

        add_filter('user_has_cap', [$this, 'enableAllCapabilities'], 10, 2);

        $key = '';
        try {
            $compiled = [];
            foreach ($yamlFiles as $path) {
                $fixtureFiles = $this->fetchFiles($path);
                WP_CLI::debug(sprintf('Found %d configurations in "%s"', count($fixtureFiles), $path));

                foreach ($fixtureFiles as $fixtureFile) {
                    $fixtures = $this->loader->loadFile($fixtureFile, [], $compiled);
                    WP_CLI::debug(
                        sprintf(
                            'Found %d new objects in %s',
                            count($fixtures->getObjects()) - count($compiled),
                            $fixtureFile
                        )
                    );

                    foreach ($fixtures->getObjects() as $key => $object) {
                        if (array_key_exists($key, $compiled)) {
                            // Duplicate keys happen because loadFile gets the already compiled
                            continue;
                        }

                        $this->persist($object, $key);
                        $compiled[$key] = $object;
                    }
                }
            }
        } catch (Exception $e) {
            $message = $e->getMessage();

            if ($key) {
                // Provide key where possible
                $message = $key . ': ' . $message;
            }

            WP_CLI::error($message);
        }
    }

    public function enableAllCapabilities($capabilities, $caps)
    {
        foreach ($caps as $cap) {
            // allow all.
            $capabilities[$cap] = true;
        }

        return $capabilities;
    }


    private function fetchFiles($path, $prefix = '', $suffix = '.yaml'): array
    {
        if (is_file($path)) {
            if (!preg_match('/^' . preg_quote($prefix, '/') . '.*' . preg_quote($suffix, '/') . '$/', $path)) {
                return [];
            }

            WP_CLI::debug(sprintf('Found %s', $path));
            return [$path];
        }

        WP_CLI::debug(sprintf('Searching in %s', $path));

        $files = [];

        if (is_dir($path)) {
            // Files first
            foreach ((array) glob($path . '/' . $prefix . '*' . $suffix, GLOB_NOSORT) as $singleFile) {
                $files[] = [$singleFile];
            }

            // Recurse in directories
            foreach ((array) glob($path . '/*', GLOB_ONLYDIR) as $subDir) {
                $files[] = $this->fetchFiles($subDir, $prefix, $suffix);
            }
        }

        if (!$files) {
            // Nothing found (standalone because array_merge(...[]) = null => wrong return type)
            return [];
        }

        return array_merge(...$files);
    }

    private function persist($object, string $fixtureName)
    {
        if (!$this->force) {
            $exists = $this->repo->find($object, $fixtureName);

            if (null !== $exists) {
                throw new RuntimeException(
                    sprintf('Entity already present in the database (ID %d).', $exists)
                );
            }
        }

        $this->repo->persist($object, $fixtureName);
    }
}
