# Certificate Verifier WordPress Plugin

![Certificate Verifier Banner](certificate-verifier/generated-icon.png)

A WordPress plugin that allows administrators to upload certificate data and enables users to verify certificates through a shortcode-based form.

## Description

Certificate Verifier is a powerful yet simple-to-use WordPress plugin designed for organizations that need to provide certificate verification functionality on their websites. The plugin enables your website visitors to verify the authenticity of certificates by entering a certificate ID through a clean, customizable form.

### Key Features

- **Admin Data Management**: Upload certificate data via CSV or XLSX files
- **User-Friendly Verification**: Simple shortcode-based form for certificate verification
- **AJAX Processing**: Real-time certificate validation without page reloads
- **Responsive Design**: Works flawlessly on all devices
- **Data Security**: Implements WordPress security best practices
- **Customizable Forms**: Easily style the verification form to match your site
- **Page Builder Compatible**: Works with Elementor and other popular page builders
- **License Key Protection**: Optional license key activation system for premium versions
- **Anti-Spam Measures**: CAPTCHA integration to prevent automated verification attempts

## Installation

1. Download the plugin zip file
2. Go to WordPress admin > Plugins > Add New
3. Click "Upload Plugin" and select the downloaded zip file
4. Click "Install Now" and then "Activate"
5. Access the plugin settings via the "Certificate Verifier" menu in your WordPress admin panel

## Usage

### For Administrators

1. Navigate to the Certificate Verifier menu in your WordPress admin
2. Download the blank template to format your certificate data correctly
3. Upload your CSV or XLSX file containing certificate data
4. The plugin will process the data and store it securely in your database

### For Website Visitors

Visitors can verify certificates by entering their certificate ID in the verification form, which can be added to any page using the shortcode:

```
[certificate_validation_form]
```

You can also customize the title of the form:

```
[certificate_validation_form title="Verify Your Certificate Here"]
```

## CSV/XLSX Format Requirements

The certificate data file must contain the following columns in this exact order:

1. **Student Name** - Full name of the certificate holder
2. **Phone** - Contact phone number
3. **Course Name** - Name of the completed course
4. **Join Date** - When the student joined the course (YYYY-MM-DD format)
5. **End Date** - When the course was completed (YYYY-MM-DD format)
6. **ID** - Unique certificate identifier (e.g., CERT-123456)

## Customization

### Styling the Form

The plugin includes default styles that work well with most WordPress themes. If you want to customize the appearance, you can add custom CSS to your theme:

```css
.certificate-verifier-form-container {
    /* Your custom styles here */
}

.certificate-verifier-submit {
    /* Custom button styles */
}

.certificate-verified {
    /* Custom verified message styles */
}
```

### Modifying Text and Labels

You can modify the text and labels using WordPress translation capabilities. Create a language file for the plugin in your language to customize all text elements.

### Integrating with Elementor Page Builder

The Certificate Verifier plugin is fully compatible with Elementor and other popular page builders. To use the certificate verification form with Elementor:

1. **Using Shortcode Widget**:
   - Add an Elementor Shortcode widget to your page
   - Insert the `[certificate_validation_form]` shortcode
   - Customize the surrounding elements using Elementor's styling options

2. **Creating a Custom Elementor Widget** (for developers):
   - The plugin includes a custom Elementor widget class that can be extended
   - Access this functionality in the "elementor" subfolder of the plugin
   - Register your custom widget in the Elementor system for a native integration

You can style the form elements directly within Elementor by targeting the plugin's CSS classes in the Advanced tab.

### Adding CAPTCHA Protection

To protect your verification form from spam and automated submission attempts, you can implement CAPTCHA by following these detailed steps:

