<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * User.php
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

/**
 * User
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-02-03
 */
class User extends \stdClass implements Validatable, Sanitizable
{
    use AbbreviationTrait;

    const ABBREVIATIONS = [];
    const PREFIX = 'user_';

    public $ID;
    public $user_email;
    public $user_login;
    public $user_pass;

    public function validate(string $fixtureName)
    {
        if (isset($this->ID)) {
            throw new InvalidFieldException($this, 'ID', 'Using ID is not allowed due to wp_insert_user');
        }
    }

    /**
     * @param string $fixtureName
     *
     * @return User
     */
    public function sanitize(string $fixtureName)
    {
        $this->applyAbbreviations([static::PREFIX]);
        $this->seed($fixtureName);

        return clone $this;
    }

    /**
     * Seed missing but mandatory data.
     *
     * Note: This needs to run after expandAbbreviations.
     *
     * @see self::expandAbbreviations
     * @param string $fixtureName
     */
    private function seed(string $fixtureName)
    {
        if (empty($this->user_login)) {
            $this->user_login = $fixtureName;

            if (!empty($this->user_email)) {
                $this->user_login = $this->user_email;
            }
        }

        if (empty($this->user_email)) {
            $this->user_email = $fixtureName . '@exmaple.org';
        }

        if (empty($this->user_pass)) {
            $this->user_pass = uniqid('', true);
        }
    }
}
