<?php if ( ! defined( 'ABSPATH' ) ) exit; 
  /**
   * flgf_gravityform_labels_activate()
   *
   * Plugin Activation.
   *
   */

	if(!function_exists('flgf_gravityform_labels_activate')){

	function flgf_gravityform_labels_activate(){	

				flgf_create_tables();

		}	

	}



  /**
   * flgf_gravityforms_labels_deactivate()
   *
   * Plugin Deactivation.
   *
   */

	if(!function_exists('flgf_gravityforms_labels_deactivate')){

	function flgf_gravityforms_labels_deactivate(){	

			flgf_delete_tables();

		}

	}	



  /**
   * flgf_create_tables()
   *
   * Create the required table for the plugin.
   *
   * @return void
   */

   function flgf_create_tables() {

        global $wpdb, $table_prefix;
		$sql_gflabel_table = $wpdb->prefix . "gfform_labels";
	    $sql_gffolder_table = $wpdb->prefix . "gf_gfolders";
	    $sql_gflabel_tags_table = $wpdb->prefix . "gf_label_tags";

        $sql = "

            CREATE TABLE IF NOT EXISTS  ". $sql_gflabel_table ." (

              id int(7) NOT NULL auto_increment,

              gfform_id varchar(255) default NULL,

              gflabel_name varchar(255) default NULL,

              PRIMARY KEY  (id)

  
            )";
	   
	    $folder_sql = "

            CREATE TABLE IF NOT EXISTS  ". $sql_gffolder_table ." (

              id int(7) NOT NULL auto_increment,

              gfform_id varchar(255) default NULL,

              gf_gfolder varchar(255) default NULL,
			  
			  gfform_name varchar(255) default NULL,

              PRIMARY KEY  (id)

  
            )";
	   
	   $label_tags_sql = "

            CREATE TABLE IF NOT EXISTS  ". $sql_gflabel_tags_table ." (

              id int(11) NOT NULL auto_increment,

              gf_gfolder varchar(255) default NULL,

              PRIMARY KEY  (id)

  
            )";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );	

        dbDelta($sql);
	    dbDelta($folder_sql);
	   dbDelta($label_tags_sql);



     //  flgf_insert_sample_data();

    }





  /**
   * flgf_insert_sample_data()
   *
   * Insert some dummy data
   *
   * @return void
   */

   function flgf_insert_sample_data() {

     global $wpdb, $table_prefix;

	 $sql_gflabel_table = $wpdb->prefix . "gfform_labels";

      // Only insert the example data if no data already exists

      $sql = "

          SELECT
              id
          FROM
              ". $sql_gflabel_table ."
          LIMIT
              1";

	  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );			  

      $data_exists = $wpdb->get_var($sql);

      if ($data_exists) {

          return false;

      }else{

      // Insert example data



      $rows = array(
          array(
              'id' => 1,
              'gfform_id' => 2,
              'gflabel_name' => 'Awesome',
          )

      );

      foreach($rows as $row) {

          $wpdb->insert($sql_gflabel_table, $row);

      }

	}	

  }





  /**
   * flgf_delete_tables()
   *
   * Delete the tables which are required by the plugin.
   *
   * @return void
   */

   function flgf_delete_tables() {

     global $wpdb, $table_prefix;

	 $sql_gflabel_table = $wpdb->prefix . "gfform_labels";
	  $sql = "DROP TABLE IF EXISTS ". $sql_gflabel_table." ";
	  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' ); 

      $wpdb->query($sql);

  }

	add_filter( 'gform_addon_navigation', 'flgf_gform_folder_menu' );

	function flgf_gform_folder_menu( $menus ) {

	  $menus[] = array( 'name' => 'flgf_gform_folders',

	  					'label' => __( 'Manage Folders' ),

						'callback' =>  'flgf_gform_folders' );

	  return $menus;

	}


	add_filter( 'gform_addon_navigation', 'flgf_gform_label_menu' );

	function flgf_gform_label_menu( $menus ) {

	  $menus[] = array( 'name' => 'flgf_gform_labels',

	  					'label' => __( 'Manage Labels' ),

						'callback' =>  'flgf_gform_labels' );

	  return $menus;

	}



	function flgf_gform_folders(){ 

		if ( !current_user_can( 'install_plugins' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'gfolders' ) );
		}

		global $wpdb; 
		include('gffolder_settings.php');	

	}	


	function flgf_gform_labels(){ 

		if ( !current_user_can( 'install_plugins' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', 'gfolders' ) );
		}

		global $wpdb; 
		include('gflabel_settings.php');	

	}	





	function flgf_gform_new_label(){ 

		if ( !current_user_can( 'install_plugins' ) )  {

			wp_die( __( 'You do not have sufficient permissions to access this page.', 'gfolders' ) );

		}

		global $wpdb; 

		include('add_gflabel_settings.php');	

	}	







	function flgf_gfolders_plugin_setting($links) { 

		$settings_link = '<a href="admin.php?page=flgf_gform_labels">'.__('Settings', 'gfolders').'</a>';

		

			array_unshift($links, $settings_link); 

		

		return $links; 

	}

	

	function flgf_gfolders_registration_script() {

		wp_enqueue_script(

			'gflabel-scripts',

			plugins_url('js/scripts.js', dirname(__FILE__)),

			array('jquery')

		);	
	    wp_localize_script('gflabel-scripts', 'myScript', array(
    		'pluginsUrl' => plugins_url(),
		));	

		

		wp_enqueue_style( 'gflabel-fa-min', plugins_url('css/font-awesome.min.css', dirname(__FILE__)), array(), date('mhi') );
	}


//Delete Folder
add_action('wp_ajax_flgf_delgffolder', 'flgf_delgffolder');
add_action('wp_ajax_nopriv_flgf_delgffolder', 'flgf_delgffolder');

if (!function_exists('flgf_delgffolder')) { 
	function flgf_delgffolder() {


		 global $wpdb, $table_prefix;
		 $sql_gflabel_table = $wpdb->prefix . "gfform_labels";

		if ($_POST['folder_id']){

		$folder_id = sanitize_text_field( $_POST['folder_id'] );
		$items = $wpdb->query( "DELETE FROM ".$sql_gflabel_table." WHERE id=$folder_id" );


			if($items){
				echo "Done";
			}else{	
				echo "Error";
			}
		}
	}
}

//Delete Label
add_action('wp_ajax_flgf_delgflabel', 'flgf_delgflabel');
add_action('wp_ajax_nopriv_flgf_delgflabel', 'flgf_delgflabel');

if (!function_exists('flgf_delgflabel')) { 
	function flgf_delgflabel() {


		 global $wpdb, $table_prefix;
		 $sql_gflabel_tags_table = $wpdb->prefix . "gf_label_tags";
		 $sql_gffolder_table = $wpdb->prefix . "gf_gfolders"; 

		if ($_POST['label_id']){

		$folder_id = sanitize_text_field( $_POST['label_id'] );
		$items = $wpdb->query( "DELETE FROM ".$sql_gflabel_tags_table." WHERE id=$folder_id" );
		$del_frmgfolder = $wpdb->query( "DELETE FROM ".$sql_gffolder_table." WHERE gf_gfolder=$folder_id" );	


			if($items){
				echo "Dones";
			}else{	
				echo "Error";
			}
		}
	}
}

add_action('wp_ajax_flgf_updgffolder', 'flgf_updgffolder');
add_action('wp_ajax_nopriv_flgf_updgffolder', 'flgf_updgffolder');

if (!function_exists('flgf_updgffolder')) { 
	function flgf_updgffolder() {


		 global $wpdb, $table_prefix;
		 $sql_gflabel_table = $wpdb->prefix . "gfform_labels";
		 $sql_gfform_table = $wpdb->prefix . "gf_form";

		if ($_POST['folder_id']){

		$folder_id = sanitize_text_field($_POST['folder_id']);
		$folder_name = sanitize_text_field($_POST['folder_name']);


		$items = $wpdb->get_row(
				"SELECT  gflabel_name FROM ".$sql_gflabel_table." WHERE id= '$folder_id'");
		$Title = $items->gflabel_name;       

		$OK = $wpdb->update( $sql_gflabel_table, array( 'gflabel_name' => $folder_name),array('gflabel_name'=>$Title));



			if($OK){
				echo "Done";
			}else{	
				echo "Error";
			} 
		}
	}
}


add_action('wp_ajax_flgf_updatetags', 'flgf_updatetags');
add_action('wp_ajax_nopriv_flgf_updatetags', 'flgf_updatetags');

if (!function_exists('flgf_updatetags')) { 
	function flgf_updatetags() {


		 global $wpdb, $table_prefix;
		 $sql_gflabel_table = $wpdb->prefix . "gf_gfolders";

		if ($_POST['form_id']){

		$formid = sanitize_text_field($_POST['form_id']);
		$tagslist = sanitize_text_field($_POST['tagslist']);

		$OK = $wpdb->update( $sql_gflabel_table, array( 'gf_gfolder' => $tagslist),array('gfform_id'=>$formid));



			if($OK){
				echo "Done";
			}else{	
				echo "Error";
			} 
		}
	}
}



add_action('wp_ajax_flgf_addgftags', 'flgf_addgftags');
add_action('wp_ajax_nopriv_flgf_addgftags', 'flgf_addgftags');

if (!function_exists('flgf_addgftags')) { 
	function flgf_addgftags() {


		 global $wpdb, $table_prefix;
		 $sql_gflabel_table = $wpdb->prefix . "gf_gfolders";
		 $sql_gfform_table = $wpdb->prefix . "gf_form";

		if ($_POST['label_id']){

		$label_id = sanitize_text_field($_POST['label_id']);
		$folder_name = sanitize_text_field($_POST['folder_name']);
		$gfFormName = sanitize_text_field($_POST['gfFormName']);
		$arr_values = explode(',',$folder_name);
		if(!empty($folder_name)):	
		$items = $wpdb->get_row(
				"SELECT  gfform_id FROM ".$sql_gflabel_table." WHERE gfform_id= '$label_id'");
		$gfform_id = $items->gfform_id;	
		if($gfform_id > 0)	{
			$OK = $wpdb->update( $sql_gflabel_table, array( 'gf_gfolder' => $folder_name),array('gfform_id'=>$label_id));
		}else{
			$OK = $wpdb->query("INSERT INTO ".$sql_gflabel_table." (gfform_id, gf_gfolder, gfform_name) VALUES ('$label_id', '$folder_name','$gfFormName')"  );
		}	
		endif;		

			if($arr_values){
				echo $OK[0];
			}else{	
				echo "Error";
			} 
		}
	}
}


add_action('wp_ajax_flgf_updgflabel', 'flgf_updgflabel');
add_action('wp_ajax_nopriv_flgf_updgflabel', 'flgf_updgflabel');

if (!function_exists('flgf_updgflabel')) { 
	function flgf_updgflabel() {


		 global $wpdb, $table_prefix;
		 $sql_gflabel_table = $wpdb->prefix . "gf_label_tags";
		 $sql_gfform_table = $wpdb->prefix . "gf_form";

		if ($_POST['label_id']){

		$label_id = sanitize_text_field($_POST['label_id']);
		$label_name = sanitize_text_field($_POST['label_name']);


		$items = $wpdb->get_row(
				"SELECT  gf_gfolder FROM ".$sql_gflabel_table." WHERE id= '$label_id'");
		$Title = $items->gf_gfolder;       

		$OK = $wpdb->update( $sql_gflabel_table, array( 'gf_gfolder' => $label_name),array('gf_gfolder'=>$Title));



			if($OK){
				echo "Done";
			}else{	
				echo "Error";
			} 
		}
	}
}


add_action('wp_ajax_flgf_getallglabels', 'flgf_getallglabels');
add_action('wp_ajax_nopriv_flgf_getallglabels', 'flgf_getallglabels');

if (!function_exists('flgf_getallglabels')) { 
	function flgf_getallglabels(){

		 global $wpdb, $table_prefix;
		 $sql_gflabel_table = $wpdb->prefix . "gf_label_tags";
		 $sql_gfform_table = $wpdb->prefix . "gf_form";



		$items = $wpdb->get_results(
				"SELECT gf_gfolder FROM ".$sql_gflabel_table." ORDER BY gf_gfolder asc");


        $output = 'var colors = ';
        $all_rows = [] ;

        foreach($items as $item){
            $all_rows[] = $item->gf_gfolder;
            
        }
        /*
        while($row=mysqli_fetch_array($result)) 
        {    
            $all_rows[]=[$row['item1'],$row['item2'],$row['item3']];
        }
        */
        $output.= json_encode($all_rows).';';
        echo  $all_rows;

	}
}


add_action('wp_ajax_flgf_gfformslists', 'flgf_gfformslists');
add_action('wp_ajax_nopriv_flgf_gfformslists', 'flgf_gfformslists');

if (!function_exists('flgf_gfformslists')) { 
	function flgf_gfformslists(){

		 global $wpdb, $table_prefix;
		 $sql_gflabel_table = $wpdb->prefix . "gfform_labels";
		 $sql_gfform_table = $wpdb->prefix . "gf_form";



		$items = $wpdb->get_results(
				"SELECT  title FROM ".$sql_gfform_table." WHERE is_trash = 0 ORDER BY title asc");

	$html .= "<table><thead><tr><th>Forms</th></tr></thead>";		
		foreach($items as $item){ 
			$form_name = $item->title;
			if($form_name != ''){
				$form_name;
				$form_id = RGFormsModel::get_form_id($form_name);
			}else{
				$form_name = 'No form exists in the selected folder!';
			}
			$gform_edit_link = admin_url('admin.php?page=gf_edit_forms&id='.$form_id);
			$gform_preview_link = '/?gf_page=preview&id='.$form_id;
			$gform_entries_link = admin_url('admin.php?page=gf_entries&id='.$form_id);


		$html .="<tr>
			<td> $form_name <div class='row-actions'></span><span><a href='$gform_edit_link' target='_blank'>Edit</a> | <a href='$gform_preview_link' target='_blank'>Preview</a> | <a href='$gform_entries_link' target='_blank'>Entries</a></span></div> </td>
		</tr>";	
		}	
	$html .= "</table>";		
		echo $html;	

	}
}




add_action('wp_ajax_flgf_getformsbyid', 'flgf_getformsbyid');
add_action('wp_ajax_nopriv_flgf_getformsbyid', 'flgf_getformsbyid');


function flgf_getformsbyid(){
    

     global $wpdb, $table_prefix;
	 $sql_gflabel_table = $wpdb->prefix . "gfform_labels";
	 $sql_gfform_table = $wpdb->prefix . "gf_form";

    if ($_POST['genre']){
       
    $label_name = sanitize_text_field( $_POST['genre'] );
  
    
    $items = $wpdb->get_results(
            "SELECT $sql_gflabel_table.gfform_id as gformids FROM $sql_gflabel_table LEFT JOIN  $sql_gfform_table ON $sql_gflabel_table.gfform_id = $sql_gfform_table.title WHERE gflabel_name = '$label_name' AND gfform_id !='' AND is_trash = 0");
		

		
$html .= "<table><thead><tr><th>Forms</th></tr></thead>";		
    foreach($items as $item){ 
		$form_name = $item->gformids;
		if($form_name != ''){
		    $form_name;
			$form_id = RGFormsModel::get_form_id($form_name);
		}else{
		    $form_name = 'No form exists in the selected folder!';
		}
		$gform_edit_link = admin_url('admin.php?page=gf_edit_forms&id='.$form_id);
		$gform_preview_link = '/?gf_page=preview&id='.$form_id;
		$gform_entries_link = admin_url('admin.php?page=gf_entries&id='.$form_id);
		
 	$html .="<tr>
		<td> $form_name <div class='row-actions'></span><span><a href='$gform_edit_link' target='_blank'>Edit</a> | <a href='$gform_preview_link' target='_blank'>Preview</a> | <a href='$gform_entries_link' target='_blank'>Entries</a></span></div> </td>
	</tr>";	
    }	
$html .= "</table>";		
	echo $html;	
    
	}

}



add_action('wp_ajax_flgf_getformsbylabelid', 'flgf_getformsbylabelid');
add_action('wp_ajax_nopriv_flgf_getformsbylabelid', 'flgf_getformsbylabelid');


function flgf_getformsbylabelid(){
    

     global $wpdb, $table_prefix;
	 $sql_gflabel_table = $wpdb->prefix . "gf_gfolders";
	 $sql_gfform_table = $wpdb->prefix . "gf_form";

    if ($_POST['label']){
       
    $label_name = sanitize_text_field( $_POST['label'] );
  
    
    $items = $wpdb->get_results(
            "SELECT $sql_gflabel_table.gfform_name as gformids FROM $sql_gflabel_table LEFT JOIN  $sql_gfform_table ON $sql_gflabel_table.gfform_name = $sql_gfform_table.title WHERE gf_gfolder = '$label_name' AND gfform_id !='' AND is_trash = 0");
		

		
$html .= "<table><thead><tr><th>Forms</th></tr></thead>";		
    foreach($items as $item){ 
		$form_name = $item->gformids;
		if($form_name != ''){
		    $form_name;
			$form_id = RGFormsModel::get_form_id($form_name);
		}else{
		    $form_name = 'No form exists in the selected folder!';
		}
		$gform_edit_link = admin_url('admin.php?page=gf_edit_forms&id='.$form_id);
		$gform_preview_link = '/?gf_page=preview&id='.$form_id;
		$gform_entries_link = admin_url('admin.php?page=gf_entries&id='.$form_id);
		
 	$html .="<tr>
		<td> $form_name <div class='row-actions'></span><span><a href='$gform_edit_link' target='_blank'>Edit</a> | <a href='$gform_preview_link' target='_blank'>Preview</a> | <a href='$gform_entries_link' target='_blank'>Entries</a></span></div> </td>
	</tr>";	
    }	
$html .= "</table>";		
	echo $html;	
    
	}

}



add_action('wp_ajax_flgfpostgffolder', 'flgfpostgffolder');
add_action('wp_ajax_nopriv_flgfpostgffolder', 'flgfpostgffolder');

function flgfpostgffolder(){
	

     global $wpdb, $table_prefix;
	 $sql_gflabel_table = $wpdb->prefix . "gfform_labels";
	 $sql_gfform_table = $wpdb->prefix . "gf_form";

    if ($_POST['folder']){
       
    $folder_name = sanitize_text_field( $_POST['folder'] );
    $form_name = sanitize_text_field( $_POST['form'] );
		
	$form_check = $wpdb->get_var("SELECT COUNT(*) FROM ".$sql_gflabel_table." WHERE gfform_id = '$form_name'");
		
	$count = $wpdb->get_var("SELECT COUNT(*) FROM ".$sql_gflabel_table." WHERE gfform_id = '$form_name'");
	$all_check = $wpdb->get_var("SELECT COUNT(*) FROM ".$sql_gflabel_table." WHERE gfform_id = '$form_name' AND gflabel_name='$folder_name'");	
		
		if($all_check > 0){
			$false_msg = "This form is already assigned to ".$folder_name.".";
		}else{	
		if($count > 0 ){
			//$false_msg = "This is already Exist!";
			$rec_update = $wpdb->update(
				$sql_gflabel_table, 
				array( 
					'gflabel_name' => $folder_name    ), 
				array(
					"gfform_id" => $form_name
				) 		
			);	
			
			if($rec_update){
				$false_msg = "This form is assigned to ".$folder_name.".";
			}	
		} else{
    
    $items = $wpdb->query("INSERT INTO ".$sql_gflabel_table." (gfform_id, gflabel_name) VALUES ('$form_name', '$folder_name')"  );
	}	}	
    	if($items){
    	    $msg =  "This form is assigned to ".$folder_name.".";
			echo $msg;
    	}else{	
            echo $false_msg;
    	}
			
    }
	
	
}


add_action( 'admin_footer', 'gf_footer_scripts' );
function gf_footer_scripts(){
    
?>

<script>
	
	jQuery(function ($) {
		var BaseURL = $(location).attr('protocol')+ '//' + $(location).attr('hostname');
		var pluginUrl = '<?php echo plugin_dir_path( __FILE__ ) ?>' ;
		console.log(BaseURL);
		jQuery('.typeahead').on("focus", function() {  
			var tags = $(this).tagsManager();
			$(this).typeahead({
			  source: function (query, process) {
				return $.get(BaseURL + '/wp-content/plugins/manage-folders-and-labels/include/getall_labels.php', { query: query }, function (data) {
				    console.log(data);
				  data = $.parseJSON(data);
				  return process(data);
				});
			  },
			  afterSelect :function (item){
				tags.tagsManager("pushTag", item);
			  }
			}); 
		});
	});	
	  
	jQuery(document).ready(function($){
		aajaxurl = '<?php echo admin_url( 'admin-ajax.php' ) ?>';
		jQuery('.folderlist').on('change', function() {
			var folder_title = $(this).val(); 
			var form_title = $(this).next().val();
			$(this).removeAttr("selected"); 
			$.ajax({
				url: aajaxurl,
				data: { action: "flgfpostgffolder", folder: folder_title, form: form_title },
				type: 'post',
				success: function(response) {
					response = response.slice(0, - 1);
					$("#message").show();

					$("#message p").text(response);
															
						$("#message").show("slow").delay(3000).hide("slow");
				}

			});		
		 jQuery( "<div id='message' class='updated notice is-dismissible'><p></p></div>" ).insertAfter( ".gf_browser_chrome h2" );
			$("#message").hide(); 	

		}); 
		

    $(".add-gftags").click(function() {
		
    var label_id = $(this).data("labelbtnid");
    var comb_tags = "";
		
		var inputID = 'input[name="hidden-gftags-'+label_id+'"]';
		var gfFormNameID = '#gf-name-'+ label_id;
		var gfLabelsList = '#gf-tagslist-'+ label_id;
		
    var newVal = $('body').find(inputID).val();
		var gfFormName = $('body').find(gfFormNameID).val();
		var prev_tags = $('body').find(gfLabelsList).val();
		var newVal = $(inputID).val();
		if(prev_tags != ""){
			var comb_tags = prev_tags + ',' + newVal; 
		}else{
			var comb_tags = newVal;
		}

        $.ajax({
            type: 'post',
            url:  aajaxurl,
            data: { action: "flgf_addgftags", label_id: label_id, folder_name: comb_tags, gfFormName: gfFormName },
        success: function(response) {
            if(response != "Error0") {
            }
            else {
               console.log(JSON.stringify(error));
            }
         }
            
        });
    });	    
			

	function removeValue(list, value) {
	  list = list.split(',');
	  list.splice(list.indexOf(value), 1);
	  return list.join(',');
	}	
	
	//Delete Tags from database
    $(".remove-gftag").click(function() {
		
        var form_id = $(this).data("removegfid");
		var tagVal = $(this).data("value");
		var dataid = $(this).data("id");
		var inputID = '#tag-'+ form_id;
		var taglistID = '#gf-tagslist-'+ dataid;
		var listfield = $('body').find(taglistID);
        var newVal = $('body').find(inputID).hide();
		var taglistVal = $('body').find(taglistID).val();
		
		
		var finalResult = removeValue(taglistVal,tagVal);
		$('body').find(taglistID).val(finalResult);
		var getUpdatedTag = $('body').find(taglistID).val();
		
		
        $.ajax({
            type: 'post',
            url:  aajaxurl,
            data: { action: "flgf_updatetags", tagslist: getUpdatedTag, form_id: dataid },
        success: function(response) {
            if(response == "Done0") {
            }
            else {
               console.log(JSON.stringify(error));
            }
         }
            
        });
    });	    
		
		
	}); 	

  </script>


<?php }


function flgf_loading_scripts() {
	
	
//	$plugin_dir_js1 =  plugins_url('js/jquery.min.js', dirname(__FILE__));
	$plugin_dir_css =  plugins_url('css/admin-style.css', dirname(__FILE__));
	
	//echo '<link rel="stylesheet" type="text/css" href="'.plugins_url('css/bootstrap.min.css', dirname(__FILE__)).'">';
	echo '<link rel="stylesheet" type="text/css" href="'.plugins_url('css/tagmanager.min.css', dirname(__FILE__)).'">';
	echo '<link rel="stylesheet" type="text/css" href="'.$plugin_dir_css.'">';
//	echo '<script type="text/javascript" src="'.$plugin_dir_js1.'"></script>';
	echo '<script type="text/javascript" src="'.plugins_url('js/bootstrap.min.js', dirname(__FILE__)).'"></script>';
	echo '<script type="text/javascript" src="'.plugins_url('js/tagmanager.min.js', dirname(__FILE__)).'"></script>';
	echo '<script src="'.plugins_url('js/bootstrap3-typeahead.min.js', dirname(__FILE__)).'"></script>';
	
}
add_action('admin_head', 'flgf_loading_scripts');


add_action( 'init', 'flgf_gfolder_admin_script' );		


	function flgf_gfolder_admin_script() {


		global $css_arr;
		

		wp_register_style('gflabel-admin', plugins_url('css/admin-style.css', dirname(__FILE__)));
		wp_enqueue_style( 'gflabel-admin' );
		
		wp_register_script( "gflabel-scripts", plugins_url('js/admin-scripts.js?t='.time(), dirname(__FILE__)), array('jquery') );
		
		
		wp_register_script( "amai_woordjes_updaten", plugins_url('js/tagcomplete.min.js?t='.time(), dirname(__FILE__)), array('jquery') );
		wp_localize_script( 'gflabel-scripts', 'gfDel', array( 'gfdelajaxurl' => admin_url( 'admin-ajax.php' ))); 
		
		wp_localize_script( 'gflabel-scripts', 'flgfpostFolder', array( 'flgfpostfolderajaxurl' => admin_url( 'admin-ajax.php' ))); 

		wp_localize_script( 'gflabel-scripts', 'updategffolder', array( 'gfupdateajaxurl' => admin_url( 'admin-ajax.php' ))); 

		wp_localize_script( 'gflabel-scripts', 'getAllgforms', array( 'getAllgformsajaxurl' => admin_url( 'admin-ajax.php' ))); 

		wp_localize_script( 'gflabel-scripts', 'getgformsbyid', array( 'getgformsbyidajaxurl' => admin_url( 'admin-ajax.php' ))); 

		wp_enqueue_script( 'gflabel-scripts' );
		wp_enqueue_script( 'amai_woordjes_updaten' );
		

}
add_action( 'wp_enqueue_scripts', 'flgf_gfolder_admin_script' );

add_action( 'admin_enqueue_scripts', 'flgf_gfolder_admin_script' );
add_action( 'gform_enqueue_scripts', 'flgf_gfolder_admin_script', 10, 2 );
add_filter( 'gform_noconflict_scripts', 'register_script' );


if ( ! class_exists( 'WP_List_Table' ) ) {

    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

}



//For Labels
class GF_Labels_Table extends WP_List_Table {



    /**
     * Initialize the table list.
     */

    public function __construct() {

        parent::__construct( array(

            'singular' => __( 'gflabel', 'textdomain' ),

            'plural'   => __( 'gflabels', 'textdomain' ),

            'ajax'     => false

        ) );

    }



    /**
     * Get list columns.
     *
     * @return array
     */

    public function get_columns() {

        return array(

            'cb'            => '<input type="checkbox" />',
            'gflabel'         => __( 'Label Name', 'textdomain' ),

        );

    }


    /**
     * Column cb.
     */
    public function column_cb( $gflabel ) {
    //  return sprintf( '<input type="checkbox" name="gflabel[]" value="%1$s" />', $gflabel['id'] );
		return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $gflabel['gf_gfolder']);
    }

	function column_id( $item ) {
		global $ad_code_manager;
		$output = '<div id="inline_' . $item['post_id'] . '" style="display:none;">';
		$output .= '<div class="id">' . $item['post_id'] . '</div>';
		// Build the fields for the conditionals
		$output .= '<div class="acm-conditional-fields"><div class="form-new-row">';
		$output .= '<h4 class="acm-section-label">' . __( 'Conditionals', 'ad-code-manager' ) . '</h4>';
		if ( !empty( $item['conditionals'] ) ) {
			foreach ( $item['conditionals'] as $conditional ) {
				$function = $conditional['function'];
				$arguments = $conditional['arguments'];
				$output .= '<div class="conditional-single-field"><div class="conditional-function">';
				$output .= '<select name="acm-conditionals[]">';
				$output .= '<option value="">' . __( 'Select conditional', 'ad-code-manager' ) . '</option>';
				foreach ( $ad_code_manager->whitelisted_conditionals as $key ) {
					$output .= '<option value="' .  esc_attr( $key ) . '" ' . selected( $function, $key, false ) . '>';
					$output .= esc_html( ucfirst( str_replace( '_', ' ', $key ) ) );
					$output .= '</option>';
				}
				$output .= '</select>';
				$output .= '</div><div class="conditional-arguments">';
				$output .= '<input name="acm-arguments[]" type="text" value="' . esc_attr( implode( ';', $arguments ) ) .'" size="20" />';
				$output .= '<a href="#" class="acm-remove-conditional">Remove</a></div></div>';
			}
		}
		$output .= '</div><div class="form-field form-add-more"><a href="#" class="button button-secondary add-more-conditionals">' . __( 'Add more', 'ad-code-manager' ) . '</a></div>';
		$output .= '</div>';
		// Build the fields for the normal columns
		$output .= '<div class="acm-column-fields">';
		$output .= '<h4 class="acm-section-label">' . __( 'URL Variables', 'ad-code-manager' ) . '</h4>';
		foreach ( (array)$item['url_vars'] as $slug => $value ) {
			$output .= '<div class="acm-section-single-field">';
			$column_id = 'acm-column[' . $slug . ']';
			$output .= '<label for="' . esc_attr( $column_id ) . '">' . esc_html( $slug ) . '</label>';
			// Support for select dropdowns
			$ad_code_args = wp_filter_object_list( $ad_code_manager->current_provider->ad_code_args, array( 'key' => $slug ) );
			$ad_code_arg = array_shift( $ad_code_args );
			if ( isset( $ad_code_arg['type'] ) && 'select' == $ad_code_arg['type'] ) {
				$output .= '<select name="' . esc_attr( $column_id ) . '">';
				foreach ( $ad_code_arg['options'] as $key => $label ) {
					$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_attr( $label ) . '</option>';
				}
				$output .= '</select>';
			} else {
				$output .= '<input name="' . esc_attr( $column_id ) . '" id="' . esc_attr( $column_id ) . '" type="text" value="' . esc_attr( $value ) . '" size="20" aria-required="true">';
			}
			$output .= '</div>';
		}
		$output .= '</div>';
		// Build the field for the priority
		$output .= '<div class="acm-priority-field">';
		$output .= '<h4 class="acm-section-label">' . __( 'Priority', 'ad-code-manager' ) . '</h4>';
		$output .= '<input type="text" name="priority" value="' . esc_attr( $item['priority'] ) . '" />';
		$output .= '</div>';
		// Build the field for the logical operator
		$output .= '<div class="acm-operator-field">';
		$output .= '<h4 class="acm-section-label">' . __( 'Logical Operator', 'ad-code-manager' ) . '</h4>';
		$output .= '<select name="operator">';
		$operators = array(
			'OR'     => __( 'OR', 'ad-code-manager' ),
			'AND'    => __( 'AND', 'ad-code-manager' ),
		);
		foreach ( $operators as $key => $label ) {
			$output .= '<option ' . selected( $item['operator'], $key ) . '>' . esc_attr( $label ) . '</option>';
		}
		$output .= '</select>';
		$output .= '</div>';

		$output .= '</div>';
		return $output;
	}	
	
	
	

    /**
     * Return gflabel column
     */

    public function column_gflabel( $gflabel ) {

        return  "<div class='folder' id='label-id-" . $gflabel['id'] . "'>". $gflabel['gf_gfolder'] ."</div><div class='label-edit-div' style='display:none;' id='label-edit-" . $gflabel['id'] . "'><input class='glabelval' id='" . $gflabel['id'] . "' type='text' value=". $gflabel['gf_gfolder'] ."></div><div class='row-actions'></span><span class='trash'><a href='javascript:void(0)' class='delete-label' data-labelid=".$gflabel['id'].">Delete</a> | </span><span class='inline-edit'><a href='javascript:void(0)' class='edit-label' data-labelid=".$gflabel['id'].">Edit</a><a style='display:none;' href='javascript:void(0)' class='update-label-btn' data-labelid=".$gflabel['id'].">Update</a> | </span></div>";

    }




	public function get_sortable_columns()	{
			$sortable_columns = array(
				'gflabel' => array( 'gf_gfolder', false )
			);
			return $sortable_columns;
		}	

	

    /**
     * Prepare table list items.
     */

    public function prepare_items() {

     global $wpdb, $table_prefix;
	 $sql_gflabel_table = $wpdb->prefix . "gf_label_tags";
	 $sql_gfform_table = $wpdb->prefix . "gf_form";

        $per_page = 10;
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();


        // Column headers
        $this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		
        $current_page = $this->get_pagenum();
        if ( 1 < $current_page ) {
            $offset = $per_page * ( $current_page - 1 );
        } else {
            $offset = 0;
        }

        $search = '';



        if ( ! empty( $_REQUEST['s'] ) ) {
            $search = "AND gf_gfolder LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%' ";
        }


        $items = $wpdb->get_results(
            "SELECT id, gf_gfolder FROM ".$sql_gflabel_table." WHERE 1 = 1 {$search}" .
            $wpdb->prepare( "GROUP BY gf_gfolder ORDER BY id DESC LIMIT %d OFFSET %d;", $per_page, $offset ), ARRAY_A
        );

		function flgf_usort_reorder($a,$b){
	//	  $count = ""; 
		  $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
		  $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
		  $result = strnatcmp($a[$orderby], $b[$orderby]); //Determine sort order
		  return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
		}	
		usort($items, 'flgf_usort_reorder');
      //  if($search){
            $count = $wpdb->get_var( "SELECT COUNT(id) FROM ".$sql_gflabel_table." WHERE 1 = 1 {$search};" );
            $this->items = $items;
        
        // Set the pagination
        $this->set_pagination_args( array(
            'total_items' => $count,
            'per_page'    => $per_page,
            'total_pages' => ceil( $count / $per_page )
        ) );
        //}
    }
	
	
	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions()
	{
		$actions = ['bulk-delete' => 'Delete'];
		return $actions;
	}
	
	public static function flgf_delete_label_records($id)

	{
     global $wpdb, $table_prefix;
	 $sql_gflabel_table = $wpdb->prefix . "gf_label_tags";
		$wpdb->delete($sql_gflabel_table, ['gf_gfolder' => $id]);
	}	
	
	
	public function process_bulk_action() 
	{
		if ( 'delete' === $this->current_action() ) {	    
			self::flgf_delete_label_records( absint( $_GET['record'] ) );
		}

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )) {
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
			foreach ( $delete_ids as $id ) {
				self::flgf_delete_label_records( $id );
			}
		}
	}	
		

	function inline_edit() {
?>
	<form method="POST" action="<?php echo admin_url( 'admin-ajax.php' ); ?>"><table style="display: none"><tbody id="inlineedit">
		<tr id="inline-edit" class="inline-edit-row" style="display: none"><td colspan="<?php echo $this->get_column_count(); ?>" class="colspanchange">
			<fieldset><div class="inline-edit-col">
				<input type="hidden" name="id" value="" />
				<input type="hidden" name="action" value="acm_admin_action" />
				<input type="hidden" name="method" value="edit" />
				<input type="hidden" name="doing_ajax" value="true" />
				<?php wp_nonce_field( 'acm-admin-action', 'nonce' ); ?>
				<div class="acm-float-left">
				<div class="acm-column-fields"></div>
				<div class="acm-priority-field"></div>
				<div class="acm-operator-field"></div>
				</div>
				<div class="acm-conditional-fields"></div>
				<div class="clear"></div>
			</div></fieldset>
		<p class="inline-edit-save submit">
			<?php $cancel_text = __( 'Cancel', 'ad-code-manager' ); ?>
			<a href="#inline-edit" title="<?php echo esc_attr( $cancel_text ); ?>" class="cancel button-secondary alignleft"><?php echo esc_html( $cancel_text ); ?></a>
			<?php $update_text = __( 'Update', 'ad-code-manager' ); ?>
			<a href="#inline-edit" title="<?php echo esc_attr( $update_text ); ?>" class="save button-primary alignright"><?php echo esc_html( $update_text ); ?></a>
			<img class="waiting" style="display:none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />

			<span class="error" style="display:none;"></span>
			<br class="clear" />
		</p>
		</td></tr>
		</tbody></table></form>
	<?php
	}
	
	
	
}


