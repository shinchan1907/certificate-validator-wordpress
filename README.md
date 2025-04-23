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

## Troubleshooting

### CSV Import Issues
- Ensure your CSV file uses comma (,) as the delimiter
- Check that column headers match exactly with the template
- Verify that all required fields (ID and Student Name) are filled

### Form Not Displaying
- Make sure the shortcode is correctly inserted in your page/post
- Check if there are any JavaScript errors in your browser console
- Verify that plugin files are properly loaded

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