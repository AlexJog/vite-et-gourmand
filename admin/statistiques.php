<?php
require_once '../includes/config.php';
require_once '../includes/json-config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Compter le nombre total de commandes dans JSON
$data_json = lireStatsJSON();
$nb_total = count($data_json);

// Récupérer les filtres
$menu_filtre = isset($_GET['menu_id']) && !empty($_GET['menu_id']) ? (int)$_GET['menu_id'] : null;
$date_debut = isset($_GET['date_debut']) && !empty($_GET['date_debut']) ? $_GET['date_debut'] : null;
$date_fin = isset($_GET['date_fin']) && !empty($_GET['date_fin']) ? $_GET['date_fin'] : null;

// Construire les filtres
$filtres = [];
if ($menu_filtre) $filtres['menu_id'] = $menu_filtre;
if ($date_debut) $filtres['date_debut'] = $date_debut;
if ($date_fin) $filtres['date_fin'] = $date_fin;

// Calculer les statistiques
$stats_menus = calculerStatsParMenu($filtres);

// Récupérer la liste des menus pour le filtre
$sql_menus = "SELECT menu_id, nom FROM menu ORDER BY nom";
$stmt_menus = $pdo->query($sql_menus);
$menus = $stmt_menus->fetchAll();
?>
<?php require_once '../includes/header.php'; ?>

<main>

    <?php
    // Afficher le message de succès
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

    <section class="contact-section">
        <div class="container">
            
            <div class="contact-header">
                <h1>📊 Statistiques & Chiffre d'affaires</h1>
                <p>Données provenant de la base JSON (NoSQL)</p>
            </div>
            
            <!-- Bouton Synchroniser -->
            <div class="btn-sync-container">
                <a href="#" id="btnSync" class="btn-hero">
                    🔄 Synchroniser les données
                </a>
                <p class="btn-sync-info">
                    <?php echo $nb_total; ?> commande(s) dans la base JSON
                </p>
            </div>
            
            <!-- Filtres -->
            <div class="commande-carte stats-filtres-carte">
                <h3 class="stats-filtres-titre">🔍 Filtrer les statistiques</h3>
                
                <form method="GET" action="statistiques.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="menu_id">Menu</label>
                            <select id="menu_id" name="menu_id">
                                <option value="">Tous les menus</option>
                                <?php foreach ($menus as $menu): ?>
                                    <option value="<?php echo $menu['menu_id']; ?>" <?php echo ($menu_filtre == $menu['menu_id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($menu['nom']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_debut">Date de début</label>
                            <input type="date" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($date_debut ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="date_fin">Date de fin</label>
                            <input type="date" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($date_fin ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="submit" class="btn-contact">🔎 Filtrer</button>
                        <a href="statistiques.php" class="btn-secondary">🔄 Réinitialiser</a>
                    </div>
                </form>
            </div>
            
            <!-- Statistiques par menu -->
            <?php if (empty($stats_menus)): ?>
                <div class="message-aucune-commande">
                    <p>Aucune donnée disponible. Veuillez synchroniser les données.</p>
                    <a href="sync-json.php" class="btn-hero">🔄 Synchroniser maintenant</a>
                </div>
            <?php else: ?>
                <div class="liste-commandes">
                    <?php foreach ($stats_menus as $stat): ?>
                        <div class="commande-carte">
                            <h3 class="commande-titre"><?php echo htmlspecialchars($stat['menu_nom']); ?></h3>
                            
                            <div class="commande-details">
                                <div>
                                    <p>
                                        <strong>📦 Nombre de commandes :</strong><br>
                                        <span class="stat-nombre-commandes">
                                            <?php echo $stat['nb_commandes']; ?>
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <p>
                                        <strong>💰 Chiffre d'affaires :</strong><br>
                                        <span class="stat-nombre-ca">
                                            <?php echo number_format($stat['chiffre_affaires'], 2, ',', ' '); ?>€
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="commande-details">
                                <div>
                                    <p>
                                        <strong>👥 Personnes servies :</strong><br>
                                        <?php echo $stat['nb_personnes_total']; ?> personnes
                                    </p>
                                </div>
                                
                                <div>
                                    <p>
                                        <strong>📊 CA moyen par commande :</strong><br>
                                        <?php echo number_format($stat['chiffre_affaires'] / $stat['nb_commandes'], 2, ',', ' '); ?>€
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Graphique (avec Chart.js) -->
                <div class="commande-carte stats-graphique-carte">
                    <h3 class="stats-graphique-titre">📈 Graphique : Commandes par menu</h3>
                    <canvas 
                        id="chartCommandes" 
                        class="stats-graphique-canvas"
                        data-labels='<?php echo json_encode(array_column($stats_menus, 'menu_nom')); ?>'
                        data-values='<?php echo json_encode(array_column($stats_menus, 'nb_commandes')); ?>'
                    ></canvas>
                </div>
                
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script src="../assets/js/chart-stats.js"></script>
            <?php endif; ?>
            
        </div>
    </section>
    
    <!-- Popup de confirmation synchronisation -->
    <div class="popup-overlay" id="popupSync">
        <div class="popup-box">
            <h3>🔄 Synchroniser les données ?</h3>
            <p>Les données MySQL seront copiées vers la base JSON (NoSQL)</p>
            <div class="form-buttons">
                <button class="btn-contact" id="btnConfirmSync">✅ Oui, synchroniser</button>
                <button class="btn-secondary" id="btnCancelSync">❌ Annuler</button>
            </div>
        </div>
    </div>
</main>

<!-- Script popup -->
<script src="../assets/js/popup.js"></script>

<?php require_once '../includes/footer.php'; ?>