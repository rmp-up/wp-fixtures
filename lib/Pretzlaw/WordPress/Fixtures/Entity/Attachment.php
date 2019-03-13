<?php
declare(strict_types=1);

namespace Pretzlaw\WordPress\Fixtures\Entity;


class Attachment extends \stdClass implements Sanitizable
{
    /**
     * Post ID.
     *
     * @since 3.5.0
     * @var int
     */
    public $ID;

    /**
     * The post's title.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_title;

    /**
     * The post's content.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_content;

    /**
     * The post's status.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_status = 'publish';

    /**
     * ID of a post's parent post.
     *
     * @since 3.5.0
     * @var int
     */
    public $post_parent = 0;

    /**
     * An attachment's mime type.
     *
     * @since 3.5.0
     * @var string
     */
    public $post_mime_type;

    /**
     * @param string $fixtureName Name as given in the fixture configuration.
     * @return \stdClass a clone of the sanitized object
     */
    public function sanitize(string $fixtureName)
    {
        if (empty($this->post_title)) {
            $this->post_title = $fixtureName;
        }

        if (empty($this->post_content)) {
            //set a default image? where does it come from?
        }

        if (empty($this->post_mime_type)) {
            //depends on post_content
        }
    }
}