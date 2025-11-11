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

  let uploadedImages = [];
  let currentCropIndex = -1;
  let cropper = null;
  const maxImages = 30;

  // Variable pour stocker le dernier format demandé (pour l'upscale forcé)
  let lastRequestedFormat = { width: 0, height: 0 };

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
    const imageItem = document.createElement("div");
    imageItem.className = "image-item";
    imageItem.innerHTML = `
            <img src="${imageData.dataUrl}" alt="Image ${index + 1}">
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

    // Initialiser le système de recadrage personnalisé
    setTimeout(() => {
      initCustomCropper();
    }, 100);
  };

  // Système de recadrage personnalisé
  let cropData = { x: 0, y: 0, width: 200, height: 200 };
  let isDragging = false;
  let isResizing = false;
  let resizeHandle = "";
  let startX = 0;
  let startY = 0;

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

    // Zone de recadrage
    const cropBox = document.createElement("div");
    cropBox.className = "crop-box";
    cropBox.style.cssText = `
      position: absolute;
      border: 2px dashed #24256d;
      background: rgba(36, 37, 109, 0.1);
      cursor: move;
      box-sizing: border-box;
    `;

    // Poignées de redimensionnement
    const handles = ["nw", "ne", "sw", "se", "n", "s", "e", "w"];
    handles.forEach((handle) => {
      const handleEl = document.createElement("div");
      handleEl.className = `crop-handle crop-handle-${handle}`;
      handleEl.style.cssText = `
        position: absolute;
        width: 10px;
        height: 10px;
        background: #24256d;
        border: 2px solid white;
        border-radius: 50%;
        cursor: ${
          handle.includes("n") || handle.includes("s") ? "ns" : "ew"
        }-resize;
      `;

      // Position des poignées
      if (handle.includes("n")) handleEl.style.top = "-5px";
      if (handle.includes("s")) handleEl.style.bottom = "-5px";
      if (handle.includes("w")) handleEl.style.left = "-5px";
      if (handle.includes("e")) handleEl.style.right = "-5px";
      if (handle === "n" || handle === "s") handleEl.style.left = "50%";
      if (handle === "e" || handle === "w") handleEl.style.top = "50%";
      if (handle === "n" || handle === "s")
        handleEl.style.transform = "translateX(-50%)";
      if (handle === "e" || handle === "w")
        handleEl.style.transform = "translateY(-50%)";

      cropBox.appendChild(handleEl);
    });

    // Affichage des dimensions
    const dimensionsDisplay = document.createElement("div");
    dimensionsDisplay.className = "crop-dimensions";
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

    wrapper.appendChild(img);
    wrapper.appendChild(cropBox);

    // Container principal pour les contrôles
    const controlsContainer = document.createElement("div");
    controlsContainer.className = "crop-controls-container";
    controlsContainer.style.cssText = `
      margin-top: 15px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 20px;
    `;

    // Section gauche : Zoom + Confirmer
    const leftControls = document.createElement("div");
    leftControls.className = "left-controls";
    leftControls.style.cssText = `
      display: flex;
      align-items: center;
      gap: 15px;
    `;

    // Contrôles de zoom
    const zoomControls = document.createElement("div");
    zoomControls.className = "zoom-controls";
    zoomControls.style.cssText = `
      display: flex;
      align-items: center;
      gap: 10px;
    `;

    const zoomOutBtn = document.createElement("button");
    zoomOutBtn.textContent = "−";
    zoomOutBtn.className = "zoom-btn";
    zoomOutBtn.type = "button";

    const zoomDisplay = document.createElement("span");
    zoomDisplay.className = "zoom-display";
    zoomDisplay.textContent = "100%";

    const zoomInBtn = document.createElement("button");
    zoomInBtn.textContent = "+";
    zoomInBtn.className = "zoom-btn";
    zoomInBtn.type = "button";

    const resetZoomBtn = document.createElement("button");
    resetZoomBtn.textContent = "Reset";
    resetZoomBtn.className = "zoom-reset-btn";
    resetZoomBtn.type = "button";

    zoomControls.appendChild(zoomOutBtn);
    zoomControls.appendChild(zoomDisplay);
    zoomControls.appendChild(zoomInBtn);
    zoomControls.appendChild(resetZoomBtn);

    leftControls.appendChild(zoomControls);

    // Déplacer le bouton Confirmer dans les contrôles gauche, à droite des boutons de zoom
    if (btnCropConfirm) {
      btnCropConfirm.style.marginLeft = '8px';
      btnCropConfirm.style.padding = '8px 12px';
      btnCropConfirm.style.borderRadius = '6px';
      btnCropConfirm.style.background = '#24256d';
      btnCropConfirm.style.color = 'white';
      btnCropConfirm.style.border = 'none';
      leftControls.appendChild(btnCropConfirm);
    }

    // Section droite : Formats prédéfinis
    const rightControls = document.createElement("div");
    rightControls.className = "format-controls";
    rightControls.style.cssText = `
      display: flex;
      flex-direction: column;
      gap: 10px;
      max-width: 300px;
    `;

    const formatsTitle = document.createElement("div");
    formatsTitle.textContent = "Formats prédéfinis :";
    formatsTitle.style.cssText = `
      font-weight: bold;
      font-size: 14px;
      color: #24256d;
      margin-bottom: 5px;
    `;
    rightControls.appendChild(formatsTitle);

    // Créer les boutons de formats
    const formats = [
      { name: "A5", width: 14.8, height: 21.0 },
      { name: "A4", width: 21.0, height: 29.7 },
      { name: "A3", width: 29.7, height: 42.0 },
      { name: "10×10", width: 10.0, height: 10.0 },
      { name: "10×15", width: 10.0, height: 15.0 },
      { name: "15×15", width: 15.0, height: 15.0 },
      { name: "15×20", width: 15.0, height: 20.0 },
      { name: "15×29.7", width: 15.0, height: 29.7 },
      { name: "20×20", width: 20.0, height: 20.0 },
      { name: "21×21", width: 21.0, height: 21.0 },
      { name: "20×30", width: 20.0, height: 30.0 },
      { name: "28×36", width: 28.0, height: 36.0 },
    ];

    const formatGrid = document.createElement("div");
    formatGrid.style.cssText = `
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 5px;
    `;

    formats.forEach((format) => {
      const btn = document.createElement("button");
      btn.textContent = `${format.name} cm`;
      btn.className = "format-btn";
      btn.type = "button";
      btn.style.cssText = `
        padding: 5px 8px;
        border: 1px solid #24256d;
        background: white;
        color: #24256d;
        border-radius: 5px;
        font-size: 11px;
        cursor: pointer;
        transition: all 0.2s ease;
      `;
      btn.addEventListener("click", () =>
        setCropFormat(format.width, format.height)
      );
      formatGrid.appendChild(btn);
    });

    rightControls.appendChild(formatGrid);

    // Bouton portrait/paysage
    const orientationBtn = document.createElement("button");
    orientationBtn.textContent = "🔄 Portrait/Paysage";
    orientationBtn.className = "orientation-btn";
    orientationBtn.type = "button";
    orientationBtn.style.cssText = `
      padding: 8px 12px;
      border: 1px solid #24256d;
      background: #24256d;
      color: white;
      border-radius: 5px;
      font-size: 12px;
      cursor: pointer;
      margin-top: 5px;
    `;
    orientationBtn.addEventListener("click", toggleOrientation);
    rightControls.appendChild(orientationBtn);

    // Bouton forcer l'upscale
    const forceUpscaleBtn = document.createElement("button");
    forceUpscaleBtn.textContent = "⚡ Forcer le format exact";
    forceUpscaleBtn.className = "force-upscale-btn";
    forceUpscaleBtn.type = "button";
    forceUpscaleBtn.style.cssText = `
      padding: 8px 12px;
      border: 1px solid #e67e22;
      background: #e67e22;
      color: white;
      border-radius: 5px;
      font-size: 11px;
      cursor: pointer;
      margin-top: 5px;
      display: none;
    `;
    forceUpscaleBtn.addEventListener("click", forceExactFormat);
    rightControls.appendChild(forceUpscaleBtn);

    controlsContainer.appendChild(leftControls);
    controlsContainer.appendChild(rightControls);

    // Notice pour indiquer que le format souhaité dépasse la taille possible
    const formatNotice = document.createElement('div');
    formatNotice.className = 'format-notice';
    formatNotice.style.cssText = `
      margin-top: 8px;
      text-align: center;
      color: #e67e22;
      font-size: 13px;
      display: none;
    `;
    container.appendChild(formatNotice);

    container.appendChild(wrapper);
    container.appendChild(controlsContainer);

    // Attendre que l'image soit chargée pour initialiser la position
    img.onload = function () {
      // Sauvegarder les dimensions originales
      originalImageWidth = img.offsetWidth;
      originalImageHeight = img.offsetHeight;
      currentZoom = 1;

      // Position initiale au centre de l'image (40% de la taille)
      resetCropPosition();
      updateCropBox();
    };

    // Events
    setupCropEvents(cropBox, wrapper);
    setupZoomEvents(
      wrapper,
      img,
      zoomInBtn,
      zoomOutBtn,
      resetZoomBtn,
      zoomDisplay
    );
  }

  function updateCropBox() {
    const cropBox = document.querySelector(".crop-box");
    const dimensionsDisplay = document.querySelector(".crop-dimensions");

    if (cropBox && dimensionsDisplay) {
      cropBox.style.left = cropData.x + "px";
      cropBox.style.top = cropData.y + "px";
      cropBox.style.width = cropData.width + "px";
      cropBox.style.height = cropData.height + "px";

      // Calculer les dimensions finales en tenant compte du ratio d'affichage
      const img = document.querySelector(".crop-wrapper img");
      if (img && uploadedImages[currentCropIndex]) {
        // Créer une image temporaire pour obtenir les dimensions originales
        const tempImg = new Image();
        tempImg.onload = function () {
          const scaleX = tempImg.width / img.offsetWidth;
          const scaleY = tempImg.height / img.offsetHeight;

          // Dimensions réelles du recadrage sur l'image originale
          const realWidth = cropData.width * scaleX;
          const realHeight = cropData.height * scaleY;

          // Conversion en cm pour impression (300 DPI standard)
          const DPI = 300;
          const mmPerInch = 25.4;
          const widthCm = ((realWidth * mmPerInch) / DPI / 10).toFixed(1);
          const heightCm = ((realHeight * mmPerInch) / DPI / 10).toFixed(1);

          dimensionsDisplay.textContent = `${widthCm} × ${heightCm} cm`;
        };
        tempImg.src = uploadedImages[currentCropIndex].originalDataUrl;
      } else {
        // Fallback si pas d'image chargée
        const DPI = 300;
        const mmPerInch = 25.4;
        const widthCm = ((cropData.width * mmPerInch) / DPI / 10).toFixed(1);
        const heightCm = ((cropData.height * mmPerInch) / DPI / 10).toFixed(1);
        dimensionsDisplay.textContent = `${widthCm} × ${heightCm} cm`;
      }
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

      const deltaX = e.clientX - startX;
      const deltaY = e.clientY - startY;
      const img = wrapper.querySelector("img");
      const imgWidth = img.offsetWidth;
      const imgHeight = img.offsetHeight;

      if (isDragging) {
        cropData.x = Math.max(
          0,
          Math.min(imgWidth - cropData.width, cropData.x + deltaX)
        );
        cropData.y = Math.max(
          0,
          Math.min(imgHeight - cropData.height, cropData.y + deltaY)
        );
      } else if (isResizing) {
        handleResize(deltaX, deltaY, imgWidth, imgHeight);
      }

      updateCropBox();
      startX = e.clientX;
      startY = e.clientY;
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

  function setCropFormat(widthCm, heightCm) {
    const img = document.querySelector(".crop-wrapper img");
    if (!img || !uploadedImages[currentCropIndex]) return;
    
    // Stocker le format demandé
    lastRequestedFormat = { width: widthCm, height: heightCm };
    
    // Calculer les dimensions correctes
    const tempImg = new Image();
    tempImg.onload = function () {
      const scaleX = tempImg.width / img.offsetWidth;
      const scaleY = tempImg.height / img.offsetHeight;
      
      // Conversion cm vers pixels à 300 DPI
      const DPI = 300;
      const mmPerInch = 25.4;
      const targetWidthPx = (widthCm * 10 * DPI) / mmPerInch;  // pixels réels nécessaires
      const targetHeightPx = (heightCm * 10 * DPI) / mmPerInch; // pixels réels nécessaires
      
      // Convertir en pixels d'affichage
      const displayWidth = targetWidthPx / scaleX;
      const displayHeight = targetHeightPx / scaleY;
      
      // Appliquer les dimensions
      cropData.width = displayWidth;
      cropData.height = displayHeight;
      
      // Centrer
      cropData.x = (img.offsetWidth - cropData.width) / 2;
      cropData.y = (img.offsetHeight - cropData.height) / 2;
      
      // Vérifier si le format dépasse l'image
      const displayW = img.offsetWidth;
      const displayH = img.offsetHeight;
      
      if (cropData.width > displayW || cropData.height > displayH || 
          cropData.x < 0 || cropData.y < 0) {
        
        const noticeEl = document.querySelector('.format-notice');
        const forceBtn = document.querySelector('.force-upscale-btn');
        
        if (noticeEl && forceBtn) {
          noticeEl.innerHTML = `Format ${widthCm} × ${heightCm} cm trop grand pour cette image<br><small>Cliquez sur "Forcer format exact" pour l'appliquer quand même</small>`;
          noticeEl.style.display = 'block';
          forceBtn.style.display = 'block';
          
          setTimeout(() => { 
            noticeEl.style.display = 'none'; 
            forceBtn.style.display = 'none';
          }, 8000);
        }
        
        // Limiter pour l'affichage
        cropData.width = Math.min(cropData.width, displayW);
        cropData.height = Math.min(cropData.height, displayH);
        cropData.x = Math.max(0, Math.min(displayW - cropData.width, cropData.x));
        cropData.y = Math.max(0, Math.min(displayH - cropData.height, cropData.y));
      } else {
        const noticeEl = document.querySelector('.format-notice');
        const forceBtn = document.querySelector('.force-upscale-btn');
        
        if (noticeEl && forceBtn) {
          noticeEl.style.display = 'none';
          forceBtn.style.display = 'none';
        }
      }
      
      updateCropBox();
    };
    
    tempImg.src = uploadedImages[currentCropIndex].originalDataUrl;
  }

  function forceExactFormat() {
    const img = document.querySelector(".crop-wrapper img");
    if (!img || !lastRequestedFormat || !uploadedImages[currentCropIndex]) return;

    const widthCm = lastRequestedFormat.width;
    const heightCm = lastRequestedFormat.height;
    
    // Utiliser la même logique que setCropFormat
    const tempImg = new Image();
    tempImg.onload = function () {
      const scaleX = tempImg.width / img.offsetWidth;
      const scaleY = tempImg.height / img.offsetHeight;
      
      // Conversion cm vers pixels à 300 DPI
      const DPI = 300;
      const mmPerInch = 25.4;
      const targetWidthPx = (widthCm * 10 * DPI) / mmPerInch;
      const targetHeightPx = (heightCm * 10 * DPI) / mmPerInch;
      
      // Convertir en pixels d'affichage
      cropData.width = targetWidthPx / scaleX;
      cropData.height = targetHeightPx / scaleY;
      
      // Centrer sans contrainte
      cropData.x = (img.offsetWidth - cropData.width) / 2;
      cropData.y = (img.offsetHeight - cropData.height) / 2;
      
      updateCropBox();
      
      // Afficher confirmation
      const noticeEl = document.querySelector('.format-notice');
      const forceBtn = document.querySelector('.force-upscale-btn');
      
      if (noticeEl && forceBtn) {
        noticeEl.innerHTML = `Format exact forcé : ${widthCm} × ${heightCm} cm<br><small>⚠️ Le recadrage peut dépasser l'image</small>`;
        noticeEl.style.display = 'block';
        forceBtn.style.display = 'none';
        
        setTimeout(() => { 
          noticeEl.style.display = 'none'; 
        }, 5000);
      }
    };
    
    tempImg.src = uploadedImages[currentCropIndex].originalDataUrl;
  }
    
    if (uploadedImages[currentCropIndex]) {
      tempImg.src = uploadedImages[currentCropIndex].originalDataUrl;
    }

    // Calculer les pixels requis pour le format exact
    const targetWidthPx = (widthCm * 10 * DPI) / mmPerInch;
    const targetHeightPx = (heightCm * 10 * DPI) / mmPerInch;

    // Calculer les dimensions sur l'image affichée
    const tempImg = new Image();
    tempImg.onload = function () {
      const origW = tempImg.width;
      const origH = tempImg.height;
      const displayW = img.offsetWidth;
      const displayH = img.offsetHeight;

      const scaleX = origW / displayW;
      const scaleY = origH / displayH;

      // Calculer les dimensions d'affichage pour le format exact
      const displayWidthNeeded = targetWidthPx / scaleX;
      const displayHeightNeeded = targetHeightPx / scaleY;

      // Appliquer même si ça dépasse l'image (upscale)
      cropData.width = Math.max(50, displayWidthNeeded);
      cropData.height = Math.max(50, displayHeightNeeded);
      
      // Centrer autant que possible
      cropData.x = Math.max(0, Math.min(displayW - cropData.width, (displayW - cropData.width) / 2));
      cropData.y = Math.max(0, Math.min(displayH - cropData.height, (displayH - cropData.height) / 2));

      updateCropBox();

      // Calculer le DPI effectif obtenu
      const effectiveWidthPx = Math.min(cropData.width * scaleX, origW);
      const effectiveHeightPx = Math.min(cropData.height * scaleY, origH);
      const effectiveDpiX = (effectiveWidthPx * mmPerInch) / (widthCm * 10);
      const effectiveDpiY = (effectiveHeightPx * mmPerInch) / (heightCm * 10);
      const effectiveDpi = Math.min(effectiveDpiX, effectiveDpiY);

      // Afficher l'avertissement qualité
      const noticeEl = document.querySelector('.format-notice');
      if (noticeEl) {
        noticeEl.innerHTML = `⚠️ Format exact appliqué : ${widthCm} × ${heightCm} cm<br><small>Qualité d'impression : ${Math.round(effectiveDpi)} DPI ${effectiveDpi < 200 ? '(Qualité réduite - Upscaling appliqué)' : '(Bonne qualité)'}</small>`;
        noticeEl.style.display = 'block';
        noticeEl.style.color = effectiveDpi < 200 ? '#e74c3c' : '#27ae60';
      }

      // Masquer le bouton forcer
      const forceBtn = document.querySelector('.force-upscale-btn');
      if (forceBtn) {
        forceBtn.style.display = 'none';
      }
    };

    tempImg.src = uploadedImages[currentCropIndex].originalDataUrl;
  }

  function toggleOrientation() {
    // Échanger largeur et hauteur
    const temp = cropData.width;
    cropData.width = cropData.height;
    cropData.height = temp;

    // Recentrer si nécessaire
    const img = document.querySelector(".crop-wrapper img");
    if (img) {
      const maxWidth = img.offsetWidth;
      const maxHeight = img.offsetHeight;

      if (cropData.width > maxWidth || cropData.height > maxHeight) {
        // Redimensionner proportionnellement
        const scale = Math.min(
          maxWidth / cropData.width,
          maxHeight / cropData.height
        );
        cropData.width *= scale;
        cropData.height *= scale;
      }

      // Recentrer
      cropData.x = (maxWidth - cropData.width) / 2;
      cropData.y = (maxHeight - cropData.height) / 2;

      updateCropBox();
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

    // Appliquer le zoom à l'image
    const newWidth = originalImageWidth * currentZoom;
    const newHeight = originalImageHeight * currentZoom;

    img.style.width = newWidth + "px";
    img.style.height = newHeight + "px";

    // Ajuster la zone de recadrage proportionnellement
    const scaleRatio = currentZoom / ((currentZoom / newZoom) * currentZoom);

    // Garder la zone de recadrage dans les limites de l'image
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

      // Créer un canvas pour le recadrage
      const canvas = document.createElement("canvas");
      const ctx = canvas.getContext("2d");

      // Calculer le ratio entre l'image affichée et l'image originale
      const displayedImg = img;
      const originalImg = new Image();

      originalImg.onload = function () {
        const scaleX = originalImg.width / displayedImg.offsetWidth;
        const scaleY = originalImg.height / displayedImg.offsetHeight;

        // Dimensions du recadrage sur l'image originale
        const cropX = cropData.x * scaleX;
        const cropY = cropData.y * scaleY;
        const cropWidth = cropData.width * scaleX;
        const cropHeight = cropData.height * scaleY;

        // Configurer le canvas
        canvas.width = cropWidth;
        canvas.height = cropHeight;

        // Dessiner la partie recadrée
        ctx.drawImage(
          originalImg,
          cropX,
          cropY,
          cropWidth,
          cropHeight,
          0,
          0,
          cropWidth,
          cropHeight
        );

        // Convertir en blob et mettre à jour
        canvas.toBlob(
          function (blob) {
            const croppedFile = new File(
              [blob],
              uploadedImages[currentCropIndex].file.name,
              {
                type: uploadedImages[currentCropIndex].file.type,
              }
            );

            uploadedImages[currentCropIndex].file = croppedFile;
            uploadedImages[currentCropIndex].dataUrl = canvas.toDataURL();

            refreshPreview();
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
