<?php
/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 12/4/2016
 * Time: 4:57 PM
 */

function map_visualizer_create_file_page()
{
    $fixed_headers = $fixed_header_types = $Header_Names = $Header_types = $data =  [];
    $Header_Num = $Rows_Num = "";
    $err_Header = $err_Rows = $err_Types = $err_data = $err_data_match = $err_filename = $result = $validationerror = "";
    $step = $_POST["Step"];
    if (isset($step))
    {
        // Two step form
        if ($step == "Next")
        {
            if (isset($_POST['createcsv_nonce1']) && wp_verify_nonce($_POST['createcsv_nonce1'], 'create_csv1') && current_user_can('publish_posts'))
            {
            //Validation
                if (intval($_POST['Headers_num']) == 0 or empty($_POST['Headers_num'])) {
                    $err_Header = "Number of Headers must be at least 1";
                    unset($step);
                }

                if (empty($_POST['Rows_num']) || !is_numeric($_POST['Rows_num'])) {
                    $err_Rows = 'Number of Rows must be at least 1';
                    unset($step);
                }
                $Header_Num = intval($_POST['Headers_num']);
                $Rows_Num = intval($_POST['Rows_num']);
                $Data_Input = $_POST['Data_Input'];
                switch ($Data_Input)
                {
                    case "Lat_Long":
                        $fixed_headers[0] = "Latitude";
                        $fixed_headers[1] = "Longitude";
                        $fixed_header_types[0] = "FLOAT";
                        $fixed_header_types[1] = "FLOAT";
                        break;
                    case "Address":
                        $fixed_headers[0] = "Address";
                        $fixed_header_types[0] = "TEXT";
                        break;
                    case "Polygon":
                        $fixed_headers[0] = "Polygon";
                        $fixed_header_types[0] = "FLOAT";
                        break;
                }
            }else
            {
                $validationerror = "You are not authorized to do that";
            }
        }
        if ($step == "Create")
        {
            if (isset($_POST['createcsv_nonce2']) && wp_verify_nonce($_POST['createcsv_nonce2'], 'create_csv2') && current_user_can('publish_posts'))
            {
                //Sanitizing Header Names and Types
                for($i=0; $i<sizeof($_POST['headers']); $i++)
                {
                    $Header_Names[$i] = sanitize_text_field($_POST['headers'][$i]);
                    $Header_types[$i] = sanitize_text_field($_POST['types'][$i]);
                    //Checking if type is either TEXT, FLOAT or INT
                    if ($Header_types[$i] != 'TEXT' and $Header_types[$i] != 'INT' and $Header_types[$i] != 'FLOAT') {
                        $step = "Next";
                        $err_Types = 'Please insert one of the available Data Types';
                        break;
                    }
                }
                for($i=0; $i<sizeof($_POST['fixed_headers']);$i++)
                {
                    $fixed_headers[$i] = sanitize_text_field($_POST['fixed_headers'][$i]);
                    $fixed_header_types[$i] = sanitize_text_field($_POST['fixed_types'][$i]);
                }

                $total_header_names = array_merge($fixed_headers,$Header_Names);
                $total_header_types = array_merge($fixed_header_types,$Header_types);
                $data = $_POST['data'];
                $Header_Num = sizeof($Header_Names);
                $Rows_Num = sizeof($data)/sizeof($total_header_names);
                //Validation
                //Checking if fields are empty
                if (in_array('', $total_header_names, TRUE)) {
                    $step = "Next";
                    $err_Header =  'Please fill out all Header Names';
                }
                if (in_array('', $total_header_types, TRUE)) {
                    $step = "Next";
                    $err_Types =  'Please fill out all Data Types';
                }
                if (in_array('', $data, TRUE)) {
                    $step = "Next";
                    $err_data =  'Please fill out all Data';
                }
                if (!empty($_POST['table_name']))
                {
                    $file_name = sanitize_file_name($_POST['table_name']).'_csv';
                }
                else
                {
                    $err_filename = "Enter a Name for your file!";
                }
                //Checking if data matches with their type
                $i = 0;
                $data_size = sizeof($data) - 1;
                do {
                    foreach ($total_header_types as $headertype) {
                        $type = map_visualizer_check_type($data[$i]);
                        //Checking if data entered agree to their data type
                        if ($type != $headertype) {
                            $step = "Next";
                            $err_data_match = "Data don't match with their type!";
                            break 2;
                        }
                        $i++;
                        //Sanitizing data
                        if ($type == "TEXT")
                        {
                            $data[$i] = sanitize_text_field($data[$i]);
                        }
                    }
                } while ($i <= $data_size);

                if (($err_data =="") && ($err_Types =="") && ($err_Header =="") && ($err_data_match =="") && ($err_filename ==""))
                {
                    $result = map_visualizer_import_to_db($file_name, $total_header_names,$total_header_types, $data);
                    if($result == "Table created successfully")
                    {
                        $Header_Num = $Rows_Num = "";
                        unset($step);
                    }
                    else
                    {
                        $step = "Next";
                    }
                }
            }else
            {
                $validationerror = "You are not authorized to do that";
            }
        }
    }

?>
      <style>
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
        }
        .error {
        color: red;
        }
       </style>
