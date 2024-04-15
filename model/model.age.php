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
        "Tarif réduit (13 ans et plus)"
    );

    $prices = array(
        0,
        6.5,
        9.5,
        7.5
    );

    // Loop to insert into columns from arrays
    for ($i = 0; $i < count($categories); $i++) {
        $wpdb->insert(
            $age_table,
            array(
                'category' => $categories[$i],
                'price' => $prices[$i]
            )
        );
    }
}