class GF_Folders_Table extends WP_List_Table {



    /**

     * Initialize the table list.

     */

    public function __construct() {

        parent::__construct( array(

            'singular' => __( 'gflabel', 'textdomain' ),

            'plural'   => __( 'gflabels', 'textdomain' ),

            'ajax'     => false

        ) );

    }



    /**
     * Get list columns.
     *
     * @return array
     */

    public function get_columns() {

        return array(

            'cb'            => '<input type="checkbox" />',
            'gflabel'         => __( 'Folder Name', 'textdomain' ),

        );

    }


    /**
     * Column cb.
     */
    public function column_cb( $gflabel ) {
    //  return sprintf( '<input type="checkbox" name="gflabel[]" value="%1$s" />', $gflabel['id'] );
		return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $gflabel['gflabel_name']);
    }

	function column_id( $item ) {
		global $ad_code_manager;
		$output = '<div id="inline_' . $item['post_id'] . '" style="display:none;">';
		$output .= '<div class="id">' . $item['post_id'] . '</div>';
		// Build the fields for the conditionals
		$output .= '<div class="acm-conditional-fields"><div class="form-new-row">';
		$output .= '<h4 class="acm-section-label">' . __( 'Conditionals', 'ad-code-manager' ) . '</h4>';
		if ( !empty( $item['conditionals'] ) ) {
			foreach ( $item['conditionals'] as $conditional ) {
				$function = $conditional['function'];
				$arguments = $conditional['arguments'];
				$output .= '<div class="conditional-single-field"><div class="conditional-function">';
				$output .= '<select name="acm-conditionals[]">';
				$output .= '<option value="">' . __( 'Select conditional', 'ad-code-manager' ) . '</option>';
				foreach ( $ad_code_manager->whitelisted_conditionals as $key ) {
					$output .= '<option value="' .  esc_attr( $key ) . '" ' . selected( $function, $key, false ) . '>';
					$output .= esc_html( ucfirst( str_replace( '_', ' ', $key ) ) );
					$output .= '</option>';
				}
				$output .= '</select>';
				$output .= '</div><div class="conditional-arguments">';
				$output .= '<input name="acm-arguments[]" type="text" value="' . esc_attr( implode( ';', $arguments ) ) .'" size="20" />';
				$output .= '<a href="#" class="acm-remove-conditional">Remove</a></div></div>';
			}
		}
		$output .= '</div><div class="form-field form-add-more"><a href="#" class="button button-secondary add-more-conditionals">' . __( 'Add more', 'ad-code-manager' ) . '</a></div>';
		$output .= '</div>';
		// Build the fields for the normal columns
		$output .= '<div class="acm-column-fields">';
		$output .= '<h4 class="acm-section-label">' . __( 'URL Variables', 'ad-code-manager' ) . '</h4>';
		foreach ( (array)$item['url_vars'] as $slug => $value ) {
			$output .= '<div class="acm-section-single-field">';
			$column_id = 'acm-column[' . $slug . ']';
			$output .= '<label for="' . esc_attr( $column_id ) . '">' . esc_html( $slug ) . '</label>';
			// Support for select dropdowns
			$ad_code_args = wp_filter_object_list( $ad_code_manager->current_provider->ad_code_args, array( 'key' => $slug ) );
			$ad_code_arg = array_shift( $ad_code_args );
			if ( isset( $ad_code_arg['type'] ) && 'select' == $ad_code_arg['type'] ) {
				$output .= '<select name="' . esc_attr( $column_id ) . '">';
				foreach ( $ad_code_arg['options'] as $key => $label ) {
					$output .= '<option value="' . esc_attr( $key ) . '" ' . selected( $value, $key, false ) . '>' . esc_attr( $label ) . '</option>';
				}
				$output .= '</select>';
			} else {
				$output .= '<input name="' . esc_attr( $column_id ) . '" id="' . esc_attr( $column_id ) . '" type="text" value="' . esc_attr( $value ) . '" size="20" aria-required="true">';
			}
			$output .= '</div>';
		}
		$output .= '</div>';
		// Build the field for the priority
		$output .= '<div class="acm-priority-field">';
		$output .= '<h4 class="acm-section-label">' . __( 'Priority', 'ad-code-manager' ) . '</h4>';
		$output .= '<input type="text" name="priority" value="' . esc_attr( $item['priority'] ) . '" />';
		$output .= '</div>';
		// Build the field for the logical operator
		$output .= '<div class="acm-operator-field">';
		$output .= '<h4 class="acm-section-label">' . __( 'Logical Operator', 'ad-code-manager' ) . '</h4>';
		$output .= '<select name="operator">';
		$operators = array(
			'OR'     => __( 'OR', 'ad-code-manager' ),
			'AND'    => __( 'AND', 'ad-code-manager' ),
		);
		foreach ( $operators as $key => $label ) {
			$output .= '<option ' . selected( $item['operator'], $key ) . '>' . esc_attr( $label ) . '</option>';
		}
		$output .= '</select>';
		$output .= '</div>';

		$output .= '</div>';
		return $output;
	}	
	
	
	

    /**
     * Return gflabel column
     */

    public function column_gflabel( $gflabel ) {

        return  "<div class='folder' id='folder-id-" . $gflabel['id'] . "'>". $gflabel['gflabel_name'] ."</div><div class='folder-edit-div' style='display:none;' id='folder-edit-" . $gflabel['id'] . "'><input class='glabelval' id='" . $gflabel['id'] . "' type='text' value=". $gflabel['gflabel_name'] ."></div><div class='row-actions'></span><span class='trash'><a href='javascript:void(0)' class='delete-folder' data-folderid=".$gflabel['id'].">Delete</a> | </span><span class='inline-edit'><a href='javascript:void(0)' class='edit-folder' data-folderid=".$gflabel['id'].">Edit</a><a style='display:none;' href='javascript:void(0)' class='update-folder-btn' data-folderid=".$gflabel['id'].">Update</a> | </span></div>";

    }




	public function get_sortable_columns()	{
			$sortable_columns = array(
				'gflabel' => array( 'gflabel_name', false )
			);
			return $sortable_columns;
		}	

	

    /**
     * Prepare table list items.
     */

    public function prepare_items() {

     global $wpdb, $table_prefix;
	 $sql_gflabel_table = $wpdb->prefix . "gfform_labels";
	 $sql_gfform_table = $wpdb->prefix . "gf_form";

        $per_page = 10;
        $columns  = $this->get_columns();
        $hidden   = array();
        $sortable = $this->get_sortable_columns();


        // Column headers
        $this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		
        $current_page = $this->get_pagenum();
        if ( 1 < $current_page ) {
            $offset = $per_page * ( $current_page - 1 );
        } else {
            $offset = 0;
        }

        $search = '';



        if ( ! empty( $_REQUEST['s'] ) ) {
            $search = "AND gflabel_name LIKE '%" . esc_sql( $wpdb->esc_like( $_REQUEST['s'] ) ) . "%' ";
        }


        $items = $wpdb->get_results(
            "SELECT id, gfform_id, gflabel_name FROM ".$sql_gflabel_table." WHERE 1 = 1 {$search}" .
            $wpdb->prepare( "GROUP BY gflabel_name ORDER BY id DESC LIMIT %d OFFSET %d;", $per_page, $offset ), ARRAY_A
        );

		function flgf_usort_reorder($a,$b){
		  $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
		  $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
		  $result = strnatcmp($a[$orderby], $b[$orderby]); //Determine sort order
		  return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
		}	
		usort($items, 'flgf_usort_reorder');

        $count = $wpdb->get_var( "SELECT COUNT(id) FROM ".$sql_gflabel_table." WHERE 1 = 1 AND gfform_id=''{$search};" );
        $this->items = $items;

        // Set the pagination
        $this->set_pagination_args( array(
            'total_items' => $count,
            'per_page'    => $per_page,
            'total_pages' => ceil( $count / $per_page )
        ) );

    }
	
	
	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions()
	{
		$actions = ['bulk-delete' => 'Delete'];
		return $actions;
	}
	
	public static function flgf_delete_records($id)
	{
     global $wpdb, $table_prefix;
	 $sql_gflabel_table = $wpdb->prefix . "gfform_labels";
		$wpdb->delete($sql_gflabel_table, ['gflabel_name' => $id]);
	}	
	
	
	public function process_bulk_action() 
	{
		if ( 'delete' === $this->current_action() ) {	    
			self::flgf_delete_records( absint( $_GET['record'] ) );
		}

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )) {
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
			foreach ( $delete_ids as $id ) {
				self::flgf_delete_records( $id );
			}
		}
	}	
		

	function inline_edit() {
?>
	<form method="POST" action="<?php echo admin_url( 'admin-ajax.php' ); ?>"><table style="display: none"><tbody id="inlineedit">
		<tr id="inline-edit" class="inline-edit-row" style="display: none"><td colspan="<?php echo $this->get_column_count(); ?>" class="colspanchange">
			<fieldset><div class="inline-edit-col">
				<input type="hidden" name="id" value="" />
				<input type="hidden" name="action" value="acm_admin_action" />
				<input type="hidden" name="method" value="edit" />
				<input type="hidden" name="doing_ajax" value="true" />
				<?php wp_nonce_field( 'acm-admin-action', 'nonce' ); ?>
				<div class="acm-float-left">
				<div class="acm-column-fields"></div>
				<div class="acm-priority-field"></div>
				<div class="acm-operator-field"></div>
				</div>
				<div class="acm-conditional-fields"></div>
				<div class="clear"></div>
			</div></fieldset>
		<p class="inline-edit-save submit">
			<?php $cancel_text = __( 'Cancel', 'ad-code-manager' ); ?>
			<a href="#inline-edit" title="<?php echo esc_attr( $cancel_text ); ?>" class="cancel button-secondary alignleft"><?php echo esc_html( $cancel_text ); ?></a>
			<?php $update_text = __( 'Update', 'ad-code-manager' ); ?>
			<a href="#inline-edit" title="<?php echo esc_attr( $update_text ); ?>" class="save button-primary alignright"><?php echo esc_html( $update_text ); ?></a>
			<img class="waiting" style="display:none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
			<span class="error" style="display:none;"></span>
			<br class="clear" />
		</p>
		</td></tr>
		</tbody></table></form>
	<?php
	}
	
	
	
}



