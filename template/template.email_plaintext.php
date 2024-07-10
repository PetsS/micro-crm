<?php
// Retrieve data from wordpress transient which is used to store data for a limited time to pass it
$form_data_transient = get_transient('form_data_transient');

// Retrieve quote ID from transient if available
if ($quote_id_transient && isset($quote_id_transient)) {
    $quote_id = $quote_id_transient;
    $quote_data = getQuoteDataById($quote_id); // load sql method into variable to recover quotation data from database
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
