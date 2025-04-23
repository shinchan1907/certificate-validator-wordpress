<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    Certificate_Verifier
 * @subpackage Certificate_Verifier/includes
 */

class Certificate_Verifier_Deactivator {

    /**
     * Cleanup during plugin deactivation.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // We're not dropping the table on deactivation to preserve data
        // If you want to drop the table, uncomment the line below
        // require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-db.php';
        // Certificate_Verifier_DB::drop_table();
        
        // Clear the permalinks
        flush_rewrite_rules();
    }
}
