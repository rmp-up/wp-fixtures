<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * RepositoryFacade.php
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

namespace RmpUp\WordPress\Fixtures;

use RmpUp\WordPress\Fixtures\Repository\RepositoryInterface;

/**
 * RepositoryFacade
 *
 * @copyright  2020 M. Pretzlaw (https://rmp-up.de)
 */
class RepositoryFacade implements RepositoryInterface
{
    /**
     * @var RepositoryFactory
     */
    private $factory;

    public function __construct(RepositoryFactory $factory)
    {
        $this->factory = $factory;
    }

	/**
	 * @param object $object
	 * @param string $fixtureName Name as in the Yaml-Fixture-Config
	 *                            (deprecated, will be removed).
	 */
    public function persist($object, string $fixtureName = '')
    {
        $this->factory->forEntity($object)->persist($object, $fixtureName);
    }

    /**
     * @inheritdoc
     */
    public function find($object, string $fixtureName = '')
    {
        return $this->factory->forEntity($object)->find($object, $fixtureName);
    }

    /**
     * @param object $object
     * @param string $fixtureName Name as in the Yaml-Fixture-Config
	 *                            (deprecated, will be removed)
     */
    public function delete($object, string $fixtureName = '')
    {
        return $this->factory->forEntity($object)->delete($object, $fixtureName);
    }
}
