<?php
require_once 'includes/config.php';

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mot-de-passe-oublie.php');
    exit;
}

// Récupérer l'email
$email = trim($_POST['email']);

$erreurs = [];

if (empty($email)) {
    $erreurs[] = "L'adresse email est obligatoire.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

if (!empty($erreurs)) {
    $_SESSION['erreurs_reset'] = $erreurs;
    header('Location: mot-de-passe-oublie.php');
    exit;
}

// Vérifier si l'email existe dans la BDD
$sql = "SELECT utilisateur_id, prenom, nom FROM utilisateur WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->execute(['email' => $email]);
$user = $stmt->fetch();


if ($user) {
    // Générer un token unique
    $token = bin2hex(random_bytes(32));
    
    $expire = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    $sql_update = "UPDATE utilisateur 
                   SET reset_token = :token, reset_token_expire = :expire 
                   WHERE utilisateur_id = :id";
    
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([
        'token' => $token,
        'expire' => $expire,
        'id' => $user['utilisateur_id']
    ]);
    
    // Envoyer le mail
    $destinataire = $email;
    $sujet = "Réinitialisation de votre mot de passe - Vite & Gourmand";
    $lien = "http://localhost:3000/nouveau-mot-de-passe.php?token=$token";

    $message = "
Bonjour {$user['prenom']} {$user['nom']},

Vous avez demandé à réinitialiser votre mot de passe sur Vite & Gourmand.

Cliquez sur le lien ci-dessous pour définir un nouveau mot de passe :
→ $lien

⚠️ Ce lien est valable pendant 1 heure seulement.

Si vous n'avez pas demandé cette réinitialisation, ignorez simplement cet email.

L'équipe Vite & Gourmand
05 56 00 00 00
contact@vitegourmand.fr
";
    
    $headers = "From: noreply@vitegourmand.fr\r\n";
    $headers .= "Reply-To: contact@vitegourmand.fr\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    @mail($destinataire, $sujet, $message, $headers);
}

$_SESSION['succes_reset'] = "Si cette adresse email existe dans notre base, vous allez recevoir un lien de réinitialisation.";
header('Location: mot-de-passe-oublie.php');
exit;
?>