<div>
    <?php if (empty($step)) { ?>
    <h2> This is the Create a new File page. Let's get Started</h2>
    <br>
    <h4>Insert the number of Headers, Rows and the Data type your new CSV file will contain</h4>
    <span><?php echo esc_html($result); ?></span>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]);?>">
        <label> Number of Headers:
          <input type="text" name="Headers_num" value="<?php echo esc_attr($Header_Num); ?>" size="5">
            <span class="error"> <?php echo esc_html($err_Header); ?></span>
        </label>
        <br>
        <label> Number of Rows:
          <input type="text" name="Rows_num" value="<?php echo esc_attr($Rows_Num); ?>" size="5">
          <span class="error"> <?php echo esc_html($err_Rows); ?></span>
        </label>
        <br>
        <label>Data Input:
          <select name="Data_Input">
            <option value="Lat_Long">Latitude/Longitude</option>
            <option value="Address">Address</option>
            <option value="Polygon">Polygon</option>
          </select>
        </label>
        <br>
        <input type="submit" name="Step" value="Next" width="5">
        <?php wp_nonce_field('create_csv1','createcsv_nonce1'); ?>
    </form>
    <?php
    } elseif ($step == "Next")
    {
    ?>
    <h4>Insert The Header Names and their type</h4>
    Available Data types: TEXT, INT, FLOAT
    <h4>Finally insert your data and your file name</h4>
    Your data must agree to the type of each Header you entered
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["REQUEST_URI"]);?>">
      <table id="tb1" cellspacing="0" style="width:auto;">
        <thead>
          <tr>
            <th> Header Names:</th>
            <?php
            foreach ($fixed_headers as $fixed_header)
            {
                ?>
                <th>
                  <input type="text" name="fixed_headers[]" value="<?php echo esc_attr($fixed_header); ?>" readonly>
                </th>
                <?php
            }
            for ($i=0; $i<$Header_Num; $i++)
            {
                ?>
                <th>
                  <input type="text" name="headers[]" value="<?php echo esc_attr($Header_Names[$i]) ?>" >
                </th>
            <?php
            }
            ?>
          </tr>
          <tr>
           <th>Data Type:</th>
            <?php
            foreach ($fixed_header_types as $fixed_header_type )
            {
                ?>
                <th>
                  <input type="text" name="fixed_types[]" value="<?php echo esc_attr($fixed_header_type); ?>" readonly>
                </th>
                <?php
            }
            for ($i=0; $i<$Header_Num; $i++)
            {
                ?>
                <th>
                  <input type="text" name="types[]" value="<?php echo esc_attr($Header_types[$i]) ?>" >
                </th>
                <?php
            }
            ?>
          </tr>
        </thead>
        <tbody>
          <?php for ($i = 0; $i < $Rows_Num; $i++) { ?>
            <tr>
            <td></td>
              <?php for ($j = 0; $j < $Header_Num + sizeof($fixed_headers); $j++) { ?>
                <td>
                  <input type="text" name="data[]" value="<?php echo esc_attr($data[$i*($Rows_Num+1)+$j]) ?>" >
                </td>
              <?php } ?>
            </tr>
          <?php } ?>
        </tbody>
      </table>
      <br>
      <label> File Name (without .csv extension): <input type="text" name="table_name" </label>
      <br>
      <input type="submit" name="Step" value="Create" width="10">
      <?php wp_nonce_field('create_csv2','createcsv_nonce2'); ?>
    </form>
    <br>
    <span class="error"><?php echo esc_html($err_Header); ?></span> <br>
    <span class="error"><?php echo esc_html($err_Types); ?></span> <br>
    <span class="error"><?php echo esc_html($err_data); ?></span> <br>
    <span class="error"><?php echo esc_html($err_data_match); ?></span> <br>
    <span class="error"><?php echo esc_html($err_filename); ?></span> <br>
    <span class="error"><?php echo esc_html($result); ?></span>
    <span><?php echo esc_html($validationerror); ?></span>
    <?php
    }
}

function map_visualizer_check_type($data)
{
    if (is_numeric($data)) {
        if (is_int($data)) {
            return 'INT';
        } else {
            return 'FLOAT';
        }
    } else {
        return 'TEXT';
    }
}

function map_visualizer_import_to_db($table_name, $headers,$types, $data)
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
        if ($wpdb->last_error != "")
        {
            return $wpdb->last_error;
        }
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
        if ($wpdb->last_error != "")
        {
            return $wpdb->last_error;
        }
        //Add to Imported_files table
        $wpdb->insert('Imported_files',array('Name' => $table_name));
        return "Table created successfully";
    }
    else
    {
        return "Table already exists";
    }
}