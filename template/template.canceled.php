<?php
// Instatniate class
$formHandler = new FormHandler;
$formHandler->eraseMemory();

?>

<div id="scroll_here"></div>

<div class="container-fluid py-3">
    <header class="text-center">
        <div class="d-flex align-items-center justify-content-center">
            <span class="fs-2 p-3 text-body-emphasis" style="color: white !important;">Devis annulé!</span>
        </div>
    </header>
    <main>
        <!-- Message -->
        <p class="fs-6 text-center mb-3 text-body-emphasis" style="color: white !important;">Vos modifications ont été annulées et aucun e-mail n'a été envoyé.</p>
    </main>

    <footer class="pt-3">
        <div class="row justify-content-center d-flex align-items-center">

            <!-- navigate back to the home-page, remove all URL params -->
            <div class="p-2 col-md-auto">
                <a href="<?php echo esc_url(remove_query_arg(array('cancel'), wp_get_referer())); ?>" class="btn btn-success">Retour à la page d'accueil</a>
            </div>

        </div>
    </footer>
</div>