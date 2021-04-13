<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FinderTest.php
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

namespace RmpUp\WordPress\Fixtures\Test\Internal;

use org\bovigo\vfs\vfsStream;
use RmpUp\WordPress\Fixtures\Helper\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * FinderTest
 *
 * ```yaml
 * no:
 *   body:
 *     loves:
 *       lawyers.yaml: "nonsense: ~"
 *       banker.yml: "but_they_can_bring_you_money: ~"
 * every:
 *   body:
 *     rock.yaml: "your: body"
 *   thing:
 *     that.yml: "has: a beginning"
 *     has.yaml: "an: end"
 *     mr.yml: "anderson: ~"
 * ```
 *
 * Should find:
 *
 * - vfs://root/no/body/loves/lawyers.yaml
 * - vfs://root/no/body/loves/banker.yml
 * - vfs://root/every/body/rock.yaml
 * - vfs://root/every/thing/that.yml
 * - vfs://root/every/thing/has.yaml
 * - vfs://root/every/thing/mr.yml
 *
 * @copyright 2021 Pretzlaw (https://rmp-up.de)
 * @internal
 */
class FinderTest extends InternalTestCase
{
	/**
	 * @var Finder
	 */
	private $finder;

	/**
	 * @var \org\bovigo\vfs\vfsStreamDirectory
	 */
	private $vfs;

	protected function compatSetUp()
	{
		parent::compatSetUp();

		$structure = Yaml::parse(
			(string) $this->classComment()->code(0, '[@class="yaml"]')
		);

		$this->vfs = vfsStream::setup('root', null, $structure);
		$this->finder = new Finder();
	}

	public function testFindsAllFiles()
	{
		$files = $this->finder->find([$this->vfs->url()]);

		$result = $this->classComment()->li()->map('strval')->getArrayCopy();

		static::assertEquals($result, $files);
	}
}
