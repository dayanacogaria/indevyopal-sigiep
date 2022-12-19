<?php
#########################################################################################
#   *****************************   Modificaciones   *******************************    #
#########################################################################################
#18/07/2018 |Erica G. | Centro Costo Presupuestal Privada
#05/07/2018 |Erica G. | Guardar Registro y aprobación con comprobante afectado
#22/09/2017 |Erica G. | Archivo Creado Consultas en caso de que la empresa sea privada
#######################################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require_once './funcionesPptal.php';
session_start();
$con = new ConexionPDO();

switch ($_POST['action']) {
    #*****Realizar Registro y aprobación Automáticamente***#
    case 1:
        $iddis      = $_POST['id'];
        $resultado ='';
        #***Buscar datos de la disponibilidad***#
        $disp = "SELECT id_unico, numero, fecha, fechavencimiento, descripcion, 
                parametrizacionanno, tercero , tipocomprobante 
                FROM gf_comprobante_pptal WHERE id_unico = $iddis";
        $disp = $mysqli->query($disp);
        if(mysqli_num_rows($disp)>0){
            $ddis = mysqli_fetch_row($disp);
            $numero = $ddis[1];
            $fecha  = $ddis[2];
            $fechaV = $ddis[3];
            $desc   = $ddis[4];
            $parm   = $ddis[5];
            $terc   = $ddis[6];
            
            $tipodis = $ddis[7];
            $user   = $_SESSION['usuario'];
            $fechaE = date('Y-m-d');
            $tipoc  = afectado($tipodis);
            $tipoO  = afectado($tipoc);
            $idReg  = "";
            $idApro = "";
            #Buscar Si El Registro Ya Está Insertado 
            $rs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numero =$numero AND tipocomprobante =$tipoc ");
            if(count($rs)>0){
                $idReg = $rs[0][0];
                #Actualizar Tercero 
                $updReg = "UPDATE gf_comprobante_pptal 
                        SET tercero= $terc 
                        WHERE id_unico = $idReg";
                $updReg = $mysqli->query($updReg);
            } else {  
                #***Insertar Registro**#
                $insertReg = "INSERT INTO gf_comprobante_pptal 
                        (numero, fecha, fechavencimiento, 
                        descripcion, parametrizacionanno, tercero, 
                        usuario, fecha_elaboracion, tipocomprobante ) 
                        VALUES('$numero', '$fecha', '$fechaV', 
                        '$desc', $parm, $terc,
                        '$user', '$fechaE', $tipoc)";
                $insertReg = $mysqli->query($insertReg);
                if($insertReg==true){
                    #***Id del Registro***#
                    $idReg = "SELECT MAX(id_unico) FROM gf_comprobante_pptal 
                            WHERE numero = '$numero' AND tipocomprobante = $tipoc";
                    $idReg = $mysqli->query($idReg);
                    $idReg = mysqli_fetch_row($idReg);
                    $idReg = $idReg[0];
                }
            }
            
            if(!empty($idReg)){
                #***Buscar Si Existe Aprobación**#
                $rs = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                    WHERE numero ='$numero' AND tipocomprobante =$tipoO ");
                if(count($rs)>0){
                    $idApro = $rs[0][0];
                    #Actualizar Tercero 
                    $updReg = "UPDATE gf_comprobante_pptal 
                            SET tercero= $terc 
                            WHERE id_unico = $idApro";
                    $updReg = $mysqli->query($updReg);
                } else {  
                    #***Insertar Aprobación**#
                    $insertAprobacion = "INSERT INTO gf_comprobante_pptal 
                            (numero, fecha, fechavencimiento, 
                            descripcion, parametrizacionanno, tercero, 
                            usuario, fecha_elaboracion, tipocomprobante) 
                            VALUES('$numero', '$fecha', '$fechaV', 
                            '$desc', $parm, $terc,
                            '$user', '$fechaE', $tipoO )";
                    $insertAprobacion = $mysqli->query($insertAprobacion);
                    if($insertAprobacion==true){
                        #***Id Aprobación***#
                        $idApro = "SELECT MAX(id_unico) FROM gf_comprobante_pptal 
                                WHERE numero = '$numero' AND tipocomprobante = $tipoO";
                        $idApro = $mysqli->query($idApro);
                        $idApro = mysqli_fetch_row($idApro);
                        $idApro = $idApro[0];
                    }
                }
            } else {
                $resultado = "Error";
            }
            if(!empty($idReg) && !empty($idApro)){
                #*****Buscar Detalles Disponibilidad****#
                $queryDetallesDis ="SELECT DISTINCT id_unico, descripcion, valor, 
                        rubrofuente, conceptoRubro, tercero, proyecto, centro_costo 
                        FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $iddis"; 
                $queryDetallesDis = $mysqli->query($queryDetallesDis);
                if(mysqli_num_rows($queryDetallesDis)>0){
                    while ($rowd = mysqli_fetch_row($queryDetallesDis)) {
                        $idd = $rowd[0];
                        $des = $rowd[1];
                        $val = $rowd[2];
                        $rfu = $rowd[3];
                        $cru = $rowd[4];
                        $ter = $rowd[5];
                        $pry = $rowd[6];
                        if(empty($rowd[7])){
                            $cec ='NULL';
                        } else {
                            $cec = $rowd[7];
                        }
                        $insertR = "INSERT INTO gf_detalle_comprobante_pptal "
                                . "(descripcion, valor, rubrofuente, "
                                . "conceptoRubro, tercero, proyecto, "
                                . "comprobantepptal,comprobanteafectado, centro_costo ) "
                                . "VALUES('$des', $val, $rfu, "
                                . "$cru, $ter, $pry, "
                                . "$idReg,$idd, $cec )";
                        $insertR = $mysqli->query($insertR);
                        #** Buscar id detalle registro 
                        $iddt = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal 
                            WHERE comprobantepptal =$idReg");
                        $idR = $iddt[0][0];
                        $insertApr = "INSERT INTO gf_detalle_comprobante_pptal "
                                . "(descripcion, valor, rubrofuente, "
                                . "conceptoRubro, tercero, proyecto, "
                                . "comprobantepptal,comprobanteafectado, centro_costo) "
                                . "VALUES('$des', $val, $rfu, "
                                . "$cru, $ter, $pry, "
                                . "$idApro,$idR, $cec )";
                        $insertApr = $mysqli->query($insertApr);
                    }
                }
                
                $_SESSION['id_comp_pptal_OP']=$idApro;
                $_SESSION['nuevo_OP'] = 1;
                $_SESSION['nuevo_ED'] = 1;
                $_SESSION['nuevo_CP'] = "";
                $_SESSION['id_comp_pptal_CP']=$idApro;
                $resultado = $idApro;
            } else {
                 $resultado = "Error";
            }
                 
        } else {
            $resultado = "Error";
        }
        echo $resultado;
    break;
    #*** Comprobar Que Tenga Afectación ***#
    case 2:
        $tipo = $_REQUEST['tipo'];
        $rta  = 1;
        $html = "";
        #*** Si Disponiblidad Tiene Afectado ***#
        $afr = afectado($tipo);
        if(!empty($afr)){
            #Registro 
            $afa = afectado($afr);
            if(!empty($afa)){
            } else {
                $html .="Registro No Tiene Comprobante Afectado".'<br/>';
                $rta   = 2;
            }
        } else {
            $html .="Disponibilidad No Tiene Comprobante Afectado".'<br/>';
            $rta   = 2;
        }
        $datos = array("rta"=>$rta,"html"=>$html);
        echo json_encode($datos); 
    break;
}
function afectado ($tipo){
    global $con;
    $af = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE afectado = $tipo");
    return $af[0][0];
}