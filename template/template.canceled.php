<?php
// Instatniate class
$formHandler = new FormHandler;
$formHandler->eraseMemory();
                        
?>

<div id="scroll_here"></div>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Annulé!</h1>
            <br>

            <p>Votre devis a été annulé.</p>
            <br>

            <!-- navigate back to the home-page, remove all URL params -->
            <a href="<?php echo esc_url(remove_query_arg(array('cancel'), wp_get_referer())); ?>" class="btn btn-primary">Retour à la page d'accueil</a>

        </div>
    </div>
</div>