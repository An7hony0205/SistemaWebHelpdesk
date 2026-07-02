<?php
    /* TODO: Inicio de Sesion */
    session_start();
    require_once("env.php");

    class Conectar{
        protected $dbh;
        protected function Conexion(){
            try {
                //TODO: Cadena de Conexion Local
				$conectar = $this->dbh = new PDO("mysql:local=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
				return $conectar;
			} catch (Exception $e) {
				print "¡Error BD!: Ocurrió un problema al conectar con la base de datos: " . $e->getMessage() . "<br/>";
				die();
			}
        }

        /* TODO: Set Name para utf 8 español - evitar tener problemas con las tildes */
        public function set_names(){
			return $this->dbh->query("SET NAMES 'utf8'");
        }

    
        public static function ruta(){
			return $_ENV['APP_URL'] . "view/";
		}

    }
