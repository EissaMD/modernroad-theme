<?php
/**
 * My Account page (ModernRoad override): side menu + content. Used only when
 * the My Account page is an MR_ page (see modernroad_gated_templates()).
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined('ABSPATH') || exit;
?>

<div class="mr-page-header">
    <h1 class="mr-page-header__title">حسابي</h1>
</div>

<div class="mr-account">
    <?php do_action('woocommerce_account_navigation'); ?>

    <div class="woocommerce-MyAccount-content mr-account__content">
        <?php do_action('woocommerce_account_content'); ?>
    </div>
</div>
