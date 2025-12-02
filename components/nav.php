<?php
if (!defined('DB_HOST')) {
    require_once '../includes/config.php';
}

$cartCount = getCartCount();
$isUserLoggedIn = isLoggedIn();
$userInfo = $isUserLoggedIn ? getUserInfo() : null;
?>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <div class="top-bar-content">
            <div class="top-bar-left">
                <i class="fas fa-truck"></i>
                <span>Livraison gratuite dès <?= formatPrice(FREE_SHIPPING_THRESHOLD) ?></span>
            </div>
            <div class="top-bar-right">
                <i class="fas fa-tag"></i>
                <span><?= DISCOUNT_RATE ?>% de remise dès <?= formatPrice(DISCOUNT_THRESHOLD) ?> d'achat</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Navigation -->
<nav class="navbar">
    <div class="container">
        <div class="nav-wrapper">
            <!-- Logo -->
            <a href="<?= SITE_URL ?>/public/index.php" class="logo">
                <i class="fas fa-shopping-bag"></i>
                <span><?= SITE_NAME ?></span>
            </a>
            
            <!-- Menu Desktop -->
            <ul class="nav-menu">
                <li><a href="<?= SITE_URL ?>/public/index.php" class="nav-link"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="<?= SITE_URL ?>/public/products.php" class="nav-link"><i class="fas fa-shopping-cart"></i> Produits</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="<?= SITE_URL ?>/admin/dashboard.php" class="nav-link"><i class="fas fa-crown"></i> Admin</a></li>
                <?php endif; ?>
            </ul>
            
            <!-- Actions -->
            <div class="nav-actions">
                <!-- Panier -->
                <a href="<?= SITE_URL ?>/public/cart.php" class="nav-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cartCount > 0): ?>
                        <span class="badge"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
                
                <!-- User Menu -->
                <?php if ($isUserLoggedIn): ?>
                    <div class="user-menu">
                        <button class="user-btn">
                            <i class="fas fa-user-circle"></i>
                            <span><?= secure($userInfo['prenom']) ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown">
                            <a href="<?= SITE_URL ?>/public/orders.php"><i class="fas fa-box"></i> Mes commandes</a>
                            <?php if (isAdmin()): ?>
                                <a href="<?= SITE_URL ?>/admin/dashboard.php"><i class="fas fa-crown"></i> Administration</a>
                            <?php endif; ?>
                            <hr>
                            <a href="<?= SITE_URL ?>/public/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= SITE_URL ?>/public/login.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-sign-in-alt"></i> Connexion
                    </a>
                <?php endif; ?>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-toggle" id="mobileToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Menu -->
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-menu-header">
        <span><?= SITE_NAME ?></span>
        <button class="mobile-close" id="mobileClose">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <ul class="mobile-menu-list">
        <li><a href="<?= SITE_URL ?>/public/index.php"><i class="fas fa-home"></i> Accueil</a></li>
        <li><a href="<?= SITE_URL ?>/public/products.php"><i class="fas fa-shopping-cart"></i> Produits</a></li>
        <li><a href="<?= SITE_URL ?>/public/cart.php"><i class="fas fa-shopping-bag"></i> Panier (<?= $cartCount ?>)</a></li>
        <?php if ($isUserLoggedIn): ?>
            <li><a href="<?= SITE_URL ?>/public/orders.php"><i class="fas fa-box"></i> Mes commandes</a></li>
            <?php if (isAdmin()): ?>
                <li><a href="<?= SITE_URL ?>/admin/dashboard.php"><i class="fas fa-crown"></i> Admin</a></li>
            <?php endif; ?>
            <li><a href="<?= SITE_URL ?>/public/logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
        <?php else: ?>
            <li><a href="<?= SITE_URL ?>/public/login.php"><i class="fas fa-sign-in-alt"></i> Connexion</a></li>
            <li><a href="<?= SITE_URL ?>/public/register.php"><i class="fas fa-user-plus"></i> Inscription</a></li>
        <?php endif; ?>
    </ul>
</div>