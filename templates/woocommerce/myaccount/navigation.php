<?php
/**
 * My Account navigation (ModernRoad override): menu with icons. Used only when
 * the My Account page is an MR_ page (see modernroad_gated_templates()).
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if (!defined('ABSPATH')) {
    exit;
}

// Endpoints added by plugins (B2BKing etc.) get the default icon
$icons = [
    'dashboard'       => 'home',
    'orders'          => 'box',
    'subaccounts'     => 'users',
    'edit-address'    => 'pin',
    'edit-account'    => 'user',
    'customer-logout' => 'logout',
];

// Labels plugins leave untranslated
$labels = [
    'Subaccounts' => 'الحسابات الفرعية',
];

// Plugins append tabs after "Log out"; keep it last
$items = wc_get_account_menu_items();
if (isset($items['customer-logout'])) {
    $logout = $items['customer-logout'];
    unset($items['customer-logout']);
    $items['customer-logout'] = $logout;
}

do_action('woocommerce_before_account_navigation');
?>

<nav class="woocommerce-MyAccount-navigation mr-account__nav" aria-label="<?php esc_html_e('Account pages', 'woocommerce'); ?>">
    <ul>
        <?php foreach ($items as $endpoint => $label) :
            $label = $labels[$label] ?? $label;
            ?>
            <li class="<?php echo wc_get_account_menu_item_classes($endpoint); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
                <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>" <?php echo wc_is_current_account_menu_item($endpoint) ? 'aria-current="page"' : ''; ?>>
                    <?php echo modernroad_icon($icons[$endpoint] ?? 'file', 18); ?>
                    <span><?php echo esc_html($label); ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<?php do_action('woocommerce_after_account_navigation'); ?>
