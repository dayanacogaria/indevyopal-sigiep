<?php
##############################################################################################################################################################################
#           Modificaciones
##############################################################################################################################################################################
#07/12/2017 | Erica G. | Archivo Creado
##############################################################################################################################################################################
require_once '../Conexion/ConexionPDO.php';
include_once("../Conexion/conexion.php");
require_once '../jsonPptal/funcionesPptal.php';
session_start();
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];
$fechaE = date('Y-m-d');
$con = new ConexionPDO();
$numAnn = anno($anno);
##******** Buscar Centro De Costo ********#
$cc         = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno");
$cent       = $cc[0][0];
$pro        = $con->Listar("SELECT * FROM gf_proyecto WHERE nombre='Varios' AND compania = $compania");
$proy       = $pro[0][0]; 
$ter        = $con->Listar("SELECT * FROM gf_tercero WHERE numeroidentificacion = '9999999999' AND compania = $compania");
$terc       = $ter[0][0]; 

switch ($_POST['action']){
    #******Guardar Cierre****#    
    case 1:
        $fecha = $_REQUEST['fecha'];
        $tipo  = $_REQUEST['tipo'];
        $num   = $_REQUEST['num'];
        
        $descr = "Comprobante Cierre Año: $numAnn";
        #****Guardar Comprobante***#
        $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                ( `numero`, `fecha`, `descripcion`,`tipocomprobante`,`compania`,
                `parametrizacionanno`,`tercero`, `usuario`, `fecha_elaboracion` ) 
        VALUES (:numero, :fecha, :descripcion, :tipocomprobante,:compania, 
                :parametrizacion_anno, :tercero, :usuario,:fecha_elaboracion)";
        $sql_dato = array(
                array(":numero",$num),
                array(":fecha",$fecha),
                array(":descripcion",$descr),
                array(":tipocomprobante",$tipo),
                array(":compania",$compania),   
                array(":parametrizacion_anno",$anno),
                array(":tercero",$terc),
                array(":usuario",$usuario),
                array(":fecha_elaboracion",$fechaE),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
    //    var_dump($resp);
        if (empty($resp)) {
            #*******Buscar Id Comprobante Creado********#
            $com = $con->Listar("SELECT id_unico FROM gf_comprobante_cnt WHERE numero = $num AND tipocomprobante = $tipo");
            $id_com = $com[0][0];
            $sum4 =0;
            $sum5 =0;
            $sum6 =0;
            $sum7 =0;
#****************************************Guardar Cuentas Grupo 4*************************************#
            $row4 = $con->Listar("SELECT DISTINCT c.id_unico, c.naturaleza  
                                FROM gf_detalle_comprobante dc 
                                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
                                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                WHERE YEAR(cn.fecha)='$numAnn' 
                                AND (c.codi_cuenta LIKE '4%') 
                                AND tc.clasecontable !=20 
                                AND cn.parametrizacionanno = $anno 
                                ORDER BY c.codi_cuenta ASC");
            if(count($row4)>0){
            #******Buscar Valores***#
            for ($i = 0; $i < count($row4); $i++) {
                $v4 = $con->Listar("SELECT SUM(dc.valor) FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                    LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
                                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                    WHERE YEAR(cn.fecha)='$numAnn' AND tc.clasecontable !=20  AND c.id_unico = ".$row4[$i][0]);
                if($row4[$i][1] ==1){
                    $valor = $v4[0][0];
                } else {
                    $valor = $v4[0][0]*-1;
                }
                $valor = ROUND($valor,2);
                $sum4 += $valor;
                $valor = $v4[0][0]*-1;
                #Insertar Detalle
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `descripcion`,`comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `proyecto`, `centrocosto` ) 
                VALUES (:fecha, :descripcion, :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :proyecto,:centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":descripcion",$descr),
                        array(":comprobante",$id_com),
                        array(":valor",($valor)),
                        array(":cuenta",$row4[$i][0]),   
                        array(":naturaleza",$row4[$i][1]),
                        array(":tercero",$terc),
                        array(":proyecto",$proy),
                        array(":centrocosto",$cent),
                );
                
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            #**********Buscar la Cuenta configurada para el grupo 4***********#
            $ctc =$con->Listar("SELECT cf.id_unico, cf.contracuenta, cc.naturaleza  
                                FROM gf_configuracion_cierre_contable cf 
                                LEFT JOIN gf_cuenta c ON c.id_unico = cf.cuentacerrar 
                                LEFT JOIN gf_cuenta cc ON cc.id_unico = cf.contracuenta 
                                WHERE c.codi_cuenta ='4' 
                                AND cf.parametrizacionanno = $anno AND cf.tipocondicion IS NULL");
            #Insertar Detalle
            if(count($ctc)>0) {
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `descripcion`,`comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `proyecto`, `centrocosto` ) 
                VALUES (:fecha, :descripcion, :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :proyecto,:centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":descripcion",$descr),
                        array(":comprobante",$id_com),
                        array(":valor",($sum4)),
                        array(":cuenta",$ctc[0][1]),   
                        array(":naturaleza",$ctc[0][2]),
                        array(":tercero",$terc),
                        array(":proyecto",$proy),
                        array(":centrocosto",$cent),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            }
#****************************************Guardar Cuentas Grupo 5*************************************#
            $row4 = $con->Listar("SELECT DISTINCT c.id_unico, c.naturaleza  
                                FROM gf_detalle_comprobante dc 
                                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
                                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                WHERE YEAR(cn.fecha)='$numAnn' 
                                AND (c.codi_cuenta LIKE '5%') 
                                AND tc.clasecontable !=20 
                                AND cn.parametrizacionanno = $anno 
                                ORDER BY c.codi_cuenta ASC");
            if(count($row4)>0){
            #******Buscar Valores***#
            for ($i = 0; $i < count($row4); $i++) {
            
                $v4 = $con->Listar("SELECT SUM(dc.valor) FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                    LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
                                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                    WHERE YEAR(cn.fecha)='$numAnn' AND tc.clasecontable !=20  AND c.id_unico = ".$row4[$i][0]);
                $valor = $v4[0][0]*-1;
                $valor = ROUND($valor,2);
                $sum5 += $valor;
                #Insertar Detalle
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `descripcion`,`comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `proyecto`, `centrocosto` ) 
                VALUES (:fecha, :descripcion, :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :proyecto,:centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":descripcion",$descr),
                        array(":comprobante",$id_com),
                        array(":valor",($valor)),
                        array(":cuenta",$row4[$i][0]),   
                        array(":naturaleza",$row4[$i][1]),
                        array(":tercero",$terc),
                        array(":proyecto",$proy),
                        array(":centrocosto",$cent),
                );
                
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            } 
            #**********Buscar la Cuenta configurada para el grupo 5***********#
            $ctc =$con->Listar("SELECT cf.id_unico, cf.contracuenta, cc.naturaleza  
                                FROM gf_configuracion_cierre_contable cf 
                                LEFT JOIN gf_cuenta c ON c.id_unico = cf.cuentacerrar 
                                LEFT JOIN gf_cuenta cc ON cc.id_unico = cf.contracuenta 
                                WHERE c.codi_cuenta ='5' 
                                AND cf.parametrizacionanno = $anno AND cf.tipocondicion IS NULL");
            if(count($ctc)>0) {
                $sum5 = $sum5*-1;
                #Insertar Detalle
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `descripcion`,`comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `proyecto`, `centrocosto` ) 
                VALUES (:fecha, :descripcion, :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :proyecto,:centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":descripcion",$descr),
                        array(":comprobante",$id_com),
                        array(":valor",($sum5)),
                        array(":cuenta",$ctc[0][1]),   
                        array(":naturaleza",$ctc[0][2]),
                        array(":tercero",$terc),
                        array(":proyecto",$proy),
                        array(":centrocosto",$cent),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            }
#****************************************Guardar Cuentas Grupo 6*************************************#
            $row4 = $con->Listar("SELECT DISTINCT c.id_unico, c.naturaleza  
                                FROM gf_detalle_comprobante dc 
                                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
                                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                WHERE YEAR(cn.fecha)='$numAnn' 
                                AND (c.codi_cuenta LIKE '6%') AND tc.clasecontable !=20 
                                AND cn.parametrizacionanno = $anno 
                                ORDER BY c.codi_cuenta ASC");
            if(count($row4)>0){
            #******Buscar Valores***#            
            for ($i = 0; $i < count($row4); $i++) {                
                $v4 = $con->Listar("SELECT SUM(dc.valor) FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                    LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
                                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                    WHERE YEAR(cn.fecha)='$numAnn' AND tc.clasecontable !=20 AND c.id_unico = ".$row4[$i][0]);
                $valor = $v4[0][0]*-1;
                $valor = ROUND($valor,2);
                $sum6 += $valor;
                #Insertar Detalle
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `descripcion`,`comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `proyecto`, `centrocosto` ) 
                VALUES (:fecha, :descripcion, :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :proyecto,:centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":descripcion",$descr),
                        array(":comprobante",$id_com),
                        array(":valor",($valor)),
                        array(":cuenta",$row4[$i][0]),   
                        array(":naturaleza",$row4[$i][1]),
                        array(":tercero",$terc),
                        array(":proyecto",$proy),
                        array(":centrocosto",$cent),
                );
                
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            #**********Buscar la Cuenta configurada para el grupo 6***********#
            $ctc =$con->Listar("SELECT cf.id_unico, cf.contracuenta, cc.naturaleza  
                                FROM gf_configuracion_cierre_contable cf 
                                LEFT JOIN gf_cuenta c ON c.id_unico = cf.cuentacerrar 
                                LEFT JOIN gf_cuenta cc ON cc.id_unico = cf.contracuenta 
                                WHERE c.codi_cuenta ='6' 
                                AND cf.parametrizacionanno = $anno AND cf.tipocondicion IS NULL");
            if(count($ctc)>0) {
                $sum6 = $sum6*-1;
                #Insertar Detalle
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `descripcion`,`comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `proyecto`, `centrocosto` ) 
                VALUES (:fecha, :descripcion, :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :proyecto,:centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":descripcion",$descr),
                        array(":comprobante",$id_com),
                        array(":valor",($sum6)),
                        array(":cuenta",$ctc[0][1]),   
                        array(":naturaleza",$ctc[0][2]),
                        array(":tercero",$terc),
                        array(":proyecto",$proy),
                        array(":centrocosto",$cent),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            }
#****************************************Guardar Cuentas Grupo 7*************************************#
            $row4 = $con->Listar("SELECT DISTINCT c.id_unico, c.naturaleza  
                                FROM gf_detalle_comprobante dc 
                                LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
                                LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                WHERE YEAR(cn.fecha)='$numAnn' 
                                AND (c.codi_cuenta LIKE '7%') AND tc.clasecontable !=20 
                                AND cn.parametrizacionanno = $anno 
                                ORDER BY c.codi_cuenta ASC");
            if(count($row4)>0){
            #******Buscar Valores***#            
            for ($i = 0; $i < count($row4); $i++) {                
                $v4 = $con->Listar("SELECT SUM(dc.valor) FROM gf_detalle_comprobante dc 
                                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico 
                                    LEFT JOIN gf_comprobante_cnt cn ON cn.id_unico = dc.comprobante 
                                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                                    WHERE YEAR(cn.fecha)='$numAnn' AND tc.clasecontable !=20  AND c.id_unico = ".$row4[$i][0]);
                $valor = $v4[0][0]*-1;
                $valor = ROUND($valor,2);
                $sum7 += $valor;
                #Insertar Detalle
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `descripcion`,`comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `proyecto`, `centrocosto` ) 
                VALUES (:fecha, :descripcion, :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :proyecto,:centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":descripcion",$descr),
                        array(":comprobante",$id_com),
                        array(":valor",($valor)),
                        array(":cuenta",$row4[$i][0]),   
                        array(":naturaleza",$row4[$i][1]),
                        array(":tercero",$terc),
                        array(":proyecto",$proy),
                        array(":centrocosto",$cent),
                );
                
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            #**********Buscar la Cuenta configurada para el grupo 7***********#
            $ctc =$con->Listar("SELECT cf.id_unico, cf.contracuenta, cc.naturaleza  
                                FROM gf_configuracion_cierre_contable cf 
                                LEFT JOIN gf_cuenta c ON c.id_unico = cf.cuentacerrar 
                                LEFT JOIN gf_cuenta cc ON cc.id_unico = cf.contracuenta 
                                WHERE c.codi_cuenta ='7' 
                                AND cf.parametrizacionanno = $anno AND cf.tipocondicion IS NULL");
            if(count($ctc)>0) {
                $sum7 = $sum7*-1;
                #Insertar Detalle
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `descripcion`,`comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `proyecto`, `centrocosto` ) 
                VALUES (:fecha, :descripcion, :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :proyecto,:centrocosto)";
                $sql_dato = array(
                        array(":fecha",$fecha),
                        array(":descripcion",$descr),
                        array(":comprobante",$id_com),
                        array(":valor",$sum7),
                        array(":cuenta",$ctc[0][1]),   
                        array(":naturaleza",$ctc[0][2]),
                        array(":tercero",$terc),
                        array(":proyecto",$proy),
                        array(":centrocosto",$cent),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            }
            #***********Buscar Cuentas De Utilidades y Pédidas**************#
            
            $sumFinal =$sum4+$sum5+$sum6+$sum7;
            if($sumFinal >0){
                $cf =$con->Listar("SELECT cf.id_unico, cf.contracuenta, c.naturaleza, cf.cuentacerrar , cc.naturaleza 
                                FROM gf_configuracion_cierre_contable cf 
                                LEFT JOIN gf_cuenta c On cf.contracuenta = c.id_unico 
                                LEFT JOIN gf_cuenta cc On cc.id_unico = cf.cuentacerrar 
                                WHERE cf.parametrizacionanno = $anno 
                                AND cf.tipocondicion =2");
            } else {
                $cf =$con->Listar("SELECT cf.id_unico, cf.contracuenta, c.naturaleza, cf.cuentacerrar , cc.naturaleza 
                                FROM gf_configuracion_cierre_contable cf 
                                LEFT JOIN gf_cuenta c On cf.contracuenta = c.id_unico 
                                LEFT JOIN gf_cuenta cc On cc.id_unico = cf.cuentacerrar 
                                WHERE cf.parametrizacionanno = $anno 
                                AND cf.tipocondicion =1");
            }
            #Insertar Detalle Contracuenta
            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                    ( `fecha`, `descripcion`,`comprobante`,`valor`,
                    `cuenta`,`naturaleza`,`tercero`, `proyecto`, `centrocosto` ) 
            VALUES (:fecha, :descripcion, :comprobante,:valor, 
                    :cuenta,:naturaleza, :tercero, :proyecto,:centrocosto)";
            $sql_dato = array(
                    array(":fecha",$fecha),
                    array(":descripcion",$descr),
                    array(":comprobante",$id_com),
                    array(":valor",$sumFinal*-1),
                    array(":cuenta",$cf[0][1]),   
                    array(":naturaleza",$cf[0][2]),
                    array(":tercero",$terc),
                    array(":proyecto",$proy),
                    array(":centrocosto",$cent),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            #Insertar Detalle Cuenta Cerrar 
            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                    ( `fecha`, `descripcion`,`comprobante`,`valor`,
                    `cuenta`,`naturaleza`,`tercero`, `proyecto`, `centrocosto` ) 
            VALUES (:fecha, :descripcion, :comprobante,:valor, 
                    :cuenta,:naturaleza, :tercero, :proyecto,:centrocosto)";
            $sql_dato = array(
                    array(":fecha",$fecha),
                    array(":descripcion",$descr),
                    array(":comprobante",$id_com),
                    array(":valor",$sumFinal*-1),
                    array(":cuenta",$cf[0][3]),   
                    array(":naturaleza",$cf[0][4]),
                    array(":tercero",$terc),
                    array(":proyecto",$proy),
                    array(":centrocosto",$cent),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            
            $rta=1;
        } else {
            $rta =2;
        }
        echo $rta;
    break;
    #*********Eliminar Comprobante******#
    case 2:
        $id = $_REQUEST['id'];
        $dd ="DELETE FROM `gf_detalle_comprobante` WHERE `comprobante`=:comprobante";
        $dato = array(
                array(":comprobante",$id),	
        );
        $res_det = $con->InAcEl($dd,$dato);
        $dc ="DELETE FROM `gf_comprobante_cnt` WHERE `id_unico`=:id_unico";
        $dato = array(
                array(":id_unico",$id),	
        );
        $res_comp = $con->InAcEl($dc,$dato);
        if (empty($res_comp)) {
            $rta=1;
        } else {
            $rta =2;
        }
        echo $rta;
    break;
    #*******Validar Configuracion*********#
    case 3:
        $ctas = $con->Listar("SELECT DISTINCT id_unico, codi_cuenta, nombre FROM gf_cuenta WHERE codi_cuenta in (4,5,6,7) AND parametrizacionanno =$anno");
        $x=0;
        $msj ="Cuentas Sin Configurar <br/>";
        for ($i = 0; $i < count($ctas); $i++) {
            $conf = $con->Listar("SELECT * FROM gf_configuracion_cierre_contable WHERE cuentacerrar=".$ctas[$i][0]." AND parametrizacionanno =$anno");
            if(count($conf)==0){
                $x=1;
                $msj.=$ctas[$i][1].' - '.$ctas[$i][2].'<br/>';
            } 
        }
        $datos = array("msj"=>$msj,"rta"=>$x);
        echo json_encode($datos); 
    break;
}
    

