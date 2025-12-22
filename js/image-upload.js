// Gestion du module d'upload d'images avec prévisualisation et recadrage
document.addEventListener("DOMContentLoaded", function () {
  const imageUpload = document.getElementById("imageUpload");
  const imagesPreview = document.getElementById("imagesPreview");
  const imageCount = document.getElementById("imageCount");
  const uploadDropzone = document.querySelector(".upload-dropzone");
  const cropModal = document.getElementById("cropModal");
  const closeModal = document.querySelector(".close-modal");
  const cropImage = document.getElementById("cropImage");
  const btnCropConfirm = document.querySelector(".btn-crop-confirm");
  const btnCropCancel = document.querySelector(".btn-crop-cancel");

  // Variables pour le zoom
  let currentZoom = 1;
  let originalImageWidth = 0;
  let originalImageHeight = 0;

  // Variables pour l'orientation
  let currentFormatWidth = 10;
  let currentFormatHeight = 10;

  let uploadedImages = [];
  let currentCropIndex = -1;
  let cropper = null;
  const maxImages = 30;

  // Gestion du drag & drop
  uploadDropzone.addEventListener("dragover", function (e) {
    e.preventDefault();
    uploadDropzone.classList.add("drag-over");
  });

  uploadDropzone.addEventListener("dragleave", function (e) {
    e.preventDefault();
    uploadDropzone.classList.remove("drag-over");
  });

  uploadDropzone.addEventListener("drop", function (e) {
    e.preventDefault();
    uploadDropzone.classList.remove("drag-over");
    const files = e.dataTransfer.files;
    handleFiles(files);
  });

  // Gestion de la sélection de fichiers
  imageUpload.addEventListener("change", function (e) {
    handleFiles(e.target.files);
  });

  // Traitement des fichiers
  function handleFiles(files) {
    for (let i = 0; i < files.length; i++) {
      if (uploadedImages.length >= maxImages) {
        alert(`Maximum ${maxImages} images autorisées`);
        break;
      }

      const file = files[i];
      if (file.type.startsWith("image/")) {
        if (file.size > 5 * 1024 * 1024) {
          // 5MB max
          alert(`L'image "${file.name}" est trop volumineuse (max 5MB)`);
          continue;
        }

        addImageToPreview(file);
      }
    }
    updateImageCount();
  }

  // Ajouter une image à la prévisualisation
  function addImageToPreview(file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      const imageData = {
        file: file,
        dataUrl: e.target.result,
        originalDataUrl: e.target.result,
      };

      uploadedImages.push(imageData);
      renderImagePreview(imageData, uploadedImages.length - 1);
      updateImageCount();
    };
    reader.readAsDataURL(file);
  }

  // Afficher la prévisualisation d'une image
  function renderImagePreview(imageData, index) {
    console.log(
      `Rendu vignette ${index}:`,
      imageData.dataUrl.substring(0, 50) + "..."
    );

    const imageItem = document.createElement("div");
    imageItem.className = "image-item";
    // Conserver le nom de fichier original pour réutilisation ultérieure
    const originalName = (imageData.file && imageData.file.name) ? imageData.file.name : `image_${index + 1}.jpg`;
    imageItem.setAttribute('data-filename', originalName);
    imageItem.innerHTML = `
        <img src="${imageData.dataUrl}" alt="${originalName}">
        <div class="image-controls">
          <button type="button" class="btn-crop" onclick="openCropModal(${index})" title="Recadrer">✂️</button>
          <button type="button" class="btn-delete" onclick="deleteImage(${index})" title="Supprimer">🗑️</button>
        </div>
      `;

    imagesPreview.appendChild(imageItem);
  }

  // Mettre à jour le compteur d'images
  function updateImageCount() {
    imageCount.textContent = uploadedImages.length;
    syncUploadedImages();
  }

  // Synchroniser les images uploadées avec la page (rendre visible le bouton final)
  function syncUploadedImages() {
    try {
      // Exposer une version légère des images sur window pour que perso.js y accède
      window.fichiersUploades = uploadedImages.map(img => ({ name: img.file.name, size: img.file.size, dataUrl: img.dataUrl }));
      // Afficher/masquer le bloc d'actions d'upload
      const uploadActions = document.getElementById('uploadActions');
      if (uploadActions) {
        uploadActions.style.display = uploadedImages.length > 0 ? 'block' : 'none';
      }
    } catch (e) {
      console.error('Erreur syncUploadedImages:', e);
    }
  }

  // Supprimer une image
  window.deleteImage = function (index) {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette image ?")) {
      uploadedImages.splice(index, 1);
      refreshPreview();
      updateImageCount();
    }
  };

  // Rafraîchir la prévisualisation
  function refreshPreview() {
    imagesPreview.innerHTML = "";
    uploadedImages.forEach((imageData, index) => {
      renderImagePreview(imageData, index);
    });
  }

  // Ouvrir le modal de recadrage
  window.openCropModal = function (index) {
    currentCropIndex = index;
    const imageData = uploadedImages[index];
    cropImage.src = imageData.originalDataUrl;
    cropModal.style.display = "block";

    // Initialiser l'interface de recadrage
    setTimeout(() => {
      initCustomCropper();

      // Démarrer automatiquement avec le format 10×10
      setTimeout(() => {
        setCropFormat(10, 10);
        // Afficher automatiquement l'aperçu
        showPreviewSidebar();
      }, 200);
    }, 100);
  };

  // Système de recadrage personnalisé
  let cropData = { x: 0, y: 0, width: 200, height: 200 };
  let isDragging = false;
  let isResizing = false;
  let resizeHandle = "";
  let startX = 0;
  let startY = 0;

  // Fonction pour basculer entre portrait et paysage
  function toggleOrientation() {
    // Échanger largeur et hauteur
    const newWidth = currentFormatHeight;
    const newHeight = currentFormatWidth;

    console.log(
      `Basculement: ${currentFormatWidth}×${currentFormatHeight} → ${newWidth}×${newHeight}`
    );

    // Appliquer le nouveau format
    setCropFormat(newWidth, newHeight);
  }

  // Fonction pour mettre à jour l'état visuel des boutons de format
  function updateFormatButtonStates(selectedWidth, selectedHeight) {
    const formatBtns = document.querySelectorAll(".format-btn");
    formatBtns.forEach((btn) => {
      const format = btn.dataset.format;
      // Vérifier les deux orientations possibles
      if (
        format === `${selectedWidth}x${selectedHeight}` ||
        format === `${selectedHeight}x${selectedWidth}`
      ) {
        // Bouton sélectionné - couleurs inversées
        btn.style.background = "#24256d";
        btn.style.color = "white";
        btn.style.fontWeight = "bold";
      } else {
        // Bouton non sélectionné - style normal
        btn.style.background = "white";
        btn.style.color = "#24256d";
        btn.style.fontWeight = "normal";
      }
    });
  }

  // Fonction simple pour définir un format de recadrage fixe
  function setCropFormat(widthCm, heightCm) {
    // Mémoriser les dimensions actuelles
    currentFormatWidth = widthCm;
    currentFormatHeight = heightCm;

    // Mettre à jour l'état des boutons
    updateFormatButtonStates(widthCm, heightCm);

    // Utiliser le système simple
    if (window.createSimpleCropGrid) {
      window.createSimpleCropGrid(widthCm, heightCm);
      // Mettre à jour l'aperçu automatiquement
      setTimeout(() => showPreviewSidebar(), 100);
      return;
    }
    const img = document.querySelector(".crop-wrapper img");
    if (!img) return;

    console.log(`Format demandé: ${widthCm} × ${heightCm} cm`);

    // Taille de grille basée sur les cm (1 cm = 40 pixels pour que ce soit plus raisonnable)
    const pixelsPerCm = 40;
    const displayWidth = widthCm * pixelsPerCm;
    const displayHeight = heightCm * pixelsPerCm;

    console.log(`Taille grille: ${displayWidth} × ${displayHeight} px`);

    // Vérifier si l'image est assez grande pour la grille
    const currentImageWidth = img.offsetWidth;
    const currentImageHeight = img.offsetHeight;

    // Si la grille est plus grande que l'image, zoomer l'image en gardant le ratio
    const requiredZoomX = (displayWidth * 1.3) / originalImageWidth; // 130% de la grille
    const requiredZoomY = (displayHeight * 1.3) / originalImageHeight;
    const requiredZoom = Math.max(requiredZoomX, requiredZoomY, currentZoom); // Ne jamais dézoomer

    if (requiredZoom > currentZoom) {
      console.log(`Zoom requis: ${requiredZoom}`);
      updateZoom(
        document.querySelector(".crop-wrapper"),
        img,
        document.querySelector(".zoom-display"),
        requiredZoom
      );

      // Attendre que le zoom soit appliqué
      setTimeout(() => {
        setCropDimensionsFixed(displayWidth, displayHeight, widthCm, heightCm);
      }, 100);
    } else {
      // Appliquer directement les dimensions de la grille
      setCropDimensionsFixed(displayWidth, displayHeight, widthCm, heightCm);
    }
  }

  // Fonction pour créer une grille fixe non-redimensionnable
  function setCropDimensionsFixed(
    width,
    height,
    originalWidthCm,
    originalHeightCm
  ) {
    const img = document.querySelector(".crop-wrapper img");
    if (!img) return;

    console.log(`setCropDimensionsFixed: ${width} × ${height} px`);

    // Stocker les vraies dimensions pour l'affichage
    window.currentFormatCm = {
      width: originalWidthCm,
      height: originalHeightCm,
    };

    // Définir les dimensions FIXES de la grille
    cropData.width = width;
    cropData.height = height;

    // Centrer la grille sur l'image, mais si elle est trop grande, la positionner à 0,0
    const imageWidth = img.offsetWidth;
    const imageHeight = img.offsetHeight;

    console.log(`Image: ${imageWidth} × ${imageHeight} px`);

    // Ajuster les dimensions de la grille selon l'échelle d'affichage
    const scale = window.displayScale || 1;
    const displayCropWidth = cropData.width * scale;
    const displayCropHeight = cropData.height * scale;

    // Centrer la grille d'affichage
    cropData.x = (imageWidth - displayCropWidth) / 2;
    cropData.y = (imageHeight - displayCropHeight) / 2;

    // Stocker les vraies dimensions pour les calculs, mais utiliser les dimensions d'affichage pour la position
    cropData.displayWidth = displayCropWidth;
    cropData.displayHeight = displayCropHeight;

    console.log(`Position grille: ${cropData.x}, ${cropData.y}`);

    // Recréer la grille sans poignées de redimensionnement
    // createFixedCropBox();
    // updateCropBoxFixed();
  }

  // Créer une grille fixe (style Facebook) - DESACTIVE
  function createFixedCropBox() {
    // Fonction désactivée - on utilise maintenant le système simple
    return;

    // Supprimer l'ancienne grille
    const oldBox = document.querySelector(".crop-box");
    if (oldBox) oldBox.remove();

    const wrapper = document.querySelector(".crop-wrapper");
    if (!wrapper) return;

    const cropBox = document.createElement("div");
    cropBox.className = "crop-box crop-box-fixed";

    // Définir tous les styles directement et explicitement
    cropBox.style.position = "absolute";
    cropBox.style.border = "3px solid #24256d";
    cropBox.style.background = "rgba(36, 37, 109, 0.1)";
    cropBox.style.cursor = "move";
    cropBox.style.boxSizing = "border-box";
    cropBox.style.boxShadow = "0 0 0 9999px rgba(0, 0, 0, 0.5)";
    cropBox.style.zIndex = "10";
    cropBox.style.minWidth = "50px";
    cropBox.style.minHeight = "50px";

    // Utiliser les dimensions d'affichage si disponibles
    const displayW = cropData.displayWidth || cropData.width;
    const displayH = cropData.displayHeight || cropData.height;

    cropBox.style.width = displayW + "px";
    cropBox.style.height = displayH + "px";
    cropBox.style.left = cropData.x + "px";
    cropBox.style.top = cropData.y + "px";

    console.log(
      `Création grille: ${displayW}×${displayH} à (${cropData.x},${cropData.y})`
    );

    wrapper.appendChild(cropBox);

    // Vérifier que la grille est bien créée
    setTimeout(() => {
      console.log(
        `Grille après création: ${cropBox.offsetWidth}×${cropBox.offsetHeight}`
      );
    }, 10);

    // Gérer le déplacement
    let dragStartX = 0;
    let dragStartY = 0;
    let isDraggingFixed = false;

    cropBox.addEventListener("mousedown", function (e) {
      e.preventDefault();
      e.stopPropagation();

      console.log("Début du drag sur grille fixe");
      isDraggingFixed = true;

      // Calculer le décalage initial correctement
      const rect = cropBox.getBoundingClientRect();
      dragStartX = e.clientX - rect.left;
      dragStartY = e.clientY - rect.top;

      const mouseMoveHandler = function (e) {
        if (!isDraggingFixed) return;

        // Calculer la nouvelle position relative au wrapper
        const wrapper = document.querySelector(".crop-wrapper");
        const wrapperRect = wrapper.getBoundingClientRect();

        cropData.x = e.clientX - wrapperRect.left - dragStartX;
        cropData.y = e.clientY - wrapperRect.top - dragStartY;

        console.log(`Drag position: ${cropData.x}, ${cropData.y}`);
        updateCropBoxFixed();
      };

      const mouseUpHandler = function () {
        console.log("Fin du drag sur grille fixe");
        isDraggingFixed = false;
        document.removeEventListener("mousemove", mouseMoveHandler);
        document.removeEventListener("mouseup", mouseUpHandler);
      };

      document.addEventListener("mousemove", mouseMoveHandler);
      document.addEventListener("mouseup", mouseUpHandler);
    });
  }

  // Mise à jour de l'affichage pour grille fixe
  function updateCropBoxFixed() {
    const cropBox = document.querySelector(".crop-box-fixed");
    const dimensionsDisplay = document.querySelector(".crop-dimensions");

    if (cropBox && dimensionsDisplay) {
      const displayW = cropData.displayWidth || cropData.width;
      const displayH = cropData.displayHeight || cropData.height;

      console.log(
        `Updating cropBox: ${displayW} × ${displayH} px at (${cropData.x}, ${cropData.y})`
      );

      cropBox.style.left = cropData.x + "px";
      cropBox.style.top = cropData.y + "px";
      cropBox.style.width = displayW + "px";
      cropBox.style.height = displayH + "px";

      // Forcer l'affichage avec !important
      cropBox.style.setProperty("width", displayW + "px", "important");
      cropBox.style.setProperty("height", displayH + "px", "important");

      // Afficher les vraies dimensions en cm
      if (window.currentFormatCm) {
        dimensionsDisplay.textContent = `${window.currentFormatCm.width} × ${window.currentFormatCm.height} cm`;
      }
    } else {
      console.log("cropBox ou dimensionsDisplay non trouvé");
    }
  }

  function initCustomCropper() {
    const container = document.querySelector(".crop-container");
    container.innerHTML = "";

    // Wrapper pour l'image et la zone de recadrage
    const wrapper = document.createElement("div");
    wrapper.className = "crop-wrapper";
    wrapper.style.position = "relative";
    wrapper.style.display = "inline-block";

    // Image
    const img = document.createElement("img");
    img.src = uploadedImages[currentCropIndex].originalDataUrl;
    img.style.display = "block";
    img.style.maxWidth = "100%";
    img.style.maxHeight = "400px";
    img.style.userSelect = "none";

    // Zone de recadrage - DESACTIVEE (on utilise le système simple)
    /* 
    const cropBox = document.createElement('div');
    cropBox.className = 'crop-box';
    cropBox.style.cssText = `
      position: absolute;
      border: 2px dashed #24256d;
      background: rgba(36, 37, 109, 0.1);
      cursor: move;
      box-sizing: border-box;
    `;
    
    // Poignées de redimensionnement
    const handles = ['nw', 'ne', 'sw', 'se', 'n', 's', 'e', 'w'];*/
    /* 
    handles.forEach(handle => {
      const handleEl = document.createElement('div');
      handleEl.className = `crop-handle crop-handle-${handle}`;
      handleEl.style.cssText = `
        position: absolute;
        width: 10px;
        height: 10px;
        background: #24256d;
        border: 2px solid white;
        border-radius: 50%;
        cursor: ${handle.includes('n') || handle.includes('s') ? 'ns' : 'ew'}-resize;
      `;
      
      // Position des poignées
      if (handle.includes('n')) handleEl.style.top = '-5px';
      if (handle.includes('s')) handleEl.style.bottom = '-5px';
      if (handle.includes('w')) handleEl.style.left = '-5px';
      if (handle.includes('e')) handleEl.style.right = '-5px';
      if (handle === 'n' || handle === 's') handleEl.style.left = '50%';
      if (handle === 'e' || handle === 'w') handleEl.style.top = '50%';
      if (handle === 'n' || handle === 's') handleEl.style.transform = 'translateX(-50%)';
      if (handle === 'e' || handle === 'w') handleEl.style.transform = 'translateY(-50%)';
      
      cropBox.appendChild(handleEl);
    });
    
    // Affichage des dimensions
    const dimensionsDisplay = document.createElement('div');
    dimensionsDisplay.className = 'crop-dimensions';
    dimensionsDisplay.style.cssText = `
      position: absolute;
      top: -30px;
      left: 0;
      background: #24256d;
      color: white;
      padding: 4px 8px;
      border-radius: 4px;
      font-size: 12px;
      font-weight: bold;
    `;
    cropBox.appendChild(dimensionsDisplay);
    
    wrapper.appendChild(cropBox);
    */

    wrapper.appendChild(img);

    // Contrôles de zoom
    const zoomControls = document.createElement("div");
    zoomControls.className = "zoom-controls";
    zoomControls.style.cssText = `
      margin-top: 10px;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    `;

    const zoomOutBtn = document.createElement("button");
    zoomOutBtn.textContent = "−";
    zoomOutBtn.className = "zoom-btn";
    zoomOutBtn.type = "button";
    zoomOutBtn.style.cssText = `
      width: 40px;
      height: 40px;
      border: 2px solid #24256d;
      background: white;
      color: #24256d;
      border-radius: 6px;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    `;

    const zoomDisplay = document.createElement("span");
    zoomDisplay.className = "zoom-display";
    zoomDisplay.textContent = "100%";
    zoomDisplay.style.cssText = `
      font-weight: bold;
      color: #24256d;
      min-width: 50px;
      text-align: center;
    `;

    const zoomInBtn = document.createElement("button");
    zoomInBtn.textContent = "+";
    zoomInBtn.className = "zoom-btn";
    zoomInBtn.type = "button";
    zoomInBtn.style.cssText = `
      width: 40px;
      height: 40px;
      border: 2px solid #24256d;
      background: white;
      color: #24256d;
      border-radius: 6px;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    `;

    const resetZoomBtn = document.createElement("button");
    resetZoomBtn.textContent = "Reset";
    resetZoomBtn.className = "zoom-reset-btn";
    resetZoomBtn.type = "button";
    resetZoomBtn.style.cssText = `
      padding: 8px 12px;
      border: 1px solid #666;
      background: #f5f5f5;
      color: #333;
      border-radius: 4px;
      font-size: 12px;
      cursor: pointer;
    `;

    // Effets hover pour les boutons de zoom
    zoomInBtn.addEventListener("mouseenter", () => {
      zoomInBtn.style.background = "#24256d";
      zoomInBtn.style.color = "white";
    });
    zoomInBtn.addEventListener("mouseleave", () => {
      zoomInBtn.style.background = "white";
      zoomInBtn.style.color = "#24256d";
    });

    zoomOutBtn.addEventListener("mouseenter", () => {
      zoomOutBtn.style.background = "#24256d";
      zoomOutBtn.style.color = "white";
    });
    zoomOutBtn.addEventListener("mouseleave", () => {
      zoomOutBtn.style.background = "white";
      zoomOutBtn.style.color = "#24256d";
    });

    resetZoomBtn.addEventListener("mouseenter", () => {
      resetZoomBtn.style.background = "#e0e0e0";
    });
    resetZoomBtn.addEventListener("mouseleave", () => {
      resetZoomBtn.style.background = "#f5f5f5";
    });

    zoomControls.appendChild(zoomOutBtn);
    zoomControls.appendChild(zoomDisplay);
    zoomControls.appendChild(zoomInBtn);
    zoomControls.appendChild(resetZoomBtn);

    // Boutons de format
    const formatControls = document.createElement("div");
    formatControls.className = "format-controls";
    formatControls.style.cssText = `
      margin-top: 15px;
      text-align: center;
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      justify-content: center;
    `;

    const formats = [
      { name: "10 × 10 cm", width: 10, height: 10 },
      { name: "10 × 15 cm", width: 10, height: 15 },
      { name: "15 × 15 cm", width: 15, height: 15 },
      { name: "15 × 20 cm", width: 15, height: 20 },
      { name: "20 × 20 cm", width: 20, height: 20 },
      { name: "20 × 30 cm", width: 20, height: 30 },
      { name: "A5", width: 14.8, height: 21 },
      { name: "A4", width: 21, height: 29.7 },
      { name: "A3", width: 29.7, height: 42 },
    ];

    formats.forEach((format) => {
      const btn = document.createElement("button");
      btn.textContent = format.name;
      btn.className = "format-btn";
      btn.type = "button";
      btn.dataset.format = `${format.width}x${format.height}`;
      btn.style.cssText = `
        padding: 6px 10px;
        border: 1px solid #24256d;
        background: white;
        color: #24256d;
        border-radius: 4px;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s ease;
      `;
      btn.addEventListener("click", () =>
        setCropFormat(format.width, format.height)
      );
      formatControls.appendChild(btn);
    });

    container.appendChild(wrapper);
    // container.appendChild(zoomControls); // Supprimé - on utilise les contrôles inline
    container.appendChild(formatControls);

    // Attendre que l'image soit chargée pour initialiser la position
    img.onload = function () {
      // Sauvegarder les dimensions réelles de l'image (pas l'affichage)
      originalImageWidth = img.naturalWidth || img.width;
      originalImageHeight = img.naturalHeight || img.height;
      currentZoom = 1;

      console.log(
        "Image chargée - dimensions réelles:",
        originalImageWidth,
        "x",
        originalImageHeight
      );

      // Position initiale au centre de l'image (40% de la taille)
      resetCropPosition();
      updateCropBox();
    };

    // Events
    // setupCropEvents(cropBox, wrapper); // Désactivé car cropBox n'existe plus
    // setupZoomEvents(wrapper, img, zoomInBtn, zoomOutBtn, resetZoomBtn, zoomDisplay); // Supprimé - anciens contrôles

    // Connecter les boutons de zoom inline (nouveaux contrôles compacts)
    setupInlineZoomEvents(wrapper, img);
  }

  function updateCropBox() {
    const cropBox = document.querySelector(".crop-box");
    const dimensionsDisplay = document.querySelector(".crop-dimensions");

    if (cropBox && dimensionsDisplay) {
      cropBox.style.left = cropData.x + "px";
      cropBox.style.top = cropData.y + "px";
      cropBox.style.width = cropData.width + "px";
      cropBox.style.height = cropData.height + "px";

      // Convertir en cm pour l'affichage (30 pixels = 1 cm)
      const pixelsPerCm = 30;
      const widthCm = (cropData.width / pixelsPerCm).toFixed(1);
      const heightCm = (cropData.height / pixelsPerCm).toFixed(1);
      dimensionsDisplay.textContent = `${widthCm} × ${heightCm} cm`;
    }
  }

  function setupCropEvents(cropBox, wrapper) {
    // Déplacement de la zone de recadrage
    cropBox.addEventListener("mousedown", function (e) {
      if (e.target.classList.contains("crop-handle")) {
        isResizing = true;
        // Extraire la direction de la classe (crop-handle-nw -> nw)
        const classes = e.target.className.split(" ");
        const handleClass = classes.find((cls) =>
          cls.startsWith("crop-handle-")
        );
        resizeHandle = handleClass ? handleClass.split("-")[2] : "";
        console.log("Resize handle detected:", resizeHandle); // Debug
      } else {
        isDragging = true;
        console.log("Dragging started"); // Debug
      }

      startX = e.clientX;
      startY = e.clientY;
      e.preventDefault();
    });

    document.addEventListener("mousemove", function (e) {
      if (!isDragging && !isResizing) return;

      const img = wrapper.querySelector("img");
      const imgWidth = img.offsetWidth;
      const imgHeight = img.offsetHeight;

      if (isDragging) {
        // Pour la grille fixe, permettre le déplacement même hors de l'image
        cropData.x = e.clientX - startX;
        cropData.y = e.clientY - startY;
      } else if (isResizing && !document.querySelector(".crop-box-fixed")) {
        // Le redimensionnement n'est autorisé que pour les grilles classiques
        const deltaX = e.clientX - startX;
        const deltaY = e.clientY - startY;
        handleResize(deltaX, deltaY, imgWidth, imgHeight);
        startX = e.clientX;
        startY = e.clientY;
      }

      // Utiliser la fonction d'update appropriée
      if (document.querySelector(".crop-box-fixed")) {
        updateCropBoxFixed();
      } else {
        updateCropBox();
      }
    });

    document.addEventListener("mouseup", function () {
      isDragging = false;
      isResizing = false;
      resizeHandle = "";
    });
  }

  function handleResize(deltaX, deltaY, imgWidth, imgHeight) {
    const minSize = 50;
    console.log(
      "Handling resize:",
      resizeHandle,
      "deltaX:",
      deltaX,
      "deltaY:",
      deltaY
    ); // Debug

    switch (resizeHandle) {
      case "se":
        cropData.width = Math.max(
          minSize,
          Math.min(imgWidth - cropData.x, cropData.width + deltaX)
        );
        cropData.height = Math.max(
          minSize,
          Math.min(imgHeight - cropData.y, cropData.height + deltaY)
        );
        break;
      case "sw":
        const newWidth = cropData.width - deltaX;
        if (newWidth >= minSize && cropData.x + deltaX >= 0) {
          cropData.x += deltaX;
          cropData.width = newWidth;
        }
        cropData.height = Math.max(
          minSize,
          Math.min(imgHeight - cropData.y, cropData.height + deltaY)
        );
        break;
      case "ne":
        cropData.width = Math.max(
          minSize,
          Math.min(imgWidth - cropData.x, cropData.width + deltaX)
        );
        const newHeight = cropData.height - deltaY;
        if (newHeight >= minSize && cropData.y + deltaY >= 0) {
          cropData.y += deltaY;
          cropData.height = newHeight;
        }
        break;
      case "nw":
        const newW = cropData.width - deltaX;
        const newH = cropData.height - deltaY;
        if (newW >= minSize && cropData.x + deltaX >= 0) {
          cropData.x += deltaX;
          cropData.width = newW;
        }
        if (newH >= minSize && cropData.y + deltaY >= 0) {
          cropData.y += deltaY;
          cropData.height = newH;
        }
        break;
      case "n":
        const newHN = cropData.height - deltaY;
        if (newHN >= minSize && cropData.y + deltaY >= 0) {
          cropData.y += deltaY;
          cropData.height = newHN;
        }
        break;
      case "s":
        cropData.height = Math.max(
          minSize,
          Math.min(imgHeight - cropData.y, cropData.height + deltaY)
        );
        break;
      case "e":
        cropData.width = Math.max(
          minSize,
          Math.min(imgWidth - cropData.x, cropData.width + deltaX)
        );
        break;
      case "w":
        const newWW = cropData.width - deltaX;
        if (newWW >= minSize && cropData.x + deltaX >= 0) {
          cropData.x += deltaX;
          cropData.width = newWW;
        }
        break;
    }
  }

  function resetCropPosition() {
    const displayWidth = originalImageWidth * currentZoom;
    const displayHeight = originalImageHeight * currentZoom;

    cropData.width = Math.max(100, displayWidth * 0.4);
    cropData.height = Math.max(100, displayHeight * 0.4);
    cropData.x = (displayWidth - cropData.width) / 2;
    cropData.y = (displayHeight - cropData.height) / 2;
  }

  function updateZoom(wrapper, img, zoomDisplay, newZoom) {
    const minZoom = 0.5;
    const maxZoom = 3;

    currentZoom = Math.max(minZoom, Math.min(maxZoom, newZoom));

    // Recalculer les dimensions originales si nécessaire
    if (originalImageWidth === 0 || originalImageHeight === 0) {
      // Récupérer les vraies dimensions de l'image
      const tempImg = new Image();
      tempImg.src = img.src;
      tempImg.onload = function () {
        originalImageWidth = tempImg.width;
        originalImageHeight = tempImg.height;
        // Relancer le zoom avec les bonnes dimensions
        updateZoom(
          document.querySelector(".crop-wrapper"),
          img,
          zoomDisplay,
          newZoom
        );
      };
      return;
    }

    // Calculer les dimensions de base d'affichage (taille optimale dans le modal)
    const maxModalWidth = window.innerWidth * 0.8;
    const maxModalHeight = window.innerHeight * 0.6;

    // Ratio de l'image originale
    const imageRatio = originalImageWidth / originalImageHeight;

    let baseDisplayWidth, baseDisplayHeight;
    if (imageRatio > maxModalWidth / maxModalHeight) {
      // Image plus large que haute
      baseDisplayWidth = maxModalWidth;
      baseDisplayHeight = maxModalWidth / imageRatio;
    } else {
      // Image plus haute que large
      baseDisplayHeight = maxModalHeight;
      baseDisplayWidth = maxModalHeight * imageRatio;
    }

    // Appliquer le zoom à ces dimensions de base
    const displayWidth = baseDisplayWidth * currentZoom;
    const displayHeight = baseDisplayHeight * currentZoom;

    // Appliquer les dimensions d'affichage
    img.style.width = displayWidth + "px";
    img.style.height = displayHeight + "px";
    img.style.maxWidth = "none";
    img.style.maxHeight = "none";
    img.style.transform = "none";

    // Stocker l'échelle d'affichage pour les calculs de position
    window.displayScale = displayWidth / newWidth;

    console.log(
      `Zoom appliqué: ${currentZoom}x -> ${newWidth}×${newHeight}px (affiché: ${displayWidth}×${displayHeight}px)`
    );

    // Garder la zone de recadrage dans les limites de l'image zoomée
    cropData.x = Math.max(0, Math.min(newWidth - cropData.width, cropData.x));
    cropData.y = Math.max(0, Math.min(newHeight - cropData.height, cropData.y));

    // Mettre à jour l'affichage
    zoomDisplay.textContent = Math.round(currentZoom * 100) + "%";
    updateCropBox();
  }

  function setupZoomEvents(
    wrapper,
    img,
    zoomInBtn,
    zoomOutBtn,
    resetBtn,
    zoomDisplay
  ) {
    // Zoom avec les boutons
    zoomInBtn.addEventListener("click", function () {
      updateZoom(wrapper, img, zoomDisplay, currentZoom + 0.1);
    });

    zoomOutBtn.addEventListener("click", function () {
      updateZoom(wrapper, img, zoomDisplay, currentZoom - 0.1);
    });

    resetBtn.addEventListener("click", function () {
      updateZoom(wrapper, img, zoomDisplay, 1);
      resetCropPosition();
      updateCropBox();
    });

    // Zoom avec la molette de la souris
    wrapper.addEventListener("wheel", function (e) {
      e.preventDefault();
      const delta = e.deltaY > 0 ? -0.05 : 0.05;
      updateZoom(wrapper, img, zoomDisplay, currentZoom + delta);
    });
  }

  function setupInlineZoomEvents(wrapper, img) {
    const zoomInInline = document.getElementById("zoomInInline");
    const zoomOutInline = document.getElementById("zoomOutInline");
    const zoomResetInline = document.querySelector(".zoom-reset-inline");
    const zoomDisplayInline = document.querySelector(".zoom-display-inline");
    const orientationToggle = document.getElementById("orientationToggle");

    if (orientationToggle) {
      orientationToggle.addEventListener("click", function () {
        toggleOrientation();
      });
    }

    if (zoomInInline) {
      zoomInInline.addEventListener("click", function () {
        updateZoom(wrapper, img, zoomDisplayInline, currentZoom + 0.1);
      });
    }

    if (zoomOutInline) {
      zoomOutInline.addEventListener("click", function () {
        updateZoom(wrapper, img, zoomDisplayInline, currentZoom - 0.1);
      });
    }

    if (zoomResetInline) {
      zoomResetInline.addEventListener("click", function () {
        updateZoom(wrapper, img, zoomDisplayInline, 1);
        resetCropPosition();
        updateCropBox();
      });
    }

    // Zoom avec la molette de la souris
    wrapper.addEventListener("wheel", function (e) {
      e.preventDefault();
      const delta = e.deltaY > 0 ? -0.05 : 0.05;
      updateZoom(wrapper, img, zoomDisplayInline, currentZoom + delta);
    });
  }

  // Fermer le modal
  function closeCropModal() {
    cropModal.style.display = "none";
    if (cropper) {
      cropper.destroy();
      cropper = null;
    }
    currentCropIndex = -1;
  }

  closeModal.addEventListener("click", closeCropModal);
  btnCropCancel.addEventListener("click", closeCropModal);

  // Fonction pour afficher l'aperçu automatiquement
  async function showPreviewSidebar() {
    if (currentCropIndex >= 0 && window.generateCropPreview) {
      const previewSidebar = document.querySelector(".crop-preview-sidebar");
      const previewImage = document.getElementById("cropPreviewImage");
      const previewInfo = document.querySelector(".preview-info");

      try {
        // Générer l'aperçu
        const previewDataUrl = await window.generateCropPreview();

        if (previewDataUrl) {
          previewImage.src = previewDataUrl;

          // Récupérer les données de recadrage
          const cropData = window.getSimpleCropData();
          if (cropData) {
            previewInfo.innerHTML = `<strong>${cropData.realWidthCm} × ${cropData.realHeightCm} cm</strong><br>300 DPI`;
          }

          previewSidebar.style.display = "block";
        }
      } catch (error) {
        console.error("Erreur lors de la génération de l'aperçu:", error);
      }
    }
  }

  // Fermer le modal en cliquant en dehors
  cropModal.addEventListener("click", function (e) {
    if (e.target === cropModal) {
      closeCropModal();
    }
  });

  // Confirmer le recadrage
  btnCropConfirm.addEventListener("click", function () {
    if (currentCropIndex >= 0) {
      const wrapper = document.querySelector(".crop-wrapper");
      const img = wrapper.querySelector("img");

      // Récupérer les données de recadrage du système simple
      const simpleCropData = window.getSimpleCropData
        ? window.getSimpleCropData()
        : null;

      if (!simpleCropData) {
        console.error(
          "Pas de données de recadrage disponibles - système simple non initialisé"
        );
        alert("Erreur: Veuillez d'abord sélectionner un format de recadrage");
        return;
      }

      console.log("Données de recadrage simple récupérées:", simpleCropData);

      // Vérifier si les ratios sont valides
      if (!simpleCropData.ratioX && simpleCropData.ratioX !== 0) {
        console.error("Ratios de recadrage invalides");
        alert("Erreur: Données de recadrage invalides");
        return;
      }

      // Créer un canvas pour le recadrage
      const canvas = document.createElement("canvas");
      const ctx = canvas.getContext("2d");

      // Calculer le ratio entre l'image affichée et l'image originale
      const displayedImg = img;
      const originalImg = new Image();

      originalImg.onload = function () {
        const scaleX = originalImg.width / displayedImg.offsetWidth;
        const scaleY = originalImg.height / displayedImg.offsetHeight;

        // Utiliser les ratios du système simple pour calculer les coordonnées sur l'image originale
        let cropX = Math.max(0, simpleCropData.ratioX * originalImg.width);
        let cropY = Math.max(0, simpleCropData.ratioY * originalImg.height);
        let cropWidth = simpleCropData.ratioWidth * originalImg.width;
        let cropHeight = simpleCropData.ratioHeight * originalImg.height;

        console.log(
          "Recadrage - Dimensions originales:",
          originalImg.width,
          "x",
          originalImg.height
        );
        console.log(
          "Recadrage - Ratios:",
          simpleCropData.ratioX,
          simpleCropData.ratioY,
          simpleCropData.ratioWidth,
          simpleCropData.ratioHeight
        );
        console.log(
          "Recadrage - Coordonnées calculées:",
          cropX,
          cropY,
          cropWidth,
          cropHeight
        );

        // S'assurer que le recadrage ne dépasse pas l'image originale
        // Si ça dépasse, on étire la partie disponible pour remplir le format demandé
        if (cropX < 0) {
          cropWidth += cropX;
          cropX = 0;
        }
        if (cropY < 0) {
          cropHeight += cropY;
          cropY = 0;
        }
        if (cropX + cropWidth > originalImg.width) {
          cropWidth = originalImg.width - cropX;
        }
        if (cropY + cropHeight > originalImg.height) {
          cropHeight = originalImg.height - cropY;
        }

        // Créer deux canvas : un pour l'image finale haute résolution et un pour la vignette
        const finalCanvas = document.createElement("canvas");
        const finalCtx = finalCanvas.getContext("2d");

        // Canvas haute résolution pour l'image finale (300 DPI)
        const dpi = 300;
        const pixelsPerCm = dpi / 2.54;
        finalCanvas.width = simpleCropData.realWidthCm * pixelsPerCm;
        finalCanvas.height = simpleCropData.realHeightCm * pixelsPerCm;

        // Canvas pour la vignette (taille raisonnable)
        const thumbnailMaxSize = 200;
        const ratio = simpleCropData.realWidthCm / simpleCropData.realHeightCm;
        let thumbWidth, thumbHeight;

        if (ratio > 1) {
          // Paysage
          thumbWidth = thumbnailMaxSize;
          thumbHeight = thumbnailMaxSize / ratio;
        } else {
          // Portrait ou carré
          thumbHeight = thumbnailMaxSize;
          thumbWidth = thumbnailMaxSize * ratio;
        }

        canvas.width = thumbWidth;
        canvas.height = thumbHeight;

        // Dessiner sur le canvas haute résolution (pour le fichier final)
        finalCtx.drawImage(
          originalImg,
          cropX,
          cropY,
          cropWidth,
          cropHeight,
          0,
          0,
          finalCanvas.width,
          finalCanvas.height
        );

        // Dessiner sur le canvas vignette (pour l'affichage)
        ctx.drawImage(
          originalImg,
          cropX,
          cropY,
          cropWidth,
          cropHeight,
          0,
          0,
          canvas.width,
          canvas.height
        );

        console.log(
          "Canvas haute résolution créé:",
          finalCanvas.width,
          "x",
          finalCanvas.height
        );
        console.log("Canvas vignette créé:", canvas.width, "x", canvas.height);

        // Générer la vignette pour l'affichage
        const thumbnailDataUrl = canvas.toDataURL();

        // Convertir le canvas haute résolution en blob pour le fichier
        finalCanvas.toBlob(
          function (finalBlob) {
            console.log(
              "Blob haute résolution créé, taille:",
              finalBlob.size,
              "bytes"
            );

            // Fichier haute résolution pour l'envoi
            const croppedFile = new File(
              [finalBlob],
              uploadedImages[currentCropIndex].file.name,
              {
                type: uploadedImages[currentCropIndex].file.type,
              }
            );

            uploadedImages[currentCropIndex].file = croppedFile;
            uploadedImages[currentCropIndex].dataUrl = thumbnailDataUrl;

            console.log("Image recadrée mise à jour, index:", currentCropIndex);
            console.log("Vignette générée:", thumbWidth, "x", thumbHeight);

            // Forcer la mise à jour complète de la vignette
            setTimeout(() => {
              refreshPreview();
              console.log("Vignettes rechargées après recadrage");
            }, 100);
            closeCropModal();
          },
          uploadedImages[currentCropIndex].file.type,
          0.9
        );
      };

      originalImg.src = uploadedImages[currentCropIndex].originalDataUrl;
    }
  });

  // Soumission du formulaire avec les images
  const devisForm = document.querySelector(".devis-form");
  if (devisForm) {
    devisForm.addEventListener("submit", function (e) {
      // Ajouter les images au formulaire
      const formData = new FormData(devisForm);

      // Supprimer les anciennes images s'il y en a
      formData.delete("images[]");

      // Ajouter toutes les images
      uploadedImages.forEach((imageData, index) => {
        formData.append("images[]", imageData.file);
      });

      // Envoyer via AJAX ou laisser le comportement par défaut
      console.log("Formulaire soumis avec", uploadedImages.length, "images");
    });
  }
});

// Fonction utilitaire pour formater la taille des fichiers
function formatFileSize(bytes) {
  if (bytes === 0) return "0 Bytes";
  const k = 1024;
  const sizes = ["Bytes", "KB", "MB", "GB"];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
}
