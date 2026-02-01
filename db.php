<?php
$conn = new mysqli("localhost", "root", "", "gestion_employes");

if ($conn->connect_error) {
    die("Connection failed");
}
?>
