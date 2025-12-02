<?php
require_once '../includes/config.php';

requireAdmin();

$pageTitle = 'Gestion commandes';
$message = '';

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_status') {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        
        if ($orderId && in_array($status, ['en_attente', 'confirmee', 'expediee', 'livree', 'annulee'])) {
            $stmt = $pdo->prepare("UPDATE orders SET statut = ? WHERE id = ?");
            $stmt->execute([$status, $orderId]);
            $message = showSuccess('Statut mis à jour');
        }
    }
}

// Filtres
$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Construire la requête
$where = [];
$params = [];

if ($statusFilter) {
    $where[] = 'o.statut = ?';
    $params[] = $statusFilter;
}

if ($search) {
    $where[] = '(o.numero_commande LIKE ? OR u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
    SELECT o.*, u.nom, u.prenom, u.email,
           COUNT(oi.id) as nb_items
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    $whereClause
    GROUP BY o.id
    ORDER BY o.date_commande DESC
");
$stmt->execute($params);
$orders = $stmt->fetchAll();

function getStatusBadgeAdmin($status) {
    $badges = [
        'en_attente' => ['🕐', 'En attente', 'warning'],
        'confirmee' => ['✅', 'Confirmée', 'info'],
        'expediee' => ['📦', 'Expédiée', 'primary'],
        'livree' => ['🎉', 'Livrée', 'success'],
        'annulee' => ['❌', 'Annulée', 'error']
    ];
    
    $badge = $badges[$status] ?? ['❓', 'Inconnu', 'secondary'];
    return $badge;
}

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 2rem 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📦 Gestion des commandes</h1>
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
                        placeholder="🔍 Rechercher (n°, client, email)..."
                        value="<?= secure($search) ?>"
                    >
                </div>
                
                <div style="min-width: 200px;">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" <?= $statusFilter == 'en_attente' ? 'selected' : '' ?>>🕐 En attente</option>
                        <option value="confirmee" <?= $statusFilter == 'confirmee' ? 'selected' : '' ?>>✅ Confirmée</option>
                        <option value="expediee" <?= $statusFilter == 'expediee' ? 'selected' : '' ?>>📦 Expédiée</option>
                        <option value="livree" <?= $statusFilter == 'livree' ? 'selected' : '' ?>>🎉 Livrée</option>
                        <option value="annulee" <?= $statusFilter == 'annulee' ? 'selected' : '' ?>>❌ Annulée</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </form>
        </div>
    </div>
    
    <!-- Liste commandes -->
    <?php if (empty($orders)): ?>
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted">Aucune commande trouvée</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <?php $badge = getStatusBadgeAdmin($order['statut']); ?>
            <div class="card mb-4">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="font-size: 1.125rem; margin-bottom: 0.25rem;">
                            Commande <?= secure($order['numero_commande']) ?>
                        </h3>
                        <p class="text-muted" style="font-size: 0.875rem; margin: 0;">
                            <?= secure($order['prenom']) ?> <?= secure($order['nom']) ?> - <?= secure($order['email']) ?>
                        </p>
                    </div>
                    
                    <form method="POST" style="display: flex; gap: 0.5rem; align-items: center;">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <input type="hidden" name="action" value="update_status">
                        <select name="status" class="form-select" style="width: auto; min-width: 150px;">
                            <option value="en_attente" <?= $order['statut'] == 'en_attente' ? 'selected' : '' ?>>🕐 En attente</option>
                            <option value="confirmee" <?= $order['statut'] == 'confirmee' ? 'selected' : '' ?>>✅ Confirmée</option>
                            <option value="expediee" <?= $order['statut'] == 'expediee' ? 'selected' : '' ?>>📦 Expédiée</option>
                            <option value="livree" <?= $order['statut'] == 'livree' ? 'selected' : '' ?>>🎉 Livrée</option>
                            <option value="annulee" <?= $order['statut'] == 'annulee' ? 'selected' : '' ?>>❌ Annulée</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">✔️</button>
                    </form>
                </div>
                
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
                        <div>
                            <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.25rem;">📅 Date</div>
                            <div style="font-weight: 600;"><?= formatDateTime($order['date_commande']) ?></div>
                        </div>
                        
                        <div>
                            <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.25rem;">💰 Montant</div>
                            <div style="font-weight: 700; color: var(--primary); font-size: 1.25rem;"><?= formatPrice($order['montant_total']) ?></div>
                        </div>
                        
                        <div>
                            <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.25rem;">📦 Articles</div>
                            <div style="font-weight: 600;"><?= $order['nb_items'] ?></div>
                        </div>
                        
                        <div>
                            <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.25rem;">🚚 Livraison</div>
                            <div style="font-weight: 600;"><?= formatDate($order['date_livraison_estimee']) ?></div>
                        </div>
                    </div>
                    
                    <div style="margin-top: 1rem; padding: 1rem; background: var(--gray-50); border-radius: var(--radius-md);">
                        <strong style="font-size: 0.875rem;">📍 Adresse :</strong>
                        <?= secure($order['adresse_livraison']) ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once '../components/footer.php'; ?>