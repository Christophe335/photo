<?php
/**
 * Menu flottant permanent qui génère automatiquement les liens
 * basé sur les ID des sections présentes dans la page
 */
?>

<div id="menu-flottant" class="menu-flottant">
    <div class="menu-toggle" onclick="toggleMenu()">
        <span>☰</span>
    </div>
    <nav class="menu-nav" id="menu-nav">
        <div class="menu-header">
            <h4>Navigation</h4>
            <button class="menu-close" onclick="toggleMenu()">×</button>
        </div>
        <ul class="menu-links" id="menu-links">
            <!-- Les liens seront générés automatiquement par JavaScript -->
        </ul>
    </nav>
</div>

<!-- Bouton retour en haut -->
<button id="btn-retour-haut" class="btn-retour-haut" onclick="scrollToTop()" title="Retour en haut">
    <span><img src="../images/logo-icon/arrow-up.png" alt="logo flèche vers le haut" width="25" height="25"></span>
</button>

<style>
.menu-flottant {
    position: fixed;
    top: 50%;
    left: 20px;
    transform: translateY(-50%);
    z-index: 1000;
    font-family: 'Roboto', sans-serif;
}

.menu-toggle {
    width: 50px;
    height: 50px;
    background: var(--or2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
}

.menu-toggle:hover {
    background: var(--or1);
    transform: scale(1.1);
}

.menu-toggle span {
    color: white;
    font-size: 18px;
    font-weight: bold;
}

.menu-nav {
    position: absolute;
    left: 70px;
    top: 50%;
    transform: translateY(-50%);
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    min-width: 270px;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    border: 1px solid #e1e8ed;
}

.menu-nav.active {
    opacity: 1;
    visibility: visible;
}

.menu-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    border-bottom: 1px solid #e1e8ed;
    background: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.menu-header h4 {
    margin: 0;
    color: #2c3e50;
    font-size: 16px;
    font-weight: 600;
}

.menu-close {
    background: none;
    border: none;
    font-size: 20px;
    color: #666;
    cursor: pointer;
    padding: 0;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.menu-close:hover {
    background: #e1e8ed;
    color: #2c3e50;
}

.menu-links {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 400px;
    overflow-y: auto;
}

.menu-links li {
    border-bottom: 1px solid #f1f3f4;
}

.menu-links li:last-child {
    border-bottom: none;
}

.menu-links a {
    display: block;
    padding: 12px 20px;
    text-decoration: none;
    color: #1a1b4d;
    font-size: 14px;
    font-weight: 400;
    transition: all 0.2s ease;
    position: relative;
}

.menu-links a:hover {
    background: var(--or3);
    color: var(--noir2);
    padding-left: 25px;
}

.menu-links a.active {
    background: var(--or3);
    color: white;
    font-weight: 500;
}

.menu-links a.active:hover {
    background: var(--or2);
    color: white;
}

/* Animation smooth scroll */
html {
    scroll-behavior: smooth;
}

/* Bouton retour en haut */
.btn-retour-haut {
    position: fixed;
    bottom: 30px;
    right: 30px;
    width: 40px;
    height: 40px;
    background: var(--or2);
    border: none;
    border-radius: 50%;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
    transition: all 0.3s ease;
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px);
}

.btn-retour-haut.visible {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.btn-retour-haut:hover {
    background: var(--or1);
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.6);
}

.btn-retour-haut span {
    color: white;
    font-size: 20px;
    font-weight: bold;
}

/* Responsive */
@media (max-width: 768px) {
    .menu-flottant {
        left: 10px;
    }
    
    .menu-nav {
        left: 60px;
        min-width: 180px;
    }
    
    .btn-retour-haut {
        bottom: 20px;
        right: 20px;
        width: 45px;
        height: 45px;
    }
    
    .btn-retour-haut span {
        font-size: 18px;
    }
}
</style>

<script>
// Variables globales
let menuOpen = false;
let currentSection = '';
let btnRetourHaut;

// Toggle du menu
function toggleMenu() {
    const menuNav = document.getElementById('menu-nav');
    menuOpen = !menuOpen;
    
    if (menuOpen) {
        menuNav.classList.add('active');
    } else {
        menuNav.classList.remove('active');
    }
}

// Génération automatique du menu basé sur les ID des sections
function generateMenu() {
    const menuLinks = document.getElementById('menu-links');
    const menuFlottant = document.getElementById('menu-flottant');
    if (!menuLinks || !menuFlottant) return;
    
    // Trouver uniquement les balises <section> avec un ID
    const sections = document.querySelectorAll('section[id]');
    menuLinks.innerHTML = '';
    
    // Compter les sections valides
    const validSections = Array.from(sections).filter(section => {
        const id = section.id;
        return id && id.trim() !== '';
    });
    
    // Cacher le menu s'il n'y a qu'une seule section ou moins
    if (validSections.length <= 1) {
        menuFlottant.style.display = 'none';
        return;
    }
    
    // Afficher le menu s'il y a plus d'une section
    menuFlottant.style.display = 'block';
    
    validSections.forEach(section => {
        const id = section.id;
        const li = document.createElement('li');
        const a = document.createElement('a');
        
        // Le nom du lien est l'ID avec la première lettre en majuscule
        a.textContent = id.charAt(0).toUpperCase() + id.slice(1);
        a.href = `#${id}`;
        a.onclick = function(e) {
            e.preventDefault();
            scrollToSection(id);
            toggleMenu(); // Fermer le menu après clic
        };
        
        li.appendChild(a);
        menuLinks.appendChild(li);
    });
}

// Scroll vers une section
function scrollToSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        const headerHeight = 100; // Hauteur approximative du header
        const sectionTop = section.offsetTop - headerHeight;
        
        window.scrollTo({
            top: sectionTop,
            behavior: 'smooth'
        });
        
        // Marquer comme section active
        updateActiveSection(sectionId);
    }
}

