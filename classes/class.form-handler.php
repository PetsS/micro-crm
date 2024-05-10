<?php

/**
 * This Class is handling data processing and submission of the forms.
 * The handle_form_submission() method checks if the form has been submitted and if the nonce field has been set and verified using wp_verify_nonce() to prevent CSRF attacks.
 * It then sanitizes and validates each input field using appropriate WordPress functions to ensure data integrity and security.
 */

class FormHandler
{
    public function handle_form_submission()
    {
        // Define $errors to store error messages
        $errors = array();

        // Verify nonce
        if (isset($_POST['form_nonce']) && wp_verify_nonce($_POST['form_nonce'], 'form_submit')) {

            if (isset($_POST['submit-btn-question'])) {

                // Sanitize all input
                $email_quest = sanitize_email($_POST['email_quest']);
                $lastname_quest = sanitize_text_field($_POST['lastname_quest']);
                $firstname_quest = sanitize_text_field($_POST['firstname_quest']);
                $phone_quest = sanitize_text_field($_POST['phone_quest']);
                $message = sanitize_textarea_field($_POST['message']);

                // Validate all input
                // After sanitizing the input, it checks if the field is not empty and if it's a valid email address.
                if (!filter_var($email_quest, FILTER_VALIDATE_EMAIL)) {
                    $errors['email_quest'] = 'Veuillez saisir une adresse e-mail valide (email@example.fr).';
                }
                // Last name validation
                if (empty($lastname_quest)) {
                    $errors['lastname_quest'] = 'Veuillez saisir votre nom de famille.';
                } else if (!preg_match("/^[a-zA-Z-'À-ÿ ]*$/u", $lastname_quest)) { // check if name only contains letters and whitespace including French accents
                    $errors['lastname_quest'] = 'Seules les lettres et les espaces blancs sont autorisés.';
                }
                // First name validation
                if (empty($firstname_quest)) {
                    $errors['firstname_quest'] = 'Veuillez saisir votre prénom.';
                } else if (!preg_match("/^[a-zA-Z-'À-ÿ ]*$/u", $firstname_quest)) { // check if name only contains letters and whitespace including French accents
                    $errors['firstname_quest'] = 'Seules les lettres et les espaces blancs sont autorisés.';
                }
                // Phone validation
                if (!empty($phone_quest) && !preg_match("/^[\d+\-\s]+$/", $phone_quest)) { // This regex pattern allows for digits, plus symbols, dashes, and single whitespaces between characters.
                    $errors['phone_quest'] = 'Veuillez saisir un numéro de téléphone valide.';
                }
                // Message validation
                if (empty($message)) {
                    $errors['message'] = 'Veuillez saisir votre message.';
                }

                // If there are no errors, process the form data
                if (empty($errors)) {

                    // successful submit send an email to client
                    $mailSender = new MailSender();
                    $mailSender->send_email_question_to_admin($_POST); // Pass post form data to the method and send email to admin 
                    $mailSender->send_email_question_to_client($_POST); // Pass post form data to the method and send email to client

                    // Redirect to the referer page with a parameter
                    wp_redirect(remove_query_arg('form_error', add_query_arg('confirm', 'true', wp_get_referer())));

                    $this->eraseMemory();

                    exit;
                } else {

                    // Store errors and form data in the transient
                    $data_to_store = array(
                        'form_errors' => $errors, // Return errors array and load it into transient
                        'form_data' => $_POST  // Store all form data for repopulation the form
                    );
                    set_transient('form_data_transient', $data_to_store, 600); // Store data for 600 seconds

                    // Redirect back to the referer page to display errors
                    wp_redirect(esc_url(add_query_arg(array('form_error' => 'form'), wp_get_referer())));

                    exit; // exit is to prevent further execution of the admin-post.php
                }
            } else if (isset($_POST['submit-btn-quotation'])) {

                // Sanitize inputs and store them in an array for later use
                $email_quot = sanitize_email($_POST['email_quot']);
                $lastname_quot = sanitize_text_field(ucwords($_POST['lastname_quot']));
                $firstname_quot = sanitize_text_field(ucwords($_POST['firstname_quot']));
                $companyName = sanitize_text_field(stripslashes($_POST['companyName']));
                $address = sanitize_text_field($_POST['address']);
                $phone_quot = sanitize_text_field($_POST['phone_quot']);
                $visitetype = sanitize_text_field($_POST['visitetype']);
                $datetimeVisit = sanitize_text_field($_POST['datetimeVisit']);
                $payment = sanitize_text_field($_POST['payment']);
                $comment = sanitize_textarea_field($_POST['comment']);

                // Validate inputs
                // After sanitizing the input, it checks if the field is not empty and if it's a valid email address.
                if (!filter_var($email_quot, FILTER_VALIDATE_EMAIL)) {
                    $errors['email_quot'] = 'Veuillez saisir une adresse e-mail valide.';
                }
                // Last name validation
                if (empty($lastname_quot)) {
                    $errors['lastname_quot'] = 'Veuillez saisir votre nom de famille.';
                } else if (!preg_match("/^[a-zA-Z-'À-ÿ ]*$/u", $lastname_quot)) { // check if name only contains letters and whitespace including French accents
                    $errors['lastname_quot'] = 'Veuillez saisir un nom valide.';
                }
                // First name validation
                if (empty($firstname_quot)) {
                    $errors['firstname_quot'] = 'Veuillez saisir votre prénom.';
                } else if (!preg_match("/^[a-zA-Z-'À-ÿ ]*$/u", $firstname_quot)) { // check if name only contains letters and whitespace including French accents
                    $errors['firstname_quot'] = 'Veuillez saisir un prénom valide.';
                }
                // Company name validation
                if (!empty($companyName) && !preg_match("/^[a-zA-Z0-9\s][^|=]*$/u", $companyName)) { // This pattern allows letters (uppercase and lowercase), numbers, whitespace, and the following basic special characters: -, ', &, ., ,, (, ).
                    $errors['companyName'] = 'Veuillez saisir un raison social valide.';
                }
                // Address validation
                if (empty($address)) {
                    $errors['address'] = 'Veuillez saisir votre adresse.';
                } else if (!preg_match("/^[a-zA-Z0-9-'À-ÿ&.,() \/]*$/u", $address)) {
                    $errors['address'] = 'Adresse invalide.';
                }
                // Phone validation
                if (empty($phone_quot) && !preg_match("/^[\d+\-\s]+$/", $phone_quot)) { // This regex pattern allows for digits, plus symbols, dashes, and single whitespaces between characters.
                    $errors['phone_quot'] = 'Veuillez saisir un numéro de téléphone valide.';
                }
                // Visit Type validation
                if ($visitetype === 'default') {
                    $errors['visitetype'] = 'Veuillez sélectionner un type de visite.';
                } else if ($visitetype === '2' && array_sum($_POST['nbPersons']) > 30) {
                    $errors['visitetype'] = 'Visite guidé jusqu\'à 30 personnes maximum.';
                }
                // Date and time of visit validation
                if ($datetimeVisit < date('Y-m-d\TH:i')) {
                    $errors['datetimeVisit'] = 'La visite doit être dans le futur.';
                }
                // Payment validation
                if ($payment === 'default') {
                    $errors['payment'] = 'Veuillez sélectionner un type de paiement.';
                }
                // Persons validation                
                foreach ($_POST['nbPersons'] as $index => $nbPerson) {
                    if (empty($nbPerson) || $nbPerson < 0) {
                        $errors['nbPersons'][$index] = 'Le nombre de personnes doit être supérieur à zéro.';
                    }
                }
                // Ages validation
                foreach ($_POST['ages'] as $index => $age) {
                    if ($age === 'default') {
                        $errors['ages'][$index] = "Veuillez sélectionner un catégorie d'âge.";
                    }
                }

                // If there are no errors, process the form data
                if (empty($errors)) {

                    // Retrieve the quote_id from the URL parameter for updating/modifying
                    $quote_id = isset($_GET['update']) ? intval($_GET['update']) : 0;

                    // Set the variable true so it can be used in conditional in the main template file
                    $isSuccess = true;

                    // Store data in the transient
                    $data_to_store = array(
                        'form_data' => $_POST, // store data to display and repopulate update form
                        'form_errors' => $errors, // Store errors in transient
                        // 'quote_id' => $quote_id, // store id to pass it as parameter
                        'isSuccess' => $isSuccess // store variable to retreive it on main template page
                    );
                    set_transient('form_data_transient', $data_to_store, 3600); // Store data for 3600 seconds (1h)
                    
                    // If Update has been submitted from the form, redirect back to the confirm page
                    if (isset($_GET['update']) && $_GET['update'] === 'false') {                        
                        wp_redirect(esc_url(remove_query_arg(array('update', 'form_error'), wp_get_referer())));
                    } else if (isset($_GET['update']) && trim($_GET['update']) === trim($quote_id)) { // If the update URL parameter exist and maching with the id, it proceed to the function that updates the corresponding data in db

                        // Update quotation data in the database
                        updateQuoteData($quote_id, $email_quot, $lastname_quot, $firstname_quot, $companyName, $address, $phone_quot, $visitetype, $datetimeVisit, $payment, $comment);

                        // Call method which updates person data in the database
                        $this->updatePersons($quote_id);

                        // call function in class to proceed update
                        $this->handle_update($quote_id);

                    } else {
                        // Redirect to referer page, clear all URL parameters
                        wp_redirect(esc_url(remove_query_arg('form_error', wp_get_referer())));
                    }

                    exit;
                } else {

                    // Retrieve the quote_id from the URL parameter for updating
                    // $quote_id = isset($_GET['update']) ? intval($_GET['update']) : 0;

                    // Store errors and form data in the transient
                    $data_to_store = array(
                        'form_errors' => $errors, // Return errors array and load it into transient
                        'form_data' => $_POST,  // Store all form data for repopulation the form
                        // 'quote_id' => $quote_id, // store id to pass it as parameter
                    );
                    set_transient('form_data_transient', $data_to_store, 3600); // Store data for 3600 seconds (1h)

                    wp_redirect(add_query_arg(array('form_error' => 'form'), wp_get_referer()));

                    exit; // exit is to prevent further execution of the admin-post.php
                }
            }
        } else {
            // Nonce verification failed, handle error
            wp_die('Nonce verification failed', 'Error', array('response' => 403));
        }
    }

