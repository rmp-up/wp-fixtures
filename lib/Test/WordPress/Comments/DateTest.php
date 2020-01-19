<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * DateTest.php
 *
 * LICENSE: This source file is created by the company around Mike Pretzlaw
 * located in Germany also known as rmp-up. All its contents are proprietary
 * and under german copyright law. Consider this file as closed source and/or
 * without the permission to reuse or modify its contents.
 * This license is available through the world-wide-web at the following URI:
 * https://rmp-up.de/license-generic.txt . If you did not receive a copy
 * of the license and are unable to obtain it through the web, please send a
 * note to mail@rmp-up.de so we can mail you a copy.
 *
 * @package    wp-fixtures
 * @copyright  2020 Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @since      2020-01-20
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Test\WordPress\Comments;

use DateTime;
use RmpUp\WordPress\Fixtures\Entity\Comment;
use RmpUp\WordPress\Fixtures\Test\AbstractTestCase;

/**
 * Using fake data
 *
 * As with most entites comments can also can make use of fake data
 * that is randomly generated like this:
 *
 * ```yaml
 * RmpUp\WordPress\Fixtures\Entity\Comment:
 *   the_fake_comment:
 *     content: "What year is it?"
 *     date: <dateTimeBetween('2010-04-13 00:54:24', 'now')>
 *     author_email: <safeEmail()>
 *     author_url: https://<safeEmailDomain()>
 * ```
 *
 * @copyright  2020 Pretzlaw (https://rmp-up.de)
 */
class DateTest extends AbstractTestCase
{
    /**
     * @var Comment
     */
    private $comment;

    protected function setUp()
    {
        $this->comment = $this->loadFromDocComment(0, 'the_fake_comment');

        parent::setUp();
    }

    public function testHasForeignObjectsAttached()
    {
        static::assertInstanceOf(DateTime::class, $this->comment->date);
    }

    public function testSanitzerReducesObject()
    {
        static::assertInstanceOf(DateTime::class, $this->comment->date);
        $expectedDate = $this->comment->date->format('Y-m-d\TH:i:sP');

        $this->comment->sanitize('');

        static::assertEquals($expectedDate, $this->comment->date);
    }
}