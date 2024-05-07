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

// Create number formatter instance for currency and decimal formats
$number_currency = new NumberFormatter("fr_FR", NumberFormatter::CURRENCY);
$number_decimal = new NumberFormatter("fr_FR", NumberFormatter::DECIMAL);

// Define the number of decimal places
$number_decimal->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

// Retrieve data from wordpress transient which is used to store data for a limited time to pass it
$form_data_transient = get_transient('form_data_transient');

// Populate form fields with transient data if available
if ($form_data_transient && isset($form_data_transient['form_data'])) {
    $form_data = $form_data_transient['form_data'];
}

// Retrieve quote ID from transient if available
if ($form_data_transient && isset($form_data_transient['quote_id'])) {
    $quote_id = $form_data_transient['quote_id'];
    // $quote_data = getQuoteDataById($quote_id); // load sql method into variable to recover a single quotation row from database
    // $person_data = getPersonByQuoteId($quote_id); // load sql method into variable to recover a single person row from database

    // Instantiate the QuoteCalculator class to use calculated results
    $quote_calculator = new QuoteCalculator();

    // Calculate results: totals, unit prices, references, quantities, etc
    // $results = $quote_calculator->calculateResults($quote_data, $person_data);
    $results = $quote_calculator->calculateResultsFromTransient($form_data);

    // Extract results from the returned calculated results
    $total_tva = $results['total_tva'];
    $total_ht = $results['total_ht'];
    $total_ttc = $results['total_ttc'];
    $total_paying_persons = $results['total_paying_persons'];
    $total_persons = $results['total_persons'];
    $unit_ht = $results['unit_ht'];
    $amount_ht = $results['amount_ht'];
    $amount_ttc = $results['amount_ttc'];
    $ref = $results['ref'];
    $guided_qty = $results['guided_qty'];
    $guided_price_ht = $results['guided_price_ht'];
    $guided_amount_ht = $results['guided_amount_ht'];
    $guided_amount_ttc = $results['guided_amount_ttc'];
    $total_free_persons = $results['total_free_persons'];
    $discount_unit_ht = $results['discount_unit_ht'];
    $discount_amount_ht = $results['discount_amount_ht'];
    $discount_amount_ttc = $results['discount_amount_ttc'];
} else {
    // Display an error message
    wp_die('Error: Quote ID not found in transient.');
    exit; // Exit the script
}

// Read CSS file content and inline it because PHPMailer does not directly handle external CSS styling for email templates.
$css_content = file_get_contents(plugin_dir_url(__FILE__) . '../src/css/pdf_style.css');
?>

<!-- TCPDF only support inline CSS styles -->
<style>
    <?php echo $css_content; ?>
</style>

<div id="scroll_here"></div>

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
                    <div class="quote-header">
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
                    <?php foreach ($person_data as $index => $person) : ?>
                        <?php
                        $age_data = getAgeById($person->age_id); // get one row of age data in the current quote
                        ?>
                        <!-- details -->
                        <tr class="tr-details">
                            <td class="cell-10"><?php echo $ref; ?></td>
                            <td class="cell-30"><?php echo $age_data->category; ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format($person->nbPersons); ?></td>
                            <td class="cell-10"><?php echo $number_currency->format($unit_ht[$index]); ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format(0); ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format(TVA); ?> %</td>
                            <td class="cell-10"><?php echo $number_currency->format($amount_ht[$index]); ?></td>
                            <td class="cell-10"><?php echo $number_currency->format($amount_ttc[$index]); ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Add guided option if exists -->
                    <?php if ($quote_data->visitetype_id === "2") : ?>
                        <?php
                        // run a query in the database to get the guided category row
                        $visitetype_guided = getVisiteTypeById($quote_data->visitetype_id);
                        ?>
                        <tr class="tr-details">
                            <td class="cell-10"><?php echo $visitetype_guided->ref; ?></td>
                            <td class="cell-30"><?php echo "Visite " . $visitetype_guided->name; ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format($guided_qty); ?></td>
                            <td class="cell-10"><?php echo $number_currency->format($guided_price_ht); ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format(0); ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format(TVA); ?> %</td>
                            <td class="cell-10"><?php echo $number_currency->format($guided_amount_ht); ?></td>
                            <td class="cell-10"><?php echo $number_currency->format($guided_amount_ttc); ?></td>
                        </tr>
                    <?php endif; ?>

                    <!-- Calculate and include the person for free -->
                    <?php if ($total_free_persons > 0) : ?>
                        <?php
                        $age_list = getAgeList(); // run a query in the database to get the category

                        ?>
                        <tr class="tr-details">
                            <td class="cell-10"><?php echo $age_list[2]->ref_disc; ?></td>
                            <td class="cell-30"><?php echo $age_list[2]->category; ?></td>
                            <td class="cell-10"><?php echo $number_decimal->format($total_free_persons); ?></td>
                            <td class="cell-10"><?php echo $number_currency->format($discount_unit_ht); ?></td>
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