-- ============================================
-- BASE DE DONNÉES DROPSHIPPING BTS CIEL
-- Version : 1.0
-- Date : 2025-12-02
-- ============================================

CREATE DATABASE IF NOT EXISTS dropshipping_bts CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dropshipping_bts;

-- ============================================
-- TABLE : users (Utilisateurs)
-- ============================================

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    adresse TEXT,
    ville VARCHAR(100),
    code_postal VARCHAR(10),
    role ENUM('client', 'admin') DEFAULT 'client',
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : categories (Catégories produits)
-- ============================================

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    slug VARCHAR(100) NOT NULL UNIQUE,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : products (Produits)
-- ============================================

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10,2) NOT NULL,
    prix_promo DECIMAL(10,2) DEFAULT NULL,
    stock INT DEFAULT 0,
    categorie_id INT,
    image_url VARCHAR(500),
    sku VARCHAR(50) UNIQUE,
    poids DECIMAL(8,2),
    actif BOOLEAN DEFAULT TRUE,
    featured BOOLEAN DEFAULT FALSE,
    date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_actif (actif),
    INDEX idx_featured (featured),
    INDEX idx_categorie (categorie_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : orders (Commandes)
-- ============================================

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    numero_commande VARCHAR(50) UNIQUE NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'expediee', 'livree', 'annulee') DEFAULT 'en_attente',
    adresse_livraison TEXT NOT NULL,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_livraison_estimee DATE,
    notes TEXT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_statut (statut),
    INDEX idx_date (date_commande)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TABLE : order_items (Détails commandes)
-- ============================================

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantite INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DONNÉES DE TEST
-- ============================================

-- Utilisateurs de test
INSERT INTO users (nom, prenom, email, password, telephone, adresse, ville, code_postal, role) VALUES
('Dupont', 'Marie', 'client@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0612345678', '10 Rue de la Paix', 'Paris', '75001', 'client'),
('Admin', 'Super', 'admin@test.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0698765432', '1 Avenue Admin', 'Paris', '75000', 'admin'),
('Martin', 'Jean', 'jean.martin@email.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0623456789', '25 Rue Victor Hugo', 'Lyon', '69001', 'client');

-- Catégories
INSERT INTO categories (nom, description, slug) VALUES
('Électronique', 'Appareils et accessoires électroniques', 'electronique'),
('Mode', 'Vêtements et accessoires de mode', 'mode'),
('Maison', 'Décoration et équipement maison', 'maison'),
('Sport', 'Équipements et accessoires sportifs', 'sport'),
('Beauté', 'Produits de beauté et cosmétiques', 'beaute');

-- Produits
INSERT INTO products (nom, description, prix, prix_promo, stock, categorie_id, image_url, sku, featured) VALUES
('Casque Audio Sans Fil Premium', 'Casque Bluetooth avec réduction de bruit active, autonomie 30h, son haute définition', 89.99, 69.99, 45, 1, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500', 'AUDIO-001', TRUE),
('Montre Connectée Sport', 'Montre intelligente avec suivi GPS, cardio, sommeil. Étanche 5ATM', 199.99, 149.99, 30, 1, 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500', 'WATCH-001', TRUE),
('Sac à Dos Urbain', 'Sac à dos moderne avec compartiment laptop 15", port USB, anti-vol', 49.99, NULL, 78, 2, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500', 'BAG-001', FALSE),
('Lampe LED Design', 'Lampe de bureau LED avec variateur, 3 modes de couleur, USB rechargeable', 34.99, 24.99, 120, 3, 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?w=500', 'LAMP-001', TRUE),
('Tapis de Yoga Premium', 'Tapis antidérapant 6mm, matériau écologique, avec sac de transport', 29.99, NULL, 65, 4, 'https://images.unsplash.com/photo-1601925260368-ae2f83cf8b7f?w=500', 'YOGA-001', FALSE),
('Coffret Soins Visage Bio', 'Set complet de soins visage naturels : nettoyant, sérum, crème hydratante', 59.99, 44.99, 42, 5, 'https://images.unsplash.com/photo-1556228578-0d85b1a4d571?w=500', 'BEAUTY-001', TRUE),
('Enceinte Bluetooth Portable', 'Son 360°, batterie 12h, étanche IPX7, mains-libres', 79.99, NULL, 55, 1, 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500', 'SPEAK-001', FALSE),
('Bouteille Isotherme Inox', 'Garde chaud 12h / froid 24h, sans BPA, 750ml', 24.99, 19.99, 150, 4, 'https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=500', 'BOTTLE-001', TRUE);

-- Commande exemple
INSERT INTO orders (user_id, numero_commande, montant_total, statut, adresse_livraison, date_livraison_estimee) VALUES
(1, 'CMD-2025-00001', 194.97, 'confirmee', '10 Rue de la Paix, 75001 Paris', DATE_ADD(CURDATE(), INTERVAL 3 DAY));

INSERT INTO order_items (order_id, product_id, quantite, prix_unitaire) VALUES
(1, 1, 1, 69.99),
(1, 4, 2, 24.99),
(1, 8, 2, 19.99);

-- ============================================
-- VUES UTILES
-- ============================================

-- Vue : Produits avec catégories
CREATE OR REPLACE VIEW v_products_full AS
SELECT 
    p.*,
    c.nom AS categorie_nom,
    c.slug AS categorie_slug,
    CASE 
        WHEN p.prix_promo IS NOT NULL THEN ROUND(((p.prix - p.prix_promo) / p.prix) * 100)
        ELSE 0
    END AS pourcentage_reduction
FROM products p
LEFT JOIN categories c ON p.categorie_id = c.id;

-- Vue : Statistiques commandes
CREATE OR REPLACE VIEW v_order_stats AS
SELECT 
    o.id,
    o.numero_commande,
    o.montant_total,
    o.statut,
    o.date_commande,
    u.nom,
    u.prenom,
    u.email,
    COUNT(oi.id) AS nb_items
FROM orders o
JOIN users u ON o.user_id = u.id
LEFT JOIN order_items oi ON o.id = oi.order_id
GROUP BY o.id;

-- ============================================
-- FIN DU SCRIPT
-- ============================================