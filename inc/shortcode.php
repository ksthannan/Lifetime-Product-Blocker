<?php
/**
 * Admin Settings
 */
class Lifetime_Pro_Blocker_Shortcode extends Lifetime_Pro_Blocker{

    /**
     * Constructor 
     */
    public function __construct() {

        parent::__construct();

        add_shortcode( 'lifetime_product_blocker', array( $this, 'register_ordered_list_product_shortcode' ) );

    }

    /**
     * Register shortcode lifetime_product_blocker for ordered product list
     */
    public function register_ordered_list_product_shortcode($atts, $content = null) {

        if(!class_exists('WooCommerce') || $this->lifeproblocker_active == false) return;

        if(! is_user_logged_in()) return;

            ob_start();

            $user_id = get_current_user_id();

// 			update_user_meta( $user_id, 'product_order_info', array());
            $product_order_info = get_user_meta( $user_id, 'product_order_info',  true);
            

            // echo '<pre>';
            // echo var_dump(count($product_order_info));
            // echo '</pre>';

            $atts = shortcode_atts( array(
                'limit' => $this->limit,
                'customer_id' => $user_id
            ), $atts, 'lifetime_product_blocker' );

            $customer_orders = wc_get_orders(array(
                'limit' => $atts['limit'],
                'customer_id' => $user_id,
                'return' => 'ids',
            ));
		
			$cart = WC()->cart;
			$cart_items = $cart->get_cart();
			global $wp;
			$current_url = esc_url(home_url(add_query_arg(array(), $wp->request)));

            $order_list = '';
            $order_list .= '<table  class="blocker_list_purchase"><tr class="list_purchase">';
            $order_list .= '<th>' . __("Product", "lifeproblocker") . '</th>';
            $order_list .= '<th>' . __("Purchase Date", "lifeproblocker") . '</th>';
            $order_list .= '<th>' . __("Status", "lifeproblocker") . '</th>';
            $order_list .= '<th>' . __("Actions", "lifeproblocker") . '</th>';
            $order_list .= '</tr>';

		if( count($customer_orders) > 0 || count($cart_items) > 0 ){
			
            $number = 1;
            // Loop through each order and get ordered items
            foreach ($customer_orders as $order_id) {
                $order = wc_get_order($order_id);

                foreach ($order->get_items() as $item_id => $item) {
                    // Get product details
                    $product = $item->get_product();

                    $date = '';
                    $status = '';
						if( is_array($product_order_info) && isset($product_order_info[$product->get_id()])){
							$date = $product_order_info[$product->get_id()]["purchase_date"];
							$status = $product_order_info[$product->get_id()]["status"];
						}

                    $status_label = $this->get_option('wc-' . $status) ? $this->get_option('wc-' . $status) : $status;

                    if($status == 'return-requested' || $status == 'return-approved' || $status == 'payu-waiting'){
                        $refund_icon = '<span class="dashicons dashicons-update"></span>';
                    }else{
                        $refund_icon = '';
                    }

                    if($status == 'processing' || $status == 'completed' || $status == 'return-requested' || $status == 'return-approved' || $status == 'payu-waiting'){

                        $order_list .= '<tr class="'.$status.'" style="background-color:'.$this->get_option('wc-' . $status . '-theme_color').'">';
                        $order_list .= '<td><a class="purchase_product_url" href="' . get_permalink($product->get_id()) . '">#' .$number . ' '  . $product->get_name() . '</a></td>';
                        $order_list .= '<td><span class="item_purchase_date">' . $date . '</span></td>';
                        $order_list .= '<td><span class="item_status">' . $refund_icon . $status_label . '</span></td>';
                        $order_list .= '<td><a title="Manage" href="'.wc_get_account_endpoint_url('orders').'" class="order_item_action"><img width="30" height="30" src="'.LIFE_PRO_BLOCKER_URL_ASSETS.'/img/dots.png"></a></td>';
                        $order_list .= '</tr>';
                        $number++;

					}

				}
			}
			
			if (class_exists('WC_Session')) {
				$session = WC()->session;
				$session->set('total_items_to_list', $number - 1);
			}

			if ($cart_items) {
				foreach ($cart_items as $cart_item_key => $cart_item) {

					$status = $this->get_option('cart_items') ? $this->get_option('cart_items') : __('New', 'lifeproblocker');

					$product_id = $cart_item['product_id'];
					$product_name = $cart_item['data']->get_name();
					$order_list .= '<tr class="cart_item" style="background-color:'.$this->get_option('cart_items-theme_color').'">';
					$order_list .= '<td><a class="purchase_product_url" href="' . get_permalink($product_id) . '">#' . $number . ' ' . $product_name . '</a></td>';
					$order_list .= '<td><span class="item_purchase_date"><span>'.$status.'</span></td>';
					$order_list .= '<td><span><a class="button blocker_button_delete" href="'.get_the_permalink().'?remove_cart='.$product_id.'&return='.urlencode($current_url).'"><span class="dashicons dashicons-trash"></span>'. __('Delete from cart', 'lifeproblocker') .'</a></span></td>';
					$order_list .= '<td><a title="Manage" href="'.wc_get_account_endpoint_url('orders').'" class="order_item_action"><img width="30" height="30" src="'.LIFE_PRO_BLOCKER_URL_ASSETS.'/img/dots.png"></a></td>';
					$order_list .= '</tr>';

					$number ++;
				}

			}
			
			$order_list .= '</table>';
			
			if(count($cart_items) > 0){
			$order_list .= '<div class="blocker_action_btns">
				<a class="button blocker_back" href="'.wc_get_page_permalink("shop").'">'.__('Back to Shop', 'lifeproblocker').'</a>
				<a class="button blocker_proceed" href="'.wc_get_cart_url().'">'.__('Proceed to Checkout', 'lifeproblocker').'</a>
				</div>';
			}else{
				$order_list .= '<div class="blocker_action_btns">
					<a class="button blocker_manage" href="'.wc_get_account_endpoint_url('orders').'">'.__('Manage My Products', 'lifeproblocker').'</a>
					</div>';
			}
			
			
		}else{
			$order_list .= '</table>';
			$order_list .= '<div class="blocker_action_btns">';
			$order_list .= '<p class="no_blocker_item_found">' . __('No item found!', 'lifeproblocker') . '</p>';
			$order_list .= '</div>';
		}
		
		// var_dump($product_order_info);

        echo $order_list;
        
        $output = ob_get_clean();
        return $output;

    }


}

new Lifetime_Pro_Blocker_Shortcode();
