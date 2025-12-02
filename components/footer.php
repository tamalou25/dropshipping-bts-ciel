<?php
if (!defined('DB_HOST')) {
    require_once '../includes/config.php';
}
?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <!-- About -->
            <div class="footer-col">
                <h3><?= SITE_NAME ?></h3>
                <p>Votre boutique dropshipping de confiance. Produits de qualité, livraison rapide, service client réactif.</p>
                <div class="social-links">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://github.com/tamalou25" aria-label="GitHub" target="_blank"><i class="fab fa-github"></i></a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="footer-col">
                <h4>Liens rapides</h4>
                <ul>
                    <li><a href="<?= SITE_URL ?>/public/index.php">Accueil</a></li>
                    <li><a href="<?= SITE_URL ?>/public/products.php">Produits</a></li>
                    <li><a href="<?= SITE_URL ?>/public/cart.php">Panier</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="<?= SITE_URL ?>/public/orders.php">Mes commandes</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Customer Service -->
            <div class="footer-col">
                <h4>Service client</h4>
                <ul>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Livraison</a></li>
                    <li><a href="#">Retours</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div class="footer-col">
                <h4>Contact</h4>
                <ul class="contact-info">
                    <li><i class="fas fa-envelope"></i> <?= SITE_EMAIL ?></li>
                    <li><i class="fas fa-phone"></i> 01 23 45 67 89</li>
                    <li><i class="fas fa-map-marker-alt"></i> Paris, France</li>
                </ul>
            </div>
        </div>
        
        <hr class="footer-divider">
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. Tous droits réservés.</p>
            <p class="footer-credits">
                Projet BTS CIEL - Développé par <a href="https://github.com/tamalou25" target="_blank">tamalou25</a>
            </p>
        </div>
    </div>
</footer>

<!-- JavaScript -->
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>