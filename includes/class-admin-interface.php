<?php
/**
 * Admin Interface for Managing Applications
 */

if (!defined('ABSPATH')) {
    exit;
}

class JB_Admin_Interface {
    
    /**
     * Initialize hooks
     */
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_meta_boxes'));
        add_filter('manage_jb_application_posts_columns', array(__CLASS__, 'set_custom_columns'));
        add_action('manage_jb_application_posts_custom_column', array(__CLASS__, 'custom_column_content'), 10, 2);
        add_filter('manage_edit-jb_application_sortable_columns', array(__CLASS__, 'sortable_columns'));
    }
    
    /**
     * Add meta boxes for application details
     */
    public static function add_meta_boxes() {
        add_meta_box(
            'jb_application_details',
            esc_html__('Application Details', 'jb-job-application'),
            array(__CLASS__, 'render_details_meta_box'),
            'jb_application',
            'normal',
            'high'
        );
    }
    
    /**
     * Render application details meta box
     */
    public static function render_details_meta_box($post) {
        $first_name = get_post_meta($post->ID, '_jb_first_name', true);
        $last_name = get_post_meta($post->ID, '_jb_last_name', true);
        $email = get_post_meta($post->ID, '_jb_email', true);
        $phone = get_post_meta($post->ID, '_jb_phone', true);
        $resume_url = get_post_meta($post->ID, '_jb_resume_url', true);
        $submission_date = get_post_meta($post->ID, '_jb_submission_date', true);
        
        $applicant = get_user_by('id', $post->post_author);
        ?>
        <table class="form-table">
            <tr>
                <th><strong><?php esc_html_e('Applicant User:', 'jb-job-application'); ?></strong></th>
                <td><?php echo esc_html($applicant->user_login); ?> (ID: <?php echo esc_html($applicant->ID); ?>)</td>
            </tr>
            <tr>
                <th><strong><?php esc_html_e('First Name:', 'jb-job-application'); ?></strong></th>
                <td><?php echo esc_html($first_name); ?></td>
            </tr>
            <tr>
                <th><strong><?php esc_html_e('Last Name:', 'jb-job-application'); ?></strong></th>
                <td><?php echo esc_html($last_name); ?></td>
            </tr>
            <tr>
                <th><strong><?php esc_html_e('Email:', 'jb-job-application'); ?></strong></th>
                <td><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></td>
            </tr>
            <tr>
                <th><strong><?php esc_html_e('Phone:', 'jb-job-application'); ?></strong></th>
                <td><?php echo esc_html($phone); ?></td>
            </tr>
            <tr>
                <th><strong><?php esc_html_e('Resume:', 'jb-job-application'); ?></strong></th>
                <td>
                    <?php if ($resume_url): ?>
                        <a href="<?php echo esc_url($resume_url); ?>" target="_blank" class="button">
                            <?php esc_html_e('Download Resume (PDF)', 'jb-job-application'); ?>
                        </a>
                    <?php else: ?>
                        <?php esc_html_e('No resume uploaded', 'jb-job-application'); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th><strong><?php esc_html_e('Submission Date:', 'jb-job-application'); ?></strong></th>
                <td><?php echo esc_html($submission_date); ?></td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Set custom columns for applications list
     */
    public static function set_custom_columns($columns) {
        return array(
            'cb' => $columns['cb'],
            'title' => esc_html__('Application', 'jb-job-application'),
            'applicant_name' => esc_html__('Applicant Name', 'jb-job-application'),
            'email' => esc_html__('Email', 'jb-job-application'),
            'phone' => esc_html__('Phone', 'jb-job-application'),
            'resume' => esc_html__('Resume', 'jb-job-application'),
            'date' => esc_html__('Submitted', 'jb-job-application'),
        );
    }
    
    /**
     * Custom column content
     */
    public static function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'applicant_name':
                $first_name = get_post_meta($post_id, '_jb_first_name', true);
                $last_name = get_post_meta($post_id, '_jb_last_name', true);
                echo esc_html($first_name . ' ' . $last_name);
                break;
                
            case 'email':
                $email = get_post_meta($post_id, '_jb_email', true);
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                break;
                
            case 'phone':
                $phone = get_post_meta($post_id, '_jb_phone', true);
                echo esc_html($phone);
                break;
                
            case 'resume':
                $resume_url = get_post_meta($post_id, '_jb_resume_url', true);
                if ($resume_url) {
                    echo '<a href="' . esc_url($resume_url) . '" target="_blank" class="button button-small">' . esc_html__('View PDF', 'jb-job-application') . '</a>';
                } else {
                    echo '—';
                }
                break;
        }
    }
    
    /**
     * Make columns sortable
     */
    public static function sortable_columns($columns) {
        $columns['applicant_name'] = 'applicant_name';
        $columns['email'] = 'email';
        return $columns;
    }
}
