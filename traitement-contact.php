<?php
session_start();
require_once 'includes/config.php';

// Vérifier que le formulaire a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

// Récupération et nettoyage des données
$email = trim($_POST['email']);
$titre = trim($_POST['titre']);
$message = trim($_POST['message']);

$erreurs = [];

// VALIDATION DES DONNÉES

if (empty($email) || empty($titre) || empty($message)) {
    $erreurs[] = "Tous les champs sont obligatoires.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}

if (!empty($titre) && strlen($titre) > 255) {
    $erreurs[] = "Le titre ne peut pas dépasser 255 caractères.";
}

if (!empty($message) && strlen($message) < 10) {
    $erreurs[] = "Le message doit contenir au moins 10 caractères.";
}

// S'IL Y A DES ERREURS

if (!empty($erreurs)) {
    $_SESSION['erreurs_contact'] = $erreurs;
    $_SESSION['form_contact'] = $_POST;
    header('Location: contact.php');
    exit;
}

// TOUT EST BON : ENREGISTRER EN BDD

try {
    $sql = "INSERT INTO contact (email, titre, message) 
            VALUES (:email, :titre, :message)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'email' => $email,
        'titre' => $titre,
        'message' => $message
    ]);
    
    $_SESSION['succes_contact'] = "Votre message a bien été envoyé ! Nous vous répondrons dans les plus brefs délais.";
    header('Location: contact.php');
    exit;
    
} catch (PDOException $e) {
    $_SESSION['erreurs_contact'] = ["Une erreur est survenue. Veuillez réessayer."];
    header('Location: contact.php');
    exit;
}
?>