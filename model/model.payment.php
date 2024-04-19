<?php

/**
 * These functions are handling database operations for the table
 * 
 */

function getPaymentList()
{
    global $wpdb;
    $payment_table = $wpdb->prefix . 'payment';

    $sql = $wpdb->prepare("
        SELECT * FROM $payment_table
    ");

    return $wpdb->get_results($sql);
}

// Function to retrieve information by ID
function getPaymentById($payment_id)
{
    global $wpdb;
    $payment_table = $wpdb->prefix . 'payment';

    $sql = $wpdb->prepare("
        SELECT * FROM $payment_table WHERE id = %d
    ", $payment_id);

    return $wpdb->get_row($sql);
}

// Function to insert fixed data
function insertPaymentData($wpdb, $payment_table)
{   
    $categories = array(
        "Virement",
        "CB",
        "Chèque",
        "Espèce"
    );

    // Loop to insert data into columns from arrays
    for ($i = 0; $i < count($categories); $i++) {
        $wpdb->insert(
            $payment_table,
            array(
                'category' => $categories[$i]
            )
        );
    }
}
