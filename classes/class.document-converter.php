<?php

/**
 * This Class is responsible for the conversion from HTML to PDF using TCPDF Library
 */

// Start output buffering
ob_start();

// Extend TCPDF class to customize footer
class CustomTCPDF extends TCPDF
{
    // Footer
    public function Footer()
    {
        // Set font
        $this->SetFont('helvetica', '', 6);

        // Add divider line
        $this->SetLineStyle(array('width' => 0.1, 'color' => array(0, 0, 0)));
        $this->Line($this->GetX(), $this->GetY(), $this->getPageWidth() - $this->GetX(), $this->GetY());

        // Add page number
        $this->Cell(0, 10, $this->getAliasNumPage() . ' / ' . $this->getNumPages(), 0, false, 'L', 0, '', 0, false, 'T', 'M');

        // Set position for custom footer content
        $this->SetY(-30);

        ob_start();
        // Read HTML content from the template file
        include(plugin_dir_path(__FILE__) . '../template/template.footer_pdf.php');
        $htmlFooter = ob_get_clean();

        // Add custom footer content
        $this->writeHTML($htmlFooter, true, false, true, false, 'L');
    }
}

class DocumentConverter
{
    // Define a class properties
    // private $quote_data;
    private $form_data;
    private $pdfFileName;
    // private $quote_id;

    public function __construct()
    {
        // Retrieve data from wordpress transient which is used to store data for a limited time to pass it
        $form_data_transient = get_transient('form_data_transient');
        // $quote_id_transient = get_transient('quote_id_transient');

        // Populate form fields with transient data if available
        if ($form_data_transient && isset($form_data_transient['form_data'])) {
            $this->form_data = $form_data_transient['form_data'];
            $this->pdfFileName = $this->generatePdfFileName();
        }

        // Retrieve quote ID from transient if available
        // if ($quote_id_transient && isset($quote_id_transient)) {
        //     $this->quote_id = $quote_id_transient;
        // }

        // Retreive one row of quote data from database
        // $this->quote_data = getQuoteDataById($this->quote_id);

        // Retrieve quote ID from transient if available
        // if ($form_data_transient && isset($form_data_transient['quote_id'])) {
        //     $quote_id = $form_data_transient['quote_id'];
        //     $quote_data = getQuoteDataById($quote_id);
        //     // Verify that quote data is valid
        //     if ($quote_id) {
        //         // Generate dynamic PDF file name
        //         $this->pdfFileName = $this->generatePdfFileName($quote_data);
        //     }
        // }

    }

    // convert html to pdf using TCPDF
    public function convert_html_to_pdf($quote_id)
    {
        $quote_data = getQuoteDataById($quote_id); // Retrieve quote data by ID
        $document_number = $quote_data->number_quote; // Retreive the actual quote number
        $pdfFileName = $this->pdfFileName;

        // Create a new PDF document
        $pdf = new CustomTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, false);

        // Set document information
        $pdf->SetCreator('MicroZoo');
        $pdf->SetAuthor('MicroZoo');
        $pdf->SetTitle('Devis : ' . (!empty($quote_data->companyName) ? ($quote_data->companyName) : ($quote_data->firstname_quot . '_' . $quote_data->lastname_quot)));
        $pdf->SetSubject($quote_data->number_quote); // generate the quote number by calling function from quote model
        $pdf->SetKeywords('Devis, MicroZoo');

        // Set margins
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 30);

        // Add a page
        $pdf->AddPage();

        ob_start();
        // Read HTML content from the template file
        include(plugin_dir_path(__FILE__) . '../template/template.quotation.php');
        $html = ob_get_clean(); // Get the contents of the output buffer and clean it

        // Convert HTML to PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // Path to save the PDF
        $pdfPath = plugin_dir_path(__FILE__) . '../src/save/' . $pdfFileName;

        // Empty the save folder before saving the PDF
        $this->deletePdfFilesInSaveFolder();

        // Convert HTML to PDF and get the PDF content as a string
        $content = $pdf->Output('', 'S');

        // Save the PDF content to a file
        file_put_contents($pdfPath, $content);

        // Save the PDF content to the database
        insertPdfdocumentData($quote_id, $document_number, $pdfFileName, $content); // call insert function in model

    }

    // public function convert_pdf_save_redirect()
    // {
    //     $pdfFileName = $this->pdfFileName;

    //     if (isset($_GET['pdf']) && $_GET['pdf'] === 'true') {

    //         $this->convert_html_to_pdf();

    //         // Redirect the user to a new page where the PDF is displayed in a new tab
    //         $pdfUrl = plugin_dir_url(__FILE__) . '../src/save/' . $pdfFileName; // Get the URL of the PDF file

    //         // Redirection
    //         wp_redirect(esc_url($pdfUrl));
    //         exit;
    //     }
    // }

    // Function to empty the save folder
    public function deletePdfFilesInSaveFolder()
    {
        $saveFolderPath = plugin_dir_path(__FILE__) . '../src/save/';
        $files = glob($saveFolderPath . '*.pdf'); // Get all PDF files in the folder

        // Loop through each file and delete it
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    // Generate dynamic PDF file name
    public function generatePdfFileName()
    {
        // $quote_data = getQuoteDataById($quote_id); // Retrieve quote data by ID

        // Get creation date in the format: YEAR_MONTH_DAY
        $date = date('Y_m_d');

        // If company name exists, use it in the file name, otherwise use last name and first name
        $customer_name = !empty($this->form_data['companyName']) ? ($this->form_data['companyName']) : ($this->form_data['firstname_quot'] . '_' . $this->form_data['lastname_quot']);

        // Instantiate the QuoteCalculator class to use calculated results
        $quote_calculator = new QuoteCalculator();

        // Call function in calculator class
        $results = $quote_calculator->calculateResultsFromTransient($this->form_data);

        // Extract results from the returned calculated results
        $total_ttc = $results['total_ttc'];

        // Construct the file name
        $pdfFileName = $date . '_' . strtoupper($customer_name) . '_DEVIS_' . $total_ttc . '_EUROS_TTC' . '.pdf';

        return $pdfFileName;
    }

    // Generate dynamic PDF file name
    // public function generatePdfFileNameById($quote_id)
    // {
    //     // $quote_data = getQuoteDataById($quote_id); // Retrieve quote data by ID

    //     // Get creation date in the format: YEAR_MONTH_DAY
    //     $date = date('Y_m_d', strtotime($quote_data->creation_date));

    //     // If company name exists, use it in the file name, otherwise use last name and first name
    //     $customer_name = !empty($quote_data->companyName) ? ($quote_data->companyName) : ($quote_data->firstname_quot . '_' . $quote_data->lastname_quot);

    //     // Load SQL method into variable to recover person data for the current quote
    //     $person_data = getPersonByQuoteId($quote_id);

    //     // Instantiate the QuoteCalculator class to use calculated results
    //     $quote_calculator = new QuoteCalculator();

    //     // Call function in calculator class
    //     $results = $quote_calculator->calculateResults($quote_data, $person_data);

    //     // Extract results from the returned calculated results
    //     $total_ttc = $results['total_ttc'];

    //     // Construct the file name
    //     $pdfFileName = $date . '_' . strtoupper($customer_name) . '_DEVIS_' . $total_ttc . '_EUROS_TTC' . '.pdf';

    //     return $pdfFileName;
    // }

}
