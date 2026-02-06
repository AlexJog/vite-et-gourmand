<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<main>

    <?php
    // Afficher les messages d'erreur s'il y en a
    if (isset($_SESSION['erreurs_inscription'])) {
        echo '<div class="alert alert-error">';
        foreach ($_SESSION['erreurs_inscription'] as $erreur) {
            echo '<p>• ' . htmlspecialchars($erreur) . '</p>';
        }
        echo '</div>';
        unset($_SESSION['erreurs_inscription']);
    }
    ?>

    <!-- Section Inscription -->
    <section class="inscription-section">
        <div class="container">
            
            <div class="inscription-box">
                
                <!-- Titre -->
                <h1>Créer un compte</h1>
                <p class="inscription-subtitle">Rejoignez-nous pour commander facilement</p>
                
                <!-- Formulaire d'inscription -->
                <form action="traitement-inscription.php" method="POST" class="inscription-form">
                    
                    <!-- Nom et Prénom -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input 
                                type="text" 
                                id="nom" 
                                name="nom" 
                                placeholder="Dupont"
                                value="<?php echo isset($_SESSION['form_data']['nom']) ? htmlspecialchars($_SESSION['form_data']['nom']) : ''; ?>"
                                required
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input 
                                type="text" 
                                id="prenom" 
                                name="prenom" 
                                placeholder="Jean"
                                value="<?php echo isset($_SESSION['form_data']['prenom']) ? htmlspecialchars($_SESSION['form_data']['prenom']) : ''; ?>"
                                required
                            >
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Adresse email *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="exemple@email.com"
                            value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>"
                            required
                        >
                    </div>
                    
                    <!-- Téléphone -->
                    <div class="form-group">
                        <label for="telephone">Téléphone *</label>
                        <input 
                            type="tel" 
                            id="telephone" 
                            name="telephone" 
                            placeholder="06 12 34 56 78"
                            value="<?php echo isset($_SESSION['form_data']['telephone']) ? htmlspecialchars($_SESSION['form_data']['telephone']) : ''; ?>"
                            required
                        >
                    </div>
                    
                    <!-- Adresse postale -->
                    <div class="form-group">
                        <label for="adresse">Adresse postale *</label>
                        <input 
                            type="text" 
                            id="adresse" 
                            name="adresse" 
                            placeholder="12 rue de la République"
                            value="<?php echo isset($_SESSION['form_data']['adresse']) ? htmlspecialchars($_SESSION['form_data']['adresse']) : ''; ?>"
                            required
                        >
                    </div>
                    
                    <!-- Ville et Code postal -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="code_postal">Code postal *</label>
                            <input 
                                type="text" 
                                id="code_postal" 
                                name="code_postal" 
                                placeholder="33000"
                                value="<?php echo isset($_SESSION['form_data']['code_postal']) ? htmlspecialchars($_SESSION['form_data']['code_postal']) : ''; ?>"
                                required
                            >
                        </div>
                        
                        <div class="form-group">
                            <label for="ville">Ville *</label>
                            <input 
                                type="text" 
                                id="ville" 
                                name="ville" 
                                placeholder="Bordeaux"
                                value="<?php echo isset($_SESSION['form_data']['ville']) ? htmlspecialchars($_SESSION['form_data']['ville']) : ''; ?>"
                                required
                            >
                        </div>
                    </div>
                    
                    <!-- Mot de passe -->
                    <div class="form-group">
                        <label for="password">Mot de passe *</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••"
                            required
                        >
                        <small class="form-hint">10 caractères minimum : 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial (@#$%&*!?.,;:_-)</small>
                    </div>
                    
                    <!-- Confirmation mot de passe -->
                    <div class="form-group">
                        <label for="password_confirm">Confirmer le mot de passe *</label>
                        <input 
                            type="password" 
                            id="password_confirm" 
                            name="password_confirm" 
                            placeholder="••••••••"
                            minlength="10"
                            required
                        >
                    </div>
                    
                    <!-- Bouton d'inscription -->
                    <button type="submit" class="btn-inscription">Créer mon compte</button>

                    <?php
                    // Supprimer les anciennes données du formulaire après affichage
                    if (isset($_SESSION['form_data'])) {
                        unset($_SESSION['form_data']);
                    }
                    ?>
                    
                </form>
                
                <!-- Lien vers connexion -->
                <div class="connexion-link">
                    <p>Vous avez déjà un compte ? 
                        <a href="connexion.php">Se connecter</a>
                    </p>
                </div>
                
            </div>
            
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>