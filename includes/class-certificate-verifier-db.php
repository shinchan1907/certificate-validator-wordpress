<?php
/**
 * Database operations for the plugin.
 *
 * @since      1.0.0
 * @package    Certificate_Verifier
 * @subpackage Certificate_Verifier/includes
 */

class Certificate_Verifier_DB {

    /**
     * The name of the database table.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $table_name    The name of the database table.
     */
    private static $table_name = 'wp_certificates';

    /**
     * Create the database table.
     *
     * @since    1.0.0
     */
    public static function create_table() {
        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE " . self::get_table_name() . " (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            certificate_id varchar(255) NOT NULL,
            student_name varchar(255) NOT NULL,
            phone varchar(255) NOT NULL,
            course_name varchar(255) NOT NULL,
            join_date date NOT NULL,
            end_date date NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY certificate_id (certificate_id)
        ) $charset_collate;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    /**
     * Drop the database table.
     *
     * @since    1.0.0
     */
    public static function drop_table() {
        global $wpdb;
        
        $wpdb->query( "DROP TABLE IF EXISTS " . self::get_table_name() );
    }

    /**
     * Get the full table name.
     *
     * @since    1.0.0
     * @return   string    The full table name.
     */
    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'certificates';
    }

    /**
     * Insert a single certificate record.
     *
     * @since    1.0.0
     * @param    array    $certificate    The certificate data.
     * @return   mixed    The row ID on success, false on failure.
     */
    public static function insert_certificate( $certificate ) {
        global $wpdb;
        
        // Sanitize data
        $data = array(
            'certificate_id' => sanitize_text_field( $certificate['certificate_id'] ),
            'student_name'   => sanitize_text_field( $certificate['student_name'] ),
            'phone'          => sanitize_text_field( $certificate['phone'] ),
            'course_name'    => sanitize_text_field( $certificate['course_name'] ),
            'join_date'      => sanitize_text_field( $certificate['join_date'] ),
            'end_date'       => sanitize_text_field( $certificate['end_date'] )
        );
        
        // Insert data
        $result = $wpdb->insert(
            self::get_table_name(),
            $data,
            array(
                '%s', // certificate_id
                '%s', // student_name
                '%s', // phone
                '%s', // course_name
                '%s', // join_date
                '%s'  // end_date
            )
        );
        
        if ( $result === false ) {
            return false;
        }
        
        return $wpdb->insert_id;
    }

    /**
     * Insert multiple certificate records.
     *
     * @since    1.0.0
     * @param    array    $certificates    Array of certificate data.
     * @return   array    Results of each insert operation.
     */
    public static function insert_certificates( $certificates ) {
        $results = array(
            'success' => 0,
            'failed'  => 0,
            'duplicate' => 0,
            'errors'  => array()
        );
        
        foreach ( $certificates as $certificate ) {
            try {
                $inserted = self::insert_certificate( $certificate );
                if ( $inserted ) {
                    $results['success']++;
                } else {
                    global $wpdb;
                    if ( strpos( $wpdb->last_error, 'Duplicate' ) !== false ) {
                        $results['duplicate']++;
                    } else {
                        $results['failed']++;
                        $results['errors'][] = "Failed to insert certificate ID: {$certificate['certificate_id']} - " . $wpdb->last_error;
                    }
                }
            } catch ( Exception $e ) {
                $results['failed']++;
                $results['errors'][] = "Exception for certificate ID: {$certificate['certificate_id']} - " . $e->getMessage();
            }
        }
        
        return $results;
    }

    /**
     * Get a certificate by its ID.
     *
     * @since    1.0.0
     * @param    string    $certificate_id    The certificate ID.
     * @return   array|null    The certificate data or null if not found.
     */
    public static function get_certificate_by_id( $certificate_id ) {
        global $wpdb;
        
        $table_name = self::get_table_name();
        $safe_id = sanitize_text_field( $certificate_id );
        
        $query = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE certificate_id = %s",
            $safe_id
        );
        
        $result = $wpdb->get_row( $query, ARRAY_A );
        
        return $result;
    }

    /**
     * Get all certificates.
     *
     * @since    1.0.0
     * @param    int       $limit     Limit of records to fetch.
     * @param    int       $offset    Offset for pagination.
     * @return   array     Array of certificates.
     */
    public static function get_all_certificates( $limit = 50, $offset = 0 ) {
        global $wpdb;
        
        $table_name = self::get_table_name();
        
        $query = $wpdb->prepare(
            "SELECT * FROM $table_name ORDER BY id DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        );
        
        $results = $wpdb->get_results( $query, ARRAY_A );
        
        return $results;
    }

    /**
     * Count all certificates.
     *
     * @since    1.0.0
     * @return   int    The total number of certificates.
     */
    public static function count_certificates() {
        global $wpdb;
        
        $table_name = self::get_table_name();
        
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
        
        return intval( $count );
    }

    /**
     * Delete all certificates.
     *
     * @since    1.0.0
     * @return   int|false    The number of rows deleted, or false on error.
     */
    public static function delete_all_certificates() {
        global $wpdb;
        
        $table_name = self::get_table_name();
        
        $result = $wpdb->query( "DELETE FROM $table_name" );
        
        return $result;
    }
}
