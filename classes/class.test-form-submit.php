<?php

/**
 * This Class is responsible for simulating test data on the admin side.
 * Test the form handling logic without manual input.
 * This setup allows to programmatically submit the forms, 
 * simulating the behavior of a user filling out and submitting the forms.
 * 
 * Use the URL parameter :
 *  ?test_question_form=true
 * in your WordPress admin area to trigger the question form submission simulation.
 * 
 * For example: https://your-site.com/wp-admin/?test_question_form=true
 * 
 * -----
 * 
 * To test the submission for quotation form, use URL parameter :
 *  ?test_quotation_form=true
 * 
 * For example: https://your-site.com/wp-admin/?test_quotation_form=true
 */

class TestFormSubmit
{
    public function submit_question_form()
    {
        // Define the test data for the "Question" form
        $question_data = [
            $_POST['firstname_quest'] = 'Jean',
            $_POST['lastname_quest'] = 'Dupont',
            $_POST['email_quest'] = 'jean.dupont@example.com',
            $_POST['phone_quest'] = '0612345678',
            $_POST['message'] = 'Bonjour, ceci est un test de message.',
            $_POST['captcha'] = '5',
            $_POST['form_nonce'] = wp_create_nonce('form_submit'),
            $_POST['submit-btn-question'] = true,
            $_POST['js_validation'] = 'validated',

            // Include the reCAPTCHA key and response for testing
            $_POST['g-recaptcha-response'] = 'test_recaptcha_response',
        ];

        // Simulate form submission
        $this->send_post_request_question($question_data);
    }

    public function send_post_request_question()
    {
        if (isset($_POST['form_nonce']) && wp_verify_nonce($_POST['form_nonce'], 'form_submit')) {

            if (isset($_POST['submit-btn-question'])) {

                // Instantiate FormHandler class
                $formHandler = new FormHandler();

                // call security checks
                $formHandler->security_check();

                // Mock the reCAPTCHA verification for testing purposes
                $isRecaptchaSuccess = $_POST['g-recaptcha-response'] === 'test_recaptcha_response';

                // custom captcha validation
                if (empty($_POST['captcha']) || trim($_POST['captcha']) !== '5') {
                    $formHandler->eraseMemory();
                    wp_die('CAPTCHA failed', 'Error', array('response' => 403));
                }

                // reCAPTCHA validation
                if ($isRecaptchaSuccess) {
                    // send email
                    $mailSender = new MailSender();
                    $mailSender->send_email_question_to_admin($_POST);
                } else {
                    wp_die('reCAPTCHA failed', 'Error', array('response' => 403));
                }

                $formHandler->eraseMemory();
            }
        }
    }

    public function submit_quotation_form()
    {
        // Define the test data for the "Quotation" form
        $quotation_data = [
            $_POST['firstname_quot'] = 'Marie',
            $_POST['lastname_quot'] = 'Durand',
            $_POST['email_quot'] = 'marie.durand@example.com',
            $_POST['phone_quot'] = '0612345678',
            $_POST['companyName'] = 'Entreprise Test',
            $_POST['address'] = '123 rue de l\'exemple, 12345 Ville',
            $_POST['datetimeVisit'] = date('Y-m-d\TH:i', strtotime('+2 days')),
            $_POST['visitetype'] = '1', // Adjust according to your actual visit types
            $_POST['payment'] = '1', // Adjust according to your actual payment methods
            $_POST['nbPersons'] = [5],
            $_POST['ages'] = [2], // Adjust according to your actual age categories
            $_POST['comment'] = 'Ceci est un test de commentaire.',
            $_POST['captcha'] = '5',
            $_POST['form_nonce'] = wp_create_nonce('form_submit'),
            $_POST['submit-btn-quotation'] = true,
            $_POST['js_validation'] = 'validated',

            // Include the reCAPTCHA key and response for testing
            $_POST['g-recaptcha-response'] = 'test_recaptcha_response',
        ];

        // Simulate form submission
        $this->send_post_request_quotation($quotation_data);
    }

