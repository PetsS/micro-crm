<?php

/**
 * This Class is handling data processing and submission of the tags in admin side.
 */

class TagHandler
{

    public function handle_tag_submission()
    {

        if (isset($_POST['submit-btn-tag']) && isset($_POST['tagselect']) && $_POST['tagselect'] !== 'default') {

            $tagname_id = sanitize_textarea_field($_POST['tagselect']); // Extract and sanitize the selected tag ID
            $quote_id = isset($_POST['quote_id']) ? sanitize_text_field($_POST['quote_id']) : null; // Recover the quote id from a hidden field in the form to pass it to database operations

            // Insert the tag into the database
            insertTagData($quote_id, $tagname_id);

            // Redirect back to the same page
            wp_safe_redirect(wp_get_referer());
            exit;
        }
    }

    public function add_tag_class_bg($tagname_id)
    {
        // Initialize a variable to hold the class string
        $class = '';

        // Use a switch loop to generate different background colors for each case
        switch ($tagname_id) {
            case 1:
                $class = 'badge rounded-pill text-bg-primary';
                break;
            case 2:
                $class = 'badge rounded-pill text-bg-secondary';
                break;
            case 3:
                $class = 'badge rounded-pill text-bg-warning';
                break;
            case 4:
                $class = 'badge rounded-pill text-bg-primary';
                break;
            case 5:
                $class = 'badge rounded-pill text-bg-success';
                break;
            case 6:
                $class = 'badge rounded-pill text-bg-primary';
                break;
            case 7:
                $class = 'badge rounded-pill text-bg-dark';
                break;
            case 8:
                $class = 'badge rounded-pill text-bg-light';
                break;
            case 9:
                $class = 'badge rounded-pill text-bg-warning';
                break;
            case 10:
                $class = 'badge rounded-pill text-bg-success';
                break;
            case 11:
                $class = 'badge rounded-pill text-bg-danger';
                break;
            default:
                $class = 'badge rounded-pill text-bg-light';
                break;
        }

        // Return the class string
        return $class;
    }

    public function delete_tag()
    {

        if (isset($_POST['delete-btn-tag'])) {
            $tag_id = isset($_POST['tag_id']) ? sanitize_text_field($_POST['tag_id']) : null; // Recover the tag id from a hidden field in the form to pass it to database operations

            if ($tag_id) {
                deleteTagDataById($tag_id);
            }

            // Redirect back to the same page
            wp_safe_redirect(wp_get_referer());
            exit;
        }

    }
}
