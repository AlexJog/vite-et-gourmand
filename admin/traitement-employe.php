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
    header('Location: creer-employe.php');
    exit;
}

// Récupérer les données
$nom = trim($_POST['nom']);
$prenom = trim($_POST['prenom']);
$email = trim($_POST['email']);
$telephone = trim($_POST['telephone']);
$password = $_POST['password'];

$erreurs = [];

if (empty($nom) || empty($prenom) || empty($email) || empty($telephone) || empty($password)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

// Valider le mot de passe sécurisé
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

// Vérifier que l'email n'existe pas déjà
if (empty($erreurs)) {
    $sql_check = "SELECT utilisateur_id FROM utilisateur WHERE email = :email";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute(['email' => $email]);
    
    if ($stmt_check->fetch()) {
        $erreurs[] = "Cette adresse email est déjà utilisée.";
    }
}

// S'il y a des erreurs
if (!empty($erreurs)) {
    $_SESSION['erreurs_employe'] = $erreurs;
    header('Location: creer-employe.php');
    exit;
}

// Créer le compte employé
try {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Récupérer l'ID du rôle "employe"
    $sql_role = "SELECT role_id FROM role WHERE libelle = 'employe'";
    $stmt_role = $pdo->query($sql_role);
    $role = $stmt_role->fetch();
    $role_id = $role['role_id'];
    
    // Insérer l'employé
    $sql = "INSERT INTO utilisateur (email, password, nom, prenom, telephone, adresse_postale, code_postal, ville, pays, role_id) 
            VALUES (:email, :password, :nom, :prenom, :telephone, '', '', '', 'France', :role_id)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'email' => $email,
        'password' => $password_hash,
        'nom' => $nom,
        'prenom' => $prenom,
        'telephone' => $telephone,
        'role_id' => $role_id
    ]);
    
    // ENVOYER UN MAIL À L'EMPLOYÉ
    
    $destinataire = $email;
    $sujet = "Votre compte employé - Vite & Gourmand";
    $message = "
Bonjour $prenom $nom,

Un compte employé a été créé pour vous sur la plateforme Vite & Gourmand.

Informations de connexion :
- Email : $email
- Mot de passe : Pour des raisons de sécurité, le mot de passe n'est pas communiqué par email. Veuillez contacter l'administrateur pour l'obtenir.

Vous pouvez vous connecter à l'adresse suivante :
→ https://vitegourmand.fr/connexion.php

Une fois connecté, vous aurez accès à votre espace employé pour gérer les commandes et les avis clients.

Bienvenue dans l'équipe !

L'équipe Vite & Gourmand
";
    
    $headers = "From: noreply@vitegourmand.fr\r\n";
    $headers .= "Reply-To: contact@vitegourmand.fr\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    @mail($destinataire, $sujet, $message, $headers);
    
    // Succès
    $_SESSION['succes_admin'] = "Le compte employé a été créé avec succès. Un email a été envoyé à $email.";
    header('Location: dashboard.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['erreurs_employe'] = ["Une erreur est survenue lors de la création du compte."];
    header('Location: creer-employe.php');
    exit;
}
?>