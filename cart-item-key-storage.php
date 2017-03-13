<?php

class CartItemKeyStorage{
	
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

?>