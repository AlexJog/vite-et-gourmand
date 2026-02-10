<?php
require_once 'includes/config.php';

// Vérifier que le formulaire a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: connexion.php');
    exit;
}

// Récupération et nettoyage des données
$email = trim($_POST['email']);
$password = $_POST['password'];

$erreurs = [];

// VALIDATION DES DONNÉES

if (empty($email) || empty($password)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

if (!empty($erreurs)) {
    $_SESSION['erreurs_connexion'] = $erreurs;
    $_SESSION['form_email'] = $email;
    header('Location: connexion.php');
    exit;
}

// VÉRIFIER L'UTILISATEUR DANS LA BDD

try {
    $sql = "SELECT u.*, r.libelle as role_nom 
            FROM utilisateur u
            INNER JOIN role r ON u.role_id = r.role_id
            WHERE u.email = :email";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        
        // VÉRIFIER QUE LE COMPTE EST ACTIF
        
        if (isset($user['actif']) && $user['actif'] == 0) {
            $erreurs[] = "Votre compte a été désactivé. Veuillez contacter l'administrateur.";
            $_SESSION['erreurs_connexion'] = $erreurs;
            $_SESSION['form_email'] = $email;
            header('Location: connexion.php');
            exit;
        }
        
        // CONNEXION RÉUSSIE
        
        // Créer les variables de session
        $_SESSION['user_id'] = $user['utilisateur_id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_nom'] = $user['nom'];
        $_SESSION['user_prenom'] = $user['prenom'];
        $_SESSION['user_role'] = $user['role_nom'];
        $_SESSION['user_adresse'] = $user['adresse_postale'];
        $_SESSION['first_login'] = true;

        // Nettoyer les anciennes erreurs
        unset($_SESSION['error']);
        unset($_SESSION['erreurs_connexion']);
        
        if ($user['role_nom'] === 'admin') {
            header('Location: admin/dashboard.php');
        } elseif ($user['role_nom'] === 'employe') {
            header('Location: employe/dashboard.php');
        } else {
            header('Location: index.php');
        }
        exit;
        
    } else {
        $erreurs[] = "Email ou mot de passe incorrect.";
        $_SESSION['erreurs_connexion'] = $erreurs;
        $_SESSION['form_email'] = $email;
        header('Location: connexion.php');
        exit;
    }
    
} catch (PDOException $e) {
    $_SESSION['erreurs_connexion'] = ["Une erreur est survenue. Veuillez réessayer."];
    header('Location: connexion.php');
    exit;
}
?>