    public function handle_confirmation()
    {

        // If the confirm URL parameter is true, proceed to database query
        if (isset($_POST['submit-btn-confirm'])) {

            // Retrieve data from wordpress transient which is used to store data for a limited time to pass it
            $form_data_transient = get_transient('form_data_transient');

            // Recover $_POST data from transient and load it into a $form_data variable
            if ($form_data_transient && isset($form_data_transient['form_data'])) {
                $form_data = $form_data_transient['form_data'];
            }

            // Sanitize POST inputs recovered from transient $form_data before sending to database
            $email_quot = sanitize_email($form_data['email_quot']);
            $lastname_quot = sanitize_text_field(ucwords($form_data['lastname_quot']));
            $firstname_quot = sanitize_text_field(ucwords($form_data['firstname_quot']));
            $companyName = sanitize_text_field(stripslashes($form_data['companyName']));
            $address = sanitize_text_field($form_data['address']);
            $phone_quot = sanitize_text_field($form_data['phone_quot']);
            $visitetype = sanitize_text_field($form_data['visitetype']);
            $datetimeVisit = sanitize_text_field($form_data['datetimeVisit']);
            $payment = sanitize_text_field($form_data['payment']);
            $comment = sanitize_textarea_field($form_data['comment']);

            $nbPersons = $form_data['nbPersons'];
            $ages = $form_data['ages'];

            // Retrieve the quote_id from the URL parameter for updating
            // $quote_id = isset($_GET['quote_id']) ? intval($_GET['quote_id']) : 0;

            // Insert quotation data and capture the ID
            $quote_id = insertQuoteData($email_quot, $lastname_quot, $firstname_quot, $companyName, $address, $phone_quot, $visitetype, $datetimeVisit, $payment, $comment);

            // Write quote id data in the transient again to store the correct data
            set_transient('quote_id_transient', $quote_id, 3600); // Store data for 3600 seconds (1h)

            // Call method which inserts data into the person table
            $this->insertPersons($quote_id, $nbPersons, $ages);

            // Call method confirm
            $this->confirm($quote_id);
        }
    }

