<?php
require_once '../includes/config.php';
require_once '../includes/check-auth.php';

// Vérifier que c'est bien un admin
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Rediriger vers la page employé (les admins ont les mêmes droits)
header('Location: ../employe/gestion-commandes.php');
exit;
?>