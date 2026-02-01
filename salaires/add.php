<?php include("../config/db.php"); ?>

<form method="POST">
<select name="employeId">
<?php
$e = $conn->query("SELECT * FROM employe");
while($row=$e->fetch_assoc()){
    echo "<option value='{$row['id']}'>{$row['nom']}</option>";
}
?>
</select>

<input type="number" name="montant" placeholder="Montant">
<input type="date" name="datePaiement">
<button name="save">Enregistrer</button>
</form>

<?php
if(isset($_POST['save'])){
    $conn->query("INSERT INTO salaire(employeId,montant,datePaiement)
    VALUES('{$_POST['employeId']}','{$_POST['montant']}','{$_POST['datePaiement']}')");
}
?>
