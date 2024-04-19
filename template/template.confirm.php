<?php

// Instatniate class
$formHandler = new FormHandler();

// Check if the cancellation action is triggered
// Trim ensures that any leading or trailing whitespace is removed
$isCancelled = isset($_GET['cancel']) && trim($_GET['cancel']) === trim($quote_id);

// If the cancellation is made, call the method to delete data from database
if ($isCancelled) {
    // Call delete function using the retrieved quote ID
    $formHandler->deleteData($quote_id);
}

?>

<div id="scroll_here"></div>

<!-- Condition for the if the cancel action is set -->
<?php if (!$isCancelled) : ?>
    <div class="container" style="max-width: 500px;">
        <div class="row">
            <div class="col-md-12">
                <h1>Révisez votre devis!</h1>

                <!-- Insert the QUOTATION HTML file here -->
                <?php include_once(plugin_dir_path(__FILE__) . 'template.quotation.php'); ?>

                <br>
                <p>Que voulez-vous faire ensuite?</p>

                <!-- Cancellation, delete insert from database and navigate back to the home-page -->
                <a href="<?php echo esc_url(remove_query_arg(array('update', 'form_error'), add_query_arg('cancel', $quote_id ?? null, wp_get_referer()))); ?>" class="btn btn-danger">Annuler</a>

                <!-- Update the form -->
                <a href="<?php echo esc_url(add_query_arg('update', $quote_id ?? null, wp_get_referer())); ?>" class="btn btn-success">Corriger le formulaire</a>

                <!-- redirect back to the confirm page and send email -->
                <a href="<?php echo esc_url(remove_query_arg(array('update', 'form_error'), add_query_arg('confirm', 'true', wp_get_referer()))); ?>" class="btn btn-success">Confirmer et envoyer E-mail</a>

                <!-- <form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
                        <input type="hidden" name="confirm" value="true">
                        <button type="submit" class="btn btn-success">Confirmer et envoyer E-mail</button>
                    </form> -->

                <!-- download PDF -->
                <a href="<?php echo esc_url(remove_query_arg(array('update', 'form_error'), add_query_arg('pdf', 'true', wp_get_referer()))); ?>" target="_blank" class="btn btn-success">Télécharger PDF</a>
                <!-- <a href="#" class="btn btn-primary">Télécharger PDF</a> -->

            </div>
        </div>
    </div>
<?php else : ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>Annulé!</h1>
                <br>

                <p>Votre devis a été annulé.</p>
                <br>

                <!-- navigate back to the home-page -->
                <a href="<?php echo home_url('/'); ?>" class="btn btn-primary">Retour à la page d'accueil</a>
            </div>
        </div>
    </div>
<?php endif; ?>