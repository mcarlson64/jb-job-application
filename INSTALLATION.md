# Installation and Testing Guide

## Quick Start Installation

1. **Copy plugin to WordPress**:
   ```bash
   # Copy this folder to your WordPress plugins directory
   cp -r jb-job-application /path/to/wordpress/wp-content/plugins/
   ```

2. **Activate the plugin**:
   - Log in to WordPress admin
   - Go to Plugins → Installed Plugins
   - Find "JB Job Application" and click "Activate"

3. **Verify activation**:
   - Check that "Job Applications" appears in the admin menu
   - The "applicant" user role should now exist

## Testing the Plugin

### Test 1: Applicant Registration

1. Log out of WordPress admin
2. Go to: `yoursite.com/wp-login.php?action=register`
3. Fill in the registration form
4. Select "Applicant (Job Seeker)" as the role
5. Complete registration
6. Verify you can log in with the new account

### Test 2: Add Application Form Block

1. Log in as an administrator
2. Create a new page (Pages → Add New)
3. Title it "Job Application"
4. Click the "+" button to add a block
5. Search for "Job Application Form"
6. Insert the block
7. Publish the page
8. Note the page URL for testing

### Test 3: Submit Application (Not Logged In)

1. Log out from admin account
2. Visit the job application page
3. Verify you see a message asking to login/register
4. Click the "Register as Applicant" button
5. Register a new applicant account

### Test 4: Submit Application (Logged In as Applicant)

1. Log in as an applicant user
2. Visit the job application page
3. Fill out the form:
   - First Name: John
   - Last Name: Doe
   - Email: john.doe@example.com
   - Phone: (555) 123-4567
   - Upload a PDF resume (create a test PDF if needed)
4. Click "Submit Application"
5. Verify you see a success message

### Test 5: Review Application in Admin

1. Log out and log in as an administrator
2. Go to "Job Applications" in the admin menu
3. Verify the submitted application appears in the list
4. Click on the application to view details
5. Verify all information is displayed correctly
6. Click "Download Resume (PDF)" to verify the file

### Test 6: Security Tests

1. **Test non-applicant access**:
   - Create a regular subscriber user
   - Log in as subscriber
   - Try to access the form
   - Verify you see an error message

2. **Test file type validation**:
   - Log in as an applicant
   - Try to upload a non-PDF file (e.g., .docx, .txt)
   - Verify you see an error about file type

3. **Test file size validation**:
   - Try to upload a PDF larger than 5MB
   - Verify you see an error about file size

## Creating Test PDF

If you need a test PDF file:

```bash
# On Linux/Mac
echo "Test Resume" | ps2pdf - test-resume.pdf

# Or use online tool: https://smallpdf.com/word-to-pdf
# Or create one in Microsoft Word/Google Docs
```

## Troubleshooting

### Form doesn't appear
- Make sure the plugin is activated
- Check that you've added the block to a page
- Clear browser cache

### Upload fails
- Check PHP upload_max_filesize in php.ini (should be >= 5MB)
- Check post_max_size in php.ini (should be >= 5MB)
- Verify wp-content/uploads directory is writable

### Applications don't appear in admin
- Verify you submitted the form successfully
- Check database for posts of type 'jb_application'
- Look for JavaScript errors in browser console

## Technical Details

### Database Structure
Applications are stored as WordPress custom posts with meta data:
- Post Type: `jb_application`
- Meta Keys:
  - `_jb_first_name`
  - `_jb_last_name`
  - `_jb_email`
  - `_jb_phone`
  - `_jb_resume_url`
  - `_jb_resume_file`
  - `_jb_submission_date`

### File Storage
Uploaded resumes are stored in: `wp-content/uploads/`

### User Role
Custom role: `applicant` with capability: `submit_application`

### AJAX Endpoints
- Action: `jb_submit_application`
- Nonce: `jb_application_submit`

## Uninstallation

To remove the plugin:

1. Deactivate the plugin in WordPress admin
2. Delete the plugin files
3. (Optional) Manually remove:
   - Custom role: `applicant`
   - Post type: `jb_application` entries
   - Uploaded resume files
