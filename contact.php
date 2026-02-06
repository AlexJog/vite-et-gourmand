<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<main>

    <?php
    if (isset($_SESSION['succes_contact'])) {
        echo '<div class="alert alert-success">';
        echo '<p>' . htmlspecialchars($_SESSION['succes_contact']) . '</p>';
        echo '</div>';
        unset($_SESSION['succes_contact']);
    }
    
    if (isset($_SESSION['erreurs_contact'])) {
        echo '<div class="alert alert-error">';
        foreach ($_SESSION['erreurs_contact'] as $erreur) {
            echo '<p>• ' . htmlspecialchars($erreur) . '</p>';
        }
        echo '</div>';
        unset($_SESSION['erreurs_contact']);
    }
    ?>

    <!-- Section Contact -->
    <section class="contact-section">
        <div class="container">
            
            <!-- En-tête -->
            <div class="contact-header">
                <h1>Contactez-nous</h1>
                <p>Une question ? Un projet d'événement ? N'hésitez pas à nous écrire !</p>
            </div>
            
            <!-- Grille : Formulaire + Infos -->
            <div class="contact-grid">
                
                <!-- FORMULAIRE DE CONTACT -->
                <div class="contact-form-container">
                    <form action="traitement-contact.php" method="POST" class="contact-form">
                        
                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Votre email *</label>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                placeholder="exemple@email.com"
                                value="<?php echo isset($_SESSION['form_contact']['email']) ? htmlspecialchars($_SESSION['form_contact']['email']) : ''; ?>"
                                required>
                        </div>
                        
                        <!-- Titre du message -->
                        <div class="form-group">
                            <label for="titre">Titre du message *</label>
                            <input 
                                type="text" 
                                id="titre" 
                                name="titre" 
                                placeholder="Objet de votre message"
                                value="<?php echo isset($_SESSION['form_contact']['titre']) ? htmlspecialchars($_SESSION['form_contact']['titre']) : ''; ?>"
                                required>
                        </div>
                        
                        <!-- Message -->
                        <div class="form-group">
                            <label for="message">Votre message *</label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="6"
                                placeholder="Décrivez votre demande..."
                                required>
                            <?php echo isset($_SESSION['form_contact']['message']) ? htmlspecialchars($_SESSION['form_contact']['message']) : ''; ?></textarea>
                        </div>
                        
                        <!-- Bouton Envoyer -->
                        <button type="submit" class="btn-contact">Envoyer le message</button>
                        <?php
                        // Supprimer les données du formulaire après affichage
                        if (isset($_SESSION['form_contact'])) {
                            unset($_SESSION['form_contact']);
                        }
                        ?>
                    </form>
                </div>
                
                <!-- INFORMATIONS DE CONTACT -->
                <div class="contact-info">
                    <div class="info-card">
                        <h3>📧 Email</h3>
                        <p>contact@vitegourmand.fr</p>
                    </div>
                    
                    <div class="info-card">
                        <h3>📞 Téléphone</h3>
                        <p>05 56 00 00 00</p>
                        <small>Lundi - Vendredi : 9h - 18h</small>
                        <small>Samedi : 10h - 16h</small>
                    </div>
                    
                    <div class="info-card">
                        <h3>📍 Adresse</h3>
                        <p>Bordeaux, France</p>
                        <small>Livraison à Bordeaux et périphérie</small>
                    </div>
                </div>
                
            </div>
            
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>