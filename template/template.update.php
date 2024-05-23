<div id="scroll_here"></div>

<div id="formQuotationUpdate" class="bg-transparent text-white rounded">

    <div class="container-fluid py-3">

        <div class="col-md-auto">

            <h4 class="mb-3">Mettre à jour</h4>

            <form action="<?php echo esc_url(admin_url('admin-post.php')); ?>?update=<?php echo "false"; ?>" method="post">

                <div class="row g-3">

                    <div class="col-sm-6">
                        <label for="firstname_quot_upd" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="firstname_quot_upd" name="firstname_quot" placeholder="" value="<?php echo isset($form_data['firstname_quot']) ? esc_attr(stripslashes($form_data['firstname_quot'])) : ''; ?>" />
                        <!-- display error message -->
                        <?php if (isset($form_errors) && isset($form_errors['firstname_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['firstname_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label for="lastname_quot_upd" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="lastname_quot_upd" name="lastname_quot" placeholder="" value="<?php echo isset($form_data['lastname_quot']) ? esc_attr(stripslashes($form_data['lastname_quot'])) : ''; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['lastname_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['lastname_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label for="companyName_upd" class="form-label">Raison Social</label>
                        <input type="text" class="form-control" id="companyName_upd" name="companyName" placeholder="" value="<?php echo isset($form_data['companyName']) ? esc_attr(stripslashes($form_data['companyName'])) : ''; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['companyName'])) : ?>
                            <span class="error"><?php echo $form_errors['companyName']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="text" class="form-control" id="email_quot_upd" name="email_quot" placeholder="email@example.com" value="<?php echo isset($form_data['email_quot']) ? esc_attr(stripslashes($form_data['email_quot'])) : ''; ?>" />
                        </div>
                        <?php if (isset($form_errors) && isset($form_errors['email_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['email_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                            <input type="tel" class="form-control" id="phone_quot_upd" name="phone_quot" placeholder="0612345678" value="<?php echo isset($form_data['phone_quot']) ? esc_attr(stripslashes($form_data['phone_quot'])) : ''; ?>" />
                        </div>
                        <?php if (isset($form_errors) && isset($form_errors['phone_quot'])) : ?>
                            <span class="error"><?php echo $form_errors['phone_quot']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <label for="address_upd" class="form-label"> <?php echo !empty($form_data['companyName']) ? 'Adresse de la société:' : 'Adresse:'; ?> </label>
                        <input type="text" class="form-control" id="address_upd" name="address" placeholder="123 rue d'exemple, 12345 Ville" value="<?php echo isset($form_data['address']) ? esc_attr(stripslashes($form_data['address'])) : ''; ?>" />
                        <?php if (isset($form_errors) && isset($form_errors['address'])) : ?>
                            <span class="error"><?php echo $form_errors['address']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text"><label for="datetimeVisit_upd">Date et heure de la visite:</label></span>
                            <input class="form-control" type="datetime-local" id="datetimeVisit_upd" name="datetimeVisit" value="<?php echo isset($form_data['datetimeVisit']) ? esc_attr($form_data['datetimeVisit']) : date('Y-m-d\T10:00', strtotime('+1 day')); ?>" />
                        </div>
                        <?php if (isset($form_errors) && isset($form_errors['datetimeVisit'])) : ?>
                            <span class="error"><?php echo $form_errors['datetimeVisit']; ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="col-sm-6">
                        <label for="visitetype_upd" class="form-label">Type de visite:</label>
                        <select id="visitetype_upd" class="form-select" name="visitetype">
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
                        <label for="payment_upd" class="form-label">Mode paiement:</label>
                        <select id="payment_upd" class="form-select" name="payment">
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
                    <div id="info-visiteType_upd" class="hidden">
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
                                        <label class="input-group-text" for="nbPersons_upd">Nombre de personnes:</label>
                                        <input class="form-control" type="number" id="nbPersons_upd" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." min="0" value="<?php echo isset($form_data['nbPersons'][$i]) ? esc_attr($form_data['nbPersons'][$i]) : ''; ?>" />
                                    </div>
                                    <?php if (isset($form_errors) && isset($form_errors['nbPersons'][$i])) : ?>
                                        <span class="error"><?php echo $form_errors['nbPersons'][$i]; ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="col-12">
                                    <div id="tarif-group" class="input-group">
                                        <label class="input-group-text" for="ages_upd">Tarif:</label>
                                        <select class="form-select" id="ages_upd" name="ages[]">
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
                                    <label class="input-group-text" for="nbPersons_upd"><i class="pe-2 bi bi-person"></i>Nombre de personnes</label>
                                    <input class="form-control" type="number" id="nbPersons_upd" name="nbPersons[]" placeholder="1 - 14, ou 15 et plus..." min="0" value="<?php echo isset($form_data['nbPersons']) ? esc_attr($form_data['nbPersons']) : ''; ?>" />
                                </div>
                                <?php if (isset($form_errors) && isset($form_errors['nbPersons'])) : ?>
                                    <span class="error"><?php echo $form_errors['nbPersons']; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="col-12">
                                <div id="tarif-group" class="input-group">
                                    <label class="input-group-text" for="ages_upd"><i class="pe-2 bi bi-currency-euro"></i>Tarif</label>
                                    <select class="form-select" id="ages_upd" name="ages[]">
                                        <option value="default" <?php echo (isset($form_data['ages']) && $form_data['ages'] == 'default') ? ' selected' : ''; ?>>Choisissez...</option>
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
                        <label for="comment_upd" class="form-label">Commentaire</label>
                        <textarea id="comment_upd" class="form-control" name="comment" rows="4" placeholder="Votre commentaire..."><?php echo isset($form_data['comment']) ? esc_textarea(stripslashes($form_data['comment'])) : ''; ?></textarea>
                    </div>

                    <!-- Upload file -->
                    <!-- <div class="col-12">
                        <div class="input-group">
                            <input type="file" class="form-control" id="inputGroupFile04" aria-describedby="inputGroupFileAddon04" aria-label="Upload">
                            <button class="btn btn-warning" type="button" id="inputGroupFileAddon04">OK</button>
                        </div>
                    </div> -->

                    <!-- reCAPTCHA -->
                    <div class="col-12">
                        <div class="g-recaptcha" data-sitekey="<?php echo SITE_KEY ?>" data-action="LOGIN"></div>
                        <?php if (isset($form_errors) && isset($form_errors['recaptcha_quote'])) : ?>
                            <span class="error"><?php echo $form_errors['recaptcha_quote']; ?></span>
                        <?php endif; ?>
                    </div>

                    <hr class="my-4">

                    <input type="hidden" name="action" value="form_submission">
                    <input type="hidden" name="form_nonce" value="<?php echo wp_create_nonce('form_submit'); ?>">
                    <?php wp_nonce_field('form_submit', 'form_nonce'); ?>

                    <!-- Submit updated form or Return-->
                    <div class="container-fluid">
                        <div class="row justify-content-center">

                            <!-- Return back to the review page -->
                            <div class="p-2 col-md-auto">
                                <a class="btn btn-danger" href="<?php echo esc_url(remove_query_arg('update', wp_get_referer())); ?>">Retour</a>
                            </div>

                            <!-- Submit -->
                            <div class="p-2 col-md-auto">
                                <button class="btn btn-success" type="submit" name="submit-btn-quotation">Modifier</button>
                            </div>

                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>