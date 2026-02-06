<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Compter le nombre de menus
$sql_count = "SELECT COUNT(*) as nb_menus FROM menu";
$nb_menus = $pdo->query($sql_count)->fetch()['nb_menus'];

// Compter le nombre de commandes
$sql_commandes = "SELECT COUNT(*) as nb_commandes FROM commande";
$nb_commandes = $pdo->query($sql_commandes)->fetch()['nb_commandes'];

// Compter le nombre d'utilisateurs
$sql_users = "SELECT COUNT(*) as nb_users FROM utilisateur WHERE role_id = 3";
$nb_users = $pdo->query($sql_users)->fetch()['nb_users'];
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <section class="contact-section">
        <div class="container">
            <div class="contact-header">
                <h1>Espace Administrateur</h1>
                <p>Bienvenue <?php echo htmlspecialchars($_SESSION['user_prenom']); ?> !</p>
            </div>
            
            <div class="dashboard-content">
                
                <!-- Statistiques -->
                <div class="admin-stats-grid">
                    
                    <div class="dashboard-card admin-stat-card">
                        <h3>📋 Menus</h3>
                        <p class="admin-stat-number admin-stat-menus">
                            <?php echo $nb_menus; ?>
                        </p>
                    </div>
                    
                    <div class="dashboard-card admin-stat-card">
                        <h3>📦 Commandes</h3>
                        <p class="admin-stat-number admin-stat-commandes">
                            <?php echo $nb_commandes; ?>
                        </p>
                    </div>
                    
                    <div class="dashboard-card admin-stat-card">
                        <h3>👥 Utilisateurs</h3>
                        <p class="admin-stat-number admin-stat-users">
                            <?php echo $nb_users; ?>
                        </p>
                    </div>
                    
                </div>
                
                <!-- Actions -->
                <div class="dashboard-card">
                    <h2>📋 Gestion des Menus</h2>
                    <p>Créer, modifier et supprimer les menus</p>
                    <a href="gestion-menus.php" class="btn-hero">Gérer les menus</a>
                </div>
                
            </div>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>