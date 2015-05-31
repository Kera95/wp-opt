<?php

namespace OptAdmin;

/**
 * Class WPOpt_Loader
 * @package OptAdmin
 * @author Kerim Karalic
 * This class loads everything that is needed for WP Theme Options to work
 */
class WPOpt_Loader {

    public function __construct() {
        /**
         * Initializes everything in the Dashboard for WPOpt
         */
        $this->wpopt_admin_init();
    }

    /**
     * Loads the functions from the class
     */
    private function wpopt_admin_init() {
        $this->wpopt_action_hooks();
    }

    /**
     * Takes no arguments, instead calls add_action() function for action hooks.
     * Loads what's needed for the dashboard
     * @return void
     */
    public function wpopt_action_hooks() {
        add_action( 'admin_menu', array( $this, 'wpopt_init_admin_page' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'wpopt_load_scripts' ) );
    }

    /**
     * No args, this function creates the Theme Options page under Appearance->Theme Options
     * @return void
     */
    public function wpopt_init_admin_page() {
        add_theme_page( 'Theme Options', 'Theme Options', 'manage_options', 'wpopt-menu-page', array( $this, 'wpopt_load_template' ) ); // Need to add Template
    }

    /**
     * Loads the template for Theme Options page.
     * @return void
     */
    public function wpopt_load_template() {
        require_once('templates/wpopt-menu-page.php');
    }

    /**
     * Loads the stylesheet for Theme Options page
     * @return void
     */
    public function wpopt_load_scripts() {
        wp_enqueue_style( 'wpopt-main-css', get_template_directory_uri() . '/wp-opt/admin/templates/css/wpopt-main.css', array(), '1.0.0' );
    }

}

?>