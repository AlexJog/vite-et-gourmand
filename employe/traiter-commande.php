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
    header('Location: gestion-commandes.php');
    exit;
}

// Récupérer les données
$commande_id = (int)$_POST['commande_id'];

// Déterminer le nouveau statut
if (isset($_POST['nouveau_statut'])) {
    $nouveau_statut = $_POST['nouveau_statut'];
} elseif (isset($_POST['action'])) {
    // Compatibilité avec l'ancien système
    $action = $_POST['action'];
    if ($action === 'valider') {
        $nouveau_statut = 'accepté';
    } elseif ($action === 'refuser') {
        $nouveau_statut = 'refusée';
    } elseif ($action === 'livrer') {
        $nouveau_statut = 'livré';
    } else {
        header('Location: gestion-commandes.php');
        exit;
    }
} else {
    header('Location: gestion-commandes.php');
    exit;
}

$pret_materiel = isset($_POST['pret_materiel']) ? 1 : 0;
$restitution_materiel = isset($_POST['restitution_materiel']) ? 1 : 0;

try {
    if ($pret_materiel) {
        $sql = "UPDATE commande 
                SET statut = :statut, pret_materiel = 1 
                WHERE commande_id = :commande_id";
    } elseif ($restitution_materiel) {
        $sql = "UPDATE commande 
                SET statut = :statut, restitution_materiel = 1 
                WHERE commande_id = :commande_id";
    } else {
        $sql = "UPDATE commande 
                SET statut = :statut 
                WHERE commande_id = :commande_id";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'statut' => $nouveau_statut,
        'commande_id' => $commande_id
    ]);

    // SI COMMANDE TERMINÉE : ENVOYER MAIL POUR AVIS

    if ($nouveau_statut === 'terminée') {
        $sql_info = "SELECT u.email, u.prenom, u.nom, m.nom AS menu_nom, c.commande_id
                     FROM commande c
                     INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
                     INNER JOIN menu m ON c.menu_id = m.menu_id
                     WHERE c.commande_id = :commande_id";
        
        $stmt_info = $pdo->prepare($sql_info);
        $stmt_info->execute(['commande_id' => $commande_id]);
        $info = $stmt_info->fetch();
        
        if ($info) {
            $destinataire = $info['email'];
            $sujet = "Votre commande est terminée - Laissez-nous un avis !";
            $message = "
Bonjour {$info['prenom']} {$info['nom']},

Votre commande du menu \"{$info['menu_nom']}\" est maintenant terminée !

Nous espérons que tout s'est bien passé et que vous avez apprécié nos services.

Nous serions ravis d'avoir votre retour ! Connectez-vous à votre espace client pour laisser un avis sur votre expérience :
→ https://vitegourmand.fr/utilisateur/mes-commandes.php

Votre avis nous aide à améliorer nos services et aide d'autres clients à faire leur choix.

Merci de votre confiance !

L'équipe Vite & Gourmand
05 56 00 00 00
contact@vitegourmand.fr
";
            
            $headers = "From: noreply@vitegourmand.fr\r\n";
            $headers .= "Reply-To: contact@vitegourmand.fr\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            @mail($destinataire, $sujet, $message, $headers);
        }
    }

    // SI ATTENTE MATÉRIEL : ENVOYER MAIL D'AVERTISSEMENT

    if ($nouveau_statut === 'attente matériel') {
        $sql_info = "SELECT u.email, u.prenom, u.nom
                     FROM commande c
                     INNER JOIN utilisateur u ON c.utilisateur_id = u.utilisateur_id
                     WHERE c.commande_id = :commande_id";
        
        $stmt_info = $pdo->prepare($sql_info);
        $stmt_info->execute(['commande_id' => $commande_id]);
        $info = $stmt_info->fetch();
        
        if ($info) {
            $destinataire = $info['email'];
            $sujet = "Important : Restitution du matériel - Vite & Gourmand";
            $message = "
Bonjour {$info['prenom']} {$info['nom']},

Votre commande a bien été livrée. Nous vous rappelons que du matériel vous a été prêté.

⚠️ IMPORTANT : Vous disposez de 10 jours ouvrés pour nous restituer le matériel prêté.

Passé ce délai, des frais de 600€ vous seront facturés conformément à nos conditions générales de vente.

Pour organiser la restitution du matériel, merci de nous contacter :
- Par téléphone : 05 56 00 00 00
- Par email : contact@vitegourmand.fr

Merci de votre compréhension.

L'équipe Vite & Gourmand
";
            
            $headers = "From: noreply@vitegourmand.fr\r\n";
            $headers .= "Reply-To: contact@vitegourmand.fr\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
            
            @mail($destinataire, $sujet, $message, $headers);
        }
    }

    // Message de succès
    $_SESSION['succes_employe'] = "La commande #$commande_id a été mise à jour : \"$nouveau_statut\".";
    
} catch (PDOException $e) {
    $_SESSION['error_employe'] = "Une erreur est survenue lors de la mise à jour.";
}

// Redirection
header('Location: gestion-commandes.php');
exit;
?>