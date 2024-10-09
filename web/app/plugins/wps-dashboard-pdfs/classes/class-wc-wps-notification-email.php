<?php

if ( ! class_exists( 'WC_wps_notification_Email' ) ) {

    class WC_wps_notification_Email extends WC_Email {

        // Constructor
        public function __construct() {
            $this->id          = 'wps_notification_email';
            $this->title       = 'Benachrichtigung: Neue Buchhaltungsdok.';
            $this->description = 'E-Mail erstellt durch das Plugin: wps-dashboard-pdf';

            $this->heading              = __( 'Buchhaltung', 'wps-dashboard-pdf' );
            $this->subject              = __( 'Buchhaltung: Es sind neue Dokumente sind verfügbar', 'wps-dashboard-pdf' );
            $this->recipient            =  '';

            // Templates
            $this->template_html  = 'email-wps-notification.php';
            $this->template_plain = 'plain-email-wps-notification.php';
            $this->template_base  = plugin_dir_path( __FILE__ ) . '../templates/emails/';

            parent::__construct();
        }

        // Trigger email
        public function trigger(string $email) {

            $this->recipient = $email;

            if ( ! $this->recipient ) {
                return false;
            }

            if ( $this->is_enabled() && $this->get_recipient() ) {
                $emailSent = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

                if ( $emailSent ) {
                    return true;
                }
            }

            return false;
        }

        // Get email content
        public function get_content_html() {
            return wc_get_template_html( $this->template_html, array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false,
                'email'         => $this,
            ), '', $this->template_base );
        }

        // Get plain email content
        public function get_content_plain() {
            return wc_get_template_html( $this->template_plain, array(
                'order'         => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => true,
                'email'         => $this,
            ), '', $this->template_base );
        }
    }
}