    public function send_post_request_quotation()
    {
        if (isset($_POST['form_nonce']) && wp_verify_nonce($_POST['form_nonce'], 'form_submit')) {

            if (isset($_POST['submit-btn-quotation'])) {

                // Instantiate FormHandler class
                $formHandler = new FormHandler();

                // call security checks
                $formHandler->security_check();

                // Mock the reCAPTCHA verification for testing purposes
                $isRecaptchaSuccess = $_POST['g-recaptcha-response'] === 'test_recaptcha_response';

                // custom captcha validation
                if (empty($_POST['captcha']) || trim($_POST['captcha']) !== '5') {
                    $formHandler->eraseMemory();
                    wp_die('CAPTCHA failed', 'Error', array('response' => 403));
                }

                // reCAPTCHA validation
                if ($isRecaptchaSuccess) {

                    $data_to_store = array(
                        'form_data' => $_POST, // store data to display and repopulate update form
                    );

                    set_transient('form_data_transient', $data_to_store, 600); // Store data for 600 seconds

                } else {
                    wp_die('reCAPTCHA failed', 'Error', array('response' => 403));
                }

                $formHandler->eraseMemory();
            }
        }
    }

    public function review_quotation()
    {
        $number_currency = new NumberFormatter("fr_FR", NumberFormatter::CURRENCY);
        $number_decimal = new NumberFormatter("fr_FR", NumberFormatter::DECIMAL);
        $number_decimal->setAttribute(NumberFormatter::FRACTION_DIGITS, 2);

        // Retrieve the transient data using the user-specific key
        $form_data_transient = get_transient('form_data_transient');

        // Populate document with transient data if available
        if ($form_data_transient && isset($form_data_transient['form_data'])) {
            $form_data = $form_data_transient['form_data'];

            // Sanitize POST inputs recovered from transient $form_data before sending to database
            $email_quot = sanitize_email($form_data['email_quot']);
            $lastname_quot = sanitize_text_field(ucwords($form_data['lastname_quot']));
            $firstname_quot = sanitize_text_field(ucwords($form_data['firstname_quot']));
            $companyName = sanitize_text_field(stripslashes($form_data['companyName']));
            $address = sanitize_text_field($form_data['address']);
            $phone_quot = sanitize_text_field($form_data['phone_quot']);
            $visitetype = sanitize_text_field($form_data['visitetype']);
            $datetimeVisit = sanitize_text_field($form_data['datetimeVisit']);
            $payment = sanitize_text_field($form_data['payment']);
            $comment = sanitize_textarea_field($form_data['comment']);

            $nbPersons = $form_data['nbPersons'];
            $ages = $form_data['ages'];

            // Instantiate the QuoteCalculator class to use calculated results
            $quote_calculator = new QuoteCalculator();

            // Calculate results: totals, unit prices, references, quantities, etc
            // $results = $quote_calculator->calculateResults($quote_data, $person_data);
            $results = $quote_calculator->calculateResultsFromTransient($form_data);

            // Extract results from the returned calculated results
            $total_tva = $results['total_tva'];
            $total_ht = $results['total_ht'];
            $total_ttc = $results['total_ttc'];
            $total_paying_persons = $results['total_paying_persons'];
            $total_persons = $results['total_persons'];
            $unit_ht = $results['unit_ht'];
            $amount_ht = $results['amount_ht'];
            $amount_ttc = $results['amount_ttc'];
            $ref = $results['ref'];
            $guided_qty = $results['guided_qty'];
            $guided_price_ht = $results['guided_price_ht'];
            $guided_amount_ht = $results['guided_amount_ht'];
            $guided_amount_ttc = $results['guided_amount_ttc'];
            $total_free_persons = $results['total_free_persons'];
            $discount_unit_ht = $results['discount_unit_ht'];
            $discount_amount_ht = $results['discount_amount_ht'];
            $discount_amount_ttc = $results['discount_amount_ttc'];
        } else {
            // Display an error message
            wp_die('no data in transient', 'Error', array('response' => 403));
        }

?>

        <div class="container-fluid py-3">
            <header>
                <div class="d-flex flex-column flex-md-row align-items-center pb-3 mb-4 border-bottom">
                    <span class="fs-2 p-3 text-body-emphasis" >Test Data!</span>
                </div>
            </header>

            <main>

                <!-- Client Information -->
                <div class="row mb-3 text-center">
                    <div class="col">
                        <div class="card rounded-3 shadow-sm">
                            <div class="card-header py-3">
                                <h4 class="my-0 fw-normal">
                                    <?php echo (!empty($companyName)) ?
                                        (strtoupper($companyName)) : ($firstname_quot . ' ' . strtoupper($lastname_quot));
                                    ?>
                                </h4>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($companyName)) : ?>
                                    <h5 class="card-title">
                                        <?php echo $firstname_quot ?> <?php echo strtoupper($lastname_quot) ?>
                                    </h5>
                                <?php endif; ?>
                                <ul class="list-unstyled mt-3 mb-4">
                                    <li><?php echo $address ?></li>
                                    <li>Tel : <?php echo $phone_quot ?></li>
                                    <li>Email : <?php echo $email_quot ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="row mb-3 text-center">
                    <div class="col">
                        <div class="card rounded-3 shadow-sm">
                            <div class="table-responsive p-3">
                                <table class="table" style="border-collapse: collapse;">
                                    <thead>
                                        <tr>
                                            <th class="align-baseline" scope="col" style="border: none;">Tarif</th>
                                            <th class="align-baseline" scope="col" style="border: none;">Quantité</th>
                                            <th class="align-baseline" scope="col" style="border: none;">Prix HT</th>
                                            <th class="align-baseline" scope="col" style="border: none;">Montant TTC</th>
                                        </tr>
                                    </thead>
                                    <tbody class="table-group-divider">
                                        <?php for ($i = 0; $i < count($nbPersons); $i++) : ?>
                                            <?php
                                            $age_data = getAgeById($ages[$i]); // get one row of age data in the current quote
                                            ?>
                                            <!-- details -->
                                            <tr class="border-bottom">
                                                <td class=" align-baseline text-start bg-white text-dark" style="border: none;"><?php echo $age_data->category; ?></td>
                                                <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_decimal->format($nbPersons[$i]); ?></td>
                                                <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($unit_ht[$i]); ?></td>
                                                <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($amount_ttc[$i]); ?></td>
                                            </tr>
                                        <?php endfor; ?>

