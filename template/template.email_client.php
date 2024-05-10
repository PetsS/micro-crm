<?php
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation - Message reçu de MicroZoo</title>
</head>

<body>

    <!-- Header -->
    <header class="email_header">
        <img src="data:image/png;base64,<?php echo base64_encode($logo_header); ?>" alt="MicroZoo Logo">
    </header>

    <div class="container_email">

        <!-- Email Body -->
        <p>Bonjour,</p>
        <p>Nous avons bien reçu votre message et vous remercions de l'intérêt que vous portez au MicroZoo de Saint-Malo. Nous vous répondrons dans les meilleurs délais.</p>

        <h3>Horaires d'ouverture :</h3>
        <p>Le MicroZoo Saint-Malo est ouvert toute l'année, de 10h à 19h (10h-20h en juillet et août).</p>

        <h3>Informations pratiques :</h3>
        <ul>
            <li>Accessible aux poussettes.</li>
            <li>Parking disponible aux abords de la ville historique d'intra-muros : <a href="https://www.st-malo.com/tourisme/parking-saint-malo/">Lien vers les parkings</a></li>
            <li>Accessible en bus : dépose à la Porte St Vincent, à 50 mètres de l'entrée du MicroZoo.</li>
            <li>Pas de réservation nécessaire pour les individuels.</li>
        </ul>

        <h3>Tarifs individuels en visite libre :</h3>
        <ul>
            <li>Moins de 3 ans: gratuit.</li>
            <li>3 à 12 ans : 6,9 euros.</li>
            <li>13 ans et plus : 9,9 euros.</li>
            <li>Tarif réduit sur présentation d'un justificatif : 7 euros.</li>
        </ul>

        <h3>Abonnements annuels :</h3>
        <ul>
            <li>Adulte : 20 euros</li>
            <li>Enfant : 15 euros</li>
        </ul>

        <h3>Activités spéciales (Sur réservation) :</h3>
        <ul>
            <li>Soigneur d'un jour: Adulte 70 euros, Enfant 50 euros. Tarif réduit 60 euros.</li>
            <li>Visite guidée avec nourrissage commenté : 79 euros pour un groupe de 6 personnes maximum (2h).</li>
        </ul>

        <h3>Tarifs groupes en visite libre (à partir de 15 personnes) :</h3>
        <ul>
            <li>1 gratuité accompagnateur par tranche de 15 entrées payantes.</li>
            <li>Moins de 3 ans : gratuit.</li>
            <li>3 à 12 ans: 5,9 euros.</li>
            <li>13 ans et plus : 8,9 euros.</li>
            <li>Tarif réduit sur présentation d'un justificatif : 7,9 euros.</li>
            <li>Option visite guidée avec nourrissage commenté (2h) : 70 euros pour 10 visiteurs.</li>
        </ul>

        <p>Merci de votre intérêt pour le MicroZoo et restons à votre disposition pour toute information complémentaire.</p>
        <p>À bientôt !</p>
        <p>L'équipe Micro Zoo</p>
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