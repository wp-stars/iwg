<?php

/**
 * @package           PluginPackage
 * @author            wp-stars
 * @copyright         2024 wp-stars gmbh
 *
 * @wordpress-plugin
 * Plugin Name:       WPS Dashboard PDF Rechnungen
 * Plugin URI:        https://wp-stars.com
 * Description:       Erzeugt einen neuen Tab im WooCommerce Dashboard, um PDF Rechnungen anzuzeigen.
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

class DashboardPDFs
{

    public string $endpoint = 'buchhaltung';
    public string $menueItem = 'Buchhaltung';
    public int $userId = 0;

    public function __construct()
    {
        require_once 'fieldgroups/user.php';

        add_action('init', [$this, 'init']);
        add_action('woocommerce_account_'.$this->endpoint.'_endpoint', [$this, 'endpoint']);

        // ajax request + metabox with button
        add_action('admin_enqueue_scripts', [$this, 'profileScripts']);

        add_action('show_user_profile', [$this, 'addMetaBox']);
        add_action('edit_user_profile', [$this, 'addMetaBox']);

        add_action('wp_ajax_profile_notification_profile_action', [$this, 'sendNotification']);
        add_action('add_meta_boxes', [$this, 'setupMetaBoxes']);

        // custom email class
        add_filter( 'woocommerce_locate_template', [$this, 'loadCustomEmailTemplate'], 10, 3 );
        add_filter( 'woocommerce_email_classes', [$this, 'registerNotificationEmailClass'] );

        add_filter('woocommerce_account_menu_items', [$this, 'menu']);
    }

    public function init(){
        add_rewrite_endpoint($this->endpoint, EP_ROOT | EP_PAGES);
    }

    public function setupMetaBoxes(){

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" style="width: 1em; height: 1em; scale: 2; transform: translateX(2px);">
          <path d="M2.5 3A1.5 1.5 0 0 0 1 4.5v.793c.026.009.051.02.076.032L7.674 8.51c.206.1.446.1.652 0l6.598-3.185A.755.755 0 0 1 15 5.293V4.5A1.5 1.5 0 0 0 13.5 3h-11Z" />
          <path d="M15 6.954 8.978 9.86a2.25 2.25 0 0 1-1.956 0L1 6.954V11.5A1.5 1.5 0 0 0 2.5 13h11a1.5 1.5 0 0 0 1.5-1.5V6.954Z" />
        </svg>';

        add_meta_box(
            'wps-dashboard-pdf-box',
            $svg . 'Buchaltung',
            [$this, 'addMetaBox'],
            'woocommerce_page_wc-orders',
            'side',
            'high'
        );

        /*add_meta_box(
            'wps-dashboard-acf-box',
            'Rechnungen & Lieferscheine hochladen',
            [$this, 'acfMetabox'],
            'woocommerce_page_wc-orders',
            'normal'
        );*/

    }

    public function acfMetabox(){
        global $post;
        acf_form(array(
            'post_id' => $post->ID,  // Ensure fields load for the correct order
            'field_groups' => array('group_66f567b2bd302'),  // Replace with your ACF field group ID
            'form' => true,
            'uploader' => 'wp',
            'honeypot' => true,
        ));
    }

    public function addMetaBox(){

        $html = '';
        $html .= "<style>body.user-edit-php div#wps-pdf-notification-box{border: dashed 1px #555; padding: 10px; border-radius: 5px; max-width: 300px; float: right;}</style>";

        $lastNotification = get_user_meta($this->userId, 'wps_last_notification_datetime', true);
        if(!$lastNotification){
            $lastNotification = 'keine Benachrichtigung versendet';
        }else{
            $lastNotification = 'letzte Benachrichtigung: ' . $lastNotification;
        }

        $html .= '<div id="wps-pdf-notification-box">';
        $html .= '<p>'.__('Senden Sie ihren Kunden Benachrichtigen über neu hochgeladene Dokumente').'</p>';
        $html .= '<form method="post" action="">';
        $html .= '<div style="display: flex; flex-direction: column; justify-content: end; padding-right: 10px; max-width: 350px;">';
        $html .= '<button id="customerNotificationAboutNewDocuments" style="display: flex; flex-direction: row; justify-content: center; align-items: center; gap: 15px; text-align: center; margin-bottom: 15px;" class="button-primary">' . __('Benachrichtigung senden') . '</button>';

        if(get_current_screen()->id !== 'user-edit') {
            $html .= '<a target="_blank" href="/wp/wp-admin/user-edit.php?user_id=' . $this->userId . '" style="display: flex; flex-direction: row; justify-content: center; align-items: center; gap: 15px; text-align: center; margin-bottom: 15px;" class="button-secondary">' . __('Zum Kundenprofil') . '</a>';
        }

        $html .= '<span style="font-size: 11px;" id="notificationResponse">'.$lastNotification.'</span>';
        $html .= '</div>';
        $html .= '</form>';
        $html .= '</div>';

        echo $html;

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

    public function profileScripts() {

        wp_enqueue_script(
            'wps-profile-ajax-script',
            plugins_url('/wps-dashboard-pdfs/assets/js/profile.js'),
            array('jquery'),
            null,
        );


        $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : 0;
        $order_id = isset($_GET['id']) ? $_GET['id'] : 0;

        $order = wc_get_order( $order_id );
        $user = get_user_by('ID', $user_id);

        if(!!$order){
            // get userID from single-order
            $this->userId = $order->get_user_id();
            // get userID from single-user
        }else if($user instanceof WP_User){
            $this->userId = $user->ID;
        }else{
            // return if there is no id - don't use current user
            return;
        }

        wp_localize_script('wps-profile-ajax-script', 'profileNotifactionAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('profile_notification_nonce'),
            'user_id'  => $this->userId
        ));
    }

    public function sendNotification()
    {
        check_ajax_referer('profile_notification_nonce', 'nonce');


        $this->userId = (int) $_POST['user'];
        $current_user = get_user_by('ID', $this->userId);

        // check if user exists
        if(!$current_user instanceof WP_User){
            echo "Benutzer konnte nicht gefunden werden.";
            wp_die();
        }

        $date = new DateTime('now', new DateTimeZone('Europe/Berlin'));

        // check if notification was sent within the last hour
        $lastNotification = get_user_meta($this->userId, 'wps_last_notification_datetime', true);
        if(!!$lastNotification){
            $lastNotification = new DateTime($lastNotification, new DateTimeZone('Europe/Berlin'));
            $diff = $date->diff($lastNotification);
            if($diff->i < 5){
                echo "Es wurde bereits eine Benachrichtigung innerhalb der letzten 5 Minuten versendet.";
                wp_die();
            }
        }

        // send email
        $submission = $this->sendEmail($current_user->user_email);
        if(false === $submission){
            echo "Die Benachrichtigung konnte nicht versendet werden.";
            wp_die();
        }

        // mark notification as sent
        update_user_meta($current_user->ID, 'wps_last_notification_datetime', $date->format('Y-m-d H:i:s'));
        echo "Eine Benachrichtung wurde an folgende E-Mail versendet: <strong>{$current_user->user_email}</strong>.";
        wp_die();
    }

    public function sendEmail(string $email): bool
    {
        // send email
        $mailObject = WC()->mailer()->emails['WC_Notification_Email'];
        return $mailObject->trigger($email);
    }

    public function loadCustomEmailTemplate($template, $template_name, $template_path){

        $plugin_path = plugin_dir_path( __FILE__ ) . 'templates/emails/';
        if ( file_exists( $plugin_path . $template_name ) ) {
            $template = $plugin_path . $template_name;
        }

        return $template;
    }

    public function registerNotificationEmailClass($email_classes){
        include_once plugin_dir_path( __FILE__ ) . 'classes/class-wc-wps-notification-email.php';
        $email_classes['WC_Notification_Email'] = new WC_wps_notification_Email();

        return $email_classes;
    }

}

new DashboardPDFs();