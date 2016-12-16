<?php

/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 4/8/2015
 * Time: 1:03 PM
 */
class map_visualizer_Quick_CSV_import
{
    var $table_name; //where to import to
    var $file_name;  //where to import from
    var $use_csv_header; //use first line of file OR generated columns names
    var $field_separate_char; //character to separate fields
    var $field_enclose_char; //character to enclose fields, which contain separator char into content
    var $field_escape_char;  //char to escape special symbols
    var $error; //error message
    var $arr_csv_columns; //array of columns
    var $arr_csv_types;
    var $arr_csv_data;
    var $table_exists; //flag: does table for import exist


    function map_visualizer_Quick_CSV_import($file_name = "")
    {
        $this->file_name = $file_name;
        $this->arr_csv_columns = array();
        $this->arr_csv_types = array();
        $this->arr_csv_data = array();

        $this->use_csv_header = true;
        $this->field_separate_char = ",";
        $this->field_enclose_char = '"';
        $this->field_escape_char = "\\";
        $this->table_exists = false;
    }


    function map_visualizer_import()
    {
        global $wpdb;
        if ($this->table_name == "")
            $this->table_name = str_replace('.', '_', $_FILES['file_source']['name']);
            $this->table_exists = false;
            $this->map_visualizer_create_import_table();

        if (empty($this->arr_csv_columns))
            $this->map_visualizer_get_csv_header_fields();
//        if ($this->arr_csv_columns[0] != 'Latitude' or $this->arr_csv_columns[1] != 'Longitude' or $this->arr_csv_columns[0] != 'latitude' or $this->arr_csv_columns[1] != 'longitude')
//            return $this->error = 'The first two headings of your file should be Latitude and Longitude';


        if ($this->table_exists and empty($this->error)) {
            $sql = "INSERT INTO " . $this->table_name . " (";
            for ($i = 0; $i < sizeof($this->arr_csv_columns); $i++) {
                $arr2[] = "" . $this->arr_csv_columns[$i] . "";
            }
            $sql .= implode(",", $arr2);
            $sql .= ") VALUES ";
            $i = 0;
            do {
                for ($j = 0; $j < sizeof($this->arr_csv_columns); $j++) {
                    if ($j == (sizeof($this->arr_csv_columns) - 1)) {
                        $arr3[] = "'" . $this->arr_csv_data[$i] . "')";
                        $i++;
                    } elseif ($j == 0) {
                        $arr3[] = "('" . $this->arr_csv_data[$i] . "'";
                        $i++;
                    } else {
                        $arr3[] = "'" . $this->arr_csv_data[$i] . "'";
                        $i++;
                    }
                }
            } while ($i < sizeof($this->arr_csv_data));
            $sql .= implode(",", $arr3);

            $wpdb->query($sql);
            $this->error = $wpdb ->last_error;
        }

    }


    //returns array of CSV file columns
    function map_visualizer_get_csv_header_fields()
    {
        $this->arr_csv_columns = array();
        $fpointer = fopen($this->file_name, "r");
        if ($fpointer) {
            $row = 1;
            while (($arr = fgetcsv($fpointer, 10 * 1024, $this->field_separate_char)) !== FALSE){

                if ($row == 1) {
                    if (is_array($arr) && !empty($arr)) {
                        if ($this->use_csv_header) {
                            foreach ($arr as $val)
                                if (trim($val) != "")
                                    $this->arr_csv_columns[] = $val;
                        } else {
                            $i = 1;
                            foreach ($arr as $val)
                                if (trim($val) != "")
                                    $this->arr_csv_columns[] = "column" . $i++;
                        }
                    }
                } else if ($row == 2) {
                    foreach ($arr as $val1) {
                        if (trim($val1) != "")
                            $this->arr_csv_types[] = $val1;
                    }
                } else {
                    foreach ($arr as $val2) {
                        if (trim($val2) != "")
                            $this->arr_csv_data[] = $val2;
                    }
                }
                $row++;
            }
                unset($arr);
                fclose($fpointer);

        } else
            $this->error = "file cannot be opened: " . ("" == $this->file_name ? "[empty]" :

                    @mysql_escape_string($this->file_name));

        return $this->arr_csv_columns;
    }


    function map_visualizer_create_import_table()
    {
        global $wpdb;
        $sql = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (";

        if (empty($this->arr_csv_columns))
            $this->map_visualizer_get_csv_header_fields();

        if (!empty($this->arr_csv_columns)) {
            $arr = array();
            for ($i = 0; $i < sizeof($this->arr_csv_columns); $i++)
                $arr[] = "`" . $this->arr_csv_columns[$i] . "` " . $this->arr_csv_types[$i] ;
            $sql .= implode(",", $arr);
            $sql .= ")";
            $wpdb->query($sql);
//            $res = @mysql_query($sql);
            $this->error = $wpdb ->last_error;
            $this->table_exists = "" == $wpdb ->last_error;
        }
    }

}

