<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Comments.php
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
 * @package   pretzlaw/wp-fixtures
 * @copyright 2019 Mike Pretzlaw
 * @license   https://rmp-up.de/license-generic.txt
 * @link      https://rmp-up.de/wp-fixtures
 */

declare(strict_types=1);

namespace RmpUp\WordPress\Fixtures\Repository;

use RmpUp\WordPress\Fixtures\Entity\Comment;
use WP_Comment;
use wpdb;

/**
 * Comments
 *
 * @copyright 2019 Mike Pretzlaw (https://mike-pretzlaw.de)
 */
class Comments implements RepositoryInterface
{
    /**
     * @param Comment $object
     * @param string  $fixtureName
     */
    protected function create($object, string $fixtureName)
    {
        $object->id = wp_insert_comment($this->toArray($object));

        if (false === $object->id) {
            throw new \RuntimeException('Could not insert comment ' . $fixtureName);
        }

        update_comment_meta($object->id, static::FIXTURE_NAME_META, $fixtureName);
    }

    /**
     * Find a fixture by its name
     *
     * @param string $fixtureName Name of the fixture
     *
     * @return \WP_Comment|null
     */
    private function findByName(string $fixtureName)
    {
        $comments = get_comments(
            [
                'meta_key' => static::FIXTURE_NAME_META,
                'meta_value' => $fixtureName,
                'number' => 1,
            ]
        );

        if (false === is_array($comments) || false === $comments[0] instanceof WP_Comment) {
            return null;
        }

        return $comments[0];
    }

    /**
     * @param Comment     $object
     * @param string|null $fixtureName
     */
    public function persist($object, string $fixtureName = null)
    {
        $object->sanitize((string) $fixtureName);

        // Create when not exists already
        $existing = $this->find($object, $fixtureName);
        $fixtureId = $object->id;
        if (null === $object->id || null === $existing) {
            // Does not exist already under given ID
            $this->create($object, (string) $fixtureName);
        }

        if (null !== $fixtureId && $fixtureId !== $object->id) {
            // Newly created but under different ID which need to be corrected
            $this->wpdb()->update($this->wpdb()->comments, ['comment_ID' => $fixtureId], ['comment_ID' => $object->id]);
            $object->id = $fixtureId;
        }

        if (null !== $existing) {
            // Did exist before so nothing happened so far but this update will write the fixture.
            $this->update($object, (string) $fixtureName);
        }
    }

    /**
     * @param Comment     $object
     * @param string|null $fixtureName
     *
     * @return int|null
     */
    public function find($object, string $fixtureName = null)
    {
        if (null !== $object->id && get_comment($object->id) instanceof WP_Comment) {
            return (int) $object->id;
        }

        if (null !== $fixtureName) {
            $comment = $this->findByName($fixtureName);
            if (null !== $comment) {
                return (int) $comment->comment_ID;
            }
        }

        return null;
    }

    /**
     *
     * @param Comment $object
     * @param string   $fixtureName
     */
    public function delete($object, string $fixtureName)
    {
        $id = $this->find($object);

        if (null === $id) {
            return;
        }

        wp_delete_comment($id);
    }

    /**
     * @param Comment $comment
     *
     * @return array
     */
    protected function toArray(Comment $comment): array
    {
        return [
            'comment_ID' => $comment->id,
            'comment_author' => $comment->author,
            'comment_author_email' => $comment->author_email,
            'comment_author_IP' => $comment->author_ip,
            'comment_author_url' => $comment->author_url,
            'comment_content' => $comment->content,
            'comment_date' => $comment->date,
            'comment_approved' => $comment->approved,
            'comment_post_ID' => $comment->post,
        ];
    }

    /**
     * @param Comment $object
     * @param string  $fixtureName
     */
    protected function update($object, string $fixtureName)
    {
        wp_update_comment($this->toArray($object));
        update_comment_meta($object->id, static::FIXTURE_NAME_META, $fixtureName);
    }

    /**
     * DB helper
     *
     * @return \wpdb
     */
    private function wpdb(): wpdb
    {
        global $wpdb;

        return $wpdb;
    }
}
