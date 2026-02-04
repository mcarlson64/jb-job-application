<?php
/**
 * Plugin Name: JB Job Application
 * Plugin URI: https://github.com/mcarlson64/jb-job-application
 * Description: Job application plugin with applicant registration, form submission, and admin management.
 * Version: 1.0.0
 * Author: JB
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: jb-job-application
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('JB_JOB_APP_VERSION', '1.0.0');
define('JB_JOB_APP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('JB_JOB_APP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once JB_JOB_APP_PLUGIN_DIR . 'includes/class-user-roles.php';
require_once JB_JOB_APP_PLUGIN_DIR . 'includes/class-post-types.php';
require_once JB_JOB_APP_PLUGIN_DIR . 'includes/class-application-handler.php';
require_once JB_JOB_APP_PLUGIN_DIR . 'includes/class-admin-interface.php';
require_once JB_JOB_APP_PLUGIN_DIR . 'includes/class-application-block.php';

/**
 * Initialize the plugin
 */
function jb_job_application_init() {
    // Initialize custom user roles
    JB_User_Roles::init();
    
    // Initialize custom post types
    JB_Post_Types::init();
    
    // Initialize application handler
    JB_Application_Handler::init();
    
    // Initialize admin interface
    JB_Admin_Interface::init();
    
    // Initialize Gutenberg block
    JB_Application_Block::init();
}
add_action('plugins_loaded', 'jb_job_application_init');

/**
 * Activation hook
 */
function jb_job_application_activate() {
    // Create applicant role
    JB_User_Roles::create_applicant_role();
    
    // Register post type for flush_rewrite_rules to work
    JB_Post_Types::register_post_type();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'jb_job_application_activate');

/**
 * Deactivation hook
 */
function jb_job_application_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'jb_job_application_deactivate');
