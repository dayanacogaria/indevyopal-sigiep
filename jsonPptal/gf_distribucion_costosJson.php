<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#27/07/2018 |Erica G. | Reteica
#26/03/2018 |Erica G. | Archivo Creado 
#######################################################################################################
require_once '../Conexion/conexion.php';
require_once '../Conexion/ConexionPDO.php';
require '../jsonPptal/funcionesPptal.php';
ini_set('max_execution_time', 0);
$con = new ConexionPDO();
session_start();
$parm_anno = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$anno       = anno($parm_anno);
$cc         = $con->Listar("SELECT id_unico FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $parm_anno");
$centrocv   = $cc[0][0];
$id_usuario = $_SESSION['id_usuario'];
switch ($_REQUEST['action']){
    # ******************* Guardar Configuración  ******************* #
    case (1):
        $g =0;
        $centro_costo       = $_POST['centro_costo'];
        $concepto           = $_POST['concepto'];
        $id_c                 = 'cuenta'.$centro_costo;
        $cuenta              = $_POST[$id_c];
        $porc               = 'porcentaje'.$centro_costo;
        $porcentaje         = $_POST[$porc];
        $sql_cons ="INSERT INTO `gf_configuracion_distribucion` 
        ( `concepto`, `centro_costo`, `cuenta`,`porcentaje` ) 
        VALUES (:concepto, :centro_costo, :cuenta, :porcentaje)";
        $sql_dato = array(
                array(":concepto",$concepto),
                array(":centro_costo",$centro_costo),
                array(":cuenta",$cuenta),
                array(":porcentaje",$porcentaje),
        );
        $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
        #var_dump($obj_resp);
        if(empty($obj_resp)){
            $g +=1;
        }
        if($g >0){
            $rta = 0;
        } else {
            $rta = 1;
        }
        echo $rta;
    break;
    # ******************* Eliminar Configuración ******************* #
    case (2):
        $ids        = $_POST['id'];
        $sql_cons =$con->Listar("DELETE cf.* FROM `gf_configuracion_distribucion` cf 
            WHERE cf.id_unico = $ids");      
        
        if(empty($obj_resp)){
            $res = 0;
        } else {
            $res = 1;
        }
        echo json_decode($res);
    break;
    #*** Validación distribución por formulario **¨#
    case(3):
        $id_pptal = $_REQUEST['id'];
        $validacion = validarConfiguracionDistribucion($id_pptal);
        $rta = $validacion["rta"];
        echo json_encode($validacion);
        
    break;
    #Generacion
    case(4):
        $id_pptal   = $_REQUEST['id'];
        $id_cnt     = $_REQUEST['idcnt'];
        $generacion = guardarDistribucionCostos($id_pptal, $id_cnt);
        #var_dump($generacion);
        echo $generacion;
    break;
    #* Ver distribucion de costos
    case(5):
        $id_p   = $_REQUEST['id']; 
        $id_cnt = $_REQUEST['idcnt']; 
        $html   = ""; 
        $html  .= '<table border="1" style="width:100%">'; 
        #* Buscar los conceptos de la cuenta por pagar
        $rowc = $con->Listar("SELECT DISTINCT dc.concepto, UPPER(c.nombre) 
            FROM gf_distribucion_costos dc
            LEFT JOIN gf_concepto c ON c.id_unico = dc.concepto 
            WHERE dc.cnt = $id_cnt AND dc.pptal = $id_p 
            ORDER BY c.id_unico");
        if(!empty($rowc[0][0])>0){
            for ($c = 0; $c < count($rowc); $c++) {
                $concepto = $rowc[$c][0];
                $html .= '<th colspan="3" style="text-align:center">'.($rowc[$c][1]).'</th>';
                $html .= '<tr>';
                $html .= '<td style="text-align:center"><strong><i>Centro Costo</i></strong></td>';
                $html .= '<td style="text-align:center"><strong><i>Cuenta</i></strong></td>';
                $html .= '<td style="text-align:center"><strong><i>Valor</i></strong></td>';
                $html .= '</tr>';
                
                #* Buscar distribución de la cuenta por pagar
                $dc = $con->Listar("SELECT dc.id_unico, UPPER(c.nombre), 
                    UPPER(cc.sigla), LOWER(cc.nombre), 
                    cta.codi_cuenta, LOWER(cta.nombre), dc.valor, dc.concepto  
                    FROM gf_distribucion_costos dc
                    LEFT JOIN gf_concepto c ON c.id_unico = dc.concepto 
                    LEFT JOIN gf_centro_costo cc oN dc.centro_costo = cc.id_unico 
                    LEFT JOIN gf_cuenta cta ON dc.cuenta = cta.id_unico
                    WHERE cnt = $id_cnt AND pptal = $id_p 
                        AND dc.concepto = $concepto
                        ORDER BY c.id_unico");
                if(!empty($dc[0][0])>0){
                    $tc = 0;
                    for ($i = 0; $i < count($dc); $i++) {
                        $html .= '<tr>';
                        $html .= '<td>'.$dc[$i][2].' - '.ucwords($dc[$i][3]).'</td>';
                        $html .= '<td>'.$dc[$i][4].' - '.ucwords($dc[$i][5]).'</td>';
                        $html .= '<td style="text-align:right">'.number_format($dc[$i][6],2,'.',',').'</td>';
                        $html .= '</tr>';
                        $tc += $dc[$i][6];
                    }
                    $html .= '<tr>';
                    $html .= '<td style="text-align:left" colspan="2"><strong><i>TOTAL: '.$rowc[$c][1].'</i></strong></td>';
                    $html .= '<td style="text-align:right"><strong><i>'.number_format($tc,2,'.',',').'</i></strong></td>';
                    $html .= '</tr>';
                }
            }
        }
        $html .= "</table>"; 
        echo $html;
    break;
    #* Verificar tipo de distribucion
    case(6):
        $id_p   = $_REQUEST['id']; 

        $rowd = $con->Listar("SELECT dc.id_unico, c.id_unico, c.nombre 
        FROM gf_detalle_comprobante_pptal dc 
        LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = dc.conceptoRubro 
        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
        WHERE dc.comprobantepptal =$id_p");
        $tx =0;
        for ($i = 0; $i < count($rowd); $i++) {
            $concepto = $rowd[$i][1];
            $dc = $con->Listar("SELECT SUM(porcentaje) FROM gf_configuracion_distribucion where concepto = $concepto");
            if($dc[0][0]>0){
                $tx +=1;
            }
        }
        echo $tx;
    break;
    #*Modal porcentajes
    case 7:
        #require_once './gf_style_tabla.php';
        $id_p   = $_REQUEST['id']; 
        $id_cnt = $_REQUEST['idcnt']; 
        $html   = ""; 
        $html  .='<div align="center"  class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">';          
        $html  .='<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">';
        $html  .='<table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">';

        $rowd = $con->Listar("SELECT dc.id_unico, c.id_unico, c.nombre 
        FROM gf_detalle_comprobante_pptal dc 
        LEFT JOIN gf_concepto_rubro cr ON cr.id_unico = dc.conceptoRubro 
        LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
        WHERE dc.comprobantepptal =$id_p");
        $tx1 =0;
        $tx2 =0;
        $datos = array();
        for ($i = 0; $i < count($rowd); $i++) {
            $concepto = $rowd[$i][1];
            $html .= '<th colspan="3" style="text-align:center"><strong><i>'.($rowd[$i][2]).'<strong><i></th>';
            $html .= '<tr>';
            $html .= '<td style="text-align:center"><strong><i>Centro Costo</i></strong></td>';
            $html .= '<td style="text-align:center"><strong><i>Cuenta</i></strong></td>';
            $html .= '<td style="text-align:center"><strong><i>Porcentaje</i></strong></td>';
            $html .= '</tr>';
            
            #* Buscar distribución concepto
            $dc = $con->Listar("SELECT dc.id_unico, UPPER(c.nombre), 
                UPPER(cc.sigla), LOWER(cc.nombre), 
                cta.codi_cuenta, LOWER(cta.nombre), 
                dc.porcentaje, dc.centro_costo, 
                cta.id_unico, cta.naturaleza 
                FROM gf_configuracion_distribucion dc
                LEFT JOIN gf_concepto c ON c.id_unico = dc.concepto 
                LEFT JOIN gf_centro_costo cc oN dc.centro_costo = cc.id_unico 
                LEFT JOIN gf_cuenta cta ON dc.cuenta = cta.id_unico
                WHERE dc.concepto = $concepto
                    ORDER BY c.id_unico");
            if(!empty($dc[0][0])>0){
                $tc = 0;
                
                for ($j = 0; $j < count($dc); $j++) {                    
                    
                    $html .= '<tr>';
                    $html .= '<td>'.$dc[$j][2].' - '.ucwords($dc[$j][3]).'</td>';
                    $html .= '<td>'.$dc[$j][4].' - '.ucwords($dc[$j][5]).'</td>';
                    $html .= '<td>';
                    $html .= '<form id="formg'.$i.$j.'" name="formg'.$i.$j.'" method="POST">';
                    $html .= '<input type="number" step="0.01" name="porcentaje'.$i.$j.'" id="porcentaje'.$i.$j.'" class="form_control" style="width:50px" onchange="cambio('.$i.','.$j.')" required value="'.number_format($dc[$j][6],2,'.',',').'">%';
                    $html .= '<input type="hidden" name="concepto'.$i.$j.'" id="concepto'.$i.$j.'" value="'.$concepto.'">';
                    $html .= '<input type="hidden" name="centro_costo'.$i.$j.'" id="centro_costo'.$i.$j.'" value="'.$dc[$j][7].'">';
                    $html .= '<input type="hidden" name="cuenta'.$i.$j.'" id="cuenta'.$i.$j.'" value="'.$dc[$j][8].'">';
                    $html .= '<input type="hidden" name="naturaleza'.$i.$j.'" id="naturaleza'.$i.$j.'" value="'.$dc[$j][9].'">';
                    $html .= '<input type="hidden" name="valoro'.$i.$j.'" id="valoro'.$i.$j.'" value="'.$dc[$j][6].'">';
                    $html .= '<input type="hidden" name="detallec'.$i.$j.'" id="detallec'.$i.$j.'" value="'.$rowd[$i][0].'">';
                    $html .= '</form>';
                    $html .= '</td>';
                    
                    
                    $html .= '</tr>';
                    $tc += $dc[$j][6];
                    $tx1 +=1;
                    
                }
                
                $html .= '<tr>';
                $html .= '<td style="text-align:left" colspan="2"><strong><i>TOTAL: </i></strong></td>';
                $html .= '<td style="text-align:right"><label id="lbltotal'.$i.'"><strong><i>'.number_format($tc,2,'.',',').'</i></strong></label></td>';
                $html .= '<input type="hidden" name="total'.$i.'" id="total'.$i.'" value="'.(100-$tc).'">';
                $html .= '</tr>';
                $tx2 +=1;
            }
        }
        #var_dump($datos);
        $html .= '<input type="hidden" name="datos2" id="datos2" value="'.$tx2.'">';
        $html .= '<input type="hidden" name="datos1" id="datos1" value="'.($tx1/$tx2).'">';
        
        $html .="</table>";
        $html .="</div>";
        $html .="</div>";
        echo $html;
    break;
    
    #* guardar porcentajes 
    case 8:
        $id_pptal   = $_REQUEST['id'];
        $id_cnt     = $_REQUEST['idcnt'];
        $varl       = $_REQUEST['pc'];
        $porcentaje = $_REQUEST['porcentaje'.$varl];
        $concepto   = $_REQUEST['concepto'.$varl];
        $cc         = $_REQUEST['centro_costo'.$varl];
        $cta        = $_REQUEST['cuenta'.$varl];
        $natu       = $_REQUEST['naturaleza'.$varl];
        $id_det     = $_REQUEST['detallec'.$varl];
        
        $generacion = guardarDistribucionCostospc($id_pptal, $id_cnt, $concepto, $cc, $cta, $natu, $porcentaje,$id_det);
        #var_dump($generacion);
        echo $generacion;
    break;
    #Validar Comprobante de amortización
    case 9:
        $rta = 0;
        $com = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE amortizacion = 1 AND compania = $compania");
        if(!empty($com[0][0])){
            $rta = $com[0][0];
        }
        echo $rta;
    break;
    #* Guardar Amortización
    case 10:
        $rta        = 0;
        $num_mes    = $_REQUEST['id'];
        $tipo_c     = $_REQUEST['id_tc'];
        $naanno     = anno($_SESSION['anno']);
        $row = $con->Listar("SELECT da.id_unico, da.valor, 
            da.detallecomprobante,c.id_unico, 
            da.numero_cuota, a.tercero, 
            da.fecha_programada, a.numero_documento, 
            cta.id_unico, cta.naturaleza 
        FROM gf_detalle_amortizacion da 
        LEFT JOIN gf_amortizacion a ON da.amortizacion = a.id_unico 
        LEFT JOIN gf_detalle_comprobante_pptal dc ON a.detallecomprobantepptal = dc.id_unico 
        LEFT JOIN gf_comprobante_pptal cn ON dc.comprobantepptal = cn.id_unico 
        LEFT JOIN gf_concepto c ON a.concepto = c.id_unico 
        LEFT JOIN gf_cuenta cta ON a.cuenta_debito = cta.id_unico 
        LEFT JOIN gf_parametrizacion_anno pa ON cn.parametrizacionanno = pa.id_unico 
        WHERE month(da.fecha_programada) = '".$num_mes."' 
            AND year(da.fecha_programada) = '".$naanno."' 
            AND cn.parametrizacionanno <= $parm_anno
            AND c.parametrizacionanno = $parm_anno 
            AND cta.parametrizacionanno = $parm_anno 
            AND pa.compania = $compania  
            AND da.detallecomprobante IS NULL"); 
        
        for ($i = 0; $i < count($row); $i++) {
            $numero = numero ('gf_comprobante_cnt', $tipo_c, $parm_anno);
            $valor  = $row[$i][1];
            $concepto = $row[$i][3];
            $cta_debi = $row[$i][8];
            $nat_debi = $row[$i][9];
            #Crear comprobante Amortización
            $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                    ( `numero`, `fecha`, 
                    `descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`,
                    `numerocontrato`, `tercero`,
                    `usuario`, `fecha_elaboracion`,
                    `compania`,`estado`) 
            VALUES (:numero, :fecha, 
                    :descripcion,
                    :parametrizacionanno,:tipocomprobante,
                    :numerocontrato,:tercero,
                    :usuario, :fecha_elaboracion, 
                    :compania, :estado )";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$row[$i][6]),
                    array(":descripcion",'Comprobante de amortización'),
                    array(":parametrizacionanno",$parm_anno),
                    array(":tipocomprobante",$tipo_c),
                    array(":numerocontrato",$row[$i][7]),
                    array(":tercero",$row[$i][5]),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y-m-d')),
                    array(":compania",$compania),
                    array(":estado",2),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            $bs = $con->Listar("SELECT * FROM gf_comprobante_cnt  
                WHERE numero = $numero AND tipocomprobante =$tipo_c ");
            $id_cxpcnt = $bs[0][0];
            #*** Buscar cuenta débito relacionada al concepto
            $crc = $con->Listar("SELECT cta.id_unico, cta.naturaleza FROM gf_concepto_rubro_cuenta crc 
                LEFT JOIN gf_concepto_rubro cr ON crc.concepto_rubro = cr.id_unico 
                LEFT JOIN gf_concepto c ON cr.concepto = c.id_unico 
                LEFT JOIN gf_cuenta cta ON crc.cuenta_debito = cta.id_unico 
                WHERE c.id_unico = $concepto");
           
            $cta_ct = $crc[0][0];
            $nat_ct = $crc[0][1];
            if($nat_ct==1){
                $vc = $valor*-1;
            } else {
                $vc = $valor;
            }
            $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                    ( `fecha`, `comprobante`,`valor`,
                    `cuenta`,`naturaleza`,`tercero`, `centrocosto`, `proyecto`) 
            VALUES (:fecha,  :comprobante,:valor, 
                    :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
            $sql_dato = array(
                    array(":fecha",$row[$i][6]),
                    array(":comprobante",$id_cxpcnt),
                    array(":valor",($vc)),
                    array(":cuenta",$cta_ct),   
                    array(":naturaleza",$nat_ct),
                    array(":tercero",$row[$i][5]),
                    array(":centrocosto",$centrocv),
                    array(":proyecto",2147483647),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            $bsdc = $con->Listar("SELECT * FROM gf_detalle_comprobante  
                WHERE comprobante = $id_cxpcnt");
            $id_dcnt = $bsdc[0][0];
            #**** Cuenta Débito distribuida**********#
            #****************************************"#
            
            $rowc = $con->Listar("SELECT cf.centro_costo, cf.cuenta, 
                    cc.cantidad_distribucion, c.naturaleza 
                FROM gf_configuracion_distribucion cf 
                LEFT JOIN gf_centro_costo cc ON cf.centro_costo =cc.id_unico 
                LEFT JOIN gf_cuenta c On cf.cuenta = c.id_unico 
                WHERE concepto = $concepto");
            if(!empty($rowc[0][0])){
                #** Definir la cantidad total a distribuir
                $cd = $con->Listar("SELECT SUM(cc.cantidad_distribucion) 
                    FROM gf_configuracion_distribucion cf 
                    LEFT JOIN gf_centro_costo cc ON cf.centro_costo =cc.id_unico  
                    WHERE concepto = $concepto");
                $cd = $cd[0][0];
                $tp = 0;
                for ($c = 0; $c < count($rowc); $c++) {
                    $c_costo    = $rowc[$c][0];
                    $cuenta     = $rowc[$c][1];
                    $cantidad   = $rowc[$c][2];
                    $naturaleza = $rowc[$c][3];
                    $porcentajeaplicar = ROUND($cantidad *100/$cd,1);
                    $tp += $porcentajeaplicar;
                    #** Validar Porcentaje 
                    if($c==(count($rowc)-1)){
                        if($tp!=100){
                            $pf = 100-$tp;
                            $porcentajeaplicar = $porcentajeaplicar+$pf;
                        }
                    }
                    $valor_a = ($valor*$porcentajeaplicar)/100;
                    $valor_a = ROUND($valor_a);
                    $valor_r = $valor_a;
                    if($naturaleza ==2){
                        $valor_a = $valor_a*-1;
                    }
                    $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                            ( `fecha`, `comprobante`,`valor`,
                            `cuenta`,`naturaleza`,`tercero`, 
                            `centrocosto`, `proyecto`) 
                    VALUES (:fecha,  :comprobante,:valor, 
                            :cuenta,:naturaleza, :tercero, 
                            :centrocosto,:proyecto)";
                    $sql_dato = array(
                            array(":fecha",$row[$i][6]),
                            array(":comprobante",$id_cxpcnt),
                            array(":valor",$valor_a),
                            array(":cuenta",$cuenta),   
                            array(":naturaleza",$naturaleza),
                            array(":tercero",$row[$i][5]),
                            array(":centrocosto",$c_costo),
                            array(":proyecto",2147483647),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);   
                }
            } else {
                #Guardar Cuenta Débito
                if($nat_debi==1){
                    $vd = $valor;
                } else {
                    $vd = $valor*-1;
                }
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`, `proyecto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$row[$i][6]),
                        array(":comprobante",$id_cxpcnt),
                        array(":valor",($vd)),
                        array(":cuenta",$cta_debi),   
                        array(":naturaleza",$nat_debi),
                        array(":tercero",$row[$i][5]),
                        array(":centrocosto",$centrocv),
                        array(":proyecto",2147483647),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            
            #** Actualizar Detalle amortizacion 
            $da = $row[$i][0];
            $sql_cons ="UPDATE `gf_detalle_amortizacion` 
                SET `comprobante` =:comprobante , 
                `detallecomprobante`=:detallecomprobante 
                WHERE `id_unico` =:id_unico ";
            $sql_dato = array(
                array(":comprobante",$id_cxpcnt),
                array(":detallecomprobante",$id_dcnt),
                array(":id_unico",$da),
            );
            $obj_resp = $con->InAcEl($sql_cons,$sql_dato);
            #var_dump($obj_resp);
            if(empty($obj_resp)){
                $rta +=1;
            }
        }
        echo $rta;
    break;
    case 11:
        $id_cnt = $_REQUEST['id']; 
        $html   = ""; 
        $html  .='<div align="center"  class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">';          
        $html  .='<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">';
        $html  .='<table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">';

        $rowd = $con->Listar("SELECT   DT.id_unico,
            CT.id_unico as cuenta,
            CT.nombre,
            CT.codi_cuenta,
            CT.naturaleza,
            N.id_unico,
            N.nombre,
            IF(CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos) 
                IS NULL OR CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos) = '',
                (t.razonsocial),
                CONCAT_WS(' ',
                t.nombreuno,
                t.nombredos,
                t.apellidouno,
                t.apellidodos)) AS NOMBRE,
            CC.id_unico,
            CC.nombre,
            DT.valor
        FROM       gf_detalle_comprobante DT
        LEFT JOIN  gf_cuenta CT ON DT.cuenta = CT.id_unico
        LEFT JOIN  gf_naturaleza N ON N.id_unico = CT.naturaleza
        LEFT JOIN  gf_tercero t ON DT.tercero = t.id_unico 
        LEFT JOIN  gf_centro_costo CC ON DT.centrocosto = CC.id_unico
        LEFT JOIN  gf_proyecto PR ON DT.proyecto = PR.id_unico
        WHERE (DT.comprobante) = $id_cnt");
        $html .= '<tr>';
        $html .= '<td style="text-align:center"><strong><i>Cuenta</i></strong></td>';
        $html .= '<td style="text-align:center"><strong><i>Tercero</i></strong></td>';
        $html .= '<td style="text-align:center"><strong><i>Centro Costo</i></strong></td>';
        $html .= '<td style="text-align:center"><strong><i>Débito</i></strong></td>';
        $html .= '<td style="text-align:center"><strong><i>Crédito</i></strong></td>';
        $html .= '</tr>';
        $td =0;
        $tc =0;
        for ($i = 0; $i < count($rowd); $i++) {
            
            $html .= '<tr>';
            $html .= '<td>'.$rowd[$i][3].' - '.ucwords($rowd[$i][2]).'</td>';
            $html .= '<td>'.ucwords($rowd[$i][7]).'</td>';
            $html .= '<td>'.ucwords($rowd[$i][9]).'</td>';
            $vd =0;
            $vc =0;
            if($rowd[$i][4]==1){
                if($rowd[$i][10]>0){
                    $vd =$rowd[$i][10];
                } else {
                    $vc =$rowd[$i][10]*-1;
                }
            } else {
                if($rowd[$i][10]>0){
                    $vc =$rowd[$i][10];
                } else {
                    $vd =$rowd[$i][10]*-1;
                }
            }
            $td +=$vd;
            $tc +=$vc;
            $html .= '<td>'. number_format($vd, 2, ',', '.').'</td>';
            $html .= '<td>'. number_format($vc, 2, ',', '.').'</td>';
            $html .= '</tr>'; 
            
        }
        $html .= '<tr>';
        $html .= '<td style="text-align:left" colspan="3"><strong><i>TOTAL: </i></strong></td>';
        $html .= '<td style="text-align:right"><strong><i>'.number_format($td,2,'.',',').'</i></strong></td>';
        $html .= '<td style="text-align:right"><strong><i>'.number_format($tc,2,'.',',').'</i></strong></td>';
        $html .= '</tr>';
        $html .="</table>";
        $html .="</div>";
        $html .="</div>";
        echo $html;
    break;
    #Validar Comprobante de traslado
    case 12:
        $rta = 0;
        $com = $con->Listar("SELECT * FROM gf_tipo_comprobante WHERE traslado = 1 AND compania = $compania");
        if(!empty($com[0][0])){
            $rta = $com[0][0];
        }
        echo $rta;
    break;
    #Validar Si ya existe comprobante traslado
    case 13:
        $tipoc = $_REQUEST['id_tc'];
        $nanno = $_REQUEST['nanno'];
        $nmes  = $_REQUEST['nmes'];
        $diaf  = diaf($nmes, $nanno);
        $fechac = $nanno.'-'.$nmes.'-'.$diaf;
        $rta = 0;
        $com = $con->Listar("SELECT id_unico  
            FROM gf_comprobante_cnt 
            WHERE tipocomprobante = $tipoc 
                AND fecha= '$fechac'");
        if(!empty($com[0][0])){
            $rta = $com[0][0];
        }
        echo $rta;
    break;
    #Eliminar traslado
    case 14:
        $id = $_REQUEST['id'];
        $rta = eliminardetallescnt($id);
        echo $rta;
    break;
    #** Guardar traslado 
    case 15:
        $tipoc = $_REQUEST['id_tc'];
        $nanno = $_REQUEST['nanno'];
        $nmes  = $_REQUEST['nmes'];
        $diaf  = diaf($nmes, $nanno);
        $fechac = $nanno.'-'.$nmes.'-'.$diaf;
        $rta = 0;
        $com = $con->Listar("SELECT * 
            FROM gf_comprobante_cnt 
            WHERE tipocomprobante = $tipoc 
                AND fecha= '$fechac'");
        if(!empty($com[0][0])){
            $id_cnt = $com[0][0];
        } else {
            #Guardar Comprobante 
            $numero = numero ('gf_comprobante_cnt', $tipoc, $parm_anno);
            $sql_cons ="INSERT INTO `gf_comprobante_cnt` 
                    ( `numero`, `fecha`, 
                    `descripcion`, 
                    `parametrizacionanno`,`tipocomprobante`,
                    `tercero`,
                    `usuario`, `fecha_elaboracion`,
                    `compania`,`estado`) 
            VALUES (:numero, :fecha, 
                    :descripcion,
                    :parametrizacionanno,:tipocomprobante,
                    :tercero,
                    :usuario, :fecha_elaboracion, 
                    :compania, :estado )";
            $sql_dato = array(
                    array(":numero",$numero),
                    array(":fecha",$fechac),
                    array(":descripcion",'Comprobante de traslado'),
                    array(":parametrizacionanno",$parm_anno),
                    array(":tipocomprobante",$tipoc),
                    array(":tercero",$compania),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y-m-d')),
                    array(":compania",$compania),
                    array(":estado",2),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato); 
            $bs = $con->Listar("SELECT * FROM gf_comprobante_cnt  
                WHERE numero = $numero AND tipocomprobante =$tipoc ");
            $id_cnt = $bs[0][0];
        }
        
        $rowc = $con->Listar("SELECT 
            ct.id_unico, 
            ct.centro_costo, 
            ct.centro_costo_debito, 
            ct.centro_costo_credito, 
            cat.id_unico, cat.naturaleza, 
            ctd.id_unico, ctd.naturaleza, 
            ctc.id_unico, ctc.naturaleza 
        FROM gf_configuracion_traslado ct  
        LEFT JOIN gf_cuenta cat ON ct.cuenta_traslado = cat.id_unico 
        LEFT JOIN gf_cuenta ctd ON ct.cuenta_debito = ctd.id_unico 
        LEFT JOIN gf_cuenta ctc ON ct.cuenta_credito= ctc.id_unico 
        WHERE cat.parametrizacionanno = $parm_anno  ");
        for ($i = 0; $i < count($rowc); $i++) {
            $cuenta_t = $rowc[$i][4];
            $ncta_t   = $rowc[$i][5];
            $centro_t = $rowc[$i][1];
            $cuenta_d = $rowc[$i][6];
            $ncta_d   = $rowc[$i][7];
            $centro_d = $rowc[$i][2];
            $cuenta_c = $rowc[$i][8];
            $ncta_c   = $rowc[$i][9];
            $centro_c = $rowc[$i][3];
            $valor  = totalmovimientocc($cuenta_t, $centro_t, $nmes,$nanno, $parm_anno, $ncta_t);
            
            if($valor!=0){
                $valord = 0;
                
                if($ncta_d==1){
                    $valord = $valor;
                } else {
                    $valord = $valor*-1;
                }
                #Insetar Detalle Débito
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`, `proyecto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$fechac),
                        array(":comprobante",$id_cnt),
                        array(":valor",($valord)),
                        array(":cuenta",$cuenta_d),   
                        array(":naturaleza",$ncta_d),
                        array(":tercero",$compania),
                        array(":centrocosto",$centro_d),
                        array(":proyecto",2147483647),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                #Insetar Detalle Credito 
                $valorc = 0;
                if($ncta_c==2){
                    $valorc = $valor;
                } else {
                    $valorc = $valor*-1;
                }
                $sql_cons ="INSERT INTO `gf_detalle_comprobante` 
                        ( `fecha`, `comprobante`,`valor`,
                        `cuenta`,`naturaleza`,`tercero`, `centrocosto`, `proyecto`) 
                VALUES (:fecha,  :comprobante,:valor, 
                        :cuenta,:naturaleza, :tercero, :centrocosto, :proyecto)";
                $sql_dato = array(
                        array(":fecha",$fechac),
                        array(":comprobante",$id_cnt),
                        array(":valor",($valorc)),
                        array(":cuenta",$cuenta_c),   
                        array(":naturaleza",$ncta_c),
                        array(":tercero",$compania),
                        array(":centrocosto",$centro_c),
                        array(":proyecto",2147483647),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                
                if(empty($resp)){
                    $rta +=1;
                }
            }
        }
        echo $rta;
    break;
    #** Eliminar distribucion
    case 16:
        $id_p   = $_REQUEST['id']; 
        $id_cnt = $_REQUEST['idcnt']; 
        $sql_cons ="DELETE FROM  `gf_detalle_comprobante`
        WHERE `comprobante`=:comprobante AND `distribucion`=:distribucion";
        $sql_dato = array(
            array(":comprobante",$id_cnt),
            array(":distribucion",1),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);

        $sql_cons ="DELETE FROM  `gf_distribucion_costos`
        WHERE `cnt`=:cnt ";
        $sql_dato = array(
            array(":cnt",$id_cnt),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
    break;
    #* Configuracion consolidado
    case 17:
        $id     = $_REQUEST['id'];
        $tipo   = $_REQUEST['tipo'];
        #* Buscar si ya existe 
        $cn = $con->Listar("SELECT * FROM gf_consolidacion WHERE compania = $id");
        if(empty($cn[0][0])){
            $sql_cons ="INSERT INTO `gf_consolidacion` 
                    ( `compania`, `consolidado`) 
            VALUES (:compania, :consolidado)";
            $sql_dato = array(
                array(":compania",$id),
                array(":consolidado",$tipo),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        } else {
            $sql_cons ="UPDATE  `gf_consolidacion` 
            SET `consolidado` =:consolidado
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":consolidado",$tipo),
                array(":id_unico",$cn[0][0]),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        echo $resp;
    break;
    #** Guardar consolidacion
    case 18:
        $mesI = $_REQUEST['mesI'];
        $mesF = $_REQUEST['mesF'];
        $terceroI = $_REQUEST['terceroI'];
        $terceroF = $_REQUEST['terceroF'];
        $rm = $con->Listar("SELECT DISTINCT numero FROM gf_mes WHERE numero BETWEEN '$mesI' and '$mesF'");
        $tc = $con->Listar("SELECT DISTINCT id_unico FROM gf_tipo_comprobante WHERE compania = $compania AND consolidado = 1 ");
        $cn = $con->Listar("SELECT DISTINCT c.compania, t.numeroidentificacion 
            FROM gf_consolidacion c 
            LEFT JOIN gf_tercero t ON c.compania = t.id_unico  
            WHERE c.consolidado = 1 
            AND c.compania BETWEEN $terceroI AND $terceroF");
        for ($i=0; $i <count($cn) ; $i++) { 
            $id_com = $cn[$i][1];
            #* Buscar parametrizacion 
            $pc = $con->Listar("SELECT pa.id_unico 
                FROM gf_parametrizacion_anno pa 
                LEFT JOIN gf_tercero t ON pa.compania = t.id_unico 
                WHERE t.numeroidentificacion = $id_com AND pa.anno = $anno  ");
            if(!empty($pc[0][0])>0){
                for ($m=0; $m <count($rm) ; $m++) { 
                    $fechaI = $anno.'-'.$rm[$m][0].'-01';
                    $diaf =diaf($rm[$m][0],$anno);
                    $fechaF =$anno.'-'.$rm[$m][0].'-'.$diaf;
                    generarB($anno, $pc[0][0], $fechaI, $fechaF);
                    $g =  guardarConsolidado($cn[$i][0], $tc[0][0], $fechaF,$parm_anno,$compania);
                }
            }            
        }
        echo 1;
    break;
    #** 
    case 19:        
        $tipo   = $_REQUEST['tipo'];
        $rowc = $con->Listar("SELECT id_unico, razonsocial, numeroidentificacion, digitoverficacion 
            FROM gf_tercero t 
            WHERE compania = $compania AND id_unico !=$compania");
        for ($i = 0; $i < count($rowc); $i++) {
            $id     = $rowc[$i][0];
            #* Buscar si ya existe 
            $cn = $con->Listar("SELECT * FROM gf_consolidacion WHERE compania = $id");
            if(empty($cn[0][0])){
                $sql_cons ="INSERT INTO `gf_consolidacion` 
                        ( `compania`, `consolidado`) 
                VALUES (:compania, :consolidado)";
                $sql_dato = array(
                    array(":compania",$id),
                    array(":consolidado",$tipo),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            } else {
                $sql_cons ="UPDATE  `gf_consolidacion` 
                SET `consolidado` =:consolidado
                WHERE `id_unico`=:id_unico ";
                $sql_dato = array(
                    array(":consolidado",$tipo),
                    array(":id_unico",$cn[0][0]),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
            
        }
        
        echo $resp;
    break;
    
    
}
