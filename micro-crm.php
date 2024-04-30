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
require_once(plugin_dir_path(__FILE__) . 'classes/class.tag-handler.php');
require_once(plugin_dir_path(__FILE__) . 'classes/class.mail-sender.php');
require_once(plugin_dir_path(__FILE__) . 'classes/class.document-converter.php');
require_once(plugin_dir_path(__FILE__) . 'classes/class.admin-menu.php');
require_once(plugin_dir_path(__FILE__) . 'classes/class.quote-calculator.php');
require_once(plugin_dir_path(__FILE__) . 'classes/class.document-downloader.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.quote.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.person.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.tag.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.age.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.visitetype.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.payment.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.tagname.php');
require_once(plugin_dir_path(__FILE__) . 'model/model.pdfdocument.php');

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

		// hook action for PDF conversion
		add_action('init', array($this, 'document_conversion'));

		// hook action for handling tag requests
		add_action('init', array($this, 'tag_handler'));
		
		// Enqueue front assets
		add_action('wp_enqueue_scripts', array($this, 'load_front_assets'));
		
		// Add admin menu items
		add_action('admin_menu', array($this, 'handle_admin_menus'));

		// Enqueue assets for all admin pages
		add_action('admin_enqueue_scripts', array($this, 'load_admin_assets'));

		// add shortcode which is called 'micro-crm'
		add_shortcode('micro-crm', array($this, 'load_shortcode_plugin'));

		// hook actions, which WordPress will call when processing form submissions for logged-in and non-logged-in users, respectively.
		add_action('admin_post_form_submission', array($this, 'form_submission'));
		add_action('admin_post_nopriv_form_submission', array($this, 'form_submission')); // For non-logged-in users
	}

	// creating a custom post type using register_post_type() function
	public function handle_admin_menus()
	{
		$admin_menu = new AdminMenu();
		$admin_menu->add_admin_menus();
	}

	// Enqueue CSS stylesheets for all admin pages on the admin side
	public function load_admin_assets()
	{
		// Enqueue general style
		wp_enqueue_style('admin-style', plugin_dir_url(__FILE__) . 'src/css/admin_style.css', array(), 1, 'all');

		// Enqueue js file
		wp_enqueue_script('admin-pages-scripts', plugin_dir_url(__FILE__) . '/src/js/admin-pages-scripts.js', array(), 1, true);
		
		// load Font Awesome cdn
		wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');

		// load Bootstrap css cdn
		wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3');

		// load Bootstrap js cdn
		wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.3', true);

		// load Bootstrap icons cdn
		wp_enqueue_style('bootstrap-icons-css', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');
	}

	// Enqueue CSS qnd JS on the Front End side
	public function load_front_assets()
	{
		// load Font Awesome cdn
		wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), '5.15.4');
		
		// load Bootstrap css cdn
		wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css', array(), '5.3.3');
		
		// load Bootstrap js cdn
		wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '5.3.3', true);
		
		// load Bootstrap icons cdn
		wp_enqueue_style('bootstrap-icons-css', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css');

		// load css file, general style
		wp_enqueue_style('general-style', plugin_dir_url(__FILE__) . 'src/css/style.css', array(), 1, 'all');

		// load css file, email style
		wp_enqueue_style('email-style', plugin_dir_url(__FILE__) . 'src/css/email_style.css', array(), 1, 'all');

		// load css file, email style
		wp_enqueue_style('pdf-style', plugin_dir_url(__FILE__) . 'src/css/pdf_style.css', array(), 1, 'all');
		
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
		$form_handler = new FormHandler();
		$form_handler->handle_form_submission();
	}

	public function document_conversion()
	{
		$document_converter = new DocumentConverter;
		$document_converter->convert_pdf_save_redirect();
	}

	public function tag_handler()
	{
		$tag_handler = new TagHandler();
		$tag_handler->handle_tag_submission();
		$tag_handler->delete_tag();
	}

	// Function to create tables
	function create_tables()
	{
		// Declare global $wpdb object and table names
		global $wpdb;
		$age_table = $wpdb->prefix . 'age';
		$visitetype_table = $wpdb->prefix . 'visitetype';
		$payment_table = $wpdb->prefix . 'payment';
		$tagname_table = $wpdb->prefix . 'tagname';
		$quote_table = $wpdb->prefix . 'quote';
		$person_table = $wpdb->prefix . 'person';
		$tag_table = $wpdb->prefix . 'tag';
		$pdfdocument_table = $wpdb->prefix . 'pdfdocument';

		// Create age table
		if ($wpdb->get_var("SHOW TABLES LIKE '$age_table'") != $age_table) {
			$sql = "
            CREATE TABLE IF NOT EXISTS $age_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
				ref VARCHAR(100),
				ref_disc VARCHAR(100),
                category VARCHAR(255),
                price FLOAT,
				price_disc FLOAT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			// Function dbDelta is in the file upgrade.php
			dbDelta($sql);

			// Insert fixed data into age table category column
			insertAgeData($wpdb, $age_table);
		}

		// Create visitetype table
		if ($wpdb->get_var("SHOW TABLES LIKE '$visitetype_table'") != $visitetype_table) {
			$sql = "
            CREATE TABLE IF NOT EXISTS $visitetype_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255),
				ref VARCHAR(100),
                price FLOAT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			// Function dbDelta is in the file upgrade.php
			dbDelta($sql);

			// Insert fixed data into table
			insertVisiteTypeData($wpdb, $visitetype_table);
		}

		// Create payment table
		if ($wpdb->get_var("SHOW TABLES LIKE '$payment_table'") != $payment_table) {
			$sql = "
            CREATE TABLE IF NOT EXISTS $payment_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
                category VARCHAR(255)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			// Function dbDelta is in the file upgrade.php
			dbDelta($sql);

			// Insert fixed data into table
			insertPaymentData($wpdb, $payment_table);
		}

		// Create tagname table
		if ($wpdb->get_var("SHOW TABLES LIKE '$tagname_table'") != $tagname_table) {
			$sql = "
            CREATE TABLE IF NOT EXISTS $tagname_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
				category VARCHAR(255)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			// Function dbDelta is in the file upgrade.php
			dbDelta($sql);

			// Insert fixed data into table
			insertTagnameData($wpdb, $tagname_table);
		}

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
				visitetype_id INT,
				datetimeVisit DATETIME,
				payment_id INT,
				comment TEXT,
				number_quote VARCHAR(255),
				creation_date DATETIME DEFAULT CURRENT_TIMESTAMP,
				FOREIGN KEY (visitetype_id) REFERENCES $visitetype_table(id) ON DELETE CASCADE ON UPDATE CASCADE,
				FOREIGN KEY (payment_id) REFERENCES $payment_table(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			// Function dbDelta is in the file upgrade.php
			dbDelta($sql);
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

		// Create tag table
		if ($wpdb->get_var("SHOW TABLES LIKE '$tag_table'") != $tag_table) {
			$sql = "
            CREATE TABLE IF NOT EXISTS $tag_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
				quote_id INT,
				tagname_id INT,
				FOREIGN KEY (quote_id) REFERENCES $quote_table(id) ON DELETE CASCADE ON UPDATE CASCADE,
				FOREIGN KEY (tagname_id) REFERENCES $tagname_table(id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			// Function dbDelta is in the file upgrade.php
			dbDelta($sql);
		}

		// Create pdfdocument table
		if ($wpdb->get_var("SHOW TABLES LIKE '$pdfdocument_table'") != $pdfdocument_table) {
			$sql = "
            CREATE TABLE IF NOT EXISTS $pdfdocument_table (
                id INT AUTO_INCREMENT PRIMARY KEY,
				quote_id INT,
				filename VARCHAR (255),
                content MEDIUMBLOB,
				FOREIGN KEY (quote_id) REFERENCES $quote_table(id) ON DELETE CASCADE ON UPDATE CASCADE
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
		$pdfdocument_table = $wpdb->prefix . 'pdfdocument';
		$tag_table = $wpdb->prefix . 'tag';
		$person_table = $wpdb->prefix . 'person';
		$quote_table = $wpdb->prefix . 'quote';
		$payment_table = $wpdb->prefix . 'payment';
		$tagname_table = $wpdb->prefix . 'tagname';
		$visitetype_table = $wpdb->prefix . 'visitetype';
		$age_table = $wpdb->prefix . 'age';

		// Check if plugin is being completely uninstalled
		if (defined('WP_UNINSTALL_PLUGIN') && WP_UNINSTALL_PLUGIN == plugin_basename(__FILE__)) {
			// If plugin is being completely uninstalled, drop tables
			if ($wpdb->get_var("SHOW TABLES LIKE '$pdfdocument_table'") == $pdfdocument_table) {
				$wpdb->query("DROP TABLE IF EXISTS $pdfdocument_table");
			}
			if ($wpdb->get_var("SHOW TABLES LIKE '$tag_table'") == $tag_table) {
				$wpdb->query("DROP TABLE IF EXISTS $tag_table");
			}
			if ($wpdb->get_var("SHOW TABLES LIKE '$person_table'") == $person_table) {
				$wpdb->query("DROP TABLE IF EXISTS $person_table");
			}
			if ($wpdb->get_var("SHOW TABLES LIKE '$quote_table'") == $quote_table) {
				$wpdb->query("DROP TABLE IF EXISTS $quote_table");
			}
			if ($wpdb->get_var("SHOW TABLES LIKE '$payment_table'") == $payment_table) {
				$wpdb->query("DROP TABLE IF EXISTS $payment_table");
			}
			if ($wpdb->get_var("SHOW TABLES LIKE '$tagname_table'") == $tagname_table) {
				$wpdb->query("DROP TABLE IF EXISTS $tagname_table");
			}
			if ($wpdb->get_var("SHOW TABLES LIKE '$visitetype_table'") == $visitetype_table) {
				$wpdb->query("DROP TABLE IF EXISTS $visitetype_table");
			}
			if ($wpdb->get_var("SHOW TABLES LIKE '$age_table'") == $age_table) {
				$wpdb->query("DROP TABLE IF EXISTS $age_table");
			}
		} else {
			// If plugin is just being deactivated, do not drop tables
			// TODO add some logging or notification here
		}
	}
}

new MicroCrm;
