<?php

namespace WPMASTERYHUB\RestAPI\Controllers;

use WP_Query;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;
use WPMASTERYHUB\RestAPI\Helper;
use WPMASTERYHUB\RestAPI\Routes;

class BookController {

    public function register_hooks(){
        add_action( 'rest_api_init', [ new Routes(), 'register_routes' ] );
    }

    /**
     * Get Books
     *
     * @param WP_REST_Request $request
     * @return WP_REST_Response
     */
    public function get_items( WP_REST_Request $request ) : WP_REST_Response | WP_Error {
        $per_page = $request->get_param( 'per_page' );
        $paged    = $request->get_param( 'page' ) ?: 1;

        $query_args = [
            'post_type'      => 'wmh_book',
            'posts_per_page' => $per_page,
            'post_status'    => 'public',
            'paged'          => $paged,
        ];

        $query = new WP_Query( $query_args );

        $data = [];

        // If there are NO posts at all:
        if ( ! $query->have_posts() ) {
            // But we requested a non-first page? Then it's a page overflow.
            if ( $paged > 1 ) {
                return new WP_Error(
                    'rest_book_page_not_found',
                    __( 'Page number exceeds available pages.', 'wp-mastery-hub' ),
                    [ 'status' => 400 ]
                );
            }

            // Otherwise, return empty valid response
            return rest_ensure_response([
                'data' => [],
                'meta' => [
                    'total'    => 0,
                    'pages'    => 0,
                    'per_page' => (int) $per_page,
                    'current'  => (int) $paged,
                ],
            ]);
        }

        if( $query->have_posts() ) {

            while( $query->have_posts() ) {
                $query->the_post();
                $book_id = get_the_ID();

                $data[] = [
                    'id'            => $book_id,
                    'title'         => get_the_title(),
                    'content'       => get_the_content(),
                    'excerpt'       => get_the_excerpt(),
                    'date'          => get_the_date( 'c' ),
                    'modified'      => get_the_modified_date( 'c' ),
                    'thumbnail_url' => get_the_post_thumbnail_url(),
                    'permalink'     => get_permalink($book_id, 'full' ),
                ];
            }
            wp_reset_postdata();
        }

        return rest_ensure_response([
            'data' => $data,
            'meta' => [
                'total'    => $query->found_posts,
                'pages'    => $query->max_num_pages,
                'per_page' => (int) $request->get_param( 'per_page' ),
                'current'  => (int) $request->get_param( 'page' ) ?: 1,
                ],
            ] );
    }

    /**
     * Handles the creation of a new "wmh_book" post via the REST API.
     *
     * Validates and sanitizes input parameters, inserts a new book post,
     * optionally uploads and sets a featured image from a provided URL,
     * and returns the created post data or an error.
     *
     * @param WP_REST_Request $request The REST API request object containing post data.
     *
     * @return WP_REST_Response|WP_Error The response containing the created post data, or a WP_Error on failure.
     */
    public function create_item( WP_REST_Request $request ) : WP_REST_Response | WP_Error {
        $title    = sanitize_text_field( $request->get_param( 'title' ) );
        $content  = wp_kses_post( $request->get_param( 'content' ) );
        $excerpt  = wp_kses_post( $request->get_param( 'excerpt' ) );
        $status   = sanitize_key( $request->get_param( 'status' ) );
        $thum_url = esc_url_raw( $request->get_param( 'thumbnail_url' ) );

         // Basic validation
        if ( empty( $title ) ) {
            return new \WP_Error(
                'rest_book_title_required',
                __( 'Book title is required.', 'wp-mastery-hub' ),
                [ 'status' => 400 ]
            );
        }

        // Insert post
        $post_id = wp_insert_post( [
            'post_type'    => 'wmh_book',
            'post_title'   => $title,
            'post_content' => $content,
            'post_excerpt' => $excerpt,
            'post_status'  => $status ?: 'publish',
        ] );

        // Check for insert failure
        if ( is_wp_error( $post_id ) ) {
            return $post_id;
        }

        if ( ! empty( $thum_url ) ) {
            $media_id = Helper::upload_image_from_url( $thum_url, $post_id );

            if ( ! is_wp_error( $media_id ) ) {
                set_post_thumbnail( $post_id, $media_id );
            }
        }

        // Return the newly created post
        return rest_ensure_response( [
            'id'        => $post_id,
            'title'     => get_the_title( $post_id ),
            'content'   => get_post_field( 'post_content', $post_id ),
            'excerpt'   => get_post_field( 'post_excerpt', $post_id ),
            'status'    => get_post_status( $post_id ),
            'permalink' => get_permalink( $post_id ),
            'thumbnail_url' => get_the_post_thumbnail_url( $post_id, 'full' )
        ] );
    }

    public function update_item(){

    }
}