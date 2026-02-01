<?php
require_once 'config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        if ($auth->login($username, $password)) {
            header('Location: index.php');
            exit();
        } else {
            $error = 'Nom d\'utilisateur ou mot de passe incorrect.';
        }
    } elseif (isset($_POST['signup'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $confirm_password = trim($_POST['confirm_password']);
        
        // Validate inputs
        if (empty($username) || empty($email) || empty($password)) {
            $error = 'Tous les champs sont obligatoires.';
        } elseif ($password !== $confirm_password) {
            $error = 'Les mots de passe ne correspondent pas.';
        } elseif (strlen($password) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caractères.';
        } else {
            if ($auth->register($username, $email, $password)) {
                $success = 'Compte créé avec succès. Vous pouvez maintenant vous connecter.';
            } else {
                $error = 'Le nom d\'utilisateur ou l\'email existe déjà.';
            }
        }
    }
}

// If already logged in, redirect to index
if ($auth->isLoggedIn()) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion des Employés</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-header">
            <h1><i class="fas fa-users"></i> Gestion des Employés</h1>
            <p>Système de gestion</p>
        </div>
        
        <div class="auth-tabs">
            <button class="tab-btn active" data-tab="login">Connexion</button>
            <button class="tab-btn" data-tab="signup">Inscription</button>
        </div>
        
        <!-- Messages -->
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>
        
        <!-- Login Form -->
        <div id="login-form" class="auth-form active">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="login-username">
                        <i class="fas fa-user"></i> Nom d'utilisateur ou Email
                    </label>
                    <input type="text" id="login-username" name="username" required 
                           placeholder="Entrez votre nom d'utilisateur ou email">
                </div>
                
                <div class="form-group">
                    <label for="login-password">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>
                    <input type="password" id="login-password" name="password" required 
                           placeholder="Entrez votre mot de passe">
                    <span class="toggle-password" onclick="togglePassword('login-password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="login" class="auth-btn">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </button>
                </div>
                
                <div class="auth-links">
                    <p>Compte de test: <strong>admin</strong> / <strong>admin123</strong></p>
                </div>
            </form>
        </div>
        
        <!-- Signup Form -->
        <div id="signup-form" class="auth-form">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="signup-username">
                        <i class="fas fa-user"></i> Nom d'utilisateur
                    </label>
                    <input type="text" id="signup-username" name="username" required 
                           placeholder="Choisissez un nom d'utilisateur">
                </div>
                
                <div class="form-group">
                    <label for="signup-email">
                        <i class="fas fa-envelope"></i> Email
                    </label>
                    <input type="email" id="signup-email" name="email" required 
                           placeholder="Entrez votre email">
                </div>
                
                <div class="form-group">
                    <label for="signup-password">
                        <i class="fas fa-lock"></i> Mot de passe
                    </label>
                    <input type="password" id="signup-password" name="password" required 
                           placeholder="Créez un mot de passe (min. 6 caractères)">
                    <span class="toggle-password" onclick="togglePassword('signup-password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="form-group">
                    <label for="signup-confirm-password">
                        <i class="fas fa-lock"></i> Confirmer le mot de passe
                    </label>
                    <input type="password" id="signup-confirm-password" name="confirm_password" required 
                           placeholder="Confirmez votre mot de passe">
                    <span class="toggle-password" onclick="togglePassword('signup-confirm-password')">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="signup" class="auth-btn">
                        <i class="fas fa-user-plus"></i> S'inscrire
                    </button>
                </div>
            </form>
        </div>
        
        <div class="auth-footer">
            <p>Système de Gestion des Employés &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>

    <script>
        // Tab switching
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tab = this.getAttribute('data-tab');
                
                // Update active tab button
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                this.classList.add('active');
                
                // Show selected form
                document.querySelectorAll('.auth-form').forEach(form => {
                    form.classList.remove('active');
                });
                document.getElementById(tab + '-form').classList.add('active');
            });
        });
        
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = input.nextElementSibling.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Password strength indicator
        const passwordInput = document.getElementById('signup-password');
        const confirmInput = document.getElementById('signup-confirm-password');
        
        if (passwordInput && confirmInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = checkPasswordStrength(password);
                updateStrengthIndicator(strength);
            });
            
            confirmInput.addEventListener('input', function() {
                checkPasswordMatch();
            });
        }
        
        function checkPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            return Math.min(strength, 5);
        }
        
        function updateStrengthIndicator(strength) {
            const indicator = document.getElementById('password-strength');
            if (!indicator) return;
            
            const colors = ['#e74c3c', '#e67e22', '#f1c40f', '#2ecc71', '#27ae60'];
            const texts = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
            
            indicator.style.width = (strength * 20) + '%';
            indicator.style.backgroundColor = colors[strength - 1] || '#e74c3c';
            
            const textElement = document.getElementById('password-strength-text');
            if (textElement) {
                textElement.textContent = texts[strength - 1] || 'Très faible';
                textElement.style.color = colors[strength - 1] || '#e74c3c';
            }
        }
        
        function checkPasswordMatch() {
            const password = passwordInput.value;
            const confirm = confirmInput.value;
            
            if (!password || !confirm) return;
            
            if (password === confirm) {
                confirmInput.style.borderColor = '#2ecc71';
            } else {
                confirmInput.style.borderColor = '#e74c3c';
            }
        }
    </script>
</body>
</html>