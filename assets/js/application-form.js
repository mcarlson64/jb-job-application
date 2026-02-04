(function($) {
    'use strict';
    
    $(document).ready(function() {
        var form = $('#jb-application-form');
        
        if (!form.length) {
            return;
        }
        
        var messagesContainer = form.find('.jb-form-messages');
        var submitBtn = form.find('.jb-submit-btn');
        
        function showMessage(message, type) {
            var messageClass = type === 'success' ? 'notice-success' : 'notice-error';
            var messageElement = $('<div>')
                .addClass('notice ' + messageClass)
                .text(message);
            messagesContainer.empty().append(messageElement);
            
            // Scroll to message
            $('html, body').animate({
                scrollTop: messagesContainer.offset().top - 100
            }, 500);
        }
        
        function clearMessages() {
            messagesContainer.empty();
        }
        
        form.on('submit', function(e) {
            e.preventDefault();
            
            clearMessages();
            
            // Validate file
            var fileInput = $('#jb-resume')[0];
            if (fileInput.files.length === 0) {
                showMessage('Please select a resume file.', 'error');
                return;
            }
            
            var file = fileInput.files[0];
            
            // Check file type
            if (file.type !== 'application/pdf') {
                showMessage('Only PDF files are allowed.', 'error');
                return;
            }
            
            // Check file size (5MB = 5 * 1024 * 1024 bytes)
            if (file.size > 5 * 1024 * 1024) {
                showMessage('Resume file size must not exceed 5MB.', 'error');
                return;
            }
            
            // Disable submit button
            submitBtn.prop('disabled', true).text('Submitting...');
            
            // Prepare form data
            var formData = new FormData();
            formData.append('action', 'jb_submit_application');
            formData.append('nonce', jbJobApp.nonce);
            formData.append('first_name', $('#jb-first-name').val());
            formData.append('last_name', $('#jb-last-name').val());
            formData.append('email', $('#jb-email').val());
            formData.append('phone', $('#jb-phone').val());
            formData.append('resume', file);
            
            // Submit via AJAX
            $.ajax({
                url: jbJobApp.ajaxUrl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showMessage(response.data.message, 'success');
                        form[0].reset();
                    } else {
                        showMessage(response.data.message || 'An error occurred. Please try again.', 'error');
                    }
                },
                error: function(xhr) {
                    var message = 'An error occurred. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.data && xhr.responseJSON.data.message) {
                        message = xhr.responseJSON.data.message;
                    }
                    showMessage(message, 'error');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text('Submit Application');
                }
            });
        });
        
        // File input change handler - show filename
        $('#jb-resume').on('change', function() {
            var fileInput = this;
            var fileName = fileInput.files.length > 0 ? fileInput.files[0].name : '';
            if (fileName) {
                $(this).next('.jb-form-help').text('Selected: ' + fileName);
            }
        });
    });
})(jQuery);
