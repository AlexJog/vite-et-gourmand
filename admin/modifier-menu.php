<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Vérifier que l'ID est dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: gestion-menus.php');
    exit;
}

$menu_id = (int)$_GET['id'];

// Récupérer les infos du menu
$sql = "SELECT * FROM menu WHERE menu_id = :menu_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['menu_id' => $menu_id]);
$menu = $stmt->fetch();

// Si le menu n'existe pas
if (!$menu) {
    $_SESSION['error_admin'] = "Ce menu n'existe pas.";
    header('Location: gestion-menus.php');
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
                <h1>Modifier le menu</h1>
                <p>Modifiez les informations du menu "<?php echo htmlspecialchars($menu['nom']); ?>"</p>
            </div>
            
            <!-- Formulaire -->
            <div class="contact-form-container">
                
                <form action="traitement-menu.php" method="POST" enctype="multipart/form-data" class="contact-form">
                    
                    <input type="hidden" name="action" value="modifier">
                    <input type="hidden" name="menu_id" value="<?php echo $menu_id; ?>">
                    
                    <!-- Nom du menu -->
                    <div class="form-group">
                        <label for="nom">Nom du menu *</label>
                        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($menu['nom']); ?>" required>
                    </div>
                    
                    <!-- Description -->
                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($menu['description']); ?></textarea>
                    </div>
                    
                    <!-- Service -->
                    <div class="form-group">
                        <label for="service">Service *</label>
                        <select id="service" name="service" required>
                            <option value="">-- Sélectionnez --</option>
                            <option value="Petit-déjeuner" <?php echo ($menu['service'] == 'Petit-déjeuner') ? 'selected' : ''; ?>>Petit-déjeuner</option>
                            <option value="Déjeuner" <?php echo ($menu['service'] == 'Déjeuner') ? 'selected' : ''; ?>>Déjeuner</option>
                            <option value="Dîner" <?php echo ($menu['service'] == 'Dîner') ? 'selected' : ''; ?>>Dîner</option>
                            <option value="Cocktail" <?php echo ($menu['service'] == 'Cocktail') ? 'selected' : ''; ?>>Cocktail</option>
                            <option value="Buffet" <?php echo ($menu['service'] == 'Buffet') ? 'selected' : ''; ?>>Buffet</option>
                        </select>
                    </div>
                    
                    <!-- Régime et Thème -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="regime_id">Régime *</label>
                            <select id="regime_id" name="regime_id" required>
                                <option value="">-- Sélectionnez --</option>
                                <?php foreach ($regimes as $regime): ?>
                                    <option value="<?php echo $regime['regime_id']; ?>" <?php echo ($menu['regime_id'] == $regime['regime_id']) ? 'selected' : ''; ?>>
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
                                    <option value="<?php echo $theme['theme_id']; ?>" <?php echo ($menu['theme_id'] == $theme['theme_id']) ? 'selected' : ''; ?>>
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
                            <input type="number" id="prix_par_personne" name="prix_par_personne" step="0.01" min="0" value="<?php echo $menu['prix_par_personne']; ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="personne_minimum">Nombre minimum de personnes *</label>
                            <input type="number" id="personne_minimum" name="personne_minimum" min="1" value="<?php echo $menu['personne_minimum']; ?>" required>
                        </div>
                    </div>
                    
                    <!-- Stock -->
                    <div class="form-group">
                        <label for="quantite_restante">Stock disponible *</label>
                        <input type="number" id="quantite_restante" name="quantite_restante" min="0" value="<?php echo $menu['quantite_restante']; ?>" required>
                    </div>
                    
                    <!-- Image actuelle -->
                    <?php if (!empty($menu['image_url'])): ?>
                        <div class="form-group">
                            <label>Image actuelle :</label>
                            <div class="image-actuelle-container">
                                <img src="<?php echo htmlspecialchars($menu['image_url']); ?>" alt="Image actuelle" class="image-actuelle-preview">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Upload nouvelle image -->
                    <div class="form-group">
                        <label for="image">Changer l'image (optionnel)</label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/jpg,image/png">
                        <small class="form-hint">Laissez vide pour conserver l'image actuelle. Formats acceptés : JPG, JPEG, PNG (max 10 Mo)</small>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="form-buttons">
                        <button type="submit" class="btn-contact">✅ Enregistrer les modifications</button>
                        <a href="gestion-menus.php" class="btn-secondary">❌ Annuler</a>
                    </div>
                    
                </form>
                
            </div>
            
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>