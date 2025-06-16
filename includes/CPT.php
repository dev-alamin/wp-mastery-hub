<?php

namespace WPMASTERYHUB;

class CPT {

    public function register_hooks() {
        add_action( 'init', [ $this, 'book_cpt_cb' ] );
    }

    public function book_cpt_cb() {
        $labels = [
            'name'                  => __( 'Books', 'wp-mastery-hub' ),
            'singular_name'         => __( 'Book', 'wp-mastery-hub' ),
            'add_new'               => __( 'Add New Book', 'wp-mastery-hub' ),
            'add_new_item'          => __( 'Add New Book', 'wp-mastery-hub' ),
            'edit_item'             => __( 'Add New Book', 'wp-mastery-hub' ),
            'new_item'              => __( 'Add New Book', 'wp-mastery-hub' ),
            'view_item'             => __( 'Add New Book', 'wp-mastery-hub' ),
            'view_items'            => __( 'Add New Book', 'wp-mastery-hub' ),
            'featured_image'        => __( 'Book Cover Image', 'wp-mastery-hub' ),
            'set_featured_image'    => __( 'Set Book Cover Image', 'wp-mastery-hub' ),
            'remove_featured_image' => __( 'Set Book Cover Image', 'wp-mastery-hub' ),

        ];

        $args = [
            'labels'          => $labels,
            'public'          => true,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'query_var'       => true,
            'capability_type' => 'post',
            'supports'        => ['title', 'editor', 'thumbnail', 'revisions' ],
            'show_in_rest'    => true,
            'rewrite'         => [
                'slug' => _x( 'book', 'wp-mastery-hub' ),
                // 'with_front' => true, // By default this is true
            ],
        ];
        
        // Register the post type with prefix so that does not conflict
        register_post_type( 'wmh_book', $args );
    }
}