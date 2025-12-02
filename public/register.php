<?php
require_once '../includes/config.php';

$pageTitle = 'Inscription';
$message = '';

// Si déjà connecté, rediriger
if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $message = showError('Veuillez remplir tous les champs obligatoires');
    } elseif (!isValidEmail($email)) {
        $message = showError('Adresse email invalide');
    } elseif (!isValidPassword($password)) {
        $message = showError('Le mot de passe doit contenir au moins 8 caractères');
    } elseif ($password !== $confirmPassword) {
        $message = showError('Les mots de passe ne correspondent pas');
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $message = showError('Cette adresse email est déjà utilisée');
        } else {
            // Créer le compte
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("
                INSERT INTO users (nom, prenom, email, password, role)
                VALUES (?, ?, ?, ?, 'client')
            ");
            
            try {
                $stmt->execute([$nom, $prenom, $email, $hashedPassword]);
                $message = showSuccess('Compte créé avec succès ! Vous pouvez maintenant vous connecter.');
                
                // Rediriger après 2 secondes
                header("refresh:2;url=login.php");
            } catch (PDOException $e) {
                $message = showError('Erreur lors de la création du compte');
            }
        }
    }
}

require_once '../components/header.php';
require_once '../components/nav.php';
?>

<div class="container" style="padding: 4rem 0; max-width: 600px;">
    <div class="card">
        <div class="card-header text-center">
            <h1>👤 Inscription</h1>
            <p class="text-muted">Créez votre compte en quelques secondes</p>
        </div>
        
        <div class="card-body">
            <?= $message ?>
            
            <form method="POST" data-validate>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label" for="nom">Nom *</label>
                        <input 
                            type="text" 
                            id="nom" 
                            name="nom" 
                            class="form-input" 
                            required
                            value="<?= secure($nom ?? '') ?>"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="prenom">Prénom *</label>
                        <input 
                            type="text" 
                            id="prenom" 
                            name="prenom" 
                            class="form-input" 
                            required
                            value="<?= secure($prenom ?? '') ?>"
                        >
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="email">📧 Adresse email *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        required
                        value="<?= secure($email ?? '') ?>"
                    >
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">🔒 Mot de passe *</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        required
                        minlength="8"
                        placeholder="Minimum 8 caractères"
                    >
                    <small class="text-muted">Le mot de passe doit contenir au moins 8 caractères</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="confirm_password">🔒 Confirmer le mot de passe *</label>
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        class="form-input" 
                        required
                        minlength="8"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-block btn-lg">
                    <i class="fas fa-user-plus"></i> Créer mon compte
                </button>
            </form>
        </div>
        
        <div class="card-footer text-center">
            <p class="text-muted">
                Déjà inscrit ? 
                <a href="login.php" style="color: var(--primary); font-weight: 600;">
                    Connectez-vous
                </a>
            </p>
        </div>
    </div>
</div>

<?php require_once '../components/footer.php'; ?>