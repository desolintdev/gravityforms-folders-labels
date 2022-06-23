<?php defined( 'ABSPATH' ) or die( 'No script please!' );

	if ( !current_user_can( 'install_plugins' ) ) {

		wp_die( __( 'You do not have sufficient permissions to access this page.', 'gfolders' ) );

	}



	global $gfolder_activated;

	global $gflabels_data, $wpdb, $gflabels_url, $gflabels_dir;

	$wpurl = get_bloginfo('wpurl');
	

//if ( ! defined( 'ABSPATH' ) ) exit; 

     global $wpdb, $table_prefix;
	 $sql_gflabel_table = $wpdb->prefix . "gf_label_tags";
	 $sql_gfform_table = $wpdb->prefix . "gf_form";

    if (isset($_POST['genre'])){
       
    $label_name = sanitize_text_field( $_POST['genre'] );
  
    
    $items = $wpdb->get_results(
            "SELECT  gfform_id FROM ".$sql_gflabel_table." WHERE gf_gfolder= '$label_name'");
	$data['result'] = $items;
	echo  json_encode($data);	
    }	


if(isset($_POST["submit"])){
if(!wp_verify_nonce($_POST['gflabel_nonce'],'create_gflabel')){
	 wp_die('Our Site is protected!!');
}else{
$gform_name = sanitize_text_field($_REQUEST['gform_name']);
$gf_label_name = sanitize_text_field($_REQUEST['gf_label_name']);
if($gf_label_name == ''){
	$gf_label_name = sanitize_text_field($_REQUEST['gf_label_name1']);
}

     global $wpdb, $table_prefix;
	 $sql_gflabel_table = $wpdb->prefix . "gf_label_tags";
	
      $rows = array(
          array(
              'gf_gfolder' => $gf_label_name,
          )
      );
	
	$count = $wpdb->get_var("SELECT COUNT(*) FROM ".$sql_gflabel_table." WHERE gf_gfolder = '$gf_label_name'");
	$count_all = $wpdb->get_var("SELECT COUNT(*) FROM ".$sql_gflabel_table." WHERE gfform_id = ''");
	
	$label_listing_page = get_site_url() . '/wp-admin/admin.php?page=flgf_gform_labels';

	//if($count_all == 5 OR $count_all > 5){
		
	//	$redirect = add_query_arg( 'label-added', 'limit-reached', $label_listing_page );
	//	wp_redirect( $redirect );
		
//	}else{
	
		if($count > 0){
		$redirect = add_query_arg( 'label-added', 'failure', $label_listing_page );
		wp_redirect( $redirect );
			
		}else{
		  foreach($rows as $row) {
			  $wpdb->insert($sql_gflabel_table, $row);
		  }
		$redirect = add_query_arg( 'label-added', 'success', $label_listing_page );
		wp_redirect( $redirect );
	   }
//	}

}
}

?>



<div class="wrap gfolders_setting_sec">



<?php if ( filter_input( INPUT_GET, 'label-added' ) === 'success' ) { ?>

<div class="alert alert-success">
  <strong>Success!</strong> Label Successfully Added!
</div>

<?php }elseif(filter_input( INPUT_GET, 'label-added' ) === 'failure'){ ?>
	
<div class="alert alert-danger">
  <strong>Sorry!</strong> Label Already Exists!
</div>
	
<?php }elseif(filter_input( INPUT_GET, 'label-added' ) === 'limit-reached'){ ?>

<div class="alert alert-danger">
  <strong>Sorry!</strong> You've reached your maximum limit! You need to upgrade you version.
</div>
	
<?php }?>	
	
<div class="wrap gfolders_setting_sec">
        <h2><?php echo esc_html("Add New Label"); ?> </h2> 
<div class="gf_label_text"><?php _e('You can add label to the desire form', 'gfolders'); ?>:</div>
<!--</div>-->

<?php

		global $wpdb, $table_prefix;
		$gform_table = $wpdb->prefix . "gf_form";
		$gffolder_table = $wpdb->prefix . "gf_label_tags";
		$gformLists = $wpdb->get_results("SELECT id, title FROM ".$gform_table." ORDER BY date_created ASC");
		//get folder names	
		$gformFolders = $wpdb->get_results("SELECT DISTINCT gf_gfolder  FROM ".$gffolder_table." ORDER BY gf_gfolder ASC");

?>

<form class="nav-tab-content gf_label_form" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
<?php wp_nonce_field( 'create_gflabel', 'gflabel_nonce' );?>	

<!--<input type="hidden" name="gfolders_gf" value="<?php // echo wp_create_nonce($_GET['t']); ?>" />-->


	<div class="input-group" id="new-folder-name">
		<label for="sel1">Type Folder Name:</label>	
		<input type="text" name="gf_label_name" placeholder="Type Label Name" class="form-control gf_label_input" required>
	</div>

<p class="submit"><input type="submit" value="<?php _e('Create Label', 'gfolders'); ?>" class="button button-primary" id="submit" name="submit"></p>

</form>
</div>

        <h1 class="wp-heading-inline"><?php echo esc_html("Gravity Form Labels"); ?> <!--<a class="page-title-action" href="admin.php?page=gform_add_label">Add New</a>--></h1> 

<div class="gflabels-list">

<?php

$_table_list = new GF_Labels_Table();
echo '<form method="post">';
$_table_list->prepare_items();

echo '<input type="hidden" name="page" value="" />';
echo '<input type="hidden" name="section" value="gflabels" />';

$_table_list->views();
$_table_list->search_box( __( 'Search Labels', 'textdomain' ), 'key' );
$_table_list->display();
echo '</form>';	

?>


</div>




</div>