                                        <!-- Add guided option if exists -->
                                        <?php if ($visitetype === "2") : ?>
                                            <?php
                                            // run a query in the database to get the guided category row
                                            $visitetype_guided = getVisiteTypeById($visitetype);
                                            ?>
                                            <tr class="border-bottom">
                                                <td class="align-baseline text-start bg-white text-dark" style="border: none;"><?php echo "Visite " . $visitetype_guided->name; ?></td>
                                                <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_decimal->format($guided_qty); ?></td>
                                                <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($guided_price_ht); ?></td>
                                                <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($guided_amount_ttc); ?></td>
                                            </tr>
                                        <?php endif; ?>

                                        <!-- Calculate and include the person for free -->
                                        <?php if ($total_free_persons > 0) : ?>
                                            <?php
                                            $age_list = getAgeList(); // run a query in the database to get the category
                                            ?>
                                            <tr class="border-bottom">
                                                <td class="align-baseline text-start bg-white text-dark" style="border: none;"><?php echo $age_list[2]->category; ?></td>
                                                <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_decimal->format($total_free_persons); ?></td>
                                                <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo "gratuite"; ?></td>
                                                <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($discount_amount_ttc); ?></td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="row mb-3 text-center">
                    <div class="col">
                        <div class="card rounded-3 shadow-sm">
                            <div class="table-responsive p-3">
                                <table class="table" style="border-collapse: collapse;">
                                    <tbody>
                                        <tr class="border-bottom">
                                            <td class="align-baseline text-start bg-white text-dark" style="border: none;">Total HT</td>
                                            <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($total_ht); ?></td>
                                        </tr>
                                        <tr class="border-bottom">
                                            <td class="align-baseline text-start bg-white text-dark" style="border: none;">TVA (5.50 %)</td>
                                            <td class="align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($total_tva); ?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold align-baseline text-start bg-white text-dark" style="border: none;">Total TTC</td>
                                            <td class="fw-bold align-baseline bg-white text-dark" style="border: none;"><?php echo $number_currency->format($total_ttc); ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thanks -->
                <p class="fs-6 text-center mb-3 text-body-emphasis" style="color: white !important;">Toute l'équipe de Micro Zoo vous remercie!</p>
            </main>
        </div>
<?php

    }
}
