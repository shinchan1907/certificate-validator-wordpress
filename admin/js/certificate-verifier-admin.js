(function( $ ) {
    'use strict';

    $(document).ready(function() {
        // Handle certificate upload form submission
        $('#certificate-upload-form').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            formData.append('action', 'certificate_verifier_upload');
            formData.append('nonce', certificate_verifier_admin.nonce);
            
            // Clear previous responses
            $('#upload-response').empty().removeClass('error success');
            
            // Show loading indicator
            $('#upload-response').html('<div class="loading">' + certificate_verifier_admin.loading + '</div>');
            
            $.ajax({
                url: certificate_verifier_admin.ajax_url,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#upload-response').empty();
                    
                    if (response.success) {
                        var data = response.data;
                        var message = '<div class="success-message">';
                        
                        message += '<p><strong>' + certificate_verifier_admin.success + ':</strong> ';
                        message += 'Successfully imported ' + data.success + ' certificates.</p>';
                        
                        if (data.duplicate > 0) {
                            message += '<p>' + data.duplicate + ' duplicate certificates were found and skipped.</p>';
                        }
                        
                        if (data.failed > 0) {
                            message += '<p>' + data.failed + ' certificates failed to import.</p>';
                        }
                        
                        // Add file errors if any
                        if (data.file_errors && data.file_errors.length > 0) {
                            message += '<div class="validation-errors">';
                            message += '<p><strong>File validation errors:</strong></p>';
                            message += '<ul>';
                            
                            $.each(data.file_errors, function(index, error) {
                                message += '<li>' + error + '</li>';
                            });
                            
                            message += '</ul>';
                            message += '</div>';
                        }
                        
                        // Add database errors if any
                        if (data.errors && data.errors.length > 0) {
                            message += '<div class="db-errors">';
                            message += '<p><strong>Database errors:</strong></p>';
                            message += '<ul>';
                            
                            $.each(data.errors, function(index, error) {
                                message += '<li>' + error + '</li>';
                            });
                            
                            message += '</ul>';
                            message += '</div>';
                        }
                        
                        message += '</div>';
                        
                        $('#upload-response').html(message).addClass('success');
                        
                        // Reload the page after 2 seconds to show the updated data
                        if (data.success > 0) {
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        }
                    } else {
                        var errorMessage = '<div class="error-message">';
                        errorMessage += '<p><strong>' + certificate_verifier_admin.error + ':</strong> ';
                        errorMessage += response.data.message + '</p>';
                        errorMessage += '</div>';
                        
                        $('#upload-response').html(errorMessage).addClass('error');
                    }
                },
                error: function(xhr, status, error) {
                    $('#upload-response').empty();
                    
                    var errorMessage = '<div class="error-message">';
                    errorMessage += '<p><strong>' + certificate_verifier_admin.error + ':</strong> ';
                    errorMessage += 'An error occurred during the upload. Please try again.</p>';
                    errorMessage += '</div>';
                    
                    $('#upload-response').html(errorMessage).addClass('error');
                }
            });
        });
    });

})( jQuery );
