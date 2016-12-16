<?php
/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 7/4/2015
 * Time: 1:09 AM
 */
function map_visualizer_visualize($atts)
{
    $a = shortcode_atts(array(
        'file_name' => 'f_name',
        'category' => 'stg',
        'map' => 'Streets',
        'marker_type' => 'simple marker',
        'type' => 'marker',
        'colorant' => 'default',
        'circle_radius' => 5,
        'fill_opacity' => 0.3,
        'center_point' => 'default',
        'zoom' => 3
    ), $atts);

    $table_name = $a['file_name'];
    $category = $a['category'];
    $map_type = $a['map'];
    $marker_type = $a['marker_type'];
    $type = $a['type'];
    $colorant = $a['colorant'];
    $radius = $a['circle_radius'];
    $fill_opacity = $a['fill_opacity'];
    $center_point = $a['center_point'];
    $zoom = $a['zoom'];
    // Create connection
    global $wpdb;
    // Get the Headers from the csv file and store them in col_names
    $sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$wpdb->dbname' AND TABLE_NAME='$table_name'";
    $result = $wpdb->get_results($sql, ARRAY_A);
    $col_names = array();
    foreach ($result as $res) {
        $col_names[] = $res['COLUMN_NAME'];
    }
    $sql2 = "SELECT * FROM $table_name";
    $result2 = $wpdb->get_results($sql2, ARRAY_A);
    ?>

    <div id='map'></div>
    <style> #map {
            height: 400px;
            width: 500px;
            position: inherit;
            z-index: 0;
        }
    </style>
    <script>
        var accessToken = 'pk.eyJ1IjoiZHNhbSIsImEiOiJrTGdOSjhVIn0.zlpcBqn7P2TQyzQVNBCU7w';
        var map = L.map('map');
        var mapboxTiles1 = L.tileLayer('https://{s}.tiles.mapbox.com/v4/dsam.llp7bo02/{z}/{x}/{y}.png?access_token=' + accessToken, {
            attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
        });
        var mapboxTiles2 = L.tileLayer('https://{s}.tiles.mapbox.com/v4/dsam.m3en6deb/{z}/{x}/{y}.png?access_token=' + accessToken, {
            attribution: '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
        });
        var file_name = "<?php echo esc_js($a['file_name']); ?>";
        var baseMaps = {
            "Satellite": mapboxTiles2,
            "Streets": mapboxTiles1
        };
        L.control.layers(baseMaps).addTo(map);
        //Checking if map_type is "Streets" or "Satellite"
        <?php if ($map_type == 'Streets') { ?>
        map.addLayer(mapboxTiles1)
            .setView([42.3610, -71.0587], 3);
        <?php
           }elseif ($map_type == 'Satellite') { ?>
        map.addLayer(mapboxTiles2)
            .setView([42.3610, -71.0587], 3);
        <?php
           }//else { echo 'not available map'; }
           $max = NULL;
           $min = NULL;
           $once =0;
               foreach ($result2 as $row) {
                   //Checking if geocode format is Address or Latitude/Longitude
                   if ($col_names[0] == "Address"){
                        $address = $row[$col_names[0]];
                        $coords = map_visualizer_getcoord($address);
                        $latitude = $coords['latitude'];
                        $longitude = $coords['longitude'];
                   }else{
                        $latitude = floatval($row[$col_names[0]]);
                        $longitude = floatval($row[$col_names[1]]);
                   }
                   //Centering and zooming the map
                   if ($once ==0){
                       if ($zoom == 3){
                           if ($center_point == 'default'){
                                ?>
        map.setView(<?php echo esc_js("[".$latitude.",".$longitude."],3"); ?>);
        <?php
                                  }else{
                                       $cnt_coords = map_visualizer_getcoord($center_point);
                                       $cnt_latitude = $cnt_coords['latitude'];
                                       $cnt_longitude = $cnt_coords['longitude'];
                                       ?>
        map.setView(<?php echo esc_js("[".$cnt_latitude.",".$cnt_longitude."],3"); ?>);
        <?php
                                  }
                              }else{
                                   if ($center_point == 'default'){
                                       ?>
        map.setView(<?php echo esc_js("[".$latitude.",".$longitude."],".$zoom); ?>);
        <?php
                                   }else{
                                       $cnt_coords = map_visualizer_getcoord($center_point);
                                       $cnt_latitude = $cnt_coords['latitude'];
                                       $cnt_longitude = $cnt_coords['longitude'];
                                       ?>
        map.setView(<?php echo esc_js("[".$cnt_latitude.",".$cnt_longitude."],".$zoom); ?>);
        <?php
                              }
                              }
                          }
                          $once++;
                           // Checking if type is "marker" or "polygon
                           if ($type == 'marker') {
                               //Checking if marker_type is "circle" or "simple"
                               if ($marker_type == 'circle marker'){
                                  if (array_key_exists($category,$row)){
                                      //Create a scale and retrieve the colour for each value of the inserted category
                                          if ($colorant == 'temp'){
                                               $color = map_visualizer_gettempColor($row[$category]);
                                          }else{
                                               if ($max == NULL or $min == NULL){
                                                  $sql3 = "SELECT MAX($category) AS max_value FROM $table_name";
                                                  $sql4 = "SELECT MIN($category) AS min_value FROM $table_name";
                                                  $result3 = $wpdb->get_results($sql3,ARRAY_A );
                                                  $result4 =  $wpdb->get_results($sql4,ARRAY_A );
                                                  $max = $result3[0]['max_value'];
                                                  $min = $result4[0]['min_value'];
                                                  $scale = map_visualizer_create_values($max,$min);
                                               }
                                              $color = map_visualizer_getColor($row[$category],$scale);
                                          }
                                  }
                               ?>
        //Creating CircleMarkers
        L.circleMarker([<?php echo esc_js($latitude); ?>, <?php echo esc_js($longitude); ?>], {
            stroke: false,
            radius: <?php if (isset($radius)) { echo esc_js($radius); }else { echo esc_js(5); } ?>,
            opacity: 0.1,
            fillOpacity: <?php if (isset($fill_opacity)) { echo esc_js($fill_opacity); }else { echo esc_js(0.3); } ?>,
            color: '<?php if (isset($color)) { echo esc_js($color); }else { echo esc_js('#800026'); } ?>'
        }).addTo(map)
            .bindPopup("<?php foreach($col_names as $col_name): echo esc_js($col_name); ?> : <?php echo esc_js(str_replace("\r", '', $row[$col_name])); ?> <br> <?php endforeach; ?>");
        <?php
    } elseif ($marker_type == 'simple marker') {
            //Creating Simple Markers
    if (array_key_exists($category,$row)){
      ?>
        L.marker([<?php echo esc_js($latitude); ?>, <?php echo esc_js($longitude); ?>])
            .addTo(map)
            .bindPopup("<?php echo esc_js($category); ?> : <?php echo esc_js(str_replace("\r", '', $row[$category])); ?> <br> ");
        <?php
        }else{
              ?>
        L.marker([<?php echo esc_js($latitude); ?>, <?php echo esc_js($longitude); ?>])
            .addTo(map)
            .bindPopup("<?php foreach($col_names as $col_name): echo esc_js($col_name); ?> : <?php echo esc_js(str_replace("\r", '', $row[$col_name])); ?> <br> <?php endforeach; ?>");
        <?php
        }
}else { echo 'not available marker'; }
}elseif ($type == 'polygon'){
if (array_key_exists($category,$row)){
       //Create a scale and retrieve the colour for each value of the inserted category
           if ($colorant == 'temp'){
                $color = map_visualizer_gettempColor($row[$category]);
           }else{
                if ($max == NULL or $min == NULL){
                   $sql3 = "SELECT MAX($category) AS max_value FROM $table_name";
                   $sql4 = "SELECT MIN($category) AS min_value FROM $table_name";
                   $result3 = $wpdb->get_results($sql3,ARRAY_A );
                   $result4 =  $wpdb->get_results($sql4,ARRAY_A );
                   $max = $result3[0]['max_value'];
                   $min = $result4[0]['min_value'];
                   $scale = map_visualizer_create_values($max,$min);
                }
               $color = map_visualizer_getColor($row[$category],$scale);
           }
   }
//Creating Polygons
$x = $row['Polygon'];
$coords = str_replace("POLYGON ","",$x);
$coords = str_replace("((","",$coords);
$coords = str_replace("))","",$coords);
$exp = explode(",",$coords);
foreach($exp as $crds){
    $cords[] = explode(" ",$crds);
}
$last = end($cords);
$first = $cords[0];
//if ($first[1] == "13.234997"){
//echo "hello";
//}
$i=0;
if ($first[1] == "47.543327"){

}
while ($first[0] == $last[0] or $first[1] == $last[1]){
unset($cords[$i]);
$i++;
$first = $cords[$i];
$last = end($cords);
}
?>
        L.polygon([<?php foreach($cords as $crds) { echo esc_js("[".floatval($crds[1]).",".floatval($crds[0])."]"); if ($last[1] != $crds[1]) { echo esc_js(","); } }?>],
            {
                fillOpacity: <?php if (isset($fill_opacity)) { echo esc_js($fill_opacity); }else { echo esc_js(0.3); } ?>,
                color: '<?php if (isset($color)) { echo esc_js($color); }else { echo esc_js('#800026'); } ?>'
            })
            .addTo(map)
            .bindPopup("<?php foreach($col_names as $col_name){ if ($col_name != "Polygon") { echo esc_js($col_name.":".str_replace("\r", '', $row[$col_name])."<br>"); } }?>");
        <?php
        unset($cords);
        unset($first);
        unset($last);
        unset ($i);
    }//else{ echo "not available type";}
}
        ?>
    </script>
