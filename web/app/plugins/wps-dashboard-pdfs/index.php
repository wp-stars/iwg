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

// debugging by michaelritsch - 26.09.24
if (!in_array( $_SERVER['REMOTE_ADDR'], ['88.116.97.118', '2a02:8388:2809:c880:a82f:50b:bdce:94bc'])){
    //return;
}

class DashboardPDFs
{

    public string $endpoint = 'buchhaltung';
    public string $menueItem = 'Buchhaltung';

    public function __construct()
    {
        require_once 'fieldgroups/user.php';

        add_action('init', [$this, 'init']);
        add_action('woocommerce_account_'.$this->endpoint.'_endpoint', [$this, 'endpoint']);
        //add_action('show_user_profile', [$this, 'addMetaBox']);
        //add_action('edit_user_profile', [$this, 'addMetaBox']);

        add_filter('woocommerce_account_menu_items', [$this, 'menu']);
    }

    public function init(){
        add_rewrite_endpoint($this->endpoint, EP_ROOT | EP_PAGES);
    }

    public function addMetaBox(){
        echo '<p>Es wurden neue Dokumente hochgeladen.</p>';
    }

    public function menu($items){

        $current_user = wp_get_current_user();

        if($current_user instanceof WP_User){

            $results = get_field('wps_dashboardpdfs_list', 'user_'.$current_user->ID);

            if(!!$results) {

                return array_slice($items, 0, 3, true)
                    + array(
                        $this->endpoint => $this->menueItem
                    )
                    + array_slice($items, 3, count($items), true);
            }
        }

        return array_slice($items, 0, 3, true)
            + array_slice($items, 3, count($items), true);

    }

    public function endpoint(){
        $html = '';
        $current_user = wp_get_current_user();

        if(!$current_user instanceof WP_User){
            return;
        }

        $results = get_field('wps_dashboardpdfs_list', 'user_'.$current_user->ID);

        if(is_array($results) && count($results) > 0){
            $html .= '<section class="article-content"><figure class="wp-block-table dashboard-table"><table>';
            $html .= '<thead><tr>';
            $html .= '<th>' . __('Onlineshop Bestellnr.') . '</th>';
            $html .= '<th style="text-align: left;">Rechnung</th>';
            $html .= '<th style="text-align: left;">Lieferschein</th>';
            $html .= '</tr></thead>';

            foreach ($results as $line){

                extract($line);

                // set default values
                if(!$invoice) $invoice = 'n.a.';
                if(!$lieferscheinNumber) $lieferscheinNumber = 'n.a.';
                if(!$invoiceNumber) $invoiceNumber = 'n.a.';

                // create link if pdf is available
                if(!!$pdf && isset($pdf['url'])){
                    $invoiceNumber = $this->toLink($pdf['url'],$invoiceNumber);
                }

                // create link if lieferschein is available
                if(!!$lieferschein && isset($lieferschein['url'])){
                    $lieferscheinNumber = $this->toLink($lieferschein['url'],$lieferscheinNumber);
                }

                $html .= '<tr>';
                $html .= '<td>' . $invoice . '</td>';
                $html .= '<td>' . $invoiceNumber . '</td>';
                $html .= '<td>' . $lieferscheinNumber . '</td>';
                $html .= '</tr>';
            }
            $html .= '</table></figure></section>';
        }

        echo $html;
    }

    public function toLink(string $url='', string $label=''): string
    {
        return sprintf("<a style='text-decoration: underline;' href='%s' target='_blank'>%s</a>",$url,$label);
    }
}

new DashboardPDFs();