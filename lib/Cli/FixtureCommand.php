<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FixtureCommand.php
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
 * @package    rmp-up/wp-fixtures
 * @copyright  2020 M. Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://rmp-up.de/wp-fixtures
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Cli;

use Nelmio\Alice\FileLoaderInterface;
use RmpUp\WordPress\Fixtures\Faker\WordPressFixtureLoader;
use RmpUp\WordPress\Fixtures\Helper\Finder;
use RmpUp\WordPress\Fixtures\Helper\FinderInterface;
use RmpUp\WordPress\Fixtures\Helper\WpCompat;
use RmpUp\WordPress\Fixtures\Repository\RepositoryInterface;
use RmpUp\WordPress\Fixtures\RepositoryFacade;
use RmpUp\WordPress\Fixtures\RepositoryFactory;
use RuntimeException;
use Throwable;
use WP_CLI;

/**
 * Fixture command for wp-cli .
 *
 * @copyright  2020 M. Pretzlaw (https://rmp-up.de)
 */
class FixtureCommand
{
	/**
	 * @var FinderInterface
	 */
	private $finder;

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
    public function __construct(
		FileLoaderInterface $loader = null,
		RepositoryInterface $repo = null,
		FinderInterface $finder = null
	)
	{
		if (null === $loader) {
			$loader = new WordPressFixtureLoader();
		}

		if (null === $repo) {
			$repo = new RepositoryFacade(new RepositoryFactory());
        }

        if (null === $finder) {
        	$finder = new Finder();
		}

        $this->loader = $loader;
        $this->repo = $repo;
        $this->finder = $finder;
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
     * @throws WP_CLI\ExitException
     */
    public function __invoke($yamlFiles, $options)
    {
		WpCompat::check();

        $this->force = $options['force'] ?? false;

        add_filter('user_has_cap', [$this, 'enableAllCapabilities'], 10, 2);

        $key = '';
        try {
            $compiled = [];
            $fixtureFiles = $this->finder->find($yamlFiles);
			WP_CLI::debug(sprintf('Found %d configurations', count($fixtureFiles)));
			foreach ($fixtureFiles as $fixtureFile) {
				$fixtures = $this->loader->loadFile($fixtureFile, [], $compiled);

				WP_CLI::debug(
					sprintf(
						'Found %d new objects in "%s"',
						count($fixtures->getObjects()) - count($compiled),
						$fixtureFile
					)
				);

				$compiled = $fixtures->getObjects();
            }

			foreach ($compiled as $key => $fixture) {
				$this->persist($fixture, $key);
			}
        } catch (Throwable $e) {
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
