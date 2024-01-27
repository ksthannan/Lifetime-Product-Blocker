<?php
/**
 * Custom Functions 
 */
trait Lifetime_Pro_Blocker_Functions{

    /**
     * get option functions for settings field
     */
    public function get_option( $option_name, $default = '' ) {
        if ( is_null( $this->options ) ) $this->options = ( array ) get_option( LIFE_PRO_BLOCKER_OPT_NAME, array() );
        if ( isset( $this->options[$option_name] ) ) return $this->options[$option_name];
        return $default;
    }

    /**
     * Update functions for settings field
     */
    public function update_option( $option_name, $default = '' ) {
        if ( is_null( $this->options ) ) $this->options = ( array ) get_option( LIFE_PRO_BLOCKER_OPT_NAME, array() );
        $this->options[$option_name] = $default;
        update_option( LIFE_PRO_BLOCKER_OPT_NAME, $this->options);
    }

    /**
     * Remove an item from cart 
     */
    public static function remove_product_from_cart($product_id){
    
        if (class_exists('WooCommerce')) {
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                if ( $cart_item['product_id'] == $product_id ) {
                    $removed = WC()->cart->remove_cart_item( $cart_item_key );
                    if($removed){
                        return true;
                    }else{
                        return false;
                    }
                }
            }
        }
        
    }

}
