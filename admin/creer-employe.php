<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <section class="contact-section">
        <div class="container">
            
            <?php
            if (isset($_SESSION['erreurs_employe'])) {
                echo '<div class="alert alert-error">';
                foreach ($_SESSION['erreurs_employe'] as $erreur) {
                    echo '<p>• ' . htmlspecialchars($erreur) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['erreurs_employe']);
            }
            
            if (isset($_SESSION['succes_admin'])) {
                echo '<div class="alert alert-success">';
                echo '<p>' . htmlspecialchars($_SESSION['succes_admin']) . '</p>';
                echo '</div>';
                unset($_SESSION['succes_admin']);
            }
            ?>
            
            <div class="contact-header">
                <h1>Créer un compte employé</h1>
                <p>Ajoutez un nouveau membre à votre équipe</p>
            </div>
            
            <div class="contact-form-container">
                
                <form action="traitement-employe.php" method="POST" class="contact-form">
                    
                    <!-- Nom et Prénom -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" required>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                        <small class="form-hint">Cet email servira d'identifiant de connexion</small>
                    </div>
                    
                    <!-- Téléphone -->
                    <div class="form-group">
                        <label for="telephone">Téléphone *</label>
                        <input type="tel" id="telephone" name="telephone" required>
                    </div>
                    
                    <!-- Mot de passe -->
                    <div class="form-group">
                        <label for="password">Mot de passe *</label>
                        <input type="password" id="password" name="password" required>
                        <small class="form-hint">10 caractères minimum : 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial (@#$%&*!?.,;:_-)</small>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="form-buttons">
                        <button type="submit" class="btn-contact">✅ Créer le compte employé</button>
                        <a href="dashboard.php" class="btn-secondary">❌ Annuler</a>
                    </div>
                    
                </form>
                
            </div>
            
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>