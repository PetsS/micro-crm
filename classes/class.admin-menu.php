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
            'micro-crm-admin', // Parent menu slug
            'Database Commands', // Page title
            'Database Commands', // Menu title
            'manage_options', // Capability required to access the page
            'micro-crm-database-commands', // Menu slug
            array($this, 'micro_crm_database_commands_page') // Callback function to display the page
        );

    }

    // Callback function to display Micro CRM admin page
    public function micro_crm_followup_page()
    {
        // Include the template file
        include_once(plugin_dir_path(__FILE__) . '../template/template.admin_followup.php');
    }

    // Callback function to display Database Commands page
    public function micro_crm_database_commands_page()
    {
        // Display HTML content for Database Commands page
    }

}