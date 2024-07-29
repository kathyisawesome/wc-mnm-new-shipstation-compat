<?php

/**
 * Plugin Name: WooCommerce Mix and Match - New Shipstation Compat
 * Plugin URI:  https://woocommerce.com/products/woocommerce-mix-and-match-products/
 * Description: Test a new approach to ShipStation compatibility with Mix and Match.
 * Version: 1.0.0
 * Author: Kathy Darling
 * Author URI: kathyisawesome.com
 * 
 * Requires at least: 6.4.0
 * Tested up to: 6.6.0
 *
 * WC requires at least: 9.0.0
 * WC tested up to: 9.1.0
 * 
 * GitHub Plugin URI: https://github.com/kathyisawesome/wc-mnm-new-shipstation-compat
 * Primary Branch: trunk
 * Release Asset: true
 *
 * Copyright: © 2024 Kathy Darling
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * New ShipStation Compatibility Module
 * Adds compatibility with WooCommerce ShipStation.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_Shipstation_Compatibility Class.
 */
class New_WC_MNM_Shipstation_Compatibility {

	public static function init() {
		
		if ( ! did_action( 'wc_mnm_loaded' ) ) {
			return;
		}

		add_filter( 'wc_mnm_compatibility_modules', array( __CLASS__ ,  'disable_module' ) );

		// Make modifications only on Shipstation calls.
		add_action( 'woocommerce_api_wc_shipstation', array( __CLASS__, 'add_filters' ), 5 );

	}

	/**
	 * Turn off the default module.
	 * 
	 * @param array $modules
	 * @return array
	 */
	public static function disable_module( $modules ) {
		unset( $modules['shipstation'] );
		return $modules;
	}
	
	/**
	 * Modify the returned order items and products to return the correct weights for shipping.
	 */
	public static function add_filters() {
		add_filter( 'woocommerce_order_item_product', array( __CLASS__, 'get_product_from_item' ), 10, 2 );
		add_filter( 'woocommerce_order_item_get_formatted_meta_data',  array( __CLASS__, 'get_formatted_meta_data' ), 10, 2 );
	}


	/**
	 * Modifies parent/child order products in order to reconstruct an accurate representation of a bundle for shipping purposes:
	 * 
	 * - If packed together and weight not cumulative - children have zero weight
	 * - If packed separately and no physical container - parent has zero weight @todo- If the parent is virtual, should it even be imported as a line item?
	 *
	 * Used in combination with 'get_order_items', right above.
	 *
	 * @param  WC_Product  $product
	 * @param  array       $item
	 * @param  WC_Order    $order
	 * @return WC_Product
	 */
	public static function get_product_from_item( $product, $item, $order = false ) {

		if ( ! $product ) {
			return $product;
		}

		// If it's a container item...
		if ( wc_mnm_is_container_order_item( $item ) ) {

			if ( $product->needs_shipping() ) {

				// If it needs shipping, modify its weight to include the weight of all "packaged" items.
				$bundle_weight = $item->get_meta( '_bundle_weight', true );

				if ( $bundle_weight ) {
					$product->set_weight( $bundle_weight );
				}

			}

			// If it's a child item...
		} elseif ( wc_mnm_is_child_order_item( $item, $order ) ) {

			// If it's "packaged" in its container, set it to 0 weight.
			if ( $product->needs_shipping() && 'no' === $item->get_meta( '_mnm_item_needs_shipping', true ) ) {
				$product->set_weight( 0 );
			}
		}

		return $product;
	}


	/**
	 * Adds back the "Part of" item meta to display at ShipStation
	 * 
	 * ShipStation assumes everything in a single order will be packed in 1 box anyway, so it's a better visual to send
	 * every line item to ShipStation for the packing list.
	 *
	 * @param  array  $meta_data
	 * @param  WC_Order_Item_Product $item
	 * @return array
	 */
	public static function get_formatted_meta_data( $meta_data, $item ) {

		if ( wc_mnm_maybe_is_child_order_item( $item ) ) {

			$container_order_item = wc_mnm_get_order_item_container( $item );

			if ( $container_order_item ) {

				$display_key   = esc_html_x( 'Part of', '[Frontend]', 'woocommerce-mix-and-match-products' );
				$display_value = $container_order_item->get_name();

				// Create a psuedo $meta object.
				$meta = (object) array(
					'key'           => 'wc_mnm_part_of',
					'value'         => $display_value,
				);
		
				$meta_data[] = (object) array(
					'key'           => $meta->key,
					'value'         => $meta->value,
					'display_key'   => apply_filters( 'woocommerce_order_item_display_meta_key', $display_key, $meta, $item ),
					'display_value' => wpautop( apply_filters( 'woocommerce_order_item_display_meta_value', $display_value, $meta, $item ) ),
				);

			}
		}

		return $meta_data;
	}
	
}

add_action( 'plugins_loaded', array( 'New_WC_MNM_Shipstation_Compatibility', 'init' ), 20 );
