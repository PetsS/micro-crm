<?php

/**
 * These functions are handling database operations for the table
 * 
 */

function getVisiteTypeList()
{
    global $wpdb;
    $visitetype_table = $wpdb->prefix . 'visitetype';

    $sql = $wpdb->prepare("
        SELECT * FROM $visitetype_table
    ");

    return $wpdb->get_results($sql);
}

// Function to retrieve a single row by ID
function getVisiteTypeById($visitetype_id)
{
    global $wpdb;
    $visitetype_table = $wpdb->prefix . 'visitetype';

    $sql = $wpdb->prepare("
        SELECT * FROM $visitetype_table WHERE id = %d
    ", $visitetype_id);

    return $wpdb->get_row($sql);
}

// Function to insert fixed data
function insertVisiteTypeData($wpdb, $visitetype_table)
{   
    $names = array(
        "Libre",
        "Guidé"
    );

    $references = array(
        "L",
        "VG"
    );

    $prices = array(
        0,
        70
    );

    // Loop to insert data into columns from arrays
    for ($i = 0; $i < count($names); $i++) {
        $wpdb->insert(
            $visitetype_table,
            array(
                'name' => $names[$i],
                'ref' => $references[$i],
                'price' => $prices[$i]
            )
        );
    }
}
