<?php
require_once '../includes/config.php';

requireLogin();

$pageTitle = 'Commande confirmée';

if (!isset($_SESSION['last_order_id'])) {
    redirect('orders.php');
}

$orderId = $_SESSION['last_order_id'];

// Récupérer la commande
$stmt = $pdo->prepare("
    SELECT o.*, COUNT(oi.id) as nb_items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    WHERE o.id = ? AND o.user_id = ?
    GROUP BY o.id
");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    redirect('orders.php');
}

unset($_SESSION['last_order_id']);

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 4rem 0; max-width: 700px;">
    <div class="card">
        <div class="card-body text-center" style="padding: 3rem;">
            <div style="font-size: 5rem; color: var(--success); margin-bottom: 1.5rem;">✅</div>
            
            <h1 style="color: var(--success); margin-bottom: 1rem;">Commande confirmée !</h1>
            
            <p style="font-size: 1.125rem; color: var(--gray-700); margin-bottom: 2rem;">
                Merci pour votre commande. Vous recevrez un email de confirmation sous peu.
            </p>
            
            <div style="background: var(--gray-50); padding: 2rem; border-radius: var(--radius-lg); margin-bottom: 2rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; text-align: left;">
                    <div>
                        <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">Numéro de commande</div>
                        <div style="font-weight: 700; font-size: 1.125rem;"><?= secure($order['numero_commande']) ?></div>
                    </div>
                    
                    <div>
                        <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">Montant total</div>
                        <div style="font-weight: 700; font-size: 1.125rem; color: var(--primary);"><?= formatPrice($order['montant_total']) ?></div>
                    </div>
                    
                    <div>
                        <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">Nombre d'articles</div>
                        <div style="font-weight: 700;"><?= $order['nb_items'] ?></div>
                    </div>
                    
                    <div>
                        <div style="font-size: 0.875rem; color: var(--gray-600); margin-bottom: 0.5rem;">Livraison estimée</div>
                        <div style="font-weight: 700;"><?= formatDate($order['date_livraison_estimee']) ?></div>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="orders.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-list"></i> Voir mes commandes
                </a>
                <a href="products.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-shopping-cart"></i> Continuer mes achats
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../components/footer.php'; ?>