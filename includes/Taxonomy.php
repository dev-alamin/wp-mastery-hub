<?php

namespace WPMASTERYHUB;

class Taxonomy {

    public function register_hooks() {
        add_action( 'init', [ $this, 'register_tax' ] );
    }

    public function register_tax() {

        $labels = [
            'name'          => __( 'Book Genre', 'wp-mastery-hub' ),
            'singular_name' => __( 'Book Genre', 'wp-mastery-hub' ),
            'add_new_item'  => __( 'Add New Genre', 'wp-mastery-hub' ),
            'not_found'     => __( 'No Genre Found', 'wp-mastery-hub' ),

        ];

        $args = [
            'labels'            => $labels,
            'public'            => true,
            'hierarchical'      => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
        ];

        register_taxonomy( 'genre', 'wmh_book', $args );
    }
}