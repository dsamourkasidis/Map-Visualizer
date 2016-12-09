<?php
/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 7/4/2015
 * Time: 1:13 AM
 */
function mv_index_page()
{
    ?>
    <h1>Visualization Plugin</h1>
    <div id="maps">
        <div id="map"></div>
        <div id="map2"></div>
    </div>
    <style>
        #map {
            height: 400px;
            width: 500px;
            float: left;
        }

        #map2 {
            height: 400px;
            width: 500px;
        }
    </style>
    <script>
        //Sample maps for index page
        var mapbox_accessToken = 'pk.eyJ1IjoiZHNhbSIsImEiOiJrTGdOSjhVIn0.zlpcBqn7P2TQyzQVNBCU7w';
        // Replace 'examples.map-i87786ca' with your map id.
        var mapboxTiles = L.tileLayer('https://{s}.tiles.mapbox.com/v4/dsam.llp7bo02/{z}/{x}/{y}.png?access_token=' + mapbox_accessToken, {
            attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
        });
        var map = L.map('map')
            .addLayer(mapboxTiles)
            .setView([42.3610, -71.0587], 1);

        // L.mapbox.accessToken = 'pk.eyJ1IjoiZHNhbSIsImEiOiJrTGdOSjhVIn0.zlpcBqn7P2TQyzQVNBCU7w';
        // Replace 'examples.map-i87786ca' with your map id.
        var mapboxTiles2 = L.tileLayer('https://{s}.tiles.mapbox.com/v4/dsam.m3en6deb/{z}/{x}/{y}.png?access_token=' + mapbox_accessToken, {
            attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
        });
        var map2 = L.map('map2')
            .addLayer(mapboxTiles2)
            .setView([42.3610, -71.0587], 1);

        var marker = L.marker([51.5, -0.09]).addTo(map)
            .bindPopup("I am a marker.");
        var circle = L.circleMarker([21.508, -0.11], {
            stroke: false,
            opacity: 1,
            fillOpacity: 0.7,
            color: '#800026'
        }).addTo(map);
        circle.bindPopup("I am a circle.");
        var polyg = L.polygon([[[41.902783, 12.496366], [40.416775, -3.703790], [52.520007, 13.404954]],[[-53.494339, 5.572342],[-53.483612, 5.568055],[-53.40834, 5.548888]]]).addTo(map);

        var marker2 = L.marker([51.5, -0.09]).addTo(map2)
            .bindPopup("I am a marker.");
        var circle2 = L.circleMarker([21.508, -0.11], {
            stroke: false,
            opacity: 1,
            fillOpacity: 0.7,
            color: '#800026'
        }).addTo(map2);
        circle2.bindPopup("I am a circle.");
        var polyg2 = L.polygon([[41.902783, 12.496366], [40.416775, -3.703790], [52.520007, 13.404954]]).addTo(map2);
    </script>

    <div id="instructions">
        <br>

        <h2> Visualization Plugin is a Wordpress Plugin that allows you to create, import or export csv files and
            visualise
            them in
            your posts/pages. Additionally, apart from csv you can import netcdf files for visualization. </h2>

        <p>Instructions:
            <br>You can either create manually a new csv file from the "Create new csv file" page or import your already
            created from the "Import csv file" page. The netcdf files can be imported through the "Import netcdf File"
            page.
            <br>After the import is completed your file is stored into the Wordpress Database. You can then view all of
            your
            imported files in the "List Imported files" page, where the required shortcode to visualise each file is
            displayed, along with the available options.
            <br>By inserting the appropriate shortcode into a post or page the visualization is complete.
            In the above maps you can view some of the options provided inside the shortcode
        </p>
    </div>
<?php
}