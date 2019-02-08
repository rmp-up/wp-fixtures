<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * AbbreviationTrait.php
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

namespace Pretzlaw\WordPress\Fixtures\Entity;

/**
 * AbbreviationTrait
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
trait AbbreviationTrait
{
    protected $abbreviations = [];
    /**
     * @param string $prefix
     */
    private function expandPrefix(string $prefix)
    {
        $vars = get_object_vars($this);

        foreach ($vars as $field => $value) {
            $long = $prefix . $field;
            if (!array_key_exists($long, $vars)) {
                continue;
            }

            if ($value && $this->$long && $value !== $this->$long) {
                throw new \RuntimeException(
                    sprintf('Could not change "%s" to "%s": Value would be overwritten', $field, $long)
                );
            }

            // Swap to long name
            $this->$long = $value;
            unset($this->$field);
        }
    }

    protected function applyAbbreviations(array $prefixes = [])
    {
        foreach ($prefixes as $prefix) {
            $this->expandPrefix($prefix);
        }

        $path = [];
        foreach ($this->abbreviations as $source => $target) {
            if (empty($this->$source)) {
                continue;
            }

            // If target is an array it describes a path.
            $destination = $this;
            $target = (array)$target;
            while ($target) {
                $next = array_shift($target);
                $path[] = $next;

                if (is_object($destination)) {
                    if (!isset($destination->$next)) {
                        $destination->$next = null;
                    }

                    $destination =& $destination->$next;
                    continue;
                }

                if (!is_array($destination)) {
                    $destination = [];
                }

                if (!array_key_exists($next, $destination)) {
                    $destination[$next] = null;
                }

                $destination =& $destination[$next];
            }

            if (null !== $destination && !empty($this->$source) && $this->$source !== $destination) {
                throw new \RuntimeException(
                    sprintf('Could not change "%s" to "%s": Value would be overwritten', $source, implode('.', $path))
                );
            }

            $destination = $this->$source;
            unset($this->$source, $destination);
        }
    }

    abstract public function sanitize();
}