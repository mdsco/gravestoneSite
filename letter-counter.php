<?php
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

function lc_filter_woocommerce_cart_product_price( $wc_price ) {

	$current_key = CartItemKeyHolder::getCartItemKey();

	$cost_per_letter = 5.00;

	$dir = dirname(__DIR__);

	$count_object = file_get_contents($dir.'/marksPlugin/letter-count-log.txt');

	// $count_array = array();
	$count_array = json_decode($count_object, true);
	$wc_price_int = (double) $wc_price;

	$count = 1.00;

	if(array_key_exists ( $current_key , $count_array )){

		$count = (double) $count_array[$current_key];

		$wc_price_int += $count * $cost_per_letter;

		return $wc_price_int;

	}

    return $wc_price;
};

function set_price_for_product_on_cart_item($wc_cart){

	$cart = $wc_cart->get_cart();

	foreach ( $cart as $cart_item_key => $values ) {
		
		CartItemKeyHolder::setCartItemKey($cart_item_key);

		$_product = $values['data'];

		if($_product != null){
			$original_price = $_product->get_price();

			$_product->set_price(lc_filter_woocommerce_cart_product_price( $original_price ));
		}
	}

	return $wc_cart;

}

function twentysixteen_child_scripts(){

    wp_register_script( 'Letter Counter', plugins_url( '/js/letter-counter-mod.js', __FILE__ ), array('jquery') );
	wp_enqueue_script('Letter Counter');

}

function lc_update_key_of_no_key_element($array_item, $item, $key){

	$dir = dirname(__DIR__);

	$count_array = array();

	if(!file_exists($dir.'/marksPlugin/letter-count-log.txt')){

		file_put_contents($dir.'/marksPlugin/letter-count-log.txt', json_encode($count_array));
	
	}
	
	$count_object = file_get_contents($dir.'/marksPlugin/letter-count-log.txt');
	$count_array = json_decode($count_object, true);
	
	if(array_key_exists ( 'no_key' , $count_array )){

		$no_key_count_value = $count_array['no_key'];

		if(!array_key_exists ( $key , $count_array )){

			$count_array[$key] = $no_key_count_value;
			unset($count_array['no_key']);
			$no_key_object = json_encode($count_array);

			file_put_contents($dir.'/marksPlugin/letter-count-log.txt', $no_key_object);

		}

	}

	return $array_item;

}

function setCartItemKey($visible, $item, $key){

	CartItemKeyHolder::setCartItemKey($key);

	return $visible;

}

add_action('woocommerce_remove_cart_item', 'remove_count_element_from_file'); 

add_filter('woocommerce_before_calculate_totals', 'set_price_for_product_on_cart_item');

add_filter('woocommerce_cart_item_product_id', 'lc_update_key_of_no_key_element', 10, 3);

add_filter('woocommerce_cart_item_visible', 'setCartItemKey', 10, 3);

add_filter('woocommerce_widget_cart_item_visible', 'setCartItemKey', 10, 3);

add_action('wp_enqueue_scripts', 'twentysixteen_child_scripts');

class CartItemKeyHolder{
	
    private static $cart_item_key = null;

    public static function setCartItemKey($value)
    {
        self::$cart_item_key = $value;
    }

    public static function getCartItemKey()
    {
        return self::$cart_item_key;
    }

}