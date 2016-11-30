=== Map Visualizer ===
Contributors: dsamourkasidis,
Donate link:
Tags: csv, netcdf, map, visualization, geocode, leaflet
Requires at least: 4.1.5
Tested up to: 4.3.1
Stable tag:
License: GPL 3.0
License URI: https://www.gnu.org/licenses/gpl.html

Import your data from csv or netcdf files and visualise them on Google streets/satellite maps

== Description ==

Map Visualizer allows users to import csv and netcdf files, and  to create a map out of them using the plugin's interface. 
After you have successfully imported (or created) a data source, you can visualise it on a map and add it to your posts/pages with a shortcode. 
All shortcodes can be constructed easily through the plugin's inteface.

= CSV File Format =

In order for the CSV file to be imported successfully, its format must comply with the following rules:

* The first row must contain all the Header names:
  * If you use geographical coordinates, the headers of *the first two column* must be named "Latitude" and "Longitude" respectively. 
  * If you use addresses, the *first column* must be named "Address" and the plugin will geocode the data accordingly.
  * If you wish to use polygons, the *first column* must be named "Polygon". Format of polygon data should be of Well-Known Text.

* The second row must contain the data type of each column:
  * Available data types include "INT", "FLOAT" and "TEXT".
  * For "Latitude" and "Longitude" headers, the data type to be used is "FLOAT".
  * For "Polygon" or "Address" headers, the data type to be used is "TEXT".
  
= netCDF File Format =

* The plugin currently supports only 2-dimensional netCDF file formats. 
* Only geographical coordinates are supported for netCDF files, so the two dimensions must be named "Latitude" and "Longitude" of "float" type.
* The available data type for all the variables are again either "INT", "FLOAT" or "TEXT".

* Issues with uploading netCDF files on you WordPress installation
    * Please make sure you have the latest Java installed on your machine
	* .nc Extension:
        Wordpress does not allow the upload of unregistered file types due to security reasons. This means, you should add
        the .nc extension to the list of permitted extensions by following the instructions linked here:
        http://www.wpbeginner.com/wp-tutorials/how-to-add-additional-file-types-to-be-uploaded-in-wordpress
	* File Size:
        Wordpress has a limit for the maximum file size that can be uploaded, depending on the web hosting company you choose and the package you select.
        You can view it on the "Media Uploader" page and can increase it, if necessary, following the instructions linked here:
        http://www.wpbeginner.com/wp-tutorials/how-to-increase-the-maximum-file-upload-size-in-wordpress/

= shortcode format =

The general format of this plugin's shortcodes is [visualise]. Available attributes include:

* "file_name" : The name of the file imported you wish to visualise.
* "map" : The map type to be used for the visualization. You can choose between "Satellite" or "Streets" maps and the default one is "Streets".
* "type" : Type of visualization. Either visualise your data as "polygon" or as "marker". Default type is "marker".
* "marker_type" : Choose between "simple marker" or "circle_marker" to be pinned on the chosen map. The default markers are "simple marker".
* "category" : Header Name of your file, which will be used to differentiate the data based on the marker_type chosen.
* "colorant" : Color of circle_markers and polygons, used to differentiate the data.
* "circle_radius" : Radius of circle_markers. The default value is 5, and it is advised to stay between a 1-10 scale.
* "fill_opacity" : Level of opacity for circle_markers and polygons. The default value is 0.3, and it is advised to stay between a 0.1-1 scale.
* "center_point" : An Address for setting the initial center point of the map.
* "zoom" : Initial zoom of the map. Value 1 shows the entire world map and the default zoom used is 3.

== Installation ==
1. Upload the content of the ZIP archive to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Import your file through the 'Import netcdf File' or 'Import CSV File' pages. Alternatively create your own through the 'Create New File' page.
4. View your stored files, construct and copy a shortcode through the 'List All Files' page.
5. Paste the shortcode in the text where you want the map visualization to appear

== Changelog ==

= 1.0 =
*Initial Release