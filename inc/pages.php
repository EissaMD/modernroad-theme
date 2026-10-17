<?php
/**
 * The MR_ pages this theme builds, and lookups for them.
 *
 * Page IDs differ per site, so the installer stores them in an option
 * (MODERNROAD_PAGES_OPTION) and everything else looks them up by key.
 */

if (!defined('ABSPATH')) {
    exit;
}

const MODERNROAD_PAGES_OPTION     = 'modernroad_pages';
const MODERNROAD_INSTALLED_OPTION = 'modernroad_installed';
const MODERNROAD_BACKUP_OPTION    = 'modernroad_install_backup';
const MODERNROAD_MENU_NAME        = 'MR Menu';

/**
 * key => [
 *   title   MR_ page title (never shown on the site)
 *   slug    URL
 *   content shortcode(s) placed in a Shortcode block
 *   menu    label in "MR Menu" (omit to leave out of the menu)
 *   visible callback: whether the current user may see the page (menu item,
 *           links); omit for everyone
 *   assign  'front' or the WooCommerce page option it is assigned to
 *   heading / intro  page header printed above plugin shortcodes that have
 *           none of their own (see modernroad_wrap_page_content())
 * ]
 */
function modernroad_managed_pages() {
    return [
        'home' => [
            'title'   => 'MR_Home',
            'slug'    => 'dashboard',
            'content' => '[modernroad_home]',
            'menu'    => 'الرئيسية',
            'assign'  => 'front',
        ],
        'new-order' => [
            'title'   => 'MR_NewOrder',
            'slug'    => 'new-order',
            'content' => '[car_parts_order_form][car_order_form][tire_battery_order_form]',
            'menu'    => 'طلب جديد',
            'heading' => 'طلب جديد',
            'intro'   => 'أدخل بيانات المركبة والأصناف المطلوبة، ثم أرسل الطلب.',
        ],
        'orders' => [
            'title'   => 'MR_Orders',
            'slug'    => 'my-orders',
            'content' => '[IcyCode_orders_table]',
            'menu'    => 'الطلبات',
            'heading' => 'الطلبات',
            'intro'   => 'جميع الطلبات وحالتها. اضغط على رقم الطلب لعرض تفاصيله.',
            'card'    => true,
        ],
        'car-report' => [
            'title'   => 'MR_CarReport',
            'slug'    => 'car-report',
            'content' => '[IcyCode_car_report]',
            'menu'    => 'تقرير السيارات',
            'visible' => 'modernroad_user_has_car_report',
        ],
        'account' => [
            'title'   => 'MR_MyAccount',
            'slug'    => 'account',
            'content' => '[woocommerce_my_account]',
            'assign'  => 'woocommerce_myaccount_page_id',
        ],
        'checkout' => [
            'title'   => 'MR_Checkout',
            'slug'    => 'complete-order',
            'content' => '[woocommerce_checkout]',
            'assign'  => 'woocommerce_checkout_page_id',
        ],
    ];
}

/** Car report is enabled per user (user meta b2b_car_table, icycode-b2b plugin). */
function modernroad_user_has_car_report() {
    return class_exists('DisplayCarLimit') && DisplayCarLimit::user_has_report();
}

/** Whether the current user may see an MR_ page (its "visible" callback). */
function modernroad_page_visible($key) {
    $pages = modernroad_managed_pages();
    return empty($pages[$key]['visible']) || call_user_func($pages[$key]['visible']);
}

// Hide menu items of MR_ pages the current user may not see
add_filter('wp_nav_menu_objects', function ($items) {
    foreach (array_keys(modernroad_managed_pages()) as $key) {
        $id = modernroad_page_id($key);
        if ($id && !modernroad_page_visible($key)) {
            $items = array_filter($items, function ($item) use ($id) {
                return !($item->object === 'page' && (int) $item->object_id === $id);
            });
        }
    }
    return $items;
});

function modernroad_is_installed() {
    return (bool) get_option(MODERNROAD_INSTALLED_OPTION);
}

/** ID of an MR_ page created by the installer, or 0. */
function modernroad_page_id($key) {
    $ids = get_option(MODERNROAD_PAGES_OPTION, []);
    $id  = isset($ids[$key]) ? (int) $ids[$key] : 0;
    return $id && get_post_status($id) === 'publish' ? $id : 0;
}

function modernroad_page_url($key, $fallback = '') {
    $id = modernroad_page_id($key);
    return $id ? get_permalink($id) : ($fallback ?: home_url('/'));
}

/** Key of the MR_ page being viewed (also for Woo endpoints under it), or ''. */
function modernroad_current_page_key() {
    if (!is_page()) {
        return '';
    }
    $current = get_queried_object_id();
    foreach (array_keys(modernroad_managed_pages()) as $key) {
        if ($current && modernroad_page_id($key) === $current) {
            return $key;
        }
    }
    return '';
}
