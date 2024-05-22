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
            'Micro CRM', // Page title
            'Micro CRM', // Menu title
            // 'manage_options', // Capability required to access the page. manage_options is available for administrators
            'read', // The read capability is available to all users, including subscribers.
            'micro-crm-admin', // Menu slug
            array($this, 'micro_crm_followup_page'), // Callback function to display the page
            'dashicons-buddicons-replies', // Menu icon
            25 // Menu position
        );

        // Add a sub-menu page
        add_submenu_page(
            null, // Parent menu slug; if null, it doesnt diplay in the side menu
            'Sub Page', // Page title
            'Sub Page', // Menu title
            // 'manage_options', // Capability required to access the page
            'read', // The read capability is available to all users, including subscribers.
            'micro-crm-modify-quote-page', // Menu slug
            array($this, 'modify_quote_page') // Callback function to display the page
        );

        // Add a sub-menu page
        add_submenu_page(
            null, // Parent menu slug
            'Sub Page', // Page title
            'Sub Page', // Menu title
            // 'manage_options', // Capability required to access the page
            'read', // The read capability is available to all users, including subscribers.
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

    // Callback function for modify quote page
    public function modify_quote_page()
    {
        // Include the template file
        include_once(plugin_dir_path(__FILE__) . '../template/template.modify_quotation.php');
    }

    // Callback function to display a sub-menu page
    public function micro_crm_sub_page()
    {
        echo "<p>Sub page to be displayed...</p>";
        // Display HTML content for Database Commands page
    }

}
