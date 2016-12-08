<?php
/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 4/7/2015
 * Time: 9:21 PM
 */

function mv_import_csv_page()
{
    include "CSV-import/Quick-CSV-import.php"; // Have you class seperate from your working code.
    $validationerror = "";
    $import_result ="";
    $csv = new Quick_CSV_import();
    if (isset($_POST["Go"]) && "" != $_POST["Go"]) //form was submitted
    {
        if (isset($_POST['importcsv_nonce']) && wp_verify_nonce($_POST['importcsv_nonce'], 'import_csv') && current_user_can('publish_posts'))
        {
            if ($_POST["Go"] == "Import it") {
                $csv->file_name = $_FILES['file_source']['tmp_name'];
                //start import now
                $csv->import();
                if (empty($csv->error)) {
                    //Store file name in Imported_files.txt
                    $contents = file_get_contents(dirname(__FILE__) . "/Imported_files.txt");
                    if (strpos($contents, $csv->table_name . "\r\n") === false) {
                        $myfile = fopen(dirname(__FILE__) . "/Imported_files.txt", "a") or die("Unable to open file!");
                        $txt = $csv->table_name . "\r\n";
                        fwrite($myfile, $txt);
                        fclose($myfile);
                    }
                    //Give a success message
                    $import_result = "File Imported successfully";
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
            -moz-border-top-colors: #aaaaaa #ffffff #aaaaa;a;
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
    <p> <b>1)</b> The first row must contain the Header Names.<br>
        <b>2)</b> The second row must contain the data type for each Header. Available data types are "INT", "TEXT" and "FLOAT".<br>
        <b>3)</b> The first Header Names should be "Latitude" and "Longitude" of type "FLOAT". Alternatively, you can have one named "Address",
            of type "TEXT", containing the desired address according to the national post service of each country.
            Please notice that a combination of the two options is not supported.<br>
        <b>4)</b> If you wish to use polygons for visualization a single Header named "Polygon", of type "TEXT", will be used in the form of
        <a href="https://en.wikipedia.org/wiki/Well-known_text" target="_blank">"Well-known text"</a>.
        <br> Sample CSV Files can be found in the plugin's directory
    </p>
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