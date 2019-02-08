<?php

\Pretzlaw\WPInt\run_wp();

add_filter('user_has_cap', 'rmp_wp_fixture_all_caps', 10, 2);

function rmp_wp_fixture_all_caps($allcaps, $caps)
{
    foreach ($caps as $cap) {
        // allow all.
        $allcaps[$cap] = true;
    }

    return $allcaps;
}