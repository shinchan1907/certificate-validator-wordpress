<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Certificate_Verifier
 * @subpackage Certificate_Verifier/includes
 */

class Certificate_Verifier {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Certificate_Verifier_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'CERTIFICATE_VERIFIER_VERSION' ) ) {
            $this->version = CERTIFICATE_VERIFIER_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'certificate-verifier';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for database operations
         */
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-db.php';

        /**
         * The class responsible for file processing
         */
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-file-processor.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'admin/class-certificate-verifier-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'public/class-certificate-verifier-public.php';
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Certificate_Verifier_Admin( $this->get_plugin_name(), $this->get_version() );
        
        // Add admin menu
        add_action( 'admin_menu', array( $plugin_admin, 'add_admin_menu' ) );
        
        // Register admin scripts and styles
        add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );
        
        // Handle file upload
        add_action( 'wp_ajax_certificate_verifier_upload', array( $plugin_admin, 'handle_file_upload' ) );
        
        // Download template
        add_action( 'admin_init', array( $plugin_admin, 'download_template' ) );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new Certificate_Verifier_Public( $this->get_plugin_name(), $this->get_version() );
        
        // Register public scripts and styles
        add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_scripts' ) );
        
        // Register shortcode
        add_shortcode( 'certificate_validation_form', array( $plugin_public, 'display_certificate_form' ) );
        
        // Handle certificate verification AJAX
        add_action( 'wp_ajax_certificate_verifier_validate', array( $plugin_public, 'validate_certificate' ) );
        add_action( 'wp_ajax_nopriv_certificate_verifier_validate', array( $plugin_public, 'validate_certificate' ) );
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Run the plugin.
     *
     * @since    1.0.0
     */
    public function run() {
        // Nothing to run here since we're using WordPress hooks
    }
}
