<?php
/**
 * Plugin Name:       WC API Product Thumbnails
 * Plugin URI:        https://github.com/asnelzin/wc-api-thumbnails
 * Description:       This plugin adds image thumbnails to API product responses.
 * Version:           0.0.3
 * Author:            Alexander Nelzin
 * License:           MIT License
 * License URI:       https://opensource.org/licenses/mit-license.php
 * Text Domain:       wc-api-thumbnails
 * GitHub Plugin URI: https://github.com/asnelzin/wc-api-thumbnails
 * GitHub Branch:     master
 */

add_filter( 'woocommerce_rest_prepare_product', 'custom_products_api_data', 90, 2 );
function custom_products_api_data( $response, $post ) {
    $product = wc_get_product( $post );

    foreach ($response->data['images'] as $index=>$image) {
        $response->data['meta'] = $image['id'];
        $thumbnail = wp_get_attachment_image_url( $image['id'], 'shop_catalog' );
        if ($thumbnail) {
            $response->data['images'][$index]['thumbnail_url'] = $thumbnail;
        }
    }

    return $response;
}
