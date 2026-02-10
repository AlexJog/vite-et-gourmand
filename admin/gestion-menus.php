<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Récupérer tous les menus
$sql = "SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom
        FROM menu m
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        LEFT JOIN theme t ON m.theme_id = t.theme_id
        ORDER BY m.menu_id DESC";

$stmt = $pdo->query($sql);
$menus = $stmt->fetchAll();
?>
<?php require_once '../includes/header.php'; ?>

<script src="<?= BASE_URL ?>assets/js/main.js"></script>

<main>
    <section class="contact-section">
        <div class="container">
            
            <?php
            if (isset($_SESSION['succes_admin'])) {
                echo '<div class="alert alert-success">';
                echo '<p>' . htmlspecialchars($_SESSION['succes_admin']) . '</p>';
                echo '</div>';
                unset($_SESSION['succes_admin']);
            }
            
            if (isset($_SESSION['error_admin'])) {
                echo '<div class="alert alert-error">';
                echo '<p>' . htmlspecialchars($_SESSION['error_admin']) . '</p>';
                echo '</div>';
                unset($_SESSION['error_admin']);
            }
            ?>
            
            <div class="contact-header">
                <h1>Gestion des menus</h1>
                <p>Créer, modifier et supprimer les menus</p>
            </div>
            
            <!-- Bouton Créer un nouveau menu -->
            <div class="btn-creer-menu-container">
                <a href="ajouter-menu.php" class="btn-hero">➕ Créer un nouveau menu</a>
            </div>
            
            <!-- Liste des menus -->
            <div class="liste-menus-admin">
                
                <?php if (empty($menus)): ?>
                    <p class="message-aucun-menu">
                        Aucun menu disponible.
                    </p>
                <?php else: ?>
                    
                    <?php foreach ($menus as $menu): ?>
                        <div class="menu-admin-carte">
                            
                            <!-- Image -->
                            <?php $image = !empty($menu['image_url']) ? $menu['image_url'] : '/assets/images/menus/menu-default.jpg';?>
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($menu['nom']); ?>" class="menu-admin-image">
                            
                            <!-- Informations -->
                            <div class="menu-admin-infos">
                                <h3 class="menu-admin-titre">
                                    <?php echo htmlspecialchars($menu['nom']); ?>
                                </h3>
                                <p class="menu-admin-details">
                                    <strong>Service :</strong> <?php echo htmlspecialchars($menu['service']); ?> | 
                                    <strong>Régime :</strong> <?php echo htmlspecialchars($menu['regime_nom']); ?> | 
                                    <strong>Thème :</strong> <?php echo htmlspecialchars($menu['theme_nom']); ?>
                                </p>
                                <p class="menu-admin-prix">
                                    <strong>Prix :</strong> <?php echo number_format($menu['prix_par_personne'], 2, ',', ' '); ?>€/pers | 
                                    <strong>Min :</strong> <?php echo $menu['personne_minimum']; ?> pers | 
                                    <strong>Stock :</strong> <?php echo $menu['quantite_restante']; ?>
                                </p>
                            </div>
                            
                            <!-- Actions -->
                            <div class="menu-admin-actions">
                                <a href="modifier-menu.php?id=<?php echo $menu['menu_id']; ?>" class="btn-menu-modifier">
                                    ✏️ Modifier
                                </a>
                                <a href="#" onclick="afficherPopup('supprimer-menu.php?id=<?php echo $menu['menu_id']; ?>', event)" class="btn-menu-supprimer">
                                    🗑️ Supprimer
                                </a>
                            </div>
                            
                        </div>
                    <?php endforeach; ?>
                    
                <?php endif; ?>
                
            </div>
            
        </div>
    </section>

    <!-- Popup de confirmation de suppression -->
    <div class="popup-overlay" id="popupSuppression">
        <div class="popup-box">
            <h3>⚠️ Confirmation de suppression</h3>
            <p>Êtes-vous sûr de vouloir supprimer ce menu ?<br>Cette action est irréversible.</p>
            <div class="popup-actions">
                <button class="popup-btn popup-btn-annuler" onclick="fermerPopup()">Annuler</button>
                <a href="#" id="lienSuppression" class="popup-btn popup-btn-confirmer" style="text-decoration: none;">Supprimer</a>
            </div>
        </div>
    </div>

</main>

<?php require_once '../includes/footer.php'; ?>