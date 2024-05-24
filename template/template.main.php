<?php

$isSuccess = false; // Initialize to false by default

// Check if session is started and destroy session for non-logged-in users
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (is_user_logged_in()) {
    $user_id = get_current_user_id();
    $user_key = 'user_' . $user_id;
} else {
    // Use session ID for non-logged-in users
    if (!isset($_SESSION['user_key'])) {
        $_SESSION['user_key'] = 'user_' . session_id();
    }
    $user_key = $_SESSION['user_key'];
}

// Retrieve the transient data using the user-specific key
$form_data_transient = get_transient($user_key . '_form_data_transient');

// Populate form fields and errors with transient data if available
if ($form_data_transient) {
    if (isset($form_data_transient['form_data'])) {
        $form_data = $form_data_transient['form_data'];
    }
    if (isset($form_data_transient['form_errors'])) {
        $form_errors = $form_data_transient['form_errors'];
    }
    if (isset($form_data_transient['isSuccess'])) {
        $isSuccess = $form_data_transient['isSuccess'];
    }
}

$isUpdated = isset($_GET['update']) && $_GET['update'] === 'true'; // If the confirm URL parameter is set and true, it shows the confirmed template.
$isConfirmed = isset($_GET['confirm']) && $_GET['confirm'] === 'true'; // If the confirm URL parameter is set and true, it shows the confirmed template.
$isCanceled = isset($_GET['cancel']) && $_GET['cancel'] === 'true'; // If the cancel URL parameter is set and true, it shows the canceled template.
$isQuestionSent = isset($_GET['question']) && $_GET['question'] === 'true'; // If the question URL parameter is set and true, it shows the question_sent template.

if (!$isCanceled) {
    if (!$isQuestionSent) {
        if (!$isConfirmed) {
            if (!$isUpdated) { // Condition for the if the update action is set
                if (!$isSuccess) {
                    include_once(plugin_dir_path(__FILE__) . 'template.form.php');
                } else {
                    include_once(plugin_dir_path(__FILE__) . 'template.confirm.php');
                }
            } else {
                include_once(plugin_dir_path(__FILE__) . 'template.update.php');
            }
        } else {
            include_once(plugin_dir_path(__FILE__) . 'template.confirmed.php');
        }
    } else {
        include_once(plugin_dir_path(__FILE__) . 'template.question_sent.php');
    }
} else {
    include_once(plugin_dir_path(__FILE__) . 'template.canceled.php');
}