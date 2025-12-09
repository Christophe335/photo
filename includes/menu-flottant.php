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
    background: #1a1b4d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
}

.menu-toggle:hover {
    background: #24256d;
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
    background: #f8f9fa;
    color: #24256d;
    padding-left: 25px;
}

.menu-links a.active {
    background: #2a256d;
    color: white;
    font-weight: 500;
}

.menu-links a.active:hover {
    background: #3032a1;
    color: white;
}

/* Animation smooth scroll */
html {
    scroll-behavior: smooth;
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
}
</style>

<script>
// Variables globales
let menuOpen = false;
let currentSection = '';

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
    if (!menuLinks) return;
    
    // Trouver uniquement les balises <section> avec un ID
    const sections = document.querySelectorAll('section[id]');
    menuLinks.innerHTML = '';
    
    sections.forEach(section => {
        const id = section.id;
        if (id && id.trim() !== '') {
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
        }
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

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    generateMenu();
    
    // Écouter le scroll pour détecter la section active
    window.addEventListener('scroll', detectCurrentSection);
    
    // Fermer le menu en cliquant à l'extérieur
    document.addEventListener('click', handleClickOutside);
    
    // Détecter la section initiale
    setTimeout(detectCurrentSection, 100);
});
</script>