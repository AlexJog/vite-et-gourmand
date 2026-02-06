<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Vérifier que l'ID est dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: gestion-menus.php');
    exit;
}

$menu_id = (int)$_GET['id'];

// Récupérer les infos du menu
$sql = "SELECT * FROM menu WHERE menu_id = :menu_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['menu_id' => $menu_id]);
$menu = $stmt->fetch();

// Si le menu n'existe pas
if (!$menu) {
    $_SESSION['error_admin'] = "Ce menu n'existe pas.";
    header('Location: gestion-menus.php');
    exit;
}

// Supprimer le menu
try {
    if (!empty($menu['image_url']) && file_exists('../' . $menu['image_url'])) {
        unlink('../' . $menu['image_url']);
    }
    
    // Supprimer le menu de la BDD
    $sql_delete = "DELETE FROM menu WHERE menu_id = :menu_id";
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->execute(['menu_id' => $menu_id]);
    
    $_SESSION['succes_admin'] = "Le menu \"" . $menu['nom'] . "\" a été supprimé avec succès.";
    
} catch (PDOException $e) {
    $_SESSION['error_admin'] = "Erreur lors de la suppression du menu.";
}

// Redirection
header('Location: gestion-menus.php');
exit;
?>