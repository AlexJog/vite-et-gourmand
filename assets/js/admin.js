// Fonction pour afficher la popup de suppression
function afficherPopup(lien, event) {
    event.preventDefault();
    document.getElementById('popupSuppression').classList.add('active');
    document.getElementById('lienSuppression').href = lien;
}

// Fonction pour fermer la popup
function fermerPopup() {
    document.getElementById('popupSuppression').classList.remove('active');
}

// Fermer la popup si on clique en dehors
window.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('popupSuppression');
    if (popup) {
        popup.addEventListener('click', function(e) {
            if (e.target === this) {
                fermerPopup();
            }
        });
    }
});