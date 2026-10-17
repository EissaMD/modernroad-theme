<?php
/**
 * Order details table shown in emails (ModernRoad override): order summary,
 * vehicle, items. Emails never show prices or totals, for any recipient.
 * Used once the new design is installed.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 *
 * @var WC_Order $order
 * @var bool     $sent_to_admin
 * @var bool     $plain_text
 * @var WC_Email $email
 */

defined('ABSPATH') || exit;

$text_align  = is_rtl() ? 'right' : 'left';
$order_url   = $sent_to_admin ? $order->get_edit_order_url() : $order->get_view_order_url();
$car         = class_exists('OrderCarInfo') ? array_filter(OrderCarInfo::get_all($order), 'strlen') : [];

do_action('woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email);
?>

<table class="mr-email-meta" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td style="text-align:<?php echo esc_attr($text_align); ?>;">
            <span class="mr-email-label">رقم الطلب</span>
            <span class="mr-email-value">#<?php echo esc_html($order->get_order_number()); ?></span>
        </td>
        <td width="8" style="background:none;padding:0;"></td>
        <td style="text-align:<?php echo esc_attr($text_align); ?>;">
            <span class="mr-email-label">التاريخ</span>
            <span class="mr-email-value"><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></span>
        </td>
        <td width="8" style="background:none;padding:0;"></td>
        <td style="text-align:<?php echo esc_attr($text_align); ?>;">
            <span class="mr-email-label">الحالة</span>
            <span class="mr-email-value"><?php echo esc_html(wc_get_order_status_name($order->get_status())); ?></span>
        </td>
    </tr>
</table>

<?php if ($car) : ?>
    <div class="mr-email-section">
        <h2>بيانات المركبة</h2>
        <table class="mr-email-car" width="100%" cellspacing="0" cellpadding="0" border="0">
            <?php foreach (OrderCarInfo::FIELDS as $field => $label) :
                if (!isset($car[$field])) {
                    continue;
                } ?>
                <tr>
                    <td style="text-align:<?php echo esc_attr($text_align); ?>;color:#6b7280;width:40%;"><?php echo esc_html($label); ?></td>
                    <td style="text-align:<?php echo esc_attr($text_align); ?>;font-weight:700;"><?php echo esc_html($car[$field]); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

<div class="mr-email-section">
    <h2>الأصناف</h2>
    <table class="mr-email-table" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th scope="col" style="text-align:<?php echo esc_attr($text_align); ?>;"><?php esc_html_e('Product', 'woocommerce'); ?></th>
                <th scope="col" class="mr-email-num"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Legacy per-item car fields are shown in the vehicle section; photos stay on the site
            $hidden_keys = array_merge(['صورة القطعة', 'صورة المنتج'], class_exists('OrderCarInfo') ? array_values(OrderCarInfo::FIELDS) : []);
            $hide_meta   = function ($meta) use ($hidden_keys) {
                return array_filter($meta, function ($m) use ($hidden_keys) {
                    return !in_array($m->key, $hidden_keys, true);
                });
            };
            add_filter('woocommerce_order_item_get_formatted_meta_data', $hide_meta);

            foreach ($order->get_items() as $item_id => $item) :
                if (!apply_filters('woocommerce_order_item_visible', true, $item)) {
                    continue;
                }
                $product = $item->get_product();
                $sku     = $sent_to_admin && $product ? $product->get_sku() : '';
                ?>
                <tr>
                    <td style="text-align:<?php echo esc_attr($text_align); ?>;">
                        <strong><?php echo wp_kses_post(apply_filters('woocommerce_order_item_name', $item->get_name(), $item, false)); ?></strong>
                        <?php if ($sku) : ?>
                            <span style="color:#6b7280;font-size:12px;">(#<?php echo esc_html($sku); ?>)</span>
                        <?php endif; ?>
                        <?php
                        do_action('woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text);
                        wc_display_item_meta($item, ['label_before' => '<strong class="wc-item-meta-label">', 'label_after' => ':</strong> ']);
                        do_action('woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text);
                        ?>
                    </td>
                    <td class="mr-email-num"><?php echo esc_html($item->get_quantity()); ?></td>
                </tr>
            <?php endforeach;
            remove_filter('woocommerce_order_item_get_formatted_meta_data', $hide_meta);
            ?>
        </tbody>
    </table>

    <?php if ($order->get_customer_note()) : ?>
        <p style="margin:12px 0 0;padding:12px 14px;background:#f7f8fa;border-radius:8px;">
            <strong><?php esc_html_e('Note:', 'woocommerce'); ?></strong>
            <?php echo wp_kses(nl2br(wptexturize($order->get_customer_note())), []); ?>
        </p>
    <?php endif; ?>
</div>

<?php if ($order_url) : ?>
    <p style="text-align:center;margin:0 0 28px;">
        <a class="mr-email-button" href="<?php echo esc_url($order_url); ?>">عرض الطلب</a>
    </p>
<?php endif; ?>

<?php do_action('woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email); ?>
