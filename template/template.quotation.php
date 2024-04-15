<?php
// Create a date formatter instance with locale to French for proper month names
$formatter = new IntlDateFormatter(
    'fr_FR',
    IntlDateFormatter::FULL,
    IntlDateFormatter::NONE
);

// Set the pattern for the date format including the French day name
$formatter->setPattern("'Le' EEEE d MMMM yyyy");

// Get today's date
$date = new DateTimeImmutable();

// Format today's date
$formatted_date = $formatter->format($date);

$number_currency = new NumberFormatter("fr_FR", NumberFormatter::CURRENCY);
$number_decimal = new NumberFormatter("fr_FR", NumberFormatter::DECIMAL);

// Define the number of decimal places
$number_decimal->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

// Define constants
define('TVA', 5.50);
define('DISCOUNT', 100.00); // the percentage of discount can be modified here

// Retrieve data from wordpress transient which is used to store data for a limited time to pass it
$form_data_transient = get_transient('form_data_transient');

// Retrieve quote ID from transient if available
if ($form_data_transient && isset($form_data_transient['quote_id'])) {
    $quote_id = $form_data_transient['quote_id'];
    $quote_data = getQuoteDataById($quote_id); // load sql method into variable to recover quotation data from database
    $person_data = getPersonByQuoteId($quote_id); // load sql method into variable to recover person data from database

} else {
    // Display an error message
    wp_die('Error: Quote ID not found in transient. This window will close in 10 seconds...');
    // Introduce a delay before closing the window
    echo '<script>
            setTimeout(function() {
                window.close();
            }, 10000);
          </script>';
    exit; // Exit the script
}

?>

<!-- TCPDF only support inline CSS styles -->
<style>
    /* Document style */
    .container {
        box-sizing: border-box;
        width: 100%;
        margin: 0;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10pt;
    }


    /* Header style */
    .table-header {
        width: 100%;
        font-size: 10pt;
        text-align: left;
    }

    .table-header td {
        width: 50%;
        vertical-align: top;
        border: hidden;
    }

    .table-header th {
        border: hidden;
        vertical-align: center;
    }

    .span-head-title {
        text-align: end;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 16pt;
        font-weight: bold;
    }

    .span-head-header {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: bold;
    }

    .logo {
        width: 100px;
        height: 100px;
    }

    /* Details style */
    .table-details {
        border: 1px solid #dddddd;
        border-collapse: collapse;
        width: 100%;
        font-size: 9pt;
        padding: 5px;
    }

    .table-details th {
        background-color: #f5f5f5;
        border: 1px solid #dddddd;
        text-align: left;
    }

    .table-details td {
        border: 1px solid #dddddd;
    }

    .cell-30 {
        width: 30%;
    }

    .cell-10 {
        width: 10%;
    }

    .span-details-quote-id {
        font-family: Arial, Helvetica, sans-serif;
        font-weight: bold;
        font-size: 14pt;
    }


    /* Footer style */
    .table-footer {
        border: hidden;
        width: 100%;
        font-size: 10pt;
        padding: 5px;
    }

    .table-footer td {
        border: hidden;
        width: 50%;
        vertical-align: top;
    }

    .table-footer-payment {
        background-color: #f5f5f5;
        padding: 5px;
        margin: 10px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 10pt;
    }

    .table-footer-payment-td-left {
        text-align: left;
    }

    .table-footer-payment-td-right {
        text-align: right;
    }

    .table-footer-payment-tr-biggerfont {
        font-size: 12pt;
        font-weight: bold;
    }

    .crocodile {
        width: 200px;
        height: auto;
    }

    .span-thanks {
        font-size: 8pt;
        text-align: center;
    }

    .quote-payment {
        text-align: center;
    }

    .span-terms {
        font-size: 6pt;
    }
</style>

