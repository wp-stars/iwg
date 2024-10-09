<?php


// #1 Register the new product type 'Musterbestellung'
add_filter( 'product_type_selector', function ( $types ) {
	$types['musterbestellung'] = 'Musterbestellung';

	return $types;
} );

// --------------------------
// #2 Add New Product Type Class
add_action( 'init', function () {

	class WC_Product_Musterbestellung extends WC_Product_Simple {

		public function get_type() {
			return 'musterbestellung';
		}
	}

} );

// --------------------------
// #3 Load New Product Type Class

add_filter( 'woocommerce_product_class', function ( $classname, $product_type ) {

	if ( 'musterbestellung' === $product_type ) {
		$classname = 'WC_Product_Musterbestellung';
	}

	return $classname;

},          10, 2 );


// --------------------------
// #4 Show Product Data General Tab Prices

add_action( 'woocommerce_product_data_panels', function () {
	global $product_object;
	if ( $product_object && 'musterbestellung' === $product_object->get_type() ) {
		wc_enqueue_js( "
            jQuery(document).ready(function($) {
                // Show the general tab and pricing options for the 'Musterbestellung' product type.
                $('.options_group.pricing').addClass('show_if_musterbestellung').show();
                $('.show_if_simple').addClass('show_if_musterbestellung').show();
                // Ensure general tab is visible
                $('li.general_options.general_tab').addClass('show_if_musterbestellung').show();
            });
        " );
	}
} );


// --------------------------
// #5 Show Add to Cart Button
add_action( 'woocommerce_musterbestellung_add_to_cart', function () {
	do_action( 'woocommerce_simple_add_to_cart' );
} );

// --------------------------
// #6 Adding Custom Fields to Frontend

add_action( 'woocommerce_before_add_to_cart_button', 'add_custom_fields_to_musterbestellung_frontend' );

function add_custom_fields_to_musterbestellung_frontend() {
	global $product;

	if ( 'musterbestellung' === $product->get_type() ) {
		// Check if the cookie exists and decode it
		$selectedProducts = getSampleProductCookieData();
		$options          = get_products_options();

		echo '<div class="musterbestellung-custom-fields">';
		for ( $i = 1; $i <= 3; $i ++ ) {
			// Determine the selected value for this iteration
			$selectedValue = '';
			// Determine the selected value for this iteration by finding the first instance
			if ( ! empty( $selectedProducts ) ) {
				foreach ( $selectedProducts as $index => $productId ) {
					if ( array_key_exists( $productId, $options ) ) {
						// Set the selected value to the first instance of the product ID
						$selectedValue = intval( $productId );
						// Remove this product ID from the array to avoid duplicate selections
						unset( $selectedProducts[ $index ] );
						break; // Stop the loop once the first match is found
					}
				}
			}

			// Pass the 'selected' option to pre-select the dropdown
			woocommerce_form_field( '_associated_product_' . $i, [
				'type'    => 'select',
				'id'      => '_associated_product_select_' . $i,
				'class'   => [ 'musterbestellung-input', 'block', 'w-full', 'bg-white', 'px-4', 'py-2', 'pr-8' ],
				'label'   => sprintf( __( 'Select Associated Product %d', 'woocommerce' ), $i ),
				'options' => $options,
				'default' => $selectedValue, // Use the 'selected' parameter to pre-select the option
			] );
		}
		echo '</div>';
	}
}

// Function to populate the options in the custom fields
function get_products_options() {
	$args = [
		'status'    => 'publish',
		'limit'     => - 1,
		'tax_query' => [
			[
				'taxonomy' => 'purchasability', // Your custom taxonomy name
				'field'    => 'slug',
				'terms'    => 'sample-available', // Your taxonomy term's slug
			],
		],
	];

	$products = wc_get_products( $args );
	$options  = [ '' => __( 'Select a Product', 'woocommerce' ) ];
	foreach ( $products as $product ) {
		if ( 'musterbestellung' !== $product->get_type() ) {
			$options[ $product->get_id() ] = $product->get_name();
		}
	}

	return $options;
}

function enqueueCustomJsForMusterbestellung() {
	global $product;

	if ( is_product() && $product->get_type() === 'musterbestellung' ) {
		wp_enqueue_script( 'single-musterbestellung-js', get_template_directory_uri() . '/assets/js/single-musterbestellung.js', [ 'jquery' ], '', true );

		// Localize script to inject PHP values if necessary (demonstrative; not used directly in your JS)
		wp_localize_script( 'single-musterbestellung-js', 'singleMusterbestellungParams', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'restUrl' => get_rest_url()
			// Any other data you might want to pass from PHP to your JS; this is just a placeholder
		] );
	}

	wp_enqueue_script( 'custom-musterbestellung-js', get_template_directory_uri() . '/assets/js/custom-musterbestellung.js', [ 'jquery' ], '', true );

	$products = [];

	if ( getSampleProductCookieData() ) {
		$products = pullMusterbestellungProductsFromCookie();
	}

	// musterbestellungNames

	$sampleBoxProductid = apply_filters( 'wpml_object_id', 13819, 'any', true );

	$sampleBoxItemName = wc_get_product( $sampleBoxProductid )->get_name();

	wp_localize_script( 'custom-musterbestellung-js', 'customMusterbestellungParams', [
		'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
		'restUrl'                  => get_rest_url(),
		'themeUrl'                 => get_template_directory_uri(),
		'musterbestellungProducts' => $products,
		'sampleBoxItemName'        => $sampleBoxItemName,
	] );
}

