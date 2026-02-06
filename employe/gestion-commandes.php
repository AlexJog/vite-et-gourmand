<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un employé ou admin
if ($_SESSION['user_role'] !== 'employe' && $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Récupérer le filtre de statut (si présent)
$filtre_statut = isset($_GET['statut']) ? $_GET['statut'] : 'tous';

// Construire la requête SQL selon le filtre
$sql = "SELECT c.*, m.nom AS menu_nom, u.prenom, u.nom AS user_nom, u.email, u.telephone
        FROM commande c
        INNER JOIN menu m ON c.menu_id = m.menu_id
        INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id";

if ($filtre_statut !== 'tous') {
    $sql .= " WHERE c.statut = :statut";
}

$sql .= " ORDER BY c.date_commande DESC";

$stmt = $pdo->prepare($sql);

if ($filtre_statut !== 'tous') {
    $stmt->execute(['statut' => $filtre_statut]);
} else {
    $stmt->execute();
}

$commandes = $stmt->fetchAll();

// Compter les commandes par statut
$sql_stats = "SELECT statut, COUNT(*) as nb FROM commande GROUP BY statut";
$stmt_stats = $pdo->query($sql_stats);
$stats = [];
while ($row = $stmt_stats->fetch()) {
    $stats[$row['statut']] = $row['nb'];
}
?>
<?php require_once '../includes/header.php'; ?>

<main>

    <?php
    if (isset($_SESSION['succes_employe'])) {
        echo '<div class="alert alert-success">';
        echo '<p>' . htmlspecialchars($_SESSION['succes_employe']) . '</p>';
        echo '</div>';
        unset($_SESSION['succes_employe']);
    }
    
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
                <h1>Gestion des commandes</h1>
                <p>Gérez toutes les commandes des clients</p>
            </div>
            
            <!-- Statistiques rapides -->
            <div class="stats-grid">
                
                <a href="?statut=tous" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'tous' ? 'active-tous' : ''; ?>">
                        <h3>📦 Toutes</h3>
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
                
                <a href="?statut=accepté" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'accepté' ? 'active-accepte' : ''; ?>">
                        <h3>✅ Acceptées</h3>
                        <p class="stat-card-number stat-number-accepte">
                            <?php echo $stats['accepté'] ?? 0; ?>
                        </p>
                    </div>
                </a>
                
                <a href="?statut=en préparation" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'en préparation' ? 'active-preparation' : ''; ?>">
                        <h3>👨‍🍳 Préparation</h3>
                        <p class="stat-card-number stat-number-preparation">
                            <?php echo $stats['en préparation'] ?? 0; ?>
                        </p>
                    </div>
                </a>
                
                <a href="?statut=en livraison" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'en livraison' ? 'active-livraison' : ''; ?>">
                        <h3>🚚 Livraison</h3>
                        <p class="stat-card-number stat-number-livraison">
                            <?php echo $stats['en livraison'] ?? 0; ?>
                        </p>
                    </div>
                </a>
                
                <a href="?statut=livré" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'livré' ? 'active-livre' : ''; ?>">
                        <h3>📦 Livrées</h3>
                        <p class="stat-card-number stat-number-livre">
                            <?php echo $stats['livré'] ?? 0; ?>
                        </p>
                    </div>
                </a>
                
                <a href="?statut=attente matériel" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'attente matériel' ? 'active-materiel' : ''; ?>">
                        <h3>🔄 Attente mat.</h3>
                        <p class="stat-card-number stat-number-materiel">
                            <?php echo $stats['attente matériel'] ?? 0; ?>
                        </p>
                    </div>
                </a>
                
                <a href="?statut=refusée" class="stat-card-link">
                    <div class="dashboard-card stat-card <?php echo $filtre_statut === 'refusée' ? 'active-refusee' : ''; ?>">
                        <h3>❌ Refusées</h3>
                        <p class="stat-card-number stat-number-refusee">
                            <?php echo $stats['refusée'] ?? 0; ?>
                        </p>
                    </div>
                </a>
                
            </div>
            
            <?php if (empty($commandes)): ?>
                <!-- Aucune commande -->
                <div class="message-aucune-commande-gestion">
                    <p>
                        <?php 
                        if ($filtre_statut === 'tous') {
                            echo "Aucune commande pour le moment.";
                        } else {
                            echo "Aucune commande avec le statut \"$filtre_statut\".";
                        }
                        ?>
                    </p>
                </div>
            <?php else: ?>
                <!-- Liste des commandes -->
                <div class="liste-commandes-gestion">
                    <?php foreach ($commandes as $commande): ?>
                        <div class="commande-carte-gestion">
                            
                            <!-- En-tête -->
                            <div class="commande-gestion-header">
                                <div>
                                    <h3 class="commande-gestion-titre">
                                        <?php echo htmlspecialchars($commande['menu_nom']); ?>
                                    </h3>
                                    <p class="commande-gestion-info">
                                        Commande #<?php echo $commande['commande_id']; ?> - 
                                        <?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?>
                                    </p>
                                </div>
                                
                                <!-- Badge statut -->
                                <?php
                                $statut = $commande['statut'];
                                if ($statut === 'en attente') {
                                    $badge_color = '#FFC107';
                                    $badge_text = '⏳ En attente';
                                } elseif ($statut === 'accepté') {
                                    $badge_color = '#17A2B8';
                                    $badge_text = '✅ Accepté';
                                } elseif ($statut === 'en préparation') {
                                    $badge_color = '#FD7E14';
                                    $badge_text = '👨‍🍳 En préparation';
                                } elseif ($statut === 'en livraison') {
                                    $badge_color = '#007BFF';
                                    $badge_text = '🚚 En livraison';
                                } elseif ($statut === 'livré') {
                                    $badge_color = '#28A745';
                                    $badge_text = '📦 Livré';
                                } elseif ($statut === 'attente matériel') {
                                    $badge_color = '#FF9800';
                                    $badge_text = '🔄 Attente matériel';
                                } elseif ($statut === 'terminée') {
                                    $badge_color = '#6B8E23';
                                    $badge_text = '✅ Terminée';
                                } elseif ($statut === 'refusée') {
                                    $badge_color = '#DC3545';
                                    $badge_text = '❌ Refusée';
                                } else {
                                    $badge_color = '#6C757D';
                                    $badge_text = $statut;
                                }
                                ?>
                                <div class="badge-statut" style="background-color: <?php echo $badge_color; ?>;">
                                    <?php echo $badge_text; ?>
                                </div>
                            </div>
                            
                            <!-- Informations -->
                            <div class="commande-info-grid">
                                
                                <!-- Client -->
                                <div class="commande-info-section">
                                    <h4>👤 Client</h4>
                                    <p>
                                        <strong><?php echo htmlspecialchars($commande['prenom'] . ' ' . $commande['user_nom']); ?></strong>
                                    </p>
                                    <p>
                                        <small>📧 <?php echo htmlspecialchars($commande['email']); ?></small>
                                    </p>
                                    <p>
                                        <small>📱 <?php echo htmlspecialchars($commande['telephone']); ?></small>
                                    </p>
                                </div>
                                
                                <!-- Prestation -->
                                <div class="commande-info-section">
                                    <h4>📅 Prestation</h4>
                                    <p>
                                        <strong>Date :</strong> <?php echo date('d/m/Y', strtotime($commande['date_prestation'])); ?>
                                    </p>
                                    <p>
                                        <strong>Heure :</strong> <?php echo date('H:i', strtotime($commande['heure_livraison'])); ?>
                                    </p>
                                    <p>
                                        <strong>Personnes :</strong> <?php echo $commande['nombre_personnes']; ?>
                                    </p>
                                </div>
                                
                                <!-- Livraison -->
                                <div class="commande-info-section">
                                    <h4>📍 Livraison</h4>
                                    <p>
                                        <small>
                                            <?php echo htmlspecialchars($commande['adresse_livraison']); ?><br>
                                            <?php echo htmlspecialchars($commande['code_postal']); ?> <?php echo htmlspecialchars($commande['ville']); ?>
                                        </small>
                                    </p>
                                </div>
                                
                            </div>
                            
                            <!-- Commentaire -->
                            <?php if (!empty($commande['commentaire'])): ?>
                                <div class="commande-commentaire-box">
                                    <p>
                                        <strong>💬 Commentaire :</strong><br>
                                        <em><?php echo nl2br(htmlspecialchars($commande['commentaire'])); ?></em>
                                    </p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Prix et Actions -->
                            <div class="commande-footer">
                                
                                <!-- Prix -->
                                <div>
                                    <p class="commande-prix-total">
                                        Total : <?php echo number_format($commande['prix_total'], 2, ',', ' '); ?>€
                                    </p>
                                </div>
                                
                                <!-- Boutons d'action -->
                                <div class="commande-actions">
                                    
                                    <?php if ($commande['statut'] === 'en attente'): ?>
                                        <!-- Accepter ou Refuser -->
                                        <form method="POST" action="traiter-commande.php">
                                            <input type="hidden" name="commande_id" value="<?php echo $commande['commande_id']; ?>">
                                            <input type="hidden" name="nouveau_statut" value="accepté">
                                            <button type="submit" class="btn-action btn-accepter">
                                                ✅ Accepter
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="traiter-commande.php">
                                            <input type="hidden" name="commande_id" value="<?php echo $commande['commande_id']; ?>">
                                            <input type="hidden" name="nouveau_statut" value="refusée">
                                            <button type="submit" class="btn-action btn-refuser">
                                                ❌ Refuser
                                            </button>
                                        </form>
                                    
                                    <?php elseif ($commande['statut'] === 'accepté'): ?>
                                        <!-- Passer en préparation -->
                                        <form method="POST" action="traiter-commande.php">
                                            <input type="hidden" name="commande_id" value="<?php echo $commande['commande_id']; ?>">
                                            <input type="hidden" name="nouveau_statut" value="en préparation">
                                            <button type="submit" class="btn-action btn-preparation">
                                                👨‍🍳 Passer en préparation
                                            </button>
                                        </form>
                                    
                                    <?php elseif ($commande['statut'] === 'en préparation'): ?>
                                        <!-- Passer en livraison -->
                                        <form method="POST" action="traiter-commande.php">
                                            <input type="hidden" name="commande_id" value="<?php echo $commande['commande_id']; ?>">
                                            <input type="hidden" name="nouveau_statut" value="en livraison">
                                            <button type="submit" class="btn-action btn-livraison">
                                                🚚 Passer en livraison
                                            </button>
                                        </form>
                                    
                                    <?php elseif ($commande['statut'] === 'en livraison'): ?>
                                        <!-- Marquer comme livré -->
                                        <form method="POST" action="traiter-commande.php">
                                            <input type="hidden" name="commande_id" value="<?php echo $commande['commande_id']; ?>">
                                            <input type="hidden" name="nouveau_statut" value="livré">
                                            <button type="submit" class="btn-action btn-livre">
                                                📦 Marquer comme livré
                                            </button>
                                        </form>
                                        
                                        <!-- OU avec matériel prêté -->
                                        <form method="POST" action="traiter-commande.php">
                                            <input type="hidden" name="commande_id" value="<?php echo $commande['commande_id']; ?>">
                                            <input type="hidden" name="nouveau_statut" value="attente matériel">
                                            <input type="hidden" name="pret_materiel" value="1">
                                            <button type="submit" class="btn-action btn-materiel">
                                                📦 Livré avec matériel prêté
                                            </button>
                                        </form>
                                    
                                    <?php elseif ($commande['statut'] === 'livré'): ?>
                                        <!-- Terminer -->
                                        <form method="POST" action="traiter-commande.php">
                                            <input type="hidden" name="commande_id" value="<?php echo $commande['commande_id']; ?>">
                                            <input type="hidden" name="nouveau_statut" value="terminée">
                                            <button type="submit" class="btn-action btn-terminer">
                                                ✅ Terminer la commande
                                            </button>
                                        </form>
                                    
                                    <?php elseif ($commande['statut'] === 'attente matériel'): ?>
                                        <!-- Matériel restitué -->
                                        <form method="POST" action="traiter-commande.php">
                                            <input type="hidden" name="commande_id" value="<?php echo $commande['commande_id']; ?>">
                                            <input type="hidden" name="nouveau_statut" value="terminée">
                                            <input type="hidden" name="restitution_materiel" value="1">
                                            <button type="submit" class="btn-action btn-accepter">
                                                ✅ Matériel restitué - Terminer
                                            </button>
                                        </form>
                                    
                                    <?php else: ?>
                                        <!-- Statut final -->
                                        <p class="commande-statut-final">
                                            Commande <?php echo $commande['statut']; ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                </div>
                                
                            </div>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>