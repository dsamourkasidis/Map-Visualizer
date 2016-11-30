<?php
/**
 * Plugin Name: Map Visualizer
 * Description: Insert netcdf or CSV files and visualize your data on one of the available maps. You can also create your own files for visualization
 * Version: 1.0.0
 * Author: Dimitris Samourkasidis and Ioannis N. Athanasiadis
 * License: GPL3
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


//Hook up to the init action
add_action( 'init', 'init_map_visualizer' ) ;


function init_map_visualizer(){

    //Register menu pages
    add_action( 'admin_menu', 'plugin_menu');

    //Register the shortcode
    include 'visualization-shortcode/visualization_shortcode.php';
    add_shortcode('visualize', 'visualize_func');

}

function plugin_menu()
{
    add_menu_page('Map Visualizer', 'Map Visualizer', 'publish_posts', 'my-plugin-settings', 'index_page', 'dashicons-admin-generic');
    add_submenu_page('my-plugin-settings', 'Import', 'Import CSV File', 'publish_posts', 'import-page', 'Import_CSV_File');
    add_submenu_page('my-plugin-settings', 'Create', 'Create New File', 'publish_posts', 'creation-page', 'Create_New_File');
    add_submenu_page('my-plugin-settings', 'netcdf-import', 'Import netcdf File', 'publish_posts', 'netcd', 'Import_netcdf_File');
    add_submenu_page('my-plugin-settings', 'File-list', 'List All Files', 'publish_posts', 'list-page', 'List_All_Files');
    include 'submenu-pages/index.php';
    include 'submenu-pages/Import-CSV-File.php';
    include 'submenu-pages/List-All-Files.php';
    include 'submenu-pages/Create-New-File.php';
    include 'submenu-pages/Import-netcdf-File.php';
}