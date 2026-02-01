<?php
require_once 'config/database.php';

// Require login to access the page
$auth->requireLogin();

// For admin-only pages, use:
// $auth->requireAdmin();
?>
<?php
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion des Employés</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
<header>
    <div class="container">
        <div class="header-top">
            <h1><i class="fas fa-users"></i> Gestion des Employés</h1>
            <?php if ($auth->isLoggedIn()): ?>
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
            <?php else: ?>
            <div class="user-menu">
                <a href="login.php" class="btn-add">
                    <i class="fas fa-sign-in-alt"></i> Connexion
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if ($auth->isLoggedIn()): ?>
        <nav>
            <ul>
                <li><a href="index.php" class="active"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="employes.php"><i class="fas fa-user-tie"></i> Employés</a></li>
                <li><a href="services.php"><i class="fas fa-building"></i> Services</a></li>
                <li><a href="salaires.php"><i class="fas fa-money-bill-wave"></i> Salaires</a></li>
                <li><a href="rapports.php"><i class="fas fa-chart-bar"></i> Rapports</a></li>
                <?php if ($auth->isAdmin()): ?>
                <li><a href="admin.php"><i class="fas fa-cog"></i> Administration</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</header>

        <main>
            <div class="dashboard">
                <h2>Tableau de Bord</h2>
                <div class="stats">
                    <?php
                    // Get total employees
                    $query = "SELECT COUNT(*) as total FROM Employe";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $total_employes = $row['total'];
                    
                    // Get total services
                    $query = "SELECT COUNT(*) as total FROM Service";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $total_services = $row['total'];
                    
                    // Get total salary mass
                    $query = "SELECT SUM(montant) as total FROM Salaire WHERE MONTH(datePaiement) = MONTH(CURDATE())";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $masse_salariale = $row['total'] ?? 0;
                    ?>
                    <div class="stat-card">
                        <h3><i class="fas fa-user-tie"></i> Employés</h3>
                        <p><?php echo $total_employes; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fas fa-building"></i> Services</h3>
                        <p><?php echo $total_services; ?></p>
                    </div>
                    <div class="stat-card">
                        <h3><i class="fas fa-money-bill-wave"></i> Masse Salariale (Mois)</h3>
                        <p><?php echo number_format($masse_salariale, 2, ',', ' '); ?> €</p>
                    </div>
                </div>

                <div class="recent-activities">
                    <h3>Derniers salaires versés</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Employé</th>
                                <th>Montant</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT e.nom, e.prenom, s.montant, s.datePaiement 
                                     FROM Salaire s 
                                     JOIN Employe e ON s.employeid = e.id 
                                     ORDER BY s.datePaiement DESC 
                                     LIMIT 5";
                            $stmt = $db->prepare($query);
                            $stmt->execute();
                            
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $row['prenom'] . " " . $row['nom'] . "</td>";
                                echo "<td>" . number_format($row['montant'], 2, ',', ' ') . " €</td>";
                                echo "<td>" . date("d/m/Y", strtotime($row['datePaiement'])) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>

        <footer>
            <p>Système de Gestion des Employés - Projet #16 &copy; <?php echo date('Y'); ?></p>
        </footer>
    </div>

    <script src="js/script.js"></script>
</body>
</html>