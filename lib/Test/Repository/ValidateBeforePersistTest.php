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

namespace RmpUp\WordPress\Fixtures\Test\Repository;

use PHPUnit\Framework\Constraint\IsInstanceOf;
use RmpUp\WordPress\Fixtures\Entity\Post;
use RmpUp\WordPress\Fixtures\Entity\Validatable;
use RmpUp\WordPress\Fixtures\Repository\Posts;
use RmpUp\WordPress\Fixtures\Repository\RepositoryInterface;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Validate and sanitize
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class ValidateBeforePersistTest extends AbstractTestCase
{
    public function testValidate()
    {
        $mock = $this->getMockBuilder(Validatable::class)
            ->setMethods(['validate'])
            ->getMock();

        $mock->expects($this->once())
            ->method('validate')
            ->willThrowException(new \RuntimeException('Just a test'));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Just a test');
        $this->posts()->persist($mock, '');
    }
}