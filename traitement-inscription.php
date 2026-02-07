<?php
require_once 'includes/config.php';

// Vérifier que le formulaire a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inscription.php');
    exit;
}

// Récupération et nettoyage des données du formulaire
$nom = trim($_POST['nom']);
$prenom = trim($_POST['prenom']);
$email = trim($_POST['email']);
$telephone = trim($_POST['telephone']);
$adresse = trim($_POST['adresse']);
$code_postal = trim($_POST['code_postal']);
$ville = trim($_POST['ville']);
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];

$erreurs = [];

// VALIDATION DES DONNÉES

if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || 
    empty($adresse) || empty($code_postal) || empty($ville) || empty($password)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

if ($password !== $password_confirm) {
    $erreurs[] = "Les mots de passe ne correspondent pas.";
}

if (!empty($password)) {
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
}

// VÉRIFIER QUE L'EMAIL N'EXISTE PAS DÉJÀ

if (empty($erreurs)) {
    
    $sql = "SELECT utilisateur_id FROM utilisateur WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    
    if ($stmt->fetch()) {
        $erreurs[] = "Cette adresse email est déjà utilisée.";
    }
}

// S'IL Y A DES ERREURS, REDIRIGER AVEC MESSAGE

if (!empty($erreurs)) {
    $_SESSION['erreurs_inscription'] = $erreurs;
    $_SESSION['form_data'] = $_POST;
    header('Location: inscription.php');
    exit;
}

// TOUT EST BON : INSÉRER L'UTILISATEUR EN BDD

try {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql_role = "SELECT role_id FROM role WHERE libelle = 'utilisateur'";
    $stmt_role = $pdo->query($sql_role);
    $role = $stmt_role->fetch();
    $role_id = $role['role_id'];
    
    $sql = "INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, code_postal, ville, pays, role_id) 
            VALUES (:email, :password, :nom, :prenom, :telephone, :adresse, :code_postal, :ville, 'France', :role_id)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'email' => $email,
        'password' => $password_hash,
        'nom' => $nom,
        'prenom' => $prenom,
        'telephone' => $telephone,
        'adresse' => $adresse,
        'code_postal' => $code_postal,
        'ville' => $ville,
        'role_id' => $role_id
    ]);
    
// Succès ! Envoyer un mail de bienvenue
$destinataire = $email;
$sujet = "Bienvenue chez Vite & Gourmand !";
$message = "
Bonjour $prenom $nom,

Nous sommes ravis de vous accueillir chez Vite & Gourmand !

Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter et découvrir nos délicieux menus pour vos événements.

Informations de votre compte :
- Email : $email
- Nom : $prenom $nom
- Téléphone : $telephone

N'hésitez pas à nous contacter si vous avez la moindre question.

À très bientôt,
L'équipe Vite & Gourmand
";

$headers = "From: noreply@vitegourmand.fr\r\n";
$headers .= "Reply-To: contact@vitegourmand.fr\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail($destinataire, $sujet, $message, $headers);
if (!$sent) {
    error_log("Email bienvenue NON envoyé à $destinataire via mail()");
}

$_SESSION['succes_inscription'] = "Votre compte a été créé avec succès ! Un email de bienvenue vous a été envoyé.";
header('Location: connexion.php');
exit;
    
} catch (PDOException $e) {
    $_SESSION['erreurs_inscription'] = ["Une erreur est survenue lors de la création de votre compte. Veuillez réessayer."];
    header('Location: inscription.php');
    exit;
}
?>