<?php
/*
Plugin Name: Letter Counter
Plugin URI:
Description: Generates an accurate letter count of words added to the designer by a user.
Author: Mike Scoboria
Author URI:
Version: 0.1
*/

add_action("admin_menu", "addMenu");

function addMenu(){
	add_menu_page("Example Options", "Example Options", 4, "example_options", "exampleMenu");
}

function exampleMenu(){
	
}

function lc_filter_woocommerce_cart_product_price( $wc_price ) {

	$cost_per_letter = 5.00;

	$dir = dirname(__DIR__);

	$count_string = file_get_contents($dir.'/marksPlugin/letter-count-log.txt');

	$count = (int) $count_string;

	$wc_price += $count * $cost_per_letter;

	$output = "<script>console.log( 'Price: " . $count_string . "' );</script>";
	echo $output;
	
    return $wc_price; 
};

function lc_clear_letter_count_file($thing){

	$dir = dirname(__DIR__);

	$result = file_put_contents($dir.'/marksPlugin/letter-count-log.txt', '0');

	return $thing;
}

function twentysixteen_child_scripts(){

    wp_register_script( 'Letter Counter', plugins_url( '/js/letter-counter-mod.js', __FILE__ ), array('jquery') );
	wp_enqueue_script('Letter Counter');

}

function lc_this_function($session_data){

		// $output = "<script>console.log( 'Result: " . count($session_data['check_cart_items']) . "' );</script>";
		// echo $output;

		return $session_data;

}

function lc_update_key_of_no_key_element($array_item, $item, $key){

	$dir = dirname(__DIR__);

	$count_object = file_get_contents($dir.'/marksPlugin/letter-count-log.txt');
	$count_array = json_decode($count_object, true);

	if(array_key_exists ( 'no_key' , $count_array )){
	//if(($no_key_count_value = $count_array['no_key']) != null){

		$no_key_count_value = $count_array['no_key'];

		if(!array_key_exists ( $key , $count_array )){
		// if($count_array[$key] == null){

			$count_array[$key] = $no_key_count_value;
			unset($count_array['no_key']);
			$no_key_object = json_encode($count_array);

			file_put_contents($dir.'/marksPlugin/letter-count-log.txt', $no_key_object);

		}

	} else {

		$output = "<script>console.log( 'Can access file: " . $count_object . " valueola is null '  );</script>";
		echo $output;
	}

	return $array_item;

}

add_filter('woocommerce_get_price', 'lc_filter_woocommerce_cart_product_price'); 

add_action('woocommerce_check_cart_items', 'lc_this_function');

//add_action('woocommerce_before_main_content', 'lc_clear_letter_count_file');

add_filter('woocommerce_cart_item_product_id', 'lc_update_key_of_no_key_element', 10, 3);

add_action('woocommerce_ajax_added_to_cart', function($product_id){

	return $product_id;
});

add_action('wp_enqueue_scripts', 'twentysixteen_child_scripts');