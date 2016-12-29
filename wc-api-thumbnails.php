<?php
/*
Plugin Name: WC API Thumbnails
Version:     0.0.2
Author:      Alexander Nelzin
Author URI:  https://asnelzin.ru
*/

add_filter( 'woocommerce_rest_prepare_product', 'custom_products_api_data', 90, 2 );
function custom_products_api_data( $response, $post ) {
    $product = wc_get_product( $post );

    foreach ($response->data['images'] as $index=>$image) {
        $thumbnail = wp_get_attachment_image_url( $image->id, 'shop_catalog' );
        $response->data['images'][$index] = $thumbnail;
    }
    
    return $response;
}