/**
 * @return stdClass[]
 */
function pullMusterbestellungProductsFromCookie(): array {
	$musterbestellungProducts = getSampleProductCookieData();

	return array_map( function ( $productId ) {
		// Assuming getProductImageById is a function that returns an image URL by product ID
		$local_product = new stdClass();

		$local_product->image = wp_get_attachment_image_src( get_post_thumbnail_id( $productId ), 'single-post-thumbnail' );
		$local_product->name  = get_the_title( $productId );
		$local_product->id    = $productId;

		return $local_product;
	}, $musterbestellungProducts );
}

add_action( 'wp_enqueue_scripts', 'enqueueCustomJsForMusterbestellung' );

add_action( 'rest_api_init', function () {
	register_rest_route( 'wps/v1', '/musterbestellung/', [
		'methods'             => 'GET',
		'callback'            => 'updateMusterbestellungProducts',
		'permission_callback' => '__return_true', // Adjust permissions based on your needs
	] );
} );


/**
 * Add the Musterbestellung product to the cart if it is not already in the cart.
 */
function manageSampleboxProductInCart( $sampleItemsNeeded ): void {
	// Product ID of the Sample Box in current language
	$sampleBoxProductid = apply_filters( 'wpml_object_id', 13819, 'any', true );

	$cart = WC()->cart->get_cart();

	// Check if the product is already in the cart and store its key
	$musterboxInCart          = array_filter( $cart, fn( $cartItem ) => $cartItem['product_id'] == $sampleBoxProductid );
	$musterboxIsInCart        = ! empty( $musterboxInCart );
	$cartItemKeyMusterproduct = $musterboxIsInCart ? array_key_last( $musterboxInCart ) : null;

	unset( $cart );

	if ( $musterboxIsInCart && ! $sampleItemsNeeded ) {
		WC()->cart->remove_cart_item( $cartItemKeyMusterproduct );
	}

	if ( ! $musterboxIsInCart && $sampleItemsNeeded ) {
		WC()->cart->add_to_cart( $sampleBoxProductid );
	}

	unset( $sampleCookieProducts );
}

function cartItemIsSample( mixed $cartItem ): bool {
	return isset( $cartItem['data'] ) && $cartItem['data']->get_type() == 'musterbestellung';
}

function updateMusterbestellungProducts( WP_REST_Request $request ): WP_REST_Response {
	$updatedProductIds = explode( ',', $request->get_param( 'ids' ) );
	$updatedProductIds = array_filter($updatedProductIds);

	error_log( print_r($updatedProductIds, true));

	$samplesCurrentlyInCart = getSampleProductsInCart();
	$sampleProductIdsInCart = array_map( fn( $cartItem ) => $cartItem['product_id'], $samplesCurrentlyInCart );

	// pull out missing product ids in updatedProductIds
	$removedProductIds = array_filter( $sampleProductIdsInCart, fn( $cookieProductId ) => ! in_array( $cookieProductId, $updatedProductIds ) );

	// pul out added product ids in updatedProductIds
	$addedProductIds = array_filter( $updatedProductIds, fn( $updatedProductId ) => ! in_array( $updatedProductId, $sampleProductIdsInCart ) );

	$cartNeedsUpdate = ! empty( $removedProductIds ) && ! empty( $addedProductIds );

	manageSampleboxProductInCart( ! empty( $updatedProductIds ) );

	// just return on no updates needed
	if ( ! $cartNeedsUpdate ) {
		$products = array_map( 'generateProductFrontendElement', $updatedProductIds );

		return new WP_REST_Response( $products, 200 );
	}

	// just remove all samples from cart if no samples are given
	if ( empty( $updatedProductIds ) ) {
		foreach ( $samplesCurrentlyInCart as $cartItemKey => $cartItem ) {
			WC()->cart->remove_cart_item( $cartItemKey );
		}

		manageSampleboxProductInCart( false );

		return new WP_REST_Response( [], 200 );
	}

	error_log( 'setting sample data' );

	// Update cart item with custom data
	foreach ( $samplesCurrentlyInCart as $cartItemKey => $cartItem ) {
		$productSampleData                                                        = array_map( 'setupMusterbestellungData', $updatedProductIds );
		WC()->cart->cart_contents[ $cartItemKey ]['musterbestellung_custom_data'] = $productSampleData;
	}

	unset( $samplesCurrentlyInCart );

	error_log( 'newly setting updated cart contents' );

	WC()->cart->set_session();

	unset( $currentCartContents );

	$products = array_map( 'generateProductFrontendElement', $updatedProductIds );

	return new WP_REST_Response( $products, 200 );
}

