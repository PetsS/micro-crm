<?php
$ages = getAgeList();
?>

<h2>Contactez-nous</h2>

<div id="formButtons">
    <button id="buttonQuotation">Demandez un devis</button>
    <button id="buttonQuestion">Une question?</button>
</div>

<div id="scrollHereIfErrors"></div>

<div id="formQuestion" class="hidden">

    <!-- use of esc_url() for clean url bar.  -->
    <!-- The admin-post.php is a WordPress admin URL used for handling form submissions. -->
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">

        <label for="email_quest">Email:</label>
        <input type="text" id="email_quest" name="email_quest" placeholder="Votre email" value="<?php echo isset($form_data['email_quest']) ? esc_attr(stripslashes($form_data['email_quest'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['email_quest'])) : ?>
            <span class="error"><?php echo $form_errors['email_quest']; ?><br></span>
        <?php endif; ?>
        <br>

        <label for="lastname_quest">Nom:</label>
        <input type="text" id="lastname_quest" name="lastname_quest" placeholder="Votre nom" value="<?php echo isset($form_data['lastname_quest']) ? esc_attr(stripslashes($form_data['lastname_quest'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['lastname_quest'])) : ?>
            <span class="error"><?php echo $form_errors['lastname_quest']; ?><br></span>
        <?php endif; ?><br>

        <label for="firstname_quest">Prénom:</label>
        <input type="text" id="firstname_quest" name="firstname_quest" placeholder="Votre prénom" value="<?php echo isset($form_data['firstname_quest']) ? esc_attr(stripslashes($form_data['firstname_quest'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['firstname_quest'])) : ?>
            <span class="error"><?php echo $form_errors['firstname_quest']; ?><br></span>
        <?php endif; ?><br>

        <label for="phone_quest">Tel:</label>
        <input type="tel" id="phone_quest" name="phone_quest" placeholder="Votre numéro de téléphone" value="<?php echo isset($form_data['phone_quest']) ? esc_attr(stripslashes($form_data['phone_quest'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['phone_quest'])) : ?>
            <span class="error"><?php echo $form_errors['phone_quest']; ?><br></span>
        <?php endif; ?><br>

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="4" placeholder="Votre message"><?php echo isset($form_data['message']) ? esc_textarea(stripslashes($form_data['message'])) : ''; ?></textarea>
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['message'])) : ?>
            <span class="error"><?php echo $form_errors['message']; ?><br></span>
        <?php endif; ?><br>

        <input type="hidden" name="action" value="form_submission">
        <!-- This hidden input is used to store the nonce value generated using wp_create_nonce().
            It's named form_nonce and will be used for nonce verification to prevent CSRF attacks. -->
        <input type="hidden" name="form_nonce" value="<?php echo wp_create_nonce('form_submit'); ?>">
        <!-- This line fo code is a WordPress nonce field: This function generates and adds an additional nonce field to the form.
            It's another layer of security against CSRF attacks. -->
        <?php wp_nonce_field('form_submit', 'form_nonce'); ?>
        <button type="submit" name="submit-btn-question">Envoyer</button>
    </form>
</div>

