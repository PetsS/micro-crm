<?php

/**
 * These functions are handling database operations for the table
 * 
 */

function getPdfdocumentList()
{
    global $wpdb;
    $pdfdocument_table = $wpdb->prefix . 'pdfdocument';

    $sql = $wpdb->prepare("
        SELECT * FROM $pdfdocument_table
    ");

    return $wpdb->get_results($sql);
}

function getPdfdocumentByQuoteId($quote_id)
{
    global $wpdb;
    $pdfdocument_table = $wpdb->prefix . 'pdfdocument';

    $sql = $wpdb->prepare("
        SELECT * FROM $pdfdocument_table WHERE quote_id = %d;
    ", $quote_id);

    return $wpdb->get_row($sql);
}

function insertPdfdocumentData($quote_id, $filename, $content)
{
    global $wpdb;
    $pdfdocument_table = $wpdb->prefix . 'pdfdocument';

    $result = $wpdb->insert(
        $pdfdocument_table,
        array(
            'quote_id' => $quote_id,
            'filename' => $filename,
            'content' => $content,
        ),
        array(
            '%d', // %d for decimal (integer)
            '%s',
            '%s',
        )
    );

    // Get the ID of the inserted row
    $pdfdocument_id = $wpdb->insert_id;

    // Check if there was an error in the insert operation
    if (false === $result) {
        // Insertion failed
    } else {
        // Insertion successful
        return $pdfdocument_id;
    }
}

function updatePdfdocumentDataByQuoteId($pdfdocument_id, $quote_id, $filename, $content)
{
    global $wpdb;
    $pdfdocument_table = $wpdb->prefix . 'pdfdocument';

    $result = $wpdb->update(
        $pdfdocument_table,
        array(
            'quote_id' => $quote_id,
            'filename' => $filename,
            'content' => $content,
        ),
        array('id' => $pdfdocument_id), // Array defining the WHERE clause to identify which rows to update.
        array(
            '%d',
            '%s',
            '%s',
        ),
        array('%d') // Array defining the format of the data in the WHERE clause.
    );

    // Check if there was an error in the insert operation
    if (false === $result) {
        // Insertion failed
        return false;
    } else {
        // Insertion successful
        return true;
    }
}

/**
 * Delete a pdfdocument record from the database by ID
 * 
 * @param int $pdfdocument_id The ID of the pdfdocument to delete
 * @return bool True on success, false on failure
 */
function deletePdfdocumentDataById($pdfdocument_id)
{
    global $wpdb;
    $pdfdocument_table = $wpdb->prefix . 'pdfdocument';

    // Delete the data row from the database
    $result = $wpdb->delete(
        $pdfdocument_table,
        array('id' => $pdfdocument_id), // Array defining the WHERE clause to identify which row to delete.
        array('%d')
    );

    // Check if the deletion was successful
    if (false === $result) {
        // Deletion failed
        return false;
    } else {
        // Deletion successful
        return true;
    }
}

/**
 * Delete all pdfdocument records from the database with a specific quote_id
 * 
 * @param int $quote_id The ID of the quote to delete pdfdocument records for
 * @return bool True on success, false on failure
 */
function deletePdfdocumentDataByQuoteId($quote_id)
{
    global $wpdb;
    $pdfdocument_table = $wpdb->prefix . 'pdfdocument';

    // Delete the data rows from the database where quote_id matches
    $result = $wpdb->delete(
        $pdfdocument_table,
        array('quote_id' => $quote_id), // Specify the WHERE clause to identify which rows to delete.
        array('%d')
    );

    // Check if the deletion was successful
    if (false === $result) {
        // Deletion failed
        return false;
    } else {
        // Deletion successful
        return true;
    }
}


