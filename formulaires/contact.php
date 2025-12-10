<?php include '../includes/header.php'; ?>

<main class="cadre">
    <div class="container">
        <h1 class="title-h1 bull">Contactez-nous</h1>
        <p class="subtitle">N'hésitez pas à nous contacter pour toute question ou demande d'information.</p>
        
        <div class="contact-container">
            <!-- Section image à gauche -->
            <div class="contact-image-section">
                <div class="contact-image-wrapper">
                    <img src="../images/logo-icon/homme-avec-du-papier-1.webp" alt="Contactez-nous" class="contact-image">
                    <div class="contact-info-overlay">
                        <h3>Nous sommes là pour vous aider</h3>
                        <div class="contact-details">
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span>03 84 78 38 39</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span>contact@bindy-studio.fr</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>9 rue de la Gare<br>70000 Vallerois-le-Bois</span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-clock"></i>
                                <span>Lun-Ven: 9h-17h</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Section formulaire à droite -->
            <div class="contact-form-section">
                <form class="contact-form" action="../includes/process-contact.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom <span class="required">*</span></label>
                            <input type="text" id="nom" name="nom" required>
                        </div>
                        <div class="form-group">
                            <label for="prenom">Prénom <span class="required">*</span></label>
                            <input type="text" id="prenom" name="prenom" required>
                        </div>
                    </div>
                
                <div class="form-group">
                    <label for="societe">Société</label>
                    <input type="text" id="societe" name="societe">
                </div>
                
                <div class="form-group">
                    <label for="rue">Adresse</label>
                    <input type="text" id="rue" name="rue" placeholder="Rue et numéro">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="code_postal">Code postal</label>
                        <input type="text" id="code_postal" name="code_postal" maxlength="5">
                    </div>
                    <div class="form-group">
                        <label for="ville">Ville</label>
                        <input type="text" id="ville" name="ville">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">E-mail <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="telephone">Téléphone <span class="required">*</span></label>
                        <input type="tel" id="telephone" name="telephone" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="message">Message <span class="required">*</span></label>
                    <textarea id="message" name="message" rows="6" required placeholder="Votre message..."></textarea>
                </div>
                
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Envoyer le message</button>
                        <button type="reset" class="btn-reset">Effacer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
