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

function getTagnameById($tagname_id)
{
    global $wpdb;
    $tagname_table = $wpdb->prefix . 'tagname';

    $sql = $wpdb->prepare("
        SELECT * FROM $tagname_table WHERE id = %d;
    ", $tagname_id);

    return $wpdb->get_row($sql);
}

function getAvailableTagnameList($quote_id)
{
    global $wpdb;
    $tagname_table = $wpdb->prefix . 'tagname';
    $tag_table = $wpdb->prefix . 'tag';

    $sql = $wpdb->prepare("
        SELECT tn.* 
        FROM $tagname_table tn
        LEFT JOIN $tag_table t ON tn.id = t.tagname_id AND t.quote_id = %d
        WHERE t.id IS NULL
    ", $quote_id);

    return $wpdb->get_results($sql);
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


