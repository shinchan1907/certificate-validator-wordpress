<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://bytenex.io
 * @since             1.0.0
 * @package           Certificate_Verifier
 *
 * @wordpress-plugin
 * Plugin Name:       Certificate Verifier
 * Plugin URI:        https://bytenex.io/certificate-verifier
 * Description:       A WordPress plugin that allows administrators to upload certificate data and enables users to verify certificates through a shortcode-based form.
 * Version:           1.0.0
 * Author:            Sunny Gupta
 * Author URI:        https://bytenex.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       certificate-verifier
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 */
define( 'CERTIFICATE_VERIFIER_VERSION', '1.0.0' );
define( 'CERTIFICATE_VERIFIER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CERTIFICATE_VERIFIER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_certificate_verifier() {
    require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-activator.php';
    Certificate_Verifier_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_certificate_verifier() {
    require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-deactivator.php';
    Certificate_Verifier_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_certificate_verifier' );
register_deactivation_hook( __FILE__, 'deactivate_certificate_verifier' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_certificate_verifier() {
    $plugin = new Certificate_Verifier();
    $plugin->run();
}

run_certificate_verifier();
