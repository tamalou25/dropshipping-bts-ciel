<?php
require_once '../includes/config.php';

requireAdmin();

$pageTitle = 'Gestion produits';
$message = '';

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'toggle_active') {
        $productId = (int)($_POST['product_id'] ?? 0);
        if ($productId) {
            $stmt = $pdo->prepare("UPDATE products SET actif = NOT actif WHERE id = ?");
            $stmt->execute([$productId]);
            $message = showSuccess('Statut modifié');
        }
    }
    
    if ($action === 'toggle_featured') {
        $productId = (int)($_POST['product_id'] ?? 0);
        if ($productId) {
            $stmt = $pdo->prepare("UPDATE products SET featured = NOT featured WHERE id = ?");
            $stmt->execute([$productId]);
            $message = showSuccess('Mis en vedette modifié');
        }
    }
    
    if ($action === 'update_stock') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $stock = (int)($_POST['stock'] ?? 0);
        if ($productId) {
            $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
            $stmt->execute([$stock, $productId]);
            $message = showSuccess('Stock mis à jour');
        }
    }
}

// Filtres
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Construire la requête
$where = [];
$params = [];

if ($search) {
    $where[] = '(nom LIKE ? OR description LIKE ? OR sku LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($categoryFilter) {
    $where[] = 'categorie_id = ?';
    $params[] = $categoryFilter;
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("SELECT * FROM v_products_full $whereClause ORDER BY date_ajout DESC");
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY nom")->fetchAll();

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 2rem 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>🛍️ Gestion des produits</h1>
        <a href="dashboard.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
    
    <?= $message ?>
    
    <!-- Filtres -->
    <div class="card mb-6">
        <div class="card-body">
            <form method="GET" class="flex items-center gap-4" style="flex-wrap: wrap;">
                <div style="flex: 1; min-width: 250px;">
                    <input 
                        type="text" 
                        name="search" 
                        class="form-input" 
                        placeholder="🔍 Rechercher..."
                        value="<?= secure($search) ?>"
                    >
                </div>
                
                <div style="min-width: 200px;">
                    <select name="category" class="form-select">
                        <option value="">Toutes catégories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $categoryFilter == $cat['id'] ? 'selected' : '' ?>>
                                <?= secure($cat['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </form>
        </div>
    </div>
    
    <!-- Tableau produits -->
    <div class="card">
        <div class="card-body" style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Catégorie</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <img src="<?= secure($product['image_url']) ?>" alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: var(--radius-md);">
                                    <div>
                                        <strong><?= secure($product['nom']) ?></strong>
                                        <?php if ($product['featured']): ?>
                                            <span class="btn btn-warning btn-sm" style="margin-left: 0.5rem; cursor: default;">⭐</span>
                                        <?php endif; ?>
                                        <br>
                                        <small class="text-muted"><?= secure($product['sku']) ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><?= secure($product['categorie_nom'] ?? 'N/A') ?></td>
                            <td>
                                <?php if ($product['prix_promo']): ?>
                                    <span style="color: var(--primary); font-weight: 600;"><?= formatPrice($product['prix_promo']) ?></span><br>
                                    <small style="text-decoration: line-through; color: var(--gray-500);"><?= formatPrice($product['prix']) ?></small>
                                <?php else: ?>
                                    <span style="font-weight: 600;"><?= formatPrice($product['prix']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="action" value="update_stock">
                                    <input 
                                        type="number" 
                                        name="stock" 
                                        value="<?= $product['stock'] ?>" 
                                        min="0"
                                        style="width: 70px; padding: 0.25rem 0.5rem; border: 1px solid var(--gray-300); border-radius: var(--radius-sm);"
                                    >
                                    <button type="submit" class="btn btn-primary btn-sm">✔️</button>
                                </form>
                            </td>
                            <td>
                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <input type="hidden" name="action" value="toggle_active">
                                    <button type="submit" class="btn <?= $product['actif'] ? 'btn-success' : 'btn-secondary' ?> btn-sm">
                                        <?= $product['actif'] ? '✅ Actif' : '❌ Inactif' ?>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <div style="display: flex; gap: 0.5rem;">
                                    <form method="POST">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <input type="hidden" name="action" value="toggle_featured">
                                        <button type="submit" class="btn <?= $product['featured'] ? 'btn-warning' : 'btn-secondary' ?> btn-sm" title="Mettre en vedette">
                                            ⭐
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../components/footer.php'; ?>