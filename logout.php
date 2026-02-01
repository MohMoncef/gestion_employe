<?php
require_once 'config/database.php';

if ($auth->logout()) {
    header('Location: login.php');
    exit();
}
?>