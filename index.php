<?php if ( ! defined( 'ABSPATH' ) ) exit; 
/*
Plugin Name: Manage Folders and Labels
Plugin URI: https://desolint.com
Description: This will allow you to add the folders and labels to the gravity form.
Version: 1.0
Author: Desol Int
*/ 
        
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	global $gfolder_activated;
	global $gfolders_data, $gfolders_dir, $gf_get_all_plugins, $gfolders_url, $gfolders_plugins_activated;
	
	
	
	$gfolder_activated = false;
	$gfolders_url = plugin_dir_url( __FILE__ );
	$gf_get_all_plugins = get_plugins();
	$gfolders_plugins_activated = apply_filters( 'active_plugins', get_option( 'active_plugins' ));
	
	if(is_multisite()){			
		
		$active_sitewide_plugins = get_site_option( 'active_sitewide_plugins' );
		
		$gfolders_plugins_activated = array_keys($active_sitewide_plugins);		
		
	}
		
	if(array_key_exists('gravityforms/gravityforms.php', $gf_get_all_plugins) && in_array('gravityforms/gravityforms.php', $gfolders_plugins_activated)){
		$gfolder_activated = true;
	}

	$gfolders_dir = plugin_dir_path( __FILE__ );
	$gfolders_data = get_plugin_data(__FILE__);
	
	
	include('include/functions.php');
        
	register_activation_hook(__FILE__, 'flgf_gravityform_labels_activate');

	register_deactivation_hook(__FILE__, 'flgf_gravityforms_labels_deactivate' );

	add_action( 'wp_enqueue_scripts', 'flgf_gfolders_registration_script' );
	
	
	
		
	if(is_admin()){
		$plugin = plugin_basename(__FILE__); 
		add_filter("plugin_action_links_$plugin", 'flgf_gfolders_plugin_setting' );	
		add_action( 'admin_enqueue_scripts', 'flgf_gfolder_admin_script', 99 );
	}