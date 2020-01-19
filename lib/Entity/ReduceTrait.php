<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ReduceTrait.php
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
 * @package   wp-fixtures
 * @copyright 2019 Mike Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 * @link      https://rmp-up.de/wp-fixtures
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Entity;

/**
 * Reduce references to primitives
 *
 * @copyright 2020 Mike Pretzlaw (https://mike-pretzlaw.de)
 */
trait ReduceTrait
{
    /**
     * Reduce fields to other primitives
     *
     * Example:
     *
     * ```
     * ::reduce(
     *   [
     *     'someField' => [
     *       SomeEntity::class => 'foreignField',
     *       Post::class => 'ID',
     *     ],
     *   ]
     * );
     * ```
     *
     * When "someField" is type of "SomeEntity" then it will get the value of "foreignField",
     * which is like `$this->someField = $this->someField->foreignField`.
     * When it is a post then it will be reduced to the ID.
     *
     * @param array $map Mapping of field to type to counterpart as described in the example.
     */
    public function reduce($map)
    {
        foreach ($map as $fieldName => $typeToStrategy) {
            $currentValue = $this->{$fieldName};
            if (false === is_object($currentValue)) {
                continue;
            }

            $this->{$fieldName} = $this->reduceByType($this->{$fieldName}, $typeToStrategy);
        }
    }

    /**
     * Mapping from type to reduction
     *
     * @param mixed $value
     * @param array $typeToStrategy
     *
     * @return mixed
     */
    protected function reduceByType($value, $typeToStrategy)
    {
        foreach ($typeToStrategy as $foreignType => $foreignField) {
            if (false === $value instanceof $foreignType) {
                continue;
            }

            if ($foreignField instanceof \Closure) {
                return $foreignField($value);
            }

            return $value->{$foreignField};
        }

        return $value;
    }
}