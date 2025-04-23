(function( $ ) {
    'use strict';

    $(document).ready(function() {
        // Handle certificate verification form submission
        $('#certificate-verification-form').on('submit', function(e) {
            e.preventDefault();
            
            var certificate_id = $('#certificate_id').val();
            
            // Clear previous results
            $('#certificate-verification-result').empty().removeClass('error success');
            
            // Show loading indicator
            $('#certificate-verification-result').html('<div class="loading">' + certificate_verifier.loading + '</div>').addClass('loading');
            
            $.ajax({
                url: certificate_verifier.ajax_url,
                type: 'POST',
                data: {
                    action: 'certificate_verifier_validate',
                    certificate_id: certificate_id,
                    nonce: certificate_verifier.nonce
                },
                success: function(response) {
                    $('#certificate-verification-result').empty().removeClass('loading');
                    
                    if (response.success) {
                        var data = response.data;
                        var html = '<div class="certificate-details">';
                        
                        html += '<h3>' + data.student_name + '</h3>';
                        html += '<div class="certificate-info">';
                        html += '<div class="certificate-info-row"><span class="label">' + 'Course Name:' + '</span><span class="value">' + data.course_name + '</span></div>';
                        html += '<div class="certificate-info-row"><span class="label">' + 'Phone:' + '</span><span class="value">' + data.phone + '</span></div>';
                        html += '<div class="certificate-info-row"><span class="label">' + 'Join Date:' + '</span><span class="value">' + data.join_date + '</span></div>';
                        html += '<div class="certificate-info-row"><span class="label">' + 'End Date:' + '</span><span class="value">' + data.end_date + '</span></div>';
                        html += '</div>';
                        
                        html += '<div class="certificate-verified">';
                        html += '<svg class="check-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
                        html += '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>';
                        html += '<polyline points="22 4 12 14.01 9 11.01"></polyline>';
                        html += '</svg>';
                        html += '<span>' + 'Certificate Verified' + '</span>';
                        html += '</div>';
                        
                        html += '</div>';
                        
                        $('#certificate-verification-result').html(html).addClass('success');
                    } else {
                        var html = '<div class="certificate-error">';
                        html += '<svg class="error-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
                        html += '<circle cx="12" cy="12" r="10"></circle>';
                        html += '<line x1="15" y1="9" x2="9" y2="15"></line>';
                        html += '<line x1="9" y1="9" x2="15" y2="15"></line>';
                        html += '</svg>';
                        html += '<span>' + response.data.message + '</span>';
                        html += '</div>';
                        
                        $('#certificate-verification-result').html(html).addClass('error');
                    }
                },
                error: function(xhr, status, error) {
                    $('#certificate-verification-result').empty().removeClass('loading');
                    
                    var html = '<div class="certificate-error">';
                    html += '<svg class="error-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
                    html += '<circle cx="12" cy="12" r="10"></circle>';
                    html += '<line x1="15" y1="9" x2="9" y2="15"></line>';
                    html += '<line x1="9" y1="9" x2="15" y2="15"></line>';
                    html += '</svg>';
                    html += '<span>' + 'An error occurred during verification. Please try again.' + '</span>';
                    html += '</div>';
                    
                    $('#certificate-verification-result').html(html).addClass('error');
                }
            });
        });
    });

})( jQuery );
