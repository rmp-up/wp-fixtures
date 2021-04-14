<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * TestAssertions.php
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

namespace RmpUp\WordPress\Fixtures\Test;

trait TestAssertions
{
	protected function assertEntityMatchesDefinition(int $index, string $className, $entityOrName, $expectedClass = null)
	{
		if (null === $expectedClass) {
			$expectedClass = $className;
		}

		if (is_string($entityOrName)) {
			// Warning: This may turn dates into numbers (unix timestamp)
			$data = $this->loadYaml($index, $className, $entityOrName);

			static::assertNotNull($data, sprintf('Failed loading "%s" from %d. YAML', $entityOrName, $index));

			$entityOrName = $this->loadEntities($index, $entityOrName);
		}

		static::assertInstanceOf($expectedClass, $entityOrName);

		foreach ($data as $key => $value) {
			static::assertEquals($entityOrName->$key, $value);
		}
	}
}
