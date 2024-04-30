<?php

/**
 * These functions are handling database operations for the table
 * 
 */

// Method to get all quotation data from the database with optional search query
function getQuoteDataList($search_query = '', $sort_by = 'creation_date', $sort_order = 'desc', $start_date = '', $end_date = '')
{
    global $wpdb;
    $quote_table = $wpdb->prefix . 'quote';

    // Construct the SQL query
    $sql = "SELECT * FROM $quote_table";

    // Append the WHERE clause if any of the filters are provided
    $where_conditions = array();

    // Search query condition
    if (!empty($search_query)) {
        $search_conditions = array();
        $columns = array('email_quot', 'lastname_quot', 'firstname_quot', 'companyName', 'address', 'phone_quot', 'visitetype_id', 'datetimeVisit', 'payment_id', 'comment', 'number_quote');
        foreach ($columns as $column) {
            $search_conditions[] = "$column LIKE '%$search_query%'";
        }
        $where_conditions[] = "(" . implode(" OR ", $search_conditions) . ")";
    }

    // Start date condition
    if (!empty($start_date)) {
        $start_date_formatted = date("Y-m-d", strtotime($start_date));
        $where_conditions[] = "creation_date >= STR_TO_DATE('$start_date_formatted', '%Y-%m-%d')";
    }

    // End date condition
    if (!empty($end_date)) {
        $end_date_formatted = date("Y-m-d", strtotime($end_date));
        $where_conditions[] = "creation_date <= DATE_ADD(STR_TO_DATE('$end_date_formatted', '%Y-%m-%d'), INTERVAL 1 DAY)";
    }

    // Combine all WHERE conditions
    if (!empty($where_conditions)) {
        $sql .= " WHERE " . implode(" AND ", $where_conditions);
    }

    // Append sorting criteria
    $sql .= " ORDER BY $sort_by $sort_order";

    // Retrieve data from the database
    $results = $wpdb->get_results($sql);

    // Return the results
    return $results ? $results : [];
}



// Method to get all quotation data from the database sorted by the specified column
// function getQuoteDataListSortedByColumn($sort_by, $sort_order)
// {
//     global $wpdb;
//     $quote_table = $wpdb->prefix . 'quote';

//     // Retrieve all rows from the quote table sorted by the specified column and order
//     $sql = $wpdb->get_results("SELECT * FROM $quote_table ORDER BY $sort_by $sort_order");

//     // Check if there was an error in the retrieval operation
//     if (!$sql) {
//         // Retrieval failed
//         return false;
//     } else {
//         // Retrieval successful
//         return $sql;
//     }
// }

// Method to get quotation data by quote_id from the database
function getQuoteDataById($quote_id)
{
    global $wpdb;
    $quote_table = $wpdb->prefix . 'quote';

    // Retrieve the row with the specified quote_id from the quote table
    $sql = $wpdb->get_row($wpdb->prepare("SELECT * FROM $quote_table WHERE id = %d", $quote_id));

    // Check if there was an error in the retrieval operation
    if (!$sql) {
        // Retrieval failed
        return false;
    } else {
        // Retrieval successful
        return $sql;
    }
}

// Method to insert all quotation data to the database and return the ID of the inserted row
function insertQuoteData($email_quot, $lastname_quot, $firstname_quot, $companyName, $address, $phone_quot, $visitetype_id, $datetimeVisit, $payment_id, $comment)
{
    global $wpdb;
    $quote_table = $wpdb->prefix . 'quote';

    // Format datetimeVisit into the MySQL datetime format
    $formattedDateTimeVisit = date('Y-m-d H:i:s', strtotime($datetimeVisit));

    // Generate a dynamic formatted number
    $number_quote = generateQuoteNumber();

    // Insert quotation data into database
    $result = $wpdb->insert(
        $quote_table,
        array(
            'email_quot' => $email_quot,
            'lastname_quot' => $lastname_quot,
            'firstname_quot' => $firstname_quot,
            'companyName' => $companyName,
            'address' => $address,
            'phone_quot' => $phone_quot,
            'visitetype_id' => $visitetype_id,
            'datetimeVisit' => $formattedDateTimeVisit,
            'payment_id' => $payment_id,
            'comment' => $comment,
            'number_quote' => $number_quote
        ),
        array(
            '%s', // %s for string
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d', // %d for decimal (integer)
            '%s', // %s for datetime
            '%d',
            '%s',
            '%s',
        )
    );

    // Get the ID of the inserted row
    $quote_id = $wpdb->insert_id;

    // Check if there was an error in the insert operation
    if (false === $result) {
        // Insertion failed
    } else {
        // Insertion successful
        return $quote_id;
    }
}

