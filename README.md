# 🍽️ Vite & Gourmand - Installation en local

## 📖 Présentation
Vite & Gourmand est un site web de traiteur réalisé dans le cadre de l’ECF (Évaluation en Cours de Formation) du Titre Professionnel Développeur Web et Web Mobile.

Le site permet de consulter des menus, passer des commandes et gérer l’activité selon différents rôles (client, employé, administrateur).

**Technologies utilisées :** PHP, MySQL, HTML, CSS, JavaScript

---

## 📋 Prérequis
Pour installer et lancer le projet en local, il est nécessaire d’avoir :
- PHP (version 8 minimum)
- MySQL
- Un serveur local (XAMPP, WAMP ou serveur PHP intégré)
- phpMyAdmin (recommandé)

---

## 🚀 Installation du projet

### 1. Récupération du projet
Téléchargez le projet ou clonez-le dans votre dossier de travail :
```bash
git clone https://github.com/AlexJog/vite-et-gourmand.git
cd vite-et-gourmand
```

---

### 2. Création de la base de données
1. Ouvrez phpMyAdmin
2. Créez une base de données nommée `vitegourmand`
3. Importez le fichier `vitegourmand.sql` fourni dans le projet

---

### 3. Configuration de la connexion à la base de données
Ouvrez le fichier `includes/config.php` et vérifiez les informations de connexion:
```php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'vitegourmand';
$username = 'root';        // Votre utilisateur MySQL
$password = '';            // Votre mot de passe MySQL
```

---

### 4. Dossiers nécessaires
Vérifiez que les dossiers suivants existent dans le projet:
- `data/` (stockage des statistiques)
- `assets/images/menus/` (images des menus)

Si ce n'est pas le cas, créez-les manuellement.

---

### 5. Lancer le projet

**Option 1:** Serveur PHP intégré
Dans le dossier du projet, lancez:
```bash
php -S localhost:3000
```
Puis ouvrez votre navigateur et allez sur : `http://localhost:3000`

**Option 2:** XAMPP /WAMP
Placez le dossier du projet dans `htdocs` ou `www`
Puis accédez a l'adresse suivante : `http://localhost/vite-et-gourmand`

---

## 👤 Comptes de test

**Administrateur**
- Email: `admin@vitegourmand.fr`
- Mot de passe: `Test12345.`

**Employé**
- Email: `employe@vitegourmand.fr`
- Mot de passe: `Test12345.`

**Client**
- Email: `client@client.fr`
- Mot de passe: `Test12345.`

## ⚠️ Remarques importantes
- Les emails ne sont pas envoyés en environnement local
- Les statistiques sont accessibles depuis l'espace administrateur

## ✅ Vérification de l'installation
L'installation est considérée comme fonctionnelle si :
- La page d'accueil s'affiche correctement
- La connexion avec un compte de test fonctionne
- Les menus et les commandes sont accessibles selon le rôle utilisateur

*Installation réalisée avec succès ? Vous pouvez commencer à tester le site ! 🥳*