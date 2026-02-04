# JB Job Application Plugin

A WordPress plugin that provides a complete job application system with applicant registration, secure form submission, and admin management.

## Features

- **Applicant Registration**: Custom "applicant" user role with dedicated registration process
- **Authentication Required**: Only authenticated applicants can access and submit applications
- **Gutenberg Block**: Easy-to-embed application form block for any page or post
- **Secure File Upload**: PDF resume upload with file type and size validation (max 5MB)
- **Personal Information Form**: Collect first name, last name, email, and phone number
- **Admin Dashboard**: Review and manage all job applications from the WordPress admin
- **Security**: Built-in nonces, capability checks, and file validation

## Installation

1. Upload the `jb-job-application` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. The plugin will automatically create the "applicant" user role
4. **Important**: Enable user registration in WordPress:
   - Go to Settings → General
   - Check "Anyone can register"
   - Save changes

## Usage

### For Applicants

1. **Register**: New users can register on the WordPress registration page and select "Applicant (Job Seeker)" as their role
2. **Login**: Log in to your WordPress account
3. **Submit Application**: Navigate to a page with the job application form block and fill out the form
4. **Upload Resume**: Upload your resume in PDF format (max 5MB)

### For Site Administrators

1. **Add Form Block**: In the block editor, add the "Job Application Form" block to any page or post
2. **View Applications**: Go to "Job Applications" in the WordPress admin menu to see all submissions
3. **Review Details**: Click on any application to view full details and download the applicant's resume
4. **Manage**: Applications are stored as custom posts with all data securely saved

## Block Usage

To add the job application form to a page:

1. Edit a page or post in the WordPress block editor
2. Click the "+" button to add a new block
3. Search for "Job Application Form"
4. Insert the block - it will automatically render the form for authenticated applicants

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- File upload enabled in PHP configuration

## Security Features

- CSRF protection with WordPress nonces
- User authentication and role verification
- File type validation (PDF only)
- File size limits (5MB maximum)
- Secure file upload handling
- Capability checks for admin access

## Developer Information

### Custom Post Type

Applications are stored in a custom post type: `jb_application`

### User Role

The plugin creates a custom role: `applicant` with the capability `submit_application`

### AJAX Endpoint

Form submissions are handled via: `wp_ajax_jb_submit_application`

## License

GPL v2 or later
