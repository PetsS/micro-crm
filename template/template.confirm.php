<?php

?>

<div id="scroll_here"></div>

<div class="container" style="max-width: 500px;">
    <div class="row">
        <div class="col-md-12">
            <h1>Révisez votre devis!</h1>

            <!-- Insert the QUOTATION HTML file here -->
            <?php include_once(plugin_dir_path(__FILE__) . 'template.quotation.php'); ?>

            <br>
            <p>Que voulez-vous faire ensuite?</p>

            <!-- Cancellation, erase memory and navigate back to the home-page -->
            <a href="<?php echo esc_url(remove_query_arg(array('update', 'form_error'), add_query_arg('cancel', 'true', wp_get_referer()))); ?>" class="btn btn-danger">Annuler</a>

            <!-- Update the form -->
            <a href="<?php echo esc_url(add_query_arg('update', 'true', wp_get_referer())); ?>" class="btn btn-success">Corriger le formulaire</a>

            <!-- Confirm quote by submiting miniform -->
            <form method="post" action="<?php echo esc_url(remove_query_arg(array('update', 'form_error'), add_query_arg('confirm', 'true', wp_get_referer()))); ?>">
                <!-- <input type="hidden" name="quote_id" value="<?php echo esc_attr($quote_id); ?>"> -->
                <button type="submit" class="btn btn-success" name="submit-btn-confirm">Confirmer et envoyer E-mail</button>
            </form>

        </div>
    </div>
</div>