<?php
}

//Get the coordinates from Google by Address
function map_visualizer_getcoord($address)
{
    $prepAddr = str_replace(' ', '+', $address);
    $url = 'http://maps.google.com/maps/api/geocode/json?address=' . $prepAddr . '&sensor=false';
    $response = wp_remote_get($url);
    $body = wp_remote_retrieve_body( $response );
    $output = json_decode($body);
    $latitude = $output->results[0]->geometry->location->lat;
    $longitude = $output->results[0]->geometry->location->lng;
    $coords['latitude'] = $latitude;
    $coords['longitude'] = $longitude;
    return $coords;
}

//Returning the colour for a value
function map_visualizer_getColor($d, $scale)
{
    if ($d >= $scale[5]) {
        return '#99000d';
    } elseif ($d > $scale[4]) {
        return '#cb181d';
    } elseif ($d > $scale[3]) {
        return '#ef3b2c';
    } elseif ($d > $scale[2]) {
        return '#fb6a4a';
    } elseif ($d > $scale[1]) {
        return '#fc9272';
    } elseif ($d > $scale[0]) {
        return '#fcbba1';
    } else {
        return '#fee5d9';
    }
}

//Returning Temperature colours
function map_visualizer_gettempColor($t)
{
    if ($t >= 86.666) {
        return '#FF0DF0';
    } elseif ($t > 82.2222) {
        return '#FF09F0';
    } elseif ($t > 77.77) {
        return '#FF05F0';
    } elseif ($t > 73.333) {
        return '#FF01F0';
    } elseif ($t > 68.888) {
        return '#FF00C0';
    } elseif ($t > 64.444) {
        return '#FF0080';
    } elseif ($t > 60) {
        return '#FF0040';
    } elseif ($t > 55.555) {
        return '#FF0000';
    } elseif ($t > 51.111) {
        return '#FF2800';
    } elseif ($t > 46.666) {
        return '#FF5000';
    } elseif ($t > 42.222) {
        return '#FF7800';
    } elseif ($t > 37.777) {
        return '#FFa000';
    } elseif ($t > 33.33) {
        return '#FFc800';
    } elseif ($t > 28.888) {
        return '#FFf000';
    } elseif ($t > 24.444) {
        return '#b0ff00';
    } elseif ($t > 20) {
        return '#17ff00';
    } elseif ($t > 15.555) {
        return '#00ff83';
    } elseif ($t > 11.111) {
        return '#00e4ff';
    } elseif ($t > 6.666) {
        return '#00a4ff';
    } elseif ($t > 2.222) {
        return '#0064ff';
    } elseif ($t > -2.222) {
        return '#0022ff';
    } elseif ($t > -6.666) {
        return '#0100ff';
    } elseif ($t > -11.111) {
        return '#0400ff';
    } else {
        return '#0500ff';
    }
}

//Creating a scale for colours
function map_visualizer_create_values($max, $min)
{
    $arr = array();
    $scale = ($max - $min) / 5;
    $arr[0] = intval($min);
    $arr[1] = $min + $scale;
    $arr[2] = $min + 2 * $scale;
    $arr[3] = $min + 3 * $scale;
    $arr[4] = $min + 4 * $scale;
    $arr[5] = $min + 5 * $scale;
    return $arr;
}