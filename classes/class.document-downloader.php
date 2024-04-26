<?php

/**
 * This Class is responsible for the download of PDF documents
 */

class DocumentDownloader
{
    // Download Quote in PDF Document by quote ID
    public function download_quote_PDF($quote_id)
    {
        // Retrieve the PDF content based on the quote ID
        $pdfContent = getPdfdocumentByQuoteId($quote_id)->content;
        $pdfFilename = getPdfdocumentByQuoteId($quote_id)->filename;

        if ($pdfContent !== null || $pdfFilename !== null) {

            // Send appropriate headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $pdfFilename . '"');

            // Output the PDF content
            echo $pdfContent;
            exit;
        } else {
            // If PDF file doesn't exist, redirect to the same page
            wp_redirect(add_query_arg(array('error' => 'pdf'), wp_get_referer()));
            exit;
        }
    }
}
