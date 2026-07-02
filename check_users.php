<?php
require_once 'config/conexion.php';
try {
    $conectar = new PDO("mysql:local=localhost;dbname=anthony_helpdesk","root","");
    $stmt = $conectar->query("SELECT * FROM tm_usuario");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
