<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ${SHORT}
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
 * @package    wp-fixtures
 * @copyright  2020 M. Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://project.rmp-up.de/wp-fixtures
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Repository;

/**
 * RepositoryInterface
 *
 * @copyright  2020 M. Pretzlaw (https://rmp-up.de)
 */
interface RepositoryInterface
{
    /**
     * @param object $object Fixture data.
     * @param string $fixtureName Key as provided in fixture config
	 *                            (deprecated, will be removed)
     */
    public function persist($object, string $fixtureName = '');

    /**
     * @param object $object Fixture to lookup.
     * @param string $fixtureName Name of the fixture
	 *                            (deprecated, will be removed)
	 *
     * @return mixed|null ID or null when not found
     */
    public function find($object, string $fixtureName = '');

    /**
     * @param mixed $object
     * @param string $fixtureName Name of the fixture
	 *                            (deprecated, will be removed)
     */
    public function delete($object, string $fixtureName = '');
}
