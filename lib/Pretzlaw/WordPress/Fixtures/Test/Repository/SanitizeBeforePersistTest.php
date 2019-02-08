<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ValidateBeforePersist.php
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
 * @since      2019-02-03
 */

declare(strict_types=1);

namespace Pretzlaw\WordPress\Fixtures\Test\Repository;

use PHPUnit\Framework\Constraint\IsInstanceOf;
use Pretzlaw\WordPress\Fixtures\Entity\Post;
use Pretzlaw\WordPress\Fixtures\Entity\Sanitizable;
use Pretzlaw\WordPress\Fixtures\Entity\Validatable;
use Pretzlaw\WordPress\Fixtures\Repository\Posts;
use Pretzlaw\WordPress\Fixtures\Repository\RepositoryInterface;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Persist
 *
 * Before persisting an object it should be sanitized.
 * When the repository detects an object of type `Sanitizable`
 * it will call it's `sanitize` method injecting itself
 * for further investigation:
 *
 * ```php
 * use \Pretzlaw\WordPress\Fixtures\Entity\Sanitizable;
 *
 * class SomeThing implements Sanitizable {
 *      public function sanitize(RepositoryInterface $repo): \stdClass
 *      {
 *          // ...
 *
 *          return clone $this;
 *      }
 * }
 * ```
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class SanitizeBeforePersistTest extends AbstractTestCase
{
    public function testSanitize()
    {
        $mock = $this->getMockBuilder(Sanitizable::class)
            ->setMethods(['sanitize'])
            ->getMock();

        $mock->expects($this->once())
            ->method('sanitize')
            ->willThrowException(new \RuntimeException('Just a test'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Just a test');
        $this->posts()->persist($mock);
    }
}