<?php if (isset($_GET['form_error'])) : ?>
    <div id="scroll_here"></div>
<?php endif; ?>

<!-- Header part -->
<header>
    <div class="text-center pb-3 border-bottom">
        <span class="fs-2 p-3 text-body-emphasis" style="color: white !important;">Choisissez une option</span>
    </div>

    <div class="pt-3 text-center" id="formButtons">
        <button class="btn btn-outline-light" id="buttonQuotation">Devis!</button>
        <button class="btn btn-outline-light" id="buttonQuestion">Question?</button>
    </div>
</header>

<!-- Question part -->
<div id="formQuestion" class="bg-transparent text-white hidden">

    <div class="container-fluid p-3">

        <div class="col-md-auto">
            <h4 class="mb-3">Posez une question</h4>

            <!-- use of esc_url() for clean url bar.  -->
            <!-- The admin-post.php is a WordPress admin URL used for handling form submissions. -->
            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label for="firstname_quest" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="firstname_quest" name="firstname_quest" placeholder="" value="<?php echo isset($form_data['firstname_quest']) ? esc_attr(stripslashes($form_data['firstname_quest'])) : ''; ?>" />
                        <!-- display error message -->
                        <?php if (isset($form_errors) && isset($form_errors['firstname_quest'])) : ?>
                            <span class="error"><?php echo $form_errors['firstname_quest']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label for="lastname_quest" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="lastname_quest" name="lastname_quest" placeholder="" value="<?php echo isset($form_data['lastname_quest']) ? esc_attr(stripslashes($form_data['lastname_quest'])) : ''; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['lastname_quest'])) : ?>
                            <span class="error"><?php echo $form_errors['lastname_quest']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <!-- <label for="email_quest" class="form-label">Email</label> -->
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="text" class="form-control" id="email_quest" name="email_quest" placeholder="email@example.com" value="<?php echo isset($form_data['email_quest']) ? esc_attr(stripslashes($form_data['email_quest'])) : ''; ?>" />
                            <?php if (isset($form_errors) && isset($form_errors['email_quest'])) : ?>
                                <span class="error"><?php echo $form_errors['email_quest']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <!-- <label for="phone_quest" class="form-label">Tel</label> -->
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="tel" class="form-control" id="phone_quest" name="phone_quest" placeholder="0612345678" value="<?php echo isset($form_data['phone_quest']) ? esc_attr(stripslashes($form_data['phone_quest'])) : ''; ?>" />
                            <!-- display error message -->
                            <?php if (isset($form_errors) && isset($form_errors['phone_quest'])) : ?>
                                <span class="error"><?php echo $form_errors['phone_quest']; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <!-- <label for="message" class="form-label">Message</label> -->
                        <textarea id="message" class="form-control" name="message" rows="4" placeholder="Votre message..."><?php echo isset($form_data['message']) ? esc_textarea(stripslashes($form_data['message'])) : ''; ?></textarea>
                        <?php if (isset($form_errors) && isset($form_errors['message'])) : ?>
                            <span class="error"><?php echo $form_errors['message']; ?></span>
                        <?php endif; ?>
                    </div>

                </div>

                <hr class="my-4">

                <input type="hidden" name="action" value="form_submission">
                <!-- This hidden input is used to store the nonce value generated using wp_create_nonce(). -->
                <!-- It's named form_nonce and will be used for nonce verification to prevent CSRF attacks. -->
                <input type="hidden" name="form_nonce" value="<?php echo wp_create_nonce('form_submit'); ?>">
                <!-- This line fo code is a WordPress nonce field: This function generates and adds an additional nonce field to the form. -->
                <!-- It's another layer of security against CSRF attacks. -->
                <?php wp_nonce_field('form_submit', 'form_nonce'); ?>

                <div class="p-2 col-md-auto">
                    <button id="submit-btn-question" class="w-100 btn btn-success btn-lg" type="submit" name="submit-btn-question">
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        Envoyer
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Quotation part -->
<div id="formQuotation" class="bg-transparent text-white hidden">

    <div class="container-fluid py-3">

        <div class="col-md-auto">

            <h4 class="mb-3">Demandez un devis</h4>

            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">

                <div class="row g-3">

                    <div class="col-sm-6">
                        <label for="firstname_quot" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="firstname_quot" name="firstname_quot" placeholder="" value="<?php echo isset($form_data['firstname_quot']) ? esc_attr(stripslashes($form_data['firstname_quot'])) : ''; ?>" />
                        <!-- display error message -->
                        <?php if (isset($form_errors) && isset($form_errors['firstname_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['firstname_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label for="lastname_quot" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="lastname_quot" name="lastname_quot" placeholder="" value="<?php echo isset($form_data['lastname_quot']) ? esc_attr(stripslashes($form_data['lastname_quot'])) : ''; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['lastname_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['lastname_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label for="companyName" class="form-label">Raison Social</label>
                        <input type="text" class="form-control" id="companyName" name="companyName" placeholder="" value="<?php echo isset($form_data['companyName']) ? esc_attr(stripslashes($form_data['companyName'])) : ''; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['companyName'])) : ?>
                            <span class="error"><?php echo $form_errors['companyName']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="text" class="form-control" id="email_quot" name="email_quot" placeholder="email@example.com" value="<?php echo isset($form_data['email_quot']) ? esc_attr(stripslashes($form_data['email_quot'])) : ''; ?>" />
                        </div>
                        <?php if (isset($form_errors) && isset($form_errors['email_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['email_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="tel" class="form-control" id="phone_quot" name="phone_quot" placeholder="0612345678" value="<?php echo isset($form_data['phone_quot']) ? esc_attr(stripslashes($form_data['phone_quot'])) : ''; ?>" />
                        </div>
                        <?php if (isset($form_errors) && isset($form_errors['phone_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['phone_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label for="address" class="form-label"> <?php echo !empty($form_data['companyName']) ? 'Adresse de la société:' : 'Adresse:'; ?> </label>
                        <input type="text" class="form-control" id="address" name="address" placeholder="123 rue d'exemple, 12345 Ville" value="<?php echo isset($form_data['address']) ? esc_attr(stripslashes($form_data['address'])) : ''; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['address'])) : ?>
                            <span class="error"><?php echo $form_errors['address']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><label for="datetimeVisit">Date et heure de la visite:</label></span>
                            <input class="form-control" type="datetime-local" id="datetimeVisit" name="datetimeVisit" value="<?php echo isset($form_data['datetimeVisit']) ? esc_attr($form_data['datetimeVisit']) : date('Y-m-d\T10:00', strtotime('+1 day')); ?>" />
                        </div>
                        <?php if (isset($form_errors) && isset($form_errors['datetimeVisit'])) : ?>
                            <span class="error"><?php echo $form_errors['datetimeVisit']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label for="visitetype" class="form-label">Type de visite:</label>
                        <select id="visitetype" class="form-select" name="visitetype">
                            <option value="default">Choisir...</option>
                            <?php foreach (getVisiteTypeList() as $visitetype) : ?>
                                <option value="<?php echo $visitetype->id ?>" <?php echo (isset($form_data['visitetype']) && $form_data['visitetype'] == $visitetype->id) ? ' selected' : ''; ?>><?php echo $visitetype->name ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($form_errors) && isset($form_errors['visitetype'])) : ?>
                            <span class="error"><?php echo $form_errors['visitetype']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label for="payment" class="form-label">Mode paiement:</label>
                        <select id="payment" class="form-select" name="payment">
                            <option value="default">Choisir...</option>
                            <?php foreach (getPaymentList() as $payment) : ?>
                                <option value="<?php echo $payment->id ?>" <?php echo (isset($form_data['payment']) && $form_data['payment'] == $payment->id) ? ' selected' : ''; ?>><?php echo $payment->category ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($form_errors) && isset($form_errors['payment'])) : ?>
                            <span class="error"><?php echo $form_errors['payment']; ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Info displayed for visit type -->
                    <div id="info-visiteType" class="hidden">
                        <div class="col-12">
                            <hr class="mb-3">
                            <div class="mx-3 card bg-light">
                                <div class="card-body">
                                    <p class="card-text">Option supplémentaire visite guidée avec nourrissages commentés (2h) :</p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><?php echo getVisiteTypeById(2)->price; ?> € en plus du prix entrée pour 1 à 10 visiteurs, </li>
                                    <li class="list-group-item"><?php echo getVisiteTypeById(2)->price * 2; ?> € pour 11 à 20 visiteurs,</li>
                                    <li class="list-group-item"><?php echo getVisiteTypeById(2)->price * 3; ?> € pour 21 à 30 visiteurs.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic container -->
                    <?php if (isset($form_data['nbPersons'])) :
                        for ($i = 0; $i < count($form_data['nbPersons']); $i++) : ?>
                            <!-- Container to be cloned -->
                            <div class="containerClone" id="container-<?php echo $i ?>">
                                <div class="col-12">
                                    <div class="input-group mb-1">
                                        <label class="input-group-text" for="nbPersons">Nombre de personnes:</label>
                                        <input class="form-control" type="number" id="nbPersons" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." min="0" value="<?php echo isset($form_data['nbPersons'][$i]) ? esc_attr($form_data['nbPersons'][$i]) : ''; ?>" />
                                    </div>
                                    <?php if (isset($form_errors) && isset($form_errors['nbPersons'][$i])) : ?>
                                        <span class="error"><?php echo $form_errors['nbPersons'][$i]; ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="col-12">
                                    <div id="tarif-group" class="input-group">
                                        <label class="input-group-text" for="ages">Tarif:</label>
                                        <select class="form-select" id="ages" name="ages[]">
                                            <option value="default" <?php echo (isset($form_data['ages'][$i]) && $form_data['ages'][$i] == 'default') ? ' selected' : ''; ?>>Choisir...</option>
                                            <?php foreach (getAgeList() as $age) : ?>
                                                <option value="<?php echo $age->id ?>" <?php echo (isset($form_data['ages'][$i]) && $form_data['ages'][$i] == $age->id) ? ' selected' : ''; ?>><?php echo $age->category ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <?php if (isset($form_errors) && isset($form_errors['ages'][$i])) : ?>
                                        <span class="error"><?php echo $form_errors['ages'][$i]; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endfor; ?>
                    <?php else : ?>
                        <!-- Permanent container -->
                        <div class="containerClone" id="container-0">
                            <div class="col-12">
                                <div class="input-group mb-1">
                                    <label class="input-group-text" for="nbPersons"><i class="pe-2 bi bi-person"></i>Nombre de personnes</label>
                                    <input class="form-control" type="number" id="nbPersons" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." min="0" value="<?php echo isset($form_data['nbPersons']) ? esc_attr($form_data['nbPersons']) : ''; ?>" />
                                </div>
                                <?php if (isset($form_errors) && isset($form_errors['nbPersons'])) : ?>
                                    <span class="error"><?php echo $form_errors['nbPersons']; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="col-12">
                                <div id="tarif-group" class="input-group">
                                    <label class="input-group-text" for="ages"><i class="pe-2 bi bi-currency-euro"></i>Tarif</label>
                                    <select class="form-select" id="ages" name="ages[]">
                                        <option value="default" <?php echo (isset($form_data['ages']) && $form_data['ages'] == 'default') ? ' selected' : ''; ?>>Choisir...</option>
                                        <?php foreach (getAgeList() as $age) : ?>
                                            <option value="<?php echo $age->id ?>" <?php echo (isset($form_data['ages']) && $form_data['ages'] == $age->id) ? ' selected' : ''; ?>><?php echo $age->category ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <?php if (isset($form_errors) && isset($form_errors['ages'])) : ?>
                                    <span class="error"><?php echo $form_errors['ages']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add button -->
                    <button type="button" class="w-100 btn btn-warning btn-lg" id="btn-add-persons" name="btn-add-persons">
                        <i class="fa fa-plus"></i>
                    </button>

                    <!-- Info displayed for number of persons -->
                    <div id="info-persons" class="hidden">
                        <div class="col-12">
                            <hr class="mb-3">
                            <div class="mx-3 card bg-light">
                                <div class="card-body">
                                    <p class="card-text">
                                        Avantages d'un groupe de 15 personnes ou plus :
                                        (les enfants de moins de 3 ans ne comptent pas)
                                    </p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item">15 entrées payantes = 1 accompagnant offert</li>
                                    <li class="list-group-item">25 entrées payantes = 2 accompagnants offerts</li>
                                    <li class="list-group-item">35 entrées payantes = 3 accompagnants offerts</li>
                                    <li class="list-group-item">etc...</li>
                                    <li class="list-group-item">Réduction sur le prix d'entrée par personne.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Info displayed for discount -->
                    <div id="info-persons-discount" class="hidden">
                        <div class="col-12">
                            <hr class="mb-3">
                            <div class="mx-3 card bg-light">
                                <div class="card-body">
                                    <p class="card-text">
                                        Vous êtes au dessus de 15 personnes payantes.
                                        Vous avez droit à une réduction.
                                        (les enfants de moins de 3 ans ne comptent pas)
                                    </p>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <?php foreach (getAgeList() as $age) : ?>
                                        <li class="list-group-item"><?php echo $age->category . ' : ' . ($age->price === "0" ? "gratuit" : $age->price_disc . " €"); ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="comment" class="form-label">Commentaire</label>
                        <textarea id="comment" class="form-control" name="comment" rows="4" placeholder="Votre commentaire..."><?php echo isset($form_data['comment']) ? esc_textarea(stripslashes($form_data['comment'])) : ''; ?></textarea>
                    </div>

                    <!-- Upload file -->
                    <div class="input-group">
                        <input type="file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                        <button class="btn btn-warning" type="button" id="inputGroupFileAddon04">OK</button>
                    </div>

                    <hr class="my-4">

                    <input type="hidden" name="action" value="form_submission">
                    <input type="hidden" name="form_nonce" value="<?php echo wp_create_nonce('form_submit'); ?>">
                    <?php wp_nonce_field('form_submit', 'form_nonce'); ?>

                    <!-- Submit form -->
                    <button id="submit-btn-quotation" class="w-100 btn btn-success btn-lg" type="submit" name="submit-btn-quotation">
                        <span class="spinner-border spinner-border-sm d-none"></span>
                        Envoyer
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>