    public function insertPersons($quote_id, $nbPersons, $ages)
    {
        if ($quote_id) {
            // Loop through each person
            foreach ($nbPersons as $index => $nbPerson) {
                // Get age ID from selected age category
                $age_id = $ages[$index];
                // Insert person data and add its id(s) into the array
                insertPersonData($quote_id, $age_id, $nbPerson);
            }
        }
    }

    public function handle_update($quote_id)
    {

        // Get the path to the save folder
        $saveFolderPath = plugin_dir_path(__FILE__) . '../src/save/';
        // Get a list of PDF files in folder
        $pdfFiles = glob($saveFolderPath . '*.pdf');

        $documentConverter = new DocumentConverter; // Instantiate converter class

        // determine pdf file name
        $actualPdfFileName = $documentConverter->generatePdfFileName();

        // Check if the save folder is empty or if the actual file name does not exist in the folder
        if (empty($pdfFiles) || !in_array($actualPdfFileName, $pdfFiles)) {
            $documentConverter->convert_html_to_pdf($quote_id); // Call the converting method to create PDF file
        }

        // Redirect back to the admin page in back office
        wp_redirect(esc_url(remove_query_arg(array('update', 'quote_id'), "admin.php?page=micro-crm-admin")));
        
        $this->eraseMemory();

        exit;
    }

