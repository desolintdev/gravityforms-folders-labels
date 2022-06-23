<?php

$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
include($path.'wp-load.php');


 global $wpdb, $table_prefix;

		 $sql_gflabel_table = $wpdb->prefix . "gf_label_tags";


		$items = $wpdb->get_results(
				"SELECT gf_gfolder FROM ".$sql_gflabel_table." WHERE gf_gfolder LIKE '%".$_GET['query']."%'");



    $data = [];
        foreach($items as $item){
            $data[] = $item->gf_gfolder;
            
        }
    echo json_encode($data);


      
      
?>      