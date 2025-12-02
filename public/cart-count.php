<?php
/**
 * API pour récupérer le nombre d'articles dans le panier
 * Utilisé par JavaScript pour mise à jour dynamique
 */

require_once '../includes/config.php';

header('Content-Type: application/json');

echo json_encode([
    'count' => getCartCount(),
    'total' => getCartTotal()
]);