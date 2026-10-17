<?php
/**
 * My Account dashboard (ModernRoad override): greeting and shortcut cards.
 * Used only when the My Account page is an MR_ page (see
 * modernroad_gated_templates()).
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 *
 * @var WP_User $current_user
 */

if (!defined('ABSPATH')) {
    exit;
}

$group = modernroad_user_group_name($current_user->ID);

// Shortcuts to the account sections this user actually has
$menu  = wc_get_account_menu_items();
$cards = [
    'orders'          => ['icon' => 'box', 'text' => 'متابعة الطلبات وحالتها'],
    'edit-address'    => ['icon' => 'pin', 'text' => 'عناوين الشحن والفاتورة'],
    'edit-account'    => ['icon' => 'user', 'text' => 'الاسم والبريد وكلمة المرور'],
];
?>

<div class="mr-account-welcome">
    <?php echo get_avatar($current_user->ID, 56, '', '', ['class' => 'mr-account-welcome__avatar']); ?>
    <div>
        <h2 class="mr-account-welcome__title">مرحبًا، <?php echo esc_html($current_user->display_name); ?></h2>
        <?php if ($group) : ?>
            <p class="mr-account-welcome__text"><?php echo esc_html($group); ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="mr-account-cards">
    <?php foreach ($cards as $endpoint => $card) :
        if (!isset($menu[$endpoint])) {
            continue;
        } ?>
        <a class="mr-account-card" href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>">
            <span class="mr-account-card__icon"><?php echo modernroad_icon($card['icon'], 22); ?></span>
            <span class="mr-account-card__title"><?php echo esc_html($menu[$endpoint]); ?></span>
            <span class="mr-account-card__text"><?php echo esc_html($card['text']); ?></span>
        </a>
    <?php endforeach; ?>
</div>

<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action('woocommerce_account_dashboard');

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_before_my_account');

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_after_my_account');
