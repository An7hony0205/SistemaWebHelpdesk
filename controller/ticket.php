<?php 
    require_once("../config/conexion.php");
    require_once("../models/Ticket.php");
    $ticket = new Ticket();

    if(isset($_SESSION["usu_id"])){
        switch($_GET["op"]){
        case "insert":
            $ticket->insert_ticket($_POST["usu_id"],$_POST["cat_id"],$_POST["tick_titulo"],$_POST["tick_descrip"]);
        break;

        case "update":
            $ticket->update_ticket($_POST["tick_id"]);
            $ticket->insert_ticketdetalle_cerrar($_POST["tick_id"],$_POST["usu_id"]);
        break;

        case "listar_x_usu":
            $datos=$ticket->listar_ticket_x_usu($_POST["usu_id"]);
            $data = Array();
            foreach($datos as $row){
                $sub_array = array();
                $sub_array[] = $row["tick_id"];
                $sub_array[] = htmlspecialchars($row["cat_nom"], ENT_QUOTES, 'UTF-8');
                $sub_array[] = htmlspecialchars($row["tick_titulo"], ENT_QUOTES, 'UTF-8');

                if($row["tick_estado"]=="Abierto"){
                     $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
                }else{
                    $sub_array[] = '<span class="label label-pill label-danger">Cerrado</span>';
                }
                
                $sub_array[] = date("d/m/Y H:i:s", strtotime($row['fech_crea']));
                $sub_array[] = '<button type="button" onClick="ver('.$row["tick_id"].');" id="'.$row["tick_id"].'" class="btn btn-outline-primary btn-icon"><div><i class="fa fa-edit"></i></div></button>';
                $data[] = $sub_array;
            }

            $results = array(
                "sEcho"=>1,
                "iTotalRecords"=>count($data),
                "iTotalDisplayRecords"=>count($data),
                "aaData"=>$data);
            echo json_encode($results);

        break;


        case "listar":
            $datos=$ticket->listar_ticket();
            $data = Array();
            foreach($datos as $row){
                $sub_array = array();
                $sub_array[] = $row["tick_id"];
                $sub_array[] = htmlspecialchars($row["cat_nom"], ENT_QUOTES, 'UTF-8');
                $sub_array[] = htmlspecialchars($row["tick_titulo"], ENT_QUOTES, 'UTF-8');

                if($row["tick_estado"]=="Abierto"){
                     $sub_array[] = '<span class="label label-pill label-success">Abierto</span>';
                }else{
                    $sub_array[] = '<span class="label label-pill label-danger">Cerrado</span>';
                }


                $sub_array[] = date("d/m/Y H:i:s", strtotime($row['fech_crea']));
                $sub_array[] = '<button type="button" onClick="ver('.$row["tick_id"].');" id="'.$row["tick_id"].'" class="btn btn-outline-primary btn-icon"><div><i class="fa fa-eye"></i></div></button>';
                $data[] = $sub_array;
            }

            $results = array(
                "sEcho"=>1,
                "iTotalRecords"=>count($data),
                "iTotalDisplayRecords"=>count($data),
                "aaData"=>$data);
            echo json_encode($results);

        break;

        case "listardetalle":
                $tick_id = isset($_POST["tick_id"]) ? $_POST["tick_id"] : 0;
                $datos = $ticket->listar_ticketdetalle_x_ticket($tick_id);
                ?>
                    <?php
                    foreach($datos as $row){
                        ?>
                            <article class="activity-line-item box-typical">
                                <div class="activity-line-date">
                                    <?php echo date("d/m/Y", strtotime($row['fech_crea']));?>
                                </div>
                                <header class="activity-line-item-header">
                                    <div class="activity-line-item-user">
                                        <div class="activity-line-item-user-photo">
                                            <a href="#">
                                                <img src="../../public/<?php echo $row['rol_id'] ?>.png" alt="">
                                            </a>
                                        </div>
                                        <div class="activity-line-item-user-name"><?php echo htmlspecialchars($row['usu_nom'].' '.$row['usu_ape'], ENT_QUOTES, 'UTF-8');?></div>
                                        <div class="activity-line-item-user-status">
                                            <?php 
                                                if ($row['rol_id']==1){
                                                    echo'Usuario';
                                                }else{
                                                    echo 'Soporte';

                                                }
                                        ?></div>
                                    </div>
                                </header>
                                <div class="activity-line-action-list">
                                    <section class="activity-line-action">
                                        <div class="time"><?php echo date("H:i:s", strtotime($row['fech_crea']));?></div>
                                        <div class="cont">
                                            <div class="cont-in">
                                                <?php echo strip_tags($row["tickd_descrip"], '<p><a><b><i><u><strong><em><br><ul><ol><li><span><div><h1><h2><h3><h4><h5><h6><img><hr><font><blockquote>'); ?>
                                            </div>
                                        </div>
                                    </section>
				            </article>
                        <?php
                    }
                    ?>
                <?php
        break;


        case "mostrar":
            $datos = $ticket->listar_ticket_x_id($_POST["tick_id"]);
            if (is_array($datos) == true and count($datos) > 0){
                foreach($datos as $row)
                {
                    $output["tick_id"]     = $row["tick_id"];
                    $output["usu_id"]      = $row["usu_id"];
                    $output["cat_id"]      = $row["cat_id"];
                    $output["tick_titulo"] = htmlspecialchars($row["tick_titulo"], ENT_QUOTES, 'UTF-8');
                    $output["tick_descrip"] = strip_tags($row["tick_descrip"], '<p><a><b><i><u><strong><em><br><ul><ol><li><span><div><h1><h2><h3><h4><h5><h6><img><hr><font><blockquote>');
                    if($row["tick_estado"]=="Abierto"){
                        $output["tick_estado"] = '<span class="label label-pill label-success">Abierto</span>';
                    }else{
                        $output["tick_estado"] = '<span class="label label-pill label-danger">Cerrado</span>';
                    }

                    $output["tick_estado_texto"] = $row["tick_estado"];

                    $output["fech_crea"]    = date("d/m/Y H:i:s", strtotime($row["fech_crea"]));
                    $output["usu_nom"]      = $row["usu_nom"];
                    $output["usu_ape"]      = $row["usu_ape"];
                    $output["cat_nom"]      = $row["cat_nom"];
                }

                echo json_encode($output);
            }
        break;


        case "insertdetalle":
            $ticket->insert_ticketdetalle($_POST["tick_id"],$_POST["usu_id"],$_POST["tickd_descrip"]);
        break;



        case "total":
            $datos = $ticket->get_ticket_count();
            if (is_array($datos) == true and count($datos) > 0){
                foreach($datos as $row)
                {
                    $output["TOTAL"] = $row["TOTAL"];
                }

                echo json_encode($output);
            }
        break;


        case "totalabierto":
            $datos = $ticket->get_ticket_count('Abierto');
            if (is_array($datos) == true and count($datos) > 0){
                foreach($datos as $row)
                {
                    $output["TOTAL"] = $row["TOTAL"];
                }

                echo json_encode($output);
            }
        break;

        case "totalcerrado":
            $datos = $ticket->get_ticket_count('Cerrado');
            if (is_array($datos) == true and count($datos) > 0){
                foreach($datos as $row)
                {
                    $output["TOTAL"] = $row["TOTAL"];
                }

                echo json_encode($output);
            }
        break;


        case "grafico";
            $datos=$ticket->get_ticket_grafico();
            echo json_encode($datos);
        break;
    }
    }
