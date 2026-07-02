<?php 
    require_once("../config/conexion.php");
    require_once("../models/Categoria.php");
    $categoria = new Categoria();

    if(isset($_SESSION["usu_id"])){
        switch($_GET["op"]){
        case "combo":
            $datos = $categoria->get_categoria();
            /* Si los ddatos son validos */
            if(is_array($datos)==true and count($datos)>0){
                /*  $html = "<option></option>"; */
                foreach($datos as $row)
                {
                    $html.="<option value='".$row['cat_id']."'>".$row['cat_nom']."</option>";
                }
                echo $html;
            }
        break;
    }
    }
