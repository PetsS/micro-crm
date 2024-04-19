<?php if (isset($_GET['form_error'])) : ?>
    <div id="scroll_here"></div>
<?php endif; ?>

<h2>Mettre à jour</h2>

<!-- <div id="scrollHereIfErrorsInUpdate"></div> -->

<div id="formQuotationUpdate">

    <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>?update=<?php echo $quote_id; ?>" method="post">

        <label for="email_quot_upd">Email:</label>
        <input type="text" id="email_quot_upd" name="email_quot" placeholder="Votre email" value="<?php echo isset($form_data['email_quot']) ? esc_attr(stripslashes($form_data['email_quot'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['email_quot'])) : ?>
            <span class="error"><?php echo $form_errors['email_quot']; ?><br></span>
        <?php endif; ?>
        <br>

        <label for="lastname_quot_upd">Nom:</label>
        <input type="text" id="lastname_quot_upd" name="lastname_quot" placeholder="Votre nom" value="<?php echo isset($form_data['lastname_quot']) ? esc_attr(stripslashes($form_data['lastname_quot'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['lastname_quot'])) : ?>
            <span class="error"><?php echo $form_errors['lastname_quot']; ?><br></span>
        <?php endif; ?><br>

        <label for="firstname_quot_upd">Prénom:</label>
        <input type="text" id="firstname_quot_upd" name="firstname_quot" placeholder="Votre prénom" value="<?php echo isset($form_data['firstname_quot']) ? esc_attr(stripslashes($form_data['firstname_quot'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['firstname_quot'])) : ?>
            <span class="error"><?php echo $form_errors['firstname_quot']; ?><br></span>
        <?php endif; ?><br>

        <label for="companyName_upd">Raison Social:</label>
        <input type="text" id="companyName_upd" name="companyName" placeholder="Nom de la société" value="<?php echo isset($form_data['companyName']) ? esc_attr(stripslashes($form_data['companyName'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['companyName'])) : ?>
            <span class="error"><?php echo $form_errors['companyName']; ?><br></span>
        <?php endif; ?><br>

        <label for="address_upd"> <?php echo !empty($form_data['companyName']) ? 'Adresse de la société:' : 'Adresse:'; ?> </label>
        <input type="text" id="address_upd" name="address" placeholder="Saisissez une adresse" value="<?php echo isset($form_data['address']) ? esc_attr(stripslashes($form_data['address'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['address'])) : ?>
            <span class="error"><?php echo $form_errors['address']; ?><br></span>
        <?php endif; ?><br>

        <label for="phone_quot_upd">Tel:</label>
        <input type="tel" id="phone_quot_upd" name="phone_quot" placeholder="Numéro de téléphone" value="<?php echo isset($form_data['phone_quot']) ? esc_attr(stripslashes($form_data['phone_quot'])) : ''; ?>" />
        <!-- display error message -->
        <?php if (isset($form_errors) && isset($form_errors['phone_quot'])) : ?>
            <span class="error"><?php echo $form_errors['phone_quot']; ?><br></span>
        <?php endif; ?><br>

        <label for="visitetype_upd">Type de visite:</label>
        <select name="visitetype" id="visitetype_upd">
            <option value="default">Choisir...</option>
            <?php foreach (getVisiteTypeList() as $visitetype) : ?>
                <option value="<?php echo $visitetype->id ?>" <?php echo (isset($form_data['visitetype']) && $form_data['visitetype'] == $visitetype->id) ? ' selected' : ''; ?>><?php echo $visitetype->name ?></option>
            <?php endforeach; ?>
        </select><br>
        <?php if (isset($form_errors) && isset($form_errors['visitetype'])) : ?>
            <span class="error"><?php echo $form_errors['visitetype']; ?><br></span>
        <?php endif; ?><br>

        <div id="info-visiteType" class="hidden">
            <!-- TODO replace price with database data -->
            <p>Visite guidée avec nourrissage commenté :</p>
            <p>1 à 6 personnes maximum (2 heures) : <?php echo getVisiteTypeById(2)->price; ?> € au total.</p>
        </div>

        <div>
            <?php if (isset($form_data['nbPersons'])) :
                for ($i = 0; $i < count($form_data['nbPersons']); $i++) : ?>
                    <div class="containerClone" id="container-<?php echo $i ?>">
                        <div class="input-group">
                            <label for="nbPersons_upd">Nombre de personnes:</label>
                            <input type="number" id="nbPersons_upd" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." min="0" value="<?php echo isset($form_data['nbPersons'][$i]) ? esc_attr($form_data['nbPersons'][$i]) : ''; ?>" />
                            <?php if (isset($form_errors) && isset($form_errors['nbPersons'][$i])) : ?>
                                <span class="error"><?php echo $form_errors['nbPersons'][$i]; ?><br></span>
                            <?php endif; ?><br>
                        </div>

                        <div class="input-group">
                            <label for="ages_upd">Age des personnes et tarif:</label>
                            <select name="ages[]" id="ages_upd">
                                <option value="default" <?php echo (isset($form_data['ages'][$i]) && $form_data['ages'][$i] == 'default') ? ' selected' : ''; ?>>Choisissez âge...</option>
                                <?php foreach (getAgeList() as $age) : ?>
                                    <option value="<?php echo $age->id ?>" <?php echo (isset($form_data['ages'][$i]) && $form_data['ages'][$i] == $age->id) ? ' selected' : ''; ?>><?php echo $age->category ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($form_errors) && isset($form_errors['ages'][$i])) : ?>
                                <span class="error"><?php echo $form_errors['ages'][$i]; ?><br></span>
                            <?php endif; ?><br>
                        </div>

                        <!-- a hidden input field to track if a container is removed -->
                        <!-- <input type="hidden" name="removed_indexes[]"> -->

                    </div>
                <?php endfor; ?>
            <?php else : ?><br>
                <div class="containerClone" id="container-0">
                    <div class="input-group">
                        <label for="nbPersons_upd">Nombre de personnes:</label>
                        <input type="number" id="nbPersons_upd" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." min="0" value="<?php echo isset($form_data['nbPersons']) ? esc_attr($form_data['nbPersons']) : ''; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['nbPersons'])) : ?>
                            <span class="error"><?php echo $form_errors['nbPersons']; ?><br></span>
                        <?php endif; ?><br>
                    </div>

                    <div class="input-group">
                        <label for="ages_upd">Age des personnes et tarif:</label>
                        <select name="ages[]" id="ages_upd">
                            <option value="default" <?php echo (isset($form_data['ages']) && $form_data['ages'] == 'default') ? ' selected' : ''; ?>>Choisissez âge...</option>
                            <?php foreach (getAgeList() as $age) : ?>
                                <option value="<?php echo $age->id ?>" <?php echo (isset($form_data['ages']) && $form_data['ages'] == $age->id) ? ' selected' : ''; ?>><?php echo $age->category ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($form_errors) && isset($form_errors['ages'])) : ?>
                            <span class="error"><?php echo $form_errors['ages']; ?><br></span>
                        <?php endif; ?><br>
                    </div>
                </div>
            <?php endif; ?><br>

            <div id="info-persons" class="hidden">
                <ul>
                    <span>Avantages d'un groupe de 15 personnes ou plus : </span>
                    <span>(les enfants de moins de 3 ans ne comptent pas)</span><br>
                    <li>1 accompagnement gratuit.</li>
                    <li> chaque dixième personne est gratuite.</li>
                    <li> 1 € moins / personne.</li>
                </ul>
                <br>
            </div>

            <div id="info-persons-discount" class="hidden">
                <ul>
                    <span>Vous êtes au dessus de 15 personnes payantes.</span>
                    <span>Vous avez droit à une réduction.</span>
                    <span>(les enfants de moins de 3 ans ne comptent pas)</span><br>
                    <?php foreach (getAgeList() as $age) : ?>
                        <li><?php echo $age->category . ' : ' . ($age->price === "0" ? "gratuit" : $age->price . " €"); ?></li>
                    <?php endforeach; ?>
                </ul>
                <br>
            </div>

            <div>
                <button type="button" class="btn btn-primary" id="btn-add-persons-update" name="btn-add-persons">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
        </div>
        <br>

        <label for="datetimeVisit_upd">Date et heure de la visite:</label>
        <input type="datetime-local" id="datetimeVisit_upd" name="datetimeVisit" value="<?php echo isset($form_data['datetimeVisit']) ? esc_attr($form_data['datetimeVisit']) : date('Y-m-d\T10:00', strtotime('+1 day')); ?>" />
        <?php if (isset($form_errors) && isset($form_errors['datetimeVisit'])) : ?>
            <span class="error"><?php echo $form_errors['datetimeVisit']; ?><br></span>
        <?php endif; ?><br>

        <label for="payment_upd">Mode paiement:</label>
        <select name="payment" id="payment_upd">
            <option value="default">Choisir...</option>
            <?php foreach (getPaymentList() as $payment) : ?>
                <option value="<?php echo $payment->id ?>" <?php echo (isset($form_data['payment']) && $form_data['payment'] == $payment->id) ? ' selected' : ''; ?>><?php echo $payment->category ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($form_errors) && isset($form_errors['payment'])) : ?>
            <span class="error"><?php echo $form_errors['payment']; ?><br></span>
        <?php endif; ?><br>
        <br>

        <label for="comment_upd">Commentaire:</label>
        <textarea name="comment" id="comment_upd" rows="4" placeholder="Votre commentaire"><?php echo isset($form_data['comment']) ? esc_textarea(stripslashes($form_data['comment'])) : ''; ?></textarea>

        <input type="hidden" name="action" value="form_submission">
        <input type="hidden" name="form_nonce" value="<?php echo wp_create_nonce('form_submit'); ?>">
        <?php wp_nonce_field('form_submit', 'form_nonce'); ?>

        <a href="<?php echo esc_url(remove_query_arg('update', wp_get_referer())); ?>" class="btn btn-danger">Retour</a>
        <button type="submit" name="submit-btn-quotation">Modifier</button>
    </form>
</div>