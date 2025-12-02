<?php
require_once '../includes/config.php';

requireLogin();

$pageTitle = 'Finaliser ma commande';
$message = '';

$cartItems = getCartItems();
$finalTotal = getFinalTotal();

if (empty($cartItems)) {
    redirect('cart.php');
}

$user = getUserInfo();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adresse = trim($_POST['adresse'] ?? '');
    $ville = trim($_POST['ville'] ?? '');
    $codePostal = trim($_POST['code_postal'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    
    if (empty($adresse) || empty($ville) || empty($codePostal)) {
        $message = showError('Veuillez remplir tous les champs obligatoires');
    } elseif (!isValidPostalCode($codePostal)) {
        $message = showError('Code postal invalide');
    } else {
        try {
            $pdo->beginTransaction();
            
            $numeroCommande = generateOrderNumber();
            $adresseLivraison = "$adresse, $codePostal $ville";
            $dateLivraison = date('Y-m-d', strtotime('+3 days'));
            
            // Créer la commande
            $stmt = $pdo->prepare("
                INSERT INTO orders (user_id, numero_commande, montant_total, adresse_livraison, date_livraison_estimee)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$_SESSION['user_id'], $numeroCommande, $finalTotal, $adresseLivraison, $dateLivraison]);
            
            $orderId = $pdo->lastInsertId();
            
            // Ajouter les items
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantite, prix_unitaire)
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($cartItems as $item) {
                $stmt->execute([
                    $orderId,
                    $item['product']['id'],
                    $item['quantity'],
                    $item['price']
                ]);
                
                // Décrémenter le stock
                $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $updateStock->execute([$item['quantity'], $item['product']['id']]);
            }
            
            $pdo->commit();
            
            // Vider le panier
            clearCart();
            
            // Rediriger vers la page de confirmation
            $_SESSION['last_order_id'] = $orderId;
            redirect('order-success.php');
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = showError('Erreur lors de la création de la commande');
        }
    }
}

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 2rem 0;">
    <h1 class="mb-6">✅ Finaliser ma commande</h1>
    
    <?= $message ?>
    
    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 2rem;" class="checkout-grid">
        <!-- Formulaire -->
        <div>
            <div class="card mb-6">
                <div class="card-header">
                    <h2 style="font-size: 1.25rem;">📍 Adresse de livraison</h2>
                </div>
                
                <div class="card-body">
                    <form method="POST" id="checkoutForm">
                        <div class="form-group">
                            <label class="form-label" for="adresse">Adresse complète *</label>
                            <input 
                                type="text" 
                                id="adresse" 
                                name="adresse" 
                                class="form-input" 
                                required
                                value="<?= secure($user['adresse'] ?? '') ?>"
                                placeholder="10 Rue de la Paix"
                            >
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label class="form-label" for="ville">Ville *</label>
                                <input 
                                    type="text" 
                                    id="ville" 
                                    name="ville" 
                                    class="form-input" 
                                    required
                                    value="<?= secure($user['ville'] ?? '') ?>"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" for="code_postal">Code postal *</label>
                                <input 
                                    type="text" 
                                    id="code_postal" 
                                    name="code_postal" 
                                    class="form-input" 
                                    required
                                    maxlength="5"
                                    pattern="[0-9]{5}"
                                    value="<?= secure($user['code_postal'] ?? '') ?>"
                                >
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="telephone">Téléphone</label>
                            <input 
                                type="tel" 
                                id="telephone" 
                                name="telephone" 
                                class="form-input" 
                                value="<?= secure($user['telephone'] ?? '') ?>"
                                placeholder="06 12 34 56 78"
                            >
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2 style="font-size: 1.25rem;">💳 Mode de paiement</h2>
                </div>
                
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>ℹ️ Projet pédagogique</strong><br>
                        Le paiement est simulé dans ce projet BTS CIEL. Aucun paiement réel ne sera effectué.
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Récapitulatif -->
        <div>
            <div class="card" style="position: sticky; top: 2rem;">
                <div class="card-header">
                    <h2 style="font-size: 1.25rem;">📋 Votre commande</h2>
                </div>
                
                <div class="card-body">
                    <?php foreach ($cartItems as $item): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--gray-200);">
                            <div>
                                <div style="font-weight: 600;"><?= secure($item['product']['nom']) ?></div>
                                <div style="font-size: 0.875rem; color: var(--gray-600);">
                                    <?= $item['quantity'] ?> x <?= formatPrice($item['price']) ?>
                                </div>
                            </div>
                            <div style="font-weight: 600; color: var(--primary);">
                                <?= formatPrice($item['subtotal']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="padding: 1rem 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Sous-total</span>
                            <span><?= formatPrice(getCartTotal()) ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span>Livraison</span>
                            <?php if (getShippingCost() > 0): ?>
                                <span><?= formatPrice(getShippingCost()) ?></span>
                            <?php else: ?>
                                <span style="color: var(--success); font-weight: 600;">GRATUIT</span>
                            <?php endif; ?>
                        </div>
                        <?php if (getDiscount() > 0): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: var(--success);">
                                <span>Remise</span>
                                <span>-<?= formatPrice(getDiscount()) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <hr>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 1.5rem; margin-bottom: 1.5rem;">
                        <strong>Total</strong>
                        <strong style="color: var(--primary);"><?= formatPrice($finalTotal) ?></strong>
                    </div>
                    
                    <button type="submit" form="checkoutForm" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-check"></i> Valider la commande
                    </button>
                    
                    <a href="cart.php" class="btn btn-secondary btn-block mt-4">
                        <i class="fas fa-arrow-left"></i> Retour au panier
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .checkout-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once '../components/footer.php'; ?>