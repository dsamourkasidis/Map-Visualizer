<?php
/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 7/4/2015
 * Time: 1:13 AM
 */
function map_visualizer_index_page()
{
    ?>
    <h1>Map Visualizer</h1>
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
        var mapboxTiles2 = L.tileLayer('https://{s}.tiles.mapbox.com/v4/dsam.m3en6deb/{z}/{x}/{y}.png?access_token=' + mapbox_accessToken, {
            attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
        });
        var map2 = L.map('map2')
            .addLayer(mapboxTiles2)
            .setView([42.3610, -71.0587], 1);

        L.marker([51.5, -0.09]).addTo(map)
            .bindPopup("I am a marker.");
        var circle = L.circleMarker([21.508, -0.11], {
            stroke: false,
            opacity: 1,
            fillOpacity: 0.7,
            color: '#800026'
        }).addTo(map)
        circle.bindPopup("I am a circle.");
        L.polygon([[[41.902783, 12.496366], [40.416775, -3.703790], [52.520007, 13.404954]],[[-53.494339, 5.572342],[-53.483612, 5.568055],[-53.40834, 5.548888]]]).addTo(map);

        L.marker([51.5, -0.09]).addTo(map2)
            .bindPopup("I am a marker.");
        var circle2 = L.circleMarker([21.508, -0.11], {
            stroke: false,
            opacity: 1,
            fillOpacity: 0.7,
            color: '#800026'
        }).addTo(map2);
        circle2.bindPopup("I am a circle.");
        L.polygon([[41.902783, 12.496366], [40.416775, -3.703790], [52.520007, 13.404954]]).addTo(map2);
    </script>

    <div id="instructions">
        <br>

        <h2> Map Visualizer is a WordPress Plugin that allows you to create, import or export csv files and
            visualise them in your posts/pages.
        </h2>

        <h4>Instructions:</h4>
        <ol>
            <li>Import or create a new CSV file</li>
            <li>View your imported files and construct shortcodes for them</li>
            <li>Paste the shortcode into your post or page</li>
            <li>Your Visualization is ready!</li>
        </ol>
    </div>
<?php
}