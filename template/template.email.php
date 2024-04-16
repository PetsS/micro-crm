<?php
// Retrieve data from wordpress transient which is used to store data for a limited time to pass it
$form_data_transient = get_transient('form_data_transient');

// Retrieve quote ID from transient if available
if ($form_data_transient && isset($form_data_transient['quote_id'])) {
    $quote_id = $form_data_transient['quote_id'];
    $quote_data = getQuoteDataById($quote_id); // load sql method into variable to recover quotation data from database
}

// Read CSS file content and inline it because PHPMailer does not directly handle external CSS styling for email templates.
$css_content = file_get_contents(plugin_dir_url(__FILE__) . '../src/css/email_style.css');
?>

<!-- Include inline custom css library -->
<style>
    <?php echo $css_content; ?>
</style>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEVIS N° <?php echo $quote_data->number_quote; ?></title>
</head>

<body>
    <div class="container_email">

        <!-- Header -->
        <header class="email_header">
            <img src="https://www.microzoo.com/wp-content/uploads/2022/01/logo-light.svg" alt="MicroZoo Logo" style="max-width: 200px;">
        </header>

        <!-- Email Body -->
        <h1>DEVIS N° <?php echo $quote_data->number_quote; ?></h1>
        <p>Bonjour <?php echo (!empty($quote_data->companyName) ? (strtoupper($quote_data->companyName)) : ($quote_data->firstname_quot . ' ' . strtoupper($quote_data->lastname_quot))); ?>,
        </p>
        <br>
        <p>Nous vous remercions d'avoir choisi MicroZoo pour votre devis. Veuillez trouver ci-joint le devis détaillé en pdf.</p>
        <p>Pour toute question ou clarification, n'hésitez pas à nous contacter.</p>
        <br>
        <p>Cordialement,<br>L'équipe MicroZoo</p>

        <!-- Email Footer -->
        <footer class="email_footer">
            <p>Micro Zoo Saint-Malo</p>
            <p>9 Place Vauban, 35400 Saint-Malo</p>
            <p>Téléphone: +33 6 22 91 83 04 | Email: contact@microzoo.fr​</p>
            <div>
                <a class="link_facebook" href="https://www.facebook.com/microzoosaintmalo">Facebook</a>
                <a class="link_instagram" href="https://www.instagram.com/microzoosaintmalo">Instagram</a>
            </div>
        </footer>
    </div>
</body>

</html>