<?php include("../config/db.php"); ?>
<h2>Services</h2>
<a href="add.php">Ajouter service</a>

<table border="1">
<tr><th>ID</th><th>Nom</th><th>Action</th></tr>

<?php
$result = $conn->query("SELECT * FROM service");
while($row = $result->fetch_assoc()){
    echo "<tr>
        <td>{$row['id']}</td>
        <td>{$row['nom']}</td>
        <td><a href='delete.php?id={$row['id']}'>Supprimer</a></td>
    </tr>";
}
?>
</table>
