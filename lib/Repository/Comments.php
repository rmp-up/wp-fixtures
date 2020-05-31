<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Comments.php
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
 * @package    rmp-up/wp-fixtures
 * @copyright  2020 M. Pretzlaw
 * @license    https://rmp-up.de/license-generic.txt
 * @link       https://rmp-up.de/wp-fixtures
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Repository;

use WP_Comment;

/**
 * Comments
 *
 * @copyright  2020 M. Pretzlaw (https://rmp-up.de)
 */
class Comments implements RepositoryInterface
{
    /**
     * @param WP_Comment $object
     * @param string     $fixtureName
     */
    public function delete($object, string $fixtureName)
    {
        if (!$object->comment_ID) {
            return;
        }

        wp_delete_comment($object->comment_ID, true);
    }

    /**
     * @param WP_Comment  $object
     * @param string|null $fixtureName
     *
     * @return int|void|null
     */
    public function find($object, string $fixtureName = null)
    {
        if ($object->comment_ID) {
            return $object->comment_ID;
        }

        $commentQuery = [
            'author_email' => $object->comment_author_email,
            'fields' => 'ids'
        ];


        if ($object->comment_post_ID) {
            $commentQuery['post_id'] = $object->comment_post_ID;
        }

        $comments = (array) get_comments($commentQuery);

        if (count($comments) === 1) {
            return $comments[0];
        }

        foreach ($comments as $commentId) {
            /** @var WP_Comment $found */
            $found = get_comment($commentId);

            if ($found->comment_content === $object->comment_content) {
                return $found->comment_ID;
            }
        }

        return null;
    }

    /**
     * @param WP_Comment  $object
     * @param string|null $fixtureName
     *
     * @return bool|false|int
     */
    public function persist($object, string $fixtureName = null)
    {
        $commentId = wp_insert_comment($object->to_array());

        if (false === $commentId) {
            throw new PersistException($object, 'Could not store comment ' . $fixtureName);
        }

        $object->comment_ID = $commentId;

        return $commentId;
    }
}
