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
    header('Location: gestion-employes.php');
    exit;
}

// Récupérer les données
$employe_id = (int)$_POST['employe_id'];
$action = $_POST['action'];

// Vérifier que c'est bien un employé
$sql_check = "SELECT u.*, r.libelle 
              FROM utilisateur u
              INNER JOIN role r ON u.role_id = r.role_id
              WHERE u.utilisateur_id = :employe_id 
              AND r.libelle = 'employe'";

$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute(['employe_id' => $employe_id]);
$employe = $stmt_check->fetch();

if (!$employe) {
    $_SESSION['error_admin'] = "Cet employé n'existe pas.";
    header('Location: gestion-employes.php');
    exit;
}

// Activer ou désactiver
try {
    if ($action === 'desactiver') {
        $sql = "UPDATE utilisateur SET actif = 0 WHERE utilisateur_id = :employe_id";
        $message = "Le compte de " . $employe['prenom'] . ' ' . $employe['nom'] . " a été désactivé.";
    } elseif ($action === 'activer') {
        $sql = "UPDATE utilisateur SET actif = 1 WHERE utilisateur_id = :employe_id";
        $message = "Le compte de " . $employe['prenom'] . ' ' . $employe['nom'] . " a été réactivé.";
    } else {
        header('Location: gestion-employes.php');
        exit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['employe_id' => $employe_id]);
    
    $_SESSION['succes_admin'] = $message;
    
} catch (PDOException $e) {
    $_SESSION['error_admin'] = "Une erreur est survenue.";
}

header('Location: gestion-employes.php');
exit;
?>