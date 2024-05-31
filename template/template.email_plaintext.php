<?php

// Check if user is logged in
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $user_key = 'user_' . $user_id;
} else {
    // Use session ID for non-logged-in users
    if (!isset($_SESSION['user_key'])) {
        $_SESSION['user_key'] = 'user_' . session_id();
    }
    $user_key = $_SESSION['user_key'];
}

// Retrieve the transient data using the user-specific key
$form_data_transient = get_transient($user_key . '_form_data_transient');

// Retrieve quote ID from transient if available
if ($quote_id_transient && isset($quote_id_transient)) {
    $quote_id = $quote_id_transient;
    $quote_data = getQuoteDataById($quote_id); // load sql method into variable to recover quotation data from database
} else {
    wp_die('no data in transient', 'Error', array('response' => 403));
}

// Generate plain text content
$plainTextContent = "DEVIS N° " . $quote_data->number_quote . "\n\n";
$plainTextContent .= "Bonjour ";
$plainTextContent .= (!empty($quote_data->companyName) ? (strtoupper($quote_data->companyName)) : ($quote_data->firstname_quot . ' ' . strtoupper($quote_data->lastname_quot)));
$plainTextContent .= ",\n\n";
$plainTextContent .= "Nous vous remercions d'avoir choisi MicroZoo pour votre devis. Veuillez trouver ci-joint le devis détaillé en pdf.\n";
$plainTextContent .= "Pour toute question ou clarification, n'hésitez pas à nous contacter.\n\n";
$plainTextContent .= "Cordialement,\nL'équipe MicroZoo";

// Output plain text content
echo $plainTextContent;
