// Calcul du prix de la commande en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const inputNbPersonnes = document.getElementById('nombre_personnes');
    const checkboxHorsBordeaux = document.getElementById('hors_bordeaux');
    const inputKilometres = document.getElementById('kilometres');
    const divKilometres = document.getElementById('div_kilometres');

    const prixMenuElement = document.getElementById('prix_menu');
    const ligneReduction = document.getElementById('ligne_reduction');
    const montantReductionElement = document.getElementById('montant_reduction');
    const fraisLivraisonElement = document.getElementById('frais_livraison');
    const prixTotalElement = document.getElementById('prix_total');

    // Récupérer les données depuis les attributs data
    const prixParPersonne = parseFloat(inputNbPersonnes.dataset.prix);
    const personneMinimum = parseInt(inputNbPersonnes.dataset.minimum);

    function calculerPrix() {
        const nbPersonnes = parseInt(inputNbPersonnes.value) || personneMinimum;
        
        // 1. Prix menu de base
        let prixMenu = prixParPersonne * nbPersonnes;
        prixMenuElement.textContent = prixMenu.toFixed(2).replace('.', ',') + '€';
        
        // 2. Réduction 10% si +5 personnes au-dessus du minimum
        let reduction = 0;
        if (nbPersonnes >= (personneMinimum + 5)) {
            reduction = prixMenu * 0.10;
            ligneReduction.style.display = 'block';
            montantReductionElement.textContent = '- ' + reduction.toFixed(2).replace('.', ',') + '€';
            prixMenu -= reduction;
        } else {
            ligneReduction.style.display = 'none';
        }
        
        // 3. Frais de livraison
        let fraisLivraison = 0;
        if (checkboxHorsBordeaux.checked) {
            const km = parseFloat(inputKilometres.value) || 0;
            fraisLivraison = 5 + (km * 0.59);
        }
        fraisLivraisonElement.textContent = fraisLivraison.toFixed(2).replace('.', ',') + '€';
        
        // 4. Total final
        const total = prixMenu + fraisLivraison;
        prixTotalElement.textContent = total.toFixed(2).replace('.', ',') + '€';
    }

    // Afficher/masquer le champ kilomètres
    function toggleKilometres() {
    if (checkboxHorsBordeaux.checked) {
        divKilometres.style.display = 'block';
        inputKilometres.setAttribute('required', 'required');
        if (inputKilometres.value === '0') {
            inputKilometres.value = ''; // Vider pour forcer la saisie
        }
    } else {
        divKilometres.style.display = 'none';
        inputKilometres.removeAttribute('required');
        inputKilometres.value = '0'; // Remettre à 0
    }
    calculerPrix();
}

    // Événements
    if (inputNbPersonnes) {
        inputNbPersonnes.addEventListener('input', calculerPrix);
    }
    
    if (checkboxHorsBordeaux) {
        checkboxHorsBordeaux.addEventListener('change', toggleKilometres);
    }
    
    if (inputKilometres) {
        inputKilometres.addEventListener('input', calculerPrix);
    }
});