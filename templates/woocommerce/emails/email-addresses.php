<?php
/**
 * Email Addresses (ModernRoad override): the order's location (saved as the
 * shipping address) in one full-width box. No billing address. Used once the new design is installed.
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
$location   = modernroad_order_location($order);
if (!$location) {
    return;
}
?>
<table id="addresses" cellspacing="0" cellpadding="0" border="0" style="width:100%;vertical-align:top;margin-bottom:28px;padding:0;">
    <tr>
        <td style="text-align:<?php echo esc_attr($text_align); ?>;border:0;padding:0;" valign="top" width="100%">
            <h2>الموقع</h2>
            <address class="mr-email-address">
                <?php echo \Hamah\B2B\Frontend\DeliveryChoice::location_html($order); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside ?>
                <?php do_action('woocommerce_email_customer_address_section', 'shipping', $order, $sent_to_admin, false); ?>
            </address>
        </td>
    </tr>
</table>
