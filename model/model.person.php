<?php

/**
 * These functions are handling database operations for the table
 * 
 */

function getPersonList()
{
    global $wpdb;
    $person_table = $wpdb->prefix . 'person';

    $sql = $wpdb->prepare("
        SELECT * FROM $person_table
    ");

    return $wpdb->get_results($sql);
}

function getPersonByQuoteId($quote_id)
{
    global $wpdb;
    $person_table = $wpdb->prefix . 'person';

    $sql = $wpdb->prepare("
        SELECT * FROM $person_table WHERE quote_id = %d;
    ", $quote_id);

    return $wpdb->get_results($sql);
}

function insertPersonData($quote_id, $age_id, $nbPersons)
{
    global $wpdb;
    $person_table = $wpdb->prefix . 'person';

    $result = $wpdb->insert(
        $person_table,
        array(
            'quote_id' => $quote_id,
            'age_id' => $age_id,
            'nbPersons' => $nbPersons,
        ),
        array(
            '%d', // %d for decimal (integer)
            '%d',
            '%d',
        )
    );

    // Get the ID of the inserted row
    $person_id = $wpdb->insert_id;

    // Check if there was an error in the insert operation
    if (false === $result) {
        // Insertion failed
        // echo "Error: " . $wpdb->last_error;
    } else {
        // Insertion successful
        // echo "Inserted " . $result . " rows.";
        return $person_id;
    }
}

function updatePersonDataByQuoteId($person_id, $quote_id, $age_id, $nbPersons)
{
    global $wpdb;
    $person_table = $wpdb->prefix . 'person';

    $result = $wpdb->update(
        $person_table,
        array(
            'quote_id' => $quote_id,
            'age_id' => $age_id,
            'nbPersons' => $nbPersons,
        ),
        array('id' => $person_id), // Array defining the WHERE clause to identify which rows to update.
        array(
            '%d', // %d for decimal (integer)
            '%d',
            '%d',
        ),
        array('%d') // Array defining the format of the data in the WHERE clause.
    );

    // Check if there was an error in the insert operation
    if (false === $result) {
        // Insertion failed
        // echo "Error: " . $wpdb->last_error;
        return false;
    } else {
        // Insertion successful
        // echo "Inserted " . $result . " rows.";
        return true;
    }
}

/**
 * Delete a person record from the database by ID
 * 
 * @param int $person_id The ID of the person to delete
 * @return bool True on success, false on failure
 */
function deletePersonDataById($person_id)
{
    global $wpdb;
    $person_table = $wpdb->prefix . 'person';

    // Delete the data row from the database
    $result = $wpdb->delete(
        $person_table,
        array('id' => $person_id), // Array defining the WHERE clause to identify which row to delete.
        array('%d')
    );

    // Check if the deletion was successful
    if (false === $result) {
        // Deletion failed
        // echo "Error: " . $wpdb->last_error;
        return false;
    } else {
        // Deletion successful
        // echo "Deleted " . $result . " rows.";
        return true;
    }
}

/**
 * Delete all person records from the database with a specific quote_id
 * 
 * @param int $quote_id The ID of the quote to delete person records for
 * @return bool True on success, false on failure
 */
function deletePersonDataByQuoteId($quote_id)
{
    global $wpdb;
    $person_table = $wpdb->prefix . 'person';

    // Delete the data rows from the database where quote_id matches
    $result = $wpdb->delete(
        $person_table,
        array('quote_id' => $quote_id), // Specify the WHERE clause to identify which rows to delete.
        array('%d')
    );

    // Check if the deletion was successful
    if (false === $result) {
        // Deletion failed
        // echo "Error: " . $wpdb->last_error;
        return false;
    } else {
        // Deletion successful
        // echo "Deleted " . $result . " rows.";
        return true;
    }
}


