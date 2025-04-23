<?php
/**
 * File processing functionality.
 *
 * @since      1.0.0
 * @package    Certificate_Verifier
 * @subpackage Certificate_Verifier/includes
 */

class Certificate_Verifier_File_Processor {

    /**
     * Process the uploaded file.
     *
     * @since    1.0.0
     * @param    array    $file    The uploaded file information.
     * @return   array    The processed data and any errors.
     */
    public static function process_file( $file ) {
        // Check if the file exists
        if ( ! isset( $file['tmp_name'] ) || empty( $file['tmp_name'] ) ) {
            return array(
                'success' => false,
                'message' => 'No file was uploaded.'
            );
        }
        
        // Check file extension
        $file_extension = pathinfo( $file['name'], PATHINFO_EXTENSION );
        
        if ( $file_extension === 'csv' ) {
            return self::process_csv_file( $file['tmp_name'] );
        } elseif ( $file_extension === 'xlsx' ) {
            return self::process_xlsx_file( $file['tmp_name'] );
        } else {
            return array(
                'success' => false,
                'message' => 'Invalid file format. Only CSV and XLSX files are supported.'
            );
        }
    }

    /**
     * Process a CSV file.
     *
     * @since    1.0.0
     * @param    string    $file_path    The path to the CSV file.
     * @return   array     The processed data and any errors.
     */
    private static function process_csv_file( $file_path ) {
        $certificates = array();
        $errors = array();
        $row = 0;
        
        if ( ( $handle = fopen( $file_path, 'r' ) ) !== false ) {
            while ( ( $data = fgetcsv( $handle, 1000, ',' ) ) !== false ) {
                $row++;
                
                // Skip header row
                if ( $row === 1 ) {
                    // Validate headers
                    $expected_headers = array(
                        'Student Name',
                        'Phone',
                        'Course Name',
                        'Join Date',
                        'End Date',
                        'ID'
                    );
                    
                    $headers_valid = true;
                    foreach ( $expected_headers as $index => $header ) {
                        if ( ! isset( $data[$index] ) || trim( $data[$index] ) !== $header ) {
                            $headers_valid = false;
                            break;
                        }
                    }
                    
                    if ( ! $headers_valid ) {
                        fclose( $handle );
                        return array(
                            'success' => false,
                            'message' => 'Invalid CSV headers. Please download and use the template provided.'
                        );
                    }
                    
                    continue;
                }
                
                // Validate data
                if ( count( $data ) < 6 ) {
                    $errors[] = "Row $row: Not enough columns.";
                    continue;
                }
                
                // Extract and sanitize data
                $certificate = array(
                    'student_name'   => sanitize_text_field( $data[0] ),
                    'phone'          => sanitize_text_field( $data[1] ),
                    'course_name'    => sanitize_text_field( $data[2] ),
                    'join_date'      => self::format_date( sanitize_text_field( $data[3] ) ),
                    'end_date'       => self::format_date( sanitize_text_field( $data[4] ) ),
                    'certificate_id' => sanitize_text_field( $data[5] )
                );
                
                // Validate required fields
                if ( empty( $certificate['certificate_id'] ) ) {
                    $errors[] = "Row $row: Certificate ID is required.";
                    continue;
                }
                
                if ( empty( $certificate['student_name'] ) ) {
                    $errors[] = "Row $row: Student Name is required.";
                    continue;
                }
                
                // Add to certificates array
                $certificates[] = $certificate;
            }
            fclose( $handle );
        } else {
            return array(
                'success' => false,
                'message' => 'Failed to open the file.'
            );
        }
        
        return array(
            'success'      => true,
            'certificates' => $certificates,
            'errors'       => $errors
        );
    }

    /**
     * Process an XLSX file.
     *
     * @since    1.0.0
     * @param    string    $file_path    The path to the XLSX file.
     * @return   array     The processed data and any errors.
     */
    private static function process_xlsx_file( $file_path ) {
        // Check if PhpSpreadsheet is available or try to load it
        if ( ! class_exists( 'PhpOffice\PhpSpreadsheet\IOFactory' ) ) {
            if ( ! file_exists( WP_PLUGIN_DIR . '/vendor/autoload.php' ) ) {
                return array(
                    'success' => false,
                    'message' => 'PhpSpreadsheet library is required to process XLSX files. Please install the library or use CSV files instead.'
                );
            }
            
            require_once WP_PLUGIN_DIR . '/vendor/autoload.php';
        }
        
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $file_path );
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            
            $certificates = array();
            $errors = array();
            
