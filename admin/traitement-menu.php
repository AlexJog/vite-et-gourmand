<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gestion-menus.php');
    exit;
}

$action = $_POST['action'];

// FONCTION UPLOAD IMAGE
function uploadImage($file) {
    
    // Vérifier qu'un fichier a été uploadé
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['erreur' => true, 'message' => "Aucune image n'a été uploadée."];
    }
    
    // Vérifier les erreurs d'upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['erreur' => true, 'message' => "Erreur lors de l'upload de l'image."];
    }
    
    // Vérifier la taille (10 Mo max)
    $max_size = 10 * 1024 * 1024;
    if ($file['size'] > $max_size) {
        return ['erreur' => true, 'message' => "L'image est trop volumineuse (max 10 Mo)."];
    }
    
    // Vérifier le type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        return ['erreur' => true, 'message' => "Format d'image non autorisé (JPG, JPEG, PNG uniquement)."];
    }
    
    // Générer un nom de fichier unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $nom_fichier = 'menu-' . time() . '-' . uniqid() . '.' . $extension;
    
    // Dossier de destination
    $dossier_upload = '../assets/images/menus/';
    $chemin_complet = $dossier_upload . $nom_fichier;
    
    // Créer le dossier s'il n'existe pas
    if (!is_dir($dossier_upload)) {
        mkdir($dossier_upload, 0755, true);
    }
    
    // Déplacer le fichier uploadé
    if (move_uploaded_file($file['tmp_name'], $chemin_complet)) {
        return ['erreur' => false, 'chemin' => '/assets/images/menus/' . $nom_fichier];
    } else {
        return ['erreur' => true, 'message' => "Erreur lors de l'enregistrement de l'image."];
    }
}

// AJOUTER UN MENU
if ($action === 'ajouter') {
    
    // Récupération des données
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $service = trim($_POST['service']);
    $regime_id = (int)$_POST['regime_id'];
    $theme_id = (int)$_POST['theme_id'];
    $prix_par_personne = (float)$_POST['prix_par_personne'];
    $personne_minimum = (int)$_POST['personne_minimum'];
    $quantite_restante = (int)$_POST['quantite_restante'];
    
    $erreurs = [];
    
    if (empty($nom) || empty($description) || empty($service)) {
        $erreurs[] = "Tous les champs sont obligatoires.";
    }
    
    if ($prix_par_personne <= 0) {
        $erreurs[] = "Le prix doit être supérieur à 0.";
    }
    
    if ($personne_minimum <= 0) {
        $erreurs[] = "Le nombre minimum de personnes doit être supérieur à 0.";
    }
    
    // Upload de l'image
    $upload_result = uploadImage($_FILES['image']);
    
    if ($upload_result['erreur']) {
        $erreurs[] = $upload_result['message'];
    }
    
    // S'il y a des erreurs
    if (!empty($erreurs)) {
        $_SESSION['erreurs_menu'] = $erreurs;
        header('Location: ajouter-menu.php');
        exit;
    }
    
    $image_url = $upload_result['chemin'];
    
    // Insertion en BDD
    try {
        $sql = "INSERT INTO menu (nom, description, service, regime_id, theme_id, prix_par_personne, personne_minimum, quantite_restante, image_url)
                VALUES (:nom, :description, :service, :regime_id, :theme_id, :prix_par_personne, :personne_minimum, :quantite_restante, :image_url)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'description' => $description,
            'service' => $service,
            'regime_id' => $regime_id,
            'theme_id' => $theme_id,
            'prix_par_personne' => $prix_par_personne,
            'personne_minimum' => $personne_minimum,
            'quantite_restante' => $quantite_restante,
            'image_url' => $image_url
        ]);
        
        $_SESSION['succes_admin'] = "Le menu a été créé avec succès !";
        header('Location: gestion-menus.php');
        exit;
        
    } catch (PDOException $e) {
        if (file_exists('../' . $image_url)) {
            unlink('../' . $image_url);
        }
        
        $_SESSION['erreurs_menu'] = ["Une erreur est survenue lors de la création."];
        header('Location: ajouter-menu.php');
        exit;
    }
}

