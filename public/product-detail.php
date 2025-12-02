<?php
require_once '../includes/config.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$productId) {
    redirect('products.php');
}

// Récupérer le produit
$stmt = $pdo->prepare("SELECT * FROM v_products_full WHERE id = ? AND actif = TRUE");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    redirect('products.php');
}

$pageTitle = $product['nom'];

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 2rem 0;">
    <!-- Breadcrumb -->
    <nav style="margin-bottom: 2rem;">
        <a href="index.php" style="color: var(--gray-600);">Accueil</a>
        <span style="margin: 0 0.5rem; color: var(--gray-400);"> /</span>
        <a href="products.php" style="color: var(--gray-600);">Produits</a>
        <span style="margin: 0 0.5rem; color: var(--gray-400);"> /</span>
        <span style="color: var(--primary); font-weight: 600;"><?= secure($product['nom']) ?></span>
    </nav>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem;" class="product-detail-grid">
        <!-- Image -->
        <div>
            <div style="position: relative; border-radius: var(--radius-xl); overflow: hidden; background: white; box-shadow: var(--shadow-lg);">
                <?php if ($product['prix_promo']): ?>
                    <div class="product-badge" style="top: 1.5rem; right: 1.5rem; font-size: 1rem; padding: 0.5rem 1rem;">
                        -<?= $product['pourcentage_reduction'] ?>%
                    </div>
                <?php endif; ?>
                <img src="<?= secure($product['image_url']) ?>" alt="<?= secure($product['nom']) ?>" style="width: 100%; height: 500px; object-fit: cover;">
            </div>
        </div>
        
        <!-- Informations -->
        <div class="card">
            <div class="card-body">
                <div style="color: var(--primary); font-weight: 600; margin-bottom: 0.5rem;">
                    <?= secure($product['categorie_nom'] ?? 'Non catégorisé') ?>
                </div>
                
                <h1 style="font-size: 2rem; margin-bottom: 1rem;"><?= secure($product['nom']) ?></h1>
                
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <?php if ($product['prix_promo']): ?>
                        <span style="font-size: 2.5rem; font-weight: 700; color: var(--primary);">
                            <?= formatPrice($product['prix_promo']) ?>
                        </span>
                        <span style="font-size: 1.5rem; color: var(--gray-500); text-decoration: line-through;">
                            <?= formatPrice($product['prix']) ?>
                        </span>
                    <?php else: ?>
                        <span style="font-size: 2.5rem; font-weight: 700; color: var(--primary);">
                            <?= formatPrice($product['prix']) ?>
                        </span>
                    <?php endif; ?>
                </div>
                
                <hr style="margin: 1.5rem 0; border: none; border-top: 1px solid var(--gray-200);">
                
                <div style="margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.125rem; margin-bottom: 0.75rem;">📝 Description</h3>
                    <p style="color: var(--gray-700); line-height: 1.8;"><?= secure($product['description']) ?></p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem; padding: 1rem; background: var(--gray-50); border-radius: var(--radius-md);">
                    <div>
                        <div style="color: var(--gray-600); font-size: 0.875rem;">Référence</div>
                        <div style="font-weight: 600;"><?= secure($product['sku']) ?></div>
                    </div>
                    <div>
                        <div style="color: var(--gray-600); font-size: 0.875rem;">Disponibilité</div>
                        <?php if ($product['stock'] > 0): ?>
                            <div style="color: var(--success); font-weight: 600;">
                                ✅ En stock (<?= $product['stock'] ?>)
                            </div>
                        <?php else: ?>
                            <div style="color: var(--error); font-weight: 600;">
                                ❌ Rupture de stock
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($product['stock'] > 0): ?>
                    <form method="POST" action="cart.php" class="add-to-cart-form">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="form-group">
                            <label class="form-label" for="quantity">Quantité</label>
                            <input 
                                type="number" 
                                id="quantity" 
                                name="quantity" 
                                class="form-input" 
                                value="1" 
                                min="1" 
                                max="<?= $product['stock'] ?>"
                            >
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block btn-lg">
                            <i class="fas fa-cart-plus"></i> Ajouter au panier
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary btn-block btn-lg" disabled>
                        <i class="fas fa-ban"></i> Produit indisponible
                    </button>
                <?php endif; ?>
                
                <div style="margin-top: 1.5rem; padding: 1rem; background: var(--gray-50); border-radius: var(--radius-md); font-size: 0.875rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-truck" style="color: var(--success);"></i>
                        <span>Livraison gratuite dès <?= formatPrice(FREE_SHIPPING_THRESHOLD) ?></span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                        <i class="fas fa-redo" style="color: var(--info);"></i>
                        <span>Retours gratuits sous 30 jours</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-shield-alt" style="color: var(--primary);"></i>
                        <span>Paiement 100% sécurisé</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .product-detail-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once '../components/footer.php'; ?>