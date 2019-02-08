<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * PersistExceptionTest.php
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

use Pretzlaw\WordPress\Fixtures\Entity\Post;
use Pretzlaw\WordPress\Fixtures\Repository\PersistException;
use Pretzlaw\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * PersistExceptionTest
 *
 * @internal
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class PersistExceptionTest extends AbstractTestCase
{
    /**
     * @var string
     */
    private $errorMessage;

    protected function setUp()
    {
        parent::setUp();

        $this->errorMessage = uniqid('', true);

        $this->expectExceptionMessage(
            sprintf('Could not persist "%s": %s', Post::class, $this->errorMessage)
        );
    }

    public function testTransformsWpErrorIntoErrorMessage()
    {
        throw new PersistException(new Post(), new \WP_Error(1, $this->errorMessage));
    }

    public function testTransformsException()
    {
        throw new PersistException(new Post(), new \Exception($this->errorMessage));
    }

    public function testReusesSimpleStrings()
    {
        throw new PersistException(new Post(), $this->errorMessage);
    }

    public function testOtherwiseShowsUnknownProblem()
    {
        $this->expectExceptionMessage(
            sprintf('Could not persist "%s": unknown problem', Post::class)
        );

        throw new PersistException(new Post(), new \stdClass());
    }
}