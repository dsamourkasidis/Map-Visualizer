<?php
/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 4/28/2015
 * Time: 5:54 PM
 */
session_start();
function Create_New_File()
{
    if (isset($_POST["Next"])) {
        // Three step form
        if ($_POST["Next"] == "Next1") {
            $_SESSION['Headers_num'] = $_POST['Headers_num'];
            $_SESSION['Rows_num'] = $_POST['Rows_num'];
            //Checking if an available number of Headers is inserted
            if (intval($_POST['Headers_num']) == 0 or empty($_POST['Headers_num'])) {
                header("Location: " . $_SERVER['REQUEST_URI']);
                echo 'Number of Headers must be at least 1';
                unset($_POST["Next"]);
            }
            //Checking if number of Rows has been inserted
            if (empty($_POST['Rows_num'])) {
                header("Location: " . $_SERVER['REQUEST_URI']);
                echo 'Please Insert the number of Rows';
                unset($_POST["Next"]);
            }
        }
        if ($_POST["Next"] == "Next2") {
            $_SESSION['headers'] = $_POST['headers'];
            $_SESSION['types'] = $_POST['types'];
            //Checking if all Header Names are filled
            if (in_array('', $_POST['headers'], TRUE)) {
                $_POST["Next"] = "Next1";
                header("Location: " . $_SERVER['REQUEST_URI']);
                echo 'Please fill out all the Header Names' . "\r\n";
            }
            //Checking if Header Names are either Lat/Long, Address or Polygon
            if (($_POST['headers'][0] != 'Latitude') or ($_POST['headers'][1]) != 'Longitude') {
                if  ($_POST['headers'][0] != 'Address' and $_POST['headers'][0] != 'Polygon') {
                    $_POST["Next"] = "Next1";
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    echo 'Please enter one the available Header Names' . "\r\n";
                }
            }
            //Checking if all Data Types are filled
            if (in_array('', $_POST['types'], TRUE)) {
                $_POST["Next"] = "Next1";
                header("Location: " . $_SERVER['REQUEST_URI']);
                echo 'Please fill out all the Data Types';
            }
            //Checking if Data types are either TEXT, INT or FLOAT
            foreach ($_POST['types'] as $type) {
                if ($type != 'TEXT' and $type != 'INT' and $type != 'FLOAT') {
                    $_POST["Next"] = "Next1";
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    echo 'Please insert one of the available Data Types';
                }
            }
            //Checking if the correct data type for Lat/Long, Address and Polygon has been set
            if ($_POST['headers'][0] == 'Latitude') {
                if (($_POST['types'][0] != 'FLOAT') or ($_POST['types'][1] != 'FLOAT')) {
                    $_POST["Next"] = "Next1";
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    echo 'Latitude and Longitude must be FLOAT type';
                }
            }elseif ($_POST['headers'][0] == 'Address') {
                if ($_POST['types'][0] != 'TEXT') {
                    $_POST["Next"] = "Next1";
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    echo 'Address must be TEXT type';
                }
            }elseif ($_POST['headers'][0] == 'Polygon') {
                if ($_POST['types'][0] != 'TEXT') {
                    $_POST["Next"] = "Next1";
                    header("Location: " . $_SERVER['REQUEST_URI']);
                    echo 'Polygon must be TEXT type';
                }
            }
        }
        if ($_POST["Next"] == "Import") {
            $data = $_POST['data'];
            $table_name = $_POST['table_name'];
            //Checking if alla fields have been filled out
            if (in_array('', $_POST['data'], TRUE)) {
                $_POST["Next"] = "Next2";
                header("Location: " . $_SERVER['REQUEST_URI']);
                $error = 1;
            }
            $i = 0;
            $data_size = sizeof($data) - 1;
            do {
                foreach ($_SESSION['types'] as $headertype) {
                    $type = check_type($data[$i]);
                    //Checking if data entered agree to their data type
                    if ($type != $headertype) {
                        $_POST["Next"] = "Next2";
                        header("Location: " . $_SERVER['REQUEST_URI']);
                        $error = 2;
                    }
                    $i++;
                }
            } while ($i <= $data_size);
            if ($error == 1) {
                echo 'Please fill out all the fields';
            } elseif ($error == 2) {
                echo 'Enter the correct data type ';
            } else {
                import_to_db($table_name, $_SESSION['headers'],$_SESSION['types'], $data);
                unset($_POST["Next"]);
                header("Location: " . $_SERVER['REQUEST_URI']);
                echo "Table Created and stored successfully";
            }
        }
    }
    ?>
    <div>
        <?php if (empty($_POST["Next"])) { ?>
            <h2> This is the Create a new File page. Let's get Started</h2>
            <br>
            <h4>Insert the number of Headers and Rows your new CSV file will contain</h4>
            <form method="post" action="">
                <label> Number of Headers: <input type="text" name="Headers_num" size="5"></label>
                <br>
                <label> Number of Rows: <input type="text" name="Rows_num" size="5"></label>
                <br>
                <input type="submit" name="Next" value="Next1" width="5">
            </form>

        <?php } elseif ($_POST["Next"] == "Next1") { ?>
            <h4>Insert The Header Names and their type</h4>
            Available Data types: TEXT, INT, FLOAT
            <br>
            The first two Header Names must be "Latitude" and "Longitude" and their type FLOAT. Alternatively, you can have one Header named "Address" of "TEXT" type.
            If you are going to use polygons one Header is, also, necessary named "Polygon" of TEXT type.
            <form method="post" action="">
                <br>
                <table>
                    <tr>
                        <td>Header Names:</td>
                        <?php for ($i = 1; $i <= intval($_SESSION['Headers_num']); $i++) { ?>
                            <td><input type="text" name="headers[]" size="10"></td>
                        <?php } ?>
                    </tr>
                    <tr>
                        <td>Data type:
                            <?php for ($i = 1;
                            $i <= intval($_SESSION['Headers_num']);
                            $i++) {
                            ?>
                        <td><input type="text" name="types[]" size="5"></td>
                        <?php } ?>
                    </tr>
                </table>
                <br>
                <input type="submit" name="Next" value="Next2" width="10">
            </form>

        <?php } elseif ($_POST["Next"] == "Next2") { ?>
            <h4>Finally insert your data and your file name</h4>
            Your data must agree to the type of each Header you entered
            <form method="post" action="">
                <table id="tb1" cellspacing="0" style="width:auto;">
                    <thead>
                    <tr>
                        <?php foreach ($_SESSION['headers'] as $selected) { ?>
                            <th>
                                <?php echo $selected; ?>
                            </th>
                        <?php } ?>
                    </tr>
                    <tr>
                        <?php foreach ($_SESSION['types'] as $selected) { ?>
                            <th>
                                <?php echo $selected; ?>
                            </th>
                        <?php } ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for ($i = 1; $i <= intval($_SESSION['Rows_num']); $i++) { ?>
                        <tr>
                            <?php for ($j = 1; $j <= sizeof($_SESSION['headers']); $j++) { ?>
                                <td>
                                    <input type="text" name="data[]"
                                </td>
                            <?php } ?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <br>
                <label> File Name (without .csv extension): <input type="text" name="table_name" </label>
                <br>
                <input type="submit" name="Next" value="Import" width="10">
            </form>
        <?php
        } elseif ($_POST["Next"] == "Import") {
            foreach ($_SESSION['data'] as $data) {
                echo $data;
            }
        } ?>
    </div>
    <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            /*background-color: #0000cc;*/
        }

        th, td {
            padding: 15px;
        }


    </style>
<?php
}

