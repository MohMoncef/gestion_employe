<?php include("../config/db.php"); ?>

<form method="GET">
<select name="service">
<option value="">Tous</option>
<?php
$s = $conn->query("SELECT * FROM service");
while($row=$s->fetch_assoc()){
    echo "<option value='{$row['id']}'>{$row['nom']}</option>";
}
?>
</select>
<button>Filtrer</button>
</form>

<table border="1">
<tr><th>Nom</th><th>Poste</th><th>Service</th></tr>

<?php
$where = "";
if(!empty($_GET['service'])){
    $where = "WHERE serviceId=".$_GET['service'];
}

$q = $conn->query("SELECT employe.*, service.nom as sname 
FROM employe JOIN service ON service.id=employe.serviceId $where");

while($r=$q->fetch_assoc()){
    echo "<tr>
        <td>{$r['nom']}</td>
        <td>{$r['poste']}</td>
        <td>{$r['sname']}</td>
    </tr>";
}
?>
</table>
