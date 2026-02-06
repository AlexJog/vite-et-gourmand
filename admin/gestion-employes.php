<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Récupérer tous les employés
$sql = "SELECT u.*, r.libelle AS role_nom
        FROM utilisateur u
        INNER JOIN role r ON u.role_id = r.role_id
        WHERE r.libelle = 'employe'
        ORDER BY u.nom ASC";

$stmt = $pdo->query($sql);
$employes = $stmt->fetchAll();
?>
<?php require_once '../includes/header.php'; ?>

<main>

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

    <section class="contact-section">
        <div class="container">
            
            <div class="contact-header">
                <h1>Gestion des employés</h1>
                <p>Gérez les comptes employés de votre entreprise</p>
            </div>
            
            <?php if (empty($employes)): ?>
                <!-- Aucun employé -->
                <div class="message-aucune-commande">
                    <p>Aucun employé enregistré.</p>
                    <a href="creer-employe.php" class="btn-hero">Créer un employé</a>
                </div>
            <?php else: ?>
                <!-- Liste des employés -->
                <div class="liste-commandes">
                    <?php foreach ($employes as $employe): ?>
                        <div class="commande-carte">
                            
                            <!-- En-tête employé -->
                            <div class="commande-header">
                                <div>
                                    <h3 class="commande-titre">
                                        <?php echo htmlspecialchars($employe['prenom'] . ' ' . $employe['nom']); ?>
                                    </h3>
                                    <p class="commande-date">
                                        <?php echo htmlspecialchars($employe['email']); ?>
                                    </p>
                                </div>
                                
                                <!-- Badge statut -->
                                <?php if ($employe['actif']): ?>
                                    <div class="badge-statut" style="background-color: #28A745;">
                                        ✅ Compte actif
                                    </div>
                                <?php else: ?>
                                    <div class="badge-statut" style="background-color: #DC3545;">
                                        ❌ Compte désactivé
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Détails employé -->
                            <div class="commande-details">
                                <div>
                                    <p>
                                        <strong>📞 Téléphone :</strong><br>
                                        <?php echo htmlspecialchars($employe['telephone']); ?>
                                    </p>
                                </div>
                                
                                <div>
                                    <p>
                                        <strong>📅 Créé le :</strong><br>
                                        <?php echo isset($employe['date_creation']) ? date('d/m/Y', strtotime($employe['date_creation'])) : 'N/A'; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="commande-avis">
                                <?php if ($employe['actif']): ?>
                                    <!-- Désactiver le compte -->
                                    <form method="POST" action="modifier-employe.php" class="employe-action-form">
                                        <input type="hidden" name="employe_id" value="<?php echo $employe['utilisateur_id']; ?>">
                                        <input type="hidden" name="action" value="desactiver">
                                        <button type="submit" class="btn-secondary" onclick="return confirm('Voulez-vous vraiment désactiver ce compte ?');">
                                            🔒 Désactiver le compte
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <!-- Réactiver le compte -->
                                    <form method="POST" action="modifier-employe.php" class="employe-action-form">
                                        <input type="hidden" name="employe_id" value="<?php echo $employe['utilisateur_id']; ?>">
                                        <input type="hidden" name="action" value="activer">
                                        <button type="submit" class="btn-laisser-avis" onclick="return confirm('Voulez-vous vraiment réactiver ce compte ?');">
                                            🔓 Réactiver le compte
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Bouton créer employé -->
                <div class="btn-creer-employe-container">
                    <a href="creer-employe.php" class="btn-hero">➕ Créer un nouvel employé</a>
                </div>
            <?php endif; ?>
            
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>