<?php
// Retrieve data from wordpress transient which is used to store data for a limited time to pass it
$form_data_transient = get_transient('form_data_transient');

// Retrieve quote ID from transient if available
if ($form_data_transient && isset($form_data_transient['quote_id'])) {
    $quote_id = $form_data_transient['quote_id'];
    $quote_data = getQuoteDataById($quote_id); // load sql method into variable to recover quotation data from database
}

?>

<style>
    /* Document style */
    .container_email {
        box-sizing: border-box;
        max-width: 600px;
        margin: 0 auto;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12pt;
        padding: 20px;
        background-color: #f9f9f9;
    }

    .container_email h1 {
        color: #333333;
        text-align: center;
    }

    .email_header {
        background-color: #23a455;
        color: #fff;
        padding: 10px;
        text-align: center;
    }

    .email_footer {
        background-color: #23a455;
        color: #fff;
        padding: 10px;
        text-align: center;
    }

    .link_facebook,
    .link_instagram {
        color: #fff;
        text-decoration: none;
        margin-right: 10px;
    }
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