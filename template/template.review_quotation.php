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

// Retrieve quote ID from URL parameter
// $quote_id = isset($_GET['quote_id']) ? intval($_GET['quote_id']) : 0;

// $quote_data = getQuoteDataById($quote_id); // load sql method into variable to recover a single quotation row from database
// $person_data = getPersonByQuoteId($quote_id); // load sql method into variable to recover a single person row from database

// Check if user is logged in
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $user_key = 'user_' . $user_id;
} else {
    // Use session ID for non-logged-in users
    if (!isset($_SESSION['user_key'])) {
        $_SESSION['user_key'] = 'user_' . session_id();
    }
    $user_key = $_SESSION['user_key'];
}

// Retrieve the transient data using the user-specific key
$form_data_transient = get_transient($user_key . '_form_data_transient');

// Populate document with transient data if available
if ($form_data_transient && isset($form_data_transient['form_data'])) {
    $form_data = $form_data_transient['form_data'];

    // Sanitize POST inputs recovered from transient $form_data before sending to database
    $email_quot = sanitize_email($form_data['email_quot']);
    $lastname_quot = sanitize_text_field(ucwords($form_data['lastname_quot']));
    $firstname_quot = sanitize_text_field(ucwords($form_data['firstname_quot']));
    $companyName = sanitize_text_field(stripslashes($form_data['companyName']));
    $address = sanitize_text_field($form_data['address']);
    $phone_quot = sanitize_text_field($form_data['phone_quot']);
    $visitetype = sanitize_text_field($form_data['visitetype']);
    $datetimeVisit = sanitize_text_field($form_data['datetimeVisit']);
    $payment = sanitize_text_field($form_data['payment']);
    $comment = sanitize_textarea_field($form_data['comment']);

    $nbPersons = $form_data['nbPersons'];
    $ages = $form_data['ages'];

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
    wp_die('no data in transient', 'Error', array('response' => 403));
}

// include logo from src
$logo_header = plugin_dir_url(__FILE__) . '../src/images/logo_square.png';

// Read CSS file content and inline it because PHPMailer does not directly handle external CSS styling for email templates.
$css_content = file_get_contents(plugin_dir_url(__FILE__) . '../src/css/style.css');
?>

<!-- TCPDF only support inline CSS styles -->
<style>
    <?php echo $css_content; ?>
</style>

<div id="scroll_here"></div>

<div class="container-fluid py-3">
    <header>
        <div class="d-flex flex-column flex-md-row align-items-center pb-3 mb-4 border-bottom">
            <img class="w-25 me-3 shadow-sm bg-body-tertiary rounded" src="<?php echo $logo_header; ?>" />
            <span class="fs-2 p-3 text-body-emphasis" style="color: white !important;">Révisez votre devis!</span>
        </div>
    </header>

    <main>

        <!-- Client Information -->
        <div class="row mb-3 text-center">
            <div class="col">
                <div class="card rounded-3 shadow-sm">
                    <div class="card-header py-3">
                        <h4 class="my-0 fw-normal">
                            <?php echo (!empty($companyName)) ?
                                (strtoupper($companyName)) : ($firstname_quot . ' ' . strtoupper($lastname_quot));
                            ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($companyName)) : ?>
                            <h5 class="card-title">
                                <?php echo $firstname_quot ?> <?php echo strtoupper($lastname_quot) ?>
                            </h5>
                        <?php endif; ?>
                        <ul class="list-unstyled mt-3 mb-4">
                            <li><?php echo $address ?></li>
                            <li>Tel : <?php echo $phone_quot ?></li>
                            <li>Email : <?php echo $email_quot ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="fs-3 text-center mb-4 text-body-emphasis" style="color: white !important;">Détails</h2>

        <!-- Details -->
        <div class="row mb-3 text-center">
            <div class="col">
                <div class="card rounded-3 shadow-sm">
                    <div class="table-responsive p-3">
                        <table class="table" style="border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th class="align-baseline" scope="col" style="border: none;">Tarif</th>
                                    <th class="align-baseline" scope="col" style="border: none;">Quantité</th>
                                    <th class="align-baseline" scope="col" style="border: none;">Prix HT</th>
                                    <th class="align-baseline" scope="col" style="border: none;">Montant TTC</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php for ($i = 0; $i < count($nbPersons); $i++) : ?>
                                    <?php
                                    $age_data = getAgeById($ages[$i]); // get one row of age data in the current quote
                                    ?>
                                    <!-- details -->
                                    <tr class="border-bottom">
                                        <td class=" align-baseline text-start bg-white text-dark" style="border: none;"><?php echo $age_data->category; ?></td>
                                        <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_decimal->format($nbPersons[$i]); ?></td>
                                        <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($unit_ht[$i]); ?></td>
                                        <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($amount_ttc[$i]); ?></td>
                                    </tr>
                                <?php endfor; ?>

                                <!-- Add guided option if exists -->
                                <?php if ($visitetype === "2") : ?>
                                    <?php
                                    // run a query in the database to get the guided category row
                                    $visitetype_guided = getVisiteTypeById($visitetype);
                                    ?>
                                    <tr class="border-bottom">
                                        <td class="align-baseline text-start bg-white text-dark" style="border: none;"><?php echo "Visite " . $visitetype_guided->name; ?></td>
                                        <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_decimal->format($guided_qty); ?></td>
                                        <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($guided_price_ht); ?></td>
                                        <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($guided_amount_ttc); ?></td>
                                    </tr>
                                <?php endif; ?>

                                <!-- Calculate and include the person for free -->
                                <?php if ($total_free_persons > 0) : ?>
                                    <?php
                                    $age_list = getAgeList(); // run a query in the database to get the category
                                    ?>
                                    <tr class="border-bottom">
                                        <td class="align-baseline text-start bg-white text-dark" style="border: none;"><?php echo $age_list[2]->category; ?></td>
                                        <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_decimal->format($total_free_persons); ?></td>
                                        <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo "gratuite"; ?></td>
                                        <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($discount_amount_ttc); ?></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment -->
        <div class="row mb-3 text-center">
            <div class="col">
                <div class="card rounded-3 shadow-sm">
                    <div class="table-responsive p-3">
                        <table class="table" style="border-collapse: collapse;">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="align-baseline text-start bg-white text-dark" style="border: none;">Total HT</td>
                                    <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($total_ht); ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="align-baseline text-start bg-white text-dark" style="border: none;">TVA (5.50 %)</td>
                                    <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($total_tva); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold align-baseline text-start bg-white text-dark" style="border: none;">Total TTC</td>
                                    <td class="fw-bold align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($total_ttc); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thanks -->
        <p class="fs-6 text-center mb-3 text-body-emphasis" style="color: white !important;">Toute l'équipe de Micro Zoo vous remercie!</p>
    </main>
</div>