<?php
/**
 * Appearance → ModernRoad: one-click Install / Restore of the new pages.
 *
 * Install creates the missing MR_ pages, assigns them (front page, WooCommerce
 * pages, "MR Menu" in the header location) and turns on the theme header and
 * footer. The settings it replaces are saved first; Restore puts them back.
 * Old pages are never modified.
 */

if (!defined('ABSPATH')) {
    exit;
}

const MODERNROAD_ADMIN_CAP = 'manage_options';

add_action('admin_menu', function () {
    add_theme_page('ModernRoad', 'ModernRoad', MODERNROAD_ADMIN_CAP, 'modernroad', 'modernroad_render_installer_page');
});

add_action('admin_post_modernroad_install', function () {
    modernroad_check_installer_request('modernroad_install');
    modernroad_install();
    modernroad_redirect_to_installer('installed');
});

add_action('admin_post_modernroad_restore', function () {
    modernroad_check_installer_request('modernroad_restore');
    modernroad_restore();
    modernroad_redirect_to_installer('restored');
});

function modernroad_check_installer_request($action) {
    if (!current_user_can(MODERNROAD_ADMIN_CAP)) {
        wp_die('غير مصرح');
    }
    check_admin_referer($action);
}

function modernroad_redirect_to_installer($done) {
    wp_safe_redirect(add_query_arg(['page' => 'modernroad', 'done' => $done], admin_url('themes.php')));
    exit;
}

/** Settings Install changes, as option name => current value. */
function modernroad_assigned_options() {
    return [
        'show_on_front'                 => get_option('show_on_front'),
        'page_on_front'                 => get_option('page_on_front'),
        'woocommerce_checkout_page_id'  => get_option('woocommerce_checkout_page_id'),
        'woocommerce_myaccount_page_id' => get_option('woocommerce_myaccount_page_id'),
    ];
}

function modernroad_install() {
    // Keep the original settings from the first install, not from a re-install
    if (!modernroad_is_installed() || !get_option(MODERNROAD_BACKUP_OPTION)) {
        update_option(MODERNROAD_BACKUP_OPTION, [
            'options'        => modernroad_assigned_options(),
            'menu_locations' => get_nav_menu_locations(),
        ], false);
    }

    $ids = [];
    foreach (modernroad_managed_pages() as $key => $page) {
        $ids[$key] = modernroad_page_id($key) ?: modernroad_create_page($page);
    }
    update_option(MODERNROAD_PAGES_OPTION, $ids);

    foreach (modernroad_managed_pages() as $key => $page) {
        if (empty($page['assign'])) {
            continue;
        }
        if ($page['assign'] === 'front') {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $ids[$key]);
        } else {
            update_option($page['assign'], $ids[$key]);
        }
    }

    $locations           = get_nav_menu_locations();
    $locations['menu-1'] = modernroad_ensure_menu($ids);
    set_theme_mod('nav_menu_locations', $locations);

    update_option(MODERNROAD_INSTALLED_OPTION, 1);
    flush_rewrite_rules(); // account endpoints under the new page URL
}

function modernroad_restore() {
    $backup = get_option(MODERNROAD_BACKUP_OPTION);
    if (is_array($backup)) {
        foreach ($backup['options'] as $name => $value) {
            update_option($name, $value);
        }
        set_theme_mod('nav_menu_locations', $backup['menu_locations']);
    }
    delete_option(MODERNROAD_BACKUP_OPTION);
    update_option(MODERNROAD_INSTALLED_OPTION, 0);
    flush_rewrite_rules();
}

/** Creates an MR_ page, or reuses one left at the same slug by an earlier install. */
function modernroad_create_page($page) {
    $existing = get_page_by_path($page['slug']);
    if ($existing && modernroad_is_mr_page($existing)) {
        if ($existing->post_status !== 'publish') {
            wp_update_post(['ID' => $existing->ID, 'post_status' => 'publish']);
        }
        return $existing->ID;
    }

    $id = wp_insert_post([
        'post_type'    => 'page',
        'post_status'  => 'publish',
        'post_title'   => $page['title'],
        'post_name'    => $page['slug'],
        'post_content' => "<!-- wp:shortcode -->\n{$page['content']}\n<!-- /wp:shortcode -->",
    ], true);

    if (is_wp_error($id)) {
        wp_die(esc_html($page['title'] . ': ' . $id->get_error_message()));
    }
    return $id;
}

