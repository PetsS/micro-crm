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
$quote_id = isset($_GET['quote_id']) ? intval($_GET['quote_id']) : 0;


$quote_data = getQuoteDataById($quote_id); // load sql method into variable to recover a single quotation row from database
$person_data = getPersonByQuoteId($quote_id); // load sql method into variable to recover a single person row from database

// Instantiate the QuoteCalculator class to use calculated results
$quote_calculator = new QuoteCalculator();

// Calculate results: totals, unit prices, references, quantities, etc
$results = $quote_calculator->calculateResults($quote_data, $person_data);

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

// Read CSS file content and inline it because PHPMailer does not directly handle external CSS styling for email templates.
$css_content = file_get_contents(plugin_dir_url(__FILE__) . '../src/css/pdf_style.css');
?>

<!-- TCPDF only support inline CSS styles -->
<style>
    <?php echo $css_content; ?>
</style>


<div class="container mt-3">
    <!-- PDF Header -->
    <table class="table-header">
        <!-- First header row -->
        <tr>
            <th><!-- Title -->
                <div class="quote-title">
                    <span class="span-head-title">DEVIS pour :
                        <?php echo (!empty($quote_data->companyName) ?
                            (strtoupper($quote_data->companyName)) : ($quote_data->firstname_quot . ' ' . strtoupper($quote_data->lastname_quot))); ?>
                    </span>
                </div>
            </th>
        </tr>
        <!-- Second header row -->
        <tr>
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
        <span><?php echo $formatter->format(new DateTimeImmutable($quote_data->creation_date)); ?></span>
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
                    </div>
                </td>
            </tr>
        </table>
    </footer>
    <hr class="divider">
</div>







<h2>Mettre à jour DEVIS N° <?php echo $quote_data->number_quote; ?></h2>

