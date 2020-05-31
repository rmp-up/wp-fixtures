<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DateTimeFormat.php
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

namespace RmpUp\WordPress\Fixtures\Faker\Generator;

use DateTime;
use DateTimeZone;
use Exception;
use Nelmio\Alice\ObjectInterface;

/**
 * DateTimeFormat
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class DateTimeFormat
{
    const DEFAULT_VALUE = '0000-00-00 00:00:00';
    const WP_DB_FORMAT = 'Y-m-d H:i:s';
    /**
     * @var string
     */
    private $fieldName;
    /**
     * @var string
     */
    private $format;
    /**
     * @var string
     */
    private $timezone;

    public function __construct(string $targetField, $format = null, $timezone = null)
    {
        $this->fieldName = $targetField;

        if (null === $format) {
            $format = DateTime::ATOM;
        }

        $this->format = $format;

        if (null === $timezone) {
            $timezone = 'GMT';

            if (date_default_timezone_get()) {
                $timezone = date_default_timezone_get();
            }
        }

        $this->timezone = $timezone;
    }

    /**
     * Sanitize a date-time field
     *
     * @param ObjectInterface $simpleObject Current object bag
     */
    public function __invoke(ObjectInterface $simpleObject)
    {
        $object = $simpleObject->getInstance();

        $currentValue = ($object->{$this->fieldName} ?? null);

        if (null === $currentValue || self::DEFAULT_VALUE === $currentValue) {
            return;
        }

        try {
            switch (true) {
                case is_int($currentValue):
                    $dateTime = new DateTime();
                    $dateTime->setTimestamp($currentValue);
                    break;
                default:
                    $dateTime = new DateTime((string) $currentValue);
            }
        } catch (Exception $e) {
            return;
        }

        $dateTime->setTimezone(new DateTimeZone($this->timezone));

        $object->{$this->fieldName} = $dateTime->format($this->format);
    }
}