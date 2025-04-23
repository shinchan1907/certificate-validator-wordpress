<?php
/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    Certificate_Verifier
 * @subpackage Certificate_Verifier/includes
 */

class Certificate_Verifier_Activator {

    /**
     * Creates the necessary database table during plugin activation.
     *
     * @since    1.0.0
     */
    public static function activate() {
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-db.php';
        
        // Create database table
        Certificate_Verifier_DB::create_table();
        
        // Add version to options
        add_option( 'certificate_verifier_version', CERTIFICATE_VERIFIER_VERSION );
        
        // Clear the permalinks
        flush_rewrite_rules();
    }
}
