<?php 

defined( 'ABSPATH' ) or die; 

$color_purchased = $this->get_option('color_purchased');
$color_return_not_confirmed = $this->get_option('color_return_not_confirmed');
$color_return_and_confirmed = $this->get_option('color_return_and_confirmed');
$color_marked_broken = $this->get_option('color_marked_broken');

// $user_can_purchase_at_once = $this->get_option('user_can_purchase_at_once');

// echo '<pre>';
// $debug = get_option('el_debugging');
// var_dump($debug);
// echo '</pre>';
?>
<div class="wrap wrap-lifeproblocker-content">
	<h1><?php _e( 'Lifetime Product Blocker', 'lifeproblocker' ); ?></h1>
	<form method="post" action="options.php">
		<?php settings_errors(); ?>
		<?php settings_fields( LIFE_PRO_BLOCKER_OPT_GROUP ); ?>
		<?php do_settings_sections( LIFE_PRO_BLOCKER_OPT_GROUP ); ?>
            
		<div class="lifeproblocker_settings">
			<h3><?php _e( 'Shortcode:', 'lifeproblocker' );	?> [lifetime_product_blocker] </h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th><?php _e( 'Lifetime Product Blocker Activation', 'lifeproblocker' );?></th>
						<td>
							<label for="lifeproblocker_active"> 
								<input type="checkbox" name="<?php esc_attr_e( LIFE_PRO_BLOCKER_OPT_NAME ); ?>[lifeproblocker_active]" value="1" id="lifeproblocker_active" <?php checked('1', $this -> lifeproblocker_active, true);?>>
								<?php _e( 'Enable', 'lifeproblocker' );?>
							</label>
						</td>
					</tr>
                    <tr>
						<th><?php _e( 'Customer can purchase in lifetime', 'lifeproblocker' );?></th>
						<td><input class="regular-text" type="number" min="1" placeholder="7" name="<?php esc_attr_e( LIFE_PRO_BLOCKER_OPT_NAME ); ?>[user_can_purchase_lifetime]" value="<?php esc_attr_e( $this->get_option('user_can_purchase_lifetime') ); ?>">
						</td>
					</tr>
					
				</tbody>
			</table>
			<h3><?php _e( 'Order Status Label & Colors', 'lifeproblocker' );	?> </h3>
			<table class="form-table">
				<tbody>
                    <?php 
                        $order_statuses = wc_get_order_statuses();
                        $labels = '';
                        $statuses = array();
                        foreach ($order_statuses as $slug => $label) {
                            array_push($statuses, 'blocker_' . $slug);
                            $labels .= '<tr>';
                            $labels .= '<tr><th>'.$label.'</th>';
                            $labels .= '<td><input class="regular-text" type="text" name="' . esc_attr__( LIFE_PRO_BLOCKER_OPT_NAME ) . '[' . $slug . ']' . '" value="'.$this->get_option($slug).'"></td>';
                            $labels .= '<tr><th>'.__('Color', 'lifeproblocker').'</th>';
                            $labels .= '<td><input class="regular-text blocker_'.$slug.'" type="text" name="' . esc_attr__( LIFE_PRO_BLOCKER_OPT_NAME ) . '[' . $slug . '-theme_color]' . '" value="'.$this->get_option($slug. '-theme_color').'"></td>';
                            $labels .= '</tr>';
                        }

                        // New for cart item
                        $labels .= '<tr>';
                        $labels .= '<tr><th>'.__('Cart Item', 'lifeproblocker').'</th>';
                        $labels .= '<td><input class="regular-text" type="text" name="' . esc_attr__( LIFE_PRO_BLOCKER_OPT_NAME ) . '[cart_items]' . '" value="'.$this->get_option('cart_items').'"></td>';
                        $labels .= '<tr><th>'.__('Color', 'lifeproblocker').'</th>';
                        $labels .= '<td><input class="regular-text blocker_cart_items" type="text" name="' . esc_attr__( LIFE_PRO_BLOCKER_OPT_NAME ) . '[cart_items-theme_color]' . '" value="'.$this->get_option('cart_items-theme_color').'"></td>';
                        $labels .= '</tr>';
                        array_push($statuses, 'blocker_cart_items');

                        wp_localize_script( 'lifeproblocker-admin-script', 'blocker_theme_colors', array(
                            'item_classes' => $statuses
                        ));
                        echo $labels;
                    ?>
                   
				</tbody>
			</table>
		</div>

		<?php submit_button(); ?>
	</form>
</div>