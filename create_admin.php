<?php
require_once 'config/database.php';

// Check if admin already exists
$query = "SELECT id FROM Users WHERE username = 'admin'";
$stmt = $db->prepare($query);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    // Create admin user
    $username = 'admin';
    $email = 'admin@entreprise.com';
    $password = 'admin123'; // Default password
    $role = 'admin';
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO Users (username, email, password, role) 
              VALUES (:username, :email, :password, :role)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':role', $role);
    
    if ($stmt->execute()) {
        echo "Administrateur créé avec succès!<br>";
        echo "Nom d'utilisateur: admin<br>";
        echo "Mot de passe: admin123<br>";
        echo "<a href='login.php'>Aller à la page de connexion</a>";
    } else {
        echo "Erreur lors de la création de l'administrateur.";
    }
} else {
    echo "L'administrateur existe déjà.";
}
?>