<body>
    <div class="container">
        <!-- PDF Header -->
        <table class="table-header">
            <!-- First header row -->
            <tr>
                <th>
                    <div>
                        <img class="logo" src="<?php echo plugin_dir_url(__FILE__) . '../src/images/logo.png'; ?>" />
                    </div>
                </th>
                <th><!-- Title -->
                    <div class="quote-title">
                        <span class="span-head-title">DEVIS :
                            <?php echo (!empty($quote_data->companyName) ?
                                (strtoupper($quote_data->companyName)) : ($quote_data->firstname_quot . ' ' . strtoupper($quote_data->lastname_quot))); ?>
                        </span>
                    </div>
                </th>
            </tr>
            <!-- Second header row -->
            <tr>
                <!-- Left column -->
                <td>
                    <!-- Header Information -->
                    <div class=" quote-header">
                        <span class="span-head-header">MICRO-ZOO ST MALO</span><br>
                        <span>9 place Vauban</span><br>
                        <span>35400 SAINT-MALO</span><br>
                        <span>Email : clement@microzoo.fr</span><br>
                        <span>Web : www.microzoo.fr</span><br>
                        <span>N° TVA Intracommunautaire : FR08848603270</span><br>
                        <span>N° SIRET : 84860327000010</span><br>
                        <span>Code NAF : 9104Z</span><br>
                        <span>Capital : 5 000 €</span><br>
                    </div>
                </td>

                <!-- Right column -->
                <td>
                    <!-- Client Information -->
                    <div class="quote-client">
                        <span class="span-head-header">
                            <?php echo (!empty($quote_data->companyName) ?
                                (strtoupper($quote_data->companyName)) : ($quote_data->firstname_quot . ' ' . strtoupper($quote_data->lastname_quot)));
                            ?>
                        </span><br>
                        <?php if (!empty($quote_data->companyName)) : ?>
                            <span><?php echo $quote_data->firstname_quot ?> <?php echo strtoupper($quote_data->lastname_quot) ?></span><br>
                        <?php endif; ?>
                        <span><?php echo $quote_data->address ?></span><br>
                        <!-- <span>Adresse : 123 Rue du Client</span><br>
                        <span>Code Postal, Ville</span><br> -->
                        <span>Tel : <?php echo $quote_data->phone_quot ?></span><br>
                        <span>Email : <?php echo $quote_data->email_quot ?></span><br>
                    </div>
                </td>
            </tr>
        </table>

        <hr class="divider">

        <!-- Details -->
        <div class="quote-ID">
            <span class="span-details-quote-id">DEVIS N° <?php echo $quote_data->number_quote; ?></span><br>
            <span><?php echo $formatted_date ?></span>
        </div>
        <div class="quote-details">
            <table class="table-details">
                <thead>
                    <tr class="tr-details">
                        <th class="cell-10">Référence</th>
                        <th class="cell-30">Désignation</th>
                        <th class="cell-10">Quantité</th>
                        <th class="cell-10">PU Vente</th>
                        <th class="cell-10">% Rem</th>
                        <th class="cell-10">TVA</th>
                        <th class="cell-10">Montant HT</th>
                        <th class="cell-10">Montant TTC</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // initialize vaiables
                    $total_tva = 0;
                    $total_ht = 0;
                    $total_ttc = 0;
                    $total_nbPersons = 0;
                    ?>
                    <?php foreach ($person_data as $person) : ?>
                        <?php $age_data = getAgeById($person->age_id); ?>
                        <?php
                        // calculate prices
                        $unit_ht = ($age_data->price / (1 + (TVA / 100))); // one unit price without tax
                        $unit_ttc = $age_data->price; // one uit price with tax - this is recovered from database
                        $amount_ht = ($age_data->price / (1 + (TVA / 100))) * ($person->nbPersons); // full price based on the number of person without tax
                        $amount_ttc = ($age_data->price) * ($person->nbPersons); // full price based on the number of person with tax
                        $amount_tva = $amount_ttc - $amount_ht; // full amount of the tax
                        $total_tva += $amount_tva; // total tax
                        $total_ht += $amount_ht; // total price without tax
                        $total_ttc += $amount_ttc; // total price with tax
                        $total_nbPersons += $person->nbPersons; // total number of person
                        // $free_person = ;
                        ?>
                        <tr class="tr-details">
                            <td class="cell-10">ref</td>
                            <td class="cell-30"><?php echo $age_data->category; ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format($person->nbPersons); ?></td>
                            <td class="cell-10"><?php echo $number_currency->format($unit_ht); ?></td>
                            <td class="cell-10">0,00</td>
                            <td class="cell-10"><?php echo $number_decimal->format(TVA); ?> %</td>
                            <td class="cell-10"><?php echo $number_currency->format($amount_ht); ?></td>
                            <td class="cell-10"><?php echo $number_currency->format($amount_ttc); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Calculate and include the person for free -->
                    <?php if ($total_nbPersons >= 15) : ?>
                        <?php
                        // run a query in the database to get the category name
                        $age_list = getAgeList();

                        // The initial free person for the first 15 persons.
                        $free_person = 1;

                        // For every additional 10 persons beyond the initial 15, add another free person.
                        $add_free_person = floor(($total_nbPersons - 15) / 10); // The floor function rounds down to the nearest whole number.

                        // Total number of free persons
                        $total_free_persons = $free_person + $add_free_person;

                        // calculate discounted HT and TTC prices
                        $discount_amount_ht = ($unit_ht - (($unit_ht * DISCOUNT) / 100)) * $total_free_persons;
                        $discount_amount_ttc = ($unit_ttc - (($unit_ttc * DISCOUNT) / 100)) * $total_free_persons;

                        ?>
                        <tr class="tr-details">
                            <td class="cell-10">ref</td>
                            <td class="cell-30"><?php echo $age_list[2]->category; ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format($total_free_persons); ?></td>
                            <td class="cell-10"><?php echo $number_currency->format($unit_ht); ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format(DISCOUNT); ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format(TVA); ?> %</td>
                            <td class="cell-10"><?php echo $number_currency->format($discount_amount_ht); ?></td>
                            <td class="cell-10"><?php echo $number_currency->format($discount_amount_ttc); ?></td>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

        <footer class="footer">
            <table class="table-footer">
                <tr>
                    <!-- Left column -->
                    <td>
                        <!-- Payment Terms -->
                        <div class=" quote-payment-terms">
                            <span>Conditions de paiement : </span><br>
                            <span><span>&bull; </span>100 % soit <?php echo $number_currency->format($total_ttc); ?> Paiement comptant.</span><br>
                        </div>
                        <br>
                        <!-- Bank account Info -->
                        <div class="quote-account">
                            <span>RIB MICRO-ZOO :</span><br>
                            <span>IBAN : FR76 3000 4002 5800 0102 6469 196</span><br>
                            <span>BIC : BNPAFRPPXXX</span><br>
                        </div>
                    </td>

                    <!-- Right column -->
                    <td>
                        <!-- Payment -->
                        <div class="quote-payment">
                            <table class="table-footer-payment">
                                <tr>
                                    <td class="table-footer-payment-td-left">Total HT</td>
                                    <td class="table-footer-payment-td-right"><?php echo $number_currency->format($total_ht); ?></td>
                                </tr>
                                <tr>
                                    <td class="table-footer-payment-td-left">TVA (5.50 %)</td>
                                    <td class="table-footer-payment-td-right"><?php echo $number_currency->format($total_tva); ?></td>
                                </tr>
                                <tr class="table-footer-payment-tr-biggerfont">
                                    <td class="table-footer-payment-td-left">Total TTC</td>
                                    <td class="table-footer-payment-td-right"><?php echo $number_currency->format($total_ttc); ?></td>
                                </tr>
                            </table>
                            <img class="crocodile" src="<?php echo plugin_dir_url(__FILE__) . '../src/images/crocodile.png'; ?>" /><br>
                            <span class="span-thaks">Toute l'équipe de Micro Zoo vous remercie !</span>
                        </div>
                    </td>
                </tr>
            </table>

        </footer>
    </div>
</body>