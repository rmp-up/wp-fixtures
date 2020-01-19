<?php

// Only load this plugin once and bail if WP CLI is not present
if (false === defined('WP_CLI')) {
    return;
}

\WP_CLI::add_command('fixture', \RmpUp\WordPress\Fixtures\Cli\FixtureCommand::class);
