<?php
/**
 * Admin page template.
 *
 * @since      1.0.0
 * @package    Certificate_Verifier
 * @subpackage Certificate_Verifier/admin/templates
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Get certificate stats
$total_certificates = Certificate_Verifier_DB::count_certificates();
?>

<div class="wrap certificate-verifier-admin">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <div class="certificate-verifier-admin-content">
        <div class="certificate-verifier-card">
            <h2><?php _e( 'Upload Certificate Data', 'certificate-verifier' ); ?></h2>
            <p><?php _e( 'Upload a CSV or XLSX file containing certificate data. Please make sure your file follows the required format.', 'certificate-verifier' ); ?></p>
            
            <div class="certificate-verifier-template-download">
                <p><?php _e( 'Not sure about the format? Download a blank template:', 'certificate-verifier' ); ?></p>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=certificate-verifier&certificate_template=download&nonce=' . wp_create_nonce( 'download_template_nonce' ) ) ); ?>" class="button button-secondary">
                    <?php _e( 'Download Template', 'certificate-verifier' ); ?>
                </a>
            </div>
            
            <form id="certificate-upload-form" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="certificate_file"><?php _e( 'Select File', 'certificate-verifier' ); ?></label>
                    <input type="file" name="certificate_file" id="certificate_file" accept=".csv,.xlsx" required>
                    <p class="description"><?php _e( 'Accepted formats: CSV, XLSX', 'certificate-verifier' ); ?></p>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="button button-primary"><?php _e( 'Upload', 'certificate-verifier' ); ?></button>
                </div>
                
                <div id="upload-response" class="certificate-verifier-response"></div>
            </form>
        </div>
        
        <div class="certificate-verifier-card">
            <h2><?php _e( 'Current Data', 'certificate-verifier' ); ?></h2>
            
            <div class="certificate-verifier-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo esc_html( $total_certificates ); ?></span>
                    <span class="stat-label"><?php _e( 'Total Certificates', 'certificate-verifier' ); ?></span>
                </div>
            </div>
            
            <h3><?php _e( 'Recent Certificates', 'certificate-verifier' ); ?></h3>
            
            <?php
            $recent_certificates = Certificate_Verifier_DB::get_all_certificates( 10, 0 );
            
            if ( empty( $recent_certificates ) ) {
                echo '<p>' . __( 'No certificates found. Upload some data to get started.', 'certificate-verifier' ) . '</p>';
            } else {
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php _e( 'Certificate ID', 'certificate-verifier' ); ?></th>
                            <th><?php _e( 'Student Name', 'certificate-verifier' ); ?></th>
                            <th><?php _e( 'Course Name', 'certificate-verifier' ); ?></th>
                            <th><?php _e( 'Join Date', 'certificate-verifier' ); ?></th>
                            <th><?php _e( 'End Date', 'certificate-verifier' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $recent_certificates as $certificate ) : ?>
                            <tr>
                                <td><?php echo esc_html( $certificate['certificate_id'] ); ?></td>
                                <td><?php echo esc_html( $certificate['student_name'] ); ?></td>
                                <td><?php echo esc_html( $certificate['course_name'] ); ?></td>
                                <td><?php echo esc_html( $certificate['join_date'] ); ?></td>
                                <td><?php echo esc_html( $certificate['end_date'] ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php
            }
            ?>
        </div>
        
        <div class="certificate-verifier-card">
            <h2><?php _e( 'How to Use', 'certificate-verifier' ); ?></h2>
            
            <h3><?php _e( '1. Upload Certificate Data', 'certificate-verifier' ); ?></h3>
            <p><?php _e( 'Use the form above to upload your certificate data in CSV or XLSX format.', 'certificate-verifier' ); ?></p>
            
            <h3><?php _e( '2. Add the Shortcode to a Page', 'certificate-verifier' ); ?></h3>
            <p><?php _e( 'Add the following shortcode to any page or post where you want the certificate verification form to appear:', 'certificate-verifier' ); ?></p>
            <code>[certificate_validation_form]</code>
            
            <h3><?php _e( '3. Test the Verification', 'certificate-verifier' ); ?></h3>
            <p><?php _e( 'Visit the page where you added the shortcode and try verifying a certificate using one of the IDs you uploaded.', 'certificate-verifier' ); ?></p>
        </div>
    </div>
</div>
