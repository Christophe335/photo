// Index complet avec tous les vrais IDs des sections
const sectionIndex = [
    // Albums
    { page: 'pages/album.php', id: 'Album Photo', title: 'Album Photo' },
    { page: 'pages/album.php', id: 'Album Photo à fenêtre', title: 'Album Photo à fenêtre' },
    
    // Livres
    { page: 'pages/livre-photo.php', id: 'Livre Photo', title: 'Livre Photo' },
    { page: 'pages/livre-photo.php', id: 'Livre photo autocollant', title: 'Livre photo autocollant' },
    { page: 'pages/livre-dela.php', id: 'Livre Photo Dela', title: 'Livre Photo Dela' },
    
    // Calendriers
    { page: 'pages/calendrier-bureau.php', id: 'Calendrier de Bureau', title: 'Calendrier de Bureau' },
    { page: 'pages/calendrier-glissant.php', id: 'Calendrier à feuilles glissantes', title: 'Calendrier à feuilles glissantes' },
    { page: 'pages/calendrier-mural.php', id: 'Calendrier Mural pour la maison', title: 'Calendrier Mural pour la maison' },
    { page: 'pages/calendrier-mural.php', id: 'Calendrier Mural Professionnel', title: 'Calendrier Mural Professionnel' },
    { page: 'pages/calendrier-mural.php', id: 'Porte-affiche', title: 'Porte-affiche' },
    
    // Boîtes
    { page: 'pages/boite-a4.php', id: 'Boîte format A4 45mm', title: 'Boîte format A4 45mm' },
    { page: 'pages/boite-a4.php', id: 'Boîte format A4 90mm', title: 'Boîte format A4 90mm' },
    { page: 'pages/boite-a4.php', id: 'Boîte format A4 à recouvrir', title: 'Boîte format A4 à recouvrir' },
    { page: 'pages/boite-a4.php', id: 'Boîte Flexibox', title: 'Boîte Flexibox' },
    { page: 'pages/boite-a4.php', id: 'Sacs Cadeaux', title: 'Sacs Cadeaux' },
    { page: 'pages/boite-a5.php', id: 'Boîte format A5', title: 'Boîte format A5' },
    { page: 'pages/boite-a5.php', id: 'Boîte format A5 à recouvrir', title: 'Boîte format A5 à recouvrir' },
    
    // Couvertures
    { page: 'pages/couverture-rigide.php', id: 'Carnet de Notes', title: 'Carnet de Notes' },
    { page: 'pages/couverture-rigide.php', id: 'Cahier de Réunion', title: 'Cahier de Réunion' },
    { page: 'pages/couverture-rigide.php', id: 'Couverture rigide 1 face', title: 'Couverture rigide 1 face' },
    { page: 'pages/couverture-rigide.php', id: 'Couverture rigide 2 faces', title: 'Couverture rigide 2 faces' },
    { page: 'pages/couverture-rigide.php', id: 'Couverture rigide personnalisables', title: 'Couverture rigide personnalisables' },
    { page: 'pages/couverture-panorama.php', id: 'Couverture rigide Panorama', title: 'Couverture rigide Panorama' },
    { page: 'pages/couverture-panorama.php', id: 'Couverture rigide Panorama personnalisée', title: 'Couverture rigide Panorama personnalisée' },
    
    // Panneaux
    { page: 'pages/panneau-photo.php', id: 'Panneau Photo', title: 'Panneau Photo' },
    { page: 'pages/panneau-acrylique.php', id: 'Panneau Acrylique', title: 'Panneau Acrylique' },
    { page: 'pages/panneau-bambou.php', id: 'Panneau Bambou', title: 'Panneau Bambou' },
    
    // Autres
    { page: 'pages/toile.php', id: 'Toile', title: 'Impression sur Toile' },
    { page: 'pages/magnet.php', id: 'Magnet', title: 'Magnet Photo' },
    { page: 'pages/infinity.php', id: 'Infinity', title: 'Dépliant Accordéon' },
    { page: 'pages/luxe.php', id: 'Luxe', title: 'Produits Luxe' },
    { page: 'pages/metal.php', id: 'Metal', title: 'Impression Métal' },
    { page: 'pages/pochette.php', id: 'Pochette', title: 'Pochette Photo' }
];

// Version intelligente de la recherche de site
function performSiteSearch() {
    const searchInput = document.getElementById('site-search-input');
    if (!searchInput) return;
    
    const query = searchInput.value.trim().toLowerCase();
    
    if (!query) {
        alert('Veuillez saisir un terme de recherche');
        return;
    }
    
    // Chercher dans tous les IDs et titres
    let bestMatch = null;
    let bestScore = 0;
    
    sectionIndex.forEach(section => {
        const titleLower = section.title.toLowerCase();
        const idLower = section.id.toLowerCase();
        let score = 0;
        
        // Score plus élevé pour correspondance exacte
        if (titleLower === query || idLower === query) {
            score = 100;
        }
        // Score élevé si le mot est contenu dans le titre ou ID
        else if (titleLower.includes(query) || idLower.includes(query)) {
            score = 50;
        }
        // Score plus faible pour correspondance de mots individuels
        else {
            const queryWords = query.split(' ');
            const titleWords = titleLower.split(' ');
            const idWords = idLower.split(' ');
            
            queryWords.forEach(qWord => {
                if (qWord.length > 2) { // Ignorer les mots trop courts
                    titleWords.forEach(tWord => {
                        if (tWord.includes(qWord) || qWord.includes(tWord)) {
                            score += 10;
                        }
                    });
                    idWords.forEach(iWord => {
                        if (iWord.includes(qWord) || qWord.includes(iWord)) {
                            score += 10;
                        }
                    });
                }
            });
        }
        
        if (score > bestScore) {
            bestScore = score;
            bestMatch = section;
        }
    });
    
    if (bestMatch) {
        // Déterminer le chemin de base
        let basePath = '';
        const currentPath = window.location.pathname;
        if (currentPath.includes('/pages/') || 
            currentPath.includes('/clients/') || 
            currentPath.includes('/admin/') ||
            currentPath.includes('/formulaires/')) {
            basePath = '../';
        }
        
        const url = `${basePath}${bestMatch.page}#${encodeURIComponent(bestMatch.id)}`;
        window.location.href = url;
    } else {
        alert('Texte introuvable');
    }
}

// Initialisation simple
document.addEventListener('DOMContentLoaded', function() {
    const searchBtn = document.getElementById('site-search-btn');
    const searchInput = document.getElementById('site-search-input');
    
    if (searchBtn) {
        searchBtn.addEventListener('click', performSiteSearch);
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSiteSearch();
            }
        });
    }
});