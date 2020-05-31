<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FixtureLoadTrait.php
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

namespace RmpUp\WordPress\Fixtures\Helper;

use ReflectionClass;

/**
 * FixtureLoadTrait
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 *
 * @method Fixtures fixtures() from FixturesTrait
 * @method string getName() from PHPUnit TestCase class
 */
trait FixturesAutoloadTrait
{
    /**
     * @var string
     */
    private $fixtureBasePath;

    /**
     * Fixtures as described in YAML files
     *
     * @var object[]
     */
    protected $fixtures = [];

    /**
     * @var string
     */
    protected $fixturesSeparator = '.';

    /**
     * @before
     * @throws \ReflectionException
     */
    public function loadFixtures()
    {
        if (null === $this->fixtureBasePath) {
            $classFile = (new ReflectionClass(get_class($this)))->getFileName();
            $this->fixtureBasePath = dirname($classFile) . DIRECTORY_SEPARATOR . basename($classFile, '.php');
        }

        $filesToLoad = [];

        $boundary = getcwd();
        $currentDir = $this->fixtureBasePath;
        do {
            $currentDir = dirname($currentDir);
            $filesToLoad[] = $currentDir . '/fixtures.yaml';
        } while ($boundary < $currentDir);

        $filesToLoad = array_reverse($filesToLoad); // load uppermost directory first and then go deep
        $filesToLoad[] = $this->fixtureBasePath . '.yaml'; // class fixtures
        $filesToLoad[] = $this->fixtureBasePath . $this->fixturesSeparator . $this->getName() . '.yaml';

        $this->fixtures = $this->fixtures()->load(array_filter($filesToLoad, 'is_file'))->getObjects();
    }
}