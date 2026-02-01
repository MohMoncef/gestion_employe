<?php include("../config/db.php"); ?>

<h2>Historique des salaires</h2>

<table border="1">
<tr><th>Employé</th><th>Montant</th><th>Date</th></tr>

<?php
$total = 0;
$q = $conn->query("SELECT employe.nom, salaire.montant, salaire.datePaiement
FROM salaire JOIN employe ON employe.id=salaire.employeId");

while($r=$q->fetch_assoc()){
    $total += $r['montant'];
    echo "<tr>
        <td>{$r['nom']}</td>
        <td>{$r['montant']}</td>
        <td>{$r['datePaiement']}</td>
    </tr>";
}
?>
</table>

<h3>Masse salariale totale : <?php echo $total; ?> DA</h3>
