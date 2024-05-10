<?php
// Retrieve data from wordpress transient which is used to store data for a limited time to pass it

$quote_id_transient = get_transient('quote_id_transient');

// Retrieve quote ID from transient if available
if ($quote_id_transient && isset($quote_id_transient)) {
    $quote_id = $quote_id_transient;
    $quote_data = getQuoteDataById($quote_id); // load sql method into variable to recover quotation data from database
}

// var_dump($quote_id);
// var_dump($quote_data);
// die();

// Read CSS file content and inline it because PHPMailer does not directly handle external CSS styling for email templates.
$css_content = file_get_contents(plugin_dir_url(__FILE__) . '../src/css/email_style.css');

// include logo from src
$logo_header = file_get_contents(plugin_dir_url(__FILE__) . '../src/images/logo_banner.png');

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
    <!-- Header -->
    <header class="email_header">
        <img src="data:image/png;base64,<?php echo base64_encode($logo_header); ?>" alt="MicroZoo Logo">
    </header>

    <div class="container_email">

        <!-- Email Body -->
        <h1>DEVIS N° <?php echo $quote_data->number_quote; ?></h1>
        <p>Bonjour <?php echo (!empty($quote_data->companyName) ? (strtoupper($quote_data->companyName)) : ($quote_data->firstname_quot . ' ' . strtoupper($quote_data->lastname_quot))); ?>,
        </p>
        <br>
        <p>Nous vous remercions d'avoir choisi MicroZoo pour votre devis. Veuillez trouver ci-joint le devis détaillé en pdf.</p>
        <p>Pour toute question ou clarification, n'hésitez pas à nous contacter.</p>
        <br>
        <p>Cordialement,<br>L'équipe Micro Zoo</p>

    </div>

    <!-- Email Footer -->
    <footer class="email_footer">
        <p>Micro <span>Zoo</span> Saint-Malo</p>
        <p>9 Place Vauban, 35400 Saint-Malo</p>
        <p>Téléphone: <a href="tel:+33622918304">+33 6 22 91 83 04</a> | Email: <a href="mailto:contact@microzoo.fr">contact@microzoo.fr</a></p>
        <p><a href="https://www.facebook.com/microzoosaintmalo">Facebook</a> | <a href="https://www.instagram.com/microzoosaintmalo">Instagram</a></p>
    </footer>

</body>

</html>