function check_type($data)
{
    if (is_numeric($data)) {
        $float_value_of_var = floatval($data);
        if (intval($data) == $float_value_of_var) {
            return 'INT';
        } else {
            return 'FLOAT';
        }
    } else {
        return 'TEXT';
    }
}

function import_to_db($table_name, $headers,$types, $data)
{
    global $wpdb;
    $result3 = $wpdb->get_results("SHOW TABLES LIKE '" . $table_name . "'",ARRAY_A );
    if (empty($result3)) {
        for ($i = 0; $i < sizeof($headers); $i++) {
            $arr[] = "`" . $headers[$i] . "` ".$types[$i];
        }
        $sql = "CREATE TABLE IF NOT EXISTS " . $table_name . " (";
        $sql .= implode(", ", $arr);
        $sql .= ")";
        $wpdb->query($sql);

        $sql2 = "INSERT INTO " . $table_name . " (";
        for ($i = 0; $i < sizeof($headers); $i++) {
            $arr2[] = "" . $headers[$i] . "";
        }
        $sql2 .= implode(",", $arr2);
        $sql2 .= ") VALUES ";
        $i = 0;
        do {
            for ($j = 0; $j < sizeof($headers); $j++) {
                if ($j == (sizeof($headers) - 1)) {
                    $arr3[] = "'" . $data[$i] . "')";
                    $i++;
                } elseif ($j == 0) {
                    $arr3[] = "('" . $data[$i] . "'";
                    $i++;
                } else {
                    $arr3[] = "'" . $data[$i] . "'";
                    $i++;
                }
            }
        } while ($i < sizeof($data));
        $sql2 .= implode(",", $arr3);
        $wpdb->query($sql2);

        $myfile = fopen(dirname(__FILE__) . "/Imported_files.txt", "a") or die("Unable to open file!");
        $txt = $table_name . "\r\n";
        fwrite($myfile, $txt);
        fclose($myfile);
    } else {

    }
}
