<?php
/**
 * Admin Settings
 */
class Lifetime_Pro_Blocker_Admin_Settings extends Lifetime_Pro_Blocker{

    /**
     * Constructor 
     */
    public function __construct() {

        parent::__construct();

        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_menu', array( $this, 'add_menu_item' ) );

    }

    /**
     * Register admin settings page
     */
    public function register_settings() {
        register_setting( LIFE_PRO_BLOCKER_OPT_GROUP, LIFE_PRO_BLOCKER_OPT_NAME );
    }

    /**
     * Admin menu items
     */
    public function add_menu_item() {
        add_menu_page(
            __( 'Lifetime Product Blocker', 'lifeproblocker' ),
            __( 'Lifetime Product Blocker', 'lifeproblocker' ),
            'manage_options',
            'lifeproblocker',
            array( $this, 'lifeproblocker_render_options_page' ),
            'dashicons-warning'
        );

    }

    /**
     * Admin settings options 
     */
    public function lifeproblocker_render_options_page(){
        require( __DIR__ . '/admin-options.php' );
    }

}

new Lifetime_Pro_Blocker_Admin_Settings();


