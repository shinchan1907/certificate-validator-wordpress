<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    Certificate_Verifier
 * @subpackage Certificate_Verifier/public
 */

class Certificate_Verifier_Public {

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
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version           The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        // Only load if shortcode is present on the page
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'certificate_validation_form' ) ) {
            wp_enqueue_style( $this->plugin_name, CERTIFICATE_VERIFIER_PLUGIN_URL . 'public/css/certificate-verifier-public.css', array(), $this->version, 'all' );
        }
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        // Only load if shortcode is present on the page
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'certificate_validation_form' ) ) {
            wp_enqueue_script( $this->plugin_name, CERTIFICATE_VERIFIER_PLUGIN_URL . 'public/js/certificate-verifier-public.js', array( 'jquery' ), $this->version, false );
            
            wp_localize_script( $this->plugin_name, 'certificate_verifier', array(
                'ajax_url'   => admin_url( 'admin-ajax.php' ),
                'nonce'      => wp_create_nonce( 'certificate_verifier_nonce' ),
                'loading'    => __( 'Verifying...', 'certificate-verifier' ),
                'error'      => __( 'Error', 'certificate-verifier' ),
                'not_found'  => __( 'Invalid Certificate ID', 'certificate-verifier' )
            ) );
        }
    }

    /**
     * Display the certificate validation form.
     *
     * @since    1.0.0
     * @param    array    $atts    Shortcode attributes.
     * @return   string    Rendered shortcode output.
     */
    public function display_certificate_form( $atts ) {
        // Process shortcode attributes
        $atts = shortcode_atts( array(
            'title' => __( 'Certificate Verification', 'certificate-verifier' ),
        ), $atts, 'certificate_validation_form' );
        
        ob_start();
        ?>
        <div class="certificate-verifier-form-container">
            <div class="certificate-verifier-form-wrapper">
                <?php if ( ! empty( $atts['title'] ) ) : ?>
                    <h2 class="certificate-verifier-title"><?php echo esc_html( $atts['title'] ); ?></h2>
                <?php endif; ?>
                
                <form id="certificate-verification-form" method="post">
                    <div class="form-group">
                        <label for="certificate_id"><?php _e( 'Enter Certificate ID', 'certificate-verifier' ); ?></label>
                        <input type="text" name="certificate_id" id="certificate_id" placeholder="<?php _e( 'e.g. CERT-123456', 'certificate-verifier' ); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="certificate-verifier-submit"><?php _e( 'Verify Certificate', 'certificate-verifier' ); ?></button>
                    </div>
                    
                    <?php wp_nonce_field( 'certificate_verifier_nonce', 'certificate_nonce' ); ?>
                </form>
                
                <div id="certificate-verification-result"></div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX handler for certificate validation.
     *
     * @since    1.0.0
     */
    public function validate_certificate() {
        check_ajax_referer( 'certificate_verifier_nonce', 'nonce' );
        
        $certificate_id = isset( $_POST['certificate_id'] ) ? sanitize_text_field( $_POST['certificate_id'] ) : '';
        
        if ( empty( $certificate_id ) ) {
            wp_send_json_error( array( 'message' => __( 'Certificate ID is required.', 'certificate-verifier' ) ) );
            wp_die();
        }
        
        // Get certificate details
        $certificate = Certificate_Verifier_DB::get_certificate_by_id( $certificate_id );
        
        if ( ! $certificate ) {
            wp_send_json_error( array( 'message' => __( 'Invalid Certificate ID', 'certificate-verifier' ) ) );
            wp_die();
        }
        
        // Format dates for display
        $join_date = date_i18n( get_option( 'date_format' ), strtotime( $certificate['join_date'] ) );
        $end_date = date_i18n( get_option( 'date_format' ), strtotime( $certificate['end_date'] ) );
        
        $response = array(
            'student_name' => $certificate['student_name'],
            'phone'        => $certificate['phone'],
            'course_name'  => $certificate['course_name'],
            'join_date'    => $join_date,
            'end_date'     => $end_date
        );
        
        wp_send_json_success( $response );
        wp_die();
    }
}
