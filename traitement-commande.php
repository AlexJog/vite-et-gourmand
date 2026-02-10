<?php
require_once 'includes/config.php';
require_once 'includes/email-functions.php';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Vous devez être connecté pour commander.";
    header('Location: connexion.php');
    exit;
}

// Vérifier que le formulaire a été soumis en POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: menus.php');
    exit;
}

// Récupération des données
$menu_id = (int)$_POST['menu_id'];
$date_prestation = trim($_POST['date_prestation']);
$heure_livraison = trim($_POST['heure_livraison']);
$nombre_personnes = (int)$_POST['nombre_personnes'];
$adresse_livraison = trim($_POST['adresse_livraison']);
$code_postal = trim($_POST['code_postal']);
$ville = trim($_POST['ville']);
$commentaire = trim($_POST['commentaire']);
$hors_bordeaux = isset($_POST['hors_bordeaux']) ? 1 : 0;
$kilometres = isset($_POST['kilometres']) ? (float)$_POST['kilometres'] : 0;
$utilisateur_id = $_SESSION['user_id'];

$erreurs = [];

// VALIDATION DES DONNÉES

if (empty($menu_id) || empty($date_prestation) || empty($heure_livraison) || 
    empty($nombre_personnes) || empty($adresse_livraison) || empty($code_postal) || empty($ville)) {
    $erreurs[] = "Tous les champs obligatoires doivent être remplis.";
}

$date_min = date('Y-m-d', strtotime('+7 days'));
if ($date_prestation < $date_min) {
    $erreurs[] = "La date de prestation doit être au minimum 7 jours après aujourd'hui.";
}

// Récupérer les infos du menu pour validation
$sql_menu = "SELECT * FROM menu WHERE menu_id = :menu_id";
$stmt_menu = $pdo->prepare($sql_menu);
$stmt_menu->execute(['menu_id' => $menu_id]);
$menu = $stmt_menu->fetch();

if (!$menu) {
    $erreurs[] = "Ce menu n'existe pas.";
}

if ($menu && $menu['quantite_restante'] <= 0) {
    $erreurs[] = "Ce menu n'est plus disponible.";
}

if ($menu && $nombre_personnes < $menu['personne_minimum']) {
    $erreurs[] = "Le nombre minimum de personnes est de " . $menu['personne_minimum'] . ".";
}

// S'IL Y A DES ERREURS

if (!empty($erreurs)) {
    $_SESSION['erreurs_commande'] = $erreurs;
    header('Location: commander.php?menu=' . $menu_id);
    exit;
}

// CALCUL DU PRIX TOTAL

$prix_menu = $menu['prix_par_personne'] * $nombre_personnes;

$reduction = 0;
if ($nombre_personnes >= ($menu['personne_minimum'] + 5)) {
    $reduction = $prix_menu * 0.10;
    $prix_menu -= $reduction;
}

$frais_livraison = 0;
if ($hors_bordeaux) {
    $frais_livraison = 5 + ($kilometres * 0.59);
}

$prix_total = $prix_menu + $frais_livraison;

// ENREGISTREMENT EN BDD

try {
    $pdo->beginTransaction();
    
    $sql = "INSERT INTO commande (
                utilisateur_id, 
                menu_id, 
                date_prestation, 
                heure_livraison, 
                nombre_personnes, 
                prix_total,
                hors_bordeaux,
                kilometres,
                frais_livraison,
                reduction,
                adresse_livraison, 
                code_postal, 
                ville, 
                commentaire, 
                statut,
                date_commande
            ) VALUES (
                :utilisateur_id, 
                :menu_id, 
                :date_prestation, 
                :heure_livraison, 
                :nombre_personnes, 
                :prix_total,
                :hors_bordeaux,
                :kilometres,
                :frais_livraison,
                :reduction,
                :adresse_livraison, 
                :code_postal, 
                :ville, 
                :commentaire,
                'en attente',
                NOW()
            )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'utilisateur_id' => $utilisateur_id,
        'menu_id' => $menu_id,
        'date_prestation' => $date_prestation,
        'heure_livraison' => $heure_livraison,
        'nombre_personnes' => $nombre_personnes,
        'prix_total' => $prix_total,
        'hors_bordeaux' => $hors_bordeaux,
        'kilometres' => $kilometres,
        'frais_livraison' => $frais_livraison,
        'reduction' => $reduction,
        'adresse_livraison' => $adresse_livraison,
        'code_postal' => $code_postal,
        'ville' => $ville,
        'commentaire' => $commentaire
    ]);
    
    $sql_stock = "UPDATE menu 
                  SET quantite_restante = quantite_restante - 1 
                  WHERE menu_id = :menu_id";
    
    $stmt_stock = $pdo->prepare($sql_stock);
    $stmt_stock->execute(['menu_id' => $menu_id]);
    
    // ENVOYER UN MAIL DE CONFIRMATION
    
    // Récupérer les infos de l'utilisateur
    $sql_user = "SELECT email, prenom, nom FROM utilisateur WHERE utilisateur_id = :utilisateur_id";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute(['utilisateur_id' => $utilisateur_id]);
    $user = $stmt_user->fetch();
    
    $nom_menu = $menu['nom'];
    
    $message = "Bonjour {$user['prenom']} {$user['nom']},

Nous avons bien reçu votre commande !

Détails de votre commande :
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Menu : $nom_menu
Date de prestation : " . date('d/m/Y', strtotime($date_prestation)) . "
Heure de livraison : " . date('H:i', strtotime($heure_livraison)) . "
Nombre de personnes : $nombre_personnes

Adresse de livraison :
$adresse_livraison
$code_postal $ville

Prix détaillé :
- Prix menu : " . number_format(($prix_menu + $reduction), 2, ',', ' ') . " €";

    if ($reduction > 0) {
        $message .= "
- Réduction 10% : -" . number_format($reduction, 2, ',', ' ') . " €";
    }

    if ($frais_livraison > 0) {
        $message .= "
- Frais de livraison : " . number_format($frais_livraison, 2, ',', ' ') . " €";
    }

    $message .= "
TOTAL : " . number_format($prix_total, 2, ',', ' ') . " €

Votre commande sera traitée dans les plus brefs délais par notre équipe.
Vous recevrez une notification dès que votre commande sera validée.

Vous pouvez suivre l'état de votre commande dans votre espace client :
→ https://vite-et-gourmand-alex-a85135b73360.herokuapp.com/utilisateur/mes-commandes.php

Merci de votre confiance !

L'équipe Vite & Gourmand
05 56 00 00 00
contact@vitegourmand.fr";
    
    // Envoyer l'email avec Brevo
    envoyerEmail(
        $user['email'],
        $user['prenom'] . ' ' . $user['nom'],
        'Confirmation de votre commande - Vite & Gourmand',
        $message
    );
    
    // Valider la transaction
    $pdo->commit();
    
    $_SESSION['succes_commande'] = "Votre commande a bien été enregistrée ! Un email de confirmation vous a été envoyé.";
    header('Location: index.php');
    exit;
    
} catch (PDOException $e) {
    $pdo->rollBack();
    
    $_SESSION['erreurs_commande'] = ["Une erreur est survenue. Veuillez réessayer."];
    header('Location: commander.php?menu=' . $menu_id);
    exit;
}
?>