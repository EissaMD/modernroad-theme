<?php
/**
 * Until the new design is installed, use the parent theme's footer (which
 * renders the Elementor footer).
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!modernroad_is_installed() && !modernroad_is_embed()) {
    require get_template_directory() . '/footer.php';
    return;
}

if (!modernroad_is_embed()) {
    get_template_part('template-parts/mr-footer');
}
wp_footer();
?>
</body>
</html>
