<?php

$isSuccess = false; // Initialize to false by default

// Retrieve data from wordpress transient which is used to store data for a limited time to pass it
$form_data_transient = get_transient('form_data_transient');
// $quote_id_transient = get_transient('quote_id_transient');

// Retrieve errors stored in transient and add it to $form_errors variable
if (isset($_GET['form_error']) && $_GET['form_error'] === 'form') {
    if ($form_data_transient && isset($form_data_transient['form_errors'])) {
        $form_errors = $form_data_transient['form_errors'];
    }
}

// Populate form fields with transient data if available
if ($form_data_transient && isset($form_data_transient['form_data'])) {
    $form_data = $form_data_transient['form_data'];
}

// Populate form fields with transient data if available
if ($form_data_transient && isset($form_data_transient['isSuccess'])) {
    $isSuccess = $form_data_transient['isSuccess'];
}

// var_dump($quote_id_transient);
// die();

// Retrieve quote ID from transient if available
// if ($quote_id_transient && isset($quote_id_transient)) {
//     $quote_id = $quote_id_transient;
    // $quote_data = getQuoteDataById($quote_id); // load sql method into variable to recover database data in quotation
    // $isUpdated = isset($_GET['update']) && trim($_GET['update']) === trim($quote_id);
// }

$isUpdated = isset($_GET['update']) && $_GET['update'] === 'true'; // If the confirm URL parameter is set and true, it shows the confirmed template.

$isConfirmed = isset($_GET['confirm']) && $_GET['confirm'] === 'true'; // If the confirm URL parameter is set and true, it shows the confirmed template.

$isCanceled = isset($_GET['cancel']) && $_GET['cancel'] === 'true'; // If the cancel URL parameter is set and true, it shows the canceled template.

if (!$isCanceled) {
    if (!$isConfirmed) {
        if (!$isUpdated) { // Condition for the if the update action is set
            if (!$isSuccess) {
                include_once(plugin_dir_path(__FILE__) . 'template.form.php');
            } else {
                include_once(plugin_dir_path(__FILE__) . 'template.confirm.php');
            }
        } else {
            include_once(plugin_dir_path(__FILE__) . 'template.update.php');
        }
    } else {
        include_once(plugin_dir_path(__FILE__) . 'template.confirmed.php');
    }
} else {
    include_once(plugin_dir_path(__FILE__) . 'template.canceled.php');
}
