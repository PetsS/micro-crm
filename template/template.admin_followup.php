<?php
$quote_data = getQuoteDataList(); // load sql method into variable
?>

<div class="container mt-5">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th class="fixed-column">Date</th>
                    <th>Nom ou Raison Social</th>
                    <th>Jour de visite</th>
                    <th>Type de tarif</th>
                    <th>Nombre de personne</th>
                    <th>Numéro Devis</th>
                    <th>Mode paiement</th>
                    <th>Montant total TTC</th>
                    <th>Balises</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quote_data as $quote) : ?>

                    <?php
                    $person_data = getPersonByQuoteId($quote->id); // Load SQL method into variable to recover person data for the current quote

                    $quote_calculator = new QuoteCalculator(); // Instantiate the QuoteCalculator class to use calculated results

                    $results = $quote_calculator->calculateResults($quote, $person_data); // Calculate results: totals, unit prices, references, quantities, etc

                    // Extract results from the returned calculated results for the current quote
                    $total_tva = $results['total_tva'];
                    $total_ht = $results['total_ht'];
                    $total_ttc = $results['total_ttc'];
                    ?>

                    <tr class="main-row" onclick="toggleDetails(this)">
                        <td class="fixed-column"><?php echo $quote->creation_date; ?></td>
                        <!-- <td class="fixed-column"><?php echo date('Y-m-d', strtotime($quote->creation_date)); ?></td> -->
                        <td><?php echo ($quote->companyName ? $quote->companyName  . " - " : "") . $quote->lastname_quot . " " . $quote->firstname_quot; ?></td>
                        <td><?php echo $quote->datetimeVisit; ?></td>
                        <td>???</td>
                        <td>???</td>
                        <td><?php echo $quote->number_quote; ?></td>
                        <td><?php echo getPaymentById($quote->payment_id)->category; ?></td>
                        <td><?php echo $total_ttc; ?></td>
                        <td>???, ???</td>
                    </tr>
                    <tr class="additional-row">
                        <td colspan="13">Additional details here...
                            <p>
                                <span>Type de visite: <?php echo getVisiteTypeById($quote->visitetype_id)->name; ?></span><br>
                                <span>Adresse: <?php echo $quote->address; ?></span><br>
                                <span>Téléphone: <?php echo $quote->phone_quot; ?></span><br>
                            </p>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>