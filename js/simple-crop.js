// Système de recadrage simple style Facebook
let currentFormatCm = { width: 10, height: 10 };
let cropBox = null;
let isDragging = false;
let dragOffset = { x: 0, y: 0 };

// Données de recadrage pour l'export
let cropData = {
  x: 0,
  y: 0,
  width: 0,
  height: 0,
  realWidthCm: 10,
  realHeightCm: 10
};

// Fonction pour créer une grille de recadrage simple
function createSimpleCropGrid(widthCm, heightCm) { 
  const wrapper = document.querySelector('.crop-wrapper');
  const img = wrapper.querySelector('img');
  if (!wrapper || !img) return;
  
  console.log(`Création grille ${widthCm}×${heightCm} cm`);
  
  // Supprimer l'ancienne grille
  if (cropBox) cropBox.remove();
  
  // Stocker le format
  currentFormatCm = { width: widthCm, height: heightCm };
  
  // Calculer la taille de base (1cm = 40px pour 10×10)
  const basePixelsPerCm = 40;
  let gridWidth = widthCm * basePixelsPerCm;
  let gridHeight = heightCm * basePixelsPerCm;
  
  // Adapter la taille pour les grands formats
  const maxWidth = window.innerWidth * 0.6; // 60% de la largeur d'écran
  const maxHeight = window.innerHeight * 0.5; // 50% de la hauteur d'écran
  
  if (gridWidth > maxWidth || gridHeight > maxHeight) {
    const scaleX = maxWidth / gridWidth;
    const scaleY = maxHeight / gridHeight;
    const scale = Math.min(scaleX, scaleY) * 0.9; // 90% pour laisser de la marge
    
    gridWidth *= scale;
    gridHeight *= scale;
    
    console.log(`Grille réduite avec échelle: ${scale.toFixed(2)}`);
  }
  
  // Calculer la position centrée
  const wrapperWidth = wrapper.offsetWidth;
  const wrapperHeight = wrapper.offsetHeight;
  const centerX = (wrapperWidth - gridWidth) / 2;
  const centerY = (wrapperHeight - gridHeight) / 2;
  
  // Créer la grille avec overlay limité à l'image
  cropBox = document.createElement('div');
  cropBox.style.cssText = `
    position: absolute;
    width: ${gridWidth}px;
    height: ${gridHeight}px;
    border: 3px solid #24256d;
    background: transparent;
    cursor: move;
    z-index: 10;
    left: ${centerX}px;
    top: ${centerY}px;
    box-shadow: 
      inset 0 0 0 2px rgba(255,255,255,0.8),
      0 0 0 500px rgba(0, 0, 0, 0.6);
  `;
  
  // Ajouter un label avec le format
  const label = document.createElement('div');
  label.style.cssText = `
    position: absolute;
    top: -30px;
    left: 0;
    background: #24256d;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: bold;
    white-space: nowrap;
  `;
  label.textContent = `${widthCm} × ${heightCm} cm`;
  cropBox.appendChild(label);
  
  // Ajouter le drag
  cropBox.addEventListener('mousedown', startDrag);
  
  wrapper.appendChild(cropBox);
  
  // Enregistrer les données de recadrage initiales
  updateCropData();
  
  // Mettre à jour l'affichage des dimensions
  updateDimensionsDisplay();
  
  console.log(`Grille créée: ${gridWidth}×${gridHeight}px`);
}

function startDrag(e) {
  e.preventDefault();
  console.log('Début drag');
  isDragging = true;
  
  const rect = cropBox.getBoundingClientRect();
  const wrapperRect = document.querySelector('.crop-wrapper').getBoundingClientRect();
  
  dragOffset.x = e.clientX - rect.left;
  dragOffset.y = e.clientY - rect.top;
  
  document.addEventListener('mousemove', doDrag);
  document.addEventListener('mouseup', stopDrag);
}

function doDrag(e) {
  if (!isDragging) return;
  
  const wrapperRect = document.querySelector('.crop-wrapper').getBoundingClientRect();
  
  const newX = e.clientX - wrapperRect.left - dragOffset.x;
  const newY = e.clientY - wrapperRect.top - dragOffset.y;
  
  cropBox.style.left = newX + 'px';
  cropBox.style.top = newY + 'px';
  
  // Mettre à jour les données de recadrage
  updateCropData();
  
  // Mettre à jour l'aperçu si il est affiché
  updatePreviewIfVisible();
  
  console.log(`Position: ${newX}, ${newY}`);
}

