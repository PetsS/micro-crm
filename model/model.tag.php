<?php

/**
 * These functions are handling database operations for the table
 * 
 */

function getTagList()
{
    global $wpdb;
    $tag_table = $wpdb->prefix . 'tag';

    $sql = $wpdb->prepare("
        SELECT * FROM $tag_table
    ");

    return $wpdb->get_results($sql);
}

function getTagByQuoteId($quote_id)
{
    global $wpdb;
    $tag_table = $wpdb->prefix . 'tag';

    $sql = $wpdb->prepare("
        SELECT * FROM $tag_table WHERE quote_id = %d;
    ", $quote_id);

    return $wpdb->get_results($sql);
}

// This function retrieves the IDs of tags based on a search query provided by the user.
function getTagIdsBySearchQuery($tag_search_query)
{
    global $wpdb;
    $tagname_table = $wpdb->prefix . 'tagname';

    $sql = $wpdb->prepare("
        SELECT id FROM $tagname_table WHERE category LIKE %s
    ", '%' . $wpdb->esc_like($tag_search_query) . '%');

    $tagname_ids = $wpdb->get_col($sql);

    return $tagname_ids;
}

function insertTagData($quote_id, $tagname_id)
{
    global $wpdb;
    $tag_table = $wpdb->prefix . 'tag';

    $result = $wpdb->insert(
        $tag_table,
        array(
            'quote_id' => $quote_id,
            'tagname_id' => $tagname_id
        ),
        array(
            '%d', // %d for decimal (integer)
            '%d'
        )
    );

    // Check if there was an error in the insert operation
    if (false === $result) {
        // Insertion failed
        return false;
    } else {
        // Insertion successful
        // Get the ID of the inserted row
        return $wpdb->insert_id;
    }
}

function updateTagDataByQuoteId($tag_id, $quote_id, $tagname_id)
{
    global $wpdb;
    $tag_table = $wpdb->prefix . 'tag';

    $result = $wpdb->update(
        $tag_table,
        array(
            'quote_id' => $quote_id,
            'tagname_id' => $tagname_id
        ),
        array('id' => $tag_id), // Array defining the WHERE clause to identify which rows to update.
        array(
            '%d', // %d for decimal (integer)
            '%d',
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
 * Delete a tag record from the database by ID
 * 
 * @param int $tag_id The ID of the tag to delete
 * @return bool True on success, false on failure
 */
function deleteTagDataById($tag_id)
{
    global $wpdb;
    $tag_table = $wpdb->prefix . 'tag';

    // Delete the data row from the database
    $result = $wpdb->delete(
        $tag_table,
        array('id' => $tag_id), // Array defining the WHERE clause to identify which row to delete.
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
 * Delete all tag records from the database with a specific quote_id
 * 
 * @param int $quote_id The ID of the quote to delete tag records for
 * @return bool True on success, false on failure
 */
function deleteTagDataByQuoteId($quote_id)
{
    global $wpdb;
    $tag_table = $wpdb->prefix . 'tag';

    // Delete the data rows from the database where quote_id matches
    $result = $wpdb->delete(
        $tag_table,
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


