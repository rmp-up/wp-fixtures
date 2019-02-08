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

namespace Pretzlaw\WordPress\Fixtures\Cli;

use Nelmio\Alice\Loader\NativeLoader;
use Pretzlaw\WordPress\Fixtures\Repository\RepositoryInterface;
use Pretzlaw\WordPress\Fixtures\RepositoryFacade;
use Pretzlaw\WordPress\Fixtures\RepositoryFactory;

/**
 * FixtureCommand
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-08
 */
class FixtureCommand extends \WP_CLI
{
    /**
     * @var RepositoryInterface
     */
    private $repo;

    public function __construct()
    {
        $this->loader = new NativeLoader();
        $this->repo = new RepositoryFacade(new RepositoryFactory());
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
    public function __invoke($arguments, $options)
    {
        $options = array_merge(
            [
                'force' => false,
            ],
            $options
        );


        $key = '';
        try {
            $compiled = [];
            foreach ($arguments as $path) {
                $fixtureFiles = $this->fetchFiles($path);
                \WP_CLI::debug(sprintf('Found %d fixtures in "%s"', $fixtureFiles, $path));

                foreach ($fixtureFiles as $fixtureFile) {
                    $fixtures = $this->loader->loadFile($fixtureFile, [], $compiled);

                    foreach ($fixtures->getObjects() as $key => $object) {
                        if (array_key_exists($key, $compiled)) {
                            // Duplicate keys happen because loadFile gets the already compiled
                            continue;
                        }

                        $this->persist($object, $options);
                        $compiled[$key] = $object;
                    }
                }
            }
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if ($key) {
                // Provide key where possible
                $message = $key . ': ' . $message;
            }

            \WP_CLI::error($message);
        }
    }

    private function fetchFiles($path, $prefix = '', $suffix = '.yaml'): array
    {

        if (is_file($path)) {
            if (!preg_match('/^' . preg_quote($prefix, '/') . '.*' . preg_quote($suffix, '/') . '$/', $path)) {
                return [];
            }

            \WP_CLI::debug(sprintf('Found %s', $path));
            return [$path];
        }

        \WP_CLI::debug(sprintf('Searching in %s', $path));

        $files = [];

        if (is_dir($path)) {
            // Files first
            foreach (glob($path . '/' . $prefix . '*' . $suffix) as $singleFile) {
                $files[] = [$singleFile];
            }

            // Recurse in directories
            foreach (glob($path . '/*', GLOB_ONLYDIR) as $subDir) {
                $files[] = $this->fetchFiles($subDir, $prefix, $suffix);
            }
        }

        if (!$files) {
            // Nothing found (standalone because array_merge(...[]) = null => wrong return type)
            return [];
        }

        return array_merge(...$files);
    }

    /**
     * @var NativeLoader
     */
    private $loader;

    private function persist($object, array $options)
    {
        if (!$options['force']) {
            $exists = $this->repo->find($object);

            if (null !== $exists) {
                throw new \RuntimeException(
                    sprintf('Entity already present in the database (ID %d).', $exists)
                );
            }
        }

        $this->repo->persist($object);
    }
}