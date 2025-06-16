<?php

namespace WPMASTERYHUB\RestAPI;

use WP_Error;

class Helper {
        public static function upload_image_from_url( string $image_url, int $post_id = 0 ) : int|WP_Error {
        if ( empty( $image_url ) ) {
            return new WP_Error( 'invalid_image_url', 'Image URL is empty.' );
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        // Use media_sideload_image to upload
        $media_id = media_sideload_image( $image_url, $post_id, null, 'id' );

        if ( is_wp_error( $media_id ) ) {
            return $media_id;
        }

        return $media_id;
    }
}