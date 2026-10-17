<?php
/**
 * WooCommerce email look (active once the new design is installed).
 * Templates are in templates/woocommerce/emails/; this file sets colours,
 * sender, footer text and the CSS that WooCommerce inlines into each email.
 */

if (!defined('ABSPATH')) {
    exit;
}

const MODERNROAD_EMAIL_COLORS = [
    'woocommerce_email_base_color'            => '#c00404',
    'woocommerce_email_background_color'      => '#f3f4f6',
    'woocommerce_email_body_background_color' => '#ffffff',
    'woocommerce_email_text_color'            => '#1f2328',
];

add_action('init', function () {
    if (!modernroad_is_installed()) {
        return;
    }

    // Brand colours win over WooCommerce → Settings → Emails
    foreach (MODERNROAD_EMAIL_COLORS as $option => $color) {
        add_filter("pre_option_{$option}", function () use ($color) {
            return $color;
        });
    }

    add_filter('woocommerce_email_from_name', function () {
        return wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES);
    });

    add_filter('woocommerce_email_footer_text', function () {
        return '{site_title} — هذه رسالة آلية، يرجى عدم الرد عليها.';
    });

    add_filter('woocommerce_email_styles', 'modernroad_email_styles', 20);

    // Drop the "Process your orders on the go. Get the app." line from admin emails
    add_action('woocommerce_email', function ($mailer) {
        if (isset($mailer->emails['WC_Email_New_Order'])) {
            remove_action('woocommerce_email_footer', [$mailer->emails['WC_Email_New_Order'], 'mobile_messaging'], 9);
        }
    });
});

/** Appended to WooCommerce's email CSS; WooCommerce inlines it before sending. */
function modernroad_email_styles($css) {
    return $css . '
        #wrapper { padding: 32px 0; }
        #template_container { border: 1px solid #e5e7eb; border-radius: 12px !important; box-shadow: none; overflow: hidden; }
        #template_header { background: #ffffff; border-bottom: 1px solid #e5e7eb; border-top: 5px solid #c00404; color: #1f2328; border-radius: 0 !important; }
        #header_wrapper { padding: 28px 40px 24px; }
        #template_header h1 { color: #1f2328; font-size: 24px; font-weight: 700; text-shadow: none; line-height: 1.4; }
        .mr-email-logo { padding: 24px 40px 0; }
        .mr-email-logo img { max-height: 56px; width: auto; }
        #body_content table td { padding: 32px 40px 24px; }
        #body_content_inner { font-size: 15px; line-height: 1.7; }
        #body_content_inner p { margin: 0 0 16px; }
        h2 { color: #1f2328; font-size: 17px; font-weight: 700; margin: 0 0 12px; }
        .mr-email-section { margin: 0 0 28px; }
        .mr-email-meta { width: 100%; border-collapse: separate; border-spacing: 0; margin: 0 0 28px; }
        .mr-email-meta td { background: #f7f8fa; border-radius: 8px; padding: 12px 16px; }
        .mr-email-label { color: #6b7280; font-size: 12px; display: block; margin-bottom: 2px; }
        .mr-email-value { color: #1f2328; font-size: 15px; font-weight: 700; }
        .mr-email-table { width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb; }
        .mr-email-table th { background: #f7f8fa; color: #6b7280; font-size: 12px; font-weight: 600; padding: 10px 12px; border-bottom: 1px solid #e5e7eb; }
        .mr-email-table td { color: #1f2328; font-size: 14px; padding: 12px; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
        .mr-email-table tfoot th, .mr-email-table tfoot td { background: #ffffff; color: #1f2328; font-size: 14px; font-weight: 700; }
        .mr-email-table .wc-item-meta { margin: 4px 0 0; padding: 0; list-style: none; color: #6b7280; font-size: 12px; }
        .mr-email-table .wc-item-meta p { display: inline; margin: 0; }
        .mr-email-num { text-align: center !important; width: 80px; white-space: nowrap; }
        .mr-email-car td { background: #f7f8fa; padding: 10px 14px; border-bottom: 2px solid #ffffff; }
        .mr-email-button { display: inline-block; background: #c00404; color: #ffffff !important; font-weight: 700; text-decoration: none; padding: 12px 28px; border-radius: 8px; }
        .mr-email-address { border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px 16px; font-style: normal; line-height: 1.8; color: #1f2328; }
        #template_footer td { padding: 0; }
        #template_footer #credit { padding: 20px 24px 8px; color: #6b7280; font-size: 12px; }
        #template_footer #credit p { margin: 0; }
    ';
}
