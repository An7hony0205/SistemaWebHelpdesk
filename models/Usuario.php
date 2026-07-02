<?php
    class Usuario extends Conectar {
        
        public function login(){
            $conectar=parent::conexion();
            parent::set_names();
            if(isset($_POST["enviar"])){
                $correo = $_POST["usu_correo"];
                $pass = $_POST["usu_pass"];
                if(empty($correo)and empty($pass)){
                    header("Location:".conectar::ruta()."index.php?m=2");
                    exit();
                }else{
                    $sql = "SELECT * FROM tm_usuario WHERE usu_correo=? AND Est=1";
                    $stmt=$conectar->prepare($sql);
                    $stmt->bindValue(1, $correo);
                    $stmt->execute();
                    $resultado = $stmt->fetch();
                    if (is_array($resultado) and count($resultado)>0 and password_verify($pass, $resultado["usu_pass"])){
                        $_SESSION["usu_id"]=$resultado["usu_id"];
                        $_SESSION["usu_nom"]=$resultado["usu_nom"];
                        $_SESSION["usu_ape"]=$resultado["usu_ape"];
                        $_SESSION["rol_id"]=$resultado["rol_id"];
                        header("Location:".Conectar::ruta()."Home/index.php");
                        exit();

                    }else{
                        header("Location:".Conectar::ruta()."index.php?m=1");
                        exit();
                    }

                }
            }

        }

        public function insert_usuario($usu_nom,$usu_ape,$usu_correo,$usu_pass,$rol_id ){
            $conectar = parent::conexion();
            parent::set_names();
            $usu_pass_hashed = password_hash($usu_pass, PASSWORD_BCRYPT);
            $sql="INSERT INTO tm_usuario (usu_nom, usu_ape, usu_correo, usu_pass, rol_id, fecha_crea, fecha_modi, Est) 
            VALUES (?, ?,?, ?, ?, now(),now(), '1')";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1,$usu_nom);
            $sql->bindValue(2,$usu_ape);
            $sql->bindValue(3,$usu_correo);
            $sql->bindValue(4,$usu_pass_hashed);
            $sql->bindValue(5,$rol_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }


        public function update_usuario($usu_id,$usu_nom,$usu_ape,$usu_correo,$usu_pass,$rol_id ){
            $conectar = parent::conexion();
            parent::set_names();
            
            if (empty($usu_pass)) {
                $sql="UPDATE tm_usuario set
                    usu_nom=?,
                    usu_ape=?,
                    usu_correo=?,
                    rol_id=?
                    WHERE
                    usu_id =?";
                $sql=$conectar->prepare($sql);
                $sql->bindValue(1,$usu_nom);
                $sql->bindValue(2,$usu_ape);
                $sql->bindValue(3,$usu_correo);
                $sql->bindValue(4,$rol_id);
                $sql->bindValue(5,$usu_id);
            } else {
                $usu_pass_hashed = password_hash($usu_pass, PASSWORD_BCRYPT);
                $sql="UPDATE tm_usuario set
                    usu_nom=?,
                    usu_ape=?,
                    usu_correo=?,
                    usu_pass=?,
                    rol_id=?
                    WHERE
                    usu_id =?";
                $sql=$conectar->prepare($sql);
                $sql->bindValue(1,$usu_nom);
                $sql->bindValue(2,$usu_ape);
                $sql->bindValue(3,$usu_correo);
                $sql->bindValue(4,$usu_pass_hashed);
                $sql->bindValue(5,$rol_id);
                $sql->bindValue(6,$usu_id);
            }
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }
        

        public function delete_usuario($usu_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="UPDATE tm_usuario 
                SET 
                    est='0',
                    fech_elim=now() 
                where usu_id=?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1,$usu_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        public function get_usuario(){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="SELECT * FROM tm_usuario where est='1'";
            $sql=$conectar->prepare($sql);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        public function get_usuario_x_id($usu_id){
            $conectar = parent::conexion();
            parent::set_names();
            $sql="SELECT * FROM tm_usuario where usu_id=?";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1,$usu_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }

        public function get_usuario_ticket_count($usu_id, $estado = null){
            $conectar = parent::conexion();
            parent::set_names();
            $sql = "SELECT COUNT(*) as TOTAL FROM tm_ticket WHERE usu_id = ?";
            if ($estado !== null) {
                $sql .= " AND tick_estado = ?";
            }
            $stmt = $conectar->prepare($sql);
            $stmt->bindValue(1, $usu_id);
            if ($estado !== null) {
                $stmt->bindValue(2, $estado);
            }
            $stmt->execute();
            return $stmt->fetchAll();
        }


        public function get_usuario_grafico($usu_id){
            $conectar=parent::conexion();
            parent::set_names();
            $sql="SELECT tm_categoria.cat_nom as nom,COUNT(*) AS total
                FROM tm_ticket JOIN
                    tm_categoria ON tm_ticket.cat_id = tm_categoria.cat_id
                WHERE
                tm_ticket.est = 1
                and tm_ticket.usu_id = ?
                GROUP BY
                tm_categoria.cat_nom
                ORDER BY total DESC";
            $sql=$conectar->prepare($sql);
            $sql->bindValue(1,$usu_id);
            $sql->execute();
            return $resultado=$sql->fetchAll();
        }
    }
    


