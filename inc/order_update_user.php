<?php
/**
 * Admin Settings
 */
class Lifetime_Pro_Blocker_Order_Update_User extends Lifetime_Pro_Blocker{

    /**
     * Constructor 
     */
    public function __construct() {

        parent::__construct();

        add_action( 'woocommerce_order_status_changed', array( $this, 'order_update_user_data' ), 10, 3 );
        add_action('woocommerce_delete_order', 'blocker_update_function_after_delete_order', 10, 1);
        add_action('wp_untrash_post', 'blocker_update_function_after_wp_untrash_post', 10, 1);

    }

    // Custom function to run after order deletion
    public function blocker_update_function_after_delete_order($order_id) {
        $user_id = get_post_field( 'post_author', $order_id );
        $order = wc_get_order($order_id);

        $product_order_info = get_user_meta( $user_id, 'product_order_info',  true);

        foreach ($order->get_items() as $item_id => $item) {
            // Get product details
            $product = $item->get_product();

            if (array_key_exists($product->get_id(), $product_order_info)) {
                unset($product_order_info[$product->get_id()]);
            } 
        }

        update_user_meta($user_id, 'product_order_info', $product_order_info);

    }

    // Custom function to run after wp_untrash_post
    public function blocker_update_function_after_wp_untrash_post($order_id) {
        $user_id = get_post_field( 'post_author', $order_id );
        $order = wc_get_order($order_id);
        $product_order_info = get_user_meta( $user_id, 'product_order_info',  true);

        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();

            $purchase_date = $order -> get_date_created();
            
            $product_order_info[$product->get_id()] = array(
                'product_id' => $product->get_id(),
                'status' => $new_status,
                'purchase_date' => $purchase_date->format('Y-m-d H:i:s'),
                'note' => '',
            );

        }

        update_user_meta($user_id, 'product_order_info', $product_order_info);

    }

    /**
     * Update user meta based on new order  
     */
    public function order_update_user_data($order_id, $old_status, $new_status){

        if (!$order_id) return;

        $user_id = get_current_user_id();
        $order = wc_get_order($order_id);
        $status = $order->get_status();
        // update_user_meta( $user_id, 'product_order_info', array());
// 		update_user_meta( $user_id, 'product_order_info', $order_id);
        $product_order_info = get_user_meta( $user_id, 'product_order_info',  true);
		
        foreach ($order->get_items() as $item_id => $item) {

            

            // Get product details
            $product = $item->get_product();
            $purchase_date = $order -> get_date_created();

            // update_user_meta( $user_id, 'product_order_info', $item);
			
            if($status == 'processing' || $status == 'completed' || $status == 'return-requested' || $status == 'return-approved'){

                $product_order_info[$product->get_id()] = array(
                    'product_id' => $product->get_id(),
                    'status' => $new_status,
                    'purchase_date' => $purchase_date->format('Y-m-d H:i:s'),
                    'note' => '',
                );
            }else{
                if (array_key_exists($product->get_id(), $product_order_info)) {
                    unset($product_order_info[$product->get_id()]);
                }
            }

            
        }

        update_user_meta($user_id, 'product_order_info', $product_order_info);

    }

    
}

new Lifetime_Pro_Blocker_Order_Update_User();
