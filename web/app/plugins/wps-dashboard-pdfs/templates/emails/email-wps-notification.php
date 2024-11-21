<?php
/**
 * Customer note email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-note.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer first name */ ?>

	<?php
		$informational_text = __('Hello, <br><br>
		Your invoice and delivery note are now available in your personal area in the customer dashboard
<br><br>
		For security reasons, you will first need to enter your email address and personal password.
<br><br>
		Do you have any further questions about the payment? <br> 
		We are happy to assist you!<br> 
		Please don’t hesitate to contact us via email or phone during our business hours', 'wps_dashboard_pdfs');
	?>

	<p>
		<?= $informational_text ?>
	</p>

    <a class="woocommerce-MyAccount-downloads-file button alt" href="https://www.iwgplating.com/my-account/buchhaltung/"><?= __('Click here to see the documents', 'wps_dashboard_pdfs'); ?></a>
	<p></p>

	<p style="margin-bottom: 0;"> <?= __('Business Hours:', 'wps_dashboard_pdfs') ?></p>
	<p style="margin: 0"> - E-Mail: <a href="mailto:office@iwgplating.com">office@iwgplating.com</a></p>
	<p style="margin: 0"> - <?= __('Phonenumber', 'wps_dashboard_pdfs') ?>: <a href="tel:+43 2287 71073">+43(0) 2287 71073</a></p>
<?php

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );