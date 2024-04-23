<?php

/**
 * These functions are handling database operations for the table
 * 
 */

function getAgeList()
{
    global $wpdb;
    $age_table = $wpdb->prefix . 'age';

    $sql = $wpdb->prepare("
        SELECT * FROM $age_table
    ");

    return $wpdb->get_results($sql);
}

// Function to retrieve age information by ID
function getAgeById($age_id)
{
    global $wpdb;
    $age_table = $wpdb->prefix . 'age';

    $sql = $wpdb->prepare("
        SELECT * FROM $age_table WHERE id = %d
    ", $age_id);

    return $wpdb->get_row($sql);
}

// Function to insert fixed data
function insertAgeData($wpdb, $age_table)
{   
    $categories = array(
        "Gratuit (1 à 2 ans)",
        "Enfant (3 à 12 ans)",
        "Adulte (13 ans et plus)",
        "Tarif réduit (13 ans et plus)",
        "Abonnement annuel (Enfant)",
        "Abonnement annuel (Adulte)",
        "Soigneur d'un jour (Enfant)",
        "Soigneur d'un jour (Adulte)",
        "Soigneur d'un jour (Tarif réduit)"
    );

    $prices = array(
        0,
        6.9,
        9.9,
        7.9,
        15,
        20,
        50,
        70,
        60
    );

    $prices_disc = array(
        0,
        5.9,
        8.9,
        7.9,
        15,
        20,
        50,
        70,
        60
    );

    $references = array(
        "EE-3",
        "EE",
        "EA",
        "TR",
        "AE",
        "AA",
        "SE",
        "SA",
        "STR"
    );

    $references_disc = array(
        "EE-3",
        "GEE",
        "GEA",
        "TR",
        "AE",
        "AA",
        "SE",
        "SA",
        "STR"
    );

    // Loop to insert into columns from arrays
    for ($i = 0; $i < count($categories); $i++) {
        $wpdb->insert(
            $age_table,
            array(
                'category' => $categories[$i],
                'price' => $prices[$i],
                'price_disc' => $prices_disc[$i],
                'ref' => $references[$i],
                'ref_disc' => $references_disc[$i]
            )
        );
    }
}
