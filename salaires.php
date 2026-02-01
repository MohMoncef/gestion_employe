<?php
require_once 'config/database.php';

// Require login to access the page
$auth->requireLogin();

// For admin-only pages, use:
// $auth->requireAdmin();
?>-
<?php
require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $query = "INSERT INTO Salaire (employeid, montant, datePaiement) 
                     VALUES (:employeid, :montant, :datePaiement)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':employeid', $_POST['employeid']);
            $stmt->bindParam(':montant', $_POST['montant']);
            $stmt->bindParam(':datePaiement', $_POST['datePaiement']);
            $stmt->execute();
        }
    }
}

// Get all salaries with employee info
$query = "SELECT s.*, e.nom, e.prenom, e.nss 
          FROM Salaire s 
          JOIN Employe e ON s.employeid = e.id 
          ORDER BY s.datePaiement DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$salaires = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get employees for dropdown
$query = "SELECT id, nom, prenom FROM Employe ORDER BY nom, prenom";
$stmt = $db->prepare($query);
$stmt->execute();
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Salaires</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                    <li><a href="salaires.php" class="active"><i class="fas fa-money-bill-wave"></i> Salaires</a></li>
                    <li><a href="rapports.php"><i class="fas fa-chart-bar"></i> Rapports</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="content-header">
                <h2>Gestion des Salaires</h2>
                <button class="btn-add" onclick="showAddSalaryForm()"><i class="fas fa-plus"></i> Ajouter un Salaire</button>
            </div>

            <!-- Salary Form -->
            <div id="salaryForm" class="form-modal" style="display: none;">
                <div class="form-content">
                    <h3>Ajouter un Salaire</h3>
                    <form method="POST">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="employeid">Employé *</label>
                                <select id="employeid" name="employeid" required>
                                    <option value="">Sélectionner un employé</option>
                                    <?php foreach ($employes as $employe): ?>
                                        <option value="<?php echo $employe['id']; ?>">
                                            <?php echo $employe['prenom'] . ' ' . $employe['nom']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="montant">Montant (€) *</label>
                                <input type="number" id="montant" name="montant" step="0.01" min="0" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="datePaiement">Date de Paiement *</label>
                            <input type="date" id="datePaiement" name="datePaiement" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" class="btn-submit">Enregistrer</button>
                            <button type="button" class="btn-cancel" onclick="hideSalaryForm()">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Salaries List -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employé</th>
                            <th>NSS</th>
                            <th>Montant</th>
                            <th>Date de Paiement</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($salaires as $salaire): ?>
                        <tr>
                            <td><?php echo $salaire['id']; ?></td>
                            <td><?php echo $salaire['prenom'] . ' ' . $salaire['nom']; ?></td>
                            <td><?php echo $salaire['nss']; ?></td>
                            <td><?php echo number_format($salaire['montant'], 2, ',', ' '); ?> €</td>
                            <td><?php echo date("d/m/Y", strtotime($salaire['datePaiement'])); ?></td>
                            <td class="actions">
                                <a href="historique.php?employeid=<?php echo $salaire['employeid']; ?>" class="btn-edit" title="Voir l'historique">
                                    <i class="fas fa-history"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer>
            <p>Système de Gestion des Employés - Projet #16 &copy; <?php echo date('Y'); ?></p>
        </footer>
    </div>

    <script>
        function showAddSalaryForm() {
            document.getElementById('salaryForm').style.display = 'block';
        }

        function hideSalaryForm() {
            document.getElementById('salaryForm').style.display = 'none';
        }
    </script>
</body>
</html>