// MODIFIER UN MENU
elseif ($action === 'modifier') {
    
    // Récupération des données
    $menu_id = (int)$_POST['menu_id'];
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);
    $service = trim($_POST['service']);
    $regime_id = (int)$_POST['regime_id'];
    $theme_id = (int)$_POST['theme_id'];
    $prix_par_personne = (float)$_POST['prix_par_personne'];
    $personne_minimum = (int)$_POST['personne_minimum'];
    $quantite_restante = (int)$_POST['quantite_restante'];
    
    $erreurs = [];
    
    if (empty($nom) || empty($description) || empty($service)) {
        $erreurs[] = "Tous les champs sont obligatoires.";
    }
    
    if ($prix_par_personne <= 0) {
        $erreurs[] = "Le prix doit être supérieur à 0.";
    }
    
    if ($personne_minimum <= 0) {
        $erreurs[] = "Le nombre minimum de personnes doit être supérieur à 0.";
    }
    
    // Vérifier si une nouvelle image a été uploadée
    $image_url = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = uploadImage($_FILES['image']);
        
        if ($upload_result['erreur']) {
            $erreurs[] = $upload_result['message'];
        } else {
            $image_url = $upload_result['chemin'];
            
            // Récupérer l'ancienne image pour la supprimer
            $sql_old = "SELECT image_url FROM menu WHERE menu_id = :menu_id";
            $stmt_old = $pdo->prepare($sql_old);
            $stmt_old->execute(['menu_id' => $menu_id]);
            $old_image = $stmt_old->fetch()['image_url'];
            
            // Supprimer l'ancienne image (si elle existe)
            if (!empty($old_image) && file_exists('../' . $old_image)) {
                unlink('../' . $old_image);
            }
        }
    }
    
    // S'il y a des erreurs
    if (!empty($erreurs)) {
        $_SESSION['erreurs_menu'] = $erreurs;
        header('Location: modifier-menu.php?id=' . $menu_id);
        exit;
    }
    
    // Mise à jour en BDD
    try {
        if ($image_url) {
            $sql = "UPDATE menu SET 
                    nom = :nom, 
                    description = :description, 
                    service = :service, 
                    regime_id = :regime_id, 
                    theme_id = :theme_id, 
                    prix_par_personne = :prix_par_personne, 
                    personne_minimum = :personne_minimum, 
                    quantite_restante = :quantite_restante, 
                    image_url = :image_url
                    WHERE menu_id = :menu_id";
            
            $params = [
                'nom' => $nom,
                'description' => $description,
                'service' => $service,
                'regime_id' => $regime_id,
                'theme_id' => $theme_id,
                'prix_par_personne' => $prix_par_personne,
                'personne_minimum' => $personne_minimum,
                'quantite_restante' => $quantite_restante,
                'image_url' => $image_url,
                'menu_id' => $menu_id
            ];
        } else {
            $sql = "UPDATE menu SET 
                    nom = :nom, 
                    description = :description, 
                    service = :service, 
                    regime_id = :regime_id, 
                    theme_id = :theme_id, 
                    prix_par_personne = :prix_par_personne, 
                    personne_minimum = :personne_minimum, 
                    quantite_restante = :quantite_restante
                    WHERE menu_id = :menu_id";
            
            $params = [
                'nom' => $nom,
                'description' => $description,
                'service' => $service,
                'regime_id' => $regime_id,
                'theme_id' => $theme_id,
                'prix_par_personne' => $prix_par_personne,
                'personne_minimum' => $personne_minimum,
                'quantite_restante' => $quantite_restante,
                'menu_id' => $menu_id
            ];
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        
        $_SESSION['succes_admin'] = "Le menu a été modifié avec succès !";
        header('Location: gestion-menus.php');
        exit;
        
    } catch (PDOException $e) {
        $_SESSION['erreurs_menu'] = ["Une erreur est survenue lors de la modification."];
        header('Location: modifier-menu.php?id=' . $menu_id);
        exit;
    }
}

// Action inconnue
else {
    header('Location: gestion-menus.php');
    exit;
}
?>