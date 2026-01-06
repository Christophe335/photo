<?php include '../includes/header.php'; ?>

<main class="cadre">
    <div class="container">
        <h1 class="title-h1 bull">Demande de devis</h1>
        <p class="subtitle">Remplissez le formulaire et joignez vos images pour recevoir un devis personnalisé.</p>
        
        <div class="devis-container">
            <!-- Formulaire à gauche -->
            <div class="devis-form-section">
                <form class="devis-form" action="../includes/process-devis.php" method="POST" enctype="multipart/form-data">
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
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="4" placeholder="Décrivez votre projet..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-submit">Demander un devis</button>
                        <button type="reset" class="btn-reset">Effacer</button>
                    </div>
                    <!-- Input file déplacé ici pour être soumis avec le formulaire -->
                    <input type="file" id="imageUpload" name="images[]" multiple accept="image/*" style="display: none;">
                </form>
            </div>
            
            <!-- Module d'upload d'images à droite -->
            <div class="upload-section">
                <div class="upload-header">
                    <h3>Vos images</h3>
                    <p>Ajoutez jusqu'à 30 images (JPG, PNG, WebP - max 5MB chacune)</p>
                </div>
                
                <div class="file-upload-area">
                    <div class="upload-dropzone" onclick="document.getElementById('imageUpload').click()">
                        <div class="upload-icon">📁</div>
                        <p>Cliquez ici ou glissez-déposez vos images</p>
                        <span>Formats acceptés : JPG, PNG, WebP</span>
                    </div>
                </div>
                
                <div class="images-counter">
                    <span id="imageCount">0</span> / 30 images
                </div>
                
                <div class="images-preview" id="imagesPreview">
                    <!-- Les images apparaîtront ici -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal pour le recadrage -->
<div id="cropModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Recadrer l'image</h3>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="crop-layout">
                <!-- Aperçu sur le côté gauche -->
                <div class="crop-preview-sidebar">
                    <h4>Aperçu final</h4>
                    <div class="preview-wrapper">
                        <img id="cropPreviewImage" src="" alt="Aperçu du recadrage" loading="lazy">
                        <div class="preview-info"></div>
                    </div>
                </div>
                
                <!-- Zone principale de recadrage -->
                <div class="crop-main">
                    <div class="crop-container">
                        <img id="cropImage" src="" alt="Image à recadrer" loading="lazy">
                    </div>
                    
                    <!-- Contrôles en bas -->
                    <div class="crop-controls-layout">
                        <!-- Boutons principaux à gauche -->
                        <div class="crop-controls">
                            <button type="button" class="btn-crop-confirm">Confirmer</button>
                            <button type="button" class="btn-crop-cancel">Annuler</button>
                        </div>
                        
                        <!-- Contrôles de zoom à droite -->
                        <div class="zoom-controls-inline">
                            <button type="button" class="orientation-btn" id="orientationToggle" title="Basculer Portrait/Paysage">⟲</button>
                            <button type="button" class="zoom-btn-inline" id="zoomOutInline" aria-label="Diminuer">-</button>
                            <span class="zoom-display-inline">100%</span>
                            <button type="button" class="zoom-btn-inline" id="zoomInInline" aria-label="Augmenter">+</button>
                            <button type="button" class="zoom-reset-inline">Reset</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../js/simple-crop.js"></script>
<script src="../js/image-upload.js"></script>
<?php if (!empty($_GET['sent'])): ?>
    <div id="sentPopup" style="position:fixed;right:20px;bottom:20px;z-index:9999;display:flex;align-items:center;gap:12px;background:#fff;border-radius:10px;padding:14px 18px;box-shadow:0 6px 24px rgba(0,0,0,0.15);border:1px solid #e6e6e6">
        <img src="../images/logo-icon/logo3.svg" alt="Bindy Studio" style="height:48px">
        <div style="min-width:200px">
            <strong>Demande envoyée</strong>
            <div style="color:#666;font-size:14px">Votre demande de devis a bien été envoyée à Bindy Studio.</div>
        </div>
    </div>
    <script>
        (function(){
            var popup = document.getElementById('sentPopup');
            if (!popup) return;
            popup.style.opacity = 0;
            popup.style.transition = 'opacity 300ms ease';
            setTimeout(function(){ popup.style.opacity = 1; }, 50);
            setTimeout(function(){ popup.style.opacity = 0; setTimeout(function(){ popup.remove(); }, 350); }, 4500);
        })();
    </script>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
