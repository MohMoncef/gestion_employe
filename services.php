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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $query = "INSERT INTO Service (nom) VALUES (:nom)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nom', $_POST['nom']);
            $stmt->execute();
        } elseif ($_POST['action'] == 'update') {
            $query = "UPDATE Service SET nom = :nom WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_POST['id']);
            $stmt->bindParam(':nom', $_POST['nom']);
            $stmt->execute();
        } elseif ($_POST['action'] == 'delete') {
            $query = "DELETE FROM Service WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_POST['id']);
            $stmt->execute();
        }
    }
}

// Get all services
$query = "SELECT s.*, COUNT(e.id) as employe_count 
          FROM Service s 
          LEFT JOIN Employe e ON s.id = e.serviceid 
          GROUP BY s.id 
          ORDER BY s.nom";
$stmt = $db->prepare($query);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Services</title>
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
                    <li><a href="services.php" class="active"><i class="fas fa-building"></i> Services</a></li>
                    <li><a href="salaires.php"><i class="fas fa-money-bill-wave"></i> Salaires</a></li>
                    <li><a href="rapports.php"><i class="fas fa-chart-bar"></i> Rapports</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="content-header">
                <h2>Gestion des Services</h2>
                <button class="btn-add" onclick="showAddServiceForm()"><i class="fas fa-plus"></i> Ajouter un Service</button>
            </div>

            <!-- Service Form -->
            <div id="serviceForm" class="form-modal" style="display: none;">
                <div class="form-content">
                    <h3 id="serviceFormTitle">Ajouter un Service</h3>
                    <form method="POST">
                        <input type="hidden" id="serviceId" name="id">
                        <input type="hidden" id="serviceAction" name="action" value="add">
                        
                        <div class="form-group">
                            <label for="serviceNom">Nom du Service *</label>
                            <input type="text" id="serviceNom" name="nom" required>
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" class="btn-submit">Enregistrer</button>
                            <button type="button" class="btn-cancel" onclick="hideServiceForm()">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Services List -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom du Service</th>
                            <th>Nombre d'Employés</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?php echo $service['id']; ?></td>
                            <td><?php echo $service['nom']; ?></td>
                            <td><?php echo $service['employe_count']; ?></td>
                            <td class="actions">
                                <button class="btn-edit" onclick="editService(<?php echo $service['id']; ?>, '<?php echo addslashes($service['nom']); ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteService(<?php echo $service['id']; ?>, '<?php echo addslashes($service['nom']); ?>')">
                                    <i class="fas fa-trash"></i>
                                </button>
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
        function showAddServiceForm() {
            document.getElementById('serviceFormTitle').innerText = 'Ajouter un Service';
            document.getElementById('serviceId').value = '';
            document.getElementById('serviceAction').value = 'add';
            document.getElementById('serviceNom').value = '';
            document.getElementById('serviceForm').style.display = 'block';
        }

        function hideServiceForm() {
            document.getElementById('serviceForm').style.display = 'none';
        }

        function editService(id, nom) {
            document.getElementById('serviceFormTitle').innerText = 'Modifier le Service';
            document.getElementById('serviceId').value = id;
            document.getElementById('serviceAction').value = 'update';
            document.getElementById('serviceNom').value = nom;
            document.getElementById('serviceForm').style.display = 'block';
        }

        function deleteService(id, nom) {
            if (confirm('Êtes-vous sûr de vouloir supprimer le service "' + nom + '" ?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'delete';
                
                form.appendChild(idInput);
                form.appendChild(actionInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>