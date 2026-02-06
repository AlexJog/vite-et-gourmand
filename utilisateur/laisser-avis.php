<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un utilisateur
if ($_SESSION['user_role'] !== 'utilisateur') {
    header('Location: ../index.php');
    exit;
}

// Vérifier que l'ID de commande est dans l'URL
if (!isset($_GET['commande_id']) || empty($_GET['commande_id'])) {
    header('Location: mes-commandes.php');
    exit;
}

$commande_id = (int)$_GET['commande_id'];
$utilisateur_id = $_SESSION['user_id'];

// Vérifier que la commande appartient bien à l'utilisateur et est terminée
$sql = "SELECT c.*, m.nom AS menu_nom 
        FROM commande c
        INNER JOIN menu m ON c.menu_id = m.menu_id
        WHERE c.commande_id = :commande_id 
        AND c.utilisateur_id = :utilisateur_id 
        AND c.statut = 'terminée'";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    'commande_id' => $commande_id,
    'utilisateur_id' => $utilisateur_id
]);

$commande = $stmt->fetch();

// Si la commande n'existe pas ou n'est pas terminée
if (!$commande) {
    $_SESSION['error_user'] = "Cette commande n'est pas disponible pour laisser un avis.";
    header('Location: mes-commandes.php');
    exit;
}

// Vérifier si un avis existe déjà pour cette commande
$sql_check = "SELECT avis_id FROM avis WHERE commande_id = :commande_id AND utilisateur_id = :utilisateur_id";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([
    'commande_id' => $commande_id,
    'utilisateur_id' => $utilisateur_id
]);

if ($stmt_check->fetch()) {
    $_SESSION['error_user'] = "Vous avez déjà laissé un avis pour cette commande.";
    header('Location: mes-commandes.php');
    exit;
}
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <section class="contact-section">
        <div class="container">
            
            <?php
            // Afficher les erreurs
            if (isset($_SESSION['erreurs_avis'])) {
                echo '<div class="alert alert-error">';
                foreach ($_SESSION['erreurs_avis'] as $erreur) {
                    echo '<p>• ' . htmlspecialchars($erreur) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['erreurs_avis']);
            }
            ?>
            
            <div class="contact-header">
                <h1>Laisser un avis</h1>
                <p>Partagez votre expérience pour la commande : <?php echo htmlspecialchars($commande['menu_nom']); ?></p>
            </div>
            
            <!-- Formulaire d'avis -->
            <div class="contact-form-container">
                
                <form action="traitement-avis.php" method="POST" class="contact-form">
                    
                    <input type="hidden" name="commande_id" value="<?php echo $commande_id; ?>">
                    
                    <!-- Note -->
                    <div class="form-group">
                        <label>Votre note *</label>
                        <div class="etoiles-container">
                            <input type="radio" name="note" value="1" id="note1" required>
                            <label for="note1" class="star" data-value="1">★</label>
                            
                            <input type="radio" name="note" value="2" id="note2" required>
                            <label for="note2" class="star" data-value="2">★</label>
                            
                            <input type="radio" name="note" value="3" id="note3" required>
                            <label for="note3" class="star" data-value="3">★</label>
                            
                            <input type="radio" name="note" value="4" id="note4" required>
                            <label for="note4" class="star" data-value="4">★</label>
                            
                            <input type="radio" name="note" value="5" id="note5" required>
                            <label for="note5" class="star" data-value="5">★</label>
                        </div>
                    </div>
                    
                    <!-- Commentaire -->
                    <div class="form-group">
                        <label for="commentaire">Votre commentaire *</label>
                        <textarea id="commentaire" name="commentaire" rows="6" required placeholder="Partagez votre expérience..."></textarea>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="form-buttons">
                        <button type="submit" class="btn-contact">✅ Envoyer mon avis</button>
                        <a href="mes-commandes.php" class="btn-secondary">❌ Annuler</a>
                    </div>
                    
                </form>
                
            </div>
            
        </div>
    </section>
</main>

<!-- Script étoiles -->
<script src="../assets/js/etoiles.js"></script>

<?php require_once '../includes/footer.php'; ?>