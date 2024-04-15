<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Votre session a expiré.</h1>

            <p>Retour au formulaire.</p>

            <a href="<?php echo esc_url(remove_query_arg(array('update', 'form_error', 'cancel', 'confirm'), home_url("/"))); ?>" class="btn btn-primary">OK</a>

        </div>
    </div>
</div>