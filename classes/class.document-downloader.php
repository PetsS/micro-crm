<?php

/**
 * This Class is responsible for the download of PDF documents
 */

class DocumentDownloader
{
    // Download Quote in PDF Document by quote ID
    public function download_quote_PDF($quote_id, $document_number)
    {
        // Retrieve the PDF content form database based on the quote ID
        // $pdfContent = getPdfdocumentByQuoteId($quote_id)->content;
        // $pdfFilename = getPdfdocumentByQuoteId($quote_id)->filename;

        // Retrieve the PDF content form database based on quote ID and document number
        $pdfContent = getPdfdocumentByQuoteIdAndDocNumber($quote_id, $document_number)->content;
        $pdfFilename = getPdfdocumentByQuoteIdAndDocNumber($quote_id, $document_number)->filename;

        if ($pdfContent !== null || $pdfFilename !== null) {

            // Send appropriate headers
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $pdfFilename . '"');

            // Output the PDF content
            echo $pdfContent;
            exit;
        } else {
            // If PDF file doesn't exist, redirect to the same page
            wp_redirect(add_query_arg(array('error' => $quote_id), wp_get_referer()));
            exit;
        }
    }

    public function download_confirmed_PDF()
    {

        if (isset($_GET['pdf']) && $_GET['pdf'] === 'true') {

            // Load transient for quote id
            $quote_id_transient = get_transient('quote_id_transient');

            if ($quote_id_transient && isset($quote_id_transient)) {
                $quote_id = $quote_id_transient;
                $quote_data = getQuoteDataById($quote_id);
                $document_number = $quote_data->number_quote;

                $this->download_quote_PDF($quote_id, $document_number);
            } else {
                // handle error
                wp_die('no valid pdf', 'Error', array('response' => 403));
            }
            exit;
        }
    }
}