add_action( 'gform_form_actions', 'flgf_gf_folder_link', 10, 4 );
function flgf_gf_folder_link( $actions, $form_id ) {
		global $wpdb, $table_prefix;
		$gform_table = $wpdb->prefix . "gf_form";
		$gffolder_table = $wpdb->prefix . "gfform_labels";
	    $gffoldertags_table = $wpdb->prefix . "gf_gfolders";
	    $tagarr = "";
	    $getFolder = "";
		$getFolderTags = "";
	$gformFolders = $wpdb->get_results("SELECT DISTINCT gflabel_name  FROM ".$gffolder_table." ORDER BY gflabel_name ASC");
	$forminfo = RGFormsModel::get_form($form_id);
  	$form_title = $forminfo->title;
	$post = $wpdb->get_row("SELECT * FROM ".$gffolder_table." WHERE gfform_id = '$form_title'");
	$gformFoldersTags = $wpdb->get_row("SELECT * FROM ".$gffoldertags_table." WHERE gfform_id = '$form_id'");
	if( isset( $post->gflabel_name ) ){
	    $getFolder = $post->gflabel_name;
	}
	if( isset( $gformFoldersTags->gf_gfolder ) ){
		$getFolderTags = $gformFoldersTags->gf_gfolder;
	}
	$getFolderTagsArr = explode(',',$getFolderTags);
	if($getFolder == ''){ $foler_name = 'Folder'; }else{ $foler_name = $getFolder.'-selected';}
	
	$abc = "<select name='gf_label_name1' class='folderlist form-control'><option selected='selected' disabled>$foler_name</option>";
	 foreach($gformFolders as $gformFolder){
		 		if($gformFolder->gflabel_name )
		$abc .= "<option value='$gformFolder->gflabel_name'>". esc_html($gformFolder->gflabel_name). "</option>";
	  }
		//Tags Array
	  foreach($getFolderTagsArr as $getFolderTag){
     if(!empty($getFolderTag)):
		$tagarr .= "<span class='tm-tag tm-tag-info' id='tag-$form_id-$getFolderTag'><span>$getFolderTag</span><a class='remove-gftag tm-tag-remove' data-id='$form_id' data-value='$getFolderTag' data-removegfid='$form_id-$getFolderTag' href='javascript:void(0)'>x</a></span>";
	 endif;
	  }
	$abc .= "</select><input type='hidden' id='gform_name' class='gformID' value='$form_title'>";
	$abc .= "<div class='gftag-section'>$tagarr<input type='text' class='typeahead tm-input tm-input-info' id='typehead-$form_id' name='gftags-$form_id' placeholder='Search labels' value=''><a href='javascript:void(0)' class='add-gftags' data-labelbtnid='$form_id'>Save Tags</a><input type='hidden' name='gf-name' id='gf-name-$form_id' value='$form_title'><input type='hidden' name='gf-tagslist' id='gf-tagslist-$form_id' value='$getFolderTags'></div>";
		$actions['gfolder_action'] = $abc;
	
        return $actions;
}