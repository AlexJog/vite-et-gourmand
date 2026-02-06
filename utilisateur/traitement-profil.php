<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un utilisateur
if ($_SESSION['user_role'] !== 'utilisateur') {
    header('Location: ../index.php');
    exit;
}

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profil.php');
    exit;
}

// Récupérer les données
$nom = trim($_POST['nom']);
$prenom = trim($_POST['prenom']);
$email = trim($_POST['email']);
$telephone = trim($_POST['telephone']);
$adresse_postale = trim($_POST['adresse_postale']);
$code_postal = trim($_POST['code_postal']);
$ville = trim($_POST['ville']);
$pays = trim($_POST['pays']);

$password_actuel = $_POST['password_actuel'] ?? '';
$nouveau_password = $_POST['nouveau_password'] ?? '';
$nouveau_password_confirm = $_POST['nouveau_password_confirm'] ?? '';

$utilisateur_id = $_SESSION['user_id'];

$erreurs = [];

// Vérifier que les champs obligatoires sont remplis
if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || 
    empty($adresse_postale) || empty($code_postal) || empty($ville) || empty($pays)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

// Valider l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

// Vérifier que l'email n'est pas déjà utilisé par un autre utilisateur
if (empty($erreurs)) {
    $sql_check = "SELECT utilisateur_id FROM utilisateur WHERE email = :email AND utilisateur_id != :utilisateur_id";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([
        'email' => $email,
        'utilisateur_id' => $utilisateur_id
    ]);
    
    if ($stmt_check->fetch()) {
        $erreurs[] = "Cette adresse email est déjà utilisée par un autre compte.";
    }
}

// Si l'utilisateur veut changer de mot de passe
$change_password = false;
if (!empty($password_actuel) || !empty($nouveau_password) || !empty($nouveau_password_confirm)) {
    $change_password = true;
    
    if (empty($password_actuel)) {
        $erreurs[] = "Le mot de passe actuel est requis pour changer de mot de passe.";
    }
    
    if (empty($nouveau_password)) {
        $erreurs[] = "Le nouveau mot de passe est requis.";
    }
    
    if (empty($nouveau_password_confirm)) {
        $erreurs[] = "La confirmation du nouveau mot de passe est requise.";
    }
    
    if ($nouveau_password !== $nouveau_password_confirm) {
        $erreurs[] = "Les nouveaux mots de passe ne correspondent pas.";
    }
    
    // Valider le nouveau mot de passe
    if (!empty($nouveau_password)) {
        if (strlen($nouveau_password) < 10) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au minimum 10 caractères.";
        }
        if (!preg_match('/[A-Z]/', $nouveau_password)) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au moins une majuscule.";
        }
        if (!preg_match('/[a-z]/', $nouveau_password)) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au moins une minuscule.";
        }
        if (!preg_match('/[0-9]/', $nouveau_password)) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au moins un chiffre.";
        }
        if (!preg_match('/[@#$%&*!?.,;:_\-]/', $nouveau_password)) {
            $erreurs[] = "Le nouveau mot de passe doit contenir au moins un caractère spécial (@#$%&*!?.,;:_-).";
        }
    }
    
    // Vérifier le mot de passe actuel
    if (!empty($password_actuel)) {
        $sql_pwd = "SELECT password FROM utilisateur WHERE utilisateur_id = :utilisateur_id";
        $stmt_pwd = $pdo->prepare($sql_pwd);
        $stmt_pwd->execute(['utilisateur_id' => $utilisateur_id]);
        $user_pwd = $stmt_pwd->fetch();
        
        if (!password_verify($password_actuel, $user_pwd['password'])) {
            $erreurs[] = "Le mot de passe actuel est incorrect.";
        }
    }
}

// S'il y a des erreurs
if (!empty($erreurs)) {
    $_SESSION['erreurs_profil'] = $erreurs;
    header('Location: profil.php');
    exit;
}

// Mettre à jour les informations
try {
    if ($change_password) {
        $password_hash = password_hash($nouveau_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE utilisateur 
                SET nom = :nom, 
                    prenom = :prenom, 
                    email = :email, 
                    telephone = :telephone, 
                    adresse_postale = :adresse_postale, 
                    code_postal = :code_postal, 
                    ville = :ville, 
                    pays = :pays,
                    password = :password
                WHERE utilisateur_id = :utilisateur_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'adresse_postale' => $adresse_postale,
            'code_postal' => $code_postal,
            'ville' => $ville,
            'pays' => $pays,
            'password' => $password_hash,
            'utilisateur_id' => $utilisateur_id
        ]);
    } else {
        $sql = "UPDATE utilisateur 
                SET nom = :nom, 
                    prenom = :prenom, 
                    email = :email, 
                    telephone = :telephone, 
                    adresse_postale = :adresse_postale, 
                    code_postal = :code_postal, 
                    ville = :ville, 
                    pays = :pays
                WHERE utilisateur_id = :utilisateur_id";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'telephone' => $telephone,
            'adresse_postale' => $adresse_postale,
            'code_postal' => $code_postal,
            'ville' => $ville,
            'pays' => $pays,
            'utilisateur_id' => $utilisateur_id
        ]);
    }
    
    // Mettre à jour la session
    $_SESSION['user_prenom'] = $prenom;
    $_SESSION['user_email'] = $email;
    
    // Message de succès
    if ($change_password) {
        $_SESSION['succes_profil'] = "Vos informations et votre mot de passe ont été mis à jour avec succès.";
    } else {
        $_SESSION['succes_profil'] = "Vos informations ont été mises à jour avec succès.";
    }
    
    header('Location: profil.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['erreurs_profil'] = ["Une erreur est survenue lors de la mise à jour."];
    header('Location: profil.php');
    exit;
}
?>