<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<?php
// Afficher le message de succès de réinitialisation
if (isset($_SESSION['succes_connexion'])) {
    echo '<div class="alert alert-success">';
    echo '<p>' . htmlspecialchars($_SESSION['succes_connexion']) . '</p>';
    echo '</div>';
    unset($_SESSION['succes_connexion']);
}
?>

<main>

    <?php
    if (isset($_SESSION['succes_inscription'])) {
        echo '<div class="alert alert-success">';
        echo '<p>' . htmlspecialchars($_SESSION['succes_inscription']) . '</p>';
        echo '</div>';
        unset($_SESSION['succes_inscription']);
    }
    
    if (isset($_SESSION['erreurs_connexion'])) {
        echo '<div class="alert alert-error">';
        foreach ($_SESSION['erreurs_connexion'] as $erreur) {
            echo '<p>• ' . htmlspecialchars($erreur) . '</p>';
        }
        echo '</div>';
        unset($_SESSION['erreurs_connexion']);
    }
    ?>

    <!-- Section Connexion -->
    <section class="connexion-section">
        <div class="container">
            
            <div class="connexion-box">
                
                <!-- Titre -->
                <h1>Connexion</h1>
                <p class="connexion-subtitle">Accédez à votre espace personnel</p>
                
                <!-- Formulaire de connexion -->
                <form action="traitement-connexion.php" method="POST" class="connexion-form">
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Adresse email</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="exemple@email.com"
                            value="<?php echo isset($_SESSION['form_email']) ? htmlspecialchars($_SESSION['form_email']) : ''; ?>"
                            required
                        >
                    </div>
                    
                    <!-- Mot de passe -->
                    <div class="form-group">
                        <label for="password">Mot de passe</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                        >
                    </div>
                    
                    <!-- Mot de passe oublié -->
                    <div class="form-links">
                        <a href="mot-de-passe-oublie.php" class="forgot-password">Mot de passe oublié ?</a>
                    </div>
                    
                    <!-- Bouton de connexion -->
                    <button type="submit" class="btn-inscription">Se connecter</button>

                    <!-- Lien mot de passe oublié -->
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="mot-de-passe-oublie.php" style="color: var(--primary-color); text-decoration: underline; font-size: 14px;">
                            Mot de passe oublié ?
                        </a>
                    </div>
                    
                </form>
                
                <!-- inscription -->
                <div class="inscription-link">
                    <p>Vous n'avez pas encore de compte ? 
                        <a href="inscription.php">Créer un compte</a>
                    </p>
                </div>
                
            </div>
            
        </div>
    </section>
    <?php
    // Supprimer le mail du formulaire apres affichage
    if (isset($_SESSION['form_email'])) {
        unset($_SESSION['form_email']);
    }
    ?>

</main>

<?php require_once 'includes/footer.php'; ?>