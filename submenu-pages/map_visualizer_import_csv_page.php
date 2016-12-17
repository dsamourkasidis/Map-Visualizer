<?php
/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 4/7/2015
 * Time: 9:21 PM
 */

function map_visualizer_import_csv_page()
{
    include "CSV-import/map_visualizer_Quick-CSV-import.php"; // Have you class seperate from your working code.
    $validationerror = "";
    $import_result ="";
    $csv = new map_visualizer_Quick_CSV_import();
    if (isset($_POST["Go"]) && "" != $_POST["Go"]) //form was submitted
    {
        if (isset($_POST['importcsv_nonce']) && wp_verify_nonce($_POST['importcsv_nonce'], 'import_csv') && current_user_can('publish_posts'))
        {
            if ($_POST["Go"] == "Import it") {
                $csv->file_name = $_FILES['file_source']['tmp_name'];
                //start import now
                $csv->map_visualizer_import();
                if (empty($csv->error)) {
                    //Store file name in Imported_files.txt
                    global $wpdb;
                    $imported = $wpdb->get_col("SELECT Imported_files.Name FROM Imported_files");
                    if (!in_array($csv->table_name,$imported))
                    {
                        //Add file name to Imported_files table
                        $wpdb->insert('Imported_files',array('Name' => $csv->table_name));
                        //Give a success message
                        $import_result = "File Imported successfully";
                    }
                    else
                    {
                        $import_result = "File already imported";
                    }
                } else {
                    $import_result = $csv->error;
                }
            }
        }else
        {
            $validationerror = "You are not authorized to do that";
        }
    }
    ?>
    <style>
        .inputfile {
            background: #ffffff;
            border: 3px double #aaaaaa;
            -moz-border-left-colors: #aaaaaa #ffffff #aaaaaa;
            -moz-border-right-colors: #aaaaaa #ffffff #aaaaaa;
            -moz-border-top-colors: #aaaaaa #ffffff #aaaaaa;
            -moz-border-bottom-colors: #aaaaaa #ffffff #aaaaaa
            width: 250px;
        }

    </style>
    <head>
        <title>Quick CSV import</title>
    </head>
    <body bgcolor="#f2f2f2">
    <h2 align="left">Import CSV File</h2>
    <h4> Your CSV file must apply to the following rules:</h4>
<ul style="list-style:disc ">
    <li>The first row must contain all the Header names:
        <ol>
          <li>If you use geographical coordinates, the headers of *the first two column*
              must be named "Latitude" and "Longitude" respectively.
          </li>
          <li>If you use addresses, the *first column* must be named "Address"
            and the plugin will geocode the data accordingly.
          </li>
            <li>If you wish to use polygons, the *first column* must be named "Polygon".
            Format of polygon data should be of <a href="https://en.wikipedia.org/wiki/Well-known_text" target="_blank">"Well-known text"</a>.
            </li>
        </ol>
    </li>
    <li> The second row must contain the data type of each column:
        <ol>
            <li>Available data types include "INT", "FLOAT" and "TEXT".</li>
            <li>For "Latitude" and "Longitude" headers, the data type to be used is "FLOAT".</li>
            <li>For "Polygon" or "Address" headers, the data type to be used is "TEXT".</li>
        </ol>
    </li>
    <li> Sample CSV Files can be found in the plugin's directory</li>
</ul>
    <form method="post" enctype="multipart/form-data">
       <input type="file" name="file_source" id="file_source" class="inputfile " >
        <br>
        <input type="Submit" name="Go" value="Import it"
        onclick=" var s = document.getElementById('file_source'); if(null != s && '' == s.value) {alert('Define file name'); s.focus(); return false;}">
        <?php wp_nonce_field('import_csv','importcsv_nonce'); ?>
    </form>
    <span><?php echo esc_html($import_result); ?></span>
    <span><?php echo esc_html($validationerror); ?></span>
    </body>
<?php
}