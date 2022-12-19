<?php
require_once ('../Conexion/conexion.php');
require_once ('../Conexion/ConexionPDO.php');
require_once ('../jsonPptal/funcionesPptal.php');
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO();
$anno   = $_SESSION['anno'];
$nanno  = anno($anno);
$action = $_REQUEST['action'];
switch ($action){
    case 1:
       #Buscar todas las parametrizaciones 
        $rowpa = $con->Listar("SELECT GROUP_CONCAT(DISTINCT id_unico)  
            FROM gf_parametrizacion_anno 
            WHERE anno = '$nanno'  
                AND id_unico != $anno ");
        echo $rowpa[0][0];
    break;
    case 2:
        $n = 0;
        $id_param = $_REQUEST['id'];
        #BUSCAN TODAS LAS CUENTAS
        $cuentas = "SELECT id_unico, codi_cuenta FROM gf_cuenta  WHERE parametrizacionanno = $id_param ORDER BY codi_cuenta ASC";
        $cuentas = $mysqli->query($cuentas);
        #VARIABLE CONTEO DE ACTUALIZACION

        #SI HAY CUENTAS ENCONTRADASS
        if(mysqli_num_rows($cuentas)>0){
                while ($row = mysqli_fetch_array($cuentas)) {
                    #ASIGNAR EL CODIGO A UNA VARIABLE
                    $codigo = str_replace(' ', '', $row[1]);
                    #CONTAR LA CANTIDAD DE DIGITOS DEL CODIGO
                    $cant = strlen($codigo);
                    #SI LA CANTIDAD ES UNO NO SE HACE NADA
                    if ($cant == 1) {

                    }else{
                    #SI NO 
                    #SE DEFINE UNAS VARIABLES CONTEO
                    $ctn = 0;
                    $cant2=$cant;
                    #CICLO PARA BUSCAR PREDECESOR
                    for($i = 0;$i <=$cant;$i++){
                        #SI EL CODIGO TIENE DOS DIGITOS
                        $men = substr($codigo,0,-1);

                        #CON EL CODIGO HALLADO BUSCAMOS UN CODIGO IGUAL EL CUAL SERIA EL PREDECESOR
                         $sql = "SELECT DISTINCTROW
                                       PADRE.id_unico,                        
                                       PADRE.codi_cuenta,
                                       PADRE.nombre 
                                FROM
                                       gf_cuenta PADRE
                                WHERE
                                    PADRE.parametrizacionanno = $id_param  
                                    AND PADRE.codi_cuenta = '$men'";

                                $ctn = $ctn + 1;

                            $codigo = $men;
                            $cant2=$cant2-1;
                        $query = $mysqli->query($sql);
                        if (mysqli_num_rows($query)>0) {

                            #SI HAY ALGUNA COINCIDENCIA 
                            #SE PASA A REALIZAR LA ACTUALIZACION
                            $pred = mysqli_fetch_row($query);
                            $predecesor = $pred[0];
                            $update = "UPDATE gf_cuenta SET predecesor ='$predecesor' WHERE id_unico = '$row[0]'";
                            $result= $mysqli->query($update);
                            if($result==true){
                                #SE CUENTA LA ACTUALIZACION
                                $n+=1;
                            }
                            #VARIABLE ROMPE EL CICLO
                            $cant=0;
                            $ctn=0;
                        } else {

                        }

                    }          
                   }
                }
            }
        
        $mensaje= $n." Cuentas Actualizadas";
        //
        echo $n;
    break;
}

#Buscar todas las parametrizaciones 
//        $rowpa = $con->Listar("SELECT DISTINCT id_unico 
//            FROM gf_parametrizacion_anno 
//            WHERE anno = '$nanno'  
//                AND id_unico != $anno");
//        $n = 0;
//        for ($z = 0; $z < count($rowpa); $z++) {
//           echo $id_param = $rowpa[$z][0];
//            #BUSCAN TODAS LAS CUENTAS
//            $cuentas = "SELECT id_unico, codi_cuenta FROM gf_cuenta  WHERE parametrizacionanno = $id_param ORDER BY codi_cuenta ASC";
//            $cuentas = $mysqli->query($cuentas);
//            #VARIABLE CONTEO DE ACTUALIZACION
//
//            #SI HAY CUENTAS ENCONTRADASS
//            if(mysqli_num_rows($cuentas)>0){
//                while ($row = mysqli_fetch_array($cuentas)) {
//                    #ASIGNAR EL CODIGO A UNA VARIABLE
//                    $codigo = str_replace(' ', '', $row[1]);
//                    #CONTAR LA CANTIDAD DE DIGITOS DEL CODIGO
//                    $cant = strlen($codigo);
//                    #SI LA CANTIDAD ES UNO NO SE HACE NADA
//                    if ($cant == 1) {
//
//                    }else{
//                    #SI NO 
//                    #SE DEFINE UNAS VARIABLES CONTEO
//                    $ctn = 0;
//                    $cant2=$cant;
//                    #CICLO PARA BUSCAR PREDECESOR
//                    for($i = 0;$i <=$cant;$i++){
//                        #SI EL CODIGO TIENE DOS DIGITOS
//                        $men = substr($codigo,0,-1);
//
//                        #CON EL CODIGO HALLADO BUSCAMOS UN CODIGO IGUAL EL CUAL SERIA EL PREDECESOR
//                         $sql = "SELECT DISTINCTROW
//                                       PADRE.id_unico,                        
//                                       PADRE.codi_cuenta,
//                                       PADRE.nombre 
//                                FROM
//                                       gf_cuenta PADRE
//                                WHERE
//                                    PADRE.parametrizacionanno = $id_param  
//                                    AND PADRE.codi_cuenta = '$men'";
//
//                                $ctn = $ctn + 1;
//
//                            $codigo = $men;
//                            $cant2=$cant2-1;
//                        $query = $mysqli->query($sql);
//                        if (mysqli_num_rows($query)>0) {
//
//                            #SI HAY ALGUNA COINCIDENCIA 
//                            #SE PASA A REALIZAR LA ACTUALIZACION
//                            $pred = mysqli_fetch_row($query);
//                            $predecesor = $pred[0];
//                            $update = "UPDATE gf_cuenta SET predecesor ='$predecesor' WHERE id_unico = '$row[0]'";
//                            $result= $mysqli->query($update);
//                            if($result==true){
//                                #SE CUENTA LA ACTUALIZACION
//                                $n+=1;
//                            }
//                            #VARIABLE ROMPE EL CICLO
//                            $cant=0;
//                            $ctn=0;
//                        } else {
//
//                        }
//
//                    }          
//                   }
//                }
//            }
//        }
//        $mensaje= $n." Cuentas Actualizadas";
//        //
//        echo json_encode($mensaje);