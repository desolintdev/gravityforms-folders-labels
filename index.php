<?php

if (! defined('ABSPATH')) {
    exit;
}
/*
Plugin Name: Manage Folders and Labels (Gravity Forms)
Plugin URI: https://desolint.com
Description: This will allow you to add the folders and labels to the gravity form.
Version: 1.0.0
Author: Desol Int
Text Domain: flgf
Domain Path: /languages
Requires at least: 3.0
Requires PHP: 5.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
@copyright Copyright (C) 2022-2022 Desolint.
@license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 or higher

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <https://www.gnu.org/licenses/>.
*/
require plugin_dir_path(__FILE__) . 'include/functions.php';
register_activation_hook(__FILE__, 'flgf_gravityform_labels_activate');
register_deactivation_hook(__FILE__, 'flgf_gravityforms_labels_deactivate');
if (! is_plugin_active('gravityforms/gravityforms.php')) {
    deactivate_plugins('gravityforms-folders-labels/index.php');
    wp_die(__('Gravity Forms is not installed and activated. Please install and activate Gravity Forms.', 'flgf'));
}
if (is_admin()) {
    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_$plugin", 'flgf_gfolders_plugin_setting');
    add_action('admin_enqueue_scripts', 'flgf_gfolder_admin_script', 99);
}
