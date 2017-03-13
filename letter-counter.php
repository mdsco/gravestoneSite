<?php

require("cart-item-key-storage.php");
// require("database-querier.php");
require("user-id-temp-storage.php");

/*
Plugin Name: Letter Counter
Plugin URI:
Description: Generates an accurate letter count of words added to the designer by a user.
Author: Mike Scoboria
Author URI:
Version: 0.1
*/

// add_action("admin_menu", "addMenu");

// function addMenu(){
// 	add_menu_page("Example Options", "Example Options", 4, "example_options", "exampleMenu");
// }

// function exampleMenu(){
	
// }

function twentysixteen_child_scripts(){

    wp_register_script( 'Letter Counter', plugins_url( '/js/letter-counter-mod.js', __FILE__ ), array('jquery') );
	wp_enqueue_script('Letter Counter');
	wp_register_script( 'JavascriptCookie', plugins_url( '/js/js-cookie-1.5.1/src/js.cookie.js', __FILE__ ), array('jquery') );
	wp_enqueue_script('JavascriptCookie');

}

function lc_filter_woocommerce_cart_product_price( $wc_price ) {

	$current_key = CartItemKeyStorage::getCartItemKey();

	$cost_per_letter = 5.00;

	// $dir = dirname(__DIR__);

	$sql = "SELECT * FROM current_user_id;";
    $result = DatabaseQuerier::queryDatabase($sql);

    $stored_user_id = '';

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $stored_user_id = $row['user_id'];
    }

	$sql = "SELECT * FROM products_count WHERE id = '" . $stored_user_id . "';";
	$result = DatabaseQuerier::queryDatabase($sql);

	while($row = $result->fetch(PDO::FETCH_ASSOC)) {

		$count_object_from_db = $row['count_object'];

		$count_array = json_decode($count_object_from_db, true);

		// if(file_exists($dir.'/marksPlugin/letter-count-log.txt')){

		// 	$count_object = file_get_contents($dir.'/marksPlugin/letter-count-log.txt');
		
		// 	$count_array = json_decode($count_object, true);

		$wc_price_int = (double) $wc_price;

		$count = 1.00;

		if(array_key_exists ( $current_key , $count_array )){

			$count = (double) $count_array[$current_key];

			$wc_price_int += $count * $cost_per_letter;

			return $wc_price_int;

		}

	}	

    return $wc_price;
};

function set_price_for_product_on_cart_item($wc_cart){

	$cart = $wc_cart->get_cart();

	foreach ( $cart as $cart_item_key => $values ) {
		
		CartItemKeyStorage::setCartItemKey($cart_item_key);

		$_product = $values['data'];

		if($_product != null){
			$original_price = $_product->get_price();

			$_product->set_price(lc_filter_woocommerce_cart_product_price( $original_price ));
		}
	}

	return $wc_cart;

}

function lc_update_key_of_no_key_element($array_item, $item, $key){

	$count_array = array();

	$sql = "SELECT * FROM current_user_id;";
    $result = DatabaseQuerier::queryDatabase($sql);

    $stored_user_id = '';

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $stored_user_id = $row['user_id'];

		$sql = "SELECT * FROM products_count WHERE id = '" . $stored_user_id . "';";
		$result = DatabaseQuerier::queryDatabase($sql);

		while($row = $result->fetch(PDO::FETCH_ASSOC)) {

			$count_object_from_db = $row['count_object'];

			$count_array = json_decode($count_object_from_db, true);

			if(array_key_exists ( 'no_key' , $count_array )){

				$no_key_count_value = $count_array['no_key'];

				if(!array_key_exists ( $key , $count_array )){

					$count_array[$key] = $no_key_count_value;
					unset($count_array['no_key']);
					$cart_item_key_object = json_encode($count_array);

					$updateStatement = "UPDATE products_count SET count_object = '"
						 				. $cart_item_key_object . "' WHERE id = '" 
	 					 				. $stored_user_id . "';";

					DatabaseQuerier::insertIntoDatabase($updateStatement);

				}

			}
		}
	}

	// if($count_object_from_db == '{}'){
		
	// 	$countArray['no_key'] = $char_count;    		
	
	// } else{

	// 	$countArray = json_decode($count_object_from_db, true);
	// 	$countArray[$product_key] = $char_count;

	// }
	
	// $count_object = file_get_contents($dir.'/marksPlugin/letter-count-log.txt');
	// 
	
	//

	return $array_item;

}

function setCartItemKey($visible, $item, $key){

	CartItemKeyStorage::setCartItemKey($key);

	return $visible;
}

add_action('woocommerce_remove_cart_item', 'remove_count_element_from_file'); 

add_filter('woocommerce_before_calculate_totals', 'set_price_for_product_on_cart_item');

add_filter('woocommerce_cart_item_product_id', 'lc_update_key_of_no_key_element', 10, 3);

add_filter('woocommerce_cart_item_visible', 'setCartItemKey', 10, 3);

add_filter('woocommerce_widget_cart_item_visible', 'setCartItemKey', 10, 3);

add_action('wp_enqueue_scripts', 'twentysixteen_child_scripts');

//For test
// add_filter('	woocommerce_get_price_html', function($price){

// 	print_r($price);

// 	return $price;

// });