<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vite & Gourmand - Traiteur à Bordeaux</title>
    
    <!-- Google Fonts pour police d'écriture -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Lien vers mon CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=<?= time() ?>">
</head>
<body>
    <header class="site-header">
        <nav class="nav-header">
            <a href="/index.php" class="logo">Vite & Gourmand</a>
            <ul class="nav-lien">
                <li><a href="/index.php">Accueil</a></li>
                <li><a href="/menus.php">Menus</a></li>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="user-menu">
                        <span class="user-greeting">Bonjour <?php echo htmlspecialchars($_SESSION['user_prenom']); ?></span>
                        
                        <?php if ($_SESSION['user_role'] === 'utilisateur'): ?>
                            <a href="/utilisateur/dashboard.php">Mon espace</a>
                        <?php elseif ($_SESSION['user_role'] === 'employe'): ?>
                            <a href="/employe/dashboard.php">Dashboard</a>
                        <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                            <a href="/admin/dashboard.php">Dashboard Admin</a>
                        <?php endif; ?>
                        
                        <a href="/deconnexion.php" class="btn-deconnexion">Déconnexion</a>
                    </li>
                <?php else: ?>
                    <!-- Si non connecté : afficher le bouton connexion -->
                    <li><a href="/connexion.php">Connexion</a></li>
                <?php endif; ?>
                
                <li><a href="/contact.php">Contact</a></li>
            </ul>
            <button class="burger-menu" id="burgerMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </nav>
        
        <!-- Sous-menu Employé (si employé connecté) -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'employe'): ?>
            <nav class="sub-nav">
                <ul>
                    <li><a href="/employe/dashboard.php">📊 Dashboard</a></li>
                    <li><a href="/employe/gestion-commandes.php">📦 Commandes</a></li>
                    <li><a href="/employe/gestion-avis.php">⭐ Avis clients</a></li>
                </ul>
            </nav>
        <?php endif; ?>
        
        <!-- Sous-menu Admin (si admin connecté) -->
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
            <nav class="sub-nav">
                <ul>
                    <li><a href="/admin/dashboard.php">📊 Dashboard</a></li>
                    <li><a href="/admin/gestion-menus.php">🍽️ Menus</a></li>
                    <li><a href="/admin/gestion-commandes.php">📦 Commandes</a></li>
                    <li><a href="/admin/gestion-avis.php">⭐ Avis clients</a></li>
                    <li><a href="/admin/gestion-employes.php">👥 Employés</a></li>
                    <li><a href="/admin/statistiques.php">📈 Statistiques</a></li>
                    <li><a href="/admin/creer-employe.php">➕ Créer employé</a></li>
                </ul>
            </nav>
        <?php endif; ?>
    </header>
    
    <!-- Menu mobile -->
    <div class="mobile-menu" id="mobileMenu">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="mobile-user-menu">
                <span class="mobile-user-greeting">Bonjour <?php echo htmlspecialchars($_SESSION['user_prenom']); ?></span>
            
                <?php if ($_SESSION['user_role'] === 'utilisateur'): ?>
                    <a href="/utilisateur/dashboard.php" class="mobile-link">Mon espace</a>
                <?php elseif ($_SESSION['user_role'] === 'employe'): ?>
                    <a href="/employe/dashboard.php" class="mobile-link">📊 Dashboard</a>
                    <a href="/employe/gestion-commandes.php" class="mobile-link">📦 Commandes</a>
                    <a href="/employe/gestion-avis.php" class="mobile-link">⭐ Avis clients</a>
                <?php elseif ($_SESSION['user_role'] === 'admin'): ?>
                    <a href="/admin/dashboard.php" class="mobile-link">📊 Dashboard</a>
                    <a href="/admin/gestion-menus.php" class="mobile-link">🍽️ Menus</a>
                    <a href="/admin/gestion-commandes.php" class="mobile-link">📦 Commandes</a>
                    <a href="/admin/gestion-avis.php" class="mobile-link">⭐ Avis clients</a>
                    <a href="/admin/gestion-employes.php" class="mobile-link">👥 Employés</a>
                    <a href="/admin/statistiques.php" class="mobile-link">📈 Statistiques</a>
                    <a href="/admin/creer-employe.php" class="mobile-link">➕ Créer employé</a>
                <?php endif; ?>
            
                <a href="/deconnexion.php" class="mobile-deconnexion">Déconnexion</a>
            </div>
        <?php endif; ?>
    
        <a href="/index.php" class="mobile-link">Accueil</a>
        <a href="/menus.php" class="mobile-link">Menus</a>
        <a href="/contact.php" class="mobile-link">Contact</a>
    
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="/connexion.php" class="mobile-link">Connexion</a>
        <?php endif; ?>
    </div>