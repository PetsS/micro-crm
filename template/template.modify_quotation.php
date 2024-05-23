<?php
// Get the current user ID
$user_id = get_current_user_id();

// Generate the user-specific key
$user_key = 'user_' . $user_id;

// Retrieve the transient data using the user-specific key
$form_data_transient = get_transient($user_key . '_form_data_transient');

// Retrieve errors stored in transient and add it to $form_errors variable
if (isset($_GET['form_error']) && $_GET['form_error'] === 'form') {
    if ($form_data_transient && isset($form_data_transient['form_errors'])) {
        $form_errors = $form_data_transient['form_errors'];
    }
}

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

?>


<div class="m-0 container py-3">
    <!-- header -->
    <header>
        <div class="d-flex flex-column flex-md-row align-items-center pb-3 mb-4 border-bottom">

            <div class="d-flex align-items-center">
                <img class="me-3 rounded-3" src="<?php echo plugin_dir_url(__FILE__) . '../src/images/logo_square.png'; ?>" height="50" />
                <span class="fs-4">DEVIS pour :
                    <?php echo (!empty($quote_data->companyName) ?
                        (strtoupper($quote_data->companyName)) : ($quote_data->firstname_quot . ' ' . strtoupper($quote_data->lastname_quot))); ?>
                </span>
            </div>
        </div>
        <div>
            <span class="fw-bolder fs-5">N° <?php echo $quote_data->number_quote; ?></span><br>
            <span class="fs-6"><?php echo $formatter->format(new DateTimeImmutable($quote_data->creation_date)); ?></span>
        </div>
    </header>

    <main>
        <div class="row mb-3 text-center">

            <!-- Client Information -->
            <div class="col">
                <div class="card mb-4 rounded-3 shadow-sm">
                    <div class="card-header">
                        <h4 class="fs-4">
                            <?php echo (!empty($quote_data->companyName) ?
                                (strtoupper($quote_data->companyName)) : ($quote_data->firstname_quot . ' ' . strtoupper($quote_data->lastname_quot)));
                            ?>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($quote_data->companyName)) : ?>
                            <h5 class="card-title">
                                <?php echo $quote_data->firstname_quot ?> <?php echo strtoupper($quote_data->lastname_quot) ?>
                            </h5>
                        <?php endif; ?>
                        <ul class="list-unstyled">
                            <li><?php echo $quote_data->address ?></li>
                            <li>Tel : <?php echo $quote_data->phone_quot ?></li>
                            <li>Email : <?php echo $quote_data->email_quot ?></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Payment -->
            <div class="col">
                <div class="card rounded-3 shadow-sm">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr class="border-bottom">
                                    <td class="align-baseline text-start">Total HT</td>
                                    <td class="align-baseline"><?php echo $number_currency->format($total_ht); ?></td>
                                </tr>
                                <tr class="border-bottom">
                                    <td class="align-baseline text-start">TVA (5.50 %)</td>
                                    <td class="align-baseline"><?php echo $number_currency->format($total_tva); ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold align-baseline text-start">Total TTC</td>
                                    <td class="fw-bold align-baseline"><?php echo $number_currency->format($total_ttc); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- Details -->
            <div>
                <table class="table">
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
                    <tbody class="table-group-divider">
                        <?php foreach ($person_data as $index => $person) : ?>
                            <?php
                            $age_data = getAgeById($person->age_id); // get one row of age data in the current quote
                            ?>
                            <!-- details -->
                            <tr>
                                <td><?php echo $ref; ?></td>
                                <td><?php echo $age_data->category; ?></td>
                                <td><?php echo $number_decimal->format($person->nbPersons); ?></td>
                                <td><?php echo $number_currency->format($unit_ht[$index]); ?></td>
                                <td><?php echo $number_decimal->format(0); ?></td>
                                <td><?php echo $number_decimal->format(TVA); ?> %</td>
                                <td><?php echo $number_currency->format($amount_ht[$index]); ?></td>
                                <td><?php echo $number_currency->format($amount_ttc[$index]); ?></td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Add guided option if exists -->
                        <?php if ($quote_data->visitetype_id === "2") : ?>
                            <?php
                            // run a query in the database to get the guided category row
                            $visitetype_guided = getVisiteTypeById($quote_data->visitetype_id);
                            ?>
                            <tr>
                                <td><?php echo $visitetype_guided->ref; ?></td>
                                <td><?php echo "Visite " . $visitetype_guided->name; ?></td>
                                <td><?php echo $number_decimal->format($guided_qty); ?></td>
                                <td><?php echo $number_currency->format($guided_price_ht); ?></td>
                                <td><?php echo $number_decimal->format(0); ?></td>
                                <td><?php echo $number_decimal->format(TVA); ?> %</td>
                                <td><?php echo $number_currency->format($guided_amount_ht); ?></td>
                                <td><?php echo $number_currency->format($guided_amount_ttc); ?></td>
                            </tr>
                        <?php endif; ?>

                        <!-- Calculate and include the person for free -->
                        <?php if ($total_free_persons > 0) : ?>
                            <?php
                            $age_list = getAgeList(); // run a query in the database to get the category

                            ?>
                            <tr>
                                <td><?php echo $age_list[2]->ref_disc; ?></td>
                                <td><?php echo $age_list[2]->category; ?></td>
                                <td><?php echo $number_decimal->format($total_free_persons); ?></td>
                                <td><?php echo $number_currency->format($discount_unit_ht); ?></td>
                                <td><?php echo $number_decimal->format(DISCOUNT); ?></td>
                                <td><?php echo $number_decimal->format(TVA); ?> %</td>
                                <td><?php echo $number_currency->format($discount_amount_ht); ?></td>
                                <td><?php echo $number_currency->format($discount_amount_ttc); ?></td>
                            </tr>
                        <?php endif; ?>

                    </tbody>

                </table>
            </div>

        </div>
    </main>
