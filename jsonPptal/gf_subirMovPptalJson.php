<?php
@session_start();
ini_set('max_execution_time', 0);
require '../ExcelR/Classes/PHPExcel/IOFactory.php';                                     
require '../Conexion/ConexionPDO.php'; 
require '../Conexion/conexion.php'; 
require '../jsonPptal/funcionesPptal.php'; 
$con = new ConexionPDO();
$compania = $_SESSION['compania'];
$usuario  = $_SESSION['usuario'];
$fechaE   = date('Y-m-d');
$anno     = $_SESSION['anno'];
$proyecto = 2147483647;
#** Centro costo por parametrizacion **#
$cc       = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $anno");
$centroc  = $cc[0][0];
#** Tercero Varios Compañia ****#
$tv       = $con->Listar("SELECT id_unico FROM gf_tercero WHERE nombreuno = 'Varios' AND compania = $compania");
$tercerov = $tv[0][0];

$inputFileName= $_FILES['file']['tmp_name'];                                       
$objReader = new PHPExcel_Reader_Excel2007();					
$objPHPExcel = PHPExcel_IOFactory::load($inputFileName); 			
$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);				
$total_filas = $objWorksheet->getHighestRow();					
$total_columnas = PHPExcel_Cell::columnIndexFromString($objWorksheet->getHighestColumn());

