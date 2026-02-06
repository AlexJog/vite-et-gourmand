<?php require_once 'includes/config.php'; ?>

<?php
// Vérifier que le token est dans l'URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $_SESSION['error'] = "Lien invalide.";
    header('Location: connexion.php');
    exit;
}

$token = $_GET['token'];

// Vérifier que le token existe et n'a pas expiré
$sql = "SELECT utilisateur_id, prenom, reset_token_expire 
        FROM utilisateur 
        WHERE reset_token = :token";

$stmt = $pdo->prepare($sql);
$stmt->execute(['token' => $token]);
$user = $stmt->fetch();

// Si le token n'existe pas
if (!$user) {
    $_SESSION['error'] = "Ce lien de réinitialisation n'est pas valide.";
    header('Location: connexion.php');
    exit;
}

// Vérifier l'expiration
if (strtotime($user['reset_token_expire']) < time()) {
    $_SESSION['error'] = "Ce lien de réinitialisation a expiré. Veuillez en demander un nouveau.";
    header('Location: mot-de-passe-oublie.php');
    exit;
}
?>

<?php require_once 'includes/header.php'; ?>

<main>
    <section class="inscription-section">
        <div class="container">
            
            <?php
            // Afficher les erreurs
            if (isset($_SESSION['erreurs_nouveau_mdp'])) {
                echo '<div class="alert alert-error">';
                foreach ($_SESSION['erreurs_nouveau_mdp'] as $erreur) {
                    echo '<p>• ' . htmlspecialchars($erreur) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['erreurs_nouveau_mdp']);
            }
            ?>
            
            <div class="inscription-box">
                
                <h1>Nouveau mot de passe</h1>
                <p class="inscription-subtitle">Bonjour <?php echo htmlspecialchars($user['prenom']); ?>, définissez votre nouveau mot de passe</p>
                
                <form action="traitement-nouveau-mdp.php" method="POST" class="inscription-form">
                    
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label for="password">Nouveau mot de passe *</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                        >
                        <small class="form-hint">10 caractères minimum : 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial (@#$%&*!?.,;:_-)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="password_confirm">Confirmer le mot de passe *</label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn-inscription">Réinitialiser mon mot de passe</button>
                    
                </form>
                
            </div>
            
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>