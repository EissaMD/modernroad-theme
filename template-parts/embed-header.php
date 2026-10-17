<?php
/**
 * Bare document start for embedded account pages (?mr_embed=1): no site
 * header, account menu or page heading — just the content, e.g. the order
 * view inside the orders table's overlay.
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
    <style>
        body.mr-embed { background: #f7f8fa; }
        .mr-embed .site-main { max-width: none; margin: 0; padding: 24px 20px 32px; }
        .mr-embed .page-header,
        .mr-embed .mr-page-header,
        .mr-embed .woocommerce-MyAccount-navigation,
        .mr-embed .mr-order-back { display: none !important; }
        .mr-embed .mr-account { display: block; }
        .mr-embed .woocommerce-MyAccount-content { float: none; width: auto; }
    </style>
</head>
<body <?php body_class('mr-embed'); ?>>
<?php wp_body_open(); ?>
