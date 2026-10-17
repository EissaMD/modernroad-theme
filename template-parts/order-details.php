<?php
/**
 * Order sections shared by the thank-you page and My Account → view order:
 * vehicle, items (+ approval box), addresses. Styles in assets/css/order.css.
 *
 * @var array $args ['order' => WC_Order]
 */

if (!defined('ABSPATH')) {
    exit;
}

/** @var WC_Order $order */
$order       = $args['order'];
$sees_prices = modernroad_user_sees_prices();

$car = class_exists('OrderCarInfo') ? array_filter(OrderCarInfo::get_all($order), 'strlen') : [];
if ($car) :
    ?>
    <section class="mr-card">
        <h2 class="mr-card__title"><?php echo modernroad_icon('car'); ?>بيانات المركبة</h2>
        <dl class="mr-info-grid">
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
    </section>
<?php endif; ?>

<section class="mr-card">
    <h2 class="mr-card__title"><?php echo modernroad_icon('box'); ?><?php esc_html_e('Order details', 'woocommerce'); ?></h2>

    <?php do_action('woocommerce_order_details_before_order_table', $order); ?>

    <table class="mr-table mr-order-items">
        <thead>
            <tr>
                <th><?php esc_html_e('Product', 'woocommerce'); ?></th>
                <th class="mr-table__num"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
                <?php if ($sees_prices) : ?>
                    <th class="mr-table__num"><?php esc_html_e('Total', 'woocommerce'); ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // Image meta is rendered as thumbnails; legacy car fields are in the car card
            $image_keys  = ['صورة القطعة', 'صورة المنتج'];
            $hidden_keys = array_merge($image_keys, class_exists('OrderCarInfo') ? array_values(OrderCarInfo::FIELDS) : []);
            $hide_meta   = function ($meta) use ($hidden_keys) {
                return array_filter($meta, function ($m) use ($hidden_keys) {
                    return !in_array($m->key, $hidden_keys, true);
                });
            };
            add_filter('woocommerce_order_item_get_formatted_meta_data', $hide_meta);

            foreach ($order->get_items() as $item) :
                $images = array_filter(array_map(function ($key) use ($item) {
                    return $item->get_meta($key, true);
                }, $image_keys));
                ?>
                <tr>
                    <td>
                        <div class="mr-order-items__product">
                            <?php foreach ($images as $url) : ?>
                                <a class="mr-order-items__thumb" href="<?php echo esc_url($url); ?>" target="_blank" rel="noopener">
                                    <img src="<?php echo esc_url($url); ?>" alt="" loading="lazy">
                                </a>
                            <?php endforeach; ?>
                            <div>
                                <span class="mr-order-items__name"><?php echo esc_html($item->get_name()); ?></span>
                                <?php wc_display_item_meta($item); ?>
                            </div>
                        </div>
                    </td>
                    <td class="mr-table__num"><?php echo esc_html($item->get_quantity()); ?></td>
                    <?php if ($sees_prices) : ?>
                        <td class="mr-table__num"><?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach;
            remove_filter('woocommerce_order_item_get_formatted_meta_data', $hide_meta);
            ?>
        </tbody>
        <?php if ($sees_prices) :
            $totals = $order->get_order_item_totals();
            unset($totals['payment_method']); // B2B orders all go through company approval
            ?>
            <tfoot>
                <?php foreach ($totals as $key => $total) : ?>
                    <tr class="mr-order-items__total--<?php echo esc_attr($key); ?>">
                        <th colspan="2"><?php echo esc_html(rtrim($total['label'], ':')); ?></th>
                        <td class="mr-table__num"><?php echo wp_kses_post($total['value']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tfoot>
        <?php endif; ?>
    </table>

    <?php if ($order->get_customer_note()) : ?>
        <div class="mr-order-note">
            <strong><?php esc_html_e('Note:', 'woocommerce'); ?></strong>
            <?php echo wp_kses(nl2br(wptexturize($order->get_customer_note())), []); ?>
        </div>
    <?php endif; ?>

    <?php
    // B2BKing prints its approve/reject box here
    ob_start();
    do_action('woocommerce_order_details_after_order_table', $order);
    $after_table = trim(ob_get_clean());
    if ($after_table !== '') :
        ?>
        <div class="mr-order-approval"><?php echo $after_table; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin output ?></div>
    <?php endif; ?>
</section>

<?php
$shipping = $order->get_formatted_shipping_address();
$billing  = $order->get_formatted_billing_address();
if ($shipping || $billing) :
    ?>
    <div class="mr-order-addresses">
        <?php if ($shipping) : ?>
            <section class="mr-card">
                <h2 class="mr-card__title"><?php echo modernroad_icon('pin'); ?><?php esc_html_e('Shipping address', 'woocommerce'); ?></h2>
                <address><?php echo wp_kses_post($shipping); ?>
                    <?php if ($order->get_shipping_phone()) : ?>
                        <br><?php echo esc_html($order->get_shipping_phone()); ?>
                    <?php endif; ?>
                </address>
            </section>
        <?php endif; ?>
        <?php if ($billing) : ?>
            <section class="mr-card">
                <h2 class="mr-card__title"><?php echo modernroad_icon('file'); ?><?php esc_html_e('Billing address', 'woocommerce'); ?></h2>
                <address><?php echo wp_kses_post($billing); ?>
                    <?php if ($order->get_billing_phone()) : ?>
                        <br><?php echo esc_html($order->get_billing_phone()); ?>
                    <?php endif; ?>
                    <?php if ($order->get_billing_email()) : ?>
                        <br><?php echo esc_html($order->get_billing_email()); ?>
                    <?php endif; ?>
                </address>
            </section>
        <?php endif; ?>
    </div>
<?php endif; ?>
