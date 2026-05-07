<?php
/**
 * Elementor Widget: Modern SaaS FAQ
 *
 * @package Modern_SaaS_FAQ
 * @since   4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSAAS_FAQ_Elementor_Widget extends \Elementor\Widget_Base {

    /**
     * نام ویجت
     */
    public function get_name() {
        return 'msaas_faq';
    }

    /**
     * عنوان ویجت
     */
    public function get_title() {
        return '⚡ Modern FAQ';
    }

    /**
     * آیکن ویجت
     */
    public function get_icon() {
        return 'eicon-help-o';
    }

    /**
     * دسته‌بندی
     */
    public function get_categories() {
        return array( 'msaas-faq' );
    }

    /**
     * کلمات کلیدی
     */
    public function get_keywords() {
        return array( 'faq', 'accordion', 'سوالات', 'پرسش' );
    }

    /**
     * تنظیمات کنترل‌ها
     */
    protected function register_controls() {

        // ─── بخش محتوا ───
        $this->start_controls_section(
            'content_section',
            array(
                'label' => 'محتوا',
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        // انتخاب مجموعه FAQ
        $this->add_control(
            'faq_id',
            array(
                'label'       => 'انتخاب مجموعه FAQ',
                'type'        => \Elementor\Controls_Manager::SELECT2,
                'options'     => $this->get_faq_sets(),
                'default'     => '',
                'label_block' => true,
            )
        );

        $this->end_controls_section();

        // ─── بخش استایل ───
        $this->start_controls_section(
            'style_section',
            array(
                'label' => 'استایل',
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
            )
        );

        $this->add_control(
            'override_style',
            array(
                'label'        => 'بازنویسی تنظیمات مجموعه',
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'label_on'     => 'بله',
                'label_off'    => 'خیر',
                'return_value' => 'yes',
                'default'      => 'no',
            )
        );

        $this->add_control(
            'style',
            array(
                'label'     => 'تم',
                'type'      => \Elementor\Controls_Manager::SELECT,
                'options'   => array(
                    'glass'    => 'Glassmorphism',
                    'neumorph' => 'Neumorphism',
                    'minimal'  => 'Minimal',
                ),
                'default'   => 'glass',
                'condition' => array( 'override_style' => 'yes' ),
            )
        );

        $this->add_control(
            'accent_color',
            array(
                'label'     => 'رنگ اکسنت',
                'type'      => \Elementor\Controls_Manager::COLOR,
                'default'   => '#ff7a00',
                'condition' => array( 'override_style' => 'yes' ),
            )
        );

        $this->add_control(
            'max_width',
            array(
                'label'     => 'حداکثر عرض',
                'type'      => \Elementor\Controls_Manager::TEXT,
                'default'   => '860px',
                'condition' => array( 'override_style' => 'yes' ),
            )
        );

        $this->end_controls_section();
    }

    /**
     * دریافت لیست مجموعه‌های FAQ
     */
    private function get_faq_sets() {
        $sets = get_posts( array(
            'post_type'      => 'msaas_faq_set',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ) );

        $options = array( '' => 'انتخاب کنید...' );

        foreach ( $sets as $set ) {
            $options[ $set->ID ] = $set->post_title;
        }

        return $options;
    }

    /**
     * رندر ویجت
     */
    protected function render() {
        $settings = $this->get_settings_for_display();
        $faq_id   = intval( $settings['faq_id'] );

        if ( $faq_id < 1 ) {
            if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
                echo '<div style="padding:20px;background:#fff3cd;border:2px dashed #ffc107;border-radius:8px;text-align:center;color:#856404;">';
                echo '<strong>⚠️ لطفاً یک مجموعه FAQ انتخاب کنید</strong>';
                echo '</div>';
            }
            return;
        }

        // اگر override فعال باشد، تنظیمات را تزریق کنیم
        if ( $settings['override_style'] === 'yes' ) {
            add_filter( 'msaas_faq_render_settings', function( $original, $post_id ) use ( $settings, $faq_id ) {
                if ( $post_id === $faq_id ) {
                    return array(
                        'style'        => $settings['style'],
                        'accent_color' => $settings['accent_color'],
                        'max_width'    => $settings['max_width'],'icon'         => $original['icon'] ?? 'chevron',
                        'multi_open'   => $original['multi_open'] ?? 'no',
                        'schema'       => $original['schema'] ?? 'yes',
                    );
                }
                return $original;
            }, 10, 2 );
        }

        echo do_shortcode( '[saas_faq id="' . $faq_id . '"]' );
    }

    /**
     * رندر محتوا در حالت ویرایش (Live Preview)
     */
    protected function content_template() {
        ?>
        <# if ( settings.faq_id ) { #>
            <div style="padding:20px;background:#e7f3ff;border:2px solid #0073aa;border-radius:8px;text-align:center;">
                <strong>⚡ Modern FAQ</strong><br>
                <span style="font-size:13px;color:#555;">ID: {{ settings.faq_id }}</span>
            </div>
        <# } else { #>
            <div style="padding:20px;background:#fff3cd;border:2px dashed #ffc107;border-radius:8px;text-align:center;color:#856404;">
                <strong>⚠️ لطفاً یک مجموعه FAQ انتخاب کنید</strong>
            </div>
        <# } #>
        <?php
    }
}
