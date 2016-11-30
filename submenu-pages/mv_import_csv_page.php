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


    $csv = new Quick_CSV_import();
    if (isset($_POST["Go"]) && "" != $_POST["Go"]) //form was submitted
    {
        $csv->file_name = $_FILES['file_source']['tmp_name'];
        //optional parameters
        $csv->use_csv_header = isset($_POST["use_csv_header"]);
        $csv->field_separate_char = $_POST["field_separate_char"][0];
        $csv->field_enclose_char = '"';
        $csv->field_escape_char = $_POST["field_escape_char"][0];

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
            echo "File Imported successfully";
        }

    } else
        $_POST["use_csv_header"] = 1;
    ?>
    <head>
        <title>Quick CSV import</title>
        <style>
            .edt {
                background: #ffffff;
                border: 3px double #aaaaaa;
                -moz-border-left-colors: #aaaaaa #ffffff #aaaaaa;
                -moz-border-right-colors: #aaaaaa #ffffff #aaaaaa;
                -moz-border-top-colors: #aaaaaa #ffffff #aaaaaa;
                -moz-border-bottom-colors: #aaaaaa #ffffff #aaaaaa;
                width: 350px;
            }

            .edt_30 {
                background: #ffffff;
                border: 3px double #aaaaaa;
                font-family: Courier;
                -moz-border-left-colors: #aaaaaa #ffffff #aaaaaa;
                -moz-border-right-colors: #aaaaaa #ffffff #aaaaaa;
                -moz-border-top-colors: #aaaaaa #ffffff #aaaaaa;
                -moz-border-bottom-colors: #aaaaaa #ffffff #aaaaaa;
                width: 30px;
            }
        </style>
    </head>
    <body bgcolor="#f2f2f2">
    <h2 align="left">Import CSV File</h2>
    <h4> Your CSV file must apply to the following rules</h4>
    <p> <b>1)</b> The first row must contain the Header Names.<br>
        <b>2)</b> The second row must contain the data type for each Header. Available data types are "INT", "TEXT" and "FLOAT".<br>
        <b>3)</b> The first Header Names should be "Latitude" and "longitude" of type "FLOAT". Alternatively, you can have one named "Address",
            of type "TEXT", containing the desired address according to the national post service of each country.
            Please notice that a combination of the two options is not supported.<br>
        <b>4)</b> If you wish to use polygons for visualization a single Header will be used in the form of
        <a href="https://en.wikipedia.org/wiki/Well-known_text" target="_blank">"Well-known text"</a>
        <br> Sample CSV Files can be found in the plugin's directory
    </p>
    <form method="post" enctype="multipart/form-data">
        <table border="0" align="left">
            <tr>
                <td>Source CSV file to import:</td>
                <td rowspan="30" width="10px">&nbsp;</td>
                <td><input type="file" name="file_source" id="file_source" class="edt" value="<?php $file_source ?>"></td>
            </tr>
            <tr>
                <td>Use CSV header:</td>
                <td><input type="checkbox" name="use_csv_header"
                           id="use_csv_header" <?= (isset($_POST["use_csv_header"]) ? "checked" : "") ?>/></td>
            </tr>
            <tr>
                <td>Separate char: (char to separate csv columns)</td>
                <td><input type="text" name="field_separate_char" id="field_separate_char" class="edt_30" maxlength="1"
                           value="<?= ("" != $_POST["field_separate_char"] ? htmlspecialchars($_POST["field_separate_char"]) : ",") ?>"/>
                </td>
            </tr>
            <tr>
                <td>Enclose char: (char to enclose csv fields) </td>
                <td><input type="text" name="field_enclose_char" id="field_enclose_char" class="edt_30" maxlength="1"
                           value="<?= ("" != $_POST["field_enclose_char"] ? htmlspecialchars($_POST["field_enclose_char"]) : htmlspecialchars("\"")) ?>"/>
                </td>
            </tr>
            <tr>
                <td>Escape char: (char to separate csv rows) </td>
                <td><input type="text" name="field_escape_char" id="field_escape_char" class="edt_30" maxlength="1"
                           value="<?= ("" != $_POST["field_escape_char"] ? htmlspecialchars($_POST["field_escape_char"]) : "\\") ?>"/>
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3" align="center"><input type="Submit" name="Go" value="Import it"
                                                      onclick=" var s = document.getElementById('file_source'); if(null != s && '' == s.value) {alert('Define file name'); s.focus(); return false;}">
                </td>
            </tr>
        </table>
    </form>
    <?= (!empty($csv->error) ? "<hr/>Errors: " . $csv->error : "") ?>
    </body>
<?php
}