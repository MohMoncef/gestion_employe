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

// Get salary mass by service
$query = "SELECT s.nom as service_nom, 
                 COUNT(DISTINCT e.id) as nb_employes,
                 SUM(sal.montant) as total_salaires
          FROM Service s
          LEFT JOIN Employe e ON s.id = e.serviceid
          LEFT JOIN Salaire sal ON e.id = sal.employeid 
                AND MONTH(sal.datePaiement) = MONTH(CURDATE())
                AND YEAR(sal.datePaiement) = YEAR(CURDATE())
          GROUP BY s.id
          ORDER BY s.nom";

$stmt = $db->prepare($query);
$stmt->execute();
$rapports = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total salary mass
$query_total = "SELECT SUM(montant) as total_masse FROM Salaire 
                WHERE MONTH(datePaiement) = MONTH(CURDATE()) 
                AND YEAR(datePaiement) = YEAR(CURDATE())";
$stmt_total = $db->prepare($query_total);
$stmt_total->execute();
$total = $stmt_total->fetch(PDO::FETCH_ASSOC);
$total_masse = $total['total_masse'] ?? 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapports et Statistiques</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1><i class="fas fa-users"></i> Gestion des Employés</h1>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="employes.php"><i class="fas fa-user-tie"></i> Employés</a></li>
                    <li><a href="services.php"><i class="fas fa-building"></i> Services</a></li>
                    <li><a href="salaires.php"><i class="fas fa-money-bill-wave"></i> Salaires</a></li>
                    <li><a href="rapports.php" class="active"><i class="fas fa-chart-bar"></i> Rapports</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="content-header">
                <h2>Rapports et Statistiques</h2>
            </div>

            <div class="dashboard">
                <div class="stats">
                    <div class="stat-card">
                        <h3><i class="fas fa-money-bill-wave"></i> Masse Salariale Totale (Mois)</h3>
                        <p><?php echo number_format($total_masse, 2, ',', ' '); ?> €</p>
                    </div>
                </div>

                <div class="table-container">
                    <h3>Répartition de la Masse Salariale par Service</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Service</th>
                                <th>Nombre d'Employés</th>
                                <th>Total des Salaires</th>
                                <th>Pourcentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rapports as $rapport): 
                                $pourcentage = $total_masse > 0 ? ($rapport['total_salaires'] / $total_masse) * 100 : 0;
                            ?>
                            <tr>
                                <td><?php echo $rapport['service_nom']; ?></td>
                                <td><?php echo $rapport['nb_employes']; ?></td>
                                <td><?php echo number_format($rapport['total_salaires'] ?? 0, 2, ',', ' '); ?> €</td>
                                <td><?php echo number_format($pourcentage, 2, ',', ' '); ?> %</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="table-container">
                    <h3>Graphique de Répartition</h3>
                    <canvas id="salaryChart" width="400" height="200"></canvas>
                </div>
            </div>
        </main>

        <footer>
            <p>Système de Gestion des Employés - Projet #16 &copy; <?php echo date('Y'); ?></p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Prepare data for chart
            const services = [];
            const salaries = [];
            const colors = [
                '#3498db', '#2ecc71', '#e74c3c', '#f39c12', 
                '#9b59b6', '#1abc9c', '#d35400', '#34495e'
            ];
            
            <?php foreach ($rapports as $rapport): ?>
                services.push("<?php echo $rapport['service_nom']; ?>");
                salaries.push(<?php echo $rapport['total_salaires'] ?? 0; ?>);
            <?php endforeach; ?>
            
            // Create chart
            const ctx = document.getElementById('salaryChart').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: services,
                    datasets: [{
                        label: 'Masse Salariale (€)',
                        data: salaries,
                        backgroundColor: colors.slice(0, services.length),
                        borderColor: colors.slice(0, services.length),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(value);
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>