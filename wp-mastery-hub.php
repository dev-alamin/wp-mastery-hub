<?php
/**
 * Plugin Name: WP Mastery Hub
 * Description: A Plugin to practice Advanced WordPress things.
 * Version: 1.0.0
 * Author: Al Amin
 * Author URI: https://almn.me
 * License: GPLv2 or later
 * @package WP Mastery Hub
 * Text Domain: wp-mastery-hub
 * Domain Path: /languages
 */

 defined( 'ABSPATH' ) || exit;

 require_once __DIR__ . '/vendor/autoload.php';

 final class WP_MASTERY_HUB {

    /**
     * Main property for instantiate the final class
     * This is singleton pattern, so we don't allow multiple times
     *
     * @var [type]
     */
    private static $instance = null;

    /**
     * Plugin's main constructor
     * As we use singleton so this has been private
     */
    private function __construct() {
        $this->init();
    }

    /**
     * Get Plugin Intance
     *
     * @return WP_MASTERY_HUB
     */
    public static function get_instance() : WP_MASTERY_HUB {

        if( ! self::$instance ) {
            self::$instance = new WP_MASTERY_HUB();
        }

        return self::$instance;
    }

    public function activate() {
        new WPMASTERYHUB\Admin();
        new WPMASTERYHUB\WP_Core();
    }

    public function init() {
        add_action( 'plugins_loaded', [ $this, 'activate' ] );
    }

    public function define_constant(){
        define( 'WMH_VERSION', '1.0.0' );
        define( 'WMH_ASSETS_URL', __DIR__ . '/assets/' );
        define( 'WMH_PLUGIN_URL', plugins_url( __FILE__ ) );
        define( 'WMH_INCLUDES_DIR', __DIR__ . '/includes' );
    }
 }

 // Call Main class
 function wp_master_hub(){
    return WP_MASTERY_HUB::get_instance();
 }

 // Kick-off the plugin
 wp_master_hub();