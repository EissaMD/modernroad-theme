<?php
/**
 * Site header: logo, main menu ("menu-1" location) and the user menu.
 */

if (!defined('ABSPATH')) {
    exit;
}

$user        = wp_get_current_user();
$account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : wp_login_url();
$logo        = modernroad_logo_html();
?>
<header class="mr-header" id="mr-header">
    <div class="mr-header__inner">

        <a class="mr-header__brand" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
            <?php if ($logo) : ?>
                <?php echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            <?php else : ?>
                <span class="mr-header__site-name"><?php bloginfo('name'); ?></span>
            <?php endif; ?>
        </a>

        <?php if (is_user_logged_in()) : ?>
            <button type="button" class="mr-header__toggle" aria-controls="mr-header-nav" aria-expanded="false" aria-label="القائمة">
                <?php echo modernroad_icon('menu', 24); ?>
            </button>

            <nav class="mr-header__nav" id="mr-header-nav" aria-label="القائمة الرئيسية">
                <?php
                wp_nav_menu([
                    'theme_location' => 'menu-1',
                    'container'      => false,
                    'menu_class'     => 'mr-menu',
                    'depth'          => 1,
                    'fallback_cb'    => false,
                ]);
                ?>
            </nav>

            <details class="mr-user">
                <summary class="mr-user__chip">
                    <?php echo get_avatar($user->ID, 36, '', '', ['class' => 'mr-user__avatar']); ?>
                    <span class="mr-user__name"><?php echo esc_html($user->display_name); ?></span>
                    <?php echo modernroad_icon('chevron', 16); ?>
                </summary>
                <div class="mr-user__menu">
                    <?php $group = modernroad_user_group_name($user->ID); ?>
                    <?php if ($group) : ?>
                        <div class="mr-user__group"><?php echo esc_html($group); ?></div>
                    <?php endif; ?>
                    <a href="<?php echo esc_url($account_url); ?>"><?php echo modernroad_icon('user', 18); ?>حسابي</a>
                    <a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>"><?php echo modernroad_icon('logout', 18); ?>تسجيل الخروج</a>
                </div>
            </details>
        <?php else : ?>
            <a class="mr-btn mr-btn--primary mr-header__login" href="<?php echo esc_url($account_url); ?>">تسجيل الدخول</a>
        <?php endif; ?>

    </div>
</header>

<script>
(function () {
    var header = document.getElementById('mr-header');
    var toggle = header.querySelector('.mr-header__toggle');
    var user = header.querySelector('.mr-user');

    if (toggle) {
        toggle.addEventListener('click', function () {
            var open = header.classList.toggle('mr-header--nav-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    // Close the user menu when clicking elsewhere
    document.addEventListener('click', function (e) {
        if (user && user.open && !user.contains(e.target)) {
            user.open = false;
        }
    });
})();
</script>
