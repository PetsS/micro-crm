<?php
// Check if sorting parameter is provided in the URL
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'creation_date'; // Default to sorting by date
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'desc'; // Default to descending order
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : ''; // Define search_query variable
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : ''; // Define start_date variable
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : ''; // Define end_date variable
$tag_search_query = isset($_GET['tag_search_query']) ? $_GET['tag_search_query'] : ''; // Define tag_search_query variable

// Function to generate sorting URL with parameters
function getSortURL($sort_by, $sort_order, $search_query, $start_date, $end_date, $tag_search_query)
{
    // Toggle sort order
    $sort_order = $sort_order === 'asc' ? 'desc' : 'asc';
    return esc_url(add_query_arg(array('sort_by' => $sort_by, 'sort_order' => $sort_order, 'search_query' => $search_query, 'start_date' => $start_date, 'end_date' => $end_date, 'tag_search_query' => $tag_search_query)));
}

// Function to generate sorting class based on current sorting state and add a bootstrap icon to the link
function getSortClass($sort_by, $column_name, $sort_order)
{
    if ($sort_by === $column_name) {
        return $sort_order === 'asc' ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill';
    }
    return '';
}

// Create number formatter instance for currency and decimal formats
$number_currency = new NumberFormatter("fr_FR", NumberFormatter::CURRENCY);
$number_decimal = new NumberFormatter("fr_FR", NumberFormatter::DECIMAL);

// Define the number of decimal places
$number_decimal->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

// Function when submit search button
if (isset($_POST['btn-search'])) {
    // Get the search query from the form submission
    $search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';

    // Get the start and end dates from the form submission
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';

    // Get the tag search query from the form submission
    $tag_search_query = isset($_POST['tag_search_query']) ? $_POST['tag_search_query'] : '';

    $quote_data = getQuoteDataList($search_query, $sort_by, $sort_order, $start_date, $end_date, $tag_search_query);
}

// Function to reset the search conditions
if (isset($_POST['btn-reset'])) {
    esc_url(remove_query_arg(array('sort_by', 'sort_order', 'search_query', 'start_date', 'end_date', 'tag_search_query')));
    // $sort_by = 'creation_date';
    // $sort_order = 'desc';
    $search_query = '';
    $start_date = '';
    $end_date = '';
    $tag_search_query = '';
    $quote_data = getQuoteDataList($search_query, $sort_by, $sort_order, $start_date, $end_date, $tag_search_query);
}

// Sorting conditions
// Choose the appropriate function based on the sorting column
if ($sort_by !== 'total_persons' && $sort_by !== 'total_ttc') {
    // If sorting by other columns, directly call getQuoteDataList with search query and date range
    $quote_data = getQuoteDataList($search_query, $sort_by, $sort_order, $start_date, $end_date, $tag_search_query);
} else {
    // Get query from database by default parameters which will be overwritten by custom function
    $quote_data = getQuoteDataList($search_query, 'creation_date', 'desc', $start_date, $end_date, $tag_search_query);

    // Get calculated total_persons and total_ttc data
    foreach ($quote_data as $quote) {
        $person_data = getPersonByQuoteId($quote->id);
        $quote_calculator = new QuoteCalculator();
        $results = $quote_calculator->calculateResults($quote, $person_data);
        $quote->total_persons = $results['total_persons'];
        $quote->total_ttc = $results['total_ttc'];
    }

    // Define a custom sorting function based on the total_persons or total_ttc property using temporaty array
    usort($quote_data, function ($a, $b) use ($sort_order, $sort_by) {
        if ($sort_order === 'asc') {
            return $a->$sort_by - $b->$sort_by;
        } else {
            return $b->$sort_by - $a->$sort_by;
        }
    });
}

