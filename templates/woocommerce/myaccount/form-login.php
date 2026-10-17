<?php
/**
 * Login Form (ModernRoad override): centred login card. Used only when the
 * My Account page is an MR_ page (see modernroad_gated_templates()).
 *
 * Registration is off on this site; if it is ever turned on, WooCommerce's
 * own template (login + register) is used instead.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

if ('yes' === get_option('woocommerce_enable_myaccount_registration')) {
    include WC()->plugin_path() . '/templates/myaccount/form-login.php';
    return;
}

$logo = modernroad_logo_html();

do_action('woocommerce_before_customer_login_form');
?>

<div class="mr-login">
    <div class="mr-card mr-login__card">
        <?php if ($logo) : ?>
            <div class="mr-login__logo"><?php echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
        <?php endif; ?>

        <h2 class="mr-login__title">تسجيل الدخول</h2>
        <p class="mr-login__text">أدخل بيانات حسابك للمتابعة.</p>

        <form class="woocommerce-form woocommerce-form-login login" method="post">

            <?php do_action('woocommerce_login_form_start'); ?>

            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="username"><?php esc_html_e('Username or email address', 'woocommerce'); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e('Required', 'woocommerce'); ?></span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
            </p>
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                <label for="password"><?php esc_html_e('Password', 'woocommerce'); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e('Required', 'woocommerce'); ?></span></label>
                <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
            </p>

            <?php do_action('woocommerce_login_form'); ?>

            <div class="mr-login__row">
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                    <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e('Remember me', 'woocommerce'); ?></span>
                </label>
                <a class="mr-login__lost" href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('Lost your password?', 'woocommerce'); ?></a>
            </div>

            <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
            <button type="submit" class="woocommerce-button button woocommerce-form-login__submit mr-btn mr-btn--primary mr-login__submit" name="login" value="<?php esc_attr_e('Log in', 'woocommerce'); ?>"><?php esc_html_e('Log in', 'woocommerce'); ?></button>

            <?php do_action('woocommerce_login_form_end'); ?>

        </form>
    </div>
</div>

<?php do_action('woocommerce_after_customer_login_form'); ?>
