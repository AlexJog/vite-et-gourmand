<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Récupérer les régimes et thèmes pour les selects
$regimes = $pdo->query("SELECT * FROM regime ORDER BY libelle")->fetchAll();
$themes = $pdo->query("SELECT * FROM theme ORDER BY libelle")->fetchAll();
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <section class="contact-section">
        <div class="container">
            
            <?php
            if (isset($_SESSION['erreurs_menu'])) {
                echo '<div class="alert alert-error">';
                foreach ($_SESSION['erreurs_menu'] as $erreur) {
                    echo '<p>• ' . htmlspecialchars($erreur) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['erreurs_menu']);
            }
            ?>
            
            <div class="contact-header">
                <h1>Créer un nouveau menu</h1>
                <p>Remplissez tous les champs pour ajouter un menu</p>
            </div>
            
            <!-- Formulaire -->
            <div class="contact-form-container">
                
                <form action="traitement-menu.php" method="POST" enctype="multipart/form-data" class="contact-form">
                    
                    <input type="hidden" name="action" value="ajouter">
                    
                    <!-- Nom du menu -->
                    <div class="form-group">
                        <label for="nom">Nom du menu *</label>
                        <input type="text" id="nom" name="nom" placeholder="Ex: Menu Noël Traditionnel" required>
                    </div>
                    
                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="4" placeholder="Description du menu..." required></textarea>
                    </div>
                    
                    <!-- Service -->
                    <div class="form-group">
                        <label for="service">Service *</label>
                        <select id="service" name="service" required>
                            <option value="">-- Sélectionnez --</option>
                            <option value="Petit-déjeuner">Petit-déjeuner</option>
                            <option value="Déjeuner">Déjeuner</option>
                            <option value="Dîner">Dîner</option>
                            <option value="Cocktail">Cocktail</option>
                            <option value="Buffet">Buffet</option>
                        </select>
                    </div>
                    
                    <!-- Régime et Thème -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="regime_id">Régime *</label>
                            <select id="regime_id" name="regime_id" required>
                                <option value="">-- Sélectionnez --</option>
                                <?php foreach ($regimes as $regime): ?>
                                    <option value="<?php echo $regime['regime_id']; ?>">
                                        <?php echo htmlspecialchars($regime['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="theme_id">Thème *</label>
                            <select id="theme_id" name="theme_id" required>
                                <option value="">-- Sélectionnez --</option>
                                <?php foreach ($themes as $theme): ?>
                                    <option value="<?php echo $theme['theme_id']; ?>">
                                        <?php echo htmlspecialchars($theme['libelle']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Prix et Personnes -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="prix_par_personne">Prix par personne (€) *</label>
                            <input type="number" id="prix_par_personne" name="prix_par_personne" step="0.01" min="0" placeholder="25.00" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="personne_minimum">Nombre minimum de personnes *</label>
                            <input type="number" id="personne_minimum" name="personne_minimum" min="1" placeholder="10" required>
                        </div>
                    </div>
                    
                    <!-- Stock -->
                    <div class="form-group">
                        <label for="quantite_restante">Stock disponible *</label>
                        <input type="number" id="quantite_restante" name="quantite_restante" min="0" placeholder="50" required>
                    </div>
                    
                    <!-- Upload de l'image -->
                    <div class="form-group">
                        <label for="image">Image du menu *</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png" required>
                        <small class="form-hint">Formats acceptés : JPG, JPEG, PNG (max 5 Mo)</small>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="form-buttons">
                        <button type="submit" class="btn-contact">✅ Créer le menu</button>
                        <a href="gestion-menus.php" class="btn-secondary">❌ Annuler</a>
                    </div>
                    
                </form>
                
            </div>
            
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>