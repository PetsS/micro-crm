<?php

/**
 * These functions are handling database operations for the table
 * 
 */

function getTagnameList()
{
    global $wpdb;
    $tagname_table = $wpdb->prefix . 'tagname';

    $sql = $wpdb->prepare("
        SELECT * FROM $tagname_table
    ");

    return $wpdb->get_results($sql);
}

function getTagnameByQuoteId($quote_id)
{
    global $wpdb;
    $tagname_table = $wpdb->prefix . 'tagname';

    $sql = $wpdb->prepare("
        SELECT * FROM $tagname_table WHERE quote_id = %d;
    ", $quote_id);

    return $wpdb->get_row($sql);
}

// Function to insert fixed data
function insertTagnameData($wpdb, $tagname_table)
{
    $categories = array(
        "Terminé",
        "En Cours",
        "Devis à modifier",
        "Devis envoyé",
        "Devis signé",
        "Facture envoyée",
        "Facture annulée",
        "Facture acquittée",
        "Paiement non reçu",
        "Paiement reçu",
        "En attente action",
    );

    // Loop to insert into columns from arrays
    for ($i = 0; $i < count($categories); $i++) {
        $wpdb->insert(
            $tagname_table,
            array(
                'category' => $categories[$i],
            )
        );
    }
}


