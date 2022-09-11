<?php

if (! defined('ABSPATH')) {
    exit;
}
// if uninstall not called from WordPress exit
if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

$option_name = '';

if ($option_name != '') {
    delete_option($option_name);
    // For site options in multisite
    delete_site_option($option_name);
}
