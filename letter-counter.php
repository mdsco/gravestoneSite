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

// function addMenu(){
// 	add_menu_page("Example Options", "Example Options", 4, "example_options", "exampleMenu");
// }

// function exampleMenu(){
// 	echo "hello world";
// }

function dwwp_filter_woocommerce_cart_product_price( $wc_price ) {

	$cost_per_letter = 5.00;

	$dir = dirname(__DIR__);

	$count_string = file_get_contents($dir.'/marksPlugin/letter-count-log.txt');

	$count = (int) $count_string;

	$wc_price += $count * $cost_per_letter;

	$output = "<script>console.log( 'Price: " . $count_string . "' );</script>";

    return $wc_price; 
}; 

function twentysixteen_child_scripts(){

    wp_register_script( 'Letter Counter', plugins_url( '/js/letter-counter-mod.js', __FILE__ ), array('jquery') );

	wp_enqueue_script('Letter Counter');
}

add_filter( 'woocommerce_get_price', 'dwwp_filter_woocommerce_cart_product_price'); 

add_action('wp_enqueue_scripts', 'twentysixteen_child_scripts');

       

