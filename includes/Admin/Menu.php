<?php

namespace WPMASTERYHUB\Admin;

class Menu {

    public function register_hooks() {
        add_action( 'admin_menu', [ $this, 'menu_cb' ] );
    }

    public function menu_cb() {
        $page_title = __( 'WP Mastery HUB', 'wp-mastery-hub' );
        $menu_title = $page_title;
        $capability = 'manage_options';
        $menu_slug  = 'wp_mastery_hub';
        $icon_url   = 'dashicons-vault';
        $position   = 60;

        add_menu_page($page_title, $menu_title, $capability, $menu_slug, [ $this, 'menu_page' ], $icon_url, $position );
    }

    public function menu_page(){
        $file = __DIR__ . '/Views/list.php';
        
        if( file_exists( $file ) ) {
            include $file;
        }else{
            error_log( "Menu page not found: $file" );
        }
    }
}