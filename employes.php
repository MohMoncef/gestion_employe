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
            // Add new employee
            $query = "INSERT INTO Employe (nom, prenom, dateNaiss, tel, email, adresse, nss, poste, serviceid) 
                     VALUES (:nom, :prenom, :dateNaiss, :tel, :email, :adresse, :nss, :poste, :serviceid)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':nom', $_POST['nom']);
            $stmt->bindParam(':prenom', $_POST['prenom']);
            $stmt->bindParam(':dateNaiss', $_POST['dateNaiss']);
            $stmt->bindParam(':tel', $_POST['tel']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':adresse', $_POST['adresse']);
            $stmt->bindParam(':nss', $_POST['nss']);
            $stmt->bindParam(':poste', $_POST['poste']);
            $stmt->bindParam(':serviceid', $_POST['serviceid']);
            $stmt->execute();
        } elseif ($_POST['action'] == 'update') {
            // Update employee
            $query = "UPDATE Employe SET 
                     nom = :nom, 
                     prenom = :prenom, 
                     dateNaiss = :dateNaiss, 
                     tel = :tel, 
                     email = :email, 
                     adresse = :adresse, 
                     nss = :nss, 
                     poste = :poste, 
                     serviceid = :serviceid 
                     WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_POST['id']);
            $stmt->bindParam(':nom', $_POST['nom']);
            $stmt->bindParam(':prenom', $_POST['prenom']);
            $stmt->bindParam(':dateNaiss', $_POST['dateNaiss']);
            $stmt->bindParam(':tel', $_POST['tel']);
            $stmt->bindParam(':email', $_POST['email']);
            $stmt->bindParam(':adresse', $_POST['adresse']);
            $stmt->bindParam(':nss', $_POST['nss']);
            $stmt->bindParam(':poste', $_POST['poste']);
            $stmt->bindParam(':serviceid', $_POST['serviceid']);
            $stmt->execute();
        } elseif ($_POST['action'] == 'delete') {
            // Delete employee
            $query = "DELETE FROM Employe WHERE id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $_POST['id']);
            $stmt->execute();
        }
    }
}

// Get all employees
$query = "SELECT e.*, s.nom as service_nom FROM Employe e 
          LEFT JOIN Service s ON e.serviceid = s.id 
          ORDER BY e.nom, e.prenom";
$stmt = $db->prepare($query);
$stmt->execute();
$employes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get services for dropdown
$query = "SELECT * FROM Service ORDER BY nom";
$stmt = $db->prepare($query);
$stmt->execute();
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Employés</title>
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
                    <li><a href="employes.php" class="active"><i class="fas fa-user-tie"></i> Employés</a></li>
                    <li><a href="services.php"><i class="fas fa-building"></i> Services</a></li>
                    <li><a href="salaires.php"><i class="fas fa-money-bill-wave"></i> Salaires</a></li>
                    <li><a href="rapports.php"><i class="fas fa-chart-bar"></i> Rapports</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <div class="content-header">
                <h2>Gestion des Employés</h2>
                <button class="btn-add" onclick="showAddForm()"><i class="fas fa-plus"></i> Ajouter un Employé</button>
            </div>

            <!-- Add/Edit Form -->
            <div id="employeeForm" class="form-modal" style="display: none;">
                <div class="form-content">
                    <h3 id="formTitle">Ajouter un Employé</h3>
                    <form id="employeeFormData" method="POST">
                        <input type="hidden" id="employeeId" name="id">
                        <input type="hidden" id="actionType" name="action" value="add">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nom">Nom *</label>
                                <input type="text" id="nom" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label for="prenom">Prénom *</label>
                                <input type="text" id="prenom" name="prenom" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="dateNaiss">Date de Naissance *</label>
                                <input type="date" id="dateNaiss" name="dateNaiss" required>
                            </div>
                            <div class="form-group">
                                <label for="nss">NSS *</label>
                                <input type="text" id="nss" name="nss" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email">
                            </div>
                            <div class="form-group">
                                <label for="tel">Téléphone</label>
                                <input type="tel" id="tel" name="tel">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="poste">Poste</label>
                                <input type="text" id="poste" name="poste">
                            </div>
                            <div class="form-group">
                                <label for="serviceid">Service</label>
                                <select id="serviceid" name="serviceid">
                                    <option value="">Sélectionner un service</option>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?php echo $service['id']; ?>"><?php echo $service['nom']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="adresse">Adresse</label>
                            <textarea id="adresse" name="adresse" rows="3"></textarea>
                        </div>
                        
                        <div class="form-buttons">
                            <button type="submit" class="btn-submit">Enregistrer</button>
                            <button type="button" class="btn-cancel" onclick="hideForm()">Annuler</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Employee List -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom & Prénom</th>
                            <th>Date Naiss.</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Poste</th>
                            <th>Service</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employes as $employe): ?>
                        <tr>
                            <td><?php echo $employe['id']; ?></td>
                            <td><?php echo $employe['prenom'] . ' ' . $employe['nom']; ?></td>
                            <td><?php echo date("d/m/Y", strtotime($employe['dateNaiss'])); ?></td>
                            <td><?php echo $employe['email']; ?></td>
                            <td><?php echo $employe['tel']; ?></td>
                            <td><?php echo $employe['poste']; ?></td>
                            <td><?php echo $employe['service_nom']; ?></td>
                            <td class="actions">
                                <button class="btn-edit" onclick="editEmployee(<?php echo $employe['id']; ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-delete" onclick="deleteEmployee(<?php echo $employe['id']; ?>, '<?php echo $employe['prenom'] . ' ' . $employe['nom']; ?>')">
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
        function showAddForm() {
            document.getElementById('formTitle').innerText = 'Ajouter un Employé';
            document.getElementById('employeeId').value = '';
            document.getElementById('actionType').value = 'add';
            document.getElementById('employeeFormData').reset();
            document.getElementById('employeeForm').style.display = 'block';
        }

        function hideForm() {
            document.getElementById('employeeForm').style.display = 'none';
        }

        function editEmployee(id) {
            // In a real app, you would fetch the employee data via AJAX
            // For simplicity, we'll redirect to an edit page
            window.location.href = 'edit_employe.php?id=' + id;
        }

        function deleteEmployee(id, name) {
            if (confirm('Êtes-vous sûr de vouloir supprimer l\'employé "' + name + '" ?')) {
                // Create a form and submit it
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
    <script src="js/script.js"></script>
</body>
</html>