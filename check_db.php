<?php
require_once 'config/conexion.php';
$conectar = new PDO("mysql:local=localhost;dbname=anthony_helpdesk","root","");
$result = $conectar->query("SHOW COLUMNS FROM tm_ticket");
print_r($result->fetchAll(PDO::FETCH_ASSOC));
$result = $conectar->query("SHOW COLUMNS FROM td_ticketdetalle");
print_r($result->fetchAll(PDO::FETCH_ASSOC));
