<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un employé ou un admin
if ($_SESSION['user_role'] !== 'employe' && $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Récupérer le filtre de statut (si présent)
$filtre_statut = isset($_GET['statut']) ? $_GET['statut'] : 'tous';

// Construire la requête SQL selon le filtre
$sql = "SELECT a.*, u.prenom, u.nom, c.commande_id, m.nom AS menu_nom
        FROM avis a
        INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
        INNER JOIN commande c ON a.commande_id = c.commande_id
        INNER JOIN menu m ON c.menu_id = m.menu_id";

if ($filtre_statut !== 'tous') {
    $sql .= " WHERE a.statut = :statut";
}

$sql .= " ORDER BY a.date_avis DESC";

$stmt = $pdo->prepare($sql);

if ($filtre_statut !== 'tous') {
    $stmt->execute(['statut' => $filtre_statut]);
} else {
    $stmt->execute();
}

$avis_list = $stmt->fetchAll();

// Compter les avis par statut
$sql_stats = "SELECT statut, COUNT(*) as nb FROM avis GROUP BY statut";
$stmt_stats = $pdo->query($sql_stats);
$stats = [];
while ($row = $stmt_stats->fetch()) {
    $stats[$row['statut']] = $row['nb'];
}
?>
<?php require_once '../includes/header.php'; ?>

<main>

    <?php
    // Afficher le message de succès
    if (isset($_SESSION['succes_employe'])) {
        echo '<div class="alert alert-success">';
        echo '<p>' . htmlspecialchars($_SESSION['succes_employe']) . '</p>';
        echo '</div>';
        unset($_SESSION['succes_employe']);
    }
    
    // Afficher les erreurs
    if (isset($_SESSION['error_employe'])) {
        echo '<div class="alert alert-error">';
        echo '<p>' . htmlspecialchars($_SESSION['error_employe']) . '</p>';
        echo '</div>';
        unset($_SESSION['error_employe']);
    }
    ?>

    <section class="contact-section">
        <div class="container">
            
            <div class="contact-header">
                <h1>Gestion des avis</h1>
                <p>Validez ou refusez les avis clients</p>
            </div>
            
            <!-- Statistiques rapides -->
            <div class="stats-grid-avis">
                
                <a href="?statut=tous" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'tous' ? 'active-tous' : ''; ?>">
                        <h3>📝 Tous</h3>
                        <p class="stat-card-number stat-number-all">
                            <?php echo array_sum($stats); ?>
                        </p>
                    </div>
                </a>
                
                <a href="?statut=en attente" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'en attente' ? 'active-attente' : ''; ?>">
                        <h3>⏳ En attente</h3>
                        <p class="stat-card-number stat-number-attente">
                            <?php echo $stats['en attente'] ?? 0; ?>
                        </p>
                    </div>
                </a>
                
                <a href="?statut=validé" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'validé' ? 'active-livre' : ''; ?>">
                        <h3>✅ Validés</h3>
                        <p class="stat-card-number stat-number-livre">
                            <?php echo $stats['validé'] ?? 0; ?>
                        </p>
                    </div>
                </a>
                
                <a href="?statut=refusé" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'refusé' ? 'active-refusee' : ''; ?>">
                        <h3>❌ Refusés</h3>
                        <p class="stat-card-number stat-number-refusee">
                            <?php echo $stats['refusé'] ?? 0; ?>
                        </p>
                    </div>
                </a>
                
            </div>
            
            <?php if (empty($avis_list)): ?>
                <!-- Aucun avis -->
                <div class="message-aucune-commande-gestion">
                    <p>
                        <?php 
                        if ($filtre_statut === 'tous') {
                            echo "Aucun avis pour le moment.";
                        } else {
                            echo "Aucun avis avec le statut \"$filtre_statut\".";
                        }
                        ?>
                    </p>
                </div>
            <?php else: ?>
                <!-- Liste des avis -->
                <div class="liste-avis-gestion">
                    <?php foreach ($avis_list as $avis): ?>
                        <div class="avis-carte-gestion">
                            
                            <!-- En-tête -->
                            <div class="avis-gestion-header">
                                <div>
                                    <h3 class="avis-gestion-nom">
                                        <?php echo htmlspecialchars($avis['prenom'] . ' ' . $avis['nom']); ?>
                                    </h3>
                                    <p class="avis-gestion-info">
                                        Menu : <?php echo htmlspecialchars($avis['menu_nom']); ?> • 
                                        <?php echo date('d/m/Y à H:i', strtotime($avis['date_avis'])); ?>
                                    </p>
                                </div>
                                
                                <!-- Badge statut -->
                                <?php
                                $statut = $avis['statut'];
                                if ($statut === 'en attente') {
                                    $badge_color = '#FFC107';
                                    $badge_text = '⏳ En attente';
                                } elseif ($statut === 'validé') {
                                    $badge_color = '#28A745';
                                    $badge_text = '✅ Validé';
                                } elseif ($statut === 'refusé') {
                                    $badge_color = '#DC3545';
                                    $badge_text = '❌ Refusé';
                                } else {
                                    $badge_color = '#6C757D';
                                    $badge_text = $statut;
                                }
                                ?>
                                <div class="badge-statut" style="background-color: <?php echo $badge_color; ?>;">
                                    <?php echo $badge_text; ?>
                                </div>
                            </div>
                            
                            <!-- Note en étoiles -->
                            <div class="avis-note-section">
                                <p><strong>Note :</strong></p>
                                <div class="avis-etoiles">
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $avis['note']) {
                                            echo '★';
                                        } else {
                                            echo '☆';
                                        }
                                    }
                                    ?>
                                    <span class="avis-note-texte">
                                        (<?php echo $avis['note']; ?>/5)
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Commentaire -->
                            <div class="avis-commentaire-box">
                                <p>
                                    <strong>💬 Commentaire :</strong><br>
                                    <?php echo nl2br(htmlspecialchars($avis['commentaire'])); ?>
                                </p>
                            </div>
                            
                            <!-- Boutons d'action -->
                            <?php if ($avis['statut'] === 'en attente'): ?>
                                <div class="avis-actions">
                                    
                                    <!-- Valider -->
                                    <form method="POST" action="traiter-avis.php">
                                        <input type="hidden" name="avis_id" value="<?php echo $avis['avis_id']; ?>">
                                        <input type="hidden" name="action" value="valider">
                                        <button type="submit" class="btn-avis-valider">
                                            ✅ Valider
                                        </button>
                                    </form>
                                    
                                    <!-- Refuser -->
                                    <form method="POST" action="traiter-avis.php">
                                        <input type="hidden" name="avis_id" value="<?php echo $avis['avis_id']; ?>">
                                        <input type="hidden" name="action" value="refuser">
                                        <button type="submit" class="btn-avis-refuser">
                                            ❌ Refuser
                                        </button>
                                    </form>
                                    
                                </div>
                            <?php else: ?>
                                <div class="avis-statut-final">
                                    <p>
                                        Avis <?php echo $avis['statut']; ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>