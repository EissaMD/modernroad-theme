<?php
/**
 * Email Addresses (ModernRoad override): billing and shipping side by side in
 * bordered boxes. Used once the new design is installed.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 8.6.0
 *
 * @var WC_Order $order
 * @var bool     $sent_to_admin
 */

if (!defined('ABSPATH')) {
    exit;
}

$text_align = is_rtl() ? 'right' : 'left';
$address    = $order->get_formatted_billing_address();
$shipping   = $order->get_formatted_shipping_address();
$has_ship   = !wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $shipping;
?>
<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width:100%;vertical-align:top;margin-bottom:28px;padding:0;">
    <tr>
        <td style="text-align:<?php echo esc_attr($text_align); ?>;border:0;padding:0;" valign="top" width="<?php echo $has_ship ? '50%' : '100%'; ?>">
            <h2><?php esc_html_e('Billing address', 'woocommerce'); ?></h2>
            <address class="mr-email-address">
                <?php echo wp_kses_post($address ?: esc_html__('N/A', 'woocommerce')); ?>
                <?php if ($order->get_billing_phone()) : ?>
                    <br/><?php echo wc_make_phone_clickable($order->get_billing_phone()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php endif; ?>
                <?php if ($order->get_billing_email()) : ?>
                    <br/><?php echo esc_html($order->get_billing_email()); ?>
                <?php endif; ?>
                <?php do_action('woocommerce_email_customer_address_section', 'billing', $order, $sent_to_admin, false); ?>
            </address>
        </td>
        <?php if ($has_ship) : ?>
            <td width="16" style="padding:0;border:0;"></td>
            <td style="text-align:<?php echo esc_attr($text_align); ?>;border:0;padding:0;" valign="top" width="50%">
                <h2><?php esc_html_e('Shipping address', 'woocommerce'); ?></h2>
                <address class="mr-email-address">
                    <?php echo wp_kses_post($shipping); ?>
                    <?php if ($order->get_shipping_phone()) : ?>
                        <br/><?php echo wc_make_phone_clickable($order->get_shipping_phone()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php endif; ?>
                    <?php do_action('woocommerce_email_customer_address_section', 'shipping', $order, $sent_to_admin, false); ?>
                </address>
            </td>
        <?php endif; ?>
    </tr>
</table>
