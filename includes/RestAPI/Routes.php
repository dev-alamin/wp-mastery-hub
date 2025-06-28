<?php

namespace WPMASTERYHUB\RestAPI;

use WP_REST_Server;
use WPMASTERYHUB\RestAPI\Controllers\BookController;

class Routes {
    public function register_routes(){
        $book = new BookController();
        $route_namespace = 'wp-mastery/v1';

        /**
         * Registers the REST API route for retrieving books.
         *
         * Route: /books
         * Method: WP_REST_Server::READABLE (GET)
         * Callback: $book->get_items
         * Permission: Public (no authentication required)
         *
         * Arguments:
         * - per_page (integer): Number of posts to return. Default is 10.
         * - page (integer): Page number for pagination. Default is 1.
         *
         * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/adding-custom-endpoints/
         */
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

        /**
         *
         * Routes:
         *  POST /books
         *    - Creates a new book.
         *    - Permissions: User must have 'edit_posts' capability.
         *    - Arguments:
         *        - title (string, required): Title of the book. Sanitized with sanitize_text_field.
         *        - content (string, optional): Content of the book. Sanitized with wp_kses_post.
         *        - excerpt (string, optional): Excerpt of the book. Sanitized with wp_kses_post.
         *        - status (string, optional, default 'publish'): Post status. Sanitized with sanitize_key.
         *        - thumbnail_url (string, optional): External image URL for thumbnail. Sanitized with esc_url_raw.
         *
         */
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

        /**
         * PUT/PATCH /books/(?P<id>\d+)
         *    - Updates an existing book by ID.
         *    - Permissions: User must have 'edit_post' capability for the given post ID.
         *    - Arguments:
         *        - id (integer, required): ID of the book. Must be a positive number.
         *        - title (string, optional): Title of the book. Sanitized with sanitize_text_field.
         *        - content (string, optional): Content of the book. Sanitized with wp_kses_post.
         *        - excerpt (string, optional): Excerpt of the book. Sanitized with wp_kses_post.
         *        - status (string, optional): Post status. Sanitized with sanitize_key.
         *        - thumbnail_url (string, optional): URL for thumbnail image. Sanitized with esc_url_raw.
         */
        register_rest_route( 'wp-mastery/v1', '/books/(?P<id>\d+)', [
            [
                'methods'             => WP_REST_Server::EDITABLE, // PUT/PATCH - update
                'callback'            => [ $book, 'update_item' ],
                'permission_callback' => function( $request ) {
                    $post_id = (int) $request->get_param('id');
                    return current_user_can( 'edit_post', $post_id );
                },
                'args' => [
                    'id' => [
                        'required'          => true,
                        'validate_callback' => function ( $param ) {
                            return is_numeric( $param ) && $param > 0;
                        },
                    ],
                    'title' => [
                        'description'       => __( 'Title of the book', 'wp-mastery-hub' ),
                        'type'              => 'string',
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                    'content' => [
                        'description'       => __( 'Content of the book', 'wp-mastery-hub' ),
                        'type'              => 'string',
                        'sanitize_callback' => 'wp_kses_post',
                    ],
                    'excerpt' => [
                        'description'       => __( 'Excerpt of the book', 'wp-mastery-hub' ),
                        'type'              => 'string',
                        'sanitize_callback' => 'wp_kses_post',
                    ],
                    'status' => [
                        'description'       => __( 'Post status', 'wp-mastery-hub' ),
                        'type'              => 'string',
                        'sanitize_callback' => 'sanitize_key',
                    ],
                    'thumbnail_url' => [
                        'description'       => __( 'URL for thumbnail image', 'wp-mastery-hub' ),
                        'type'              => 'string',
                        'sanitize_callback' => 'esc_url_raw',
                    ],
                ],
            ],
        ] );

        register_rest_route( 'wp-mastery/v1', '/books/(?P<id>\d+)', [
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => [ $book, 'delete_item' ],
            'permission_callback' => function() {
                return current_user_can( 'delete_posts' );
            },
            'args' => [
                'id' => [
                    'required'          => true,
                    'description'       => __( 'Book ID to delete', 'wp-mastery-hub' ),
                    'type'              => 'integer',
                    'validate_callback' => function( $param ) {
                        return is_numeric( $param ) && $param > 0;
                    },
                ],
            ],
        ]);

    }   
}