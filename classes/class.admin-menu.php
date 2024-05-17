<?php

/**
 * This Class is responsible for the WordPress back-office admin menus and sub-menus
 */

class AdminMenu
{
    public function __construct()
    {
        
    }

    public function add_admin_menus()
    {
        // Add a top-level menu page
        add_menu_page(
            'Micro CRM Suivi', // Page title
            'Micro CRM Suivi', // Menu title
            'manage_options', // Capability required to access the page
            'micro-crm-admin', // Menu slug
            array($this, 'micro_crm_followup_page'), // Callback function to display the page
            'dashicons-admin-generic', // Menu icon
            25 // Menu position
        );

        // Add a sub-menu page
        add_submenu_page(
            null, // Parent menu slug, if null, there will be no menu item
            'Sub Page', // Page title
            'Sub Page', // Menu title
            'manage_options', // Capability required to access the page
            'micro-crm-sub-page', // Menu slug
            array($this, 'micro_crm_sub_page') // Callback function to display the page
        );

    }

    // Callback function to display Micro CRM admin page
    public function micro_crm_followup_page()
    {
        // Include the template file
        include_once(plugin_dir_path(__FILE__) . '../template/template.admin_followup.php');
    }

    // Callback function to display sub-menu page
    public function micro_crm_sub_page()
    {
        echo "<p>Sub page to be displayed...</p>";
        // Display HTML content for sub page
    }

}
