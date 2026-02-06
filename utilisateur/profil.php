<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un utilisateur
if ($_SESSION['user_role'] !== 'utilisateur') {
    header('Location: ../index.php');
    exit;
}

// Récupérer les infos actuelles de l'utilisateur
$sql = "SELECT * FROM utilisateur WHERE utilisateur_id = :utilisateur_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['utilisateur_id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: dashboard.php');
    exit;
}
?>
<?php require_once '../includes/header.php'; ?>

<main>
    <section class="contact-section">
        <div class="container">
            
            <?php
            if (isset($_SESSION['erreurs_profil'])) {
                echo '<div class="alert alert-error">';
                foreach ($_SESSION['erreurs_profil'] as $erreur) {
                    echo '<p>• ' . htmlspecialchars($erreur) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['erreurs_profil']);
            }
            
            if (isset($_SESSION['succes_profil'])) {
                echo '<div class="alert alert-success">';
                echo '<p>' . htmlspecialchars($_SESSION['succes_profil']) . '</p>';
                echo '</div>';
                unset($_SESSION['succes_profil']);
            }
            ?>
            
            <div class="contact-header">
                <h1>Mon profil</h1>
                <p>Modifiez vos informations personnelles</p>
            </div>
            
            <!-- Formulaire de modification -->
            <div class="contact-form-container">
                
                <form action="traitement-profil.php" method="POST" class="contact-form">
                    
                    <h3 class="form-section-titre">Informations personnelles</h3>
                    
                    <!-- Nom et Prénom -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    
                    <!-- Téléphone -->
                    <div class="form-group">
                        <label for="telephone">Téléphone *</label>
                        <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($user['telephone']); ?>" required>
                    </div>
                    
                    <!-- Adresse -->
                    <div class="form-group">
                        <label for="adresse_postale">Adresse postale *</label>
                        <input type="text" id="adresse_postale" name="adresse_postale" value="<?php echo htmlspecialchars($user['adresse_postale']); ?>" required>
                    </div>
                    
                    <!-- Code postal et Ville -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="code_postal">Code postal *</label>
                            <input type="text" id="code_postal" name="code_postal" value="<?php echo htmlspecialchars($user['code_postal']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="ville">Ville *</label>
                            <input type="text" id="ville" name="ville" value="<?php echo htmlspecialchars($user['ville']); ?>" required>
                        </div>
                    </div>
                    
                    <!-- Pays -->
                    <div class="form-group">
                        <label for="pays">Pays *</label>
                        <input type="text" id="pays" name="pays" value="<?php echo htmlspecialchars($user['pays']); ?>" required>
                    </div>
                    
                    <hr class="form-separator">
                    
                    <h3 class="form-section-titre">Changer mon mot de passe</h3>
                    <p class="form-note">
                        Laissez vide si vous ne souhaitez pas modifier votre mot de passe
                    </p>
                    
                    <!-- Mot de passe actuel -->
                    <div class="form-group">
                        <label for="password_actuel">Mot de passe actuel</label>
                        <input type="password" id="password_actuel" name="password_actuel" placeholder="Requis pour changer de mot de passe">
                    </div>
                    
                    <!-- Nouveau mot de passe -->
                    <div class="form-group">
                        <label for="nouveau_password">Nouveau mot de passe</label>
                        <input type="password" id="nouveau_password" name="nouveau_password" placeholder="10 caractères minimum">
                        <small class="form-hint">10 caractères minimum : 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial (@#$%&*!?.,;:_-)</small>
                    </div>
                    
                    <!-- Confirmer nouveau mot de passe -->
                    <div class="form-group">
                        <label for="nouveau_password_confirm">Confirmer le nouveau mot de passe</label>
                        <input type="password" id="nouveau_password_confirm" name="nouveau_password_confirm" placeholder="Retapez le nouveau mot de passe">
                    </div>
                    
                    <!-- Boutons -->
                    <div class="form-buttons">
                        <button type="submit" class="btn-contact">✅ Enregistrer les modifications</button>
                        <a href="dashboard.php" class="btn-secondary">❌ Annuler</a>
                    </div>
                    
                </form>
                
            </div>
            
        </div>
    </section>
</main>

<?php require_once '../includes/footer.php'; ?>