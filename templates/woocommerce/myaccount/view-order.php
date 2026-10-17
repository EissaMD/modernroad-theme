<?php
/**
 * View Order (ModernRoad override): summary card, order updates, then the
 * sections shared with the thank-you page. Used only when the My Account page
 * is an MR_ page (see modernroad_gated_templates()).
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 *
 * @var WC_Order $order
 * @var int      $order_id
 */

defined('ABSPATH') || exit;

$notes       = $order->get_customer_order_notes();
$sees_prices = modernroad_user_sees_prices();
?>

<div class="mr-order">

    <a class="mr-order-back" href="<?php echo esc_url(modernroad_page_url('orders', wc_get_account_endpoint_url('orders'))); ?>">→ كل الطلبات</a>

    <section class="mr-card mr-order-summary">
        <div class="mr-order-summary__head">
            <h2 class="mr-order-summary__title">طلب #<?php echo esc_html($order->get_order_number()); ?></h2>
            <?php echo modernroad_status_badge($order->get_status()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
        <dl class="mr-info-grid">
            <div class="mr-info-grid__item">
                <dt><?php esc_html_e('Date:', 'woocommerce'); ?></dt>
                <dd><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></dd>
            </div>
            <div class="mr-info-grid__item">
                <dt>عدد الأصناف</dt>
                <dd><?php echo esc_html($order->get_item_count()); ?></dd>
            </div>
            <?php if ($sees_prices) : ?>
                <div class="mr-info-grid__item">
                    <dt><?php esc_html_e('Total:', 'woocommerce'); ?></dt>
                    <dd><?php echo wp_kses_post($order->get_formatted_order_total()); ?></dd>
                </div>
            <?php endif; ?>
        </dl>
    </section>

    <?php if ($notes) : ?>
        <section class="mr-card">
            <h2 class="mr-card__title"><?php echo modernroad_icon('list'); ?><?php esc_html_e('Order updates', 'woocommerce'); ?></h2>
            <ol class="mr-order-updates">
                <?php foreach ($notes as $note) : ?>
                    <li>
                        <p class="mr-order-updates__date"><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), strtotime($note->comment_date))); ?></p>
                        <div class="mr-order-updates__text"><?php echo wpautop(wptexturize($note->comment_content)); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                    </li>
                <?php endforeach; ?>
            </ol>
        </section>
    <?php endif; ?>

    <?php get_template_part('template-parts/order-details', null, ['order' => $order]); ?>

    <?php
    // Details are rendered above; keep other woocommerce_view_order callbacks running
    remove_action('woocommerce_view_order', 'woocommerce_order_details_table', 10);
    do_action('woocommerce_view_order', $order_id);
    ?>

</div>
