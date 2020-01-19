<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbstractCompleteExampleTest.php
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
 * @package    wp-fixtures
 * @copyright  2019 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-12-15
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test;

/**
 * AbstractCompleteExampleTest
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @deprecated 1.0.0 Use AbstractTestCase class or FullExampleTestCase trait instead.
 */
abstract class AbstractAllFieldsTestCase extends AbstractTestCase
{
    protected $fieldListIndex = 0;

    public function testListOfFieldsIsComplete()
    {
        $this->assertExampleContainsAllFields($this->getTargetClassName());
    }

    /**
     * @return string
     */
    abstract protected function getTargetClassName(): string;
}