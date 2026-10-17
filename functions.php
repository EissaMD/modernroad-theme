<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * Pages built for this theme are titled "MR_<name>" in wp-admin. Their
 * templates print their own heading, so the prefixed title is never shown.
 */
const MODERNROAD_PAGE_PREFIX = 'MR_';

require_once __DIR__ . '/inc/pages.php';
require_once __DIR__ . '/inc/installer.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/shortcodes.php';
require_once __DIR__ . '/inc/emails.php';

/** Version string from file time so CSS changes bust the browser cache. */
function modernroad_asset_ver($rel_path) {
    $file = get_stylesheet_directory() . '/' . $rel_path;
    return file_exists($file) ? (string) filemtime($file) : wp_get_theme()->get('Version');
}

function modernroad_enqueue_style($handle, $rel_path, $deps = []) {
    wp_enqueue_style($handle, get_stylesheet_directory_uri() . '/' . $rel_path, $deps, modernroad_asset_ver($rel_path));
}

function modernroad_is_mr_page($post = null) {
    $post = get_post($post);
    return $post && $post->post_type === 'page' && strpos($post->post_title, MODERNROAD_PAGE_PREFIX) === 0;
}

/**
 * WooCommerce templates that only apply once the matching WooCommerce page
 * (Settings → Advanced → Page setup) is an MR_ page, so the old Elementor
 * pages keep working until they are switched, and switching back is a rollback.
 * Files live in templates/woocommerce/, not woocommerce/, so Woo won't pick
 * them up on its own.
 */
function modernroad_gated_templates() {
    return [
        'checkout/form-checkout.php' => 'checkout',
        'checkout/review-order.php'  => 'checkout',
        'myaccount/my-account.php'   => 'myaccount',
        'myaccount/navigation.php'   => 'myaccount',
        'myaccount/dashboard.php'    => 'myaccount',
        'myaccount/view-order.php'   => 'myaccount',
        'myaccount/form-login.php'   => 'myaccount',
        // Emails have no page: they switch on with Install
        'emails/email-header.php'        => 'installed',
        'emails/email-footer.php'        => 'installed',
        'emails/email-order-details.php' => 'installed',
        'emails/email-addresses.php'     => 'installed',
    ];
}

function modernroad_template_gate_open($gate) {
    return $gate === 'installed' ? modernroad_is_installed() : modernroad_woo_page_is_mr($gate);
}

function modernroad_woo_page_is_mr($page) {
    return function_exists('wc_get_page_id') && modernroad_is_mr_page(wc_get_page_id($page));
}

// wc_get_template runs after Woo's template-path cache, so switching pages takes effect immediately
add_filter('wc_get_template', function ($template, $template_name) {
    $gated = modernroad_gated_templates();
    if (isset($gated[$template_name]) && modernroad_template_gate_open($gated[$template_name])) {
        $file = get_stylesheet_directory() . '/templates/woocommerce/' . $template_name;
        if (file_exists($file)) {
            return $file;
        }
    }
    return $template;
}, 10, 2);

add_action('wp_enqueue_scripts', function () {
    modernroad_enqueue_style('modernroad-base', 'assets/css/base.css', ['hello-elementor']);

    if (modernroad_is_installed()) {
        modernroad_enqueue_style('modernroad-layout', 'assets/css/layout.css', ['modernroad-base']);
    }

    // Page styles: one file per page, loaded only where needed
    $is_wc = function_exists('is_checkout');
    if ($is_wc && is_order_received_page()) {
        modernroad_enqueue_style('modernroad-order', 'assets/css/order.css', ['modernroad-base']);
        modernroad_enqueue_style('modernroad-thankyou', 'assets/css/pages/thankyou.css', ['modernroad-order']);
    } elseif ($is_wc && is_checkout() && modernroad_woo_page_is_mr('checkout')) {
        modernroad_enqueue_style('modernroad-checkout', 'assets/css/pages/checkout.css', ['modernroad-base']);
    } elseif ($is_wc && is_account_page() && modernroad_woo_page_is_mr('myaccount')) {
        modernroad_enqueue_style('modernroad-tables', 'assets/css/tables.css', ['modernroad-base']);
        modernroad_enqueue_style('modernroad-order', 'assets/css/order.css', ['modernroad-base']);
        modernroad_enqueue_style('modernroad-account', 'assets/css/pages/account.css', ['modernroad-order']);
    }

    switch (modernroad_current_page_key()) {
        case 'home':
            modernroad_enqueue_style('modernroad-home', 'assets/css/pages/home.css', ['modernroad-base']);
            break;
        case 'new-order':
            modernroad_enqueue_style('modernroad-order-forms', 'assets/css/order-forms.css', ['modernroad-base']);
            break;
        case 'orders':
            // The orders page opens the car parts form in an edit overlay
            modernroad_enqueue_style('modernroad-tables', 'assets/css/tables.css', ['modernroad-base']);
            modernroad_enqueue_style('modernroad-order-forms', 'assets/css/order-forms.css', ['modernroad-base']);
            modernroad_enqueue_style('modernroad-orders', 'assets/css/pages/orders.css', ['modernroad-tables']);
            break;
        case 'car-report':
            modernroad_enqueue_style('modernroad-tables', 'assets/css/tables.css', ['modernroad-base']);
            break;
    }
}, 20);

// Orders have their own page (MR_Orders) and nothing is downloadable: drop both tabs.
// The endpoints still work, so view-order links keep opening.
add_filter('woocommerce_account_menu_items', function ($items) {
    if (modernroad_woo_page_is_mr('myaccount')) {
        unset($items['orders'], $items['downloads']);
    }
    return $items;
}, 100);

add_filter('body_class', function ($classes) {
    if (modernroad_is_installed()) {
        $classes[] = 'mr-installed';
    }
    $key = modernroad_current_page_key();
    if ($key) {
        $classes[] = 'mr-page';
        $classes[] = 'mr-page--' . $key;
    }
    return $classes;
});

add_filter('hello_elementor_page_title', function ($show) {
    return is_page() && modernroad_is_mr_page() ? false : $show;
}, 20);

add_filter('document_title_parts', function ($parts) {
    if (isset($parts['title']) && strpos($parts['title'], MODERNROAD_PAGE_PREFIX) === 0) {
        $parts['title'] = substr($parts['title'], strlen(MODERNROAD_PAGE_PREFIX));
    }
    return $parts;
});
