<?php
/**
 * [modernroad_home] for MR_Home, and the page header added around plugin
 * shortcodes on the other MR_ pages.
 */

if (!defined('ABSPATH')) {
    exit;
}

add_shortcode('modernroad_home', 'modernroad_home_shortcode');

/**
 * MR_ pages hide their title, and the plugin shortcodes print no heading of
 * their own: add the page's heading/intro, and optionally a card around it.
 */
add_filter('the_content', 'modernroad_wrap_page_content', 20);

function modernroad_wrap_page_content($content) {
    if (!in_the_loop() || !is_main_query()) {
        return $content;
    }
    $key   = modernroad_current_page_key();
    $pages = modernroad_managed_pages();
    if (!$key || empty($pages[$key]['heading'])) {
        return $content;
    }
    $page = $pages[$key];

    $header = '<div class="mr-page-header"><h1 class="mr-page-header__title">' . esc_html($page['heading']) . '</h1>';
    if (!empty($page['intro'])) {
        $header .= '<p class="mr-page-header__text">' . esc_html($page['intro']) . '</p>';
    }
    $header .= '</div>';

    if (!empty($page['card'])) {
        $content = '<div class="mr-card mr-table-scroll">' . $content . '</div>';
    }
    return $header . $content;
}

function modernroad_home_shortcode() {
    if (!is_user_logged_in()) {
        return modernroad_home_guest();
    }

    $user  = wp_get_current_user();
    $group = modernroad_user_group_name($user->ID);

    $actions = [
        [
            'url'   => modernroad_page_url('new-order'),
            'icon'  => 'plus',
            'title' => 'طلب جديد',
            'text'  => 'إنشاء طلب لمركبة',
            'class' => 'mr-action--primary',
        ],
        [
            'url'   => modernroad_page_url('orders'),
            'icon'  => 'list',
            'title' => 'الطلبات',
            'text'  => 'متابعة الطلبات وحالتها',
        ],
    ];
    if (modernroad_page_id('car-report') && modernroad_page_visible('car-report')) {
        $actions[] = [
            'url'   => modernroad_page_url('car-report'),
            'icon'  => 'car',
            'title' => 'تقرير السيارات',
            'text'  => 'المركبات والحدود المتبقية',
        ];
    }
    $actions[] = [
        'url'   => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : '',
        'icon'  => 'user',
        'title' => 'حسابي',
        'text'  => 'البيانات والعناوين وكلمة المرور',
    ];

    // Same orders the orders table shows: this account plus its subaccounts (B2BKing)
    $customer_ids = class_exists('MyOrdersTable') ? MyOrdersTable::get_subaccounts_list($user->ID) : [$user->ID];

    $orders = function_exists('wc_get_orders') ? wc_get_orders([
        'customer_id' => $customer_ids,
        'status'      => 'any',
        'limit'       => 5,
        'orderby'     => 'date',
        'order'       => 'DESC',
    ]) : [];

    ob_start();
    ?>
    <div class="mr-home">
        <section class="mr-home-hero">
            <div>
                <h1 class="mr-home-hero__title">مرحبًا، <?php echo esc_html($user->display_name); ?></h1>
                <p class="mr-home-hero__text">
                    <?php echo $group ? esc_html($group) . ' · ' : ''; ?>ماذا تريد أن تفعل اليوم؟
                </p>
            </div>
            <a class="mr-btn mr-home-hero__cta" href="<?php echo esc_url(modernroad_page_url('new-order')); ?>">
                <?php echo modernroad_icon('plus', 18); ?>طلب جديد
            </a>
        </section>

        <section class="mr-home-actions">
            <?php foreach ($actions as $action) : ?>
                <a class="mr-action <?php echo esc_attr($action['class'] ?? ''); ?>" href="<?php echo esc_url($action['url']); ?>">
                    <span class="mr-action__icon"><?php echo modernroad_icon($action['icon'], 22); ?></span>
                    <span class="mr-action__title"><?php echo esc_html($action['title']); ?></span>
                    <span class="mr-action__text"><?php echo esc_html($action['text']); ?></span>
                </a>
            <?php endforeach; ?>
        </section>

        <section class="mr-card mr-home-orders">
            <div class="mr-home-orders__head">
                <h2 class="mr-card__title"><?php echo modernroad_icon('box'); ?>آخر الطلبات</h2>
                <a class="mr-home-orders__all" href="<?php echo esc_url(modernroad_page_url('orders')); ?>">عرض الكل</a>
            </div>

            <?php if ($orders) : ?>
                <div class="mr-table-scroll">
                    <table class="mr-table">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>العميل</th>
                                <th>التاريخ</th>
                                <th>المركبة</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order) :
                                $car = class_exists('OrderCarInfo') ? OrderCarInfo::get_all($order) : [];
                                $car_label = trim(($car['car_name'] ?? '') . ' ' . ($car['plate_no'] ?? ''));
                                ?>
                                <tr>
                                    <td><strong>#<?php echo esc_html($order->get_order_number()); ?></strong></td>
                                    <td><?php echo esc_html($order->get_formatted_billing_full_name() ?: '—'); ?></td>
                                    <td><?php echo esc_html(wc_format_datetime($order->get_date_created())); ?></td>
                                    <td><?php echo esc_html($car_label ?: '—'); ?></td>
                                    <td><?php echo modernroad_status_badge($order->get_status()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else : ?>
                <p class="mr-home-orders__empty">لا توجد طلبات بعد.
                    <a href="<?php echo esc_url(modernroad_page_url('new-order')); ?>">أنشئ أول طلب</a>
                </p>
            <?php endif; ?>
        </section>
    </div>
    <?php
    return ob_get_clean();
}

function modernroad_home_guest() {
    $logo = modernroad_logo_html();
    ob_start();
    ?>
    <div class="mr-home mr-home--guest">
        <section class="mr-card mr-home-guest">
            <?php if ($logo) : ?>
                <div class="mr-home-guest__logo"><?php echo $logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
            <?php endif; ?>
            <h1 class="mr-home-guest__title">مرحبًا بك في <?php bloginfo('name'); ?></h1>
            <p class="mr-home-guest__text">سجّل الدخول لإنشاء الطلبات ومتابعتها.</p>
            <a class="mr-btn mr-btn--primary" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : wp_login_url()); ?>">تسجيل الدخول</a>
        </section>
    </div>
    <?php
    return ob_get_clean();
}