/**
 * @param string $id
 *
 * @return stdClass
 */
function generateProductFrontendElement( string $id ): stdClass {
	// Assuming getProductImageById is a function that returns an image URL by product ID
	$product = new \stdClass();

	$product->image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
	$product->name  = get_the_title( $id );
	$product->id    = $id;

	return $product;
}

add_filter( 'woocommerce_add_cart_item_data', 'add_musterbestellung_data_to_cart_item', 10, 3 );
function add_musterbestellung_data_to_cart_item( $cart_item_data, $product_id, $variation_id ) {
	if ( ! getSampleProductCookieData() ) {
		return $cart_item_data;
	}

	// Decode the JSON from the cookie
	$musterbestellungProducts = getSampleProductCookieData();

	if ( count( $musterbestellungProducts ) > 0 ) {
		$musterbestellungData                           = array_map( 'setupMusterbestellungData', $musterbestellungProducts );
		$cart_item_data['musterbestellung_custom_data'] = $musterbestellungData;
	}

	return $cart_item_data;
}

function setupMusterbestellungData( $product_id ): array {
	return [
		'product_id'   => $product_id,
		'product_name' => get_the_title( $product_id )
	];
}

add_filter( 'woocommerce_get_item_data', 'display_musterbestellung_data_in_cart', 10, 2 );

function display_musterbestellung_data_in_cart( $item_data, $cart_item ) {
	$isMusterbestellungsProduct  = cartItemIsSample( $cart_item );
	$cartItemHasCustomSampleData = isset( $cart_item['musterbestellung_custom_data'] );

	if ( ! $isMusterbestellungsProduct || ! $cartItemHasCustomSampleData ) {
		return $item_data;
	}

	$customData = $cart_item['musterbestellung_custom_data'];

	$sampleItemData = array_map( function ( $key ) use ( $customData ) {
		$index = array_search( $key, array_keys( $customData ) ) + 1;
		$value = $customData[ $key ];

		return [ 'name' => "Product $index", 'value' => $value['product_name'] ];
	}, array_keys( $customData ) );

	return array_merge( $item_data, $sampleItemData );
}

add_action( 'woocommerce_checkout_create_order_line_item', 'save_musterbestellung_data_with_order', 10, 4 );

function save_musterbestellung_data_with_order( $item, $cart_item_key, $values, $order ): void {
	if ( ! isset( $values['musterbestellung_custom_data'] ) ) {
		return;
	}

	foreach ( $values['musterbestellung_custom_data'] as $key => $value ) {
		$index = $key + 1;
		$item->add_meta_data( "Product $index ID", $value['product_id'] );
		$item->add_meta_data( "Product $index Name", $value['product_name'] );
	}
}

add_filter( 'woocommerce_add_to_cart_validation', 'limit_musterbestellung_product_in_cart', 10, 3 );

function limit_musterbestellung_product_in_cart( $passed, $product_id, $quantity ) {
	$product = wc_get_product( $product_id );

	if ( ! $product->get_type() == 'musterbestellung' ) {
		return $passed;
	}

	$currentSampleProducts = getSampleProductsInCart();

	if ( count( $currentSampleProducts ) >= 1 ) {
		return false;
	}

	return $passed;
}

add_filter( 'woocommerce_quantity_input_args', 'musterbestellung_quantity_input_args', 10, 2 );
function musterbestellung_quantity_input_args( $args, $product ) {
	if ( 'musterbestellung' !== $product->get_type() ) {
		return $args;
	}

	$args['max_value'] = 1;  // Set maximum quantity to 1
	$args['min_value'] = 0;  // Set minimum quantity to 1 to enforce single item only

	return $args;
}

