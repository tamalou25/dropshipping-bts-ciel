<?php
require_once '../includes/config.php';

$pageTitle = 'Panier';
$message = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    
    switch ($action) {
        case 'add':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            if ($productId && $quantity > 0) {
                addToCart($productId, $quantity);
                $message = showSuccess('Produit ajouté au panier !');
            }
            break;
            
        case 'update':
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            if ($productId) {
                updateCartQuantity($productId, $quantity);
                $message = showSuccess('Quantité mise à jour !');
            }
            break;
            
        case 'remove':
            if ($productId) {
                removeFromCart($productId);
                $message = showSuccess('Produit retiré du panier');
            }
            break;
            
        case 'clear':
            clearCart();
            $message = showSuccess('Panier vidé');
            break;
    }
}

$cartItems = getCartItems();
$cartTotal = getCartTotal();
$shippingCost = getShippingCost();
$discount = getDiscount();
$finalTotal = getFinalTotal();

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 2rem 0;">
    <h1 class="mb-6">🛒 Mon panier</h1>
    
    <?= $message ?>
    
    <?php if (empty($cartItems)): ?>
        <div class="card">
            <div class="card-body text-center" style="padding: 4rem 2rem;">
                <div style="font-size: 5rem; opacity: 0.3; margin-bottom: 1rem;">🛍️</div>
                <h2 style="margin-bottom: 1rem;">Votre panier est vide</h2>
                <p class="text-muted" style="margin-bottom: 2rem;">Découvrez nos produits et ajoutez-les à votre panier</p>
                <a href="products.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-cart"></i> Découvrir nos produits
                </a>
            </div>
        </div>
    <?php else: ?>
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 2rem;" class="cart-grid">
            <!-- Liste des produits -->
            <div>
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($cartItems as $item): ?>
                            <?php $product = $item['product']; ?>
                            <div style="display: flex; gap: 1.5rem; padding: 1.5rem; border-bottom: 1px solid var(--gray-200);" class="cart-item">
                                <img 
                                    src="<?= secure($product['image_url']) ?>" 
                                    alt="<?= secure($product['nom']) ?>" 
                                    style="width: 120px; height: 120px; object-fit: cover; border-radius: var(--radius-md);"
                                >
                                
                                <div style="flex: 1;">
                                    <h3 style="font-size: 1.125rem; margin-bottom: 0.5rem;">
                                        <a href="product-detail.php?id=<?= $product['id'] ?>" style="color: var(--gray-900);">
                                            <?= secure($product['nom']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-muted" style="font-size: 0.875rem;"><?= secure($product['description']) ?></p>
                                    
                                    <div style="margin-top: 1rem; display: flex; align-items: center; gap: 1rem;">
                                        <div>
                                            <label for="qty_<?= $product['id'] ?>" style="font-size: 0.875rem; color: var(--gray-600);">Quantité:</label>
                                            <input 
                                                type="number" 
                                                id="qty_<?= $product['id'] ?>" 
                                                class="form-input quantity-input" 
                                                data-product-id="<?= $product['id'] ?>" 
                                                value="<?= $item['quantity'] ?>" 
                                                min="1"
                                                max="<?= $product['stock'] ?>"
                                                style="width: 80px; padding: 0.5rem;"
                                            >
                                        </div>
                                        
                                        <form method="POST" style="margin-left: auto;">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <input type="hidden" name="action" value="remove">
                                            <button type="submit" class="btn btn-error btn-sm">
                                                <i class="fas fa-trash"></i> Retirer
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <div style="text-align: right;">
                                    <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">
                                        <?= formatPrice($item['price']) ?> x <?= $item['quantity'] ?>
                                    </div>
                                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">
                                        <?= formatPrice($item['subtotal']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="card-footer">
                        <form method="POST">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-secondary btn-delete">
                                <i class="fas fa-trash-alt"></i> Vider le panier
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Récapitulatif -->
            <div>
                <div class="card" style="position: sticky; top: 2rem;">
                    <div class="card-header">
                        <h2 style="font-size: 1.25rem;">📋 Récapitulatif</h2>
                    </div>
                    
                    <div class="card-body">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span>Sous-total</span>
                            <strong><?= formatPrice($cartTotal) ?></strong>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                            <span>Livraison</span>
                            <?php if ($shippingCost > 0): ?>
                                <strong><?= formatPrice($shippingCost) ?></strong>
                            <?php else: ?>
                                <strong style="color: var(--success);">GRATUIT</strong>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($discount > 0): ?>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem; color: var(--success);">
                                <span>Remise (<?= DISCOUNT_RATE ?>%)</span>
                                <strong>-<?= formatPrice($discount) ?></strong>
                            </div>
                        <?php endif; ?>
                        
                        <hr style="margin: 1rem 0;">
                        
                        <div style="display: flex; justify-content: space-between; font-size: 1.5rem; margin-bottom: 1.5rem;">
                            <strong>Total</strong>
                            <strong style="color: var(--primary);"><?= formatPrice($finalTotal) ?></strong>
                        </div>
                        
                        <?php if ($cartTotal < FREE_SHIPPING_THRESHOLD): ?>
                            <div class="alert alert-info" style="font-size: 0.875rem;">
                                Plus que <?= formatPrice(FREE_SHIPPING_THRESHOLD - $cartTotal) ?> pour la livraison gratuite !
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($cartTotal < DISCOUNT_THRESHOLD): ?>
                            <div class="alert alert-info" style="font-size: 0.875rem;">
                                Plus que <?= formatPrice(DISCOUNT_THRESHOLD - $cartTotal) ?> pour <?= DISCOUNT_RATE ?>% de remise !
                            </div>
                        <?php endif; ?>
                        
                        <a href="checkout.php" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-credit-card"></i> Passer la commande
                        </a>
                        
                        <a href="products.php" class="btn btn-secondary btn-block mt-4">
                            <i class="fas fa-arrow-left"></i> Continuer mes achats
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
@media (max-width: 768px) {
    .cart-grid {
        grid-template-columns: 1fr !important;
    }
    
    .cart-item {
        flex-direction: column !important;
    }
}
</style>

<?php require_once '../components/footer.php'; ?>