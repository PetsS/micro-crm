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
                        <td class="fixed-column">2024-04-23</td>
                        <td>Company A</td>
                        <td>Meeting</td>
                        <td>Monday</td>
                        <td>123 Main St</td>
                        <td>123-456-7890</td>
                        <td>Normal</td>
                        <td>2</td>
                        <td><?php echo $quote->number_quote; ?></td>
                        <td>Credit Card</td>
                        <td>$200</td>
                        <td>$220</td>
                        <td>Tag1, Tag2</td>
                    </tr>
                    <tr class="additional-row">
                        <td colspan="13">Additional details here...</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>