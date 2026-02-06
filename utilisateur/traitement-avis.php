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
    header('Location: mes-commandes.php');
    exit;
}

// Récupérer les données
$commande_id = (int)$_POST['commande_id'];
$note = (int)$_POST['note'];
$commentaire = trim($_POST['commentaire']);
$utilisateur_id = $_SESSION['user_id'];

$erreurs = [];

if ($note < 1 || $note > 5) {
    $erreurs[] = "La note doit être entre 1 et 5.";
}

if (empty($commentaire)) {
    $erreurs[] = "Le commentaire est obligatoire.";
}

if (strlen($commentaire) < 10) {
    $erreurs[] = "Le commentaire doit contenir au minimum 10 caractères.";
}

// Vérifier que la commande appartient bien à l'utilisateur
$sql_check = "SELECT commande_id FROM commande 
              WHERE commande_id = :commande_id 
              AND utilisateur_id = :utilisateur_id 
              AND statut = 'terminée'";

$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([
    'commande_id' => $commande_id,
    'utilisateur_id' => $utilisateur_id
]);

if (!$stmt_check->fetch()) {
    $erreurs[] = "Cette commande n'est pas disponible pour laisser un avis.";
}

// S'il y a des erreurs
if (!empty($erreurs)) {
    $_SESSION['erreurs_avis'] = $erreurs;
    header('Location: laisser-avis.php?commande_id=' . $commande_id);
    exit;
}

// Insérer l'avis en BDD
try {
    $sql = "INSERT INTO avis (utilisateur_id, commande_id, note, commentaire, statut) 
            VALUES (:utilisateur_id, :commande_id, :note, :commentaire, 'en attente')";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'utilisateur_id' => $utilisateur_id,
        'commande_id' => $commande_id,
        'note' => $note,
        'commentaire' => $commentaire
    ]);
    
    $_SESSION['succes_user'] = "Votre avis a été enregistré ! Il sera visible après validation par notre équipe.";
    
} catch (PDOException $e) {
    $_SESSION['error_user'] = "Une erreur est survenue lors de l'enregistrement de votre avis.";
}

header('Location: mes-commandes.php');
exit;
?>