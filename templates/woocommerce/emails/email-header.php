<?php
/**
 * Email Header (ModernRoad override): logo, then the heading on a white bar
 * with a red top line. Used once the new design is installed.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 7.4.0
 *
 * @var string $email_heading
 */

if (!defined('ABSPATH')) {
    exit;
}

$logo_id  = get_theme_mod('custom_logo') ?: get_option('site_logo');
$logo_url = $logo_id ? wp_get_attachment_image_url((int) $logo_id, 'medium') : get_option('woocommerce_email_header_image');
$dir      = is_rtl() ? 'rtl' : 'ltr';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo('charset'); ?>" />
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <title><?php echo get_bloginfo('name', 'display'); ?></title>
    </head>
    <body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
        <table width="100%" id="outer_wrapper">
            <tr>
                <td><!-- Deliberately empty to support consistent sizing and layout across multiple email clients. --></td>
                <td width="600">
                    <div id="wrapper" dir="<?php echo esc_attr($dir); ?>">
                        <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
                            <tr>
                                <td align="center" valign="top">
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_container">
                                        <tr>
                                            <td align="center" valign="top">
                                                <!-- Header -->
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_header">
                                                    <?php if ($logo_url) : ?>
                                                        <tr>
                                                            <td class="mr-email-logo" style="text-align:<?php echo is_rtl() ? 'right' : 'left'; ?>;">
                                                                <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" height="56" style="height:56px;width:auto;border:0;" />
                                                            </td>
                                                        </tr>
                                                    <?php endif; ?>
                                                    <tr>
                                                        <td id="header_wrapper">
                                                            <h1><?php echo esc_html($email_heading); ?></h1>
                                                        </td>
                                                    </tr>
                                                </table>
                                                <!-- End Header -->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="center" valign="top">
                                                <!-- Body -->
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body">
                                                    <tr>
                                                        <td valign="top" id="body_content">
                                                            <!-- Content -->
                                                            <table border="0" cellpadding="20" cellspacing="0" width="100%">
                                                                <tr>
                                                                    <td valign="top">
                                                                        <div id="body_content_inner">
