<?php
###################################################################################
#   **********************      Modificaciones      ******************************#
###################################################################################
#21/08/2018 |Erica G. | Creado
###################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once '../jsonPptal/funcionesPptal.php';
require ('../json/registrarMovimientoAlmacenJson.php');
session_start();
$con = new ConexionPDO();

switch ($_REQUEST['action']) {
    #** Validar Fechas **#
    case 1:
        $asociado = $_REQUEST['asociado'];
        $fecha    = fechaC($_REQUEST['fecha']);
        #** Buscar Fecha Del Asociado **#
        $fa = $con->Listar("SELECT fecha FROM gf_movimiento WHERE id_unico = $asociado");
        $fechaa   = $fa[0][0];
        if($fecha<$fechaa){
            echo 1;
        } else {
            echo 0;
        }
        
    break;
    #** Agregar Asociado **#
    case 2:
        $id_asoc  = $_REQUEST['asociado'];
        $id_mov   = $_REQUEST['idmovimiento'];
        $r        = 0;
        $details = movimiento::get_detail_mov2($id_asoc);
        for ($a = 0;$a < count($details); $a++) {
            $values = movimiento::get_values_detail($details[$a]);
            $dataAso = movimiento::obtnerDataAsociado($details[$a]);
            $xc = 0;
            foreach ($dataAso as $rc) {
                $xc += $rc[0];
            }
            $xxx = $values[1] - $xc;
            if($xxx > 0){
                $mov = movimiento::save_detail_mov($values[0], $xxx, $values[2], $values[3], $id_mov, $details[$a]);
                if($mov==1){
                    $r =1;
                }
            }
        }
        echo $r;
    break;
}
