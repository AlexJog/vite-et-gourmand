<?php
require_once 'includes/config.php';
require_once 'includes/check-auth.php';

// Vérifier que l'ID du menu est dans l'URL
if (!isset($_GET['menu']) || empty($_GET['menu'])) {
    header('Location: menus.php');
    exit;
}

$menu_id = (int)$_GET['menu'];

// Récupérer les infos du menu
$sql = "SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom 
        FROM menu m
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        LEFT JOIN theme t ON m.theme_id = t.theme_id
        WHERE m.menu_id = :menu_id";

$stmt = $pdo->prepare($sql);
$stmt->execute(['menu_id' => $menu_id]);
$menu = $stmt->fetch();

// Si le menu n'existe pas
if (!$menu) {
    header('Location: menus.php');
    exit;
}

// Vérifier le stock
if ($menu['quantite_restante'] <= 0) {
    $_SESSION['error'] = "Désolé, ce menu n'est plus disponible.";
    header('Location: menu-detail.php?id=' . $menu_id);
    exit;
}
?>
<?php require_once 'includes/header.php'; ?>

<main>

    <?php
    if (isset($_SESSION['erreurs_commande'])) {
        echo '<div class="alert alert-error">';
        foreach ($_SESSION['erreurs_commande'] as $erreur) {
            echo '<p>• ' . htmlspecialchars($erreur) . '</p>';
        }
        echo '</div>';
        unset($_SESSION['erreurs_commande']);
    }
    ?>

    <!-- En-tête -->
    <section class="contact-section">
        <div class="container">
            
            <div class="contact-header">
                <h1>Commander : <?php echo htmlspecialchars($menu['nom']); ?></h1>
                <p>Complétez les informations ci-dessous pour finaliser votre commande</p>
            </div>
            
            <!-- Formulaire + Récap -->
            <div class="contact-grid">
                
                <!-- FORMULAIRE DE COMMANDE -->
                <div class="contact-form-container">
                    <form action="traitement-commande.php" method="POST" class="contact-form">
                        
                        <!-- ID du menu (caché) -->
                        <input type="hidden" name="menu_id" value="<?php echo $menu_id; ?>">
                        
                        <!-- Date de prestation -->
                        <div class="form-group">
                            <label for="date_prestation">Date de prestation *</label>
                            <input 
                                type="date" 
                                id="date_prestation" 
                                name="date_prestation" 
                                min="<?php echo date('Y-m-d', strtotime('+7 days')); ?>"
                                required
                            >
                            <small class="form-hint">Minimum 7 jours avant la prestation</small>
                        </div>
                        
                        <!-- Heure de livraison -->
                        <div class="form-group">
                            <label for="heure_livraison">Heure de livraison souhaitée *</label>
                            <input 
                                type="time" 
                                id="heure_livraison" 
                                name="heure_livraison" 
                                required
                            >
                        </div>
                        
                        <!-- Nombre de personnes -->
                        <div class="form-group">
                            <label for="nombre_personnes">Nombre de personnes *</label>
                            <input 
                                type="number" 
                                id="nombre_personnes" 
                                name="nombre_personnes" 
                                min="<?php echo $menu['personne_minimum']; ?>"
                                value="<?php echo $menu['personne_minimum']; ?>"
                                data-prix="<?php echo $menu['prix_par_personne']; ?>"
                                data-minimum="<?php echo $menu['personne_minimum']; ?>"
                                required
                            >
                            <small class="form-hint">Minimum : <?php echo $menu['personne_minimum']; ?> personnes</small>
                        </div>
                        
                        <!-- Adresse de livraison -->
                        <div class="form-group">
                            <label for="adresse_livraison">Adresse de livraison *</label>
                            <input 
                                type="text" 
                                id="adresse_livraison" 
                                name="adresse_livraison" 
                                placeholder="Rue, numéro"
                                value="<?php echo htmlspecialchars($_SESSION['user_adresse'] ?? ''); ?>"
                                required
                            >
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="code_postal">Code postal *</label>
                                <input 
                                    type="text" 
                                    id="code_postal" 
                                    name="code_postal" 
                                    placeholder="33000"
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
                                    value="Bordeaux"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Livraison hors Bordeaux -->
                        <div class="form-group">
                            <label for="hors_bordeaux">
                                <input type="checkbox" id="hors_bordeaux" name="hors_bordeaux" value="1">
                                La livraison est hors Bordeaux (+ 5€ + 0,59€/km)
                            </label>
                        </div>

                        <!-- Kilomètres (affiché seulement si hors Bordeaux) -->
                        <div class="form-group div-kilometres-hidden" id="div_kilometres">
                            <label for="kilometres">Distance depuis Bordeaux (en km) *</label>
                            <input 
                                type="number" 
                                id="kilometres" 
                                name="kilometres" 
                                min="0"
                                step="0.1"
                                placeholder="Ex: 25"
                            >
                        </div>
                        
                        <!-- Commentaire optionnel -->
                        <div class="form-group">
                            <label for="commentaire">Commentaire / Instructions particulières</label>
                            <textarea 
                                id="commentaire" 
                                name="commentaire" 
                                rows="4"
                                placeholder="Informations complémentaires, allergies non mentionnées..."
                            ></textarea>
                        </div>
                        
                        <!-- Calcul du prix -->
                        <div class="prix-recapitulatif">
                            <p>
                                Prix par personne : <span class="prix-highlight"><?php echo number_format($menu['prix_par_personne'], 2, ',', ' '); ?>€</span>
                            </p>
                            <p>
                                Prix menu : <span id="prix_menu" class="prix-highlight"><?php echo number_format($menu['prix_par_personne'] * $menu['personne_minimum'], 2, ',', ' '); ?>€</span>
                            </p>
                            <p class="ligne-reduction-hidden prix-ligne-reduction" id="ligne_reduction">
                                Réduction 10% : <span id="montant_reduction">0,00€</span>
                            </p>
                            <p>
                                Frais de livraison : <span id="frais_livraison">0,00€</span>
                            </p>
                            <hr>
                            <p class="prix-total-final">
                                TOTAL : <span id="prix_total"><?php echo number_format($menu['prix_par_personne'] * $menu['personne_minimum'], 2, ',', ' '); ?>€</span>
                            </p>
                        </div>
                        
                        <!-- Bouton Commander -->
                        <button type="submit" class="btn-contact">Valider ma commande</button>
                        
                    </form>
                </div>
                
                <!-- RÉCAPITULATIF MENU -->
                <div class="contact-info">
                    <div class="info-card">
                        <h3>📋 Récapitulatif</h3>
                        <p><strong>Menu :</strong> <?php echo htmlspecialchars($menu['nom']); ?></p>
                        <p><strong>Service :</strong> <?php echo htmlspecialchars($menu['service']); ?></p>
                        <p><strong>Régime :</strong> <?php echo htmlspecialchars($menu['regime_nom']); ?></p>
                    </div>
                    
                    <div class="info-card">
                        <h3>⚠️ Conditions</h3>
                        <p style="font-size: 14px;">• Commande minimum 7 jours avant</p>
                        <p style="font-size: 14px;">• Livraison à Bordeaux et périphérie</p>
                        <p style="font-size: 14px;">• Stock restant : <?php echo $menu['quantite_restante']; ?> commandes</p>
                    </div>
                </div>
                
            </div>
            
        </div>
    </section>
</main>

<!-- Script commande -->
<script src="assets/js/commande.js"></script>

<?php require_once 'includes/footer.php'; ?>