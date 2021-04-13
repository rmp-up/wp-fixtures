<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Loader.php
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
 * @copyright 2021 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Helper;

/**
 * Loader
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 */
class Finder implements FinderInterface
{
	/**
	 * Recursive iterate directory to find files
	 *
	 * Generates a list of files that are placed within a directory
	 * and all the directories below.
	 * We are using a custom implementation here instead of some
	 * third party package to keep the amount of required packages low
	 * and to keep compatibility for very old PHP versions up.
	 *
	 * @param string $path
	 *
	 * @return array
	 */
	private function fetchYamlFiles(string $path): array
	{
		if (is_file($path)) {
			if (false === preg_match('@\.ya?ml$@', $path)) {
				return [];
			}

			return [$path];
		}

		$files = [[]];
		if (is_dir($path)) {
			// Files first
			$dirHandle = opendir($path . '/');

			while ($node = readdir($dirHandle)) {
				if ('.' === $node || '..' === $node) {
					continue;
				}

				$files[] = $this->fetchYamlFiles($path . '/' . $node);
			}
		}

		return array_merge(...$files);
	}

	public function find(array $filesOrDirectories): array
	{
		$found = [[]];
		foreach ($filesOrDirectories as $filesOrDirectory) {
			$found[] = $this->fetchYamlFiles($filesOrDirectory);
		}

		return array_merge(...$found);
	}
}
