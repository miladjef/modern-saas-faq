<?php
/**
 * شورت‌کد FAQ — خوانش از CPT
 *
 * @package Modern_SaaS_FAQ
 * @since   4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSAAS_FAQ_Shortcodes {

    /**
     * جلوگیری از بارگذاری چندباره Assets
     */
    private static $enqueued = false;

    /**
     * ثبت شورت‌کدها
     */
    public function register() {
        // فرمت استاندارد: [saas_faq id="25"]
        add_shortcode( 'saas_faq', array( $this, 'render_faq' ) );

        // فرمت کوتاه: [Saas_faq=25]
        add_shortcode( 'Saas_faq', array( $this, 'render_faq_short' ) );
    }

    /**
     * پردازش فرمت کوتاه [Saas_faq=25]
     */
    public function render_faq_short( $atts ) {
        $id = 0;

        if ( is_array( $atts ) ) {
            foreach ( $atts as $key => $val ) {
                if ( is_numeric( $key ) ) {
                    $cleaned = ltrim( $val, '=' );
                    if ( is_numeric( $cleaned ) ) {
                        $id = intval( $cleaned );
                        break;
                    }
                }
                if ( is_numeric( $key ) && empty( $val ) ) {
                    $id = intval( $key );
                    break;
                }
            }
        }

        if ( $id > 0 ) {
            return $this->render_faq( array( 'id' => $id ) );
        }

        return '<!-- Modern SaaS FAQ: شناسه مجموعه معتبر نیست -->';
    }

    /**
     * رندر اصلی FAQ از CPT
     */
    public function render_faq( $atts ) {

        $atts = shortcode_atts( array(
            'id' => 0,
        ), $atts, 'saas_faq' );

        $post_id = intval( $atts['id'] );

        // بررسی وجود و معتبر بودن پست
        if ( $post_id < 1 || get_post_type( $post_id ) !== 'msaas_faq_set' ) {
            return '<!-- Modern SaaS FAQ: مجموعه FAQ با شناسه ' . esc_html( $post_id ) . ' یافت نشد -->';
        }

        if ( get_post_status( $post_id ) !== 'publish' ) {
            return '<!-- Modern SaaS FAQ: مجموعه FAQ منتشر نشده است -->';
        }

        // خوانش داده‌ها از Post Meta
        $items = get_post_meta( $post_id, '_msaas_faq_items', true );
        if ( ! is_array( $items ) || empty( $items ) ) {
            return '<!-- Modern SaaS FAQ: هیچ سوالی در این مجموعه وجود ندارد -->';
        }

        // خوانش تنظیمات مجموعه (با fallback به تنظیمات کلی)
        $global_defaults = wp_parse_args(
            get_option( 'msaas_faq_settings' ),
            array(
                'accent_color' => '#ff7a00',
                'style'        => 'glass',
                'max_width'    => '860px',
                'icon'         => 'chevron',
                'multi_open'   => 'no',
                'schema'       => 'yes',
            )
        );

        $settings = wp_parse_args(
            get_post_meta( $post_id, '_msaas_faq_settings', true ),
            $global_defaults
        );

        // ─── فیلتر برای Elementor و سایر ادغام‌ها ───
        $settings = apply_filters( 'msaas_faq_render_settings', $settings, $post_id );

        // ─── Lazy Load: بررسی viewport ───
        $lazy_load = apply_filters( 'msaas_faq_lazy_load', true );
        $lazy_class = $lazy_load ? ' msaas-faq-lazy' : '';
        $lazy_attr = $lazy_load ? ' data-faq-id="' . esc_attr( $post_id ) . '"' : '';

        // ─── بارگذاری یکبارهٔ CSS و JS ───
        if ( ! self::$enqueued ) {
            wp_enqueue_style( 'msaas-faq-front' );
            wp_enqueue_script( 'msaas-faq-front' );
            self::$enqueued = true;
        }

        // ─── متغیرهای CSS ───
        $hash = substr( md5( $post_id . $settings['accent_color'] . $settings['style'] ), 0, 6 );

        $css_vars = sprintf(
            '.msaas-faq-wrap-%s{--faq-accent:%s;--faq-max-w:%s}',
            esc_attr( $hash ),
            esc_attr( $settings['accent_color'] ),
            esc_attr( $settings['max_width'] )
        );

        wp_add_inline_style( 'msaas-faq-front', $css_vars );

        $uid = 'msaas-faq-wrap-' . $hash;
        $style_class = 'msaas-theme--' . sanitize_html_class( $settings['style'] );

        $data_attrs = sprintf(
            'data-multi="%s" data-icon="%s"',
            esc_attr( $settings['multi_open'] ),
            esc_attr( $settings['icon'] )
        );

        // ─── تولید HTML آیتم‌ها ───
        ob_start();

        echo '<div id="' . esc_attr( $uid ) . '" class="msaas-faq-wrap ' . esc_attr( $uid ) . ' ' . esc_attr( $style_class ) . esc_attr( $lazy_class ) . '" dir="rtl" ' . $data_attrs . $lazy_attr . '>';

        foreach ( $items as $item ) {

            $q = esc_html( $item['question'] );
            $a = wp_kses_post( $item['answer'] );
            $open = ( isset( $item['open'] ) && $item['open'] === 'yes' ) ? ' is-open' : '';

            $icon_chevron = '<svg class="faq-icon-svg" viewBox="0 0 24 24" width="18" height="18"><path d="M6 9l6 6 6-6"/></svg>';
            $icon_plus    = '<svg class="faq-icon-svg faq-icon-plus" viewBox="0 0 24 24" width="18" height="18"><path d="M12 5v14M5 12h14"/></svg>';

            ?>
            <div class="msaas-faq-item<?php echo esc_attr( $open ); ?>" itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">

                <button class="msaas-faq-trigger" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>">

                    <span class="msaas-faq-q" itemprop="name">
                        <?php echo $q; ?>
                    </span>

                    <span class="msaas-faq-icon" aria-hidden="true">
                        <?php echo $icon_chevron . $icon_plus; ?>
                    </span>

                </button>

                <div class="msaas-faq-body" role="region" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">

                    <div class="msaas-faq-answer" itemprop="text">
                        <?php echo $a; ?>
                    </div>

                </div>

            </div>
            <?php
        }

        echo '</div>';

        $html = ob_get_clean();

        // ─── Schema.org FAQPage ───
        if ( $settings['schema'] === 'yes' ) {

            $schema_items = array();

            foreach ( $items as $item ) {

                $schema_items[] = array(
                    '@type' => 'Question',
                    'name'  => wp_strip_all_tags( $item['question'] ),
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text'  => wp_strip_all_tags( $item['answer'] ),
                    ),
                );
            }

            $schema = array(
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => $schema_items,
            );

            $html .= '<script type="application/ld+json">'
                . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
                . '</script>';
        }

        return $html;
    }
}
