<?php
/**
 * Plugin Name:       WC API Product Thumbnails
 * Plugin URI:        https://github.com/asnelzin/wc-api-thumbnails
 * Description:       This plugin adds image thumbnails to API product responses.
 * Version:           0.0.4
 * Author:            Alexander Nelzin
 * License:           MIT License
 * License URI:       https://opensource.org/licenses/mit-license.php
 * Text Domain:       wc-api-thumbnails
 * GitHub Plugin URI: https://github.com/asnelzin/wc-api-thumbnails
 * GitHub Branch:     master
 */

add_filter( 'woocommerce_rest_prepare_product', 'custom_products_api_data', 90, 2 );
function custom_products_api_data( $response, $post ) {
    $sizes = get_intermediate_image_sizes();

    foreach ($response->data['images'] as $index=>$image) {
        $response->data['meta'] = $image['id'];
        $response->data['images'][$index]['sizes'] = array();
        foreach ($sizes as $size) {
            $thumbnail = wp_get_attachment_image_url( $image['id'], $size );
            if ($thumbnail) {
                $response->data['images'][$index]['sizes'][$size] = $thumbnail;
            }
        }

    }

    return $response;
}
