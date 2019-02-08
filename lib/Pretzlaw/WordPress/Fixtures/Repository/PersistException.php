<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ${SHORT}
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
 * @copyright  2018 Mike Pretzlaw
 * @license    https://mike-pretzlaw.de/license-generic.txt
 * @link       https://project.mike-pretzlaw.de/wp-fixtures
 * @since      2019-02-02
 */

declare(strict_types=1);

namespace Pretzlaw\WordPress\Fixtures\Repository;

/**
 * PersistException
 *
 * @copyright  2018 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-02
 */
class PersistException extends \RuntimeException
{
    /**
     * PersistException constructor.
     * @param $target
     * @param \Exception|\WP_Error|string $error
     */
    public function __construct($target, $error = null)
    {
        if ($error instanceof \WP_Error) {
            $error = $error->get_error_message();
        }

        if ($error instanceof \Exception) {
            $error = $error->getMessage();
        }

        if (!is_string($error)) {
            $error = 'unknown problem';
        }

        parent::__construct(
            sprintf('Could not persist "%s": %s', get_class($target), $error)
        );
    }
}