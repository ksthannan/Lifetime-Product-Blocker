<?php 
add_action('wp_loaded', 'manage_custom_functions');
function manage_custom_functions(){
    if(isset($_GET['remove_cart'])){
        $product_id = $_GET['remove_cart'];
        $return = $_GET['return'];
        $is_removed = Lifetime_Pro_Blocker::remove_product_from_cart($product_id);
        if($is_removed){
            wp_redirect($return);
            exit;
        }
    }

    // $user_id = get_current_user_id();
    // update_user_meta( $user_id, 'product_order_info', array(
    //     '22' => array(
    //         'product_id' => 22,
    //         'status' => 'purchased',
    //         'purchase_date' => current_time('mysql'),
    //         'note' => ''
    //     )
    // ));

    global $Lifetime_Pro_Blocker;
    if(is_user_logged_in()){
        $user_id = get_current_user_id();
        $product_order_info = get_user_meta( $user_id, 'product_order_info',  true);
        $product_order_info = is_array($product_order_info) ? $product_order_info : array();
    
        if(count($product_order_info) >= $Lifetime_Pro_Blocker->limit){
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            add_filter('woocommerce_loop_add_to_cart_link', 'custom_add_to_cart_button_html', 10, 2);
            add_filter('woocommerce_product_add_to_cart_url', 'custom_add_to_cart_button_html', 10, 2);
            add_action('woocommerce_single_product_summary', 'custom_add_to_cart_button_link', 30);
        }
    }
	
	/**
	* Add my product list page to my-account area 
	*/
// 	$active = $Lifetime_Pro_Blocker->lifeproblocker_active;
// 	if($active){

// 	}
	
    
    
}

add_action('init', 'register_my_product_list_endpoint');
function register_my_product_list_endpoint()
{
	add_rewrite_endpoint('my-product-list', EP_ROOT | EP_PAGES);
}
add_filter('query_vars', 'my_product_list_query_vars');
function my_product_list_query_vars($vars)
{
	$vars[] = 'my-product-list';
	return $vars;
}
add_filter('woocommerce_account_menu_items', 'add_my_product_list_item_tab', 100, 1);
function add_my_product_list_item_tab($items)
{
	$ordered_items = array();
	$new_post_menu = array(
		'my-product-list' => __('My Product List', 'lifeproblocker'
							   ));
	$items = array_merge($new_post_menu, $items);

	return $items;
	$items;
}
add_action('woocommerce_account_my-product-list_endpoint', 'add_uploads_content');
function add_uploads_content()
{
	echo do_shortcode('[lifetime_product_blocker]');

}

add_action('wp_head', 'wp_header_contents');
function wp_header_contents(){

    // $user_id = get_current_user_id();
    // update_user_meta( $user_id, 'product_order_info', array());


    global $Lifetime_Pro_Blocker;
    if(is_checkout()){
        if (class_exists('WC_Session')) {
            $session = WC()->session;

            $user_id = get_current_user_id();
            $product_order_info = get_user_meta( $user_id, 'product_order_info',  true);
            $product_order_info = is_array($product_order_info) ? $product_order_info : array();
            $cart = WC()->cart;
            $cart_items = $cart->get_cart();
            $total_items = count($product_order_info) + count($cart_items);
            $session->set('total_items_to_list', $total_items);

            $total_items_to_list = $session->get('total_items_to_list');
            if($total_items_to_list > $Lifetime_Pro_Blocker->limit){
                wc_add_notice(__('You can add maximum '.$Lifetime_Pro_Blocker->limit.' products to your list.', 'lifeproblocker'), 'error');
                wp_redirect(wc_get_account_endpoint_url('my-product-list'));
                exit;
            }
        }
    }
    // $session = WC()->session;
    // $total_items_to_list = $session->get('total_items_to_list');
    // var_dump($total_items);
}

function custom_add_to_cart_button_html($button_link, $product) {
    global $Lifetime_Pro_Blocker;
    if(is_user_logged_in()){
        $user_id = get_current_user_id();
        $product_order_info = get_user_meta( $user_id, 'product_order_info',  true);
        $product_order_info = is_array($product_order_info) ? $product_order_info : array();
    
        if(count($product_order_info) >= $Lifetime_Pro_Blocker->limit){
            $button_link = $Lifetime_Pro_Blocker->product_list_page;
        }
    }
    
    return '<a href="'.$button_link.'" class="single_add_to_cart_button button add_to_cart_button">'.__('Go to My Product List', 'lifeproblocker').'</a>';
}

function custom_add_to_cart_button_link() {
    global $Lifetime_Pro_Blocker;
    if(is_user_logged_in()){
        $user_id = get_current_user_id();
        $product_order_info = get_user_meta( $user_id, 'product_order_info',  true);
        $product_order_info = is_array($product_order_info) ? $product_order_info : array();
    

        // if (array_key_exists($product->get_id(), $product_order_info)) {
        //     unset($product_order_info[$product->get_id()]);
        // }


        if(count($product_order_info) >= $Lifetime_Pro_Blocker->limit){
            $button_link = $Lifetime_Pro_Blocker->product_list_page;
            echo '<a href="'.$button_link.'" class="single_add_to_cart_button button add_to_cart_button">'.__('Visit Products List', 'lifeproblocker').'</a>';
        }

    }

    
}


function limit_quantity_to_one($passed, $product_id, $quantity) {

    if(is_user_logged_in()){
        $user_id = get_current_user_id();
        $product_order_info = get_user_meta( $user_id, 'product_order_info',  true);
        if(isset($product_order_info[$product_id])){
            wc_add_notice(__('This item is already in your list.', 'lifeproblocker'), 'error');
            return false;
        }
    }

    // Get the current cart instance
    $cart = WC()->cart;

    // Check if the product is already in the cart
    $in_cart = $cart->find_product_in_cart($product_id);

    // If the product is in the cart, prevent adding more
    if ($in_cart == true) {
        wc_add_notice(__('You can only add once this product to your cart.', 'lifeproblocker'), 'error');
        return false;
    }

    return $passed;
}
add_filter('woocommerce_add_to_cart_validation', 'limit_quantity_to_one', 10, 3);

function limit_cart_item_quantity_to_one($cart) {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        // Check if the quantity exceeds 1
        if ($cart_item['quantity'] > 1) {
            // Set the quantity to 1
            $cart->set_quantity($cart_item_key, 1);
            // Display a notice
            wc_add_notice(__('You can only add one of this product to your cart.', 'lifeproblocker'), 'error');
        }
    }
}
add_action('woocommerce_cart_loaded_from_session', 'limit_cart_item_quantity_to_one');

