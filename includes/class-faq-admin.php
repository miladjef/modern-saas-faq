<?php
/**
 * مدیریت متاباکس‌ها و تنظیمات کلی FAQ
 *
 * @package Modern_SaaS_FAQ
 * @since   3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MSAAS_FAQ_Admin {

    const POST_TYPE = 'msaas_faq_set';

    /**
     * ثبت هوک‌ها
     */
    public function register() {
        // متاباکس‌ها
        add_action( 'add_meta_boxes',      array( $this, 'add_meta_boxes' ) );
        add_action( 'save_post_' . self::POST_TYPE, array( $this, 'save_meta' ), 10, 2 );

        // صفحه تنظیمات کلی (زیرمنوی CPT)
        add_action( 'admin_menu', array( $this, 'add_settings_submenu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /* ================================================================
     *  متاباکس‌ها
     * ================================================================ */

    /**
     * افزودن متاباکس‌ها
     */
    public function add_meta_boxes() {

        // متاباکس تنظیمات نمایش
        add_meta_box(
            'msaas_faq_display_settings',
            '⚙️ تنظیمات نمایش',
            array( $this, 'render_settings_metabox' ),
            self::POST_TYPE,
            'side',
            'high'
        );

        // متاباکس شورت‌کد
        add_meta_box(
            'msaas_faq_shortcode_box',
            '📋 شورت‌کد',
            array( $this, 'render_shortcode_metabox' ),
            self::POST_TYPE,
            'side',
            'high'
        );

        // متاباکس سوالات و جواب‌ها (اصلی)
        add_meta_box(
            'msaas_faq_items_box',
            '❓ سوالات و پاسخ‌ها',
            array( $this, 'render_items_metabox' ),
            self::POST_TYPE,
            'normal',
            'high'
        );
    }

    /**
     * متاباکس: تنظیمات نمایش (sidebar)
     */
    public function render_settings_metabox( $post ) {

        wp_nonce_field( 'msaas_faq_meta_nonce', 'msaas_faq_nonce' );

        $defaults = $this->defaults();
        $settings = wp_parse_args(
            get_post_meta( $post->ID, '_msaas_faq_settings', true ),
            $defaults
        );
        ?>
        <div class="msaas-admin-field">
            <label for="msaas-style"><strong>استایل:</strong></label>
            <select id="msaas-style" name="msaas_faq_settings[style]" style="width:100%;margin-top:4px;">
                <option value="glass"    <?php selected( $settings['style'], 'glass' ); ?>>🧊 Glassmorphism</option>
                <option value="neumorph" <?php selected( $settings['style'], 'neumorph' ); ?>>🔘 Neumorphism</option>
                <option value="minimal"  <?php selected( $settings['style'], 'minimal' ); ?>>✨ Minimal</option>
            </select>
        </div>

        <div class="msaas-admin-field" style="margin-top:12px;">
            <label for="msaas-color"><strong>رنگ اکسنت:</strong></label><br>
            <input type="text" id="msaas-color" name="msaas_faq_settings[accent_color]"
                   value="<?php echo esc_attr( $settings['accent_color'] ); ?>"
                   class="msaas-color-picker" data-default-color="#ff7a00">
        </div>

        <div class="msaas-admin-field" style="margin-top:12px;">
            <label for="msaas-max-width"><strong>حداکثر عرض:</strong></label>
            <input type="text" id="msaas-max-width" name="msaas_faq_settings[max_width]"
                   value="<?php echo esc_attr( $settings['max_width'] ); ?>"
                   placeholder="860px" style="width:100%;margin-top:4px;">
        </div>

        <div class="msaas-admin-field" style="margin-top:12px;">
            <label for="msaas-icon"><strong>نوع آیکن:</strong></label>
            <select id="msaas-icon" name="msaas_faq_settings[icon]" style="width:100%;margin-top:4px;">
                <option value="chevron" <?php selected( $settings['icon'], 'chevron' ); ?>>Chevron ›</option>
                <option value="plus"    <?php selected( $settings['icon'], 'plus' ); ?>>Plus +</option>
            </select>
        </div>

        <div class="msaas-admin-field" style="margin-top:12px;">
            <label>
                <input type="checkbox" name="msaas_faq_settings[multi_open]" value="yes"
                       <?php checked( $settings['multi_open'], 'yes' ); ?>>
                <strong>باز بودن همزمان چند آیتم</strong>
            </label>
        </div>

        <div class="msaas-admin-field" style="margin-top:12px;">
            <label>
                <input type="checkbox" name="msaas_faq_settings[schema]" value="yes"
                       <?php checked( $settings['schema'], 'yes' ); ?>>
                <strong>اسکیمای FAQPage (SEO)</strong>
            </label>
        </div>
        <?php
    }

    /**
     * متاباکس: نمایش شورت‌کد (sidebar)
     */
    public function render_shortcode_metabox( $post ) {

        if ( $post->post_status === 'auto-draft' ) {
            echo '<p style="color:#999;">ابتدا مجموعه را ذخیره کنید تا شورت‌کد تولید شود.</p>';
            return;
        }
        ?>
        <p style="margin-bottom:8px;">این شورت‌کد را در هر صفحه یا نوشته قرار دهید:</p>
        <input type="text" readonly
               value='[saas_faq id="<?php echo esc_attr( $post->ID ); ?>"]'
               style="width:100%;text-align:center;font-family:monospace;font-size:14px;padding:8px;
                      background:#f9f9f9;border:2px dashed #ff7a00;border-radius:8px;cursor:pointer;
                      color:#333;"
               onclick="this.select(); document.execCommand('copy');
                        var el=this; el.style.borderColor='#28a745';
                        setTimeout(function(){el.style.borderColor='#ff7a00';},1200);"
               title="کلیک کنید تا کپی شود">
        <p style="margin-top:6px;font-size:12px;color:#888;">
            همچنین می‌توانید از فرمت <code>[Saas_faq=<?php echo esc_html( $post->ID ); ?>]</code> استفاده کنید.
        </p>
        <?php
    }

    /**
     * متاباکس: سوالات و پاسخ‌ها (Repeater)
     */
    public function render_items_metabox( $post ) {

        $items = get_post_meta( $post->ID, '_msaas_faq_items', true );
        if ( ! is_array( $items ) ) {
            $items = array();
        }
        ?>
        <div id="msaas-faq-items-wrapper">
            <?php
            if ( ! empty( $items ) ) {
                foreach ( $items as $index => $item ) {
                    $this->render_item_row( $index, $item );
                }
            }
            ?>
        </div>

        <div style="margin-top:16px;display:flex;gap:10px;">
            <button type="button" id="msaas-add-faq-item" class="button button-primary button-large"
                    style="background:#ff7a00;border-color:#e06800;font-size:14px;">
                ➕ افزودن سوال جدید
            </button>
            <span style="color:#999;font-size:13px;line-height:38px;">
                برای مرتب‌سازی، آیتم‌ها را بکشید و رها کنید (Drag & Drop)
            </span>
        </div>

        <!-- قالب آیتم جدید (مخفی) -->
        <script type="text/html" id="tmpl-msaas-faq-item">
            <?php $this->render_item_row( '{{INDEX}}', array( 'question' => '', 'answer' => '', 'open' => 'no' ) ); ?>
        </script>
        <?php
    }

    /**
     * رندر یک ردیف سوال/جواب
     */
    private function render_item_row( $index, $item ) {
        $question = isset( $item['question'] ) ? $item['question'] : '';
        $answer   = isset( $item['answer'] )   ? $item['answer']   : '';
        $open     = isset( $item['open'] )      ? $item['open']     : 'no';
        ?>
        <div class="msaas-faq-item-row" data-index="<?php echo esc_attr( $index ); ?>">
            <div class="msaas-faq-item-header">
                <span class="msaas-faq-drag-handle dashicons dashicons-menu" title="جابجایی"></span>
                <span class="msaas-faq-item-number">#<span class="msaas-num"><?php echo is_numeric( $index ) ? intval( $index ) + 1 : '?'; ?></span></span>
                <input type="text"
                       name="msaas_faq_items[<?php echo esc_attr( $index ); ?>][question]"
                       value="<?php echo esc_attr( $question ); ?>"
                       placeholder="سوال را اینجا بنویسید..."
                       class="msaas-faq-question-input widefat">
                <label class="msaas-faq-open-label" title="از ابتدا باز باشد">
                    <input type="checkbox"
                           name="msaas_faq_items[<?php echo esc_attr( $index ); ?>][open]"
                           value="yes" <?php checked( $open, 'yes' ); ?>>
                    باز
                </label>
                <button type="button" class="msaas-faq-toggle-answer button button-small">▼ پاسخ</button>
                <button type="button" class="msaas-faq-remove-item button button-small"
                        style="color:#dc3545;border-color:#dc3545;">✕</button>
            </div>
            <div class="msaas-faq-item-body">
                <?php
                $editor_id = 'msaas_faq_answer_' . $index;
                // اگر ایندکس عددی باشد، ادیتور وردپرس رندر شود
                if ( is_numeric( $index ) ) {
                    wp_editor( $answer, $editor_id, array(
                        'textarea_name' => 'msaas_faq_items[' . $index . '][answer]',
                        'media_buttons' => true,
                        'textarea_rows' => 5,
                        'teeny'         => false,
                        'quicktags'     => true,
                    ) );
                } else {
                    // برای تمپلیت JS از textarea ساده استفاده می‌کنیم
                    ?>
                    <textarea name="msaas_faq_items[<?php echo esc_attr( $index ); ?>][answer]"
                              rows="5" class="widefat msaas-faq-answer-textarea"
                              placeholder="پاسخ را اینجا بنویسید..."><?php echo esc_textarea( $answer ); ?></textarea>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }

    /* ================================================================
     *  ذخیره متاباکس‌ها
     * ================================================================ */

    /**
     * ذخیره متادیتا
     */
    public function save_meta( $post_id, $post ) {

        // بررسی‌های امنیتی
        if ( ! isset( $_POST['msaas_faq_nonce'] ) ||
             ! wp_verify_nonce( $_POST['msaas_faq_nonce'], 'msaas_faq_meta_nonce' ) ) {
            return;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // ─── ذخیره تنظیمات نمایش ───
        $raw_settings = isset( $_POST['msaas_faq_settings'] ) ? $_POST['msaas_faq_settings'] : array();
        $clean_settings = $this->sanitize_display_settings( $raw_settings );
        update_post_meta( $post_id, '_msaas_faq_settings', $clean_settings );

        // ─── ذخیره سوالات و جواب‌ها ───
        $raw_items = isset( $_POST['msaas_faq_items'] ) ? $_POST['msaas_faq_items'] : array();
        $clean_items = array();

        if ( is_array( $raw_items ) ) {
            foreach ( $raw_items as $item ) {
                $q = isset( $item['question'] ) ? sanitize_text_field( $item['question'] ) : '';
                $a = isset( $item['answer'] )   ? wp_kses_post( $item['answer'] )          : '';
                $o = ( isset( $item['open'] ) && $item['open'] === 'yes' ) ? 'yes' : 'no';

                // فقط آیتم‌هایی که سوال دارند ذخیره شوند
                if ( ! empty( $q ) ) {
                    $clean_items[] = array(
                        'question' => $q,
                        'answer'   => $a,
                        'open'     => $o,
                    );
                }
            }
        }

        update_post_meta( $post_id, '_msaas_faq_items', $clean_items );
    }

    /**
     * پاکسازی تنظیمات نمایش
     */
    private function sanitize_display_settings( $input ) {
        $clean = array();
        $clean['style']        = in_array( $input['style'] ?? '', array( 'glass', 'neumorph', 'minimal' ), true )
                                 ? $input['style'] : 'glass';
        $clean['accent_color'] = sanitize_hex_color( $input['accent_color'] ?? '#ff7a00' );
        $clean['max_width']    = sanitize_text_field( $input['max_width'] ?? '860px' );
        $clean['icon']         = in_array( $input['icon'] ?? '', array( 'chevron', 'plus' ), true )
                                 ? $input['icon'] : 'chevron';
        $clean['multi_open']   = ( $input['multi_open'] ?? '' ) === 'yes' ? 'yes' : 'no';
        $clean['schema']       = ( $input['schema'] ?? '' ) === 'yes' ? 'yes' : 'no';
        return $clean;
    }

    /* ================================================================
     *  تنظیمات کلی (زیرمنوی CPT)
     * ================================================================ */

    /**
     * افزودن صفحه تنظیمات کلی به زیرمنوی CPT
     */
    public function add_settings_submenu() {
        add_submenu_page(
            'edit.php?post_type=' . self::POST_TYPE,
            'تنظیمات کلی FAQ',
            '⚙️ تنظیمات کلی',
            'manage_options',
            'msaas-faq-global-settings',
            array( $this, 'global_settings_page' )
        );
    }

    /**
     * ثبت تنظیمات کلی
     */
    public function register_settings() {
        register_setting( 'msaas_faq_global_group', 'msaas_faq_settings', array(
            'sanitize_callback' => array( $this, 'sanitize_global' ),
            'default'           => $this->defaults(),
        ) );
    }

    /**
     * مقادیر پیش‌فرض
     */
    public function defaults() {
        return array(
            'accent_color' => '#ff7a00',
            'style'        => 'glass',
            'max_width'    => '860px',
            'icon'         => 'chevron',
            'multi_open'   => 'no',
            'schema'       => 'yes',
        );
    }

    /**
     * پاکسازی تنظیمات کلی
     */
    public function sanitize_global( $input ) {
        return $this->sanitize_display_settings( $input );
    }

    /**
     * صفحه تنظیمات کلی
     */
    public function global_settings_page() {
        $opts = wp_parse_args( get_option( 'msaas_faq_settings' ), $this->defaults() );
        ?>
        <div class="wrap">
            <h1>⚡ تنظیمات کلی FAQ مدرن</h1>
            <p style="font-size:14px;color:#666;">
                این تنظیمات به عنوان پیش‌فرض برای مجموعه‌های جدید FAQ استفاده می‌شوند.
                هر مجموعه می‌تواند تنظیمات مخصوص به خود را داشته باشد.
            </p>

            <form method="post" action="options.php">
                <?php settings_fields( 'msaas_faq_global_group' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">رنگ اکسنت پیش‌فرض</th>
                        <td><input type="color" name="msaas_faq_settings[accent_color]"
                                   value="<?php echo esc_attr( $opts['accent_color'] ); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row">استایل پیش‌فرض</th>
                        <td>
                            <select name="msaas_faq_settings[style]">
                                <option value="glass"    <?php selected( $opts['style'], 'glass' ); ?>>Glassmorphism</option>
                                <option value="neumorph" <?php selected( $opts['style'], 'neumorph' ); ?>>Neumorphism</option>
                                <option value="minimal"  <?php selected( $opts['style'], 'minimal' ); ?>>Minimal</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">حداکثر عرض پیش‌فرض</th>
                        <td><input type="text" name="msaas_faq_settings[max_width]"
                                   value="<?php echo esc_attr( $opts['max_width'] ); ?>" placeholder="860px"></td>
                    </tr>
                    <tr>
                        <th scope="row">آیکن پیش‌فرض</th>
                        <td>
                            <select name="msaas_faq_settings[icon]">
                                <option value="chevron" <?php selected( $opts['icon'], 'chevron' ); ?>>Chevron</option>
                                <option value="plus"    <?php selected( $opts['icon'], 'plus' ); ?>>Plus</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">باز بودن همزمان چند آیتم</th>
                        <td>
                            <label><input type="checkbox" name="msaas_faq_settings[multi_open]" value="yes"
                                          <?php checked( $opts['multi_open'], 'yes' ); ?>> فعال</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">اسکیمای FAQPage</th>
                        <td>
                            <label><input type="checkbox" name="msaas_faq_settings[schema]" value="yes"
                                          <?php checked( $opts['schema'], 'yes' ); ?>> فعال</label>
                        </td>
                    </tr>
                </table>

                <?php submit_button( 'ذخیره تنظیمات' ); ?>
            </form>

            <hr>
            <h2>📖 راهنمای استفاده</h2>
            <table class="widefat" style="max-width:700px;">
                <thead>
                    <tr><th>روش</th><th>نمونه</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td>شورت‌کد استاندارد</td>
                        <td><code>[saas_faq id="25"]</code></td>
                    </tr>
                    <tr>
                        <td>شورت‌کد کوتاه</td>
                        <td><code>[Saas_faq=25]</code></td>
                    </tr>
                    <tr>
                        <td>تابع PHP</td>
                        <td><code>&lt;?php echo do_shortcode('[saas_faq id="25"]'); ?&gt;</code></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
}