// Mettre à jour la section active
function updateActiveSection(sectionId) {
    currentSection = sectionId;
    
    // Retirer la classe active de tous les liens
    document.querySelectorAll('.menu-links a').forEach(link => {
        link.classList.remove('active');
    });
    
    // Ajouter la classe active au lien correspondant
    const activeLink = document.querySelector(`.menu-links a[href="#${sectionId}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}

// Détecter la section visible lors du scroll
function detectCurrentSection() {
    const sections = document.querySelectorAll('section[id]');
    const scrollPosition = window.scrollY + 150; // Offset pour le header
    
    let currentSectionId = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionHeight = section.offsetHeight;
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
            currentSectionId = section.id;
        }
    });
    
    if (currentSectionId && currentSectionId !== currentSection) {
        updateActiveSection(currentSectionId);
    }
}

// Fermer le menu en cliquant à l'extérieur
function handleClickOutside(event) {
    const menuFlottant = document.getElementById('menu-flottant');
    if (menuOpen && menuFlottant && !menuFlottant.contains(event.target)) {
        toggleMenu();
    }
}

// Fonction pour remonter en haut de page
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Gestion de l'affichage du bouton retour en haut
function handleScrollButton() {
    const scrollPosition = window.scrollY;
    
    if (scrollPosition > 300) {
        btnRetourHaut.classList.add('visible');
    } else {
        btnRetourHaut.classList.remove('visible');
    }
}

// Fonction combinée pour gérer le scroll
function handleScroll() {
    detectCurrentSection();
    handleScrollButton();
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    generateMenu();
    
    // Initialiser le bouton retour en haut
    btnRetourHaut = document.getElementById('btn-retour-haut');
    
    // Écouter le scroll pour détecter la section active et gérer le bouton
    window.addEventListener('scroll', handleScroll);
    
    // Fermer le menu en cliquant à l'extérieur
    document.addEventListener('click', handleClickOutside);
    
    // Détecter la section initiale et l'état du bouton
    setTimeout(() => {
        detectCurrentSection();
        handleScrollButton();
    }, 100);
});
</script>