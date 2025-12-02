<?php
/**
 * Fonctions utilitaires du site
 * 
 * @author tamalou25
 * @version 1.0
 */

// ============================================
// FONCTIONS DE SÉCURITÉ
// ============================================

/**
 * Sécurise une chaîne pour l'affichage HTML
 * Prévient les attaques XSS
 */
function secure($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifie si l'utilisateur est connecté
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Vérifie si l'utilisateur est administrateur
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirige vers la page de connexion si non connecté
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Redirige vers l'accueil si non admin
 */
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        redirect('../public/index.php');
        exit;
    }
}

/**
 * Redirige vers une URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Génère un token CSRF
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Vérifie le token CSRF
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// ============================================
// FONCTIONS D'AFFICHAGE
// ============================================

/**
 * Formate un prix avec le symbole euro
 */
function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . ' €';
}

/**
 * Formate une date au format français
 */
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

/**
 * Formate une date et heure
 */
function formatDateTime($datetime) {
    return date('d/m/Y à H:i', strtotime($datetime));
}

/**
 * Affiche un message d'erreur formaté
 */
function showError($message) {
    return '<div class="alert alert-error">⚠️ ' . secure($message) . '</div>';
}

/**
 * Affiche un message de succès formaté
 */
function showSuccess($message) {
    return '<div class="alert alert-success">✅ ' . secure($message) . '</div>';
}

/**
 * Affiche un message d'information
 */
function showInfo($message) {
    return '<div class="alert alert-info">ℹ️ ' . secure($message) . '</div>';
}

// ============================================
// FONCTIONS PANIER
// ============================================

/**
 * Initialise le panier en session
 */
function initCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Ajoute un produit au panier
 */
function addToCart($productId, $quantity = 1) {
    initCart();
    
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

/**
 * Met à jour la quantité d'un produit dans le panier
 */
function updateCartQuantity($productId, $quantity) {
    initCart();
    
    if ($quantity <= 0) {
        removeFromCart($productId);
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

/**
 * Retire un produit du panier
 */
function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

/**
 * Vide le panier
 */
function clearCart() {
    $_SESSION['cart'] = [];
}

/**
 * Récupère le nombre d'articles dans le panier
 */
function getCartCount() {
    initCart();
    return array_sum($_SESSION['cart']);
}

/**
 * Récupère les articles du panier avec leurs informations
 */
function getCartItems() {
    global $pdo;
    initCart();
    
    if (empty($_SESSION['cart'])) {
        return [];
    }
    
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    $stmt = $pdo->prepare("
        SELECT * FROM products 
        WHERE id IN ($placeholders) AND actif = TRUE
    ");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();
    
    $items = [];
    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $price = $product['prix_promo'] ?? $product['prix'];
        
        $items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'price' => $price,
            'subtotal' => $price * $quantity
        ];
    }
    
    return $items;
}

/**
 * Calcule le total du panier
 */
function getCartTotal() {
    $items = getCartItems();
    $total = 0;
    
    foreach ($items as $item) {
        $total += $item['subtotal'];
    }
    
    return $total;
}

/**
 * Calcule les frais de port
 */
function getShippingCost() {
    $total = getCartTotal();
    return $total >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_COST;
}

/**
 * Calcule la remise applicable
 */
function getDiscount() {
    $total = getCartTotal();
    return $total >= DISCOUNT_THRESHOLD ? $total * (DISCOUNT_RATE / 100) : 0;
}

/**
 * Calcule le montant final à payer
 */
function getFinalTotal() {
    return getCartTotal() - getDiscount() + getShippingCost();
}

// ============================================
// FONCTIONS BASE DE DONNÉES
// ============================================

/**
 * Récupère les informations d'un utilisateur
 */
function getUserInfo($userId = null) {
    global $pdo;
    
    $id = $userId ?? $_SESSION['user_id'] ?? null;
    
    if (!$id) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Génère un numéro de commande unique
 */
function generateOrderNumber() {
    return 'CMD-' . date('Y') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

/**
 * Calcule le pourcentage de réduction
 */
function calculateDiscountPercent($originalPrice, $discountPrice) {
    if ($originalPrice <= 0) return 0;
    return round((($originalPrice - $discountPrice) / $originalPrice) * 100);
}

// ============================================
// FONCTIONS VALIDATION
// ============================================

/**
 * Valide une adresse email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valide un mot de passe (mini 8 caractères)
 */
function isValidPassword($password) {
    return strlen($password) >= 8;
}

/**
 * Valide un code postal français
 */
function isValidPostalCode($postalCode) {
    return preg_match('/^[0-9]{5}$/', $postalCode);
}

/**
 * Valide un numéro de téléphone français
 */
function isValidPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return preg_match('/^0[1-9][0-9]{8}$/', $phone);
}