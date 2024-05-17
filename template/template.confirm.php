<?php

?>

<div id="scroll_here"></div>

<!-- Insert the QUOTATION review HTML file here -->
<?php include_once(plugin_dir_path(__FILE__) . 'template.review_quotation.php'); ?>

<footer class="border-top">
    <div class="container-fluid">
        <div class="mt-3 row justify-content-center">

            <!-- Cancellation, erase memory and navigate back to the home-page -->
            <div class="p-2 col-md-auto">
                <a class="btn btn-danger" href="<?php echo esc_url(remove_query_arg(array('update', 'form_error'), add_query_arg('cancel', 'true', wp_get_referer()))); ?>">Annuler</a>
            </div>

            <!-- Update the form -->
            <div class="p-2 col-md-auto">
                <a class="btn btn-secondary" href="<?php echo esc_url(add_query_arg('update', 'true', wp_get_referer())); ?>">Corriger le formulaire</a>
            </div>

            <!-- Confirm quote by submitting mini-form -->
            <div class="p-2 col-md-auto">
                <form id="confirmForm" method="post" action="<?php echo esc_url(remove_query_arg(array('update', 'form_error'), add_query_arg('confirm', 'true', wp_get_referer()))); ?>">
                    <button id="submit-btn-confirm" class="btn btn-success" type="submit" name="submit-btn-confirm">
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        Confirmer
                    </button>
                </form>
                <script>
                    // this script adds a spinner while the page is loading after submission
                    document.addEventListener('DOMContentLoaded', function() {
                        var button = document.getElementById('submit-btn-confirm');
                        button.addEventListener('click', function() {
                            var spinner = button.querySelector('.spinner-border');
                            spinner.classList.remove('d-none'); // Show spinner
                            button.classList.add('disabled'); // Add disabled class to visually disable the button
                        });
                    });
                </script>
            </div>



        </div>
    </div>
</footer>