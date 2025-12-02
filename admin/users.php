<?php
require_once '../includes/config.php';

requireAdmin();

$pageTitle = 'Gestion utilisateurs';
$message = '';

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'toggle_active') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId && $userId != $_SESSION['user_id']) { // Ne pas se désactiver soi-même
            $stmt = $pdo->prepare("UPDATE users SET actif = NOT actif WHERE id = ?");
            $stmt->execute([$userId]);
            $message = showSuccess('Statut modifié');
        }
    }
}

// Filtres
$roleFilter = isset($_GET['role']) ? $_GET['role'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Construire la requête
$where = [];
$params = [];

if ($roleFilter) {
    $where[] = 'role = ?';
    $params[] = $roleFilter;
}

if ($search) {
    $where[] = '(nom LIKE ? OR prenom LIKE ? OR email LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
    SELECT u.*,
           COUNT(DISTINCT o.id) as nb_commandes,
           COALESCE(SUM(o.montant_total), 0) as total_depense
    FROM users u
    LEFT JOIN orders o ON u.id = o.user_id AND o.statut != 'annulee'
    $whereClause
    GROUP BY u.id
    ORDER BY u.date_inscription DESC
");
$stmt->execute($params);
$users = $stmt->fetchAll();

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 2rem 0;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>👥 Gestion des utilisateurs</h1>
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
                        placeholder="🔍 Rechercher (nom, email)..."
                        value="<?= secure($search) ?>"
                    >
                </div>
                
                <div style="min-width: 150px;">
                    <select name="role" class="form-select">
                        <option value="">Tous les rôles</option>
                        <option value="client" <?= $roleFilter == 'client' ? 'selected' : '' ?>>👤 Clients</option>
                        <option value="admin" <?= $roleFilter == 'admin' ? 'selected' : '' ?>>👑 Admins</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
            </form>
        </div>
    </div>
    
    <!-- Liste utilisateurs -->
    <div class="card">
        <div class="card-body" style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Contact</th>
                        <th>Commandes</th>
                        <th>Total dépensé</th>
                        <th>Inscription</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <strong><?= secure($user['prenom']) ?> <?= secure($user['nom']) ?></strong><br>
                                <small class="text-muted"><?= secure($user['email']) ?></small>
                            </td>
                            <td>
                                <?php if ($user['role'] === 'admin'): ?>
                                    <span class="btn btn-warning btn-sm" style="cursor: default;">👑 Admin</span>
                                <?php else: ?>
                                    <span class="btn btn-info btn-sm" style="cursor: default;">👤 Client</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($user['telephone']): ?>
                                    📞 <?= secure($user['telephone']) ?><br>
                                <?php endif; ?>
                                <?php if ($user['ville']): ?>
                                    📍 <?= secure($user['ville']) ?>
                                <?php endif; ?>
                            </td>
                            <td style="text-align: center; font-weight: 600;"><?= $user['nb_commandes'] ?></td>
                            <td style="font-weight: 600; color: var(--primary);"><?= formatPrice($user['total_depense']) ?></td>
                            <td><?= formatDate($user['date_inscription']) ?></td>
                            <td>
                                <span class="btn <?= $user['actif'] ? 'btn-success' : 'btn-error' ?> btn-sm" style="cursor: default;">
                                    <?= $user['actif'] ? '✅ Actif' : '❌ Inactif' ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST">
                                        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                        <input type="hidden" name="action" value="toggle_active">
                                        <button type="submit" class="btn btn-secondary btn-sm">
                                            <?= $user['actif'] ? '🚫 Désactiver' : '✅ Activer' ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted" style="font-size: 0.875rem;">Vous</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../components/footer.php'; ?>