// Method to update quotation data in the database
function updateQuoteData($quote_id, $email_quot, $lastname_quot, $firstname_quot, $companyName, $address, $phone_quot, $visitetype_id, $datetimeVisit, $payment_id, $comment)
{
    global $wpdb;
    $quote_table = $wpdb->prefix . 'quote';

    // Format datetimeVisit into the MySQL datetime format
    $formattedDateTimeVisit = date('Y-m-d H:i:s', strtotime($datetimeVisit));

    // Update quotation data in the database
    $result = $wpdb->update(
        $quote_table, // What table
        array(
            'email_quot' => $email_quot,
            'lastname_quot' => $lastname_quot,
            'firstname_quot' => $firstname_quot,
            'companyName' => $companyName,
            'address' => $address,
            'phone_quot' => $phone_quot,
            'visitetype_id' => $visitetype_id,
            'datetimeVisit' => $formattedDateTimeVisit,
            'payment_id' => $payment_id,
            'comment' => $comment,
        ),
        array('id' => $quote_id), // Array defining the WHERE clause to identify which rows to update.
        array(
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
            '%d',
            '%s'
        ),
        array('%d') // Array defining the format of the data in the WHERE clause.
    );

    // Check if there was an error in the update operation
    if (false === $result) {
        // Update failed
        return false;
    } else {
        // Update successful
        return true;
    }
}


// Method to delete data from the database
function deleteQuoteData($quote_id)
{
    global $wpdb;
    $quote_table = $wpdb->prefix . 'quote';

    // Delete the data row from the database
    $result = $wpdb->delete(
        $quote_table,
        array('id' => $quote_id), // Array defining the WHERE clause to identify which row to delete.
        array('%d')
    );

    // Check if there was an error in the delete operation
    if (false === $result) {
        // Deletion failed
        return false;
    } else {
        // Deletion successful
        return true;
    }
}

function generateQuoteNumber()
{
    global $wpdb;
    $quote_table = $wpdb->prefix . 'quote';

    // Get the current year and month
    $current_year = date('y');
    $current_month = date('m');
    $current_day = date('d');

    // Check if there are any quotes for the current month of the year
    $last_quote_in_day = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT number_quote 
            FROM $quote_table 
            WHERE SUBSTRING_INDEX(number_quote, '-', 4) = %s 
            ORDER BY number_quote 
            DESC LIMIT 1"
            , "D-$current_year-$current_month-$current_day"
        )
    ); // This condition checks if the first 4 parts of the number_quote string (ex. D-24-04-26), separated by -, are equal to the current year and month and day

    // If there are no quotes for the current day, set the number_quote to '01'
    if (!$last_quote_in_day) {
        $number_quote = 'D-' . $current_year . '-' . $current_month . '-' . $current_day . '-01';
    } else {
        // Extract the ID part from the last quote number
        $last_id = intval(substr($last_quote_in_day, strrpos($last_quote_in_day, '-') + 1)); // extracts the portion of the string after the last hyphen and converts it into an integer

        // Increment the last ID by 1
        $new_id = str_pad($last_id + 1, 2, '0', STR_PAD_LEFT);

        // Generate the new number_quote
        $number_quote = 'D-' . $current_year . '-' . $current_month . '-' . $current_day . '-' . $new_id;
    }

    return $number_quote;
}
