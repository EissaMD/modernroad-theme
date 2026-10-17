<?php
/**
 * Until the new design is installed, use the parent theme's header (which
 * renders the Elementor header).
 */

if (!defined('ABSPATH')) {
    exit;
}

if (modernroad_is_embed()) {
    get_template_part('template-parts/embed-header');
    return;
}

if (!modernroad_is_installed()) {
    require get_template_directory() . '/header.php';
    return;
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', 'hello-elementor'); ?></a>

<?php get_template_part('template-parts/mr-header'); ?>
