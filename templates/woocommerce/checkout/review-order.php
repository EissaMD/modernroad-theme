<?php
/**
 * Review order table (ModernRoad override). Used only when the checkout page
 * is an MR_ page (see modernroad_gated_templates()).
 *
 * The vehicle is shown once above the items instead of under every item, and
 * price columns/totals only appear for users allowed to see prices.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;

$sees_prices = modernroad_user_sees_prices();
$cart_items  = WC()->cart->get_cart();

// One car per order: take it from the first item that carries one. It is
// rendered as the table caption because the checkout AJAX refresh replaces
// the table element, so anything outside it would be duplicated.
$car = [];
if (class_exists('OrderCarInfo') && method_exists('OrderCarInfo', 'from_cart_item')) {
    foreach ($cart_items as $cart_item) {
        $car = OrderCarInfo::from_cart_item($cart_item);
        if ($car) {
            break;
        }
    }
}
?>

<table class="shop_table woocommerce-checkout-review-order-table mr-table">
    <?php if ($car) : ?>
        <caption class="mr-checkout-car">
            <div class="mr-checkout-car__title">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 17H3v-5l2-5h14l2 5v5h-2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/><path d="M9 17h6M3 12h18"/></svg>
                بيانات المركبة
            </div>
            <dl class="mr-info-grid mr-info-grid--compact">
                <?php foreach (OrderCarInfo::FIELDS as $field => $label) :
                    if (!isset($car[$field])) {
                        continue;
                    } ?>
                    <div class="mr-info-grid__item">
                        <dt><?php echo esc_html($label); ?></dt>
                        <dd><?php echo esc_html($car[$field]); ?></dd>
                    </div>
                <?php endforeach; ?>
            </dl>
        </caption>
    <?php endif; ?>
    <thead>
        <tr>
            <th class="product-name"><?php esc_html_e('Product', 'woocommerce'); ?></th>
            <th class="product-quantity mr-table__num"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
            <?php if ($sees_prices) : ?>
                <th class="product-total mr-table__num"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php
        do_action('woocommerce_review_order_before_cart_contents');

        foreach ($cart_items as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);

            if (!$_product || !$_product->exists() || $cart_item['quantity'] <= 0 || !apply_filters('woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key)) {
                continue;
            }

            // Uploaded part/product photo from the car parts and any-product forms
            $image = $cart_item['part_image'] ?? ($cart_item['product_image'] ?? '');
            ?>
            <tr class="<?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
                <td class="product-name">
                    <div class="mr-checkout-item">
                        <?php if ($image) : ?>
                            <a class="mr-checkout-item__thumb" href="<?php echo esc_url($image); ?>" target="_blank" rel="noopener">
                                <img src="<?php echo esc_url($image); ?>" alt="" loading="lazy">
                            </a>
                        <?php endif; ?>
                        <span class="mr-checkout-item__name"><?php echo esc_html($_product->get_name()); ?></span>
                    </div>
                </td>
                <td class="product-quantity mr-table__num"><?php echo esc_html($cart_item['quantity']); ?></td>
                <?php if ($sees_prices) : ?>
                    <td class="product-total mr-table__num">
                        <?php echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </td>
                <?php endif; ?>
            </tr>
            <?php
        }

        do_action('woocommerce_review_order_after_cart_contents');
        ?>
    </tbody>

    <?php if ($sees_prices) : ?>
        <tfoot>
            <tr class="cart-subtotal">
                <th colspan="2"><?php esc_html_e('Subtotal', 'woocommerce'); ?></th>
                <td class="mr-table__num"><?php wc_cart_totals_subtotal_html(); ?></td>
            </tr>

            <?php foreach (WC()->cart->get_coupons() as $code => $coupon) : ?>
                <tr class="cart-discount coupon-<?php echo esc_attr(sanitize_title($code)); ?>">
                    <th colspan="2"><?php wc_cart_totals_coupon_label($coupon); ?></th>
                    <td class="mr-table__num"><?php wc_cart_totals_coupon_html($coupon); ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                <?php do_action('woocommerce_review_order_before_shipping'); ?>
                <?php wc_cart_totals_shipping_html(); ?>
                <?php do_action('woocommerce_review_order_after_shipping'); ?>
            <?php endif; ?>

            <?php foreach (WC()->cart->get_fees() as $fee) : ?>
                <tr class="fee">
                    <th colspan="2"><?php echo esc_html($fee->name); ?></th>
                    <td class="mr-table__num"><?php wc_cart_totals_fee_html($fee); ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if (wc_tax_enabled() && !WC()->cart->display_prices_including_tax()) : ?>
                <tr class="tax-total">
                    <th colspan="2"><?php echo esc_html(WC()->countries->tax_or_vat()); ?></th>
                    <td class="mr-table__num"><?php wc_cart_totals_taxes_total_html(); ?></td>
                </tr>
            <?php endif; ?>

            <?php do_action('woocommerce_review_order_before_order_total'); ?>

            <tr class="order-total">
                <th colspan="2"><?php esc_html_e('Total', 'woocommerce'); ?></th>
                <td class="mr-table__num"><?php wc_cart_totals_order_total_html(); ?></td>
            </tr>

            <?php do_action('woocommerce_review_order_after_order_total'); ?>
        </tfoot>
    <?php endif; ?>
</table>
