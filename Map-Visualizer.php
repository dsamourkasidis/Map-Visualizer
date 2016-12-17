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
add_action( 'init', 'map_visualizer_init' );
add_action( 'admin_enqueue_scripts', 'map_visualizer_scripts' );
add_action('wp_enqueue_scripts', 'map_visualizer_scripts');
register_activation_hook( __FILE__, 'map_visualizer_activate' );
register_uninstall_hook(__FILE__, 'map_visualizer_unistall');

function map_visualizer_scripts() {
    wp_enqueue_script( 'map_visualizer_leafletjs', plugins_url( 'Scripts/leaflet.js', __FILE__ ), array());
    wp_enqueue_style( 'map_visualizer_leafletcss', plugins_url( 'Scripts/leaflet.css', __FILE__ ), false);
}

function map_visualizer_activate() {
    global $wpdb;
    //Create a table where all imported file names will be stored
    $sql = "CREATE TABLE IF NOT EXISTS Imported_files (`Name` TEXT)";
    $wpdb->query($sql);
}

function map_visualizer_unistall(){
    //Delete all tables on uninstall
    global $wpdb;
    $imported = $wpdb->get_col("SELECT Imported_files.Name FROM Imported_files");
    for($i=0;$i<sizeof($imported);$i++) {
        $name = $imported[$i];
        $sql = "DROP TABLE $name";
        $wpdb->query($sql);
    }
    $wpdb->query("DROP TABLE Imported_files");
}

function map_visualizer_init(){

    //Register menu pages
    add_action( 'admin_menu', 'map_visualizer_menu');

    //Register the shortcode
    include 'visualization-shortcode/map_visualizer_visualize.php';
    add_shortcode('visualize', 'map_visualizer_visualize');
}

function map_visualizer_menu()
{
    add_menu_page('Map Visualizer', 'Map Visualizer', 'publish_posts', 'map_visualizer_menu', 'map_visualizer_index_page', 'dashicons-location-alt');
    add_submenu_page('map_visualizer_menu', 'Import', 'Import CSV File', 'publish_posts', 'map_visualizer_import_page', 'map_visualizer_import_csv_page');
    add_submenu_page('map_visualizer_menu', 'Create', 'Create New File', 'publish_posts', 'map_visualizer_creation_page', 'map_visualizer_create_file_page');
    add_submenu_page('map_visualizer_menu', 'File-list', 'List All Files', 'publish_posts', 'map_visualizer_list_page', 'map_visualizer_list_all_files');
    include 'submenu-pages/map_visualizer_index.php';
    include 'submenu-pages/map_visualizer_import_csv_page.php';
    include 'submenu-pages/map_visualizer_create_file_page.php';
    include 'submenu-pages/map_visualizer_list_all_files.php';
}