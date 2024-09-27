<?php

/**
 * @package           PluginPackage
 * @author            wp-stars
 * @copyright         2024 wp-stars gmbh
 *
 * @wordpress-plugin
 * Plugin Name:       WPS Dashboard PDF Rechnungen
 * Plugin URI:        https://wp-stars.com
 * Description:       Erzeugt einen neuen Tab im WooCommerce Dashboard, um PDF Rechnungen hochzuladen.
 * Version:           1.0.0
 * Requires PHP:      8.1
 * Author:            wp-stars gmbh
 * Author URI:        https://wp-stars.com
 * Text Domain:       wps-dashboard-pdf-rechnungen
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

//load user field groups
require_once 'fieldgroups/user.php';

// debugging by michaelritsch - 26.09.24
if (in_array( $_SERVER['REMOTE_ADDR'], ['88.116.97.118', '2a02:8388:2809:c880:a82f:50b:bdce:94bc'])){

    add_action('init', function () {
        add_rewrite_endpoint('rechnungen', EP_ROOT | EP_PAGES);
    });

    add_filter('woocommerce_account_menu_items', function ($items) {

        $current_user = wp_get_current_user();

        if($current_user instanceof WP_User){

            $results = get_field('wps_dashboardpdfs_list', 'user_'.$current_user->ID);

            if(!!$results) {

                return array_slice($items, 0, 3, true)
                    + array(
                        'rechnungen' => 'Rechnungen'
                    )
                    + array_slice($items, 3, count($items), true);
            }
        }

        return array_slice($items, 0, 3, true)
            + array_slice($items, 3, count($items), true);
    });

    add_action('woocommerce_account_rechnungen_endpoint', function(){

        $current_user = wp_get_current_user();

        if(!$current_user instanceof WP_User){
            return;
        }

        $results = get_field('wps_dashboardpdfs_list', 'user_'.$current_user->ID);


        if(is_array($results) && count($results) > 0){
            echo '<section class="article-content"><figure class="wp-block-table dashboard-table"><table>';
            echo '<thead><tr><th>Rechnungsnummer</th><th style="text-align: left;">Download PDF</th></tr></thead>';

            foreach ($results as $line){

                extract($line);
                extract($pdf);

                echo '<tr>';
                echo '<td>' . $invoice . '</td>';
                echo '<td>' . '<a style="text-decoration: underline;" href="' . $url . '" target="_blank">' . $title . '</a>' . '</td>';
                echo '</tr>';
            }
            echo '</table></figure></section>';
        }

    });

}