            // Validate headers (first row)
            $expected_headers = array(
                'A1' => 'Student Name',
                'B1' => 'Phone',
                'C1' => 'Course Name',
                'D1' => 'Join Date',
                'E1' => 'End Date',
                'F1' => 'ID'
            );
            
            $headers_valid = true;
            foreach ( $expected_headers as $cell => $expected_value ) {
                $actual_value = $worksheet->getCell( $cell )->getValue();
                if ( $actual_value !== $expected_value ) {
                    $headers_valid = false;
                    break;
                }
            }
            
            if ( ! $headers_valid ) {
                return array(
                    'success' => false,
                    'message' => 'Invalid XLSX headers. Please download and use the template provided.'
                );
            }
            
            // Process data rows
            for ( $row = 2; $row <= $highestRow; $row++ ) {
                $student_name = $worksheet->getCell( 'A' . $row )->getValue();
                $phone = $worksheet->getCell( 'B' . $row )->getValue();
                $course_name = $worksheet->getCell( 'C' . $row )->getValue();
                $join_date = $worksheet->getCell( 'D' . $row )->getValue();
                $end_date = $worksheet->getCell( 'E' . $row )->getValue();
                $certificate_id = $worksheet->getCell( 'F' . $row )->getValue();
                
                // Extract and sanitize data
                $certificate = array(
                    'student_name'   => sanitize_text_field( $student_name ),
                    'phone'          => sanitize_text_field( $phone ),
                    'course_name'    => sanitize_text_field( $course_name ),
                    'join_date'      => self::format_date( sanitize_text_field( $join_date ) ),
                    'end_date'       => self::format_date( sanitize_text_field( $end_date ) ),
                    'certificate_id' => sanitize_text_field( $certificate_id )
                );
                
                // Validate required fields
                if ( empty( $certificate['certificate_id'] ) ) {
                    $errors[] = "Row $row: Certificate ID is required.";
                    continue;
                }
                
                if ( empty( $certificate['student_name'] ) ) {
                    $errors[] = "Row $row: Student Name is required.";
                    continue;
                }
                
                // Add to certificates array
                $certificates[] = $certificate;
            }
            
            return array(
                'success'      => true,
                'certificates' => $certificates,
                'errors'       => $errors
            );
            
        } catch ( Exception $e ) {
            return array(
                'success' => false,
                'message' => 'Error processing XLSX file: ' . $e->getMessage()
            );
        }
    }

    /**
     * Create a blank template file.
     *
     * @since    1.0.0
     * @param    string    $type    The type of template (csv or xlsx).
     * @return   string    The path to the generated template.
     */
    public static function create_blank_template( $type = 'csv' ) {
        $template_dir = CERTIFICATE_VERIFIER_PLUGIN_DIR . 'admin/templates/';
        
        if ( ! file_exists( $template_dir ) ) {
            mkdir( $template_dir, 0755, true );
        }
        
        $template_path = $template_dir . 'blank-template.' . $type;
        
        if ( $type === 'csv' ) {
            $headers = array( 'Student Name', 'Phone', 'Course Name', 'Join Date', 'End Date', 'ID' );
            $fp = fopen( $template_path, 'w' );
            fputcsv( $fp, $headers );
            fputcsv( $fp, array( 
                'John Doe', 
                '1234567890', 
                'Web Development', 
                date( 'Y-m-d', strtotime( '-1 month' ) ), 
                date( 'Y-m-d', strtotime( '+5 months' ) ), 
                'CERT-' . rand( 100000, 999999 ) 
            ) );
            fclose( $fp );
        } elseif ( $type === 'xlsx' ) {
            // XLSX generation would require PhpSpreadsheet
            // Since we're focusing on simplicity, we'll stick with CSV for now
        }
        
        return $template_path;
    }

    /**
     * Format a date string to Y-m-d format.
     *
     * @since    1.0.0
     * @param    string    $date    The date string to format.
     * @return   string    The formatted date.
     */
    private static function format_date( $date ) {
        if ( empty( $date ) ) {
            return '';
        }
        
        $timestamp = strtotime( $date );
        if ( $timestamp === false ) {
            return $date; // Return original if parsing fails
        }
        
        return date( 'Y-m-d', $timestamp );
    }
}
