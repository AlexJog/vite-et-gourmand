<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un utilisateur
if ($_SESSION['user_role'] !== 'utilisateur') {
    header('Location: ../index.php');
    exit;
}

// Récupérer toutes les commandes de l'utilisateur connecté
$sql = "SELECT c.*, m.nom AS menu_nom, m.prix_par_personne
        FROM commande c
        INNER JOIN menu m ON c.menu_id = m.menu_id
        WHERE c.utilisateur_id = :utilisateur_id
        ORDER BY c.date_commande DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['utilisateur_id' => $_SESSION['user_id']]);
$commandes = $stmt->fetchAll();
?>
<?php require_once '../includes/header.php'; ?>

<main>

    <?php
    if (isset($_SESSION['succes_user'])) {
        echo '<div class="alert alert-success">';
        echo '<p>' . htmlspecialchars($_SESSION['succes_user']) . '</p>';
        echo '</div>';
        unset($_SESSION['succes_user']);
    }

    if (isset($_SESSION['error_user'])) {
        echo '<div class="alert alert-error">';
        echo '<p>' . htmlspecialchars($_SESSION['error_user']) . '</p>';
        echo '</div>';
        unset($_SESSION['error_user']);
    }
    ?>

    <section class="contact-section">
        <div class="container">
            
            <div class="contact-header">
                <h1>Mes commandes</h1>
                <p>Retrouvez l'historique de toutes vos commandes</p>
            </div>
            
            <?php if (empty($commandes)): ?>
                <!-- Aucune commande -->
                <div class="message-aucune-commande">
                    <p>Vous n'avez pas encore passé de commande.</p>
                    <a href="../menus.php" class="btn-hero">Découvrir nos menus</a>
                </div>
            <?php else: ?>
                <!-- Liste des commandes -->
                <div class="liste-commandes">
                    <?php foreach ($commandes as $commande): ?>
                        <div class="commande-carte">
                            
                            <!-- En-tête de la commande -->
                            <div class="commande-header">
                                <div>
                                    <h3 class="commande-titre">
                                        <?php echo htmlspecialchars($commande['menu_nom']); ?>
                                    </h3>
                                    <p class="commande-date">
                                        Commandé le <?php echo date('d/m/Y à H:i', strtotime($commande['date_commande'])); ?>
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
                            
                            <!-- Détails de la commande -->
                            <div class="commande-details">
                                <div>
                                    <p>
                                        <strong>📅 Date de prestation :</strong><br>
                                        <?php echo date('d/m/Y', strtotime($commande['date_prestation'])); ?>
                                    </p>
                                    <p>
                                        <strong>🕐 Heure de livraison :</strong><br>
                                        <?php echo date('H:i', strtotime($commande['heure_livraison'])); ?>
                                    </p>
                                    <p>
                                        <strong>👥 Nombre de personnes :</strong><br>
                                        <?php echo $commande['nombre_personnes']; ?> personnes
                                    </p>
                                </div>
                                
                                <div>
                                    <p>
                                        <strong>📍 Adresse de livraison :</strong><br>
                                        <?php echo htmlspecialchars($commande['adresse_livraison']); ?><br>
                                        <?php echo htmlspecialchars($commande['code_postal']); ?> <?php echo htmlspecialchars($commande['ville']); ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Commentaire -->
                            <?php if (!empty($commande['commentaire'])): ?>
                                <div class="commande-commentaire">
                                    <p>
                                        <strong>💬 Commentaire :</strong><br>
                                        <em><?php echo nl2br(htmlspecialchars($commande['commentaire'])); ?></em>
                                    </p>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Prix total -->
                            <div class="commande-prix">
                                <p>
                                    Total : <?php echo number_format($commande['prix_total'], 2, ',', ' '); ?>€
                                </p>
                            </div>
                            
                            <!-- Bouton Laisser un avis -->
                            <?php if ($commande['statut'] === 'terminée'): ?>
                                <?php
                                // Vérifier si un avis existe déjà
                                $sql_avis = "SELECT avis_id FROM avis WHERE commande_id = :commande_id AND utilisateur_id = :utilisateur_id";
                                $stmt_avis = $pdo->prepare($sql_avis);
                                $stmt_avis->execute([
                                    'commande_id' => $commande['commande_id'],
                                    'utilisateur_id' => $_SESSION['user_id']
                                ]);
                                $avis_existe = $stmt_avis->fetch();
                                ?>
                                
                                <?php if (!$avis_existe): ?>
                                    <div class="commande-avis">
                                        <a href="laisser-avis.php?commande_id=<?php echo $commande['commande_id']; ?>" class="btn-laisser-avis">
                                            ⭐ Laisser un avis
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="commande-avis">
                                        <p class="message-avis-deja-laisse">✅ Vous avez laissé un avis pour cette commande</p>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>