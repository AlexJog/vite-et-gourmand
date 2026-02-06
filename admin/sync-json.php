<?php
require_once '../includes/config.php';
require_once '../includes/json-config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Synchroniser les données
$resultat = synchroniserStatsJSON($pdo);

if ($resultat !== false) {
    $data = lireStatsJSON();
    $nb_commandes = count($data);
    $_SESSION['succes_admin'] = "✅ Synchronisation réussie : $nb_commandes commandes synchronisées dans la base JSON (NoSQL).";
} else {
    $_SESSION['error_admin'] = "❌ Erreur lors de la synchronisation.";
}

header('Location: statistiques.php');
exit;
?>