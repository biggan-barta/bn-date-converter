<?php
/**
 * Plugin Name: Bangla Date Converter
 * Plugin URI: 
 * Description: Converts English digits to Bangla digits for date, time and entry meta.
 * Version: 1.0.1
 * Author: BigganBarta
 * Author URI: https://bigganbarta.org
 * Text Domain: bn-date-converter
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

class BN_Date_Converter {
    
    private $bn_digits = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    private $options;
    
    public function __construct() {
        $this->options = get_option('bn_date_converter_settings', [
            'enable_dates' => 1,
            'enable_times' => 1,
            'enable_meta' => 1,
            'custom_selectors' => '',
            'excluded_pages' => ''
        ]);

        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'init_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
        
        // Additional hook for initialization
        add_action('wp_footer', [$this, 'trigger_custom_conversion']);

        // Apply filters based on settings
        if (!empty($this->options['enable_dates'])) {
            add_filter('get_the_date', [$this, 'convert_to_bengali_digits']);
            add_filter('the_date', [$this, 'convert_to_bengali_digits']);
            add_filter('get_archives_link', [$this, 'convert_to_bengali_digits']);
        }

        if (!empty($this->options['enable_times'])) {
            add_filter('get_the_time', [$this, 'convert_to_bengali_digits']);
            add_filter('the_time', [$this, 'convert_to_bengali_digits']);
        }

        if (!empty($this->options['enable_meta'])) {
            add_filter('get_comment_date', [$this, 'convert_to_bengali_digits']);
            add_filter('get_comment_time', [$this, 'convert_to_bengali_digits']);
        }
    }

    public function enqueue_admin_assets($hook) {
        if ('settings_page_bn-date-converter' !== $hook) {
            return;
        }

        wp_enqueue_style('bn-date-converter-admin', plugins_url('assets/css/admin.css', __FILE__));
    }

    public function enqueue_frontend_assets() {
        // Always enqueue the script as we might need it for dynamic content
        wp_enqueue_script('bn-date-converter-frontend', plugins_url('assets/js/frontend.js', __FILE__), [], '1.0.0', true);
        wp_localize_script('bn-date-converter-frontend', 'bnDateConverter', [
            'selectors' => isset($this->options['custom_selectors']) ? $this->options['custom_selectors'] : '',
            'digits' => $this->bn_digits
        ]);
    }

    public function add_admin_menu() {
        add_options_page(
            'Bangla Date Converter Settings',
            'Bangla Date',
            'manage_options',
            'bn-date-converter',
            [$this, 'render_settings_page']
        );
    }

    public function init_settings() {
        register_setting(
            'bn_date_converter', 
            'bn_date_converter_settings'
        );

        add_settings_section(
            'bn_date_converter_main',
            'Main Settings',
            [$this, 'settings_section_callback'],
            'bn-date-converter'
        );

        add_settings_field(
            'enable_dates',
            'Enable for Dates',
            [$this, 'render_checkbox_field'],
            'bn-date-converter',
            'bn_date_converter_main',
            ['field' => 'enable_dates']
        );

        add_settings_field(
            'enable_times',
            'Enable for Times',
            [$this, 'render_checkbox_field'],
            'bn-date-converter',
            'bn_date_converter_main',
            ['field' => 'enable_times']
        );

        add_settings_field(
            'enable_meta',
            'Enable for Meta',
            [$this, 'render_checkbox_field'],
            'bn-date-converter',
            'bn_date_converter_main',
            ['field' => 'enable_meta']
        );

        add_settings_field(
            'custom_selectors',
            'Custom CSS Selectors',
            [$this, 'render_textarea_field'],
            'bn-date-converter',
            'bn_date_converter_main',
            ['field' => 'custom_selectors']
        );

        add_settings_field(
            'excluded_pages',
            'Excluded Pages (IDs)',
            [$this, 'render_textarea_field'],
            'bn-date-converter',
            'bn_date_converter_main',
            ['field' => 'excluded_pages']
        );
    }

    public function settings_section_callback() {
        echo '<p>Configure how the Bangla Date Converter should work on your site.</p>';
    }

    public function render_checkbox_field($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : 0;
        ?>
        <label class="switch">
            <input type="checkbox" name="bn_date_converter_settings[<?php echo esc_attr($field); ?>]" 
                   <?php checked(1, $value); ?> value="1">
            <span class="slider round"></span>
        </label>
        <?php
    }

    public function render_textarea_field($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : '';
        ?>
        <textarea name="bn_date_converter_settings[<?php echo esc_attr($field); ?>]" 
                  rows="4" class="large-text code"><?php echo esc_textarea($value); ?></textarea>
        <?php
        if ($field === 'custom_selectors') {
            echo '<p class="description">Enter CSS selectors one per line (e.g., .entry-date, #post-date)</p>';
        } elseif ($field === 'excluded_pages') {
            echo '<p class="description">Enter page IDs one per line where conversion should be disabled</p>';
        }
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        // Ensure settings are properly initialized
        if (false === get_option('bn_date_converter_settings')) {
            add_option('bn_date_converter_settings', [
                'enable_dates' => 1,
                'enable_times' => 1,
                'enable_meta' => 1,
                'custom_selectors' => '',
                'excluded_pages' => ''
            ]);
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <div class="notice notice-info">
                <p>Save your settings and refresh your page to see the changes.</p>
            </div>
            <form method="post" action="options.php">
                <?php
                settings_fields('bn_date_converter');
                do_settings_sections('bn-date-converter');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function convert_to_bengali_digits($content) {
        if (!$content || (is_page() && in_array(get_the_ID(), explode("\n", $this->options['excluded_pages'])))) {
            return $content;
        }
        
        $numbers = range(0, 9);
        $content = str_replace($numbers, $this->bn_digits, $content);
        
        return $content;
    }

    public function trigger_custom_conversion() {
        if (!empty($this->options['custom_selectors'])) {
            ?>
            <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof bnDateConverter !== 'undefined' && typeof bnDateConverter.processCustomSelectors === 'function') {
                    bnDateConverter.processCustomSelectors();
                }
            });
            </script>
            <?php
        }
    }

    public function sanitize_settings($input) {
        $sanitized = [];
        
        // Sanitize checkboxes
        $sanitized['enable_dates'] = isset($input['enable_dates']) ? 1 : 0;
        $sanitized['enable_times'] = isset($input['enable_times']) ? 1 : 0;
        $sanitized['enable_meta'] = isset($input['enable_meta']) ? 1 : 0;
        
        // Sanitize custom selectors
        $sanitized['custom_selectors'] = isset($input['custom_selectors']) 
            ? sanitize_textarea_field($input['custom_selectors'])
            : '';
            
        // Sanitize excluded pages
        $sanitized['excluded_pages'] = isset($input['excluded_pages'])
            ? sanitize_textarea_field($input['excluded_pages'])
            : '';

        // Add settings error for empty custom selectors if they were provided but invalid
        if (!empty($input['custom_selectors']) && empty($sanitized['custom_selectors'])) {
            add_settings_error(
                'bn_date_converter_settings',
                'invalid_selectors',
                'Invalid CSS selectors provided. Please check your input.',
                'error'
            );
        }

        return $sanitized;
    }
}

// Initialize the plugin
$bn_date_converter = new BN_Date_Converter();
