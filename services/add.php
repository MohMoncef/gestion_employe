<?php include("../config/db.php"); ?>

<form method="POST">
    <input type="text" name="nom" placeholder="Nom du service" required>
    <button name="save">Ajouter</button>
</form>

<?php
if (isset($_POST['save'])) {
    $nom = $_POST['nom'];
    $conn->query("INSERT INTO service(nom) VALUES('$nom')");
    header("Location: list.php");
}
?>
