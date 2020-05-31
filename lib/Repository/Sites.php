<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Site.php
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

namespace RmpUp\WordPress\Fixtures\Repository;

require_once ABSPATH . '/wp-admin/includes/ms.php';

use WP_Error;
use WP_Site;

/**
 * Site
 *
 * @copyright 2020 Pretzlaw (https://rmp-up.de)
 */
class Sites extends AbstractRepository
{
    protected $primaryKey = 'blog_id';

    /**
     * @param WP_Site $object
     *
     * @return int
     */
    protected function create($object): int
    {
        $object->site_id = $this->resolveSiteId($object); // Use existing sites

        $blogId = wpmu_create_blog($object->domain, $object->path, $object->blogname, 1, $object->to_array());

        if ($blogId instanceof WP_Error) {
            throw new PersistException($object, $blogId);
        }

        if ((int) $object->site_id === 0 || !$object->site_id) {
            $object->site_id = $this->resolveSiteId($object); // Use existing sites
        }

        return (int) $blogId;
    }

    /**
     * @param WP_Site $object
     * @param string  $fixtureName
     */
    public function delete($object, string $fixtureName)
    {
        if (null === $object->blog_id) {
            $object->blog_id = (string) domain_exists($object->domain, $object->path);
        }

        if (!$object->blog_id) {
            throw new RepositoryException(
                $object,
                sprintf('Could not delete blog ("%s%s", ID: "%s")', $object->domain, $object->path, $object->blog_id)
            );
        }

        wpmu_delete_blog((int) $object->blog_id, true);
    }

    /**
     * @param WP_Site $object
     * @param string  $fixtureName
     *
     * @return int|null
     */
    public function find($object, string $fixtureName)
    {
        foreach ((array) get_sites() as $site) {
            /** @var WP_Site $site */
            if ($site->domain === $object->domain && $site->path === trailingslashit($object->path)) {
                return (int) $site->blog_id;
            }
        }

        return null;
    }

    private function resolveSiteId(WP_Site $object): string
    {
        foreach ((array) get_sites() as $site) {
            /** @var WP_Site $site */
            if ($object->domain === $site->domain) {
                return $site->site_id;
            }
        }

        return '0';
        /** @see \WP_Site::$site_id */
    }

    /**
     * @param WP_Site $object
     */
    protected function update($object)
    {
        $id = wp_update_site((int) $object->site_id, $object->to_array());

        if ($id instanceof WP_Error) {
            throw new RepositoryException($object, $id);
        }
    }
}