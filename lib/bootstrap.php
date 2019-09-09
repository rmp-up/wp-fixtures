<?php

const WP_CLI = true;

\Pretzlaw\WPInt\run_wp();

function rmp_wp_fixture_all_caps($allcaps, $caps)
{
    foreach ($caps as $cap) {
        // allow all.
        $allcaps[$cap] = true;
    }

    return $allcaps;
}

add_filter('user_has_cap', 'rmp_wp_fixture_all_caps', 10, 2);

require_once __DIR__ . '/compat.php';
