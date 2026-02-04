<?php
/**
 * Application Block Registration
 */

if (!defined('ABSPATH')) {
    exit;
}

class JB_Application_Block {
    
    /**
     * Initialize hooks
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_block'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_frontend_assets'));
    }
    
    /**
     * Register the Gutenberg block
     */
    public static function register_block() {
        // Register block editor assets
        wp_register_script(
            'jb-application-block-editor',
            JB_JOB_APP_PLUGIN_URL . 'blocks/application-form/block.js',
            array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'),
            JB_JOB_APP_VERSION
        );
        
        // Register block editor styles
        wp_register_style(
            'jb-application-block-editor',
            JB_JOB_APP_PLUGIN_URL . 'blocks/application-form/editor.css',
            array('wp-edit-blocks'),
            JB_JOB_APP_VERSION
        );
        
        // Register frontend styles
        wp_register_style(
            'jb-application-block',
            JB_JOB_APP_PLUGIN_URL . 'blocks/application-form/style.css',
            array(),
            JB_JOB_APP_VERSION
        );
        
        // Register the block
        register_block_type('jb-job-application/application-form', array(
            'editor_script' => 'jb-application-block-editor',
            'editor_style' => 'jb-application-block-editor',
            'style' => 'jb-application-block',
            'render_callback' => array(__CLASS__, 'render_block'),
        ));
    }
    
    /**
     * Enqueue frontend assets
     */
    public static function enqueue_frontend_assets() {
        if (has_block('jb-job-application/application-form')) {
            wp_enqueue_script(
                'jb-application-form',
                JB_JOB_APP_PLUGIN_URL . 'assets/js/application-form.js',
                array('jquery'),
                JB_JOB_APP_VERSION,
                true
            );
            
            wp_localize_script('jb-application-form', 'jbJobApp', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('jb_application_submit'),
                'isLoggedIn' => is_user_logged_in(),
                'isApplicant' => is_user_logged_in() && in_array('applicant', wp_get_current_user()->roles),
                'loginUrl' => wp_login_url(get_permalink()),
                'registerUrl' => wp_registration_url(),
            ));
        }
    }
    
    /**
     * Render the block on the frontend
     */
    public static function render_block($attributes, $content) {
        ob_start();
        
        // Check if user is logged in and is an applicant
        if (!is_user_logged_in()) {
            ?>
            <div class="jb-application-form-wrapper">
                <div class="jb-application-notice jb-notice-warning">
                    <p><?php _e('You must be logged in as an applicant to submit a job application.', 'jb-job-application'); ?></p>
                    <p>
                        <a href="<?php echo esc_url(wp_login_url(get_permalink())); ?>" class="button button-primary">
                            <?php _e('Login', 'jb-job-application'); ?>
                        </a>
                        <a href="<?php echo esc_url(wp_registration_url()); ?>" class="button">
                            <?php _e('Register as Applicant', 'jb-job-application'); ?>
                        </a>
                    </p>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
        
        $user = wp_get_current_user();
        if (!in_array('applicant', $user->roles)) {
            ?>
            <div class="jb-application-form-wrapper">
                <div class="jb-application-notice jb-notice-error">
                    <p><?php _e('Only users with the applicant role can submit job applications.', 'jb-job-application'); ?></p>
                </div>
            </div>
            <?php
            return ob_get_clean();
        }
        
        // Render the application form
        ?>
        <div class="jb-application-form-wrapper">
            <form id="jb-application-form" class="jb-application-form" enctype="multipart/form-data">
                <div class="jb-form-header">
                    <h2><?php _e('Job Application Form', 'jb-job-application'); ?></h2>
                    <p><?php _e('Please fill out all fields and upload your resume in PDF format.', 'jb-job-application'); ?></p>
                </div>
                
                <div class="jb-form-messages"></div>
                
                <div class="jb-form-group">
                    <label for="jb-first-name">
                        <?php _e('First Name', 'jb-job-application'); ?> <span class="required">*</span>
                    </label>
                    <input type="text" id="jb-first-name" name="first_name" required />
                </div>
                
                <div class="jb-form-group">
                    <label for="jb-last-name">
                        <?php _e('Last Name', 'jb-job-application'); ?> <span class="required">*</span>
                    </label>
                    <input type="text" id="jb-last-name" name="last_name" required />
                </div>
                
                <div class="jb-form-group">
                    <label for="jb-email">
                        <?php _e('Email', 'jb-job-application'); ?> <span class="required">*</span>
                    </label>
                    <input type="email" id="jb-email" name="email" required />
                </div>
                
                <div class="jb-form-group">
                    <label for="jb-phone">
                        <?php _e('Phone Number', 'jb-job-application'); ?> <span class="required">*</span>
                    </label>
                    <input type="tel" id="jb-phone" name="phone" required />
                </div>
                
                <div class="jb-form-group">
                    <label for="jb-resume">
                        <?php _e('Resume (PDF only, max 5MB)', 'jb-job-application'); ?> <span class="required">*</span>
                    </label>
                    <input type="file" id="jb-resume" name="resume" accept=".pdf" required />
                    <small class="jb-form-help"><?php _e('Please upload your resume in PDF format. Maximum file size: 5MB', 'jb-job-application'); ?></small>
                </div>
                
                <div class="jb-form-actions">
                    <button type="submit" class="button button-primary jb-submit-btn">
                        <?php _e('Submit Application', 'jb-job-application'); ?>
                    </button>
                </div>
            </form>
        </div>
        <?php
        
        return ob_get_clean();
    }
}
