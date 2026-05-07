<?php
/**
 * Gutenberg Block برای Modern SaaS FAQ
 *
 * @package Modern_SaaS_FAQ
 * @since   4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSAAS_FAQ_Gutenberg {

    /**
     * مقداردهی اولیه
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'register_block' ) );
        add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'editor_assets' ) );
    }

    /**
     * ثبت بلاک
     */
    public static function register_block() {

        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        register_block_type( 'msaas-faq/faq-block', array(
            'editor_script'   => 'msaas-faq-block-editor',
            'editor_style'    => 'msaas-faq-block-editor-style',
            'render_callback' => array( __CLASS__, 'render_block' ),
            'attributes'      => array(
                'faqId' => array(
                    'type'    => 'number',
                    'default' => 0,
                ),
            ),
        ) );
    }

    /**
     * بارگذاری اسکریپت و استایل ادیتور
     */
    public static function editor_assets() {
        wp_enqueue_script(
            'msaas-faq-block-editor',
            MSAAS_FAQ_URL . 'assets/js/faq-block.js',
            array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
            MSAAS_FAQ_VERSION,
            true
        );

        wp_localize_script( 'msaas-faq-block-editor', 'msaasFaqData', array(
            'faqSets' => self::get_faq_sets(),
        ) );

        wp_enqueue_style(
            'msaas-faq-block-editor-style',
            MSAAS_FAQ_URL . 'assets/css/faq-block-editor.css',
            array( 'wp-edit-blocks' ),
            MSAAS_FAQ_VERSION
        );
    }

    /**
     * دریافت لیست مجموعه‌های FAQ
     */
    private static function get_faq_sets() {
        $sets = get_posts( array(
            'post_type'      => 'msaas_faq_set',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ) );

        $options = array();

        foreach ( $sets as $set ) {
            $options[] = array(
                'value' => $set->ID,
                'label' => $set->post_title,
            );
        }

        return $options;
    }

    /**
     * رندر بلاک در Frontend
     */
    public static function render_block( $attributes ) {
        $faq_id = isset( $attributes['faqId'] ) ? intval( $attributes['faqId'] ) : 0;

        if ( $faq_id < 1 ) {
            return '';
        }

        return do_shortcode( '[saas_faq id="' . $faq_id . '"]' );
    }
}
