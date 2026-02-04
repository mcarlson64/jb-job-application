<?php
/**
 * Custom Post Types
 */

if (!defined('ABSPATH')) {
    exit;
}

class JB_Post_Types {
    
    /**
     * Initialize hooks
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_type'));
    }
    
    /**
     * Register job application custom post type
     */
    public static function register_post_type() {
        $labels = array(
            'name' => esc_html__('Job Applications', 'jb-job-application'),
            'singular_name' => esc_html__('Job Application', 'jb-job-application'),
            'menu_name' => esc_html__('Job Applications', 'jb-job-application'),
            'add_new' => esc_html__('Add New', 'jb-job-application'),
            'add_new_item' => esc_html__('Add New Application', 'jb-job-application'),
            'edit_item' => esc_html__('Edit Application', 'jb-job-application'),
            'new_item' => esc_html__('New Application', 'jb-job-application'),
            'view_item' => esc_html__('View Application', 'jb-job-application'),
            'search_items' => esc_html__('Search Applications', 'jb-job-application'),
            'not_found' => esc_html__('No applications found', 'jb-job-application'),
            'not_found_in_trash' => esc_html__('No applications found in Trash', 'jb-job-application'),
        );
        
        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-id-alt',
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => 'do_not_allow',
            ),
            'map_meta_cap' => true,
            'hierarchical' => false,
            'supports' => array('title', 'custom-fields'),
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
        );
        
        register_post_type('jb_application', $args);
    }
}