    public function updatePersons($quote_id)
    {
        if ($quote_id) {
            // Get existing person IDs associated with the given quote ID
            $existing_persons = getPersonByQuoteId($quote_id);

            // Initialize an array to store existing person IDs
            $existing_person_ids = array();

            // Extract existing person IDs from the database result
            foreach ($existing_persons as $person) {
                $existing_person_ids[] = $person->id;
            }

            // Create an array to store the IDs of persons submitted in the form
            $submitted_person_ids = array();

            // Loop through each person from the form submission
            foreach ($_POST['nbPersons'] as $index => $nbPerson) {
                // Get the age ID from the form data
                $age_id = $_POST['ages'][$index];

                // If there's an existing person ID at the same index, update the record
                if (isset($existing_person_ids[$index])) {
                    $person_id = $existing_person_ids[$index];
                    updatePersonDataByQuoteId($person_id, $quote_id, $age_id, $nbPerson);
                } else {
                    // If there's no existing person ID at the same index, insert a new record
                    insertPersonData($quote_id, $age_id, $nbPerson);
                }

                // Store the ID of the person submitted in the form
                $submitted_person_ids[] = $person_id;
            }

            // Compare the existing person IDs with submitted person IDs to identify removed persons
            $removed_person_ids = array_diff($existing_person_ids, $submitted_person_ids);

            // Loop through the removed person IDs and delete the corresponding rows from the database
            foreach ($removed_person_ids as $person_id) {
                // Delete the record from the database
                deletePersonDataById($person_id);
            }
        }
    }

    public function confirm($quote_id)
    {
                // Get the path to the folder
        $saveFolderPath = plugin_dir_path(__FILE__) . '../src/save/';
        // Get a list of PDF files
        $pdfFiles = glob($saveFolderPath . '*.pdf');

        $documentConverter = new DocumentConverter; // Instantiate converter class

        // determine pdf file name
        // $actualPdfFileName = 'devis_microzoo_' . $quote_data->number_quote . '.pdf';
        $actualPdfFileName = $documentConverter->generatePdfFileName();

        // Check if the save folder is empty or if the actual file name does not exist in the folder
        if (empty($pdfFiles) || !in_array($actualPdfFileName, $pdfFiles)) {
            $documentConverter->convert_html_to_pdf($quote_id); // Call the converting method to create PDF file
        }

        $mailSender = new MailSender(); // instantiate mailer class
        $mailSender->send_email_quote($quote_id, $actualPdfFileName); // call the quote mailer method passing the quote id and file name to send email

        // Redirect back to the same page with a success message
        wp_redirect(esc_url(add_query_arg('confirm', 'true', wp_get_referer())));

        $this->eraseMemory();

        exit;
    }

    public function eraseMemory()
    {
        // Delete form data transient
        delete_transient('form_data_transient');
    }
}
