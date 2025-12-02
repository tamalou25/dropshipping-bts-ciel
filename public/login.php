<?php
require_once '../includes/config.php';

$pageTitle = 'Connexion';
$message = '';

// Si déjà connecté, rediriger
if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $message = showError('Veuillez remplir tous les champs');
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND actif = TRUE");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Connexion réussie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role'] = $user['role'];
            
            // Redirection
            $redirect = $_GET['redirect'] ?? 'index.php';
            redirect($redirect);
        } else {
            $message = showError('Email ou mot de passe incorrect');
        }
    }
}

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 4rem 0; max-width: 500px;">
    <div class="card">
        <div class="card-header text-center">
            <h1>🔑 Connexion</h1>
            <p class="text-muted">Connectez-vous à votre compte</p>
        </div>
        
        <div class="card-body">
            <?= $message ?>
            
            <form method="POST" data-validate>
                <div class="form-group">
                    <label class="form-label" for="email">📧 Adresse email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        required
                        value="<?= secure($email ?? '') ?>"
                        placeholder="exemple@email.com"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">🔒 Mot de passe</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        required
                        placeholder="Votre mot de passe"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>
        </div>
        
        <div class="card-footer text-center">
            <p class="text-muted">
                Pas encore de compte ? 
                <a href="register.php" style="color: var(--primary); font-weight: 600;">
                    Inscrivez-vous
                </a>
            </p>
        </div>
    </div>
    
    <!-- Comptes de test -->
    <div class="card mt-4" style="background: var(--gray-50);">
        <div class="card-body">
            <h3 style="font-size: 1rem; margin-bottom: 1rem;">🧪 Comptes de test disponibles</h3>
            <div style="display: grid; gap: 1rem;">
                <div style="padding: 1rem; background: white; border-radius: var(--radius-md);">
                    <strong>Client :</strong><br>
                    <code>client@test.fr</code> / <code>test1234</code>
                </div>
                <div style="padding: 1rem; background: white; border-radius: var(--radius-md);">
                    <strong>Admin :</strong><br>
                    <code>admin@test.fr</code> / <code>admin1234</code>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../components/footer.php'; ?>