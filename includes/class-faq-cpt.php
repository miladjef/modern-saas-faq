<?php
/**
 * Custom Post Type برای مجموعه‌های FAQ
 *
 * @package Modern_SaaS_FAQ
 * @since   3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSAAS_FAQ_CPT {

    /**
     * نام پست تایپ
     */
    const POST_TYPE = 'msaas_faq_set';

    /**
     * ثبت هوک‌ها
     */
    public function register() {
        $this->register_post_type();

        // ستون‌های سفارشی در لیست ادمین
        add_filter( 'manage_' . self::POST_TYPE . '_posts_columns',  array( $this, 'custom_columns' ) );
        add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( $this, 'column_content' ), 10, 2 );
    }

    /**
     * ثبت Custom Post Type
     */
    public function register_post_type() {

        $labels = array(
            'name'                  => 'مجموعه‌های FAQ',
            'singular_name'        => 'مجموعه FAQ',
            'menu_name'            => '⚡ FAQ مدرن',
            'name_admin_bar'       => 'مجموعه FAQ',
            'add_new'              => 'افزودن مجموعه جدید',
            'add_new_item'         => 'افزودن مجموعه FAQ جدید',
            'new_item'             => 'مجموعه FAQ جدید',
            'edit_item'            => 'ویرایش مجموعه FAQ',
            'view_item'            => 'مشاهده مجموعه FAQ',
            'all_items'            => 'همه مجموعه‌ها',
            'search_items'         => 'جست‌وجو در مجموعه‌ها',
            'not_found'            => 'مجموعه‌ای یافت نشد.',
            'not_found_in_trash'   => 'مجموعه‌ای در سطل زباله یافت نشد.',
        );

        $args = array(
            'labels'              => $labels,
            'public'              => false,
            'publicly_queryable'  => false,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => false,
            'rewrite'             => false,
            'capability_type'     => 'post',
            'has_archive'         => false,
            'hierarchical'        => false,
            'menu_position'       => 25,
            'menu_icon'           => 'dashicons-editor-help',
            'supports'            => array( 'title' ),
            'show_in_rest'        => false,
        );

        register_post_type( self::POST_TYPE, $args );
    }

    /**
     * ستون‌های سفارشی در لیست ادمین
     */
    public function custom_columns( $columns ) {
        $new = array();
        foreach ( $columns as $key => $val ) {
            $new[ $key ] = $val;
            if ( $key === 'title' ) {
                $new['shortcode']   = 'شورت‌کد';
                $new['faq_count']   = 'تعداد سوالات';
                $new['faq_style']   = 'استایل';
            }
        }
        return $new;
    }

    /**
     * محتوای ستون‌های سفارشی
     */
    public function column_content( $column, $post_id ) {

        switch ( $column ) {
            case 'shortcode':
                echo '<code style="background:#f0f0f1;padding:4px 10px;border-radius:4px;font-size:13px;user-select:all;">[saas_faq id="' . esc_attr( $post_id ) . '"]</code>';
                break;

            case 'faq_count':
                $items = get_post_meta( $post_id, '_msaas_faq_items', true );
                $count = is_array( $items ) ? count( $items ) : 0;
                echo '<span style="font-weight:600;color:#ff7a00;">' . esc_html( $count ) . '</span>';
                break;

            case 'faq_style':
                $settings = get_post_meta( $post_id, '_msaas_faq_settings', true );
                $style = isset( $settings['style'] ) ? $settings['style'] : 'glass';
                $labels_map = array(
                    'glass'    => 'Glassmorphism',
                    'neumorph' => 'Neumorphism',
                    'minimal'  => 'Minimal',
                );
                echo esc_html( $labels_map[ $style ] ?? $style );
                break;
        }
    }
}
