<?php
require_once '../includes/config.php';

$pageTitle = 'Nos produits';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = PRODUCTS_PER_PAGE;
$offset = ($page - 1) * $perPage;

// Filtre catégorie
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Recherche
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Construire la requête
$where = ['actif = TRUE'];
$params = [];

if ($categoryFilter) {
    $where[] = 'categorie_id = ?';
    $params[] = $categoryFilter;
}

if ($search) {
    $where[] = '(nom LIKE ? OR description LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = implode(' AND ', $where);

// Compter le total
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE $whereClause");
$stmt->execute($params);
$totalProducts = $stmt->fetchColumn();
$totalPages = ceil($totalProducts / $perPage);

// Récupérer les produits
$stmt = $pdo->prepare("
    SELECT * FROM v_products_full
    WHERE $whereClause
    ORDER BY date_ajout DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$products = $stmt->fetchAll();

// Récupérer les catégories
$categories = $pdo->query("SELECT * FROM categories WHERE actif = TRUE ORDER BY nom")->fetchAll();

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 2rem 0;">
    <h1 class="mb-6">🛒 Nos produits</h1>
    
    <!-- Filtres -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" class="flex items-center gap-4" style="flex-wrap: wrap;">
                <!-- Recherche -->
                <div style="flex: 1; min-width: 250px;">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-input" 
                        placeholder="🔍 Rechercher un produit..."
                        value="<?= secure($search) ?>"
                    >
                </div>
                
                <!-- Catégorie -->
                <div style="min-width: 200px;">
                    <select name="category" class="form-select">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>>
                                <?= secure($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Boutons -->
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <a href="products.php" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Réinitialiser
                </a>
            </form>
        </div>
    </div>
    
    <!-- Résultats -->
    <div class="mb-4">
        <p class="text-muted">
            <?= $totalProducts ?> produit<?= $totalProducts > 1 ? 's' : '' ?> trouvé<?= $totalProducts > 1 ? 's' : '' ?>
        </p>
    </div>
    
    <!-- Grille produits -->
    <?php if (empty($products)): ?>
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted">Aucun produit ne correspond à vos critères.</p>
                <a href="products.php" class="btn btn-primary mt-4">Voir tous les produits</a>
            </div>
        </div>
    <?php else: ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php if ($product['prix_promo']): ?>
                        <div class="product-badge">
                            -<?= $product['pourcentage_reduction'] ?>%
                        </div>
                    <?php endif; ?>
                    
                    <img src="<?= secure($product['image_url']) ?>" alt="<?= secure($product['nom']) ?>" class="product-image">
                    
                    <div class="product-info">
                        <div class="text-muted" style="font-size: 0.875rem; margin-bottom: 0.5rem;">
                            <?= secure($product['categorie_nom'] ?? 'Non catégorisé') ?>
                        </div>
                        
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
                        
                        <?php if ($product['stock'] > 0): ?>
                            <p class="text-muted" style="font-size: 0.875rem; margin-bottom: 1rem;">
                                ✅ En stock (<?= $product['stock'] ?> disponibles)
                            </p>
                        <?php else: ?>
                            <p style="color: var(--error); font-size: 0.875rem; margin-bottom: 1rem;">
                                ❌ Rupture de stock
                            </p>
                        <?php endif; ?>
                        
                        <div style="display: flex; gap: 0.5rem;">
                            <a href="product-detail.php?id=<?= $product['id'] ?>" class="btn btn-secondary" style="flex: 1;">
                                <i class="fas fa-eye"></i> Détails
                            </a>
                            <?php if ($product['stock'] > 0): ?>
                                <form method="POST" action="cart.php" class="add-to-cart-form" style="flex: 1;">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                                        <i class="fas fa-cart-plus"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <button class="btn btn-secondary" style="flex: 1; cursor: not-allowed;" disabled>
                                    <i class="fas fa-ban"></i> Indisponible
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center gap-4 mt-8" style="align-items: center;">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?><?= $categoryFilter ? '&category=' . $categoryFilter : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Précédent
                    </a>
                <?php endif; ?>
                
                <span class="text-muted">Page <?= $page ?> sur <?= $totalPages ?></span>
                
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?><?= $categoryFilter ? '&category=' . $categoryFilter : '' ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-secondary">
                        Suivant <i class="fas fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once '../components/footer.php'; ?>