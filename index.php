<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>
<main>

    <?php
    // Afficher le message de succès de commande
    if (isset($_SESSION['succes_commande'])) {
        echo '<div class="alert alert-success">';
        echo '<p>' . htmlspecialchars($_SESSION['succes_commande']) . '</p>';
        echo '</div>';
        unset($_SESSION['succes_commande']);
    }
    
    // Afficher les erreurs
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-error">';
        echo '<p>' . htmlspecialchars($_SESSION['error']) . '</p>';
        echo '</div>';
        unset($_SESSION['error']);
    }
    
    // Message de bienvenue (UNIQUEMENT première connexion)
    if (isset($_SESSION['first_login']) && $_SESSION['first_login'] === true) {
        echo '<div class="message-bienvenue">';
        echo '<h2>Bienvenue ' . htmlspecialchars($_SESSION['user_prenom']) . ' !</h2>';
        echo '<p>Nous sommes ravis de vous revoir sur Vite & Gourmand.</p>';
        echo '</div>';
        
        unset($_SESSION['first_login']);
    }
    ?>

    <!-- Hero -->
    <section class="hero">
        <div class="div-hero">
            <h1>Des saveurs authentiques pour vos événements</h1>
            <p>Depuis 25 ans, nous mettons notre savoir-faire à votre service.</p>
            <a href="menus.php" class="btn-hero">Découvrir nos menus</a>
        </div>
    </section>

    <!-- Présentation -->
    <section class="presentation">
        <div class="presentation-image">
            <img src="assets/images/julie-jose.jpg" alt="Julie et José, fondateurs de Vite & Gourmand">
        </div>
        <div class="presentation-text">
            <h2>Une entreprise familiale depuis 25 ans</h2>
            <p>Julie et José mettent leur passion et leur expertise au service de vos événements.
                Spécialistes du traiteur à Bordeaux, nous proposons des menus variés et de qualité
                pour toutes vos occasions.</p> 
        </div>
    </section>

    <!-- Pourquoi nous choisir -->
    <section class="experience">
        <div class="div-experience">
            <h2>Pourquoi nous choisir ?</h2>
            <div class="div-grille">
                <article class="carte">
                    <span class="emoji">⭐</span>
                    <h3>25 ans d'expérience</h3>
                    <p>Un savoir-faire reconnu à Bordeaux.</p>
                </article>

                <article class="carte">
                    <span class="emoji">🥗</span>
                    <h3>Produits de qualité</h3>
                    <p>Sélection rigoureuse des ingrédients.</p>
                </article>

                <article class="carte">
                    <span class="emoji">🤝</span>
                    <h3>Service personnalisé</h3>
                    <p>À l'écoute de vos besoins.</p>
                </article>
            </div>    
        </div>
    </section>

    <!-- Avis clients -->
    <section class="avis-section">
        <div class="container">
            
            <div class="section-header">
                <h2>Ce que nos clients disent de nous</h2>
                <p>Découvrez les témoignages de nos clients satisfaits</p>
            </div>
            
            <?php
            // Récupérer les 6 derniers avis validés
            $sql_avis = "SELECT a.*, u.prenom, u.nom 
                         FROM avis a
                         INNER JOIN utilisateur u ON a.utilisateur_id = u.utilisateur_id
                         WHERE a.statut = 'validé'
                         ORDER BY a.date_avis DESC
                         LIMIT 6";
            
            $stmt_avis = $pdo->query($sql_avis);
            $avis_valides = $stmt_avis->fetchAll();
            ?>
            
            <?php if (empty($avis_valides)): ?>
                <!-- Avis par défaut si aucun avis validé -->
                <div class="div-avis">
                    <article class="avis-carte">
                        <div class="avis-etoiles">
                            <span class="etoile-pleine">★★★★★</span>
                        </div>
                        <p class="avis-commentaire">"Excellent service ! Les plats étaient délicieux et la présentation soignée."</p>
                        <p class="avis-auteur">— Sabino</p>
                    </article>

                    <article class="avis-carte">
                        <div class="avis-etoiles">
                            <span class="etoile-pleine">★★★★★</span>
                        </div>
                        <p class="avis-commentaire">"Très professionnels, à l'écoute. Je recommande vivement pour vos événements."</p>
                        <p class="avis-auteur">— Pierre</p>
                    </article>

                    <article class="avis-carte">
                        <div class="avis-etoiles">
                            <span class="etoile-pleine">★★★★★</span>
                        </div>
                        <p class="avis-commentaire">"Une qualité irréprochable, nos invités ont adoré. Merci !"</p>
                        <p class="avis-auteur">— Rosine</p>
                    </article>
                </div>
            <?php else: ?>
                <!-- Avis de la BDD -->
                <div class="avis-grid">
                    <?php foreach ($avis_valides as $item): ?>
                        <div class="avis-carte">
                            
                            <!-- Étoiles -->
                            <div class="avis-etoiles">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $item['note']) {
                                        echo '<span class="etoile-pleine">★</span>';
                                    } else {
                                        echo '<span class="etoile-vide">☆</span>';
                                    }
                                }
                                ?>
                            </div>
                            
                            <!-- Commentaire -->
                            <p class="avis-commentaire">
                                "<?php echo htmlspecialchars($item['commentaire']); ?>"
                            </p>
                            
                            <!-- Auteur -->
                            <p class="avis-auteur">
                                — <?php echo htmlspecialchars($item['prenom'] . ' ' . substr($item['nom'], 0, 1) . '.'); ?>
                            </p>
                            
                            <!-- Date -->
                            <p class="avis-date">
                                <?php echo date('d/m/Y', strtotime($item['date_avis'])); ?>
                            </p>
                            
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>