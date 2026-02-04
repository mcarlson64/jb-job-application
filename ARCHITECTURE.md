# Plugin Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                  JB Job Application Plugin                      │
│                     (jb-job-application.php)                    │
└────────────────┬────────────────────────────────────────────────┘
                 │
                 ├─── Initialization & Hooks
                 │
    ┌────────────┴──────────────┐
    │                           │
    ▼                           ▼
┌───────────────────┐    ┌──────────────────┐
│   User System     │    │   Content        │
│                   │    │   Management     │
│ class-user-roles  │    │                  │
│  .php             │    │ class-post-types │
│                   │    │  .php            │
│ • Creates         │    │                  │
│   "applicant"     │    │ • Registers      │
│   role            │    │   jb_application │
│ • Registration    │    │   post type      │
│   form hook       │    │ • Admin only     │
│ • Role validation │    │                  │
└─────┬─────────────┘    └──────────────────┘
      │
      │  Authenticated
      │  Applicant
      ▼
┌──────────────────────────────────────────┐
│        Frontend Interface                │
│                                          │
│  class-application-block.php             │
│                                          │
│  • Gutenberg Block Registration          │
│  • Authentication Check                  │
│  • Form Rendering                        │
│                                          │
│  ┌────────────────────────────────┐     │
│  │  Block Assets                  │     │
│  │  blocks/application-form/      │     │
│  │                                │     │
│  │  • block.js (Editor UI)        │     │
│  │  • editor.css (Editor Style)   │     │
│  │  • style.css (Frontend Style)  │     │
│  └────────────────────────────────┘     │
└────────────┬─────────────────────────────┘
             │
             │ User Submits Form
             │
             ▼
┌──────────────────────────────────────────┐
│        Form Handler                      │
│                                          │
│  assets/js/application-form.js           │
│                                          │
│  • Client Validation                     │
│  • File Size/Type Check                  │
│  • AJAX Submission                       │
│                                          │
└────────────┬─────────────────────────────┘
             │
             │ AJAX Request
             │
             ▼
┌──────────────────────────────────────────┐
│        Backend Handler                   │
│                                          │
│  class-application-handler.php           │
│                                          │
│  • Nonce Verification                    │
│  • Authentication Check                  │
│  • Input Sanitization                    │
│  • File Upload (PDF only)                │
│  • Create Application Post               │
│  • Save Meta Data                        │
│                                          │
└────────────┬─────────────────────────────┘
             │
             │ Stores Data
             │
             ▼
┌──────────────────────────────────────────┐
│        WordPress Database                │
│                                          │
│  wp_posts                                │
│   • post_type: jb_application            │
│   • post_title: Name + Date              │
│   • post_author: User ID                 │
│                                          │
│  wp_postmeta                             │
│   • _jb_first_name                       │
│   • _jb_last_name                        │
│   • _jb_email                            │
│   • _jb_phone                            │
│   • _jb_resume_url                       │
│   • _jb_resume_file                      │
│   • _jb_submission_date                  │
│                                          │
│  wp_uploads/                             │
│   • PDF resume files                     │
│                                          │
└────────────┬─────────────────────────────┘
             │
             │ Admin Access
             │
             ▼
┌──────────────────────────────────────────┐
│        Admin Interface                   │
│                                          │
│  class-admin-interface.php               │
│                                          │
│  • Custom Admin Menu                     │
│  • Application List View                 │
│  • Custom Columns:                       │
│    - Applicant Name                      │
│    - Email                               │
│    - Phone                               │
│    - Resume Link                         │
│  • Detail Meta Box                       │
│  • Download Resume                       │
│                                          │
└──────────────────────────────────────────┘


Security Layers Applied at Each Level:
═════════════════════════════════════════

1. User Level:
   ✓ Role-based access control
   ✓ Authentication required

2. Form Level:
   ✓ Client-side validation
   ✓ AJAX with nonce

3. Server Level:
   ✓ Nonce verification
   ✓ Capability checks
   ✓ Input sanitization
   ✓ File type validation
   ✓ File size limits
   ✓ MIME type checking

4. Storage Level:
   ✓ WordPress APIs (prevent SQL injection)
   ✓ Secure file storage
   ✓ Output escaping

5. Admin Level:
   ✓ Capability checks
   ✓ Read-only display
   ✓ Secure download links


Data Flow:
═════════

User Registration → Applicant Role
                     ↓
              Login Required
                     ↓
          Access Application Block
                     ↓
          Fill Form + Upload PDF
                     ↓
          Client Validation
                     ↓
          AJAX Submission
                     ↓
          Server Validation
                     ↓
          File Processing
                     ↓
          Create Post + Meta
                     ↓
          Success Response
                     ↓
          Admin Can Review
```

## Key Components Interaction

### Registration Flow
```
WordPress Register → Role Select → Validate Role → Create User → Set "applicant" Role
```

### Submission Flow
```
Block Render → Check Auth → Show Form → User Submit → Validate → Upload → Store → Confirm
```

### Review Flow
```
Admin Login → Job Applications Menu → View List → Click Application → View Details → Download PDF
```

## Plugin Hooks & Integration Points

| Hook Type | Hook Name | Purpose |
|-----------|-----------|---------|
| Action | `plugins_loaded` | Initialize plugin |
| Action | `init` | Register post type & modify registration |
| Action | `wp_enqueue_scripts` | Load frontend assets |
| Action | `wp_ajax_jb_submit_application` | Handle submissions |
| Action | `register_form` | Add role field |
| Action | `user_register` | Set user role |
| Action | `add_meta_boxes` | Add admin meta boxes |
| Filter | `registration_errors` | Validate role |
| Filter | `registration_redirect` | Redirect after registration |
| Filter | `manage_jb_application_posts_columns` | Custom columns |
| Filter | `manage_edit-jb_application_sortable_columns` | Sortable columns |

## File Dependency Map

```
jb-job-application.php
    ├── includes/class-user-roles.php
    ├── includes/class-post-types.php
    ├── includes/class-application-handler.php
    ├── includes/class-admin-interface.php
    └── includes/class-application-block.php
            ├── blocks/application-form/block.js
            ├── blocks/application-form/editor.css
            ├── blocks/application-form/style.css
            └── assets/js/application-form.js
```
