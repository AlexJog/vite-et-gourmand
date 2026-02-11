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

// Compter les avis en attente
$sql_avis = "SELECT COUNT(*) as nb_avis FROM avis WHERE statut = 'en attente'";
$nb_avis_attente = $pdo->query($sql_avis)->fetch()['nb_avis'];

// Compter les employés
$sql_employes = "SELECT COUNT(*) as nb_employes FROM utilisateur WHERE role_id = 2";
$nb_employes = $pdo->query($sql_employes)->fetch()['nb_employes'];
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <section class="contact-section">
        <div class="container">
            <div class="contact-header">
                <h1>Espace Administrateur</h1>
                <p>Bienvenue <?php echo htmlspecialchars($_SESSION['user_prenom']); ?> ! 👑</p>
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
                
                <!-- Actions principales -->
                <div class="dashboard-actions">
                    
                    <div class="dashboard-card">
                        <h2>🍽️ Gestion des Menus</h2>
                        <p>Créer, modifier et supprimer les menus</p>
                        <a href="gestion-menus.php" class="btn-hero">Gérer les menus</a>
                    </div>
                    
                    <div class="dashboard-card">
                        <h2>📦 Gestion des Commandes</h2>
                        <p>Valider, suivre et gérer toutes les commandes</p>
                        <a href="gestion-commandes.php" class="btn-hero">Gérer les commandes</a>
                    </div>
                    
                    <div class="dashboard-card">
                        <h2>⭐ Gestion des Avis</h2>
                        <p>Valider ou refuser les avis clients</p>
                        <?php if ($nb_avis_attente > 0): ?>
                            <p><strong style="color: #FFC107;">⚠️ <?php echo $nb_avis_attente; ?> avis en attente</strong></p>
                        <?php endif; ?>
                        <a href="gestion-avis.php" class="btn-hero">Gérer les avis</a>
                    </div>
                    
                    <div class="dashboard-card">
                        <h2>👥 Gestion des Employés</h2>
                        <p>Voir et gérer les comptes employés (<?php echo $nb_employes; ?> employés)</p>
                        <a href="gestion-employes.php" class="btn-hero">Gérer les employés</a>
                    </div>
                    
                    <div class="dashboard-card">
                        <h2>📈 Statistiques</h2>
                        <p>Consulter les statistiques de ventes et performances</p>
                        <a href="statistiques.php" class="btn-hero">Voir les statistiques</a>
                    </div>
                    
                    <div class="dashboard-card">
                        <h2>➕ Créer un Employé</h2>
                        <p>Ajouter un nouveau compte employé</p>
                        <a href="creer-employe.php" class="btn-secondary">Créer un employé</a>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>