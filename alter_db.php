<?php
require_once 'config/conexion.php';
try {
    $conectar = new PDO("mysql:local=localhost;dbname=anthony_helpdesk","root","");
    $conectar->exec("ALTER TABLE tm_ticket MODIFY tick_descrip MEDIUMTEXT NOT NULL");
    echo "Column type updated successfully.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
