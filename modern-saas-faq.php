<?php
/**
 * Plugin Name:       Modern SaaS FAQ Pro
 * Description:       افزونه FAQ مدرن با Elementor Widget · Gutenberg Block · Lazy Load — مدیریت حرفه‌ای از طریق CPT
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            miladjef
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       modern-saas-faq
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ─── ثابت‌ها ─── */
define( 'MSAAS_FAQ_VERSION', '4.0.0' );
define( 'MSAAS_FAQ_PATH',    plugin_dir_path( __FILE__ ) );
define( 'MSAAS_FAQ_URL',     plugin_dir_url( __FILE__ ) );

/* ─── بارگذاری کلاس‌ها ─── */
require_once MSAAS_FAQ_PATH . 'includes/class-faq-cpt.php';
require_once MSAAS_FAQ_PATH . 'includes/class-faq-admin.php';
require_once MSAAS_FAQ_PATH . 'includes/class-faq-shortcodes.php';
require_once MSAAS_FAQ_PATH . 'includes/class-faq-elementor.php';
require_once MSAAS_FAQ_PATH . 'includes/class-faq-gutenberg.php';

/* ─── مقداردهی اولیه ─── */
function msaas_faq_init() {

    // ثبت CPT
    $cpt = new MSAAS_FAQ_CPT();
    $cpt->register();

    // ادمین (متاباکس‌ها + تنظیمات کلی)
    $admin = new MSAAS_FAQ_Admin();
    $admin->register();

    // شورت‌کدها
    $shortcodes = new MSAAS_FAQ_Shortcodes();
    $shortcodes->register();

    // Elementor Widget
    MSAAS_FAQ_Elementor::init();

    // Gutenberg Block
    MSAAS_FAQ_Gutenberg::init();
}
add_action( 'init', 'msaas_faq_init' );

/* ─── ثبت Assets (فقط register — enqueue در شورت‌کد) ─── */
function msaas_faq_register_assets() {
    wp_register_style(
        'msaas-faq-front',
        MSAAS_FAQ_URL . 'assets/css/faq-front.css',
        array(),
        MSAAS_FAQ_VERSION
    );

    wp_register_script(
        'msaas-faq-front',
        MSAAS_FAQ_URL . 'assets/js/faq-front.js',
        array(),
        MSAAS_FAQ_VERSION,
        true
    );
}
add_action( 'wp_enqueue_scripts', 'msaas_faq_register_assets' );

/* ─── استایل ادمین برای متاباکس ─── */
function msaas_faq_admin_assets( $hook ) {
    global $post_type;
    if ( $post_type === 'msaas_faq_set' ) {
        wp_enqueue_style(
            'msaas-faq-admin',
            MSAAS_FAQ_URL . 'assets/css/faq-admin.css',
            array(),
            MSAAS_FAQ_VERSION
        );wp_enqueue_script(
            'msaas-faq-admin',
            MSAAS_FAQ_URL . 'assets/js/faq-admin.js',
            array( 'jquery', 'jquery-ui-sortable' ),
            MSAAS_FAQ_VERSION,
            true
        );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
    }
}
add_action( 'admin_enqueue_scripts', 'msaas_faq_admin_assets' );

/* ─── پاکسازی هنگام حذف ─── */
register_uninstall_hook( __FILE__, 'msaas_faq_uninstall' );
function msaas_faq_uninstall() {
    delete_option( 'msaas_faq_settings' );

    $posts = get_posts( array(
        'post_type'      => 'msaas_faq_set',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ) );
    foreach ( $posts as $pid ) {
        wp_delete_post( $pid, true );
    }
}

/* ─── فعال‌سازی: Flush Rewrite Rules ─── */
register_activation_hook( __FILE__, 'msaas_faq_activate' );
function msaas_faq_activate() {
    $cpt = new MSAAS_FAQ_CPT();
    $cpt->register_post_type();
    flush_rewrite_rules();
}

/* ─── غیرفعال‌سازی ─── */
register_deactivation_hook( __FILE__, 'msaas_faq_deactivate' );
function msaas_faq_deactivate() {
    flush_rewrite_rules();
}