?>
<div class="container mt-5">
    <form action="" method="post">
        <div class="row">
            <div class="col">
                <input type="text" name="search_query" class="form-control" placeholder="Rechercher..." value="<?php echo $search_query ? $search_query : ''; ?>">
            </div>
            <div class="col">
                <input type="date" name="start_date" class="form-control" placeholder="Date de début" value="<?php echo $start_date ? $start_date : ''; ?>">
            </div>
            <div class="col">
                <input type="date" name="end_date" class="form-control" placeholder="Date de fin" value="<?php echo $end_date ? $end_date : ''; ?>">
            </div>
            <div class="col">
                <input type="text" name="tag_search_query" class="form-control" placeholder="Rechercher par balise..." value="<?php echo $tag_search_query ? $tag_search_query : ''; ?>">
            </div>
            <div class="col">
                <button type="submit" name="btn-search" class="btn btn-primary">Recherche</button>
                <button type="submit" name="btn-reset" class="btn btn-secondary">Réinitialiser</button>
            </div>
        </div>
    </form>
</div>


<div class="container mt-3">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th>
                        <a href="<?php echo getSortURL('lastname_quot', $sort_order, $search_query, $start_date, $end_date, $tag_search_query); ?>">Nom <i class="<?php echo getSortClass('lastname_quot', $sort_by, $sort_order); ?>"></i></a>
                        <span>ou </span>
                        <a href="<?php echo getSortURL('companyName', $sort_order, $search_query, $start_date, $end_date, $tag_search_query); ?>">RS <i class="<?php echo getSortClass('companyName', $sort_by, $sort_order); ?>"></i></a>
                    </th>
                    <th class="fixed-column"><a href="<?php echo getSortURL('creation_date', $sort_order, $search_query, $start_date, $end_date, $tag_search_query); ?>">Date <i class="<?php echo getSortClass('creation_date', $sort_by, $sort_order); ?>"></i></a></th>
                    <th><a href="<?php echo getSortURL('number_quote', $sort_order, $search_query, $start_date, $end_date, $tag_search_query); ?>">No Devis <i class="<?php echo getSortClass('number_quote', $sort_by, $sort_order); ?>"></i></a></th>
                    <th>No Facture </th>
                    <th>No Avoir </th>
                    <th><a href="<?php echo getSortURL('datetimeVisit', $sort_order, $search_query, $start_date, $end_date, $tag_search_query); ?>">Jour de visite <i class="<?php echo getSortClass('datetimeVisit', $sort_by, $sort_order); ?>"></i></a></th>
                    <th><a href="<?php echo getSortURL('visitetype_id', $sort_order, $search_query, $start_date, $end_date, $tag_search_query); ?>">Type de visite <i class="<?php echo getSortClass('visitetype_id', $sort_by, $sort_order); ?>"></i></a></th>
                    <th><a href="<?php echo getSortURL('total_persons', $sort_order, $search_query, $start_date, $end_date, $tag_search_query); ?>">Nb personnes <i class="<?php echo getSortClass('total_persons', $sort_by, $sort_order); ?>"></i></a></th>
                    <th><a href="<?php echo getSortURL('payment_id', $sort_order, $search_query, $start_date, $end_date, $tag_search_query); ?>">Mode paiement <i class="<?php echo getSortClass('payment_id', $sort_by, $sort_order); ?>"></i></a></th>
                    <th><a href="<?php echo getSortURL('total_ttc', $sort_order, $search_query, $start_date, $end_date, $tag_search_query); ?>">Total TTC <i class="<?php echo getSortClass('total_ttc', $sort_by, $sort_order); ?>"></i></a></th>
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

                    ?>

                    <tr class="main-row" onclick="toggleDetails(this)">
                        <td class="<?php echo $sort_by === 'companyName' || $sort_by === 'lastname_quot' ? 'table-light' : ''; ?>"><?php echo ($quote->companyName ? strtoupper($quote->companyName)  :  $quote->firstname_quot . " " . strtoupper($quote->lastname_quot)); ?></td>
                        <td class="fixed-column <?php echo $sort_by === 'creation_date' ? 'table-light' : ''; ?>"><?php echo date('Y-m-d', strtotime($quote->creation_date)); ?></td>
                        <td class="<?php echo $sort_by === 'number_quote' ? 'table-light' : ''; ?>"><?php echo $quote->number_quote; ?></td>
                        <td><?php echo "???"; ?></td>
                        <td><?php echo "???"; ?></td>
                        <td class="<?php echo $sort_by === 'datetimeVisit' ? 'table-light' : ''; ?>"><?php echo $quote->datetimeVisit; ?></td>
                        <td class="<?php echo $sort_by === 'visitetype_id' ? 'table-light' : ''; ?>"><?php echo getVisiteTypeById($quote->visitetype_id)->name; ?></td>
                        <td class="<?php echo $sort_by === 'total_persons' ? 'table-light' : ''; ?>"><?php echo $total_persons; ?></td>
                        <td class="<?php echo $sort_by === 'payment_id' ? 'table-light' : ''; ?>"><?php echo getPaymentById($quote->payment_id)->category; ?></td>
                        <td class="<?php echo $sort_by === 'total_ttc' ? 'table-light' : ''; ?>"><?php echo $number_currency->format($total_ttc); ?></td>
                        <td>

                            <?php foreach (getTagByQuoteId($quote->id) as $tag) : ?>
                                <?php
                                $tag_handler = new TagHandler();
                                $add_tag_bg = $tag_handler->add_tag_class_bg($tag->tagname_id);
                                ?>

                                <!-- A mini form to handle tag display and deletion -->
                                <form class="pb-1 delete-tag-form" method="post">
                                    <span class="py-0 <?php echo $add_tag_bg; ?> fw-normal">
                                        <?php echo getTagnameById($tag->tagname_id)->category; ?>
                                        <button class="p-0 ps-1 btn align-baseline" type="submit" name="delete-btn-tag" value="<?php echo $tag->id; ?>">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </span>
                                    <!-- Hidden input field to pass the tag_id to the form handling-->
                                    <input type="hidden" name="tag_id" value="<?php echo $tag->id; ?>">
                                </form>

                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr class="additional-row">
                        <td colspan="11">
                            <div class="p-3 bg-light rounded box-shadow">

                                <h6 class="border-bottom border-gray pb-2 mb-0">
                                    <div class="row row-cols-lg-auto g-2 align-items-center">
                                        <!-- Send email -->
                                        <div class="col-12">
                                            <a href="mailto:<?php echo $quote->email_quot; ?>" class="btn btn-primary">
                                                <i class="bi bi-envelope"></i> Email
                                            </a>
                                        </div>
                                        <!-- Call the phone -->
                                        <div class="col-12">
                                            <a href="tel:<?php echo $quote->phone_quot; ?>" class="btn btn-primary">
                                                <i class="bi bi-telephone"></i> Appel
                                            </a>
                                        </div>
                                        <!-- Download quotation in PDF -->
                                        <div class="col-12">
                                            <?php if (isset($_GET['error']) && $_GET['error'] === $quote->id) : ?>
                                                <button type="button" class="btn btn-outline-danger" disabled>
                                                    <i class="bi bi-x-circle-fill"></i> Devis non accessible
                                                </button>
                                            <?php else : ?>
                                                <a href="<?php echo esc_url(add_query_arg('pdf_quote', $quote->id ?? null)); ?>" class="btn btn-outline-danger">
                                                    <i class="bi bi-file-earmark-pdf"></i> Télécharger Devis
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Select and add a Tag -->
                                        <div class="col-4">
                                            <form class="input-group" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
                                                <select class="form-select" id="tagselect" name="tagselect">
                                                    <option value="default">Ajouter des balises</option>
                                                    <?php foreach (getAvailableTagnameList($quote->id) as $tagname) : ?>
                                                        <option value="<?php echo $tagname->id ?>"><?php echo $tagname->category ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <!-- Hidden input field to store the quote_id -->
                                                <input type="hidden" name="quote_id" value="<?php echo $quote->id; ?>">
                                                <button class="btn btn-primary" type="submit" name="submit-btn-tag">OK</button>
                                            </form>
                                        </div>
                                    </div>
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
        <!-- Display alert message if the table has no search results -->
        <?php if (empty($quote_data)) : ?>
            <div class="alert alert-danger" role="alert">Aucun résultat de recherche!</div>
        <?php endif; ?>
    </div>
</div>