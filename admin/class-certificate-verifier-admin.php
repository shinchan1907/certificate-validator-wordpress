<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Certificate_Verifier
 * @subpackage Certificate_Verifier/admin
 */

class Certificate_Verifier_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version           The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        $screen = get_current_screen();
        
        // Only load on plugin admin page
        if ( $screen && strpos( $screen->id, 'certificate-verifier' ) !== false ) {
            wp_enqueue_style( $this->plugin_name, CERTIFICATE_VERIFIER_PLUGIN_URL . 'admin/css/certificate-verifier-admin.css', array(), $this->version, 'all' );
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        $screen = get_current_screen();
        
        // Only load on plugin admin page
        if ( $screen && strpos( $screen->id, 'certificate-verifier' ) !== false ) {
            wp_enqueue_script( $this->plugin_name, CERTIFICATE_VERIFIER_PLUGIN_URL . 'admin/js/certificate-verifier-admin.js', array( 'jquery' ), $this->version, false );
            
            // Add the localized variables for AJAX
            wp_localize_script( $this->plugin_name, 'certificate_verifier_admin', array(
                'ajax_url'   => admin_url( 'admin-ajax.php' ),
                'nonce'      => wp_create_nonce( 'certificate_verifier_upload_nonce' ),
                'loading'    => __( 'Processing...', 'certificate-verifier' ),
                'error'      => __( 'Error', 'certificate-verifier' ),
                'success'    => __( 'Success', 'certificate-verifier' )
            ) );
        }
    }

    /**
     * Add menu items to the admin interface.
     *
     * @since    1.0.0
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Certificate Verifier', 'certificate-verifier' ),
            __( 'Certificate Verifier', 'certificate-verifier' ),
            'manage_options',
            'certificate-verifier',
            array( $this, 'display_admin_page' ),
            'dashicons-id',
            100
        );
    }

    /**
     * Display the admin page.
     *
     * @since    1.0.0
     */
    public function display_admin_page() {
        include_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'admin/templates/admin-page.php';
    }

    /**
     * Handle file upload and processing.
     *
     * @since    1.0.0
     */
    public function handle_file_upload() {
        // Check nonce for security
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'certificate_verifier_upload_nonce' ) ) {
            wp_send_json_error( array( 'message' => 'Security check failed.' ) );
            wp_die();
        }
        
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'You do not have permission to perform this action.' ) );
            wp_die();
        }
        
        // Check if file was uploaded
        if ( ! isset( $_FILES['certificate_file'] ) ) {
            wp_send_json_error( array( 'message' => 'No file was uploaded.' ) );
            wp_die();
        }
        
        // Process the file
        $file_processor = new Certificate_Verifier_File_Processor();
        $result = $file_processor::process_file( $_FILES['certificate_file'] );
        
        if ( ! $result['success'] ) {
            wp_send_json_error( array( 'message' => $result['message'] ) );
            wp_die();
        }
        
        // Insert certificates into database
        $certificates = $result['certificates'];
        $db_result = Certificate_Verifier_DB::insert_certificates( $certificates );
        
        $response = array(
            'success'    => $db_result['success'],
            'duplicate'  => $db_result['duplicate'],
            'failed'     => $db_result['failed'],
            'errors'     => $db_result['errors'],
            'file_errors' => $result['errors']
        );
        
        wp_send_json_success( $response );
        wp_die();
    }

    /**
     * Handle template download.
     *
     * @since    1.0.0
     */
    public function download_template() {
        if ( ! isset( $_GET['certificate_template'] ) || $_GET['certificate_template'] !== 'download' ) {
            return;
        }
        
        // Check nonce
        if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'download_template_nonce' ) ) {
            wp_die( 'Security check failed.' );
        }
        
        // Check user capabilities
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'You do not have permission to perform this action.' );
        }
        
        // Create or get template
        $file_processor = new Certificate_Verifier_File_Processor();
        $template_path = $file_processor::create_blank_template( 'csv' );
        
        // Set headers and output file
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="certificate_template.csv"' );
        header( 'Content-Length: ' . filesize( $template_path ) );
        header( 'Pragma: no-cache' );
        
        readfile( $template_path );
        exit;
    }
}
