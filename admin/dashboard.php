<?php
require_once '../includes/config.php';

requireAdmin();

$pageTitle = 'Dashboard Admin';

// Statistiques
$stats = [
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'client'")->fetchColumn(),
    'total_products' => $pdo->query("SELECT COUNT(*) FROM products WHERE actif = TRUE")->fetchColumn(),
    'total_orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'revenue_total' => $pdo->query("SELECT COALESCE(SUM(montant_total), 0) FROM orders WHERE statut != 'annulee'")->fetchColumn(),
    'pending_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE statut = 'en_attente'")->fetchColumn()
];

// Dernières commandes
$recentOrders = $pdo->query("
    SELECT o.*, u.nom, u.prenom, u.email
    FROM orders o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.date_commande DESC
    LIMIT 5
")->fetchAll();

// Produits avec stock faible
$lowStock = $pdo->query("
    SELECT * FROM products
    WHERE actif = TRUE AND stock < 10
    ORDER BY stock ASC
    LIMIT 5
")->fetchAll();

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 2rem 0;">
    <!-- En-tête Admin -->
    <div style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 2rem; border-radius: var(--radius-xl); margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; margin-bottom: 0.5rem;">👑 Dashboard Administrateur</h1>
        <p style="opacity: 0.9;">Bienvenue <?= secure($_SESSION['prenom']) ?> <?= secure($_SESSION['nom']) ?></p>
        
        <div style="display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
            <a href="dashboard.php" class="btn" style="background: rgba(255,255,255,0.2); color: white;">
                📊 Dashboard
            </a>
            <a href="products.php" class="btn" style="background: rgba(255,255,255,0.2); color: white;">
                🛍️ Produits
            </a>
            <a href="orders.php" class="btn" style="background: rgba(255,255,255,0.2); color: white;">
                📦 Commandes
            </a>
            <a href="users.php" class="btn" style="background: rgba(255,255,255,0.2); color: white;">
                👥 Utilisateurs
            </a>
        </div>
    </div>
    
    <!-- Statistiques -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">👥</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);"><?= $stats['total_users'] ?></div>
                <div style="color: var(--gray-600);">Clients inscrits</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">🛒</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);"><?= $stats['total_products'] ?></div>
                <div style="color: var(--gray-600);">Produits actifs</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">📦</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--primary);"><?= $stats['total_orders'] ?></div>
                <div style="color: var(--gray-600);">Commandes totales</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">💰</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--success);"><?= formatPrice($stats['revenue_total']) ?></div>
                <div style="color: var(--gray-600);">Chiffre d'affaires</div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 3rem; margin-bottom: 0.5rem;">⌛</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--warning);"><?= $stats['pending_orders'] ?></div>
                <div style="color: var(--gray-600);">En attente</div>
            </div>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;" class="admin-grid">
        <!-- Dernières commandes -->
        <div class="card">
            <div class="card-header">
                <h2 style="font-size: 1.25rem;">📝 Dernières commandes</h2>
            </div>
            
            <div class="card-body">
                <?php if (empty($recentOrders)): ?>
                    <p class="text-muted text-center">Aucune commande</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Montant</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentOrders as $order): ?>
                                <tr>
                                    <td><?= secure($order['prenom']) ?> <?= secure($order['nom']) ?></td>
                                    <td style="font-weight: 600; color: var(--primary);"><?= formatPrice($order['montant_total']) ?></td>
                                    <td><?= formatDate($order['date_commande']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div style="text-align: center; margin-top: 1rem;">
                        <a href="orders.php" class="btn btn-primary btn-sm">Voir toutes les commandes</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Alertes stock -->
        <div class="card">
            <div class="card-header">
                <h2 style="font-size: 1.25rem;">⚠️ Alertes stock</h2>
            </div>
            
            <div class="card-body">
                <?php if (empty($lowStock)): ?>
                    <p class="text-muted text-center">Aucune alerte</p>
                <?php else: ?>
                    <?php foreach ($lowStock as $product): ?>
                        <div style="padding: 0.75rem; border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <strong><?= secure($product['nom']) ?></strong><br>
                                <span style="color: <?= $product['stock'] == 0 ? 'var(--error)' : 'var(--warning)' ?>; font-size: 0.875rem;">
                                    <?= $product['stock'] == 0 ? '❌ Rupture' : "⚠️ Stock: {$product['stock']}" ?>
                                </span>
                            </div>
                            <a href="products.php?edit=<?= $product['id'] ?>" class="btn btn-secondary btn-sm">✏️</a>
                        </div>
                    <?php endforeach; ?>
                    <div style="text-align: center; margin-top: 1rem;">
                        <a href="products.php" class="btn btn-primary btn-sm">Gérer les produits</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .admin-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<?php require_once '../components/footer.php'; ?>