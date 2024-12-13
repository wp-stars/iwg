<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $product;

// hide price if the price is zero
$price = $product->get_price();
if($price == 0){
    echo '<h5 class="' . esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ) . ' mb-5"></h5>';
    return;
}
// for black friday deal: hide sale notice if price is missing
if (empty($price)) {
    echo '<style>.pwss-promo-text, .pwss-expires-text { display: none !important;}</style>';
}

?>
<div class="flex gap-4">
    <?php if ( $product->is_type( 'variable' ) ) : // variation prices aren't showing with h5 tag ?>
        <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?> mb-5">
            <?php echo $product->get_price_html(); ?>
        </p>
    <?php else : ?>
        <h5 class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?> mb-5">
            <?php echo $product->get_price_html(); ?>
        </h5>
    <?php endif; ?>
    <?php if ( !!$product->is_purchasable() && $product->is_in_stock() ) : ?>
        <div style="color: green;"><?php _e('Available', 'wps-juniper'); ?></div>
    <?php endif; ?>
</div>
