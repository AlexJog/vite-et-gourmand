// MENU BURGER MOBILE

// On attend que le DOM soit chargé
document.addEventListener('DOMContentLoaded', function() {
    
    // On récupère les éléments
    const burgerMenu = document.getElementById('burgerMenu');
    const mobileMenu = document.getElementById('mobileMenu');
    
    // Si le burger existe
    if (burgerMenu && mobileMenu) {
        
        // Au clic sur le burger
        burgerMenu.addEventListener('click', function() {
            
            // On ajoute/retire la classe "active" au menu mobile
            mobileMenu.classList.toggle('active');
            
            // Animation du burger (transformation en X)
            this.classList.toggle('active');
        });
        
        // Fermer le menu si on clique sur un lien
        const mobileLinks = document.querySelectorAll('.mobile-link');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                burgerMenu.classList.remove('active');
            });
        });
    }
});

// Fonction pour afficher la popup de suppression
function afficherPopup(lien, event) {
    event.preventDefault(); // ← IMPORTANT ! Empêche la navigation
    document.getElementById('popupSuppression').classList.add('active');
    document.getElementById('lienSuppression').href = lien;
}

// Fonction pour fermer la popup
function fermerPopup() {
    document.getElementById('popupSuppression').classList.remove('active');
}

// Fermer la popup si on clique en dehors
document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('popupSuppression');
    if (popup) {
        popup.addEventListener('click', function(e) {
            if (e.target === this) {
                fermerPopup();
            }
        });
    }
});