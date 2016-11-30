<?php
/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 4/10/2015
 * Time: 5:08 PM
 *
 */
add_action('admin_init', 'checkexport');


function List_All_Files()
{
    $myfile = fopen(dirname(__FILE__) . "/Imported_files.txt", "r") or die("Unable to open file!");
    if (isset($_POST["delete"]) && "" != $_POST["delete"])     //delete button is submitted
    {
        global $wpdb;                 //your database
        foreach ($_POST['select'] as $selected) {    //Find selected checkboxes
//Delete csv from Imported_files.txt
            $contents = file_get_contents(dirname(__FILE__) . "/Imported_files.txt");
            $contents = str_replace($selected, '', $contents);
            file_put_contents(dirname(__FILE__) . "/Imported_files.txt", $contents);
//Delete csv from db
            $string = str_replace("\r\n", '', $selected);
            $sql = "DROP TABLE $string";
            $wpdb->query($sql);
            echo "<p>" . $selected . "</p>";
        }
    }elseif ( "" != $_POST["copy"]) {

        $name = $_POST['name'][key($_POST['copy'])];
        $map = $_POST['map'][key($_POST['copy'])];
        $type = $_POST['type'][key($_POST['copy'])];
        $marker_type = $_POST['marker_type'][key($_POST['copy'])];
        $category = $_POST['category'][key($_POST['copy'])];
        $colorant = $_POST['colorant'][key($_POST['copy'])];
        $circle_radius = $_POST['circle_radius'][key($_POST['copy'])];
        $fill_opacity = $_POST['fill_opacity'][key($_POST['copy'])];
        $center_point = $_POST['center_point'][key($_POST['copy'])];
        $zoom = $_POST['zoom'][key($_POST['copy'])];
        $text = $name." map=\"".$map."\" type=\"".$type."\" marker_type=\"".$marker_type."\" category=\"".$category."\" colorant=\"".$colorant."\" circle_radius=".$circle_radius." fill_opacity=".$fill_opacity." center_point=".$center_point." zoom=".$zoom."]";
        ?>
        <script>
            window.prompt("Copy shortcode: Ctrl+C, Enter", '<?php echo $text;?>');
        </script>
<?php
    }
    checkexport();
    ?>

    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            /*background-color: #0000cc;*/
        }

        th, td {
            padding: 15px;

        }

        table#tb1 {
            background-color: #ffffff;
        }
    </style>
    <h2>List of all stored files</h2>
    <h4> Here, you can view all the files you have created or imported and are stored in your wordpress database.
       <br> Next to their name, the shortcode required to visualize each of them is displayed.
        <br>Furthermore, you can either choose to delete them or export them as CSV files.</h4>
    <p>Inside the shortcode you can view all of the available options you can enter.
        <br>
        In <b>map</b> you can enter one of the two available map types to be used for the visualisation. The two map types you can choose
        from are "Satellite" and "Streets". The default map type which will be used is Streets.
        <br>
        In <b>type</b> you can choose the form in which your set of coordinates will appear in the map. They can either appear as markers
        or as polygons. Notice that if you choose polygon type, data must be entered in the form of Well-known text.
        In <b>marker_type</b> type you can choose between two available markers to be pinned on the chosen map. The two available markers
        are "simple marker" and "circle marker". The default markers are simple markers.
        <br>
        In <b>category</b> you can enter one of your CSV Headers, which will be used to differentiate your data depending on
        the marker type you chose. If circle marker is chosen, different colors will be used to represent them, depending
        on each value. If simple marker is chosen, then the CSV Header you enter will be the only one displayed in the
        popup box. If you do not wish to enter one, then circle markers will have the same color and simple markers will
        display all of the Headers in the popup box.
        <br>
        In <b>colorant</b> you can choose the color, which will be used to differentiate the data of the category inserted above.
        It is possible to either stick to the default colors, or choose temperature colors.
        <br>
        In <b>circle_radius</b> you are able to insert a desired radius for the circle markers. The default value is 5, and it is advised
        to stay between a 1-10 scale.
        <br>
        In <b>fill_opacity</b>, the level of opacity in circle markers and polygons is determined. The default value is 0.3, and it is advised
        to stay between a 0.1-1 scale.
        <br>
        In <b>center_point</b>, you can enter an Address,which will be the initial geographical center of the map. If no Address is inserted
        the first Address in your file will be used as a center point.
        <br>
        In <b>zoom</b>, you can set the initial map zoom. Value 1 shows the entire world map and the default zoom used is 3.
        <br>

       </p>
    <form id="frm1" method="post" enctype="multipart/form-data">
        <table id="tb1" cellspacing="0" style="width:auto;">
            <thead>
            <tr>
                <th scope="col">
                    <input type="checkbox" onclick='checkedAll(frm1);'>
                </th>
                <th scope="col">
                    <span>  File Name</span>
                </th>
                <th scope="col">
                    <span>  Shortcode</span>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i=0;
            while (!feof($myfile)) {
                $name = fgets($myfile);
                ?>
                <tr>
                    <td>
                        <input type="checkbox" name="select[]" value="<?php echo htmlspecialchars($name) ?>"
                    </td>
                    <td>
                        <?php
                        echo $name;
                        ?>
                    </td>
                    <td>
                        <input type="text" name="name[]" value='[visualize file_name="<?php echo str_replace("\r\n", '', $name) ?>"'  >
                        <select name="map[]">
                            <option value="Streets">map = "Streets"</option>
                            <option value="Satellite">map = "Satellite"</option>
                        </select>
                        <select name="type[]">
                            <option value="marker">type = "marker"</option>
                            <option value="polygon">type = "polygon"</option>
                        </select>
                        <select name="marker_type[]">
                            <option value="simple marker">marker_type = "simple marker"</option>
                            <option value="circle marker">marker_type = "circle marker"</option>
                        </select>
                        <select name="category[]">
                            <option value=" ">category = " "</option>
                        </select>
                        <select name="colorant[]">
                            <option value="default">colorant = "default"</option>
                            <option value="temp">colorant = "temp"</option>
                        </select>
                        circle_radius=<input type="text" name="circle_radius[]" value="5" size="2">
                        fill_opacity=<input type="text" name="fill_opacity[]" value="0.3" size="2">
                        center_point=<input type="text" name="center_point[]" value='"default"' size="5">
                        zoom=<input type="text" name="zoom[]" value="3" size="2">
                        ]
                        <input type="submit" value="copy shortcode" name="copy[<?php echo $i; ?>]" id="copy"
                    </td>
                </tr>
            <?php
                $i++;
            }
            ?>
            <tr>
                <td><input type="submit" value="Delete" name="delete" id="delete"</td>
            </tr>
            <tr>
                <td><input type="submit" value="Export As CSV" name="export" id="export"</td>
            </tr>
            </tbody>
        </table>

    </form>

    <script type="text/javascript">
        checked = false;
        function checkedAll(frm1) {
            var aa = document.getElementById('frm1');
            if (checked == false) {
                checked = true
            }
            else {
                checked = false
            }
            for (var i = 0; i < aa.elements.length; i++) {
                aa.elements[i].checked = checked;
            }
        }
    </script>
    <?php
    fclose($myfile);
}
function exportMysqlToCsv($table)
{
    $csv_terminated = "\n";
    $csv_separator = ",";
    $csv_enclosed = '';
    $csv_escaped = "\\";
    $sql_query = "SELECT * FROM $table";

    global $wpdb;
    // Gets the data from the database
    $result = $wpdb->get_results($sql_query,ARRAY_A );
    $fields_cnt = sizeof($result[0]);
    $schema_insert = '';
    while ($element = current($result[0])) {
        $l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
                stripslashes(key($result[0]))) . $csv_enclosed;
        $hdrs[] = key($result[0]);
        $schema_insert .= $l;
        $schema_insert .= $csv_separator;
        next($result[0]);
    } // end for

    $out = trim(substr($schema_insert, 0, -1));
    $out .= $csv_terminated;

    // Format the data
    foreach ($result as $row) {
        $schema_insert = '';
        for ($j = 0; $j < $fields_cnt; $j++) {
            if ($row[$hdrs[$j]] == '0' || $row[$hdrs[$j]] != '') {

                if ($csv_enclosed == '') {
                    $schema_insert .= $row[$hdrs[$j]];
                } else {
                    $schema_insert .= $csv_enclosed .
                        str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$hdrs[$j]]) . $csv_enclosed;
                }
            } else {
                $schema_insert .= '';
            }

            if ($j < $fields_cnt - 1) {
                $schema_insert .= $csv_separator;
            }
        } // end for

        $out .= $schema_insert;
        $out .= $csv_terminated;
    } // end while
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=data.csv');
    echo $out;
    exit;
}
function checkexport()
{
    if (isset($_POST["export"]) && "" != $_POST["export"])     //export button is submitted
    {
        foreach ($_POST['select'] as $selected) {
            $string = str_replace("\r\n", '', $selected);
            $table = $string; // this is the tablename that you want to export to csv from mysql.
            exportMysqlToCsv($table);
        }
    }
}
