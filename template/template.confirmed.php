<?php

// Instatniate class
$formHandler = new FormHandler();

// Check if the confirmation action has been made
$isConfirmed = isset($_GET['confirm']) && $_GET['confirm'] === 'true';

// If the confirmation is made, call the method to handle data process
if ($isConfirmed) {
    // Call confirm function using the retrieved quote ID
    if (!empty($quote_id)) {
        $formHandler->confirm($quote_id);
    }
}

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>E-mail a été envoyé!</h1>
            <br>
            <p>Votre devis a été envoyé par e-mail avec succès</p>
            <br>

            <!-- navigate back to the home-page, remove all URL params -->
            <a href="<?php echo esc_url(remove_query_arg(array('update', 'form_error', 'confirm'), wp_get_referer())); ?>" class="btn btn-primary">OK</a>

        </div>
    </div>
</div>