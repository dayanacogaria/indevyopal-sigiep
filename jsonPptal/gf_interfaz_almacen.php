<?php 
#####################################################################################################################################################################
#                                               MODIFICACIONES
######################################################################################################################################################################
#04/12/2017 |Erica G. |Creación Interfaz
#23/11/2017 |Erica G. |Archivo Creado
##############################################################################################################################
include_once("../Conexion/ConexionPDO.php");
include_once("../Conexion/conexion.php");
include_once("../jsonPptal/funcionesPptal.php");
session_start();

$action = $_REQUEST['action'];
$anno = $_SESSION['anno'];
$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];
$fechaE = date('Y-m-d');
$obj_con = new ConexionPDO();
$panno = $_SESSION['anno'];
switch ($action) {
    #**********Guardar Configuración 1**********#
    case 1:
        $grupo = $_REQUEST['grupo'];
        $tipom = $_REQUEST['tipom'];
        $ctade = $_REQUEST['ctade'];
        $ctacr = $_REQUEST['ctacr'];
        $sql_cons ="INSERT INTO `gf_configuracion_almacen` 
            ( `plan_inventario`, `tipo_movimiento`, `cuenta_debito`,`cuenta_credito`,`parametrizacion_anno` ) 
            VALUES (:plan_inventario, :tipo_movimiento, :cuenta_debito, :cuenta_credito, :parametrizacion_anno)";
	$sql_dato = array(
            array(":plan_inventario",$grupo),
            array(":tipo_movimiento",$tipom),
            array(":cuenta_debito",$ctade),
            array(":cuenta_credito",$ctacr),
            array(":parametrizacion_anno",$anno),
	);
	$obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);            
        if (empty($obj_resp)) {
            $rowp = $obj_con->Listar("SELECT id_unico FROM gf_plan_inventario WHERE predecesor = $grupo");
            
            if(count($rowp)>0){
                for ($i = 0; $i < count($rowp); $i++) {
                    $plan_i = $rowp[$i][0];
                    #Validar Que No Este Guardado 
                    $vlg = $obj_con->Listar("SELECT * FROM gf_configuracion_almacen 
                        WHERE plan_inventario=$plan_i 
                        AND parametrizacion_anno=$panno AND tipo_movimiento=$tipom");
                    if(count($vlg)>0){}else{
                        $sql_cons ="INSERT INTO `gf_configuracion_almacen` 
                        ( `plan_inventario`, `tipo_movimiento`, `cuenta_debito`,`cuenta_credito`,`parametrizacion_anno` ) 
                        VALUES (:plan_inventario, :tipo_movimiento, :cuenta_debito, :cuenta_credito, :parametrizacion_anno)";

                        $sql_dato = array(
                            array(":plan_inventario",$plan_i),
                            array(":tipo_movimiento",$tipom),
                            array(":cuenta_debito",$ctade),
                            array(":cuenta_credito",$ctacr),
                            array(":parametrizacion_anno",$anno),
                        );
                        $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
                    }
                }
           }
            echo "Información guardada correctamente.";
        }else{
                echo "No se ha podido guardar la información";
        }
    break;
    #**********Guardar Configuración 2**********#
    case 2:
     $grupo = $_REQUEST['grupo'];
     if(empty($_REQUEST['ctaba'])){
         $ctaba = NULL;
     } else {
        $ctaba = $_REQUEST['ctaba'];
     }
     $ctade = $_REQUEST['ctade'];
     $ctacr = $_REQUEST['ctacr'];
    $sql_cons ="INSERT INTO `gf_configuracion_almacen` 
        ( `plan_inventario`, `cuenta_debito`,`cuenta_credito`,`cuenta_baja`,`parametrizacion_anno` ) 
        VALUES (:plan_inventario, :cuenta_debito, :cuenta_credito, :cuenta_baja,:parametrizacion_anno)";
    $sql_dato = array(
        array(":plan_inventario",$grupo),
        array(":cuenta_debito",$ctade),
        array(":cuenta_credito",$ctacr),
        array(":cuenta_baja",$ctaba),
        array(":parametrizacion_anno",$anno),
    );
    $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
         if (empty($obj_resp)) {
            $rowp = $obj_con->Listar("SELECT id_unico FROM gf_plan_inventario WHERE predecesor = $grupo");
            if(count($rowp)>0){
                for ($i = 0; $i < count($rowp); $i++) {
                    $plan_i = $rowp[$i][0];
                    #Validar Que No Este Guardado 
                    $vlg = $obj_con->Listar("SELECT * FROM gf_configuracion_almacen 
                        WHERE plan_inventario=$plan_i 
                        AND parametrizacion_anno=$anno AND tipo_movimiento=$tipom");
                    if(count($vlg)>0){}else{
                        $sql_cons ="INSERT INTO `gf_configuracion_almacen` 
                            ( `plan_inventario`, `cuenta_debito`,`cuenta_credito`,`cuenta_baja`,`parametrizacion_anno` ) 
                            VALUES (:plan_inventario, :cuenta_debito, :cuenta_credito, :cuenta_baja,:parametrizacion_anno)";
                        $sql_dato = array(
                            array(":plan_inventario",$plan_i),
                            array(":cuenta_debito",$ctade),
                            array(":cuenta_credito",$ctacr),
                            array(":cuenta_baja",$ctaba),
                            array(":parametrizacion_anno",$anno),
                        );
                        $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
                    }
                }
            }
             echo "Información guardada correctamente.";
         }else{
             echo "No se ha podido guardar la información ";
         }
    break;
    #**********Eliminar Configuración**********#
    case 3:
    $id = $_REQUEST['id'];
    #Buscar Grupo 
    $select = $obj_con->Listar("SELECT plan_inventario, tipo_movimiento  FROM gf_configuracion_almacen WHERE id_unico = $id");
    $grupo = $select[0][0];
    $tipom = $select[0][1];
    $sql_cons ="DELETE FROM `gf_configuracion_almacen` WHERE `id_unico`=:id_unico";
    $sql_dato = array(
            array(":id_unico",$id),	
    );
    $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);

    if (empty($obj_resp)) {
        $rowp = $obj_con->Listar("SELECT id_unico FROM gf_plan_inventario WHERE predecesor = $grupo");
        if(count($rowp)>0){
            for ($i = 0; $i < count($rowp); $i++) {
                $plan_i = $rowp[$i][0];
                $vlg = $obj_con->Listar("SELECT * FROM gf_configuracion_almacen 
                    WHERE plan_inventario=$plan_i 
                    AND parametrizacion_anno=$anno AND tipo_movimiento=$tipom");
                if(count($vlg)>0){
                    $id_c = $vlg[0][0];
                    $sql_cons ="DELETE FROM `gf_configuracion_almacen` WHERE `id_unico`=:id_unico";
                    $sql_dato = array(
                            array(":id_unico",$id_c),	
                    );
                    $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
                }
            }
        }
            echo "Información eliminada correctamente";
    }else{
            echo "No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia";
    }	
    break;
    #**********Modificar Configuración 1**********#
    case 4:
        $id = $_REQUEST['id'];
        #Buscar Grupo 
        $select = $obj_con->Listar("SELECT plan_inventario, tipo_movimiento  FROM gf_configuracion_almacen WHERE id_unico = $id");
        $grupomod = $select[0][0];
        $tipommod = $select[0][1];
        
        $tipom = $_REQUEST['tipo'];
        $ctade = $_REQUEST['ctad'];
        $ctacr = $_REQUEST['ctac'];
        $sql_cons ="UPDATE `gf_configuracion_almacen` 
            SET `tipo_movimiento`=:tipo_movimiento, 
                `cuenta_debito`=:cuenta_debito, 
                `cuenta_credito`=:cuenta_credito 
                WHERE id_unico=:id_unico ";
        $sql_dato = array(
            array(":tipo_movimiento",$tipom),
            array(":cuenta_debito",$ctade),
            array(":cuenta_credito",$ctacr),
            array(":id_unico",$id),

        );
        $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
        
        if (empty($obj_resp)) {
            $rowp = $obj_con->Listar("SELECT id_unico FROM gf_plan_inventario WHERE predecesor = $grupomod");
            if(count($rowp)>0){
                for ($i = 0; $i < count($rowp); $i++) {
                    $plan_i = $rowp[$i][0];
                    $vlg = $obj_con->Listar("SELECT * FROM gf_configuracion_almacen 
                        WHERE plan_inventario=$plan_i 
                        AND parametrizacion_anno=$anno AND tipo_movimiento=$tipommod");
                    if(count($vlg)>0){
                        $id_c = $vlg[0][0];
                        $sql_cons ="UPDATE `gf_configuracion_almacen` 
                            SET `tipo_movimiento`=:tipo_movimiento, 
                                `cuenta_debito`=:cuenta_debito, 
                                `cuenta_credito`=:cuenta_credito 
                                WHERE id_unico=:id_unico ";
                        $sql_dato = array(
                            array(":tipo_movimiento",$tipom),
                            array(":cuenta_debito",$ctade),
                            array(":cuenta_credito",$ctacr),
                            array(":id_unico",$id_c),

                        );
                        $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
                    }
                }
            }
            
            echo "Información Modificada Correctamente.";
        }else{
            echo "No se ha podido Modificar la información";
        }
    break;
    #**********Modificar Configuración 2**********#
    case 5:
        $id = $_REQUEST['id'];
        if(empty($_REQUEST['tipo'])){
            $tipom = NULL;
        } else {
            $tipom = $_REQUEST['tipo'];
        }
        
        $ctade = $_REQUEST['ctad'];
        $ctacr = $_REQUEST['ctac'];
        $sql_cons ="UPDATE `gf_configuracion_almacen` 
            SET `cuenta_baja`=:cuenta_baja, 
                `cuenta_debito`=:cuenta_debito, 
                `cuenta_credito`=:cuenta_credito 
                WHERE id_unico=:id_unico ";
        $sql_dato = array(
            array(":cuenta_baja",$tipom),
            array(":cuenta_debito",$ctade),
            array(":cuenta_credito",$ctacr),
            array(":id_unico",$id),

        );
        
        $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
        echo $obj_resp;
        
        if (empty($obj_resp)) {
            echo "Información Modificada Correctamente.";
        }else{
            echo "No se ha podido Modificar la información";
        }
    break;
    #**********Cargar Fecha Interfaz Deterioro**********#
    case 6:
        $calendario = CAL_GREGORIAN;
        $mes_con    = explode(",", $_REQUEST['mes']);
        $mes_num    = $mes_con[1];
        $annio  = $_REQUEST['annio'];
        ##**Buscar Año**##
        $anno = anno($annio); 
        $diaF = cal_days_in_month($calendario, $mes_num, $anno); 
        $fecha = $diaF.'/'.$mes_num.'/'.$anno;
        echo trim($fecha);
    break;
    #**********Numero Comprobante Interfaz Deterioro**********#
    case 7:
        $id_tipo = $_REQUEST['comprobante'];
        $numero  = anno($anno);
        $row = $obj_con->Listar('SELECT MAX(numero) 
                FROM gf_comprobante_cnt 
                WHERE tipocomprobante = '.$id_tipo .' 
                AND numero LIKE \''.$numero.'%\'');
        if($row[0][0] == 0)
        {
                $numero .= '000001';
        }
        else
        {
            
                $numero = $row[0][0] + 1;
        }

        echo $numero;
    break;
    #**********Cargar Meses Según e Interfaces Deterioro Hechas**********#
    case 8:
        $annio  = $_REQUEST['annio'];
        ##**Buscar Año**##
        $anno =anno($annio); 
        #***Buscar Meses Año Que Tienen Registros En Deterioro***#
        $md = $obj_con->Listar("SELECT DISTINCT(MONTH(fecha_dep)) FROM ga_depreciacion 
            WHERE YEAR(fecha_dep) ='".$anno."' ORDER BY MONTH(fecha_dep) ASC ");
        $mes ="";
        for($i=0; $i<count($md); $i++){
            $mes .=$md[$i][0]; 
            if((count($md)-1)!=$i){
                $mes .=',';
            }
        }
        $lista = $obj_con->Listar("SELECT id_unico, LOWER(mes), numero 
                        FROM gf_mes WHERE numero IN ($mes) 
                        AND parametrizacionanno = $annio 
                        AND id_unico NOT IN(SELECT mes FROM gf_interfaz_deterioro)");
        for($i=0; $i<count($lista); $i++){
            echo '<option value="'.$lista[$i][0].','.$lista[$i][2].','.$lista[$i][1].'">'.ucwords($lista[$i][1]).'</option>';
        }
    break;
    #**********Validar Configuración Deterioro**********#
    case 9:
        $msj    = "";
        $rta    = 0;
        $annio  = $_REQUEST['anno'];
        ##**Buscar Año**##
        $anno =anno($annio);  
        #***Tomar Mes***#
        $mes_con    = explode(",", $_REQUEST['mes']);
        $mes_num    = $mes_con[1];
        #***Buscar Dígitos Configuración**#
        $con = $obj_con->Listar("SELECT *  
                        FROM gs_parametros_basicos 
                        WHERE nombre='Dígitos Interfaz Inventario'");
        $dig = $con[0][2];
        $pi = $obj_con->Listar("SELECT DISTINCT 
                            pi.id_unico, 
                            pi.codi, 
                            LOWER(pi.nombre) 
                        FROM 
                            ga_depreciacion d 
                        INNER JOIN 
                            gf_producto p ON p.id_unico = d.producto 
                        INNER JOIN 
                            gf_movimiento_producto mp ON mp.producto = p.id_unico 
                        INNER JOIN 
                            gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                        INNER JOIN 
                            gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
                        WHERE 
                            YEAR(d.fecha_dep) ='$anno' 
                            AND MONTH(d.fecha_dep)='$mes_num' 
                            AND LENGTH(pi.codi)>='$dig' AND pi.compania = $compania ");
        if(count($pi)>0){
            #*****Buscar Si Los Planes Inventarios Tienen Configuración*****#
            $c      = 0;
            $html   = "";
            for ($i=0; $i< count($pi); $i++){
                $plani = $pi[$i][0];
                $cc    = $obj_con->Listar("SELECT * 
                                FROM 
                                    gf_configuracion_almacen ca 
                                WHERE 
                                    ca.plan_inventario ='$plani' 
                                    AND ca.tipo_movimiento IS NULL AND ca.parametrizacion_anno  =$panno");
                if(count($cc)>0){}else {
                    $html .=$pi[$i][1].' - '. ucwords($pi[$i][2]).'<br/>';
                    $c +=1;
                }
            }
            if($c>0){
                $rta = 0;
                $msj .= "Los Siguientes Grupos No Estan Configurados:".'<br/>'.$html;
            } else {
                $rta = 1;
            }
            
        } else {
            $msj    = "No Se Encontró Depreciación en este mes";
            $rta    = 2;
        }
        $datos = array("msj"=>$msj,"rta"=>$rta);
        echo json_encode($datos); 
    break;
    #**********Generar Interfaz Deterioro**********#
    case 10:
        $rta    = 1;
        $annio  = $_REQUEST['anno'];
        $num    = trim($_REQUEST['num']);
        $tipo   = $_REQUEST['comp'];
        $terc   = 2;
        $proy   = 2147483647;
        $cecos  = 12;
        ##**Buscar Año**##
        $annof =anno($annio);  
        #***Tomar Mes***#
        $mes_con    = explode(",", $_REQUEST['mes']);
        $mes_num    = $mes_con[1];
        $mes_id     = $mes_con[0];
        $mes_nom    = $mes_con[2];
        $fecha      = fechaC($_REQUEST['fech']);
        $descrip    = "Comprobante Depreciación Mes de ".ucwords($mes_nom)." De ".$annof;
        #***Buscar Dígitos Configuración**#
        $con = $obj_con->Listar("SELECT *  
                        FROM gs_parametros_basicos 
                        WHERE nombre='Dígitos Interfaz Inventario'");
        $dig = $con[0][2];
 
        #*********************Guardar Encabezado Comprobante*********************#
        $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                      ( `numero`, `fecha`,`descripcion`,`tipocomprobante`,`parametrizacionanno`, `compania`,`tercero`, `usuario`,`fecha_elaboracion`  ) 
                VALUES (:numero, :fecha, :descripcion, :tipocomprobante,:parametrizacionanno,:compania,:tercero, :usuario, :fecha_elaboracion )";
        $sql_dato = array(
            array(":numero",$num),
            array(":fecha",$fecha),
            array(":descripcion",$descrip),
            array(":tipocomprobante",$tipo),
            array(":parametrizacionanno",$anno),
            array(":compania",$compania),
            array(":tercero",$terc),
            array(":usuario",$usuario),
            array(":fecha_elaboracion",$fechaE),
         );
         $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
        if (empty($obj_resp)) {
            #*********Buscar Comprobante Que Se Acabo de Guardar*********#
            $compr  = $obj_con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_cnt WHERE numero = '$num' AND tipocomprobante = '$tipo'");
            $cnt    = $compr[0][0];
            #***Buscar Diferentes Planes Inventarios**#
            $pi = $obj_con->Listar("SELECT DISTINCT 
                                pi.id_unico  
                            FROM 
                                ga_depreciacion d 
                            INNER JOIN 
                                gf_producto p ON p.id_unico = d.producto 
                            INNER JOIN 
                                gf_movimiento_producto mp ON mp.producto = p.id_unico 
                            INNER JOIN 
                                gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                            INNER JOIN 
                                gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
                            WHERE 
                                YEAR(d.fecha_dep) ='$annof' 
                                AND MONTH(d.fecha_dep)='$mes_num' 
                                AND LENGTH(pi.codi)>='$dig' AND pi.compania =$compania");

            for ($i=0; $i< count($pi); $i++){
                $plani = $pi[$i][0];
                #*********Consultas Cuentas********#
                $cc    = $obj_con->Listar("SELECT ca.id_unico, cd.id_unico, cd.naturaleza, 
                                    cc.id_unico, cc.naturaleza
                                FROM 
                                    gf_configuracion_almacen ca 
                                LEFT JOIN 
                                    gf_cuenta cd ON ca.cuenta_debito = cd.id_unico 
                                LEFT JOIN 
                                    gf_cuenta cc ON ca.cuenta_credito = cc.id_unico 
                                WHERE 
                                    ca.plan_inventario ='$plani' 
                                    AND ca.tipo_movimiento IS NULL AND ca.parametrizacion_anno = $panno ");
                $cuenta_debito = $cc[0][1];
                $natura_debito = $cc[0][2];
                $cuenta_credit = $cc[0][3];
                $natura_credit = $cc[0][4];
                #*********Consultar Valor********#
                $vl = $obj_con->Listar("SELECT DISTINCT d.id_unico, 
                                d.valor 
                            FROM 
                                ga_depreciacion d 
                            INNER JOIN 
                                gf_producto p ON p.id_unico = d.producto 
                            INNER JOIN 
                                gf_movimiento_producto mp ON mp.producto = p.id_unico 
                            INNER JOIN 
                                gf_detalle_movimiento dm ON mp.detallemovimiento = dm.id_unico 
                            INNER JOIN 
                                gf_plan_inventario pi ON dm.planmovimiento = pi.id_unico 
                            WHERE 
                                YEAR(d.fecha_dep) ='$annof' 
                                AND MONTH(d.fecha_dep)='$mes_num' 
                                AND pi.id_unico='$plani'");
                $valor  = 0;
                for ($v=0; $v < count($vl) ; $v++) { 
                    $valor  += $vl[$v][1];
                }

                
                $valord = $valor;
                $valorc = $valor;
                if($natura_debito==2){
                    $valord =$valord*-1;
                }
                if($natura_credit==1){
                    $valorc =$valorc*-1;
                }
                #**Guardar cuenta débito**#
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                      ( `fecha`,`descripcion`,`valor`,`valorejecucion`, `comprobante`,`cuenta`, `naturaleza`,`tercero`, `proyecto`, `centrocosto`  ) 
                VALUES (:fecha, :descripcion, :valor,:valorejecucion, :comprobante, :cuenta, :naturaleza, :tercero, :proyecto, :centrocosto)";
                $sql_dato = array(
                    array(":fecha",$fecha),
                    array(":descripcion",$descrip),
                    array(":valor",$valord),
                    array(":valorejecucion",$valor),
                    array(":comprobante",$cnt),
                    array(":cuenta",$cuenta_debito),
                    array(":naturaleza",$natura_debito),
                    array(":tercero",$terc),
                    array(":proyecto",$proy),
                    array(":centrocosto",$cecos),
                 );
                $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
                #**Guardar cuenta crédito**#
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                      ( `fecha`,`descripcion`,`valor`,`valorejecucion`, `comprobante`,`cuenta`, `naturaleza`,`tercero`, `proyecto`, `centrocosto`  ) 
                VALUES (:fecha, :descripcion, :valor,:valorejecucion, :comprobante, :cuenta, :naturaleza, :tercero, :proyecto, :centrocosto)";
                $sql_dato = array(
                    array(":fecha",$fecha),
                    array(":descripcion",$descrip),
                    array(":valor",$valorc),
                    array(":valorejecucion",$valor),
                    array(":comprobante",$cnt),
                    array(":cuenta",$cuenta_credit),
                    array(":naturaleza",$natura_credit),
                    array(":tercero",$terc),
                    array(":proyecto",$proy),
                    array(":centrocosto",$cecos),
                 );
                $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);

            }
            #***********Guardar En La Tabla Interfaz ***************#
            $descripcion = "Depreciación Mes de ".ucwords($mes_nom)." De ".$annof;
            $sql_cons ="INSERT INTO `gf_interfaz_deterioro` 
                      ( `mes`,`comprobante`,`descripcion`,`fecha_elaboracion` ) 
                VALUES (:mes, :comprobante, :descripcion,:fecha_elaboracion)";
            $sql_dato = array(
                array(":mes",$mes_id),
                array(":comprobante",$cnt),
                array(":descripcion",$descripcion),
                array(":fecha_elaboracion",$fechaE), 
             );
            $obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
            if(empty($obj_resp)){
                $inte  = $obj_con->Listar("SELECT MAX(id_unico) FROM gf_interfaz_deterioro WHERE mes = '$mes_id' AND comprobante = '$cnt'");
                $id_in = $inte[0][0];    

                $rta ="GF_DETERIORO.php?id=".md5($id_in);
            } else {
                $sql_cons ="DELETE FROM `gf_detalle_comprobante` WHERE `comprobante`=:comprobante";
		$sql_dato = array(
			array(":comprobante",$cnt),	
		);
		$obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
                $sql_cons ="DELETE FROM `gf_comprobante_cnt` WHERE `id_unico`=:id_unico";
		$sql_dato = array(
			array(":id_unico",$cnt),	
		);
		$obj_resp = $obj_con->InAcEl($sql_cons,$sql_dato);
                
                $rta=1;
            }
        } else {
            $rta=1;
        }
            
        echo $rta;
    break;
    case 11:
        $fecha = fechaC($_REQUEST['fecha']);
    break;
}