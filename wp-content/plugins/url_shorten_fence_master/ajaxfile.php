<?php
header('Access-Control-Allow-Origin: *');
require_once(dirname(__FILE__).'/../../../wp-config.php');
require_once(dirname(__FILE__).'/widget.php');
global $wpdb;
$table_name = $wpdb->prefix . "faqs"; 
$sql = "SELECT * FROM `$table_name` ORDER BY rand()";
	$faqs = $wpdb->get_results($sql);
echo json_encode($faqs);
//display_primary_faqs_func(20);
?>
