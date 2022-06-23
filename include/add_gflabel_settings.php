<?php defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

	if ( !current_user_can( 'install_plugins' ) ) {

		wp_die( __( 'You do not have sufficient permissions to access this page.', 'gfolders' ) );

	}



	global $gfolder_activated;

	global $gfolders_data, $wpdb, $gfolders_url, $gfolders_dir;

	$wpurl = get_bloginfo('wpurl');



if(isset($_POST["submit"])){



$gform_name = sanitize_text_field($_REQUEST['gform_name']);

$gf_label_name = sanitize_text_field($_REQUEST['gf_label_name']);

if($gf_label_name == ''){

	$gf_label_name = sanitize_text_field($_REQUEST['gf_label_name1']);

}



     global $wpdb, $table_prefix;

	 $sql_gflabel_table = $wpdb->prefix . "gfform_labels";

      // Only insert the example data if no data already exists



      $rows = array(

          array(

              'gfform_id' => $gform_name,

              'gflabel_name' => $gf_label_name,

          )

      );

      foreach($rows as $row) {

          $wpdb->insert($sql_gflabel_table, $row);

      }



	$label_listing_page = get_site_url() . '/wp-admin/admin.php?page=gform_labels';

	$redirect = add_query_arg( 'label-added', 'success', $label_listing_page );

	wp_redirect( $redirect );

	  

}

?>





<div class="wrap gfolders_setting_sec">



        <h2><?php echo "Add New Folder"; ?> </h2> 



<div class="gf_label_text"><?php _e('You can add folder to the desire form', 'gfolders'); ?>:</div>

</div>

<?php



		global $wpdb, $table_prefix;

		$gform_table = $wpdb->prefix . "gf_form";

		$gffolder_table = $wpdb->prefix . "gfform_labels";



		$gformLists = $wpdb->get_results("SELECT id, title FROM ".$gform_table." ORDER BY date_created ASC");

		//get folder names	

		$gformFolders = $wpdb->get_results("SELECT DISTINCT gflabel_name  FROM ".$gffolder_table." ORDER BY gflabel_name ASC");

?>

<form class="nav-tab-content gf_label_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">

<input type="hidden" name="gfolders_gf" value="<?php echo sanitize_text_field($_GET['t']); ?>" />

	<div class="form-group">

	<label for="sel1">Select Form:</label>
	<select name="gform_name" class="form-control" id="sel1">
	<?php 

		foreach($gformLists as $gformList){ ?>
				<option value="<?php echo $gformList->title ?>"><?php echo $gformList->title ?> </option>
	<?php } ?>
	</select>
	</div>

	

	<div class="form-group" id="gffolder-list">

	<label for="sel1">Select Folder:</label>

	<select name="gf_label_name1" class="form-control">

	<?php 

		foreach($gformFolders as $gformFolder){ ?>

				<option value="<?php echo $gformFolder->gflabel_name ?>"><?php echo $gformFolder->gflabel_name ?> </option>

	<?php } ?>

	</select>

	</div>

    

    <div class="input-group">
		<input type="checkbox" name="gf_custom_label"> <label for="sel1">If you want to add new folder name</label>	
	</div>

	<div class="input-group" id="new-folder-name">
		<label for="sel1">Type Folder Name:</label>	
		<input type="text" name="gf_label_name" placeholder="Type Folder Name" class="form-control gf_label_input">
	</div>

<p class="submit"><input type="submit" value="<?php _e('Save Changes', 'gfolders'); ?>" class="button button-primary" id="submit" name="submit"></p>

</form>

</div>






