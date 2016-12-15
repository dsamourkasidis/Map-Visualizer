<?php
/**
 * Plugin Name: Map Visualizer
 * Description: Import CSV files and visualize your data on one of the available maps. You can also create your own files for visualization
 * Version: 1.0.0
 * Author: Dimitris Samourkasidis and Ioannis N. Athanasiadis
 * License: GPL3
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );


//Hook up to the init action
add_action( 'init', 'mv_init' );

function mv_scripts() {
    wp_enqueue_script( 'mv_leafletjs', plugins_url( 'Scripts/leaflet.js', __FILE__ ), array());
    wp_enqueue_style( 'mv_leafletcss', plugins_url( 'Scripts/leaflet.css', __FILE__ ), false);
}

add_action( 'admin_enqueue_scripts', 'mv_scripts' );
add_action('wp_enqueue_scripts', 'mv_scripts');

function mv_init(){

    //Register menu pages
    add_action( 'admin_menu', 'mv_menu');

    //Register the shortcode
    include 'visualization-shortcode/mv_visualize.php';
    add_shortcode('visualize', 'mv_visualize');

}

function mv_menu()
{
    add_menu_page('Map Visualizer', 'Map Visualizer', 'publish_posts', 'mv_menu', 'mv_index_page', 'dashicons-admin-generic');
    add_submenu_page('mv_menu', 'Import', 'Import CSV File', 'publish_posts', 'mv_import_page', 'mv_import_csv_page');
    add_submenu_page('mv_menu', 'Create', 'Create New File', 'publish_posts', 'mv_creation_page', 'mv_create_file_page');
    add_submenu_page('mv_menu', 'File-list', 'List All Files', 'publish_posts', 'mv_list_page', 'mv_list_all_files');
    include 'submenu-pages/mv_index.php';
    include 'submenu-pages/mv_import_csv_page.php';
    include 'submenu-pages/mv_create_file_page.php';
    include 'submenu-pages/mv_list_all_files.php';
}