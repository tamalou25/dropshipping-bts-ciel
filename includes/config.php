<?php
/**
 * Configuration générale du site
 * 
 * @author tamalou25
 * @version 1.0
 * @date 2025-12-02
 */

// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============================================
// CONFIGURATION BASE DE DONNÉES
// ============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'dropshipping_bts');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ============================================
// CONFIGURATION SITE
// ============================================

define('SITE_NAME', 'TechShop Pro');
define('SITE_URL', 'http://localhost/dropshipping-bts-ciel');
define('SITE_EMAIL', 'contact@techshop.fr');

// ============================================
// CHEMINS
// ============================================

define('ROOT_PATH', dirname(__DIR__));
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('ADMIN_PATH', ROOT_PATH . '/admin');
define('ASSETS_PATH', ROOT_PATH . '/assets');

// ============================================
// CONFIGURATION SÉCURITÉ
// ============================================

// Durée de vie de la session (en secondes) - 2 heures
define('SESSION_LIFETIME', 7200);

// Clé de sécurité pour les tokens CSRF (change cette valeur !)
define('CSRF_SECRET', 'change_this_secret_key_123456789');

// ============================================
// CONFIGURATION COMMANDES
// ============================================

define('TVA_RATE', 20); // Taux de TVA en %
define('FREE_SHIPPING_THRESHOLD', 50); // Montant pour livraison gratuite
define('SHIPPING_COST', 4.90); // Frais de port standard
define('DISCOUNT_THRESHOLD', 100); // Seuil pour remise
define('DISCOUNT_RATE', 10); // Pourcentage de remise

// ============================================
// CONFIGURATION AFFICHAGE
// ============================================

define('PRODUCTS_PER_PAGE', 12);
define('ORDERS_PER_PAGE', 10);

// ============================================
// MODE DÉBOGAGE
// ============================================

// Active l'affichage des erreurs (METTRE À FALSE EN PRODUCTION)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

// ============================================
// TIMEZONE
// ============================================

date_default_timezone_set('Europe/Paris');

// ============================================
// AUTOLOAD DES FICHIERS ESSENTIELS
// ============================================

require_once INCLUDES_PATH . '/db.php';
require_once INCLUDES_PATH . '/functions.php';