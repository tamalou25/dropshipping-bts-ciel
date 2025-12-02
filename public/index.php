<?php
require_once '../includes/config.php';

$pageTitle = 'Accueil';

// Récupérer les produits en vedette
$stmt = $pdo->query("
    SELECT * FROM v_products_full
    WHERE actif = TRUE AND featured = TRUE
    ORDER BY date_ajout DESC
    LIMIT 8
");
$featuredProducts = $stmt->fetchAll();

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>🛍️ Bienvenue sur <?= SITE_NAME ?></h1>
        <p>Découvrez notre sélection de produits de qualité avec livraison rapide</p>
        <a href="products.php" class="btn btn-primary btn-lg">
            <i class="fas fa-shopping-bag"></i> Découvrir nos produits
        </a>
    </div>
</section>

<!-- Avantages -->
<section style="padding: 4rem 0; background: white;">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; text-align: center;">
            <div>
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;">🚚</div>
                <h3>Livraison rapide</h3>
                <p class="text-muted">Expédition sous 48h</p>
            </div>
            <div>
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;">🔒</div>
                <h3>Paiement sécurisé</h3>
                <p class="text-muted">Transactions 100% sécurisées</p>
            </div>
            <div>
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;">📞</div>
                <h3>Support client</h3>
                <p class="text-muted">7j/7 pour vous aider</p>
            </div>
            <div>
                <div style="font-size: 3rem; color: var(--primary); margin-bottom: 1rem;">↩️</div>
                <h3>Retours gratuits</h3>
                <p class="text-muted">30 jours satisfait ou remboursé</p>
            </div>
        </div>
    </div>
</section>

<!-- Produits en vedette -->
<section style="padding: 4rem 0;">
    <div class="container">
        <div style="text-align: center; margin-bottom: 3rem;">
            <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">⭐ Produits en vedette</h2>
            <p class="text-muted">Découvrez notre sélection des meilleurs produits du moment</p>
        </div>
        
        <?php if (empty($featuredProducts)): ?>
            <div class="card">
                <div class="card-body text-center">
                    <p class="text-muted">Aucun produit en vedette pour le moment.</p>
                    <a href="products.php" class="btn btn-primary mt-4">Voir tous les produits</a>
                </div>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="product-card">
                        <?php if ($product['prix_promo']): ?>
                            <div class="product-badge">
                                -<?= $product['pourcentage_reduction'] ?>%
                            </div>
                        <?php endif; ?>
                        
                        <img src="<?= secure($product['image_url']) ?>" alt="<?= secure($product['nom']) ?>" class="product-image">
                        
                        <div class="product-info">
                            <h3 class="product-name"><?= secure($product['nom']) ?></h3>
                            <p class="product-description"><?= secure($product['description']) ?></p>
                            
                            <div class="product-prices">
                                <?php if ($product['prix_promo']): ?>
                                    <span class="product-price"><?= formatPrice($product['prix_promo']) ?></span>
                                    <span class="product-price-old"><?= formatPrice($product['prix']) ?></span>
                                <?php else: ?>
                                    <span class="product-price"><?= formatPrice($product['prix']) ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="product-detail.php?id=<?= $product['id'] ?>" class="btn btn-secondary" style="flex: 1;">
                                    <i class="fas fa-eye"></i> Détails
                                </a>
                                <form method="POST" action="cart.php" class="add-to-cart-form" style="flex: 1;">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                                        <i class="fas fa-cart-plus"></i> Panier
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-8">
                <a href="products.php" class="btn btn-primary btn-lg">
                    Voir tous les produits <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- CTA Section -->
<section style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 4rem 0; text-align: center;">
    <div class="container">
        <h2 style="font-size: 2.5rem; font-weight: 700; margin-bottom: 1rem;">Prêt à commencer ?</h2>
        <p style="font-size: 1.25rem; margin-bottom: 2rem; opacity: 0.9;">Inscrivez-vous dès maintenant et profitez de nos offres exclusives</p>
        <?php if (!isLoggedIn()): ?>
            <a href="register.php" class="btn" style="background: white; color: var(--primary); padding: 1rem 2rem; font-size: 1.125rem;">
                <i class="fas fa-user-plus"></i> Créer un compte
            </a>
        <?php else: ?>
            <a href="products.php" class="btn" style="background: white; color: var(--primary); padding: 1rem 2rem; font-size: 1.125rem;">
                <i class="fas fa-shopping-cart"></i> Commencer mes achats
            </a>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../components/footer.php'; ?>