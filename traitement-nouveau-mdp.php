<?php
require_once 'includes/config.php';

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: connexion.php');
    exit;
}

// Récupérer les données
$token = trim($_POST['token']);
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];

$erreurs = [];

if (empty($token) || empty($password)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

// Vérifier que les mots de passe correspondent
if ($password !== $password_confirm) {
    $erreurs[] = "Les mots de passe ne correspondent pas.";
}

if (strlen($password) < 10) {
    $erreurs[] = "Le mot de passe doit contenir au minimum 10 caractères.";
}
if (!preg_match('/[A-Z]/', $password)) {
    $erreurs[] = "Le mot de passe doit contenir au moins une majuscule.";
}
if (!preg_match('/[a-z]/', $password)) {
    $erreurs[] = "Le mot de passe doit contenir au moins une minuscule.";
}
if (!preg_match('/[0-9]/', $password)) {
    $erreurs[] = "Le mot de passe doit contenir au moins un chiffre.";
}
if (!preg_match('/[@#$%&*!?.,;:_\-]/', $password)) {
    $erreurs[] = "Le mot de passe doit contenir au moins un caractère spécial (@#$%&*!?.,;:_-).";
}

// Vérifier que le token existe et n'a pas expiré
$sql = "SELECT utilisateur_id, reset_token_expire 
        FROM utilisateur 
        WHERE reset_token = :token";

$stmt = $pdo->prepare($sql);
$stmt->execute(['token' => $token]);
$user = $stmt->fetch();

if (!$user) {
    $erreurs[] = "Ce lien de réinitialisation n'est pas valide.";
}

if ($user && strtotime($user['reset_token_expire']) < time()) {
    $erreurs[] = "Ce lien de réinitialisation a expiré.";
}

if (!empty($erreurs)) {
    $_SESSION['erreurs_nouveau_mdp'] = $erreurs;
    header('Location: nouveau-mot-de-passe.php?token=' . urlencode($token));
    exit;
}

// Mettre à jour le mot de passe
try {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql_update = "UPDATE utilisateur 
                   SET password = :password, 
                       reset_token = NULL, 
                       reset_token_expire = NULL 
                   WHERE utilisateur_id = :id";
    
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([
        'password' => $password_hash,
        'id' => $user['utilisateur_id']
    ]);
    
    $_SESSION['succes_connexion'] = "Votre mot de passe a été réinitialisé avec succès ! Vous pouvez maintenant vous connecter.";
    header('Location: connexion.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['erreurs_nouveau_mdp'] = ["Une erreur est survenue. Veuillez réessayer."];
    header('Location: nouveau-mot-de-passe.php?token=' . urlencode($token));
    exit;
}
?>