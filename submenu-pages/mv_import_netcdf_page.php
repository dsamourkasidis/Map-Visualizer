<?php
/**
 * Created by PhpStorm.
 * User: dimitris
 * Date: 5/7/2015
 * Time: 7:28 PM
 */
// Check that the nonce is valid, and the user can edit this post.

function mv_import_netcdf_page(){
?>
    <h2>NETCDF import</h2>
    <p>The netcdf format supported is the two-dimensional. This means that the two dimensions should be Latitude and Longitude. The available types
    for your variables are string, integer and float. Again, the type of Latitude and Longitude must bee float. Please notice, that Address input and
    polygon shape are not supported for netcdf files. </p>
    <p>After the import is completed you can find your netcdf file on the "List All Files" page, with the visualization process being identical to the
    csv files.</p>
    A sample netcdf format can be found here:
    <a href="http://www.unidata.ucar.edu/software/netcdf/examples/programs/sfc_pres_temp.cdl" target="_blank">.nc (2-d netcdf)</a>
    <div>
        <br>
<form id="featured_upload" method="post" action="#" enctype="multipart/form-data">
	<input type="file" name="netcdf" id="netcdf"  multiple="false" />
	<input type="hidden" name="post_id" id="post_id" value="55" />
	<?php wp_nonce_field( 'netcdf', 'netcdf_nonce' ); ?>
<input id="submit_netcdf" name="submit_netcdf" type="submit" value="Upload" />
</form>
    </div>
    <div>
        <h4>Issues with Wordpress</h4>
        <b>1) Please make sure you have the latest Java installed on your machine</b>
        <br>
        <b>2) .nc Extension:</b>
        Wordpress does not allow the upload of unregistered file types due to security reasons. This means, you should add
        the .nc extension to the list of permitted extensions by following the instructions linked here:
        <a href="http://www.wpbeginner.com/wp-tutorials/how-to-add-additional-file-types-to-be-uploaded-in-wordpress/" target="_blank">Add .nc extension</a>.
        Alternatively, there is plugin that allows you to add new extension and can be downloaded here:
        <a href="https://wordpress.org/plugins/enhanced-media-library/" target="_blank">Enhanced Media Library Plugin</a>.
        <br>
        <b>3) File Size:</b>
        Wordpress has a limit for the maximum file size that can be uploaded, depending on the web hosting company you choose and the package you select.
        You can view it on the "Media Uploader" page and can increase it, if necessary, following the instructions linked here:
        <a href="http://www.wpbeginner.com/wp-tutorials/how-to-increase-the-maximum-file-upload-size-in-wordpress/" target="_blank">Increase file size</a>
    </div>
<?php
    if (
        isset( $_POST['netcdf_nonce'], $_POST['post_id'] )
        && wp_verify_nonce( $_POST['netcdf_nonce'], 'netcdf' )
        && current_user_can( 'edit_post', $_POST['post_id'] )
    ) {
        // The nonce was valid and the user has the capabilities, it is safe to continue.

        // These files need to be included as dependencies when on the front end.
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');

        // Let WordPress handle the upload.
        // Remember, 'netcdf' is the name of our file input in our form above.
        $attachment_id = media_handle_upload( 'netcdf', $_POST['post_id'] );
        $upload_dir = wp_upload_dir();
        $jav = 'java -jar '.plugin_dir_path(__FILE__) .'\netcdf-java\netcdf_to_csv.jar '.$upload_dir['path'].' '.$_FILES['netcdf']['name'].' 2>&1';
        exec($jav,$output);
//        echo "Java messages: ".$output[0];
        include "CSV-import/Quick-CSV-import.php";
        $csv = new Quick_CSV_import();
        $created_csv = str_replace('.nc','.csv',$_FILES['netcdf']['name']);
        $csv->file_name = $output[0];
        $csv->table_name = str_replace('.','_',$created_csv);
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
        }else
        {
            echo "CSV error: ".$csv->error;
        }
        if ( is_wp_error( $attachment_id ) ) {
            // There was an error uploading the netcdf.
            echo 'error';
        } else {
            // The netcdf was uploaded successfully!
            echo 'Uploaded';
        }

    } else {

        // The security check failed, maybe show the user an error.
    }

}
