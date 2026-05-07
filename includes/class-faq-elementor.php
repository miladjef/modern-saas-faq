<?php
/**
 * Elementor Widget برای Modern SaaS FAQ
 *
 * @package Modern_SaaS_FAQ
 * @since   4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSAAS_FAQ_Elementor {

    /**
     * مقداردهی اولیه
     */
    public static function init() {
        add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widget' ) );
        add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'add_category' ) );
    }

    /**
     * افزودن دسته‌بندی سفارشی
     */
    public static function add_category( $elements_manager ) {
        $elements_manager->add_category(
            'msaas-faq',
            array(
                'title' => '⚡ Modern FAQ',
                'icon'  => 'fa fa-plug',
            )
        );
    }

    /**
     * ثبت ویجت
     */
    public static function register_widget( $widgets_manager ) {
        require_once MSAAS_FAQ_PATH . 'includes/elementor/widget-faq.php';
        $widgets_manager->register( new \MSAAS_FAQ_Elementor_Widget() );
    }
}
