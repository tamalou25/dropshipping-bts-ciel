# 🛍️ Site Dropshipping BTS CIEL

## 📋 Description

Site e-commerce de dropshipping professionnel développé en **PHP & MySQL** dans le cadre du projet fil rouge BTS CIEL.

### ✨ Fonctionnalités principales

- 🏠 **Page d'accueil moderne** avec hero section et promotions
- 🛒 **Catalogue produits** avec filtres et recherche
- 💳 **Système de panier** complet avec calculs automatiques
- 📦 **Gestion des commandes** pour les clients
- 👤 **Authentification** sécurisée (inscription/connexion)
- 👑 **Panel administrateur** pour gérer produits, commandes et utilisateurs
- 🔒 **Sécurité renforcée** (requêtes préparées, protection XSS/CSRF)
- 📱 **Design responsive** compatible mobile/tablette/desktop

---

## 🚀 Installation rapide

### Prérequis

- XAMPP/WAMP/MAMP (Apache + MySQL + PHP 7.4+)
- Navigateur web moderne (Chrome, Firefox, Edge)

### Étapes d'installation

#### 1️⃣ Cloner le dépôt

```bash
git clone https://github.com/tamalou25/dropshipping-bts-ciel.git
cd dropshipping-bts-ciel
```

#### 2️⃣ Configurer la base de données

1. Ouvre **phpMyAdmin** : `http://localhost/phpmyadmin`
2. Crée une nouvelle base de données nommée `dropshipping_bts`
3. Importe le fichier SQL :
   - Clique sur la base `dropshipping_bts`
   - Onglet **Importer**
   - Sélectionne `database/schema.sql`
   - Clique sur **Exécuter**

#### 3️⃣ Configurer les identifiants

Modifie le fichier `includes/config.php` avec tes paramètres MySQL :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dropshipping_bts');
define('DB_USER', 'root');           // Ton utilisateur MySQL
define('DB_PASS', '');               // Ton mot de passe MySQL
```

#### 4️⃣ Déplacer les fichiers

Copie tous les fichiers dans le dossier web de ton serveur :

**Windows (XAMPP)** :
```
C:\xampp\htdocs\dropshipping-bts-ciel\
```

**Mac (MAMP)** :
```
/Applications/MAMP/htdocs/dropshipping-bts-ciel/
```

**Linux** :
```
/var/www/html/dropshipping-bts-ciel/
```

#### 5️⃣ Lancer le site

Accède au site via ton navigateur :
```
http://localhost/dropshipping-bts-ciel/public/
```

---

## 🧪 Comptes de test

### Client
- **Email** : `client@test.fr`
- **Mot de passe** : `test1234`

### Administrateur
- **Email** : `admin@test.fr`
- **Mot de passe** : `admin1234`
- **Panel admin** : `http://localhost/dropshipping-bts-ciel/admin/`

---

## 📁 Structure du projet

```
dropshipping-bts-ciel/
├── database/
│   └── schema.sql          # Script SQL de création BDD
├── includes/
│   ├── config.php          # Configuration générale
│   ├── db.php              # Connexion PDO MySQL
│   └── functions.php       # Fonctions utilitaires
├── assets/
│   ├── css/
│   │   └── style.css       # Styles CSS
│   └── js/
│       └── main.js         # JavaScript interactif
├── components/
│   ├── header.php          # En-tête du site
│   ├── nav.php             # Navigation
│   └── footer.php          # Pied de page
├── public/
│   ├── index.php           # Page d'accueil
│   ├── products.php        # Liste des produits
│   ├── product-detail.php  # Détail d'un produit
│   ├── cart.php            # Panier d'achats
│   ├── checkout.php        # Validation commande
│   ├── login.php           # Connexion
│   ├── register.php        # Inscription
│   └── orders.php          # Historique commandes
└── admin/
    ├── dashboard.php       # Tableau de bord
    ├── products.php        # Gestion produits
    ├── orders.php          # Gestion commandes
    └── users.php           # Gestion utilisateurs
```

---

## 🛡️ Sécurité

Ce projet implémente les meilleures pratiques de sécurité :

- ✅ **Requêtes préparées** (PDO) pour éviter les injections SQL
- ✅ **Hashage des mots de passe** avec `password_hash()` et `password_verify()`
- ✅ **Protection XSS** via `htmlspecialchars()`
- ✅ **Sessions sécurisées** pour l'authentification
- ✅ **Validation des entrées** côté serveur
- ✅ **Contrôle d'accès** pour les pages admin

---

## 📚 Technologies utilisées

- **Backend** : PHP 7.4+ (programmation orientée fonctionnelle)
- **Base de données** : MySQL 5.7+ avec PDO
- **Frontend** : HTML5, CSS3 (Flexbox/Grid), JavaScript ES6+
- **Architecture** : MVC simplifié, multi-fichiers
- **Sécurité** : Sessions PHP, requêtes préparées, validation

---

## 🎓 Projet pédagogique

Ce projet couvre **l'intégralité du fil rouge BTS CIEL** :

### Séance 1 : Fondamentaux PHP
- ✅ Variables et types de données
- ✅ Structures conditionnelles
- ✅ Intégration PHP dans HTML

### Séance 2 : Tableaux et boucles
- ✅ Tableaux multidimensionnels
- ✅ Boucles foreach
- ✅ Fonctions personnalisées

### Séance 3 : Formulaires et sessions
- ✅ Traitement GET/POST
- ✅ Sessions utilisateur
- ✅ Gestion du panier

### Séance 4 : Base de données
- ✅ Conception BDD relationnelle
- ✅ Requêtes SQL (CRUD)
- ✅ PDO et requêtes préparées

---

## 🐛 Débogage

### Erreur de connexion à la base de données

```
PDOException: SQLSTATE[HY000] [1045] Access denied
```

**Solution** : Vérifie tes identifiants MySQL dans `includes/config.php`

### Page blanche

**Solution** : Active l'affichage des erreurs temporairement dans `includes/config.php` :

```php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Panier vide après ajout

**Solution** : Vérifie que les sessions PHP sont activées dans `php.ini`

---

## 📝 TODO / Améliorations possibles

- [ ] Intégration API de paiement (Stripe/PayPal)
- [ ] Système de notation produits
- [ ] Recherche avancée avec filtres
- [ ] Export commandes en PDF
- [ ] Envoi d'emails de confirmation
- [ ] Intégration API de tracking livraison
- [ ] Tableau de bord avec graphiques
- [ ] Multi-langues (i18n)

---

## 👨‍💻 Auteur

**tamalou25** - BTS CIEL - Alternance Enovacom  
📧 Contact : [GitHub](https://github.com/tamalou25)

---

## 📄 Licence

Ce projet est développé dans un cadre pédagogique (BTS CIEL).  
Libre d'utilisation pour l'apprentissage.

---

## 🙏 Remerciements

- Formation BTS CIEL
- Inspiration design : sites dropshipping modernes 2025
- Documentation PHP officielle

---

**⭐ N'oublie pas de mettre une étoile si ce projet t'aide !**