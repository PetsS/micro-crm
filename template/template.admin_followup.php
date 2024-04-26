<?php
$quote_data = getQuoteDataList(); // load sql method into variable

// Create number formatter instance for currency and decimal formats
$number_currency = new NumberFormatter("fr_FR", NumberFormatter::CURRENCY);
$number_decimal = new NumberFormatter("fr_FR", NumberFormatter::DECIMAL);

// Define the number of decimal places
$number_decimal->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);
?>

<div class="container mt-5">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th class="fixed-column">Date</th>
                    <th>Nom / RS</th>
                    <th>No Devis</th>
                    <th>Jour de visite</th>
                    <th>Nb personnes</th>
                    <th>Mode paiement</th>
                    <th>Total TTC</th>
                    <th>Balises</th>
                </tr>
            </thead>
            <tbody>
                <!-- Iterate through all the quotes in the database -->
                <?php foreach ($quote_data as $quote) : ?>
                    <?php
                    $person_data = getPersonByQuoteId($quote->id); // Load SQL method into variable to recover person data for the current quote

                    $quote_calculator = new QuoteCalculator(); // Instantiate the QuoteCalculator class to use calculated results

                    $results = $quote_calculator->calculateResults($quote, $person_data); // Call function in calculator class

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

                    if (isset($_GET['pdf_quote']) && ($_GET['pdf_quote']) === ($quote->id)) {

                        $documentDownloader = new DocumentDownloader();

                        // Call download_quote_PDF method to download the PDF
                        $documentDownloader->download_quote_PDF($quote->id);
                    }

                    if (isset($_GET['error']) && $_GET['error'] === 'pdf') {
                    }

                    ?>

                    <tr class="main-row" onclick="toggleDetails(this)">
                        <td class="fixed-column"><?php echo date('Y-m-d', strtotime($quote->creation_date)); ?></td>
                        <td><?php echo ($quote->companyName ? strtoupper($quote->companyName)  :  $quote->firstname_quot . " " . strtoupper($quote->lastname_quot)); ?></td>
                        <td><?php echo $quote->number_quote; ?></td>
                        <td><?php echo $quote->datetimeVisit; ?></td>
                        <td><?php echo $total_persons; ?></td>
                        <td><?php echo getPaymentById($quote->payment_id)->category; ?></td>
                        <td><?php echo $number_currency->format($total_ttc); ?></td>
                        <td>
                            <span class="badge rounded-pill bg-primary">Primary</span>
                            <span class="badge rounded-pill bg-secondary">Secondary</span>
                            <span class="badge rounded-pill bg-success">Success</span>
                            <span class="badge rounded-pill bg-danger">Danger</span>
                            <span class="badge rounded-pill bg-warning text-dark">Warning</span>
                            <span class="badge rounded-pill bg-info text-dark">Info</span>
                            <span class="badge rounded-pill bg-dark">Dark</span>
                        </td>
                    </tr>
                    <tr class="additional-row">
                        <td colspan="10">
                            <div class="p-3 bg-light rounded box-shadow">
                                <h6 class="border-bottom border-gray pb-2 mb-0">
                                    <small class="d-block text-right">
                                        <a href="mailto:<?php echo $quote->email_quot; ?>" class="btn btn-primary">
                                            <i class="pe-2 bi bi-envelope"></i>Email
                                        </a>
                                        <a href="tel:<?php echo $quote->phone_quot; ?>" class="btn btn-primary">
                                            <i class="pe-2 bi bi-telephone"></i>Appel
                                        </a>
                                        <?php if (isset($_GET['error']) && $_GET['error'] === 'pdf') : ?>
                                            <button type="button" class="btn btn-outline-danger" disabled>
                                                <i class="pe-2 bi bi-x-circle-fill"></i>Télécharger Devis
                                            </button>
                                        <?php else : ?>
                                            <a href="<?php echo esc_url(add_query_arg('pdf_quote', $quote->id ?? null, wp_get_referer())); ?>" class="btn btn-outline-danger">
                                                <i class="pe-2 bi bi-file-earmark-pdf"></i>Télécharger Devis
                                            </a>
                                        <?php endif; ?>
                                    </small>
                                </h6>
                                <div class="row">
                                    <div class="col-sm">
                                        <div class="media text-muted pt-3">
                                            <div class="media-body mb-0 small lh-125">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <strong class="text-gray-dark"><?php echo ($quote->companyName ? strtoupper($quote->companyName)  . " - " : "") . $quote->firstname_quot . " " . strtoupper($quote->lastname_quot); ?></strong>
                                                </div>
                                                <span class="d-block"><?php echo $quote->address; ?></span>
                                                <span class="d-block"><?php echo $quote->phone_quot; ?></span>
                                                <span class="d-block"><?php echo $quote->email_quot; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="media text-muted pt-3">
                                            <div class="media-body mb-0 ps-3 small lh-125 border-start border-gray">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <table class="table-sm table-borderless">
                                                        <thead>
                                                            <tr>
                                                                <th>Désignation</th>
                                                                <th class="px-3 text-center">Quantité</th>
                                                                <th class="px-3">HT</th>
                                                                <th>TTC</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if ($quote->visitetype_id === "2") : ?>
                                                                <tr>
                                                                    <td class="py-0"><?php echo "Visite " . getVisiteTypeById($quote->visitetype_id)->name; ?></td>
                                                                    <td class="px-3 py-0 text-center fst-italic"><span><?php echo "(" . $guided_qty . " guide" . ($guided_qty > 1 ? "s" : "") . ")"; ?></span></td>
                                                                    <td class="px-3 py-0"><?php echo $number_currency->format($guided_amount_ht); ?></td>
                                                                    <td class="py-0"><?php echo $number_currency->format($guided_amount_ttc); ?></td>
                                                                </tr>
                                                            <?php endif; ?>
                                                            <?php foreach ($person_data as $index => $person) : ?>
                                                                <tr>
                                                                    <td class="py-0"><?php echo getAgeById($person->age_id)->category; ?></td>
                                                                    <td class="py-0 px-3 text-center"><?php echo $person->nbPersons; ?></td>
                                                                    <td class="py-0 px-3"><?php echo $number_currency->format($amount_ht[$index]); ?></td>
                                                                    <td class="py-0"><?php echo $number_currency->format($amount_ttc[$index]); ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                            <?php if ($total_free_persons > 0) : ?>
                                                                <tr>
                                                                    <td class="py-0">Adulte gratuit</td>
                                                                    <td class="py-0 px-3 text-center"><?php echo $total_free_persons; ?></td>
                                                                    <td class="py-0 px-3"><?php echo $number_currency->format($discount_amount_ht); ?></td>
                                                                    <td class="py-0"><?php echo $number_currency->format($discount_amount_ttc); ?></td>
                                                                </tr>
                                                            <?php endif; ?>
                                                            <tr class="border-top">
                                                                <td></td>
                                                                <td class="px-3 fw-bolder text-center"><?php echo $total_persons; ?></td>
                                                                <td class="px-3 fw-bolder"><?php echo $number_currency->format($total_ht); ?></td>
                                                                <td class="fw-bolder"><?php echo $number_currency->format($total_ttc); ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm">
                                        <div class="media text-muted pt-3">
                                            <div class="media-body mb-0 ps-3 small lh-125 border-start border-gray">
                                                <div class="d-flex justify-content-between align-items-center w-100">
                                                    <strong class="text-gray-dark">Commentaires:</strong>
                                                </div>
                                                <?php if (!$quote->comment) : ?>
                                                    <span class="d-block fst-italic">Aucun commentaire</span>
                                                <?php else : ?>
                                                    <span class="d-block"><?php echo $quote->comment; ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>