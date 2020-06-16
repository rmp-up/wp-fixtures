<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * WordPress.php
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

namespace RmpUp\WordPress\Fixtures\Test;

use Generator;
use RmpUp\Doc\HtmlNode;
use RmpUp\Doc\RegExpBuilder;
use Symfony\Component\Finder\Finder;

/**
 * WordPress
 *
 * First of all we wan't to have a look at the WP built-in entities.
 * In the following documentation you will learn how to generate lots of data
 * for testing purposes
 * or to show your customer different examples how a website could look like.
 *
 * In terms of WordPress you can fill the following entities with data:
 *
 * * Options
 * * WP_Comment
 * * WP_Post
 * * WP_Role
 * * WP_Site
 * * WP_Term
 * * WP_User
 *
 * And they can also be persisted to the database.
 *
 * Furthermore the following data-objects can be filled with life:
 *
 * * WP_Comment_Query
 * * WP_Term_Query
 * * WP_User_Query
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class WordPressTest extends TestCase
{
    /**
     * @var string[]
     */
    private $classMap = [
        'class-feed.php' => 'WP_Feed_Cache',
        'class-walker-page-dropdown.php' => 'Walker_PageDropdown',
        'class-simplepie.php' => 'SimplePie',
        'class-wp-nav-menu-widget.php' => 'WP_Nav_Menu_Widget',
        'class-smtp.php' => 'SMTP',
        'class-IXR.php' => null,
        'class-oembed.php' => null,
    ];

    /**
     * @param HtmlNode $set
     */
    private function assertListIsSorted($set)
    {
        $set = $set->li();
        $current = $set->map('strval')->getArrayCopy();
        $set->natsort();

        static::assertEquals($set->getArrayCopy(), $current);
    }

    /**
     * @param array $coveredClasses
     */
    private function createPattern(array $coveredClasses)
    {
        $pattern = '/^(RmpUp.*';
        foreach (array_keys($coveredClasses) as $coveredName) {
            $coveredName = str_replace('*', '.*', $coveredName);
            $pattern .= '|' . $coveredName;
        }

        return $pattern . ')$/i';
    }

    /**
     * Gather WordPress class-names
     */
    private function fetchWpClasses(): Generator
    {
        $classNameFinder = (new RegExpBuilder())
            ->multiLine()->after("\nclass ")->anything();

        $finalClassNameFinder = (new RegExpBuilder())
            ->multiLine()->after("\nfinal class ")->anything();

        $abstractClassNameFinder = (new RegExpBuilder())
            ->multiLine()->after("\nabstract class ")->anything();

        $finder = new Finder();
        $finder->in([ABSPATH . '/' . WPINC])
            ->name('class-*.php')
            ->files();

        foreach ($finder as $classFile) {
            $fileName = $classFile->getBasename();

            if (array_key_exists($fileName, $this->classMap)) {
                if ($this->classMap[$fileName]) {
                    yield $fileName => $this->classMap[$fileName];
                }

                continue;
            }

            // try to guess
            $possibleClassName = $classNameFinder->getRegExp()->exec($classFile->getContents())[0]
                ?? $finalClassNameFinder->getRegExp()->exec($classFile->getContents())[0]
                ?? $abstractClassNameFinder->getRegExp()->exec($classFile->getContents())[0];

            if (null === $possibleClassName) {
                throw new \RuntimeException('Class name not found for file ' . $fileName);
            }

            // before "extends" or other
            $possibleClassName = strtok($possibleClassName, ' ');

            yield $fileName => $possibleClassName;
        }
    }

    public function testListstAreSorted()
    {
        $this->assertListIsSorted($this->methodComment(__CLASS__, 'testScope')->ul(0));
        $this->assertListIsSorted($this->classComment()->ul(0));
    }

    /**
     * But there are more classes in WP which are either no entity
     * or not covered by wp-fixtures:
     *
     * * IXR_*
     * * PHPMailer
     * * POP3
     * * PasswordHash
     * * Requests
     * * SMTP
     * * Services_JSON
     * * SimplePie
     * * Snoopy
     * * WP
     * * WP_Admin_Bar
     * * WP_Ajax_Response
     * * WP_Block_*
     * * WP_Customize_*
     * * WP_Date_Query
     * * WP_Embed
     * * WP_Error
     * * WP_Fatal_Error_Handler
     * * WP_Feed*
     * * WP_Hook
     * * WP_Http*
     * * WP_Image_*
     * * WP_List_Util
     * * WP_Locale
     * * WP_Locale_Switcher
     * * WP_MatchesMapRegex
     * * WP_Meta_Query
     * * WP_Metadata_Lazyloader
     * * WP_Nav_Menu_Widget
     * * WP_Network
     * * WP_Network_Query
     * * WP_Paused_Extensions_Storage
     * * WP_Post_Type
     * * WP_Query
     * * WP_REST_*
     * * WP_Recovery_*
     * * WP_Rewrite
     * * WP_Roles
     * * WP_Session_Tokens
     * * WP_SimplePie_File
     * * WP_SimplePie_Sanitize_KSES
     * * WP_Site_Query
     * * WP_Tax_Query
     * * WP_Taxonomy
     * * WP_Text_Diff_Renderer_Table
     * * WP_Text_Diff_Renderer_inline
     * * WP_Theme
     * * WP_User_Meta_Session_Tokens
     * * WP_User_Request
     * * WP_Widget
     * * WP_Widget_*
     * * WP_oEmbed
     * * WP_oEmbed_Controller
     * * WP_Object_Cache
     * * Walker
     * * Walker_*
     * * _WP_Dependency
     * * _WP_Editors
     * * wp_xmlrpc_server
     *
     * @internal
     */
    public function testScope()
    {
        $coveredClasses = array_flip(
            array_merge(
                $this->classComment()->ul(0)->li()->all()->map('strval')->getArrayCopy(),
                $this->classComment()->ul(1)->li()->all()->map('strval')->getArrayCopy()
            )
        );

        $ignoredClasses = array_flip(
            $this->comment()->ul(0)->li()->all()->map('strval')->getArrayCopy()
        );

        $allClasses = $this->fetchWpClasses();
        $coveredPattern = $this->createPattern($coveredClasses);
        $ignoredPattern = $this->createPattern($ignoredClasses);

        $missing = [];
        foreach ($allClasses as $fileName => $class) {
            if (preg_match($ignoredPattern, $class) || preg_match($coveredPattern, $class)) {
                continue;
            }

            $missing[] = $class;
        }

        natsort($missing);

        static::assertEquals([], $missing, 'Some classes are not mentioned');
    }

    public function testWordPressLoaded()
    {
        static::assertTrue(defined('ABSPATH') && did_action('init'));
    }
}