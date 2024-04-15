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

        ob_end_clean();
        // Add custom footer content
        $this->writeHTML($htmlFooter, true, false, true, false, 'L');
    }
}

class DocumentConverter
{
    // Define a class properties
    private $quote_data;
    private $pdfFileName;

    public function __construct()
    {
        // Retrieve data from wordpress transient which is used to store data for a limited time to pass it
        $form_data_transient = get_transient('form_data_transient');

        // Retrieve quote ID from transient if available
        if ($form_data_transient && isset($form_data_transient['quote_id'])) {
            $quote_id = $form_data_transient['quote_id'];
            $quote_data = getQuoteDataById($quote_id); // Retrieve quote data by ID
            // Verify that quote data is valid
            if ($quote_data) {
                // Set properties
                $this->quote_data = $quote_data;
                $this->pdfFileName = 'devis_microzoo_' . $quote_data->number_quote . '.pdf';
            }
        }
    }

    // convert html to pdf using TCPDF
    public function convert_html_to_pdf()
    {

        $quote_data = $this->quote_data;
        $pdfFileName = $this->pdfFileName;

        // Create a new PDF document
        $pdf = new CustomTCPDF('P', 'mm', 'A4', true, 'UTF-8', false, false);

        // Set document information
        $pdf->SetCreator('MicroZoo');
        $pdf->SetAuthor('MicroZoo');
        $pdf->SetTitle('Devis : ' . $quote_data->companyName);
        $pdf->SetSubject($quote_data->number_quote);
        $pdf->SetKeywords('Devis, MicroZoo');

        // Logo path
        // $logoPath = plugin_dir_path(__FILE__) . '../src/images/logo.png';

        // Set custom header content
        // $pdf->SetHeaderData(
        //     $logoPath, // Logo image path
        //     30, // Logo width
        //     'MicroZoo', // Title (left side)
        //     'Saint Malo' // Title (right side)
        // );

        // Set header and footer fonts
        // $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        // $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        // Set default monospaced font
        // $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(10, 10, 10);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 30);

        // Set image scale factor
        // $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Add a page
        $pdf->AddPage();

        // Set font
        // $pdf->SetFont('helvetica', '', 10);

        // Set cell paddings (top, right, bottom, left)
        // $pdf->setCellPaddings(0, 0, 0, 0); // Adjust the bottom padding (5mm) to set the margin between paragraphs
        
        
        // Read HTML content from the template file
        include(plugin_dir_path(__FILE__) . '../template/template.quotation.php');
        $html = ob_get_clean(); // Get the contents of the output buffer and clean it
        

        // Convert HTML to PDF
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Save the PDF to the desired path with a custom name
        $pdfFileName = 'devis_microzoo_' . $quote_data->number_quote . '.pdf'; // Custom PDF file name
        
        // Path to save the PDF
        $pdfPath = plugin_dir_path(__FILE__) . '../src/save/' . $pdfFileName;
        
        // Empty the save folder before saving the PDF
        $this->deletePdfFilesInSaveFolder();

        // Save the PDF, overwriting any existing file
        $pdf->Output($pdfPath, 'F');

        // Clean sent headers
        header_remove();
    }

    public function convert_pdf_save_redirect()
    {
        $pdfFileName = $this->pdfFileName;

        if (isset($_GET['pdf']) && $_GET['pdf'] === 'true') {

            $this->convert_html_to_pdf();

            // Redirect the user to a new page where the PDF is displayed in a new tab
            $pdfUrl = plugin_dir_url(__FILE__) . '../src/save/' . $pdfFileName; // Get the URL of the PDF file

            // Redirection
            wp_redirect(esc_url($pdfUrl));
            exit;
        }
    }

    // Function to empty the save folder
    private function deletePdfFilesInSaveFolder()
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

}
