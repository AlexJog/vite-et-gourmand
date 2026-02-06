<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un employé ou admin
if ($_SESSION['user_role'] !== 'employe' && $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Compter les commandes en attente
$sql_count = "SELECT COUNT(*) as nb FROM commande WHERE statut = 'en attente'";
$nb_attente = $pdo->query($sql_count)->fetch()['nb'];
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <section class="contact-section">
        <div class="container">
            <div class="contact-header">
                <h1>Espace Employé</h1>
                <p>Bienvenue <?php echo htmlspecialchars($_SESSION['user_prenom']); ?> !</p>
            </div>
            
            <div class="dashboard-content">
                
                <!-- Carte Gestion Commandes -->
                <div class="dashboard-card">
                    <h2>📦 Gestion des Commandes</h2>
                    <p>
                        <strong class="dashboard-stat-highlight"><?php echo $nb_attente; ?></strong> commande<?php echo $nb_attente > 1 ? 's' : ''; ?> en attente
                    </p>
                    <a href="gestion-commandes.php" class="btn-hero">Gérer les commandes</a>
                </div>
                
            </div>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>