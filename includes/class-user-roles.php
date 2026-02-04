<?php
/**
 * User Roles Management
 */

if (!defined('ABSPATH')) {
    exit;
}

class JB_User_Roles {
    
    /**
     * Initialize hooks
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'modify_registration'));
        add_filter('registration_redirect', array(__CLASS__, 'applicant_registration_redirect'));
    }
    
    /**
     * Create applicant role on plugin activation
     */
    public static function create_applicant_role() {
        add_role(
            'applicant',
            __('Applicant', 'jb-job-application'),
            array(
                'read' => true,
                'submit_application' => true,
            )
        );
    }
    
    /**
     * Modify registration to support applicant role
     */
    public static function modify_registration() {
        // Note: This plugin requires user registration to be enabled.
        // Admins should enable this in Settings → General → Membership
        // We don't automatically enable it to respect admin configuration.
        
        // Add role selection to registration
        add_action('register_form', array(__CLASS__, 'add_role_field'));
        add_filter('registration_errors', array(__CLASS__, 'validate_role_field'), 10, 3);
        add_action('user_register', array(__CLASS__, 'set_user_role'));
    }
    
    /**
     * Add role selection field to registration form
     */
    public static function add_role_field() {
        ?>
        <p>
            <label for="user_role"><?php _e('I want to register as:', 'jb-job-application'); ?><br />
                <select name="user_role" id="user_role" required>
                    <option value=""><?php _e('Select Role', 'jb-job-application'); ?></option>
                    <option value="applicant"><?php _e('Applicant (Job Seeker)', 'jb-job-application'); ?></option>
                </select>
            </label>
        </p>
        <?php
    }
    
    /**
     * Validate role field
     */
    public static function validate_role_field($errors, $sanitized_user_login, $user_email) {
        if (empty($_POST['user_role'])) {
            $errors->add('user_role_error', __('<strong>Error</strong>: Please select a role.', 'jb-job-application'));
        } elseif ($_POST['user_role'] !== 'applicant') {
            $errors->add('user_role_error', __('<strong>Error</strong>: Invalid role selected.', 'jb-job-application'));
        }
        return $errors;
    }
    
    /**
     * Set user role after registration
     */
    public static function set_user_role($user_id) {
        if (!empty($_POST['user_role']) && $_POST['user_role'] === 'applicant') {
            $user = new WP_User($user_id);
            $user->set_role('applicant');
        }
    }
    
    /**
     * Redirect applicants after registration
     */
    public static function applicant_registration_redirect($redirect_to) {
        // Check if user was registered as applicant
        if (!empty($_POST['user_role']) && $_POST['user_role'] === 'applicant') {
            // Redirect to a page where the application block can be used
            return home_url('/job-application/');
        }
        return $redirect_to;
    }
}