/** Returns the "MR Menu" ID, creating it if needed and adding any MR_ page missing from it. */
function modernroad_ensure_menu(array $ids) {
    $menu = wp_get_nav_menu_object(MODERNROAD_MENU_NAME);
    if ($menu) {
        // Already exists: keep manual edits, only add MR_ pages it doesn't link yet
        $menu_id = $menu->term_id;
        $linked  = array_map('intval', wp_list_pluck(wp_get_nav_menu_items($menu_id) ?: [], 'object_id'));
    } else {
        $menu_id = wp_create_nav_menu(MODERNROAD_MENU_NAME);
        if (is_wp_error($menu_id)) {
            wp_die(esc_html($menu_id->get_error_message()));
        }
        $linked = [];
    }

    foreach (modernroad_managed_pages() as $key => $page) {
        if (empty($page['menu']) || in_array((int) $ids[$key], $linked, true)) {
            continue;
        }
        wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title'     => $page['menu'],
            'menu-item-object'    => 'page',
            'menu-item-object-id' => $ids[$key],
            'menu-item-type'      => 'post_type',
            'menu-item-status'    => 'publish',
        ]);
    }
    return $menu_id;
}

function modernroad_render_installer_page() {
    $installed = modernroad_is_installed();
    $done      = sanitize_key($_GET['done'] ?? '');
    $options   = modernroad_assigned_options();
    ?>
    <div class="wrap" dir="rtl" style="max-width:900px">
        <h1>ModernRoad</h1>

        <?php if ($done === 'installed') : ?>
            <div class="notice notice-success"><p>تم التثبيت. الصفحات الجديدة مفعّلة الآن.</p></div>
        <?php elseif ($done === 'restored') : ?>
            <div class="notice notice-success"><p>تمت الاستعادة. الموقع يعمل بالصفحات والإعدادات السابقة.</p></div>
        <?php endif; ?>

        <p>
            الحالة:
            <strong><?php echo $installed ? 'مثبّت — التصميم الجديد مفعّل' : 'غير مثبّت — الموقع يعمل بالصفحات القديمة'; ?></strong>
        </p>

        <table class="widefat striped" style="margin:16px 0">
            <thead>
                <tr><th>الصفحة</th><th>الحالة</th><th>معيّنة كـ</th></tr>
            </thead>
            <tbody>
                <?php foreach (modernroad_managed_pages() as $key => $page) :
                    $id = modernroad_page_id($key);
                    $assigned = '';
                    if (!empty($page['assign'])) {
                        $option   = $page['assign'] === 'front' ? 'page_on_front' : $page['assign'];
                        $is_set   = $id && (int) $options[$option] === $id && ($page['assign'] !== 'front' || $options['show_on_front'] === 'page');
                        $assigned = ($page['assign'] === 'front' ? 'الصفحة الرئيسية' : $option) . ($is_set ? ' ✓' : ' ✗');
                    } elseif (!empty($page['menu'])) {
                        $assigned = 'القائمة: ' . $page['menu'];
                    }
                    ?>
                    <tr>
                        <td><code><?php echo esc_html($page['title']); ?></code></td>
                        <td>
                            <?php if ($id) : ?>
                                موجودة — <a href="<?php echo esc_url(get_permalink($id)); ?>" target="_blank">عرض</a>
                            <?php else : ?>
                                غير موجودة (ستُنشأ عند التثبيت)
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html($assigned); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;margin-left:8px">
            <input type="hidden" name="action" value="modernroad_install">
            <?php wp_nonce_field('modernroad_install'); ?>
            <button type="submit" class="button button-primary button-hero"><?php echo $installed ? 'إعادة التثبيت' : 'تثبيت التصميم الجديد'; ?></button>
        </form>

        <?php if ($installed) : ?>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block"
                  onsubmit="return confirm('استعادة الصفحات والإعدادات السابقة؟ الصفحات الجديدة لن تُحذف.');">
                <input type="hidden" name="action" value="modernroad_restore">
                <?php wp_nonce_field('modernroad_restore'); ?>
                <button type="submit" class="button button-hero">استعادة السابق</button>
            </form>
        <?php endif; ?>

        <p class="description" style="margin-top:16px">
            التثبيت ينشئ صفحات MR_ الناقصة، ويعيّن الصفحة الرئيسية وصفحتي الدفع والحساب في WooCommerce، وينشئ "MR Menu" للهيدر،
            ويفعّل الهيدر والفوتر الجديدين. الإعدادات السابقة تُحفظ ويمكن استعادتها، والصفحات القديمة لا تُعدَّل.
        </p>
    </div>
    <?php
}
