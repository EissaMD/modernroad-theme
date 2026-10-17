<?php
/**
 * Small helpers shared by the theme's templates.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Account page opened with ?mr_embed=1 (the orders table's view overlay):
 * header.php / footer.php print the content only.
 */
function modernroad_is_embed() {
    return !empty($_GET['mr_embed']) && function_exists('is_account_page') && is_account_page();
}

add_filter('show_admin_bar', function ($show) {
    return modernroad_is_embed() ? false : $show;
});

/*
 * Stale item meta left on orders #532–#920 by the old "update completed qty
 * (sfsp)" code snippet; nothing reads it any more. Hidden on the site, in
 * emails and on the wp-admin order screen; the data itself is untouched.
 */
const MODERNROAD_HIDDEN_ITEM_META = ['is_complete', 'product_cat'];

add_filter('woocommerce_order_item_get_formatted_meta_data', function ($meta) {
    return array_filter($meta, function ($m) {
        return !in_array($m->key, MODERNROAD_HIDDEN_ITEM_META, true);
    });
});

add_filter('woocommerce_hidden_order_itemmeta', function ($keys) {
    return array_merge($keys, MODERNROAD_HIDDEN_ITEM_META);
});

/** Whether the current user may see prices (set per user by the icycode-b2b plugin). */
function modernroad_user_sees_prices() {
    return (bool) get_user_meta(get_current_user_id(), 'b2b_show_price', true);
}

/** B2BKing customer group name of a user, or ''. */
function modernroad_user_group_name($user_id = 0) {
    $group_id = get_user_meta($user_id ?: get_current_user_id(), 'b2bking_customergroup', true);
    return $group_id && is_numeric($group_id) ? get_the_title((int) $group_id) : '';
}

/** Order status as an mr-badge, coloured by how far along the order is. */
function modernroad_status_badge($status) {
    $variants = [
        'completed'  => 'success',
        'processing' => 'info',
        'cancelled'  => 'danger',
        'failed'     => 'danger',
        'refunded'   => 'danger',
    ];
    $variant = $variants[$status] ?? '';
    return sprintf(
        '<span class="mr-badge%s">%s</span>',
        $variant ? ' mr-badge--' . $variant : '',
        esc_html(wc_get_order_status_name($status))
    );
}

/** Site logo (custom logo, or the site_logo option Elementor sets), as an <img>, or ''. */
function modernroad_logo_html($size = 'medium') {
    $logo_id = get_theme_mod('custom_logo') ?: get_option('site_logo');
    return $logo_id ? wp_get_attachment_image((int) $logo_id, $size, false, ['class' => 'mr-logo', 'alt' => get_bloginfo('name')]) : '';
}

/** Inline SVG icon (24px grid, stroke style). */
function modernroad_icon($name, $size = 20) {
    $paths = [
        'home'    => '<path d="M3 11 12 3l9 8"/><path d="M5 10v10h14V10"/><path d="M10 20v-6h4v6"/>',
        'plus'    => '<path d="M12 5v14M5 12h14"/>',
        'list'    => '<path d="M8 6h13M8 12h13M8 18h13"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/>',
        'car'     => '<path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M9 17h6M3 12h18"/>',
        'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'pin'     => '<path d="M12 21s-7-6.2-7-11a7 7 0 0 1 14 0c0 4.8-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/>',
        'logout'  => '<path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4"/><path d="M10 17l-5-5 5-5M5 12h11"/>',
        'box'     => '<path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5M12 13v8"/>',
        'file'    => '<path d="M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8z"/><path d="M14 3v5h5M9 13h6M9 17h6"/>',
        'users'   => '<circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0 1 14 0"/><path d="M16 4a3.5 3.5 0 0 1 0 7M18 14a7 7 0 0 1 4 6"/>',
        'menu'    => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'chevron' => '<path d="m6 9 6 6 6-6"/>',
        'check'   => '<path d="M20 6 9 17l-5-5"/>',
        'alert'   => '<circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01"/>',
        'dot'     => '<circle cx="12" cy="12" r="3"/>',
    ];
    return sprintf(
        '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">%2$s</svg>',
        $size,
        $paths[$name] ?? $paths['dot']
    );
}
