<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * InvalidFieldException.php
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

namespace RmpUp\WordPress\Fixtures\Entity;

use Throwable;

/**
 * InvalidFieldException
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class InvalidFieldException extends \DomainException
{
    public function __construct($object, string $field, $addMessage = null)
    {
        $message = sprintf('Field "%s" or "%s" is invalid', $field, get_class($object));

        if (null !== $addMessage) {
            $message .= ': ' . $addMessage;
        }

        parent::__construct($message);
    }
}
