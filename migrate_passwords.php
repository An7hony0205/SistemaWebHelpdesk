<?php
require_once("config/conexion.php");

class Migrator extends Conectar {
    public function migrate() {
        $conectar = parent::Conexion();
        
        // 1. Alter table to ensure usu_pass is long enough (varchar 255)
        echo "Alterando la tabla tm_usuario para usu_pass VARCHAR(255)...\n";
        $sql = "ALTER TABLE tm_usuario MODIFY usu_pass VARCHAR(255) NOT NULL;";
        try {
            $conectar->exec($sql);
            echo "Tabla alterada con éxito.\n";
        } catch (Exception $e) {
            echo "Error al alterar tabla: " . $e->getMessage() . "\n";
        }

        // 2. Hash existing passwords
        echo "Encriptando contraseñas existentes...\n";
        $sql = "SELECT usu_id, usu_pass FROM tm_usuario WHERE usu_pass NOT LIKE '$2y$10$%'";
        $stmt = $conectar->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll();

        $count = 0;
        foreach ($usuarios as $user) {
            // Verify it's not already a hash
            if (!password_get_info($user['usu_pass'])['algo']) {
                $hashed = password_hash($user['usu_pass'], PASSWORD_BCRYPT);
                $update = "UPDATE tm_usuario SET usu_pass = ? WHERE usu_id = ?";
                $stmtUpdate = $conectar->prepare($update);
                $stmtUpdate->execute([$hashed, $user['usu_id']]);
                $count++;
            }
        }
        echo "Se encriptaron $count contraseñas.\n";
    }
}

$migrator = new Migrator();
$migrator->migrate();