</div>


<div id="formQuotationModify">

    <div class="card">

        <div class="col-md-auto">

            <h2>Mettre à jour</h2>
            <h3 class="mb-4 fs-3"> DEVIS N° <?php echo $quote_data->number_quote; ?></h3>

            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>?update=<?php echo $quote_id; ?>" method="post">

                <div class="row g-3">

                    <div class="col-sm-6">
                        <label for="firstname_quot_mod" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="firstname_quot_mod" name="firstname_quot" placeholder="" value="<?php echo $quote_data->firstname_quot; ?>" />
                        <!-- display error message -->
                        <?php if (isset($form_errors) && isset($form_errors['firstname_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['firstname_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label for="lastname_quot_mod" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="lastname_quot_mod" name="lastname_quot" placeholder="" value="<?php echo $quote_data->lastname_quot; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['lastname_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['lastname_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label for="companyName_mod" class="form-label">Raison Social</label>
                        <input type="text" class="form-control" id="companyName_mod" name="companyName" placeholder="" value="<?php echo $quote_data->companyName; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['companyName'])) : ?>
                            <span class="error"><?php echo $form_errors['companyName']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="text" class="form-control" id="email_quot_mod" name="email_quot" placeholder="email@example.com" value="<?php echo $quote_data->email_quot; ?>" />
                        </div>
                        <?php if (isset($form_errors) && isset($form_errors['email_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['email_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="tel" class="form-control" id="phone_quot_mod" name="phone_quot" placeholder="0612345678" value="<?php echo $quote_data->phone_quot; ?>" />
                        </div>
                        <?php if (isset($form_errors) && isset($form_errors['phone_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['phone_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label for="address_mod" class="form-label"> <?php echo !empty($form_data['companyName']) ? 'Adresse de la société:' : 'Adresse:'; ?> </label>
                        <input type="text" class="form-control" id="address_mod" name="address" placeholder="123 rue d'exemple, 12345 Ville" value="<?php echo $quote_data->address; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['address'])) : ?>
                            <span class="error"><?php echo $form_errors['address']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><label for="datetimeVisit_mod">Date et heure de la visite:</label></span>
                            <input class="form-control" type="datetime-local" id="datetimeVisit_mod" name="datetimeVisit" value="<?php echo $quote_data->datetimeVisit; ?>" />
                        </div>
                        <?php if (isset($form_errors) && isset($form_errors['datetimeVisit'])) : ?>
                            <span class="error"><?php echo $form_errors['datetimeVisit']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label for="visitetype_mod" class="form-label">Type de visite:</label>
                        <select id="visitetype_mod" class="form-select" name="visitetype">
                            <option value="default">Choisir...</option>
                            <?php foreach (getVisiteTypeList() as $visitetype) : ?>
                                <option value="<?php echo $visitetype->id ?>" <?php echo $quote_data->visitetype_id == $visitetype->id ? 'selected' : ''; ?>><?php echo $visitetype->name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($form_errors) && isset($form_errors['visitetype'])) : ?>
                            <span class="error"><?php echo $form_errors['visitetype']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label for="payment_mod" class="form-label">Mode paiement:</label>
                        <select id="payment_mod" class="form-select" name="payment">
                            <option value="default">Choisir...</option>
                            <?php foreach (getPaymentList() as $payment) : ?>
                                <option value="<?php echo $payment->id ?>" <?php echo $quote_data->payment_id == $payment->id ? ' selected' : ''; ?>><?php echo $payment->category ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($form_errors) && isset($form_errors['payment'])) : ?>
                            <span class="error"><?php echo $form_errors['payment']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Info displayed for visit type -->
                    <!-- <div id="info-visiteType_mod" class="hidden">
                        <div class="col-12">
                            <hr class="mb-3">
                            <div class="mx-3 card bg-light">
                                <div class="card-body">
                                    <p class="card-text">Option supplémentaire visite guidée avec nourrissages commentés (2h) :</p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><?php echo getVisiteTypeById(2)->price; ?> € en plus du prix entrée pour 1 à 10 visiteurs, </li>
                                    <li class="list-group-item"><?php echo getVisiteTypeById(2)->price * 2; ?> € pour 11 à 20 visiteurs,</li>
                                    <li class="list-group-item"><?php echo getVisiteTypeById(2)->price * 3; ?> € pour 21 à 30 visiteurs.</li>
                                </ul>
                            </div>
                        </div>
                    </div> -->

                    <!-- Dynamic container -->
                    <?php if (!empty($person_data)) :
                        for ($i = 0; $i < count($person_data); $i++) : ?>
                            <!-- Container to be cloned -->
                            <div class="containerClone" id="container-<?php echo $i ?>">
                                <div class="col-12">
                                    <div class="input-group mb-1">
                                        <label class="input-group-text" for="nbPersons_mod">Nombre de personnes:</label>
                                        <input class="form-control" type="number" id="nbPersons_mod" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." min="0" value="<?php echo $person_data[$i]->nbPersons; ?>" />
                                    </div>
                                    <?php if (isset($form_errors) && isset($form_errors['nbPersons'][$i])) : ?>
                                        <span class="error"><?php echo $form_errors['nbPersons'][$i]; ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="col-12">
                                    <div id="tarif-group" class="input-group">
                                        <label class="input-group-text" for="ages_mod">Tarif:</label>
                                        <select class="form-select" id="ages_mod" name="ages[]">
                                            <option value="default" <?php echo getAgeById($person_data[$i]->age_id)->category == 'default' ? 'selected' : ''; ?>>Choisir...</option>
                                            <?php foreach (getAgeList() as $age) : ?>
                                                <option value="<?php echo $age->id ?>" <?php echo ($person_data[$i]->age_id == $age->id) ? ' selected' : ''; ?>><?php echo $age->category ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php if (isset($form_errors) && isset($form_errors['ages'][$i])) : ?>
                                        <span class="error"><?php echo $form_errors['ages'][$i]; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php else : ?>
                        <!-- Permanent container -->
                        <div class="containerClone" id="container-0">
                            <div class="col-12">
                                <div class="input-group mb-1">
                                    <label class="input-group-text" for="nbPersons_mod"><i class="pe-2 bi bi-person"></i>Nombre de personnes</label>
                                    <input class="form-control" type="number" id="nbPersons_mod" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." min="0" value="<?php echo $person_data[$i]->nbPersons; ?>" />
                                </div>
                                <?php if (isset($form_errors) && isset($form_errors['nbPersons'])) : ?>
                                    <span class="error"><?php echo $form_errors['nbPersons']; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="col-12">
                                <div id="tarif-group" class="input-group">
                                    <label class="input-group-text" for="ages_mod"><i class="pe-2 bi bi-currency-euro"></i>Tarif</label>
                                    <select class="form-select" id="ages_mod" name="ages[]">
                                        <option value="default" <?php echo getAgeById($person_data[$i]->age_id)->category == 'default' ? 'selected' : ''; ?>>Choisir...</option>
                                        <?php foreach (getAgeList() as $age) : ?>
                                            <option value="<?php echo $age->id ?>" <?php echo ($person_data[$i]->age_id == $age->id) ? ' selected' : ''; ?>><?php echo $age->category ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if (isset($form_errors) && isset($form_errors['ages'])) : ?>
                                    <span class="error"><?php echo $form_errors['ages']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add button -->
                    <button type="button" class="w-100 btn btn-warning btn-lg" id="btn-add-persons" name="btn-add-persons">
                        <i class="fa fa-plus"></i>
                    </button>

                    <!-- Info displayed for number of persons -->
                    <!-- <div id="info-persons" class="hidden">
                        <div class="col-12">
                            <hr class="mb-3">
                            <div class="mx-3 card bg-light">
                                <div class="card-body">
                                    <p class="card-text">
                                        Avantages d'un groupe de 15 personnes ou plus :
                                        (les enfants de moins de 3 ans ne comptent pas)
                                    </p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">15 entrées payantes = 1 accompagnant offert</li>
                                    <li class="list-group-item">25 entrées payantes = 2 accompagnants offerts</li>
                                    <li class="list-group-item">35 entrées payantes = 3 accompagnants offerts</li>
                                    <li class="list-group-item">etc...</li>
                                    <li class="list-group-item">Réduction sur le prix d'entrée par personne.</li>
                                </ul>
                            </div>
                        </div>
                    </div> -->

                    <!-- Info displayed for discount -->
                    <!-- <div id="info-persons-discount" class="hidden">
                        <div class="col-12">
                            <hr class="mb-3">
                            <div class="mx-3 card bg-light">
                                <div class="card-body">
                                    <p class="card-text">
                                        Vous êtes au dessus de 15 personnes payantes.
                                        Vous avez droit à une réduction.
                                        (les enfants de moins de 3 ans ne comptent pas)
                                    </p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <?php foreach (getAgeList() as $age) : ?>
                                        <li class="list-group-item"><?php echo $age->category . ' : ' . ($age->price === "0" ? "gratuit" : $age->price_disc . " €"); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div> -->

                    <div class="col-12">
                        <label for="comment_mod" class="form-label">Commentaire</label>
                        <textarea id="comment_mod" class="form-control" name="comment" rows="4" placeholder="Votre commentaire..."><?php echo esc_textarea(stripslashes($quote_data->comment)); ?></textarea>
                    </div>

                    <hr class="my-4">

                    <input type="hidden" name="action" value="form_submission">
                    <input type="hidden" name="form_nonce" value="<?php echo wp_create_nonce('form_submit'); ?>">
                    <?php wp_nonce_field('form_submit', 'form_nonce'); ?>

                    <!-- Submit updated form or Return-->
                    <div class="container-fluid">
                        <div class="row justify-content-center">

                            <!-- Return back to the review page -->
                            <div class="p-2 col-md-auto">
                                <a href="<?php echo esc_url(remove_query_arg(array('update', 'form_error', 'quote_id'), "admin.php?page=micro-crm-admin")); ?>" class="btn btn-danger">Annuler</a>
                            </div>

                            <!-- Submit -->
                            <div class="p-2 col-md-auto">
                                <button class="btn btn-success" type="submit" name="submit-btn-quotation">Modifier</button>
                            </div>

                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>