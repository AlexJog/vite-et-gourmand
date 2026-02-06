<?php require_once 'includes/config.php'; ?>

<?php

// Vérifier que l'ID est dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: menus.php');
    exit;
}

$menu_id = (int)$_GET['id'];

// RÉCUPÉRER LES INFOS DU MENU

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

// RÉCUPÉRER LES PLATS DU MENU

$sql_plats = "SELECT p.* 
              FROM plat p
              INNER JOIN menu_plat mp ON p.plat_id = mp.plat_id
              WHERE mp.menu_id = :menu_id
              ORDER BY p.plat_id";

$stmt_plats = $pdo->prepare($sql_plats);
$stmt_plats->execute(['menu_id' => $menu_id]);
$plats = $stmt_plats->fetchAll();
?>

<?php require_once 'includes/header.php'; ?>

<main>

    <!-- Image du Menu -->
    <section class="menu-detail-hero">
        <?php 
        $image = !empty($menu['image_url']) ? $menu['image_url'] : '/assets/images/menus/default.jpg';
        ?>
        <img src="<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($menu['nom']); ?>">
    </section>
    
    <!-- Titre du Menu -->
    <section class="menu-detail-titre">
        <div class="container">
            <h1><?php echo htmlspecialchars($menu['nom']); ?></h1>
        </div>
    </section>

    <!-- Section Détails -->
    <section class="menu-detail-section">
        <div class="container">
            <div class="detail-grid">
                
                <!-- Détails du menu -->
                <div class="detail-gauche">
                    
                    <!-- Description -->
                    <div class="detail-section">
                        <p class="menu-description">
                            <?php echo htmlspecialchars($menu['description']); ?>
                        </p>
                    </div>
                    
                    <!-- Service proposé -->
                    <div class="detail-section">
                        <h2 class="section-soustitre">Service proposé</h2>
                        <p><?php echo htmlspecialchars($menu['service']); ?></p>
                    </div>
                    
                    <!-- Composition -->
                    <div class="detail-section">
                        <h2 class="section-soustitre">Composition du menu</h2>
                        
                        <?php if (!empty($plats)): ?>
                            <ul class="liste-menu">
                                <?php foreach ($plats as $index => $plat): ?>
                                    <li>
                                        <?php 
                                        if ($index == 0) {
                                            echo '<strong>Entrée :</strong> ';
                                        } elseif ($index == 1) {
                                            echo '<strong>Plat :</strong> ';
                                        } elseif ($index == 2) {
                                            echo '<strong>Dessert :</strong> ';
                                        }
                                        echo htmlspecialchars($plat['nom_plat']);
                                        ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p><em>Les plats de ce menu seront détaillés prochainement.</em></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Allergènes -->
                    <div class="detail-section">
                        <h3 class="allergenes">⚠️ Allergènes ⚠️</h3>
                        
                        <?php
                        // Récupérer les allergènes des plats de ce menu
                        $sql_allergenes = "SELECT DISTINCT a.libelle
                                        FROM allergene a
                                        INNER JOIN plat_allergene pa ON a.allergene_id = pa.allergene_id
                                        INNER JOIN menu_plat mp ON pa.plat_id = mp.plat_id
                                        WHERE mp.menu_id = :menu_id
                                        ORDER BY a.libelle";
                        
                        $stmt_allergenes = $pdo->prepare($sql_allergenes);
                        $stmt_allergenes->execute(['menu_id' => $menu_id]);
                        $allergenes = $stmt_allergenes->fetchAll();
                        ?>
                        
                        <?php if (!empty($allergenes)): ?>
                            <ul class="liste-allergenes">
                                <?php foreach ($allergenes as $allergene): ?>
                                    <li><?php echo htmlspecialchars($allergene['libelle']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p><em>Ce menu ne contient aucun allergène majeur déclaré.</em></p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Infos pratiques + Commander -->
                <div class="detail-droite">
                    
                    <div class="detail-carte">
                        <!-- Prix -->
                        <p class="detail-prix"><?php echo number_format($menu['prix_par_personne'], 2, ',', ' '); ?>€ / Personne</p>
                        
                        <!-- Nb personnes -->
                        <p class="detail-info">Minimum : <?php echo htmlspecialchars($menu['personne_minimum']); ?> personnes</p>
                        
                        <!-- Thème -->
                        <?php if (!empty($menu['theme_nom'])): ?>
                        <div class="detail-badge">
                            <span>Thème : <?php echo htmlspecialchars($menu['theme_nom']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Régime -->
                        <?php if (!empty($menu['regime_nom'])): ?>
                        <div class="detail-badge">
                            <span>Régime : <?php echo htmlspecialchars($menu['regime_nom']); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Stock -->
                        <p class="detail-stock">Stock : <?php echo htmlspecialchars($menu['quantite_restante']); ?> commandes disponibles</p>
                        
                        <!-- Bouton Commander -->
                        <a href="commander.php?menu=<?php echo $menu['menu_id']; ?>" class="btn-commander">Commander ce menu</a>
                    </div>
                    
                </div>
                
            </div>
        </div>
    </section>

    <!-- CONDITIONS IMPORTANTES -->
    <section class="conditions">
        <div class="container">
            <div class="conditions-box">
                <h3 class="conditions-titre">⚠️ CONDITIONS IMPORTANTES</h3>
                <ul class="conditions-liste">
                    <li>Commande à effectuer au minimum 7 jours avant la prestation</li>
                    <li>Conservation au frais recommandée</li>
                    <li>Livraison uniquement à Bordeaux et périphérie</li>
                </ul>
            </div>
        </div>
    </section>

</main>

<?php require_once 'includes/footer.php'; ?>