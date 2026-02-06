// Gestion du popup de confirmation pour la synchronisation
document.addEventListener('DOMContentLoaded', function() {
    const btnSync = document.getElementById('btnSync');
    const popup = document.getElementById('popupSync');
    const btnConfirm = document.getElementById('btnConfirmSync');
    const btnCancel = document.getElementById('btnCancelSync');
    
    if (btnSync && popup) {
        // Ouvrir le popup
        btnSync.addEventListener('click', function(e) {
            e.preventDefault();
            popup.classList.add('active');
        });
        
        // Confirmer la synchronisation
        if (btnConfirm) {
            btnConfirm.addEventListener('click', function() {
                window.location.href = 'sync-json.php';
            });
        }
        
        // Annuler
        if (btnCancel) {
            btnCancel.addEventListener('click', function() {
                popup.classList.remove('active');
            });
        }
        
        // Fermer en cliquant sur l'overlay
        popup.addEventListener('click', function(e) {
            if (e.target === popup) {
                popup.classList.remove('active');
            }
        });
    }
});