<?php

/**
 * Class in charge of creating a plugin for MicroZoo CRM
 */

/**
 * Plugin Name:       Micro CRM
 * Plugin URI:        https://microzoo.fr
 * Description:       A minimalized CRM application plugin for WordPress. 
 * Version:           0.1.0
 * Author:            Peter Szots
 * Author URI:        https://github.com/PetsS
 * Text Domain:       micro-crm
 */


require_once(plugin_dir_path(__FILE__) . 'vendor/tcpdf/tcpdf.php'); // Include the TCPDF library
require_once(plugin_dir_path(__FILE__) . 'vendor/PHPMailer/src/Exception.php');
require_once(plugin_dir_path(__FILE__) . 'vendor/PHPMailer/src/PHPMailer.php');
require_once(plugin_dir_path(__FILE__) . 'vendor/PHPMailer/src/SMTP.php'); // Include the PHPMailer libraries
require_once(plugin_dir_path(__FILE__) . 'classes/class.form-handler.php');
require_once(plugin_dir_path(__FILE__) . 'classes/class.mail-sender.php');
require_once(plugin_dir_path(__FILE__) . 'classes/class.document-converter.php');
require_once(plugin_dir_path(__FILE__) . 'classes/class.quotation-calculator.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.quote.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.person.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.age.php');

// As a security precaution, it’s a good practice to disallow access if the ABSPATH global is not defined.
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}


class MicroCrm
{

	public function __construct()
	{
		// Create tables when activating the plugin
		register_activation_hook(__FILE__, array($this, 'create_tables'));

		// Drop tables when deactivating the plugin
		register_deactivation_hook(__FILE__, array($this, 'drop_tables'));

		// create custom post type hook
		add_action('init', array($this, 'create_custom_post_type'));

		// hook action for PDF conversion
		add_action('init', array($this, 'document_conversion'));

		// add assets (js, css, etc)
		add_action('wp_enqueue_scripts', array($this, 'load_assets'));

		// add shortcode which is called 'micro-crm'
		add_shortcode('micro-crm', array($this, 'load_shortcode_plugin'));

		// hook actions, which WordPress will call when processing form submissions for logged-in and non-logged-in users, respectively.
		add_action('admin_post_form_submission', array($this, 'form_submission'));
		add_action('admin_post_nopriv_form_submission', array($this, 'form_submission')); // For non-logged-in users

	}

	// creating a custom post type using register_post_type() function
	public function create_custom_post_type()
	{
		$labels = array(
			'name' => 'Micro CRM',
			'singular_name' => 'ContactForm Entry'
		);

		$args = array(
			'labels' => $labels,
			'public' => true,
			'has-archive' => true,
			'supports' => array('title'),
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'capability' => 'manage_options',
			'menu_icon' => 'dashicons-media-text',
		);

		register_post_type('micro_crm', $args);
	}

	public function load_assets()
	{
		// load css file, general style
		wp_enqueue_style('general-style', plugin_dir_url(__FILE__) . 'src/css/style.css', array(), 1, 'all');

		// load css file, email style
		wp_enqueue_style('email-style', plugin_dir_url(__FILE__) . 'src/css/email_style.css', array(), 1, 'all');

		// load css file, email style
		wp_enqueue_style('pdf-style', plugin_dir_url(__FILE__) . 'src/css/pdf_style.css', array(), 1, 'all');

		// load Font Awesome cdn
		wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');

		// load Bootstrap css cdn
		wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3');

		// load Bootstrap js cdn
		wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.3', true);

		// load Bootstrap icons cdn
		wp_enqueue_style('bootstrap-icons-css', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');

		// load the js file
		wp_enqueue_script('script', plugin_dir_url(__FILE__) . 'src/js/form-handling-scripts.js', array('jquery'), 1, true);

		// Localize the script with the AJAX URL
		wp_localize_script('script', 'ajax_object', array('ajaxurl' => admin_url('admin-ajax.php')));
	}

	// shortcode callback function
	public function load_shortcode_plugin()
	{
		ob_start(); // Start ob (output buffering) to capture HTML output

		// Include the template file
		include(plugin_dir_path(__FILE__) . 'template/template.main.php');

		// Get the captured HTML output, clean it and return it
		return ob_get_clean();
	}

	public function form_submission()
	{
		// Instantiate the FormHandler class
		$form_handler = new FormHandler();
		$form_handler->handle_form_submission();
	}

	public function document_conversion()
	{
		$document_converter = new DocumentConverter;
		// $document_converter->convert_html_to_pdf();
		$document_converter->convert_pdf_save_redirect();
	}

	// Function to create tables
	function create_tables()
	{
		// Declare global $wpdb object and table names
		global $wpdb;
		$quote_table = $wpdb->prefix . 'quote';
		$person_table = $wpdb->prefix . 'person';
		$age_table = $wpdb->prefix . 'age';

		// Create quote table
		if ($wpdb->get_var("SHOW TABLES LIKE '$quote_table'") != $quote_table) {
			$sql = "
            CREATE TABLE IF NOT EXISTS $quote_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email_quot VARCHAR(255),
                lastname_quot VARCHAR(255),
                firstname_quot VARCHAR(255),
				companyName VARCHAR(255),
				address VARCHAR(255),
				phone_quot VARCHAR(255),
				visitType INT,
				datetimeVisit DATETIME,
				payment INT,
				comment TEXT,
				number_quote VARCHAR(255)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			// Function dbDelta is in the file upgrade.php
			dbDelta($sql);
		}

		// Create age table
		if ($wpdb->get_var("SHOW TABLES LIKE '$age_table'") != $age_table) {
			$sql = "
            CREATE TABLE IF NOT EXISTS $age_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category VARCHAR(255),
                price FLOAT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			// Function dbDelta is in the file upgrade.php
			dbDelta($sql);

			// Insert fixed data into age table category column
			insertAgeData($wpdb, $age_table);
		}


		// Create person table
		if ($wpdb->get_var("SHOW TABLES LIKE '$person_table'") != $person_table) {
			$sql = "
            CREATE TABLE IF NOT EXISTS $person_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
				quote_id INT,
				age_id INT,
                nbPersons INT,
				FOREIGN KEY (quote_id) REFERENCES $quote_table(id) ON DELETE CASCADE ON UPDATE CASCADE,
				FOREIGN KEY (age_id) REFERENCES $age_table(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			// Function dbDelta is in the file upgrade.php
			dbDelta($sql);
		}
	}

	// Function to drop tables
	function drop_tables()
	{
		// Declare global $wpdb object and table names
		global $wpdb;
		$person_table = $wpdb->prefix . 'person';
		$age_table = $wpdb->prefix . 'age';
		$quote_table = $wpdb->prefix . 'quote';

		// Check if plugin is being completely uninstalled
		if (defined('WP_UNINSTALL_PLUGIN') && WP_UNINSTALL_PLUGIN == plugin_basename(__FILE__)) {
			// If plugin is being completely uninstalled, drop tables
			if ($wpdb->get_var("SHOW TABLES LIKE '$person_table'") == $person_table) {
				$wpdb->query("DROP TABLE IF EXISTS $person_table");
			}
			if ($wpdb->get_var("SHOW TABLES LIKE '$age_table'") == $age_table) {
				$wpdb->query("DROP TABLE IF EXISTS $age_table");
			}
			if ($wpdb->get_var("SHOW TABLES LIKE '$quote_table'") == $quote_table) {
				$wpdb->query("DROP TABLE IF EXISTS $quote_table");
			}
		} else {
			// If plugin is just being deactivated, do not drop tables
			// TODO add some logging or notification here
		}
	}
}

new MicroCrm;
