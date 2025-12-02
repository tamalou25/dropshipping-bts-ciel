<?php
require_once '../includes/config.php';

requireLogin();

$pageTitle = 'Mes commandes';

// Récupérer les commandes de l'utilisateur
$stmt = $pdo->prepare("
    SELECT o.*, COUNT(oi.id) as nb_items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.date_commande DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();

function getStatusBadge($status) {
    $badges = [
        'en_attente' => ['🕐', 'En attente', 'warning'],
        'confirmee' => ['✅', 'Confirmée', 'info'],
        'expediee' => ['📦', 'Expédiée', 'primary'],
        'livree' => ['🎉', 'Livrée', 'success'],
        'annulee' => ['❌', 'Annulée', 'error']
    ];
    
    $badge = $badges[$status] ?? ['❓', 'Inconnu', 'secondary'];
    return '<span class="btn btn-' . $badge[2] . ' btn-sm" style="cursor: default;">' . $badge[0] . ' ' . $badge[1] . '</span>';
}

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 2rem 0;">
    <h1 class="mb-6">📦 Mes commandes</h1>
    
    <?php if (empty($orders)): ?>
        <div class="card">
            <div class="card-body text-center" style="padding: 4rem 2rem;">
                <div style="font-size: 5rem; opacity: 0.3; margin-bottom: 1rem;">📦</div>
                <h2 style="margin-bottom: 1rem;">Aucune commande</h2>
                <p class="text-muted" style="margin-bottom: 2rem;">Vous n'avez pas encore passé de commande</p>
                <a href="products.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-cart"></i> Découvrir nos produits
                </a>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="card mb-6">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h2 style="font-size: 1.25rem; margin: 0;">
                        Commande #<?= secure($order['numero_commande']) ?>
                    </h2>
                    <?= getStatusBadge($order['statut']) ?>
                </div>
                
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">📅 Date de commande</div>
                            <div style="font-weight: 600;"><?= formatDateTime($order['date_commande']) ?></div>
                        </div>
                        
                        <div>
                            <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">💰 Montant total</div>
                            <div style="font-weight: 700; color: var(--primary); font-size: 1.25rem;"><?= formatPrice($order['montant_total']) ?></div>
                        </div>
                        
                        <div>
                            <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">📦 Articles</div>
                            <div style="font-weight: 600;"><?= $order['nb_items'] ?> article<?= $order['nb_items'] > 1 ? 's' : '' ?></div>
                        </div>
                        
                        <div>
                            <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">🚚 Livraison estimée</div>
                            <div style="font-weight: 600;"><?= formatDate($order['date_livraison_estimee']) ?></div>
                        </div>
                    </div>
                    
                    <div style="background: var(--gray-50); padding: 1rem; border-radius: var(--radius-md);">
                        <strong style="font-size: 0.875rem;">📍 Adresse de livraison :</strong><br>
                        <span style="color: var(--gray-700);"><?= secure($order['adresse_livraison']) ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once '../components/footer.php'; ?>