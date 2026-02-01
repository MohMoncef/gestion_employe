<?php include("../config/db.php"); ?>

<form method="POST">
<input name="nom" placeholder="Nom">
<input name="prenom" placeholder="Prénom">
<input type="date" name="dateNaiss">
<input name="poste" placeholder="Poste">

<select name="serviceId">
<?php
$s = $conn->query("SELECT * FROM service");
while($row=$s->fetch_assoc()){
    echo "<option value='{$row['id']}'>{$row['nom']}</option>";
}
?>
</select>

<button name="save">Ajouter</button>
</form>

<?php
if(isset($_POST['save'])){
    $conn->query("INSERT INTO employe(nom,prenom,dateNaiss,poste,serviceId)
    VALUES('{$_POST['nom']}','{$_POST['prenom']}','{$_POST['dateNaiss']}','{$_POST['poste']}','{$_POST['serviceId']}')");
    header("Location: list.php");
}
?>
