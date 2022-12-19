<?php
require '../Conexion/ConexionPDO.php';               
require '../Conexion/conexion.php';               
require './../jsonPptal/funcionesPptal.php';               
require './../jsonServicios/funcionesServicios.php';               
ini_set('max_execution_time', 0);
ini_set('memory_limit','160000M');

@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$fechaE     = date('Y-m-d'); 
$anno       = $_SESSION['anno'];
$action     = $_REQUEST['action'];
$cc         = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno");
$centroc    = $cc[0][0];
$usuario_t  = $_SESSION['usuario_tercero'];
switch ($action){
    #*** Guardar O Modificar Lectura ***#
    case 1:
        $periodo = $_REQUEST['periodo'];
        $uvms    = $_REQUEST['uvms'];
        $valor   = $_REQUEST['valor'];
        
        $periodoa   = periodoA($periodo);
        #*** Buscar Lectura Anterior ***#
        $la = $con->Listar("SELECT IF(length(valor)>3, valor, ''), valor, (valor-SUBSTRING(valor, -3)), 
            SUBSTRING(valor, -3), CONCAT(LEFT(valor, 1)+1, SUBSTRING((valor-SUBSTRING(valor, -3)), -3))  FROM gp_lectura 
            WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $periodoa");
        
        if(!empty($la[0][0])){
            IF($valor < $la[0][3]){
                $valor += $la[0][4];
            } else {
                $valor +=$la[0][2];
            }
        }
        
        #*** Buscar Si Existe **#
        $row = $con->Listar("SELECT * FROM gp_lectura 
            WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $periodo");
        if(count($row)>0){
            $id_a = $row[0][0];
            $sql_cons ="UPDATE `gp_lectura` 
                SET `valor`=:valor 
                WHERE `id_unico`=:id_unico";
            $sql_dato = array(
                    array(":valor",$valor),
                    array(":id_unico",$id_a),
            );
        } else {
            $sql_cons ="INSERT INTO `gp_lectura` 
                ( `unidad_vivienda_medidor_servicio`, 
                `periodo`, `valor`,
                `aforador`,`fecha`) 
            VALUES  (:unidad_vivienda_medidor_servicio,  
                :periodo, :valor, 
                :aforador,:fecha)";
            $sql_dato = array(
                    array(":unidad_vivienda_medidor_servicio",$uvms),
                    array(":periodo", $periodo),
                    array(":valor",$valor),
                    array(":aforador",$usuario_t),
                    array(":fecha",date("Y-m-d H:i:s")),
            );
        }
        $resp       = $con->InAcEl($sql_cons,$sql_dato);
        
        if(empty($resp)){
            $rta =0; 
        } else {
            $rta =1;
        }
        echo $rta;
    break;
    
    #*** Calcular Valor ***#
    case 2:
        $periodo = $_REQUEST['periodo'];
        $uvms    = $_REQUEST['uvms'];
        $valor   = $_REQUEST['valor'];
        
        $periodoa   = periodoA($periodo);
        #*** Buscar Lectura Anterior ***#
        
        #*** Buscar Lectura Anterior ***#
        $la = $con->Listar("SELECT IF(length(valor)>3, valor, ''), valor, (valor-SUBSTRING(valor, -3)), 
            SUBSTRING(valor, -3), CONCAT(LEFT(valor, 1)+1, SUBSTRING((valor-SUBSTRING(valor, -3)), -3))  FROM gp_lectura 
            WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $periodoa");
        
        if(!empty($la[0][0])){
            IF($valor < $la[0][3]){
                $valor += $la[0][4];
            } else {
                $valor +=$la[0][2];
            }
        }
        $lac = $la[0][1];
        #*** Calcular Total ***#
        $cantidad = $valor-$lac;
        
        echo $cantidad;
    break;
    #*** Promedio Lecturas ***#
    case 3:
        $periodo = $_REQUEST['periodo'];
        $uvms    = $_REQUEST['uvms'];
        $valor   = $_REQUEST['valor'];
        
        $periodoa   = periodoA($periodo);
        #*** Buscar Lectura Anterior ***#
        $la = $con->Listar("SELECT valor FROM gp_lectura 
            WHERE unidad_vivienda_medidor_servicio = $uvms AND periodo = $periodoa");
        $la = $la[0][0];
        #*** Calcular Total ***#
        $cantidad = $valor-$la;
        
        $promedio = promedioLectura($periodoa, $cantidad,$uvms,$la);
        
        $vp = round(($cantidad * 100)/$promedio);
        echo $vp;
    break;
    #** Modificar Lectura **#
    case 4 :
        $rta   = 0;
        $valor = $_REQUEST['valor'];
        $id_a  = $_REQUEST['lectura'];
        $sql_cons ="UPDATE `gp_lectura` 
            SET `valor`=:valor 
            WHERE `id_unico`=:id_unico";
        $sql_dato = array(
                array(":valor",$valor),
                array(":id_unico",$id_a),
        );
        $resp  = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta = 1;
        }
        echo $rta;
    break;
}