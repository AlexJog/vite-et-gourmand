<?php require_once 'includes/config.php'; ?>
<?php require_once 'includes/header.php'; ?>

<?php
// RÉCUPÉRER LES FILTRES

$filtre_prix = isset($_GET['prixMax']) && !empty($_GET['prixMax']) ? (float)$_GET['prixMax'] : null;
$filtre_regime = isset($_GET['regime']) && !empty($_GET['regime']) ? $_GET['regime'] : null;
$filtre_theme = isset($_GET['theme']) && !empty($_GET['theme']) ? $_GET['theme'] : null;
$filtre_nb_personnes = isset($_GET['nbPersonnes']) && !empty($_GET['nbPersonnes']) ? (int)$_GET['nbPersonnes'] : null;

// CONSTRUIRE LA REQUÊTE SQL AVEC FILTRES

$sql = "SELECT m.*, r.libelle AS regime_nom, t.libelle AS theme_nom
        FROM menu m
        LEFT JOIN regime r ON m.regime_id = r.regime_id
        LEFT JOIN theme t ON m.theme_id = t.theme_id
        WHERE 1=1";

// Ajouter les conditions si les filtres sont remplis
if ($filtre_prix !== null) {
    $sql .= " AND m.prix_par_personne <= :prix";
}

if ($filtre_regime !== null) {
    $sql .= " AND LOWER(r.libelle) = :regime";
}

if ($filtre_theme !== null) {
    $sql .= " AND LOWER(t.libelle) = :theme";
}

if ($filtre_nb_personnes !== null) {
    $sql .= " AND m.personne_minimum <= :nb_personnes";
}

$sql .= " ORDER BY m.menu_id ASC";

// EXÉCUTER LA REQUÊTE

$stmt = $pdo->prepare($sql);

// Lier les valeurs aux placeholders
if ($filtre_prix !== null) {
    $stmt->bindValue(':prix', $filtre_prix, PDO::PARAM_STR);
}
if ($filtre_regime !== null) {
    $stmt->bindValue(':regime', strtolower($filtre_regime), PDO::PARAM_STR);
}
if ($filtre_theme !== null) {
    $stmt->bindValue(':theme', strtolower($filtre_theme), PDO::PARAM_STR);
}
if ($filtre_nb_personnes !== null) {
    $stmt->bindValue(':nb_personnes', $filtre_nb_personnes, PDO::PARAM_INT);
}

$stmt->execute();
$menus = $stmt->fetchAll();
?>

<main>

    <!-- Section Titre -->
    <section class="menus-header">
        <div class="div-menus">
            <h1>Nos Menus</h1>
            <p>Découvrez notre sélection de menus pour tous vos événements</p>
        </div>
    </section>

    <!-- Section Filtres -->
    <section class="filtres">
        <div class="div-menus">
            <form class="filtres-form" method="GET" action="menus.php">
                
                <!-- Filtre Prix -->
                <div class="div-filtre">
                    <label for="prixMax">Prix max :</label>
                    <input 
                        type="number" 
                        id="prixMax" 
                        name="prixMax" 
                        placeholder="Ex: 50€" 
                        min="0"
                        value="<?php echo isset($_GET['prixMax']) ? htmlspecialchars($_GET['prixMax']) : ''; ?>"
                    >
                </div>
                
                <!-- Filtre Régime -->
                <div class="div-filtre">
                    <label for="regime">Régime :</label>
                    <select id="regime" name="regime">
                        <option value="">Tous</option>
                        <option value="classique" <?php echo (isset($_GET['regime']) && $_GET['regime'] == 'classique') ? 'selected' : ''; ?>>Classique</option>
                        <option value="vegetarien" <?php echo (isset($_GET['regime']) && $_GET['regime'] == 'vegetarien') ? 'selected' : ''; ?>>Végétarien</option>
                        <option value="vegan" <?php echo (isset($_GET['regime']) && $_GET['regime'] == 'vegan') ? 'selected' : ''; ?>>Vegan</option>
                    </select>
                </div>

                <!-- Filtre Thème -->
                <div class="div-filtre">
                    <label for="theme">Thème :</label>
                    <select id="theme" name="theme">
                        <option value="">Tous</option>
                        <option value="noel" <?php echo (isset($_GET['theme']) && $_GET['theme'] == 'noel') ? 'selected' : ''; ?>>Noël</option>
                        <option value="paques" <?php echo (isset($_GET['theme']) && $_GET['theme'] == 'paques') ? 'selected' : ''; ?>>Pâques</option>
                        <option value="classique" <?php echo (isset($_GET['theme']) && $_GET['theme'] == 'classique') ? 'selected' : ''; ?>>Classique</option>
                        <option value="evenement" <?php echo (isset($_GET['theme']) && $_GET['theme'] == 'evenement') ? 'selected' : ''; ?>>Événement</option>
                    </select>
                </div>
                                
                <!-- Filtre Nb personnes -->
                <div class="div-filtre">
                    <label for="nbPersonnes">Nb pers :</label>
                    <input 
                        type="number" 
                        id="nbPersonnes" 
                        name="nbPersonnes" 
                        placeholder="Ex: 10" 
                        min="1"
                        value="<?php echo isset($_GET['nbPersonnes']) ? htmlspecialchars($_GET['nbPersonnes']) : ''; ?>"
                    >
                </div>
                
                <!-- Bouton Rechercher -->
                <button type="submit" class="btn-rechercher">Rechercher</button>
                
            </form>
            
            <!-- Bouton Réinitialiser -->
            <?php if (!empty($_GET)): ?>
                <a href="menus.php" class="lien-reinitialiser">
                    ✕ Réinitialiser les filtres
                </a>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- Section Grille de Menus -->
    <section class="grille-menus">
        <div class="div-menus">
            
            <?php if (empty($menus)): ?>
                <p class="message-aucun-resultat">
                    Aucun menu ne correspond à vos critères de recherche.
                </p>
            <?php else: ?>
                <!-- Boucle pour afficher chaque menu -->
                <?php foreach ($menus as $menu): ?>
                    <article class="menu-card">
                        <div class="menu-image">
                            <?php 
                            $image = !empty($menu['image_url']) ? $menu['image_url'] : '/assets/images/menus/default.jpg';
                            ?>
                            <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($menu['nom']); ?>">
                        </div>
                        <div class="menu-content">
                            <h3><?php echo htmlspecialchars($menu['nom']); ?></h3>
                            <p><?php echo htmlspecialchars($menu['description']); ?></p>
                            <div class="menu-info">
                                <p class="menu-prix"><?php echo number_format($menu['prix_par_personne'], 2, ',', ' '); ?>€ / Pers</p>
                                <p class="menu-personnes">Min. <?php echo htmlspecialchars($menu['personne_minimum']); ?> personnes</p>
                            </div>
                            <a href="menu-detail.php?id=<?php echo $menu['menu_id']; ?>" class="btn-voir-detail">Voir détail</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
            
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>