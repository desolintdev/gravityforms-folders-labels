<?php

global $wpdb;

$items = $wpdb->get_results("SELECT gf_gfolder FROM {$wpdb->prefix}gf_label_tags WHERE gf_gfolder LIKE '%" . $_GET['query'] . "%'");
$data  = array();
foreach ($items as $item) {
    $data[] = $item->gf_gfolder;
}
echo json_encode($data);
