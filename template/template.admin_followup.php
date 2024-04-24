<?php
$quote_data = getQuoteDataList();
?>
<div class="container mt-5">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-dark">
                <tr>
                    <th class="fixed-column">Date</th>
                    <th>Nom ou Raison Social</th>
                    <th>Type de visite</th>
                    <th>Jour de visite</th>
                    <th>Adresse</th>
                    <th>Téléphone</th>
                    <th>Type de tarif</th>
                    <th>Nombre de personne</th>
                    <th>Numéro Devis</th>
                    <th>Mode paiement</th>
                    <th>Montant total HT</th>
                    <th>Montant total TTC</th>
                    <th>Balises</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quote_data as $quote) : ?>
                    <tr class="main-row" onclick="toggleDetails(this)">
                        <td class="fixed-column">???</td>
                        <td><?php echo ($quote->companyName ? $quote->companyName  . " - " : "") . $quote->lastname_quot . " " . $quote->firstname_quot; ?></td>
                        <td><?php echo getVisiteTypeById($quote->visitetype_id)->name; ?></td>
                        <td><?php echo $quote->datetimeVisit; ?></td>
                        <td><?php echo $quote->address; ?></td>
                        <td><?php echo $quote->phone_quot; ?></td>
                        <td>???</td>
                        <td>???</td>
                        <td><?php echo $quote->number_quote; ?></td>
                        <td><?php echo getPaymentById($quote->payment_id)->category; ?></td>
                        <td>???</td>
                        <td>???</td>
                        <td>???, ???</td>
                    </tr>
                    <tr class="additional-row">
                        <td colspan="13">Additional details here...</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>