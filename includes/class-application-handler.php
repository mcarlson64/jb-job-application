<?php
/**
 * Application Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class JB_Application_Handler {
    
    /**
     * Initialize hooks
     */
    public static function init() {
        add_action('wp_ajax_jb_submit_application', array(__CLASS__, 'handle_submission'));
        add_action('wp_ajax_nopriv_jb_submit_application', array(__CLASS__, 'require_login'));
    }
    
    /**
     * Require login for non-authenticated users
     */
    public static function require_login() {
        wp_send_json_error(array(
            'message' => __('You must be logged in as an applicant to submit an application.', 'jb-job-application')
        ), 401);
    }
    
    /**
     * Handle application submission
     */
    public static function handle_submission() {
        // Verify nonce
        $post_data = wp_unslash($_POST);
        $nonce = isset($post_data['nonce']) ? sanitize_text_field($post_data['nonce']) : '';

        if (empty($nonce) || !wp_verify_nonce($nonce, 'jb_application_submit')) {
            wp_send_json_error(array(
                'message' => __('Security check failed.', 'jb-job-application')
            ), 403);
        }
        
        // Check if user is logged in and is an applicant
        if (!is_user_logged_in()) {
            wp_send_json_error(array(
                'message' => __('You must be logged in to submit an application.', 'jb-job-application')
            ), 401);
        }
        
        $user = wp_get_current_user();
        if (!in_array('applicant', $user->roles)) {
            wp_send_json_error(array(
                'message' => __('Only applicants can submit applications.', 'jb-job-application')
            ), 403);
        }
        
        // Validate required fields
        $first_name = isset($post_data['first_name']) ? sanitize_text_field($post_data['first_name']) : '';
        $last_name = isset($post_data['last_name']) ? sanitize_text_field($post_data['last_name']) : '';
        $email = isset($post_data['email']) ? sanitize_email($post_data['email']) : '';
        $phone = isset($post_data['phone']) ? sanitize_text_field($post_data['phone']) : '';
        
        if (empty($first_name) || empty($last_name) || empty($email) || empty($phone)) {
            wp_send_json_error(array(
                'message' => __('All fields are required.', 'jb-job-application')
            ), 400);
        }
        
        if (!is_email($email)) {
            wp_send_json_error(array(
                'message' => __('Invalid email address.', 'jb-job-application')
            ), 400);
        }
        
        // Handle file upload
        $files_data = wp_unslash($_FILES);
        $file = isset($files_data['resume']) ? $files_data['resume'] : array();

        if (empty($file) || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            wp_send_json_error(array(
                'message' => __('Resume file is required.', 'jb-job-application')
            ), 400);
        }
        
        // Validate file type (PDF only)
        $file_name = isset($file['name']) ? sanitize_file_name($file['name']) : '';
        $file_type = wp_check_filetype($file_name);
        
        if ($file_type['ext'] !== 'pdf') {
            wp_send_json_error(array(
                'message' => __('Only PDF files are allowed for resume upload.', 'jb-job-application')
            ), 400);
        }
        
        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            wp_send_json_error(array(
                'message' => __('Resume file size must not exceed 5MB.', 'jb-job-application')
            ), 400);
        }
        
        // Upload file
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        
        $upload_overrides = array(
            'test_form' => false,
            'mimes' => array('pdf' => 'application/pdf')
        );
        
        $uploaded_file = wp_handle_upload($file, $upload_overrides);
        
        if (isset($uploaded_file['error'])) {
            wp_send_json_error(array(
                'message' => $uploaded_file['error']
            ), 500);
        }
        
        // Create application post
        $post_data = array(
            'post_title' => sprintf('%s %s - %s', $first_name, $last_name, current_time('mysql')),
            'post_type' => 'jb_application',
            'post_status' => 'publish',
            'post_author' => $user->ID,
        );
        
        $post_id = wp_insert_post($post_data);
        
        if (is_wp_error($post_id)) {
            wp_send_json_error(array(
                'message' => __('Failed to create application.', 'jb-job-application')
            ), 500);
        }
        
        // Save application meta data
        update_post_meta($post_id, '_jb_first_name', $first_name);
        update_post_meta($post_id, '_jb_last_name', $last_name);
        update_post_meta($post_id, '_jb_email', $email);
        update_post_meta($post_id, '_jb_phone', $phone);
        update_post_meta($post_id, '_jb_resume_url', $uploaded_file['url']);
        update_post_meta($post_id, '_jb_resume_file', $uploaded_file['file']);
        update_post_meta($post_id, '_jb_submission_date', current_time('mysql'));
        
        wp_send_json_success(array(
            'message' => __('Your application has been submitted successfully!', 'jb-job-application'),
            'application_id' => $post_id
        ));
    }
}