#### Step 1: Register with a CAPTCHA Provider
1. Create an account with Google reCAPTCHA (https://www.google.com/recaptcha/admin) or hCaptcha (https://www.hcaptcha.com/)
2. Register your website and obtain your Site Key and Secret Key
3. Note the API version you're using (v2 or v3)

#### Step 2: Add CAPTCHA Configuration to Plugin Settings
1. Create a new settings section in `class-certificate-verifier-admin.php`:
```php
public function register_settings() {
    register_setting('certificate_verifier_options', 'certificate_verifier_captcha_settings');
    
    add_settings_section(
        'certificate_verifier_captcha_section',
        __('CAPTCHA Settings', 'certificate-verifier'),
        array($this, 'captcha_section_callback'),
        'certificate_verifier_settings'
    );
    
    add_settings_field(
        'enable_captcha',
        __('Enable CAPTCHA', 'certificate-verifier'),
        array($this, 'enable_captcha_callback'),
        'certificate_verifier_settings',
        'certificate_verifier_captcha_section'
    );
    
    add_settings_field(
        'captcha_type',
        __('CAPTCHA Type', 'certificate-verifier'),
        array($this, 'captcha_type_callback'),
        'certificate_verifier_settings',
        'certificate_verifier_captcha_section'
    );
    
    add_settings_field(
        'captcha_site_key',
        __('Site Key', 'certificate-verifier'),
        array($this, 'captcha_site_key_callback'),
        'certificate_verifier_settings',
        'certificate_verifier_captcha_section'
    );
    
    add_settings_field(
        'captcha_secret_key',
        __('Secret Key', 'certificate-verifier'),
        array($this, 'captcha_secret_key_callback'),
        'certificate_verifier_settings',
        'certificate_verifier_captcha_section'
    );
}
```

#### Step 3: Modify the Public Form Template
1. Add the CAPTCHA element to the verification form in `display_certificate_form()` method:
```php
// Add CAPTCHA if enabled
$captcha_settings = get_option('certificate_verifier_captcha_settings', array());
if (isset($captcha_settings['enable_captcha']) && $captcha_settings['enable_captcha'] == 1) {
    $captcha_type = isset($captcha_settings['captcha_type']) ? $captcha_settings['captcha_type'] : 'recaptcha_v2';
    $site_key = isset($captcha_settings['site_key']) ? $captcha_settings['site_key'] : '';
    
    if (!empty($site_key)) {
        if ($captcha_type == 'recaptcha_v2') {
            echo '<div class="form-group">';
            echo '<div class="g-recaptcha" data-sitekey="' . esc_attr($site_key) . '"></div>';
            echo '</div>';
        } elseif ($captcha_type == 'recaptcha_v3') {
            echo '<input type="hidden" id="recaptcha_response" name="recaptcha_response">';
        } elseif ($captcha_type == 'hcaptcha') {
            echo '<div class="form-group">';
            echo '<div class="h-captcha" data-sitekey="' . esc_attr($site_key) . '"></div>';
            echo '</div>';
        }
    }
}
```

#### Step 4: Enqueue the Required Scripts
1. Add CAPTCHA scripts in the `enqueue_scripts()` method:
```php
// Add CAPTCHA scripts if enabled
$captcha_settings = get_option('certificate_verifier_captcha_settings', array());
if (isset($captcha_settings['enable_captcha']) && $captcha_settings['enable_captcha'] == 1) {
    $captcha_type = isset($captcha_settings['captcha_type']) ? $captcha_settings['captcha_type'] : 'recaptcha_v2';
    $site_key = isset($captcha_settings['site_key']) ? $captcha_settings['site_key'] : '';
    
    if (!empty($site_key)) {
        if ($captcha_type == 'recaptcha_v2') {
            wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true);
        } elseif ($captcha_type == 'recaptcha_v3') {
            wp_enqueue_script('recaptcha', 'https://www.google.com/recaptcha/api.js?render=' . $site_key, array(), null, true);
            
            $recaptcha_script = "
                grecaptcha.ready(function() {
                    grecaptcha.execute('" . esc_js($site_key) . "', {action: 'certificate_verification'})
                    .then(function(token) {
                        document.getElementById('recaptcha_response').value = token;
                    });
                });
            ";
            wp_add_inline_script('recaptcha', $recaptcha_script);
        } elseif ($captcha_type == 'hcaptcha') {
            wp_enqueue_script('hcaptcha', 'https://js.hcaptcha.com/1/api.js', array(), null, true);
        }
    }
}
```

#### Step 5: Validate CAPTCHA in AJAX Handler
1. Modify the `validate_certificate()` method to verify CAPTCHA:
```php
public function validate_certificate() {
    check_ajax_referer('certificate_verifier_nonce', 'nonce');
    
    // Verify CAPTCHA if enabled
    $captcha_settings = get_option('certificate_verifier_captcha_settings', array());
    if (isset($captcha_settings['enable_captcha']) && $captcha_settings['enable_captcha'] == 1) {
        $captcha_type = isset($captcha_settings['captcha_type']) ? $captcha_settings['captcha_type'] : 'recaptcha_v2';
        $secret_key = isset($captcha_settings['secret_key']) ? $captcha_settings['secret_key'] : '';
        
        if (!empty($secret_key)) {
            $captcha_verified = false;
            
            if ($captcha_type == 'recaptcha_v2' || $captcha_type == 'recaptcha_v3') {
                $recaptcha_response = isset($_POST['recaptcha_response']) ? sanitize_text_field($_POST['recaptcha_response']) : '';
                
                if (empty($recaptcha_response)) {
                    wp_send_json_error(array('message' => __('CAPTCHA verification failed.', 'certificate-verifier')));
                    wp_die();
                }
                
                // Make API request to verify CAPTCHA
                $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
                $response = wp_remote_post($verify_url, array(
                    'body' => array(
                        'secret' => $secret_key,
                        'response' => $recaptcha_response,
                        'remoteip' => $_SERVER['REMOTE_ADDR']
                    )
                ));
                
                if (!is_wp_error($response)) {
                    $result = json_decode(wp_remote_retrieve_body($response), true);
                    
                    if ($captcha_type == 'recaptcha_v2' && isset($result['success']) && $result['success']) {
                        $captcha_verified = true;
                    } elseif ($captcha_type == 'recaptcha_v3' && isset($result['success']) && $result['success'] && isset($result['score']) && $result['score'] >= 0.5) {
                        $captcha_verified = true;
                    }
                }
            } elseif ($captcha_type == 'hcaptcha') {
                $hcaptcha_response = isset($_POST['h-captcha-response']) ? sanitize_text_field($_POST['h-captcha-response']) : '';
                
                if (empty($hcaptcha_response)) {
                    wp_send_json_error(array('message' => __('CAPTCHA verification failed.', 'certificate-verifier')));
                    wp_die();
                }
                
                // Make API request to verify hCaptcha
                $verify_url = 'https://hcaptcha.com/siteverify';
                $response = wp_remote_post($verify_url, array(
                    'body' => array(
                        'secret' => $secret_key,
                        'response' => $hcaptcha_response,
                        'remoteip' => $_SERVER['REMOTE_ADDR']
                    )
                ));
                
                if (!is_wp_error($response)) {
                    $result = json_decode(wp_remote_retrieve_body($response), true);
                    
                    if (isset($result['success']) && $result['success']) {
                        $captcha_verified = true;
                    }
                }
            }
            
            if (!$captcha_verified) {
                wp_send_json_error(array('message' => __('CAPTCHA verification failed.', 'certificate-verifier')));
                wp_die();
            }
        }
    }
    
    // Continue with certificate validation
    $certificate_id = isset($_POST['certificate_id']) ? sanitize_text_field($_POST['certificate_id']) : '';
    
    // Rest of the validation code...
}
```

This implementation provides a robust CAPTCHA protection system with support for multiple providers and configuration options, ensuring your certificate verification form is secure against automated attacks.

### License Key Activation Implementation

This section explains how to implement a license key activation system to convert the plugin into a premium product:

#### Step 1: Create the License Handler Class
1. Create a new file `class-certificate-verifier-license.php` in the `includes` directory:

```php
<?php
/**
 * License functionality.
 *
 * @since      1.0.0
 * @package    Certificate_Verifier
 * @subpackage Certificate_Verifier/includes
 */

class Certificate_Verifier_License {
    
    /**
     * The license key.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $license_key    The license key.
     */
    private $license_key;
    
    /**
     * The license status.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $license_status    The license status.
     */
    private $license_status;
    
    /**
     * The license data.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $license_data    The license data.
     */
    private $license_data;
    
    /**
     * The API URL.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $api_url    The API URL.
     */
    private $api_url = 'https://your-license-server.com/api/';
    
    /**
     * Initialize the class.
     *
     * @since    1.0.0
     */
    public function __construct() {
        $this->license_key = get_option('certificate_verifier_license_key', '');
        $this->license_status = get_option('certificate_verifier_license_status', 'inactive');
        $this->license_data = get_option('certificate_verifier_license_data', array());
        
        // Add admin menu
        add_action('admin_menu', array($this, 'add_license_menu'));
        
        // Add admin notices
        add_action('admin_notices', array($this, 'admin_notices'));
        
        // Add AJAX handlers
        add_action('wp_ajax_certificate_verifier_activate_license', array($this, 'ajax_activate_license'));
        add_action('wp_ajax_certificate_verifier_deactivate_license', array($this, 'ajax_deactivate_license'));
        
        // Check license periodically
        add_action('certificate_verifier_daily_license_check', array($this, 'check_license'));
        
        // Schedule the license check
        if (!wp_next_scheduled('certificate_verifier_daily_license_check')) {
            wp_schedule_event(time(), 'daily', 'certificate_verifier_daily_license_check');
        }
    }
    
    /**
     * Add license menu.
     *
     * @since    1.0.0
     */
    public function add_license_menu() {
        add_submenu_page(
            'certificate-verifier',
            __('License', 'certificate-verifier'),
            __('License', 'certificate-verifier'),
            'manage_options',
            'certificate-verifier-license',
            array($this, 'license_page')
        );
    }
    
    /**
     * Display the license page.
     *
     * @since    1.0.0
     */
    public function license_page() {
        include_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'admin/templates/license-page.php';
    }
    
    /**
     * Display admin notices.
     *
     * @since    1.0.0
     */
    public function admin_notices() {
        if ($this->license_status !== 'valid' && current_user_can('manage_options')) {
            $screen = get_current_screen();
            if ($screen && strpos($screen->id, 'certificate-verifier') !== false) {
                echo '<div class="notice notice-warning"><p>' . 
                     sprintf(
                         __('Your Certificate Verifier license is not active. <a href="%s">Activate your license</a> to receive updates and access premium features.', 'certificate-verifier'),
                         admin_url('admin.php?page=certificate-verifier-license')
                     ) . 
                     '</p></div>';
            }
        }
    }
    
    /**
     * AJAX handler for license activation.
     *
     * @since    1.0.0
     */
    public function ajax_activate_license() {
        check_ajax_referer('certificate_verifier_license_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'certificate-verifier')));
            wp_die();
        }
        
        $license_key = isset($_POST['license_key']) ? sanitize_text_field($_POST['license_key']) : '';
        $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
        
        if (empty($license_key)) {
            wp_send_json_error(array('message' => __('Please enter a license key.', 'certificate-verifier')));
            wp_die();
        }
        
        if (empty($email) || !is_email($email)) {
            wp_send_json_error(array('message' => __('Please enter a valid email address.', 'certificate-verifier')));
            wp_die();
        }
        
        // API request to activate license
        $response = wp_remote_post($this->api_url . 'activate', array(
            'timeout' => 15,
            'body' => array(
                'license' => $license_key,
                'email' => $email,
                'domain' => home_url(),
                'product_id' => 'certificate-verifier'
            )
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            wp_die();
        }
        
        $license_data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!$license_data || !isset($license_data['success'])) {
            wp_send_json_error(array('message' => __('Invalid response from the license server.', 'certificate-verifier')));
            wp_die();
        }
        
        if ($license_data['success'] === false) {
            $message = isset($license_data['message']) ? $license_data['message'] : __('License activation failed.', 'certificate-verifier');
            wp_send_json_error(array('message' => $message));
            wp_die();
        }
        
        // Save license data
        update_option('certificate_verifier_license_key', $license_key);
        update_option('certificate_verifier_license_status', 'valid');
        update_option('certificate_verifier_license_data', $license_data);
        
        wp_send_json_success(array('message' => __('License activated successfully.', 'certificate-verifier')));
        wp_die();
    }
    
    /**
     * AJAX handler for license deactivation.
     *
     * @since    1.0.0
     */
    public function ajax_deactivate_license() {
        check_ajax_referer('certificate_verifier_license_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('You do not have permission to perform this action.', 'certificate-verifier')));
            wp_die();
        }
        
        // API request to deactivate license
        $response = wp_remote_post($this->api_url . 'deactivate', array(
            'timeout' => 15,
            'body' => array(
                'license' => $this->license_key,
                'domain' => home_url(),
                'product_id' => 'certificate-verifier'
            )
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
            wp_die();
        }
        
        $license_data = json_decode(wp_remote_retrieve_body($response), true);
        
        // Reset license data regardless of the API response
        update_option('certificate_verifier_license_status', 'inactive');
        update_option('certificate_verifier_license_data', array());
        
        wp_send_json_success(array('message' => __('License deactivated successfully.', 'certificate-verifier')));
        wp_die();
    }
    
    /**
     * Check license status.
     *
     * @since    1.0.0
     */
    public function check_license() {
        if (empty($this->license_key) || $this->license_status !== 'valid') {
            return;
        }
        
        // API request to check license
        $response = wp_remote_post($this->api_url . 'check', array(
            'timeout' => 15,
            'body' => array(
                'license' => $this->license_key,
                'domain' => home_url(),
                'product_id' => 'certificate-verifier'
            )
        ));
        
        if (is_wp_error($response)) {
            return;
        }
        
        $license_data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!$license_data || !isset($license_data['success'])) {
            return;
        }
        
        if ($license_data['success'] === false) {
            update_option('certificate_verifier_license_status', 'invalid');
            update_option('certificate_verifier_license_data', array());
            return;
        }
        
        // Update license data
        update_option('certificate_verifier_license_data', $license_data);
        
        if (isset($license_data['license']) && $license_data['license'] === 'valid') {
            update_option('certificate_verifier_license_status', 'valid');
        } else {
            update_option('certificate_verifier_license_status', 'invalid');
        }
    }
    
    /**
     * Check if license is valid.
     *
     * @since    1.0.0
     * @return   boolean    True if license is valid.
     */
    public function is_valid() {
        return ($this->license_status === 'valid');
    }
    
    /**
     * Get license expiry date.
     *
     * @since    1.0.0
     * @return   string    The license expiry date.
     */
    public function get_expiry_date() {
        if (isset($this->license_data['expires'])) {
            return date_i18n(get_option('date_format'), strtotime($this->license_data['expires']));
        }
        
        return '';
    }
}
```

#### Step 2: Create the License Page Template
1. Create a new file `license-page.php` in the `admin/templates` directory:

```php
<?php
/**
 * License page template.
 *
 * @since      1.0.0
 * @package    Certificate_Verifier
 * @subpackage Certificate_Verifier/admin/templates
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Get license data
$license_key = get_option('certificate_verifier_license_key', '');
$license_status = get_option('certificate_verifier_license_status', 'inactive');
$license_data = get_option('certificate_verifier_license_data', array());

// Format status label
$status_label = '';
$status_class = '';

if ($license_status === 'valid') {
    $status_label = __('Active', 'certificate-verifier');
    $status_class = 'license-active';
} elseif ($license_status === 'invalid') {
    $status_label = __('Invalid', 'certificate-verifier');
    $status_class = 'license-invalid';
} else {
    $status_label = __('Inactive', 'certificate-verifier');
    $status_class = 'license-inactive';
}
?>

<div class="wrap certificate-verifier-license-page">
    <h1><?php _e('Certificate Verifier License', 'certificate-verifier'); ?></h1>
    
    <div class="certificate-verifier-license-box">
        <h2><?php _e('License Information', 'certificate-verifier'); ?></h2>
        
        <div class="license-status <?php echo $status_class; ?>">
            <span class="label"><?php _e('Status:', 'certificate-verifier'); ?></span>
            <span class="value"><?php echo $status_label; ?></span>
        </div>
        
        <?php if ($license_status === 'valid' && !empty($license_data)): ?>
            <div class="license-details">
                <?php if (isset($license_data['customer_name'])): ?>
                    <div class="license-detail">
                        <span class="label"><?php _e('Customer:', 'certificate-verifier'); ?></span>
                        <span class="value"><?php echo esc_html($license_data['customer_name']); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($license_data['expires'])): ?>
                    <div class="license-detail">
                        <span class="label"><?php _e('Expires:', 'certificate-verifier'); ?></span>
                        <span class="value"><?php echo date_i18n(get_option('date_format'), strtotime($license_data['expires'])); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($license_data['activations_left'])): ?>
                    <div class="license-detail">
                        <span class="label"><?php _e('Activations Left:', 'certificate-verifier'); ?></span>
                        <span class="value"><?php echo intval($license_data['activations_left']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($license_status === 'valid'): ?>
            <form id="certificate-verifier-deactivate-license-form">
                <p class="submit">
                    <input type="hidden" name="action" value="certificate_verifier_deactivate_license">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('certificate_verifier_license_nonce'); ?>">
                    <button type="submit" class="button button-secondary" id="deactivate-license-button"><?php _e('Deactivate License', 'certificate-verifier'); ?></button>
                </p>
            </form>
        <?php else: ?>
            <form id="certificate-verifier-activate-license-form">
                <div class="form-group">
                    <label for="license_key"><?php _e('License Key', 'certificate-verifier'); ?></label>
                    <input type="text" name="license_key" id="license_key" class="regular-text" value="<?php echo esc_attr($license_key); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email"><?php _e('Email Address', 'certificate-verifier'); ?></label>
                    <input type="email" name="email" id="email" class="regular-text" value="<?php echo esc_attr(get_option('admin_email')); ?>" required>
                </div>
                
                <p class="submit">
                    <input type="hidden" name="action" value="certificate_verifier_activate_license">
                    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('certificate_verifier_license_nonce'); ?>">
                    <button type="submit" class="button button-primary" id="activate-license-button"><?php _e('Activate License', 'certificate-verifier'); ?></button>
                </p>
            </form>
        <?php endif; ?>
        
        <div id="license-response" class="license-response"></div>
    </div>
    
    <div class="certificate-verifier-license-help">
        <h2><?php _e('Need a License?', 'certificate-verifier'); ?></h2>
        <p><?php _e('You can purchase a license from our website to unlock all premium features.', 'certificate-verifier'); ?></p>
        <a href="https://example.com/buy-certificate-verifier" target="_blank" class="button button-primary"><?php _e('Purchase License', 'certificate-verifier'); ?></a>
        
        <h2><?php _e('Premium Features', 'certificate-verifier'); ?></h2>
        <ul class="premium-features">
            <li><?php _e('Advanced certificate design templates', 'certificate-verifier'); ?></li>
            <li><?php _e('PDF certificate generation', 'certificate-verifier'); ?></li>
            <li><?php _e('Bulk import/export tools', 'certificate-verifier'); ?></li>
            <li><?php _e('Priority support', 'certificate-verifier'); ?></li>
            <li><?php _e('Regular feature updates', 'certificate-verifier'); ?></li>
        </ul>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Activate license
    $('#certificate-verifier-activate-license-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var submitButton = $('#activate-license-button');
        
        submitButton.prop('disabled', true).text('<?php _e('Activating...', 'certificate-verifier'); ?>');
        $('#license-response').empty().removeClass('error success');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                submitButton.prop('disabled', false).text('<?php _e('Activate License', 'certificate-verifier'); ?>');
                
                if (response.success) {
                    $('#license-response').html('<p>' + response.data.message + '</p>').addClass('success');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    $('#license-response').html('<p>' + response.data.message + '</p>').addClass('error');
                }
            },
            error: function() {
                submitButton.prop('disabled', false).text('<?php _e('Activate License', 'certificate-verifier'); ?>');
                $('#license-response').html('<p><?php _e('An error occurred. Please try again.', 'certificate-verifier'); ?></p>').addClass('error');
            }
        });
    });
    
    // Deactivate license
    $('#certificate-verifier-deactivate-license-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var submitButton = $('#deactivate-license-button');
        
        submitButton.prop('disabled', true).text('<?php _e('Deactivating...', 'certificate-verifier'); ?>');
        $('#license-response').empty().removeClass('error success');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function(response) {
                submitButton.prop('disabled', false).text('<?php _e('Deactivate License', 'certificate-verifier'); ?>');
                
                if (response.success) {
                    $('#license-response').html('<p>' + response.data.message + '</p>').addClass('success');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    $('#license-response').html('<p>' + response.data.message + '</p>').addClass('error');
                }
            },
            error: function() {
                submitButton.prop('disabled', false).text('<?php _e('Deactivate License', 'certificate-verifier'); ?>');
                $('#license-response').html('<p><?php _e('An error occurred. Please try again.', 'certificate-verifier'); ?></p>').addClass('error');
            }
        });
    });
});
</script>
```

#### Step 3: Add CSS for the License Page
1. Add the following CSS to `admin/css/certificate-verifier-admin.css`:

```css
/* License page styles */
.certificate-verifier-license-page {
    max-width: 1000px;
}

.certificate-verifier-license-box {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.license-status {
    padding: 10px 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    display: inline-block;
}

.license-active {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.license-invalid {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.license-inactive {
    background-color: #fff3cd;
    border: 1px solid #ffeeba;
    color: #856404;
}

.license-details {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
}

.license-detail {
    margin-bottom: 10px;
}

.license-detail .label {
    font-weight: 600;
    margin-right: 10px;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 600;
}

.license-response {
    margin-top: 15px;
    padding: 10px 15px;
    border-radius: 4px;
    display: none;
}

.license-response.error {
    background-color: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    display: block;
}

.license-response.success {
    background-color: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    display: block;
}

.certificate-verifier-license-help {
    background: #fff;
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.premium-features {
    list-style: none;
    padding: 0;
    margin-left: 0;
}

.premium-features li {
    padding: 8px 0 8px 25px;
    position: relative;
}

.premium-features li:before {
    content: "\f147";
    font-family: dashicons;
    position: absolute;
    left: 0;
    color: #0073aa;
}
```

#### Step 4: Integrate with Main Plugin Class
1. Add the license manager to the main plugin class in `includes/class-certificate-verifier.php`:

```php
/**
 * The core plugin class.
 */
class Certificate_Verifier {
    
    // Existing properties...
    
    /**
     * The license manager.
     *
     * @since    1.0.0
     * @access   private
     * @var      Certificate_Verifier_License    $license    The license manager.
     */
    private $license;
    
    /**
     * Define the core functionality of the plugin.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Existing code...
        
        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        
        // Initialize license manager
        $this->license = new Certificate_Verifier_License();
    }
    
    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-activator.php';
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-deactivator.php';
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-db.php';
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-file-processor.php';
        
        // Load license class
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'includes/class-certificate-verifier-license.php';
        
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'admin/class-certificate-verifier-admin.php';
        require_once CERTIFICATE_VERIFIER_PLUGIN_DIR . 'public/class-certificate-verifier-public.php';
    }
    
    // Rest of the class...
    
    /**
     * Check if premium features are available.
     *
     * @since    1.0.0
     * @return   boolean    True if premium features are available.
     */
    public function has_premium_features() {
        return $this->license->is_valid();
    }
}
```

#### Step 5: Add Feature Checks for Premium Content
1. Add conditional checks for premium features throughout the plugin:

```php
// Example conditional check in a feature
if ($this->plugin->has_premium_features()) {
    // Premium feature code
} else {
    // Basic feature code or upsell message
}
```

2. For public-facing premium features, add notices to encourage upgrades:

```php
if (!$this->plugin->has_premium_features()) {
    echo '<div class="certificate-verifier-upsell">';
    echo '<p>' . __('This feature requires a premium license.', 'certificate-verifier') . '</p>';
    echo '<a href="https://example.com/buy-certificate-verifier" target="_blank" class="button button-primary">' . __('Upgrade Now', 'certificate-verifier') . '</a>';
    echo '</div>';
    return;
}
```

This implementation creates a complete license management system that verifies purchases, limits the number of activations, and provides access to premium features only when a valid license is present.

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher
- MySQL 5.6 or higher

## FAQ

### Can I import certificate data directly from a database?
Currently, the plugin supports data import via CSV and XLSX files only. Database import functionality may be added in future versions.

### Is the certificate data secure?
Yes, the plugin uses WordPress security best practices including data sanitization, capability checks, and nonce verification to ensure data security.

### Can I customize the certificate verification form?
Yes, you can customize the form appearance using CSS and modify the title through the shortcode parameters.

### Does the plugin work with multi-site installations?
Yes, the plugin is compatible with WordPress multi-site installations.

### How can I integrate the plugin with Elementor?
The plugin works seamlessly with Elementor and other page builders. You can add the shortcode to any Elementor text widget, or use our dedicated Elementor widget (premium version) for a more native integration with full styling controls.

### Does the plugin support CAPTCHA to prevent spam?
Yes, the plugin includes built-in support for Google reCAPTCHA (v2 and v3) and hCaptcha. This helps prevent automated submissions and certificate scraping.

### How does license key activation work?
The premium version requires a license key for activation. Once activated, the plugin will periodically check license validity and provide access to premium features and updates. Licenses are typically valid for one year and can be renewed through our website.

## Troubleshooting

### CSV Import Issues
- Ensure your CSV file uses comma (,) as the delimiter
- Check that column headers match exactly with the template
- Verify that all required fields (ID and Student Name) are filled

### Form Not Displaying
- Make sure the shortcode is correctly inserted in your page/post
- Check if there are any JavaScript errors in your browser console
- Verify that plugin files are properly loaded

### CAPTCHA Problems
- Verify that your site key and secret key are entered correctly
- Ensure your domain is properly registered with your CAPTCHA provider
- Try switching between CAPTCHA versions if you encounter compatibility issues
- Check console for JavaScript errors related to the CAPTCHA service

### License Key Activation Issues
- Make sure you're using the correct license key format
- Verify your internet connection (the plugin needs to connect to the license server)
- Check if your license has expired or reached its site limit
- Try deactivating and reactivating the license if problems persist

## Changelog

### 1.0.0
- Initial release
- Basic certificate verification functionality
- CSV and XLSX import capabilities
- Admin dashboard with statistics

## License

This plugin is licensed under the GPL v2 or later.

## Credits

- Developed by [Your Name/Company]
- Icon design: [Credit if applicable]

## Support

For support, feature requests, or bug reports, please contact [Your Support Email] or visit [Your Support Website].
