<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * FakerException.php
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
 * @copyright 2020 Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures;

use Exception;
use RuntimeException;
use WP_Error;

/**
 * FakerException
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class FixturesException extends RuntimeException
{
    /**
     * PersistException constructor.
     *
     * @param object                    $entity
     * @param Exception|WP_Error|string $error
     */
    public function __construct($entity = null, $error = null)
    {
        if ($error instanceof WP_Error) {
            $error = $error->get_error_message();
        }

        if ($error instanceof Exception) {
            $error = $error->getMessage();
        }

        if (false === is_string($error)) {
            $error = 'unknown problem';
        }

        $targetDescription = 'wp-fixtures';
        if ($entity) {
            $targetDescription = gettype($entity);
            if (is_object($entity)) {
                $targetDescription = get_class($entity);
            }
        }

        parent::__construct(sprintf('"%s": %s', $targetDescription, $error));
    }
}