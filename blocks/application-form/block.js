(function(blocks, element, editor, components, i18n) {
    var el = element.createElement;
    var __ = i18n.__;
    
    blocks.registerBlockType('jb-job-application/application-form', {
        title: __('Job Application Form', 'jb-job-application'),
        icon: 'id-alt',
        category: 'widgets',
        description: __('A form for job applicants to submit their information and resume.', 'jb-job-application'),
        
        edit: function(props) {
            return el(
                'div',
                { className: 'jb-application-block-editor' },
                el(
                    'div',
                    { className: 'jb-block-placeholder' },
                    el('span', { className: 'dashicons dashicons-id-alt' }),
                    el('h3', {}, __('Job Application Form', 'jb-job-application')),
                    el('p', {}, __('This block displays a job application form for authenticated applicants. Users must be logged in with the "applicant" role to submit applications.', 'jb-job-application'))
                )
            );
        },
        
        save: function() {
            // Dynamic block - rendered via PHP
            return null;
        }
    });
})(
    window.wp.blocks,
    window.wp.element,
    window.wp.editor,
    window.wp.components,
    window.wp.i18n
);
