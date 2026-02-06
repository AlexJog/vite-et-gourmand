<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un employé ou un admin
if ($_SESSION['user_role'] !== 'employe' && $_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Vérifier que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: gestion-avis.php');
    exit;
}

// Récupérer les données
$avis_id = (int)$_POST['avis_id'];
$action = $_POST['action'];

// Déterminer le nouveau statut
if ($action === 'valider') {
    $nouveau_statut = 'validé';
} elseif ($action === 'refuser') {
    $nouveau_statut = 'refusé';
} else {
    header('Location: gestion-avis.php');
    exit;
}

try {
    // Mettre à jour le statut de l'avis
    $sql = "UPDATE avis SET statut = :statut WHERE avis_id = :avis_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'statut' => $nouveau_statut,
        'avis_id' => $avis_id
    ]);
    
    // Message de succès
    $_SESSION['succes_employe'] = "L'avis #$avis_id a été $nouveau_statut avec succès.";
    
} catch (PDOException $e) {
    $_SESSION['error_employe'] = "Une erreur est survenue lors du traitement de l'avis.";
}

// Redirection
header('Location: gestion-avis.php');
exit;
?>