<div id="formQuotationModify">

    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>?update=<?php echo $quote_id; ?>" method="post">

        <label for="email_quot_mod">Email:</label>
        <input type="text" id="email_quot_mod" name="email_quot" placeholder="Votre email" value="<?php echo $quote_data->email_quot; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['email_quot'])) : ?>
            <span class="error"><?php echo $form_errors['email_quot']; ?><br></span>
        <?php endif; ?>
        <br>

        <label for="lastname_quot_mod">Nom:</label>
        <input type="text" id="lastname_quot_mod" name="lastname_quot" placeholder="Votre nom" value="<?php echo $quote_data->lastname_quot; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['lastname_quot'])) : ?>
            <span class="error"><?php echo $form_errors['lastname_quot']; ?><br></span>
        <?php endif; ?><br>

        <label for="firstname_quot_mod">Prénom:</label>
        <input type="text" id="firstname_quot_mod" name="firstname_quot" placeholder="Votre prénom" value="<?php echo $quote_data->firstname_quot; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['firstname_quot'])) : ?>
            <span class="error"><?php echo $form_errors['firstname_quot']; ?><br></span>
        <?php endif; ?><br>

        <label for="companyName_mod">Raison Social:</label>
        <input type="text" id="companyName_mod" name="companyName" placeholder="Nom de la société" value="<?php echo $quote_data->companyName; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['companyName'])) : ?>
            <span class="error"><?php echo $form_errors['companyName']; ?><br></span>
        <?php endif; ?><br>

        <label for="address_mod"> <?php echo !empty($form_data['companyName']) ? 'Adresse de la société:' : 'Adresse:'; ?> </label>
        <input type="text" id="address_mod" name="address" placeholder="Saisissez une adresse" value="<?php echo $quote_data->address; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['address'])) : ?>
            <span class="error"><?php echo $form_errors['address']; ?><br></span>
        <?php endif; ?><br>

        <label for="phone_quot_mod">Tel:</label>
        <input type="tel" id="phone_quot_mod" name="phone_quot" placeholder="Numéro de téléphone" value="<?php echo $quote_data->phone_quot; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['phone_quot'])) : ?>
            <span class="error"><?php echo $form_errors['phone_quot']; ?><br></span>
        <?php endif; ?><br>

        <label for="visitetype_mod">Type de visite:</label>
        <select name="visitetype" id="visitetype_mod">
            <option value="default">Choisir...</option>
            <?php foreach (getVisiteTypeList() as $visitetype) : ?>
                <option value="<?php echo $visitetype->id ?>" <?php echo $quote_data->visitetype_id == $visitetype->id ? 'selected' : ''; ?>><?php echo $visitetype->name ?></option>
            <?php endforeach; ?>
        </select><br>
        <?php if (isset($form_errors) && isset($form_errors['visitetype'])) : ?>
            <span class="error"><?php echo $form_errors['visitetype']; ?><br></span>
        <?php endif; ?><br>

        <div id="info-visiteType" class="hidden">
            <!-- TODO replace price with database data -->
            <p>
                Option supplémentaire visite guidée avec nourrissages commentés (2h) :
            <ul>
                <li><?php echo getVisiteTypeById(2)->price; ?> € en plus du prix entrée pour 1 à 10 visiteurs, </li>
                <li><?php echo getVisiteTypeById(2)->price * 2; ?> € pour 11 à 20 visiteurs,</li>
                <li><?php echo getVisiteTypeById(2)->price * 3; ?> € pour 21 à 30 visiteurs.</li>
            </ul>
            </p>
        </div>

        <div>
            <?php for ($i = 0; $i < count($person_data); $i++) : ?>
                <div class="containerClone" id="container-<?php echo $i ?>">
                    <div class="input-group">
                        <label for="nbPersons_mod">Nombre de personnes:</label>
                        <input type="number" id="nbPersons_mod" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." min="0" value="<?php echo $person_data[$i]->nbPersons; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['nbPersons'][$i])) : ?>
                            <span class="error"><?php echo $form_errors['nbPersons'][$i]; ?><br></span>
                        <?php endif; ?><br>
                    </div>

                    <div class="input-group">
                        <label for="ages_mod">Tarif:</label>
                        <select name="ages[]" id="ages_mod">
                            <option value="default" <?php echo getAgeById($person_data[$i]->age_id)->category == 'default' ? 'selected' : ''; ?>>Choisissez...</option>
                            <?php foreach (getAgeList() as $age) : ?>
                                <option value="<?php echo $age->id ?>" <?php echo ($person_data[$i]->age_id == $age->id) ? ' selected' : ''; ?>><?php echo $age->category ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($form_errors) && isset($form_errors['ages'][$i])) : ?>
                            <span class="error"><?php echo $form_errors['ages'][$i]; ?><br></span>
                        <?php endif; ?><br>
                    </div>
                </div>
            <?php endfor; ?>


            <div id="info-persons" class="hidden">
                <p>
                    Avantages d'un groupe de 15 personnes ou plus :<br>
                    (les enfants de moins de 3 ans ne comptent pas)
                <ul>
                    <li>1 accompagnement gratuit.</li>
                    <li>chaque dixième personne est gratuite.</li>
                    <li>réduction / personne.</li>
                    </p>
                </ul>
            </div>

            <div id="info-persons-discount" class="hidden">
                <p>
                    Vous êtes au dessus de 15 personnes payantes.<br>
                    Vous avez droit à une réduction.<br>
                    (les enfants de moins de 3 ans ne comptent pas)
                <ul>
                    <?php foreach (getAgeList() as $age) : ?>
                        <li><?php echo $age->category . ' : ' . ($age->price === "0" ? "gratuit" : $age->price_disc . " €"); ?></li>
                    <?php endforeach; ?>
                </ul>
                </p>
            </div>

            <div>
                <button type="button" class="btn btn-primary" id="btn-add-persons-modify" name="btn-add-persons">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <br>

        <label for="datetimeVisit_mod">Date et heure de la visite:</label>
        <input type="datetime-local" id="datetimeVisit_mod" name="datetimeVisit" value="<?php echo $quote_data->datetimeVisit; ?>" />
        <?php if (isset($form_errors) && isset($form_errors['datetimeVisit'])) : ?>
            <span class="error"><?php echo $form_errors['datetimeVisit']; ?><br></span>
        <?php endif; ?><br>

        <label for="payment_mod">Mode paiement:</label>
        <select name="payment" id="payment_mod">
            <option value="default">Choisir...</option>
            <?php foreach (getPaymentList() as $payment) : ?>
                <option value="<?php echo $payment->id ?>" <?php echo $quote_data->payment_id == $payment->id ? ' selected' : ''; ?>><?php echo $payment->category ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($form_errors) && isset($form_errors['payment'])) : ?>
            <span class="error"><?php echo $form_errors['payment']; ?><br></span>
        <?php endif; ?><br>
        <br>

        <label for="comment_mod">Commentaire:</label>
        <textarea name="comment" id="comment_mod" rows="4" placeholder="Votre commentaire"><?php echo  esc_textarea(stripslashes($quote_data->comment)); ?></textarea>

        <input type="hidden" name="action" value="form_submission">
        <input type="hidden" name="form_nonce" value="<?php echo wp_create_nonce('form_submit'); ?>">
        <?php wp_nonce_field('form_submit', 'form_nonce'); ?>

        <a href="<?php echo esc_url(remove_query_arg(array('update', 'quote_id'), wp_get_referer())); ?>" class="btn btn-danger">Annuler</a>
        <button type="submit" name="submit-btn-quotation">Modifier</button>
    </form>
</div>