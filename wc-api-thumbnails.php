<?php
/*
Plugin Name: WC API Thumbnails
Version:     0.0.1
Author:      Alexander Nelzin
Author URI:  https://asnelzin.ru
*/

/**
 * Everything is static at present.
 * We may go singletone route in the future if there is some state
 * to handle, for example if the list of protected fields can be
 * modified - perhaps through a filter hook - at the start.
 */

class Academe_Wc_Api_Thumbnails
{
    // Meta fields we want to protect, due to them being already handled
    // by the WC API.
    // To view or change these fields, go through the appropriate API.

    protected static $protected_fields = array(
        // A few WP internal fields should not be exposed.
        '_edit_lock',
        '_edit_last',
        // All these meta fields are already present in the
        // product_data in some form.
        '_visibility',
        '_stock_status',
        'total_sales',
        '_downloadable',
        '_virtual',
        '_regular_price',
        '_sale_price',
        '_purchase_note',
        '_featured',
        '_weight',
        '_length',
        '_width',
        '_height',
        '_sku',
        '_product_attributes',
        '_price',
        '_sold_individually',
        '_manage_stock',
        '_backorders',
        '_stock',
        '_upsell_ids',
        '_crosssell_ids',
        '_product_image_gallery',
        '_sale_price_dates_from',
        '_sale_price_dates_to',
    );

    protected static $product_type = array(
        'meta_simple',
        'meta_variable',
        'grouped',
        'external',
    );

    /**
     * Initialise all hooks at plugin initialisation.
     * It may be worth registering the hooks in two layers, so we
     * first check we have the capability and that WooCommerce is
     * installed, before registering the remaining hooks. Also can
     * check if we are being invoked by the WC API, as there is no
     * point registering these API hooks if we aren't.
     */
    public static function initialize()
    {
        // GET product: add in meta field to results.
        add_filter(
            'woocommerce_api_product_response',
            array('Academe_Wc_Api_Thumbnails', 'fetchCustomMeta'),
            10,
            4
        );
    }

    /**
     * Fetching a product detail.
     * Add in the custom meta fields if we have the capability.
     */
    public static function fetchCustomMeta($product_data, $product, $fields, $server) {
        // The admin and shop manager will have the capability "manage_woocommerce".
        // We only want users with this capability to see additional product meta fields.


        $product_data['meta'] = 'hui';

        return $product_data;
    }

    /**
     * Update or create a product.
     */
    public static function updateCustomMeta($id, $data) {
        // Create or update fields.
        if (!empty($data['custom_meta']) && is_array($data['custom_meta'])) {
            // Filter out protected fields.
            $custom_meta = array_diff_key(
                $data['custom_meta'],
                array_flip(static::$protected_fields)
            );

            foreach($custom_meta as $field_name => $field_value) {
                update_post_meta($id, $field_name, wc_clean($field_value));
            }
        }

        // Remove meta fields.
        if (!empty($data['remove_custom_meta']) && is_array($data['remove_custom_meta'])) {
            // Filter out protected fields.
            $remove_custom_meta = array_diff(
                $data['remove_custom_meta'],
                static::$protected_fields
            );

            foreach($remove_custom_meta as $key => $value) {
                // If the key is numeric, then assume $value is the field name
                // and all entries need to be deleted. Otherwise is is a specfic value
                // of a named meta field that should be removed.

                if (is_numeric($key)) {
                    delete_post_meta($id, $value);
                } else {
                    delete_post_meta($id, $key, $value);
                }
            }
        }
    }

}

Academe_Wc_Api_Thumbnails::initialize();