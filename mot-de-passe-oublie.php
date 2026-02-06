<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<main>
    <section class="inscription-section">
        <div class="container">
            
            <?php
            // Afficher les erreurs
            if (isset($_SESSION['erreurs_reset'])) {
                echo '<div class="alert alert-error">';
                foreach ($_SESSION['erreurs_reset'] as $erreur) {
                    echo '<p>• ' . htmlspecialchars($erreur) . '</p>';
                }
                echo '</div>';
                unset($_SESSION['erreurs_reset']);
            }
            
            // Afficher le succès
            if (isset($_SESSION['succes_reset'])) {
                echo '<div class="alert alert-success">';
                echo '<p>' . htmlspecialchars($_SESSION['succes_reset']) . '</p>';
                echo '</div>';
                unset($_SESSION['succes_reset']);
            }
            ?>
            
            <div class="inscription-box">
                
                <h1>Mot de passe oublié</h1>
                <p class="inscription-subtitle">Entrez votre adresse email pour réinitialiser votre mot de passe</p>
                
                <form action="traitement-reset.php" method="POST" class="inscription-form">
                    
                    <div class="form-group">
                        <label for="email">Adresse email *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="exemple@email.com"
                            required
                        >
                    </div>
                    
                    <button type="submit" class="btn-inscription">Envoyer le lien de réinitialisation</button>
                    
                </form>
                
                <div class="connexion-link">
                    <p>Vous vous souvenez de votre mot de passe ? 
                        <a href="connexion.php">Se connecter</a>
                    </p>
                </div>
                
            </div>
            
        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>