<div id="formQuotation" class="hidden">
    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">

        <label for="email_quot">Email:</label>
        <input type="text" id="email_quot" name="email_quot" placeholder="Votre email" value="<?php echo isset($form_data['email_quot']) ? esc_attr(stripslashes($form_data['email_quot'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['email_quot'])) : ?>
            <span class="error"><?php echo $form_errors['email_quot']; ?><br></span>
        <?php endif; ?>
        <br>

        <label for="lastname_quot">Nom:</label>
        <input type="text" id="lastname_quot" name="lastname_quot" placeholder="Votre nom" value="<?php echo isset($form_data['lastname_quot']) ? esc_attr(stripslashes($form_data['lastname_quot'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['lastname_quot'])) : ?>
            <span class="error"><?php echo $form_errors['lastname_quot']; ?><br></span>
        <?php endif; ?><br>

        <label for="firstname_quot">Prénom:</label>
        <input type="text" id="firstname_quot" name="firstname_quot" placeholder="Votre prénom" value="<?php echo isset($form_data['firstname_quot']) ? esc_attr(stripslashes($form_data['firstname_quot'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['firstname_quot'])) : ?>
            <span class="error"><?php echo $form_errors['firstname_quot']; ?><br></span>
        <?php endif; ?><br>

        <label for="companyName">Raison Social:</label>
        <input type="text" id="companyName" name="companyName" placeholder="Nom de la société" value="<?php echo isset($form_data['companyName']) ? esc_attr(stripslashes($form_data['companyName'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['companyName'])) : ?>
            <span class="error"><?php echo $form_errors['companyName']; ?><br></span>
        <?php endif; ?><br>

        <label for="address"> <?php echo !empty($form_data['companyName']) ? 'Adresse de la société:' : 'Adresse:'; ?> </label>
        <input type="text" id="address" name="address" placeholder="Saisissez une adresse" value="<?php echo isset($form_data['address']) ? esc_attr(stripslashes($form_data['address'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['address'])) : ?>
            <span class="error"><?php echo $form_errors['address']; ?><br></span>
        <?php endif; ?><br>

        <label for="phone_quot">Tel:</label>
        <input type="tel" id="phone_quot" name="phone_quot" placeholder="Numéro de téléphone" value="<?php echo isset($form_data['phone_quot']) ? esc_attr(stripslashes($form_data['phone_quot'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['phone_quot'])) : ?>
            <span class="error"><?php echo $form_errors['phone_quot']; ?><br></span>
        <?php endif; ?><br>

        <label for="visitType">Type de visite:</label>
        <select id="visitType" name="visitType">
            <option value="default">Choisir...</option>
            <option value="1" <?php echo (isset($form_data['visitType']) && $form_data['visitType'] == '1') ? ' selected' : ''; ?>>Libre</option>
            <option value="2" <?php echo (isset($form_data['visitType']) && $form_data['visitType'] == '2') ? ' selected' : ''; ?>>Guidé</option>
        </select><br>
        <?php if (isset($form_errors) && isset($form_errors['visitType'])) : ?>
            <span class="error"><?php echo $form_errors['visitType']; ?><br></span>
        <?php endif; ?><br>

        <div>
            <?php if (isset($form_data['nbPersons'])) :
                for ($i = 0; $i < count($form_data['nbPersons']); $i++) : ?>
                    <div class="containerClone" id="container-<?php echo $i ?>">
                        <div class="input-group">
                            <label for="nbPersons">Nombre de personnes:</label>
                            <input type="number" id="nbPersons" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." value="<?php echo isset($form_data['nbPersons'][$i]) ? esc_attr($form_data['nbPersons'][$i]) : ''; ?>" />
                            <?php if (isset($form_errors) && isset($form_errors['nbPersons'][$i])) : ?>
                                <span class="error"><?php echo $form_errors['nbPersons'][$i]; ?><br></span>
                            <?php endif; ?><br>
                        </div>

                        <div class="input-group">
                            <label for="ages">Age des personnes et tarif:</label>
                            <select id="ages" name="ages[]">
                                <option value="default" <?php echo (isset($form_data['ages'][$i]) && $form_data['ages'][$i] == 'default') ? ' selected' : ''; ?>>Choisissez âge...</option>
                                <?php foreach ($ages as $age) : ?>
                                    <option value="<?php echo $age->id ?>" <?php echo (isset($form_data['ages'][$i]) && $form_data['ages'][$i] == $age->id) ? ' selected' : ''; ?>><?php echo $age->category ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($form_errors) && isset($form_errors['ages'][$i])) : ?>
                                <span class="error"><?php echo $form_errors['ages'][$i]; ?><br></span>
                            <?php endif; ?><br>
                        </div>

                    </div>
                <?php endfor; ?>
            <?php else : ?><br>
                <div class="containerClone" id="container-0">
                    <div class="input-group">
                        <label for="nbPersons">Nombre de personnes:</label>
                        <input type="number" id="nbPersons" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." value="<?php echo isset($form_data['nbPersons']) ? esc_attr($form_data['nbPersons']) : ''; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['nbPersons'])) : ?>
                            <span class="error"><?php echo $form_errors['nbPersons']; ?><br></span>
                        <?php endif; ?><br>
                    </div>

                    <div class="input-group">
                        <label for="ages">Age des personnes et tarif:</label>
                        <select id="ages" name="ages[]">
                            <option value="default" <?php echo (isset($form_data['ages']) && $form_data['ages'] == 'default') ? ' selected' : ''; ?>>Choisissez âge...</option>
                            <?php foreach ($ages as $age) : ?>
                                <option value="<?php echo $age->id ?>" <?php echo (isset($form_data['ages']) && $form_data['ages'] == $age->id) ? ' selected' : ''; ?>><?php echo $age->category ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($form_errors) && isset($form_errors['ages'])) : ?>
                            <span class="error"><?php echo $form_errors['ages']; ?><br></span>
                        <?php endif; ?><br>
                    </div>
                </div>
            <?php endif; ?><br>

            <div>
                <p style="font-size: medium;">Tarif réduit 13 ans et plus (handicap, étudiant, chômage) sur présentation d'un justificatif en caisse le jour de la visite.</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary" id="btn-add-persons" name="btn-add-persons">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <br>

        <label for="datetimeVisit">Date et heure de la visite:</label>
        <input type="datetime-local" id="datetimeVisit" name="datetimeVisit" value="<?php echo isset($form_data['datetimeVisit']) ? esc_attr($form_data['datetimeVisit']) : date('Y-m-d\T10:00', strtotime('+1 day')); ?>" />
        <?php if (isset($form_errors) && isset($form_errors['datetimeVisit'])) : ?>
            <span class="error"><?php echo $form_errors['datetimeVisit']; ?><br></span>
        <?php endif; ?><br>

        <label for="payment">Mode paiement:</label>
        <select id="payment" name="payment">
            <option value="default">Choisir...</option>
            <option value="1" <?php echo (isset($form_data['payment']) && $form_data['payment'] == '1') ? ' selected' : ''; ?>>Virement</option>
            <option value="2" <?php echo (isset($form_data['payment']) && $form_data['payment'] == '2') ? ' selected' : ''; ?>>CB</option>
            <option value="3" <?php echo (isset($form_data['payment']) && $form_data['payment'] == '3') ? ' selected' : ''; ?>>Chèque</option>
            <option value="4" <?php echo (isset($form_data['payment']) && $form_data['payment'] == '4') ? ' selected' : ''; ?>>Espèce</option>
        </select><br><br>

        <label for="comment">Commentaire:</label>
        <textarea id="comment" name="comment" rows="4" placeholder="Votre commentaire"><?php echo isset($form_data['comment']) ? esc_textarea(stripslashes($form_data['comment'])) : ''; ?></textarea>

        <input type="hidden" name="action" value="form_submission">
        <input type="hidden" name="form_nonce" value="<?php echo wp_create_nonce('form_submit'); ?>">
        <?php wp_nonce_field('form_submit', 'form_nonce'); ?>

        <button type="submit" name="submit-btn-quotation">Envoyer</button>
    </form>
</div>