<?php
/**
 * Checkout Form (ModernRoad override): customer details on one side, order
 * summary and submit on the other. Used only when the checkout page is an
 * MR_ page (see modernroad_gated_templates()).
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.4.0
 */

if (!defined('ABSPATH')) {
    exit;
}

do_action('woocommerce_before_checkout_form', $checkout);

// If checkout registration is disabled and not logged in, the user cannot checkout.
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));
    return;
}
?>

<div class="mr-checkout-header">
    <h1 class="mr-checkout-header__title">إتمام الطلب</h1>
    <p class="mr-checkout-header__text">راجع بياناتك ومحتوى الطلب، ثم أرسل الطلب.</p>
</div>

<form name="checkout" method="post" class="checkout woocommerce-checkout mr-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__('Checkout', 'woocommerce'); ?>">

    <div class="mr-checkout__main">
        <?php if ($checkout->get_checkout_fields()) : ?>

            <?php do_action('woocommerce_checkout_before_customer_details'); ?>

            <div id="customer_details">
                <div class="mr-card mr-checkout__section">
                    <?php do_action('woocommerce_checkout_billing'); ?>
                </div>

                <div class="mr-card mr-checkout__section">
                    <?php do_action('woocommerce_checkout_shipping'); ?>
                </div>
            </div>

            <?php do_action('woocommerce_checkout_after_customer_details'); ?>

        <?php endif; ?>
    </div>

    <aside class="mr-checkout__side">
        <div class="mr-card mr-checkout__summary">
            <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>

            <h3 id="order_review_heading" class="mr-card__title">ملخص الطلب</h3>

            <?php do_action('woocommerce_checkout_before_order_review'); ?>

            <div id="order_review" class="woocommerce-checkout-review-order">
                <?php do_action('woocommerce_checkout_order_review'); ?>
            </div>

            <?php do_action('woocommerce_checkout_after_order_review'); ?>
        </div>
    </aside>

</form>

<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
