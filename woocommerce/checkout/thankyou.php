<?php
/**
 * Thankyou page (ModernRoad override).
 *
 * Renders the order summary, vehicle info, items and addresses itself, so the
 * default order details table is removed from woocommerce_thankyou below.
 * Other woocommerce_thankyou callbacks (car limits, B2BKing) still run.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * @var WC_Order $order
 */

defined('ABSPATH') || exit;
?>

<div class="woocommerce-order mr-ty">

    <?php if ($order) :

        do_action('woocommerce_before_thankyou', $order->get_id());

        $sees_prices = modernroad_user_sees_prices();
        ?>

        <?php if ($order->has_status('failed')) : ?>

            <div class="mr-ty-hero mr-ty-hero--failed">
                <span class="mr-ty-hero__icon"><?php echo modernroad_icon('alert', 22); ?></span>
                <div>
                    <h1 class="mr-ty-hero__title">تعذّر إتمام الطلب</h1>
                    <p class="mr-ty-hero__text"><?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?></p>
                    <div class="mr-ty-actions">
                        <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="mr-btn mr-btn--primary"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
                        <?php if (is_user_logged_in()) : ?>
                            <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>" class="mr-btn"><?php esc_html_e('My account', 'woocommerce'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <?php else : ?>

            <div class="mr-ty-hero">
                <span class="mr-ty-hero__icon"><?php echo modernroad_icon('check', 22); ?></span>
                <div>
                    <h1 class="mr-ty-hero__title">شكرًا لك، تم استلام طلبك</h1>
                    <p class="mr-ty-hero__text">
                        رقم الطلب <strong>#<?php echo esc_html($order->get_order_number()); ?></strong>
                        &nbsp;·&nbsp;
                        <span class="mr-badge"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></span>
                    </p>
                </div>
            </div>

            <dl class="mr-info-grid mr-ty-overview">
                <div class="mr-info-grid__item">
                    <dt><?php esc_html_e('Order number:', 'woocommerce'); ?></dt>
                    <dd>#<?php echo esc_html($order->get_order_number()); ?></dd>
                </div>
                <div class="mr-info-grid__item">
                    <dt><?php esc_html_e('Date:', 'woocommerce'); ?></dt>
                    <dd><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></dd>
                </div>
                <?php if ($sees_prices) : ?>
                    <div class="mr-info-grid__item">
                        <dt><?php esc_html_e('Total:', 'woocommerce'); ?></dt>
                        <dd><?php echo wp_kses_post($order->get_formatted_order_total()); ?></dd>
                    </div>
                <?php endif; ?>
            </dl>

        <?php endif; ?>

        <?php
        // Gateway instructions (e.g. "sent for review and approval")
        ob_start();
        do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id());
        $instructions = trim(ob_get_clean());
        if ($instructions !== '') :
            ?>
            <div class="mr-ty-note" dir="auto"><?php echo $instructions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- gateway output ?></div>
        <?php endif; ?>

        <?php get_template_part('template-parts/order-details', null, ['order' => $order]); ?>

        <?php
        // Details are rendered above; keep the other thankyou callbacks running
        remove_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);
        do_action('woocommerce_thankyou', $order->get_id());
        ?>

    <?php else : ?>

        <?php wc_get_template('checkout/order-received.php', ['order' => false]); ?>

    <?php endif; ?>

</div>
