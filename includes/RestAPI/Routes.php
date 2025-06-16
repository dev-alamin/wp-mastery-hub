<?php

namespace WPMASTERYHUB\RestAPI;

use WP_REST_Server;
use WPMASTERYHUB\RestAPI\Controllers\BookController;

class Routes {
    public function register_routes(){
        $book = new BookController();
        $route_namespace = 'wp-mastery/v1';

        register_rest_route( $route_namespace, '/books', [
                'method'              => WP_REST_Server::READABLE,
                'callback'            => [ $book, 'get_items' ],
                'permission_callback' => '__return_true',
                'args'                => [
                    'per_page' => [
                        'description'       => __( 'Number of posts to return', 'wp-mastery-hub' ),
                        'type'              => 'integer',
                        'default'           => 10,
                        'sanitize_callback' => 'absint',
                    ],
                    'page' => [
                        'description'       => __( 'Page number for pagination', 'wp-mastery-hub' ),
                        'type'              => 'integer',
                        'default'           => 1,
                        'sanitize_callback' => 'absint',
                    ],
                ],
            ],
        );

        register_rest_route( $route_namespace, '/books', [
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [ $book, 'create_item' ],
            'permission_callback' => function(){
                return current_user_can( 'edit_posts' );
            },
            'args' => [
                'title' => [
                    'required'          => true,
                    'type'              => 'string',
                    'sanitize_callabck' => 'sanitize_text_field',
                ],
                'content' => [
                    'type'              => 'string',
                    'sanitize_callabck' => 'wp_kses_post',
                ],
                'excerpt' => [
                    'type'              => 'string',
                    'sanitize_callabck' => 'wp_kses_post',
                ],
                'status' => [
                    'type'              => 'string',
                    'sanitize_callabck' => 'sanitize_key',
                    'default'           => 'publish',
                ],
                'thumbnail_url' => [
                    'type'              => 'string',
                    'description'       => __( 'External image URL to be used as thumbnail', 'wp-mastery-hub' ),
                    'sanitize_callback' => 'esc_url_raw',
                    'required'          => false,
                ],
            ],
        ]);
    }   
}