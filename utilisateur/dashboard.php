<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un utilisateur
if ($_SESSION['user_role'] !== 'utilisateur') {
    header('Location: ../index.php');
    exit;
}

// Compter le nombre de commandes
$sql_count = "SELECT COUNT(*) as nb_commandes FROM commande WHERE utilisateur_id = :utilisateur_id";
$stmt_count = $pdo->prepare($sql_count);
$stmt_count->execute(['utilisateur_id' => $_SESSION['user_id']]);
$nb_commandes = $stmt_count->fetch()['nb_commandes'];
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <section class="contact-section">
        <div class="container">
            <div class="contact-header">
                <h1>Mon espace personnel</h1>
                <p>Bienvenue <?php echo htmlspecialchars($_SESSION['user_prenom']); ?> !</p>
            </div>
            
            <div class="dashboard-content">
                
                <!-- Cartes Mes Commandes -->
                <div class="dashboard-card">
                    <h2>📋 Mes Commandes</h2>
                    <p>
                        Vous avez <strong><?php echo $nb_commandes; ?></strong> commande<?php echo $nb_commandes > 1 ? 's' : ''; ?>
                    </p>
                    <a href="mes-commandes.php" class="btn-hero">Voir mes commandes</a>
                </div>
                
                <!-- Actions rapides -->
                <div class="dashboard-actions">
                    <a href="profil.php" class="btn-secondary">✏️ Modifier mon profil</a>
                    <a href="../menus.php" class="btn-secondary">🍽️ Découvrir nos menus</a>
                    <a href="../contact.php" class="btn-secondary">📧 Nous contacter</a>
                </div>
                
            </div>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>