<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * ProviderTest.php
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

use Nelmio\Alice\ObjectSet;
use Pretzlaw\PHPUnit\DocGen\DocComment\Parser;
use stdClass;

/**
 * Random fake data / Provider
 *
 * Faker brings functions to generate numbers, text, names, dates and so on:
 *
 * * `<text()>` produces some text
 * * `<firstName()>` generates some first name
 * * `<numberBetween(1, 200)>` is an obvious example
 *
 * For WordPress we bring similar things:
 *
 * * `<wpPost()>` creates a `WP_Post` with random data (mostly)
 *
 *
 * @copyright  2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 * @since      2019-12-15
 */
class ProviderTest extends AbstractTestCase
{
    use Parser;

    public function testValidList()
    {
        foreach ($this->classComment()->xpath('//ul[2]/li') as $li) {
            list($providerName, $className) = $li->xpath('code');

            $providerName = html_entity_decode(strip_tags($providerName->asXML()));
            $className = strip_tags($className->asXML());

            $allProvider = $this->alice()->getFakerGenerator()->getProviders();
            $data = $this->alice()->loadData(
                [
                    stdClass::class => [
                        'foo' => [
                            'bar' => $providerName
                        ],
                    ]
                ]
            );

            static::assertInstanceOf(ObjectSet::class, $data, 'Alice could not parse data');
            $compiled = $data->getObjects();

            static::assertArrayHasKey('foo', $compiled, 'Could not create object');
            static::assertInstanceOf($className, $compiled['foo']->bar);
        }
    }
}