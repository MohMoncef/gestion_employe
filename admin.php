<?php
require_once 'config/database.php';

// Require admin access
$auth->requireAdmin();

// Get all users
$query = "SELECT * FROM Users ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion des Employés</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-top">
                <h1><i class="fas fa-users"></i> Gestion des Employés</h1>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                        <div class="user-details">
                            <span class="user-name"><?php echo $_SESSION['username']; ?></span>
                            <span class="user-role"><?php echo $_SESSION['role']; ?></span>
                        </div>
                    </div>
                    <a href="logout.php" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                </div>
            </div>
            
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="employes.php"><i class="fas fa-user-tie"></i> Employés</a></li>
                    <li><a href="services.php"><i class="fas fa-building"></i> Services</a></li>
                    <li><a href="salaires.php"><i class="fas fa-money-bill-wave"></i> Salaires</a></li>
                    <li><a href="rapports.php"><i class="fas fa-chart-bar"></i> Rapports</a></li>
                    <li><a href="admin.php" class="active"><i class="fas fa-cog"></i> Administration</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="content-header">
                <h2>Panneau d'Administration</h2>
            </div>

            <div class="dashboard">
                <div class="stats">
                    <div class="stat-card">
                        <h3><i class="fas fa-users"></i> Utilisateurs Totaux</h3>
                        <p><?php echo count($users); ?></p>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fas fa-user-shield"></i> Administrateurs</h3>
                        <p><?php 
                            $adminCount = array_filter($users, function($user) {
                                return $user['role'] === 'admin';
                            });
                            echo count($adminCount);
                        ?></p>
                    </div>
                </div>

                <div class="table-container">
                    <h3>Gestion des Utilisateurs</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom d'utilisateur</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Date d'inscription</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td>
                                    <span class="role-badge <?php echo $user['role']; ?>">
                                        <?php echo $user['role']; ?>
                                    </span>
                                </td>
                                <td><?php echo date("d/m/Y H:i", strtotime($user['created_at'])); ?></td>
                                <td class="actions">
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <button class="btn-edit" onclick="editUser(<?php echo $user['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-delete" onclick="deleteUser(<?php echo $user['id']; ?>, '<?php echo $user['username']; ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <footer>
            <p>Système de Gestion des Employés - Projet #16 &copy; <?php echo date('Y'); ?></p>
        </footer>
    </div>

    <style>
        .role-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .role-badge.admin {
            background: #e3f2fd;
            color: #1976d2;
        }
        
        .role-badge.user {
            background: #f3e5f5;
            color: #7b1fa2;
        }
    </style>
    
    <script>
        function editUser(id) {
            alert('Fonctionnalité de modification à implémenter pour l\'utilisateur ID: ' + id);
        }
        
        function deleteUser(id, username) {
            if (confirm('Êtes-vous sûr de vouloir supprimer l\'utilisateur "' + username + '" ?')) {
                // AJAX call to delete user
                fetch('ajax/delete_user.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Utilisateur supprimé avec succès!');
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue.');
                });
            }
        }
    </script>
</body>
</html>