function enqueue_disable_add_to_cart_script(): void {
	$musterbestellung_in_cart = false;

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		$product = wc_get_product( $cart_item['product_id'] );
		if ( $product && 'musterbestellung' === $product->get_type() ) {
			$musterbestellung_in_cart = true;
			break;
		}
	}

	if ( $musterbestellung_in_cart ) {
		wp_enqueue_script( 'disable-add-to-cart', get_template_directory_uri() . '/assets/js/disable-add-to-cart.js', [], false, true );
	}
}

add_action( 'wp_enqueue_scripts', 'enqueue_disable_add_to_cart_script' );

add_action( 'wp_loaded', 'maybe_load_cart', 5 );
/**
 * Loads the cart, session and notices should it be required.
 *
 * Note: Only needed should the site be running WooCommerce 3.6
 * or higher as they are not included during a REST request.
 *
 * @see https://plugins.trac.wordpress.org/browser/cart-rest-api-for-woocommerce/trunk/includes/class-cocart-init.php#L145
 * @since   2.0.0
 * @version 2.0.3
 */
function maybe_load_cart() {
	if ( version_compare( WC_VERSION, '3.6.0', '>=' ) && WC()->is_rest_api_request() ) {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return;
		}

		$rest_prefix = 'wps/v1/musterbestellung/';
		$req_uri     = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );

		$is_my_endpoint = ( false !== strpos( $req_uri, $rest_prefix ) );

		if ( ! $is_my_endpoint ) {
			return;
		}

		require_once WC_ABSPATH . 'includes/wc-cart-functions.php';
		require_once WC_ABSPATH . 'includes/wc-notice-functions.php';

		if ( null === WC()->session ) {
			$session_class = apply_filters( 'woocommerce_session_handler', 'WC_Session_Handler' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

			// Prefix session class with global namespace if not already namespaced
			if ( false === strpos( $session_class, '\\' ) ) {
				$session_class = '\\' . $session_class;
			}

			WC()->session = new $session_class();
			WC()->session->init();
		}

		/**
		 * For logged in customers, pull data from their account rather than the
		 * session which may contain incomplete data.
		 */
		if ( is_null( WC()->customer ) ) {
			if ( is_user_logged_in() ) {
				WC()->customer = new WC_Customer( get_current_user_id() );
			} else {
				WC()->customer = new WC_Customer( get_current_user_id(), true );
			}

			// Customer should be saved during shutdown.
			add_action( 'shutdown', [ WC()->customer, 'save' ], 10 );
		}

		// Load Cart.
		if ( null === WC()->cart ) {
			WC()->cart = new WC_Cart();
		}
	}
}

function getSampleProductCookieData(): array {
	$cookieExists = isset( $_COOKIE['musterbestellungProducts'] );

	return $cookieExists ? json_decode( stripslashes( $_COOKIE['musterbestellungProducts'] ), true ) : [];
}

function getSampleProductsInCart(): array {
	return array_filter( WC()->cart->get_cart(), 'cartItemIsSample' );
}

add_action( 'wp_ajax_clearCart', fn() => WC()->cart->empty_cart() );

/**
 * due to the WPML setup just being FUCKING STUPID!!!!!
 * this wpml translation helper is necessary for the english translation of the asked class to be recognized
 * filter mainly gets applied in:
 *
 * @see web/app/plugins/woocommerce-germanized/packages/woocommerce-germanized-shipments/src/ShippingMethod/ShippingMethod.php
 */
add_filter( 'woocommerce_gzd_shipping_method_rule_condition_package_shipping_classes_applies', function ( $package_data, $rule, $condition, $shipping_method ) {
	$operator_name = $condition['operator'];

	$classes = ! empty( $condition['classes'] ) ? array_map( 'absint',
		(array) $condition['classes'] ) : [];

	$classes_used_in_package = $package_data['package_shipping_classes'];

	$classes_with_translation = [];

	foreach ( $classes_used_in_package as $class_id ) {
		$class_trid        = apply_filters( 'wpml_element_trid', NULL, $class_id, 'tax_shipping_class' );
		$class_translation = apply_filters( 'wpml_get_element_translations', NULL, $class_trid, 'tax_shipping_class' );

		foreach ( $class_translation as $class_reference ) {
			$classes_with_translation[] = $class_reference->term_id;
		}
	}

	$match_exists = array_intersect( $classes_with_translation, $classes );

	return $operator_name === 'any_of' ? $match_exists : ! $match_exists;
}, 10, 4 );