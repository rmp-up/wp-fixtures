<?php

const MY_PLUGIN_DIR = __DIR__;

$_SERVER['HTTP_HOST'] = '10.211.3.71'; // DOMAIN_CURRENT_SITE in wp-config.php

define('_BASE_DIR', dirname(__DIR__, 2));
putenv('WP_DIR=' . _BASE_DIR . '/srv');

require_once _BASE_DIR . '/vendor/autoload.php';
require_once _BASE_DIR . '/etc/wp/vendor/autoload.php';
require_once _BASE_DIR . '/etc/phpunit/vendor/autoload.php';
require_once _BASE_DIR . '/etc/phpunit/vendor/pretzlaw/wp-integration-test/bootstrap.php';

class Mirror extends \stdClass {}

class_alias(Mirror::class, 'SomeThing');

// Enable after bootstrapping WP - to not confuse bootstrap of installed plugins
const WP_CLI = true;

function rmp_wp_fixture_all_caps($allcaps, $caps)
{
    foreach ($caps as $cap) {
        // allow all.
        $allcaps[$cap] = true;
    }

    return $allcaps;
}

add_filter('user_has_cap', 'rmp_wp_fixture_all_caps', 10, 2);

require_once ABSPATH . '/wp-admin/includes/user.php';

require_once __DIR__ . '/../../lib/compat.php';