function stopDrag() {
  console.log('Fin drag');
  isDragging = false;
  document.removeEventListener('mousemove', doDrag);
  document.removeEventListener('mouseup', stopDrag);
}

function updateCropData() {
  if (!cropBox) return;
  
  const wrapper = document.querySelector('.crop-wrapper');
  const img = wrapper.querySelector('img');
  if (!wrapper || !img) return;
  
  const wrapperRect = wrapper.getBoundingClientRect();
  const imgRect = img.getBoundingClientRect();
  const cropRect = cropBox.getBoundingClientRect();
  
  // Calculer les coordonnées relatives à l'image
  const relativeX = cropRect.left - imgRect.left;
  const relativeY = cropRect.top - imgRect.top;
  
  // Calculer les ratios par rapport à l'image affichée
  const ratioX = relativeX / imgRect.width;
  const ratioY = relativeY / imgRect.height;
  const ratioWidth = cropRect.width / imgRect.width;
  const ratioHeight = cropRect.height / imgRect.height;
  
  cropData = {
    x: relativeX,
    y: relativeY,
    width: cropRect.width,
    height: cropRect.height,
    ratioX: ratioX,
    ratioY: ratioY,
    ratioWidth: ratioWidth,
    ratioHeight: ratioHeight,
    realWidthCm: currentFormatCm.width,
    realHeightCm: currentFormatCm.height,
    imageDisplayWidth: imgRect.width,
    imageDisplayHeight: imgRect.height
  };
  
  console.log('Données de recadrage mises à jour:', cropData);
}

function updateDimensionsDisplay() {
  const display = document.querySelector('.crop-dimensions');
  if (display) {
    display.textContent = `${currentFormatCm.width} × ${currentFormatCm.height} cm`;
  }
}

// Fonction pour mettre à jour l'aperçu s'il est visible
function updatePreviewIfVisible() {
  const previewSidebar = document.querySelector('.crop-preview-sidebar');
  if (previewSidebar && previewSidebar.style.display !== 'none') {
    // Délai pour éviter trop de calculs pendant le drag
    clearTimeout(window.previewTimeout);
    window.previewTimeout = setTimeout(async () => {
      const previewDataUrl = await generateCropPreview();
      if (previewDataUrl) {
        const previewImage = document.getElementById('cropPreviewImage');
        if (previewImage) {
          previewImage.src = previewDataUrl;
        }
      }
    }, 100);
  }
}

// Fonction pour générer un aperçu du recadrage
function generateCropPreview() {
  if (!cropBox) return null;
  
  const wrapper = document.querySelector('.crop-wrapper');
  const img = wrapper.querySelector('img');
  if (!wrapper || !img) return null;
  
  // Créer un canvas pour l'aperçu
  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');
  
  // Dimensions finales en pixels (300 DPI)
  const dpi = 300;
  const pixelsPerCm = dpi / 2.54;
  canvas.width = currentFormatCm.width * pixelsPerCm;
  canvas.height = currentFormatCm.height * pixelsPerCm;
  
  // Créer une image temporaire avec les vraies dimensions
  const tempImg = new Image();
  tempImg.crossOrigin = 'anonymous';
  
  return new Promise((resolve) => {
    tempImg.onload = function() {
      const imgRect = img.getBoundingClientRect();
      const cropRect = cropBox.getBoundingClientRect();
      
      // Calculer les ratios
      const scaleX = tempImg.width / imgRect.width;
      const scaleY = tempImg.height / imgRect.height;
      
      // Coordonnées sur l'image originale
      const cropX = (cropRect.left - imgRect.left) * scaleX;
      const cropY = (cropRect.top - imgRect.top) * scaleY;
      const cropWidth = cropRect.width * scaleX;
      const cropHeight = cropRect.height * scaleY;
      
      // Dessiner sur le canvas
      ctx.drawImage(
        tempImg,
        Math.max(0, cropX), Math.max(0, cropY), 
        Math.min(cropWidth, tempImg.width - Math.max(0, cropX)), 
        Math.min(cropHeight, tempImg.height - Math.max(0, cropY)),
        0, 0, canvas.width, canvas.height
      );
      
      resolve(canvas.toDataURL('image/jpeg', 0.9));
    };
    
    tempImg.src = img.src;
  });
}

// Exposer les fonctions globalement
window.createSimpleCropGrid = createSimpleCropGrid;
window.getSimpleCropData = function() {
  return cropData;
};
window.generateCropPreview = generateCropPreview;