$action = $_REQUEST['action'];
switch ($action){
    #***********Validaciones Cuentas**********#
    case 1:
        $arraycuentas = array();
        $ctas =0;
        $cc ="<strong>Cuentas Que No Aparecen en el Plan Contable:</strong>";
        $datos =array();
        for ($a = 2; $a <= $total_filas; $a++) {
            $cuentadebito       = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
            $cuentacredito      = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();
            $cuentaiva          = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
            $cuentaimpoconsumo  = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
            
            if(!empty($cuentacredito)){
                #*Buscar Cuenta Crédito
                $cd = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = '$cuentacredito' AND parametrizacionanno = $anno");
                if(count($cd)<=0){
                    if(in_array($cuentacredito, $arraycuentas)) {

                    } else {
                        array_push ( $arraycuentas , $cuentacredito );
                        $cc.='<br/>'.$cuentacredito;
                    }
                    $ctas +=1;
                }
            } 
            if(!empty($cuentadebito)){
                #*Buscar Cuenta Débito
                $cd = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = '$cuentadebito' AND parametrizacionanno = $anno");
                if(count($cd)<=0){
                    if(in_array($cuentadebito, $arraycuentas)) {

                    } else {
                        array_push ( $arraycuentas , $cuentadebito );
                        $cc.='<br/>'.$cuentadebito;
                    }
                    $ctas +=1;
                }
            } 
            if(!empty($cuentaiva)){
                #*Buscar Cuenta Crédito
                $cd = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = '$cuentaiva' AND parametrizacionanno = $anno");
                if(count($cd)<=0){
                    if(in_array($cuentaiva, $arraycuentas)) {

                    } else {
                        array_push ( $arraycuentas , $cuentaiva );
                        $cc.='<br/>'.$cuentaiva;
                    }
                    $ctas +=1;
                }
            } 
            if(!empty($cuentaimpoconsumo)){
                #*Buscar Cuenta Débito
                $cd = $con->Listar("SELECT * FROM gf_cuenta WHERE codi_cuenta = '$cuentaimpoconsumo' AND parametrizacionanno = $anno");
                if(count($cd)<=0){
                    if(in_array($cuentaimpoconsumo, $arraycuentas)) {

                    } else {
                        array_push ( $arraycuentas , $cuentaimpoconsumo );
                        $cc.='<br/>'.$cuentaimpoconsumo;
                    }
                    $ctas +=1;
                }
            } 

        }
        $datos = array("cd"=>$cc,"cc"=>$cc,"rta"=>$ctas);
        echo json_encode($datos); 
    break;
    #Validar Si Ya Existe Comprobante De Saldos Iniciales
    case 2:
        #**Buscar Si Existe
        $numa       = anno($anno);
        $numero     = $numa.'000001';
        $tipoCom    = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE clasepptal = 13 and tipooperacion=1 AND compania =$compania");
        $rta =0;
        if(count($tipoCom)>0){
            $tipoCom    = $tipoCom[0][0];
            $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante =$tipoCom ");
            if(count($bs)>0){
                #Buscar Si Hay Disponibilidades
                $dis = $con->Listar("SELECT * FROM gf_comprobante_pptal c 
                            LEFT JOIN gf_tipo_comprobante_pptal tc ON c.tipocomprobante = tc.id_unico
                            WHERE c.parametrizacionanno = $anno 
                            AND tc.clasepptal = 14 AND tc.tipooperacion = 1");
                if(count($dis)>0){
                    $rta=2;
                } else {
                    $rta=1;
                }
            } 
        } else {
            $rta = 3;
        }
        echo $rta;
    break;
    #**** Guardar Configuración *****#
    case 3:
        # **Definir Arrays ***#
        $arrayRubro     = array();
        $arrayFuente    = array();
        $arrayConcepto  = array();
        #****** Guardar Comprobante Apropiación Inicial ******#
        #**Buscar Si Existe
        $numa       = anno($anno);
        $numero     = $numa.'000001';
        $tipoCom    = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE clasepptal = 13 and tipooperacion=1 AND compania =$compania");
        $tipoCom    = $tipoCom[0][0];
        $fecha      =$numa.'-01-01';
        $descripcion='Comprobante Apropiación Inicial '.$numa;

        $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante =$tipoCom ");

        if(count($bs)>0){
            $id_comprobante = $bs[0][0];
            $sql_cons ="DELETE FROM `gf_detalle_comprobante_pptal` 
                WHERE `comprobantepptal`=:comprobantepptal";
            $sql_dato = array(
                    array(":comprobantepptal",$id_comprobante),	
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        } else {
            #Guardar Comprobante 
            $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                    ( `numero`, `fecha`, 
                    `fechavencimiento`,`descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`) 
            VALUES (:numero, :fecha, 
                    :fechavencimiento,:descripcion,
                    :parametrizacionanno,:tipocomprobante)";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":fechavencimiento",$fecha),
                    array(":descripcion",$descripcion),
                    array(":parametrizacionanno",$anno),
                    array(":tipocomprobante",$tipoCom),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante =$tipoCom ");
            $id_comprobante = $bs[0][0];
        }
        $cfg =0;
        $apg =0;
        
        #**************************************************#
        for ($a = 2; $a <= $total_filas; $a++) {
            $nombre_concepto    = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $cd_rubro_pptal     = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $rubro_pptal        = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $fuente             = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $clase              = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            $vigenciaann        = $numa;
            $vigencia           = 'ACTUAL';
            $cuentadebito       = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
            $cuentacredito      = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();
            $cuentaiva          = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
            $cuentaimpoconsumo  = $objWorksheet->getCellByColumnAndRow(8, $a)->getCalculatedValue();
            $movimiento         = $objWorksheet->getCellByColumnAndRow(11, $a)->getCalculatedValue();
            $valor              = $objWorksheet->getCellByColumnAndRow(12, $a)->getCalculatedValue();
            $sector             = $objWorksheet->getCellByColumnAndRow(9, $a)->getCalculatedValue();
            $destino            = $objWorksheet->getCellByColumnAndRow(10, $a)->getCalculatedValue();
            
            $cod_sector         = $objWorksheet->getCellByColumnAndRow(13, $a)->getCalculatedValue();
            $programa           = $objWorksheet->getCellByColumnAndRow(14, $a)->getCalculatedValue();
            $subprograma        = $objWorksheet->getCellByColumnAndRow(15, $a)->getCalculatedValue();
            $elemento_c         = $objWorksheet->getCellByColumnAndRow(16, $a)->getCalculatedValue();
            $cpc                = $objWorksheet->getCellByColumnAndRow(17, $a)->getCalculatedValue();
            $cod_proyecto       = $objWorksheet->getCellByColumnAndRow(18, $a)->getCalculatedValue();
            $subproyecto        = $objWorksheet->getCellByColumnAndRow(19, $a)->getCalculatedValue();
            
            if(empty($movimiento)){
                $movimiento = 2;
            }
            
            //echo $cd_rubro_pptal.'<br/>';
            #***** Buscar Clase Rubro****#
            $cl = $con->Listar("SELECT id_unico FROM gf_tipo_clase_pptal WHERE nombre = '$clase'");
            $tipoc = $cl[0][0]; 
            #***** Buscar Clase Concepto****#
            $ccl = $con->Listar("SELECT id_unico FROM gf_clase_concepto WHERE nombre = '$clase'");
            $clasec = $ccl[0][0]; 
            #***** Buscar Vigencia ****#
            $vg = $con->Listar("SELECT id_unico FROM gf_tipo_vigencia WHERE nombre = '$vigencia'");
            $vig = $vg[0][0]; 
            #***** Buscar Cuenta Débito ***#
            $cd = $con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = '$cuentadebito' AND parametrizacionanno = $anno");
            $ctaDebito = $cd[0][0];
            #***** Buscar Cuenta Crédito ***#
            $cc = $con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = '$cuentacredito' AND parametrizacionanno = $anno");
            $ctaCredito = $cc[0][0];
            #***** Buscar Cuenta Iva ***#
            $ci = $con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = '$cuentaiva' AND parametrizacionanno = $anno");
            $ctaIva = $ci[0][0];
            #***** Buscar Cuenta Impo ***#
            $cim = $con->Listar("SELECT id_unico FROM gf_cuenta WHERE codi_cuenta = '$cuentaimpoconsumo' AND parametrizacionanno = $anno");
            $ctaImpoconsumo = $cim[0][0];
            #Si El Concepto No Está Vacio
            $id_con =0;
            
            if(!empty($nombre_concepto)){
                #Buscar Si Existe 
                $id_con = $con->Listar("SELECT id_unico FROM gf_concepto WHERE nombre ='".$nombre_concepto."' AND parametrizacionanno = $anno");
                if(!empty($id_con[0][0])){
                    $id_con = $id_con[0][0];
                }else {
                    if(in_array($nombre_concepto, $arrayConcepto)) {

                    } else {
                        array_push ( $arrayConcepto , $nombre_concepto );
                        #***Guardar Concepto
                        $sql_cons ="INSERT INTO `gf_concepto` 
                                ( `nombre`, `clase_concepto`, 
                                `parametrizacionanno`) 
                        VALUES (:nombre, :clase_concepto, 
                                :parametrizacionanno)";
                        $sql_dato = array(
                                array(":nombre",$nombre_concepto),
                                array(":clase_concepto",$clasec),
                                array(":parametrizacionanno",$anno),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);  
                    }
                    #******Consultar Id Concepto***#
                    $id_con = $con->Listar("SELECT id_unico FROM gf_concepto WHERE nombre ='".$nombre_concepto."' AND parametrizacionanno = $anno");
                    $id_con = $id_con[0][0];
                }
            }
          
            
            #**** Si el rubro no está vacío ****#
            $id_r =0;
            if(!empty($cd_rubro_pptal)){
                #Consultar Si Existe Rubro 
                $id_r = '';
                $id_r = $con->Listar("SELECT id_unico FROM gf_rubro_pptal WHERE codi_presupuesto ='$cd_rubro_pptal' AND parametrizacionanno = $anno");
               
                if(!empty($id_r[0][0]) || $id_r[0][0]!='' ){
                    $id_r = $id_r[0][0];
                }else {
                    array_push ( $arrayRubro , $cd_rubro_pptal );
                    #Buscar Sector
                    if(!empty($sector)){
                        $s = $con->Listar("SELECT * FROM gf_sector WHERE nombre = '$sector'");
                        if(count($s)>0){
                            $sec = $s[0][0];
                        } else {
                            $sec = NULL;
                        }
                    } else {
                        $sec = NULL;
                    }

                    #*Buscar destino 
                    if(!empty($destino)){
                        $des = $con->Listar("SELECT * FROM gf_destino WHERE nombre = '$destino'");
                        if(count($des)>0){
                            $destino = $des[0][0];
                        } else {
                            $destino = NULL;
                        }
                    } else {
                        $destino = NULL;
                    }

                    #***Guardar Rubro

                    $sql_cons ="INSERT INTO `gf_rubro_pptal` 
                            ( `nombre`, `codi_presupuesto`, 
                            `movimiento`,`manpac`,`vigencia`,
                            `parametrizacionanno`,`tipoclase`, `tipovigencia`,
                            `sector`, `destino` , 
                            `codigo_sector`, `programa`, `subprograma`, 
                            `elemento_constitutivo`, `clasificacion_productos`, 
                            `codigo_proyecto`, `subproyecto`) 
                    VALUES (:nombre, :codi_presupuesto, 
                            :movimiento, :manpac,:vigencia, 
                            :parametrizacionanno, :tipoclase, :tipovigencia, 
                            :sector, :destino, 
                            :codigo_sector, :programa, :subprograma, 
                            :elemento_constitutivo, :clasificacion_productos, 
                            :codigo_proyecto, :subproyecto)";
                    $sql_dato = array(
                            array(":nombre",$rubro_pptal),
                            array(":codi_presupuesto",$cd_rubro_pptal),
                            array(":movimiento",$movimiento),
                            array(":manpac",3),
                            array(":vigencia",$anno),   
                            array(":parametrizacionanno",$anno),
                            array(":tipoclase",$tipoc),
                            array(":tipovigencia",$vig),
                            array(":sector",$sec),
                            array(":destino",$destino),

                            array(":codigo_sector",$cod_sector),
                            array(":programa",$programa),
                            array(":subprograma",$subprograma),
                            array(":elemento_constitutivo",$elemento_c),
                            array(":clasificacion_productos",$cpc),
                            array(":codigo_proyecto",$cod_proyecto),
                            array(":subproyecto",$subproyecto),

                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    //var_dump('Insert Rubro: '.$resp);
                    #******Consultar Id rubro Guardado***#
                    $id_r = $con->Listar("SELECT id_unico FROM gf_rubro_pptal WHERE codi_presupuesto ='$cd_rubro_pptal' AND parametrizacionanno = $anno");
                    $id_r = $id_r[0][0];
                }
            }
            
            #**** Si la Fuente No Está Vacía ****# 
            $id_f=0;
            
            if(!empty($fuente)){
                #Consultar Si Fuente Existe
                #******Consultar Id Fuente Guardado***#
                $id_f = $con->Listar("SELECT id_unico FROM gf_fuente WHERE nombre ='$fuente' AND parametrizacionanno = $anno");
                if(!empty($id_f[0][0])){
                    $id_f = $id_f[0][0];
                } else {
                    if(in_array($fuente, $arrayFuente)) {

                    } else {
                        array_push ( $arrayFuente , $fuente );
                        #*** Guardar Fuente ***#
                        $sql_cons ="INSERT INTO `gf_fuente` 
                                ( `nombre`,`movimiento`,
                                `parametrizacionanno`,`compania` ) 
                        VALUES (:nombre, :movimiento, 
                                :parametrizacionanno, :compania)";
                        $sql_dato = array(
                                array(":nombre",$fuente),
                                array(":movimiento",1),   
                                array(":parametrizacionanno",$anno),
                                array(":compania",$compania),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    #******Consultar Id Fuente Guardado***#
                    $id_f = $con->Listar("SELECT id_unico FROM gf_fuente WHERE nombre ='$fuente' AND parametrizacionanno = $anno");
                    $id_f = $id_f[0][0];
                }
            }
            #********Si La Fuente Y El Rubro Son Diferentes de 0 Guarda Rubro Fuente*******#
            $id_rf =0;
            if($id_r !=0 && $id_f !=0){
                #Validar Si Existe 
                $id_rf = $con->Listar("SELECT id_unico FROM gf_rubro_fuente WHERE rubro ='$id_r' AND fuente = $id_f");
                
                if(!empty($id_rf[0][0])) { 
                    $id_rf = $id_rf[0][0];
                } else {
                    $sql_cons ="INSERT INTO `gf_rubro_fuente` 
                            ( `rubro`,`fuente` ) 
                    VALUES (:rubro, :fuente)";
                    $sql_dato = array(
                            array(":rubro",$id_r),
                            array(":fuente",$id_f), 
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $id_rf = $con->Listar("SELECT id_unico FROM gf_rubro_fuente WHERE rubro ='$id_r' AND fuente = $id_f");
                    $id_rf = $id_rf[0][0];
                }
            }
            #**** Guardar Concepto Rubro ***#
            $id_cr =0;
            if($id_con!=0 && $id_r !=0){
                #Validar si Existe concepto Rubro 
                $id_cr = $con->Listar("SELECT id_unico FROM gf_concepto_rubro WHERE rubro ='$id_r' AND concepto = $id_con");
                if(!empty($id_cr[0][0])){
                    $id_cr = $id_cr[0][0];
                } else {
                    $sql_cons ="INSERT INTO `gf_concepto_rubro` 
                            ( `rubro`,`concepto` ) 
                    VALUES (:rubro, :concepto)";
                    $sql_dato = array(
                            array(":rubro",$id_r),
                            array(":concepto",$id_con), 
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $id_cr = $con->Listar("SELECT id_unico FROM gf_concepto_rubro WHERE rubro ='$id_r' AND concepto = $id_con");
                    $id_cr = $id_cr[0][0];
                }
            }
            #**** Si existe concepto Rubro Guarda Concepto Rubro Cuenta***#
            if($id_cr !=0){
                if(!empty($ctaDebito) && !empty($ctaCredito)){
                    #Validar Si Ya Existe Concepto Rubro Cuenta
                    $crc = $con->Listar("SELECT * FROM gf_concepto_rubro_cuenta "
                            . "WHERE concepto_rubro = $id_cr AND cuenta_debito = $ctaDebito "
                            . "AND cuenta_credito = $ctaCredito");
                    if(count($crc)>0){

                    } else {
                        $sql_cons ="INSERT INTO `gf_concepto_rubro_cuenta` 
                            ( `concepto_rubro`,`cuenta_debito`, `cuenta_credito`, 
                            `centrocosto`, `proyecto`,`cuenta_iva`,`cuenta_impoconsumo` ) 
                        VALUES (:concepto_rubro, :cuenta_debito, :cuenta_credito, 
                        :centrocosto, :proyecto, :cuenta_iva, :cuenta_impoconsumo)";
                        $sql_dato = array(
                                array(":concepto_rubro",$id_cr), 
                                array(":cuenta_debito",$ctaDebito), 
                                array(":cuenta_credito",$ctaCredito), 
                                array(":centrocosto",$centroc), 
                                array(":proyecto",$proyecto), 
                                array(":cuenta_iva",$ctaIva), 
                                array(":cuenta_impoconsumo",$ctaImpoconsumo), 
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($resp)){
                            $cfg +=1;
                        }
                    }
                }
            }
            #*******Guardar Detalle Apropiacion****#
            
            if($id_rf !=0){
                if(!empty($valor)){
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                              ( `descripcion`,`valor`,
                              `comprobantepptal`,`rubrofuente`,
                              `tercero`, `proyecto`) 
                    VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                     :tercero, :proyecto)";
                    $sql_dato = array(
                        array(":descripcion",$descripcion),
                        array(":valor",$valor),
                        array(":comprobantepptal",$id_comprobante),
                        array(":rubrofuente",$id_rf),
                        array(":tercero",$tercerov),
                        array(":proyecto",2147483647),
                     );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    if(empty($resp)){
                        $apg +=1;
                    }
                }
            }
        }
        $datos = array("cfg"=>$cfg,"apg"=>$apg);
        echo json_encode($datos); 
    break;
    #**** Guardar Presupuesto Inicial *****#
    case 4:
        # **Definir Arrays ***#
        $arrayRubro     = array();
        $arrayFuente    = array();
        #****** Guardar Comprobante Apropiación Inicial ******#
        #**Buscar Si Existe
        $numa       = anno($anno);
        $numero     = $numa.'000001';
        $tipoCom    = $con->Listar("SELECT * FROM gf_tipo_comprobante_pptal WHERE clasepptal = 13 and tipooperacion=1 AND compania =$compania");
        $tipoCom    = $tipoCom[0][0];
        $fecha      =$numa.'-01-01';
        $descripcion='Comprobante Apropiación Inicial '.$numa;

        $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante =$tipoCom ");
        if(count($bs)>0){
            $id_comprobante = $bs[0][0];
            $sql_cons ="DELETE FROM `gf_detalle_comprobante_pptal` 
                WHERE `comprobantepptal`=:comprobantepptal";
            $sql_dato = array(
                    array(":comprobantepptal",$id_comprobante),	
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        } else {
            #Guardar Comprobante 
            $sql_cons ="INSERT INTO `gf_comprobante_pptal` 
                    ( `numero`, `fecha`, 
                    `fechavencimiento`,`descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`) 
            VALUES (:numero, :fecha, 
                    :fechavencimiento,:descripcion,
                    :parametrizacionanno,:tipocomprobante)";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fecha),
                    array(":fechavencimiento",$fecha),
                    array(":descripcion",$descripcion),
                    array(":parametrizacionanno",$anno),
                    array(":tipocomprobante",$tipoCom),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            $bs = $con->Listar("SELECT * FROM gf_comprobante_pptal WHERE numero = $numero AND tipocomprobante =$tipoCom ");
            $id_comprobante = $bs[0][0];
        }
        $cfg =0;
        $apg =0;        
        #**************************************************#
        for ($a = 2; $a <= $total_filas; $a++) {
            $cd_rubro_pptal     = $objWorksheet->getCellByColumnAndRow(0, $a)->getCalculatedValue();
            $rubro_pptal        = $objWorksheet->getCellByColumnAndRow(1, $a)->getCalculatedValue();
            $fuente             = $objWorksheet->getCellByColumnAndRow(2, $a)->getCalculatedValue();
            $clase              = $objWorksheet->getCellByColumnAndRow(3, $a)->getCalculatedValue();
            $vigenciaann        = $numa;
            $vigencia           = 'ACTUAL';
            $movimiento         = $objWorksheet->getCellByColumnAndRow(6, $a)->getCalculatedValue();
            $valor              = $objWorksheet->getCellByColumnAndRow(7, $a)->getCalculatedValue();
            $sector             = $objWorksheet->getCellByColumnAndRow(4, $a)->getCalculatedValue();
            $destino            = $objWorksheet->getCellByColumnAndRow(5, $a)->getCalculatedValue();
            if(empty($movimiento)){
                $movimiento = 2;
            }
            #***** Buscar Clase Rubro****#
            $cl = $con->Listar("SELECT id_unico FROM gf_tipo_clase_pptal WHERE nombre = '$clase'");
            $tipoc = $cl[0][0]; 
            #***** Buscar Vigencia ****#
            $vg = $con->Listar("SELECT id_unico FROM gf_tipo_vigencia WHERE nombre = '$vigencia'");
            $vig = $vg[0][0]; 
            #**** Si el rubro no está vacío ****#
            $id_r =0;
            if(!empty($cd_rubro_pptal)){
                #Consultar Si Existe Rubro 
                $id_r = $con->Listar("SELECT id_unico FROM gf_rubro_pptal WHERE codi_presupuesto ='$cd_rubro_pptal' AND parametrizacionanno = $anno");
                if(count($id_r)>0){
                    $id_r = $id_r[0][0];
                }else {
                    if(in_array($cd_rubro_pptal, $arrayRubro)) {
                    } else {
                        array_push ( $arrayRubro , $cd_rubro_pptal );
                        #Buscar Sector
                        if(!empty($sector)){
                            $s = $con->Listar("SELECT * FROM gf_sector WHERE nombre = '$sector'");
                            if(count($s)>0){
                                $sec = $s[0][0];
                            } else {
                                $sec = NULL;
                            }
                        } else {
                            $sec = NULL;
                        }                        
                        #*Buscar destino 
                        if(!empty($destino)){
                            $des = $con->Listar("SELECT * FROM gf_destino WHERE nombre = '$destino'");
                            if(count($des)>0){
                                $destino = $des[0][0];
                            } else {
                                $destino = NULL;
                            }
                        } else {
                            $destino = NULL;
                        }
                        
                        #***Guardar Rubro
                        
                        $sql_cons ="INSERT INTO `gf_rubro_pptal` 
                                ( `nombre`, `codi_presupuesto`, 
                                `movimiento`,`manpac`,`vigencia`,
                                `parametrizacionanno`,`tipoclase`, `tipovigencia`,`sector`, `destino`  ) 
                        VALUES (:nombre, :codi_presupuesto, 
                                :movimiento, :manpac,:vigencia, 
                                :parametrizacionanno, :tipoclase, :tipovigencia, :sector, :destino)";
                        $sql_dato = array(
                                array(":nombre",$rubro_pptal),
                                array(":codi_presupuesto",$cd_rubro_pptal),
                                array(":movimiento",$movimiento),
                                array(":manpac",3),
                                array(":vigencia",$anno),   
                                array(":parametrizacionanno",$anno),
                                array(":tipoclase",$tipoc),
                                array(":tipovigencia",$vig),
                                array(":sector",$sec),
                                array(":destino",$destino),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    #******Consultar Id rubro Guardado***#
                    $id_r = $con->Listar("SELECT id_unico FROM gf_rubro_pptal WHERE codi_presupuesto ='$cd_rubro_pptal' AND parametrizacionanno = $anno");
                    $id_r = $id_r[0][0];
                }
            }
            #**** Si la Fuente No Está Vacía ****# 
            $id_f=0;
            if(!empty($fuente)){
                #Consultar Si Fuente Existe
                #******Consultar Id Fuente Guardado***#
                $id_f = $con->Listar("SELECT id_unico FROM gf_fuente WHERE nombre ='$fuente' AND parametrizacionanno = $anno");
                if(count($id_f)>0){
                    $id_f = $id_f[0][0];
                } else {
                    if(in_array($fuente, $arrayFuente)) {

                    } else {
                        array_push ( $arrayFuente , $fuente );
                        #*** Guardar Fuente ***#
                        $sql_cons ="INSERT INTO `gf_fuente` 
                                ( `nombre`,`movimiento`,
                                `parametrizacionanno`,`compania` ) 
                        VALUES (:nombre, :movimiento, 
                                :parametrizacionanno, :compania)";
                        $sql_dato = array(
                                array(":nombre",$fuente),
                                array(":movimiento",1),   
                                array(":parametrizacionanno",$anno),
                                array(":compania",$compania),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    #******Consultar Id Fuente Guardado***#
                    $id_f = $con->Listar("SELECT id_unico FROM gf_fuente WHERE nombre ='$fuente' AND parametrizacionanno = $anno");
                    $id_f = $id_f[0][0];
                }
            }
            #********Si La Fuente Y El Rubro Son Diferentes de 0 Guarda Rubro Fuente*******#
            $id_rf =0;
            if($id_r !=0 && $id_f !=0){
                #Validar Si Existe 
                $id_rf = $con->Listar("SELECT id_unico FROM gf_rubro_fuente WHERE rubro ='$id_r' AND fuente = $id_f");
                if(count($id_rf)>0) { 
                    $id_rf = $id_rf[0][0];
                } else {
                    $sql_cons ="INSERT INTO `gf_rubro_fuente` 
                            ( `rubro`,`fuente` ) 
                    VALUES (:rubro, :fuente)";
                    $sql_dato = array(
                            array(":rubro",$id_r),
                            array(":fuente",$id_f), 
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    $id_rf = $con->Listar("SELECT id_unico FROM gf_rubro_fuente WHERE rubro ='$id_r' AND fuente = $id_f");
                    $id_rf = $id_rf[0][0];
                }
            }
            #*******Guardar Detalle Apropiacion****#            
            if($id_rf !=0){
                if(!empty($valor)){
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante_pptal` 
                              ( `descripcion`,`valor`,
                              `comprobantepptal`,`rubrofuente`,
                              `tercero`, `proyecto`) 
                    VALUES (:descripcion, :valor, :comprobantepptal, :rubrofuente, 
                     :tercero, :proyecto)";
                    $sql_dato = array(
                        array(":descripcion",$descripcion),
                        array(":valor",$valor),
                        array(":comprobantepptal",$id_comprobante),
                        array(":rubrofuente",$id_rf),
                        array(":tercero",$tercerov),
                        array(":proyecto",2147483647),
                     );
                    $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
                    if(empty($resp)){
                        $apg +=1;
                    }
                }
            }
        }
        $datos = array("cfg"=>$cfg,"apg"=>$apg);
        echo json_encode($datos); 
    break;
}



