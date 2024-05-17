<?php

// Instatniate class
$formHandler = new FormHandler;
$formHandler->handle_confirmation(); // Call function to execute confirmed mini form submission

?>

<div id="scroll_here"></div>

<div class="container-fluid py-3">
    <header class="text-center">
        <div class="d-flex align-items-center justify-content-center">
            <span class="fs-2 p-3 text-body-emphasis" style="color: white !important;">E-mail a été envoyé!</span>
        </div>
    </header>
    <main>
        <!-- Message -->
        <p class="fs-6 text-center mb-3 text-body-emphasis" style="color: white !important;">Vous allez recevoir un email avec des informations sur MicroZoo.</p>
    </main>

    <footer class="pt-3">
        <div class="row justify-content-center d-flex align-items-center">

            <!-- navigate back to the home-page, remove all URL params -->
            <div class="p-2 col-md-auto">
                <a class="btn btn-warning" href="<?php echo esc_url(remove_query_arg(array('update', 'form_error', 'confirm', 'question'), wp_get_referer())); ?>">OK</a>
            </div>

        </div>
    </footer>
</div>