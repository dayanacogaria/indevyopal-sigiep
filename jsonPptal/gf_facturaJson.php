<?php
require '../Conexion/ConexionPDO.php';
require '../Conexion/conexion.php';
require './funcionesPptal.php';
require '../funciones/funcionEmail.php';
require '../jsonAlmacen/funcionesAlmacen.php';

@session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$usuario_t  = $_SESSION['usuario_tercero'];
$anno       = anno($panno);
$action     = $_REQUEST['action'];
$ndc        = $con->Listar("SELECT numeroidentificacion FROM gf_tercero WHERE id_unico = $compania");
$n_doc_com  = $ndc[0][0];
$cc         = $con->Listar("SELECT * FROM gf_centro_costo WHERE nombre = 'Varios' AND parametrizacionanno = $panno");
$c_costo    = $cc[0][0];
$pro        = $con->Listar("SELECT * FROM gf_proyecto WHERE nombre='Varios' AND compania = $compania");
$proyecto   = $pro[0][0]; 
switch ($action) {
    #Número Factura
    case 1:
        $tipofactura = $_POST['tipo'];
        $cons = $con->Listar("SELECT sigue_consecutivo FROM gp_tipo_factura WHERE id_unico = $tipofactura");
        $res  = $con->Listar("SELECT numero_inicial FROM `gp_resolucion_factura` WHERE tipo_factura = $tipofactura ORDER BY id_unico DESC LIMIT 1");
        if ($cons[0][0] == 1) {
            $fac = $con->Listar("SELECT * FROM gp_factura WHERE tipofactura = $tipofactura limit 1 ");
            if(count($fac)>0){
                $sql = $con->Listar("SELECT MAX(cast(numero_factura as unsigned))+1 FROM gp_factura where tipofactura = $tipofactura ");

                $numero = $sql[0][0];
            } else {
                if(count($res)>0){
                    $numero = $res[0][0];
                } else {
                    $numero = $anno. '000001';
                }
            }
        } else {

            $fac = $con->Listar("SELECT * FROM gp_factura WHERE tipofactura = $tipofactura AND parametrizacionanno = $panno");
            if(count($fac)>0){
                $sql = $con->Listar("SELECT REPLACE(MAX(numero_factura), MAX(cast(numero_factura as unsigned)),MAX(cast(numero_factura as unsigned))+1) FROM gp_factura where tipofactura = $tipofactura AND parametrizacionanno = $panno");
                $numero = $sql[0][0];
            } else {
                if(count($res)>0){
                    $numero = $res[0][0];
                } else {
                    $numero = $anno. '000001';
                }
            }
        }

        echo $numero;
    break;
    #******** Buscar Factura ******#
    case 2:
        $factura = $_POST['factura'];
        $rowD = $con->Listar("SELECT      cnt.id_unico as cnt,ptal.id_unico as ptal
                    FROM        gp_factura pg 
                    LEFT JOIN   gp_tipo_factura tpg ON pg.tipofactura = tpg.id_unico 
                    LEFT JOIN   gf_tipo_comprobante tpc ON tpc.id_unico = tpg.tipo_comprobante 
                    LEFT JOIN   gf_comprobante_cnt cnt ON cnt.tipocomprobante = tpc.id_unico 
                                AND pg.numero_factura = cnt.numero 
                    LEFT JOIN   gf_tipo_comprobante_pptal tcp ON tpc.comprobante_pptal = tcp.id_unico 
                    LEFT JOIN   gf_comprobante_pptal ptal ON ptal.tipocomprobante = tcp.id_unico 
                                AND pg.numero_factura = ptal.numero 
                    LEFT JOIN   gf_movimiento mto ON tpg.tipo_movimiento = mto.tipomovimiento 
                                AND pg.numero_factura = ptal.numero 
                    WHERE pg.id_unico =  $factura");
        if(count($rowD)>0){
            echo "registrar_GF_FACTURA.php?factura=".md5($factura)."&cnt=".md5($rowD[0][0])."&pptal=".md5($rowD[0][1]);
        }else{
            $row = $con->Listar("SELECT dtc.comprobante FROM gp_detalle_factura dtf
            LEFT JOIN gf_detalle_comprobante dtc ON dtc.id_unico = dtf.detallecomprobante  WHERE dtf.factura = $factura");
            if(count($row)>0 && !empty($row[0][0])){
                echo "registrar_GF_FACTURA.php?factura=".md5($factura)."&cnt=".md5($row[0][0]);
            }else{

                echo "registrar_GF_FACTURA.php?factura=".md5($factura);
            }
        }
    break;
    #**** Validar Tipo Factura, Tipo Recaudo ******#
    case 3:
        $factura = $_REQUEST['id_factura'];
        #Se Busca Si tiene detalles
        $rta =0;
        $det = $con->Listar("SELECT * FROM gp_detalle_factura WHERE factura = $factura");
        if(count($det)>0){
            $tp = $con->Listar("SELECT tipofactura FROM gp_factura WHERE id_unico= $factura");
            $tr = $con->Listar ("SELECT tipo_recaudo, tipo_comprobante  FROM gp_tipo_factura WHERE id_unico = '".$tp[0][0]."'");
            if(count($tr)>0){
                if(!empty($tr[0][0])){
                    #Verificar Si ya Tuvo Recaudo De Toda La Factura
                    $r=0;
                    for($i=0; $i<count($det); $i++){
                        $rec = $con->Listar("SELECT * FROM gp_detalle_pago WHERE detalle_factura ='".$det[$i][0]."'");
                        if(count($rec)>0){
                            if(!empty($rec[0][0])){
                                $r+=1;
                            }
                        }
                    }
                    if($r==0){
                        $rta = $tr[0][0];
                    }
                }
            }
        }
        echo $rta;
    break;
    #Registrar Pago
    case 4:
        $factura  = $_REQUEST['id_factura'];
        $tipoPago = $_REQUEST['recaudo'];
        $banco    = $_REQUEST['banco'];
        $rta =0;
        #Buscar Datos Factura
        $df = $con->Listar("SELECT f.id_unico,
                        f.numero_factura, tp.nombre,
                        f.tercero, f.descripcion, f.fecha_factura, f.centrocosto
                    FROM gp_factura f LEFT JOIN gp_tipo_factura tp ON tp.id_unico = f.tipofactura
                    WHERE f.id_unico = $factura");
        $fecha      = $df[0][5];
        $responsable= $df[0][3];
        $centrocosto      = $df[0][6];
        #Calcular Numero Pago
        $fac = $con->Listar("SELECT * FROM gp_pago WHERE tipo_pago = $tipoPago AND parametrizacionanno = $panno");
        if(count($fac)>0){
            $sql = $con->Listar("SELECT MAX(numero_pago)  FROM gp_pago WHERE tipo_pago = $tipoPago AND parametrizacionanno = $panno");
            $numeroPago = $sql[0][0] + 1;
        } else {
            $numeroPago = $anno. '000001';
        }
        $estado = 1;

        $sql = "INSERT INTO gp_pago
                (numero_pago,
                tipo_pago,
                responsable,
                fecha_pago,
                banco,
                estado, parametrizacionanno, usuario)
                VALUES('$numeroPago',
                $tipoPago,$responsable,
                '$fecha',$banco,$estado, $panno, $usuario_t)";
        $resultadoP = $mysqli->query($sql);

        if($resultadoP==true){
            #********* Buscar el Registro Pago Realizado **************#
            $idPago = $con->Listar("SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=$tipoPago");
            $pago = $idPago[0][0];
            #************ Registrar Comprobante CNT***************#
            $tipoComprobanteCnt = $con->Listar("select tipo_comprobante from gp_tipo_pago where id_unico=$tipoPago");
            if(!empty($tipoComprobanteCnt[0][0])){
                #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                $tipocnt =$tipoComprobanteCnt[0][0];
                $numeroC=$numeroPago;
                #Descripción del comprobante
                $descripcion= '"Comprobante de recaudo factura N° '.$df[0][1].' '.$df[0][4].'"';
                #Insertamos el comprobante
                $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                        . "values('$numeroC','$fecha',$descripcion,$tipocnt,$panno,$responsable,'1',$compania)";
                $resultInsertC=$mysqli->query($sqlInsertC);
                #Consultamos el ultimo comprobante ingresado
                $idCnt=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocnt and numero=$numeroC");
                $id_cnt = $idCnt[0][0];

                #*********** Comprobante Pptal ***********#
                #Validamos que el tipo de comprobante cnt contenga asocidado un tipo de comprobante cnt o el campo comprobante_pptal no este vacio
                $tipoComPtal=$con->Listar("select comprobante_pptal from gf_tipo_comprobante where id_unico=$tipocnt");
                #Validamos que el tipo de comprobante no venga vacio
                if(!empty($tipoComPtal[0][0])){
                    $tipopptal = $tipoComPtal[0][0];
                    $numeroPp=$numeroPago;
                    #Insertamos los datos en comprobante pptal
                    $insertPptal="insert into "
                            . "gf_comprobante_pptal(numero,fecha,fechavencimiento,descripcion,parametrizacionanno,tipocomprobante,tercero,estado,responsable) "
                            . "values('$numeroPp','$fecha','$fecha',$descripcion,$panno,$tipopptal,$responsable,'1',$responsable)";
                    $resultInsertPptal=$mysqli->query($insertPptal);
                    #Consultamos el ultimo comprobante pptal insertado
                    $idPPAL=$con->Listar("select id_unico from gf_comprobante_pptal where tipocomprobante=$tipopptal and numero=$numeroPp");
                    $id_pptal = $idPPAL[0][0];
                }
                #************ Registrar Comprobante Causación***************#
                $tipoComprobanteC=$con->Listar("select tipo_comp_hom from gf_tipo_comprobante where id_unico=".$tipoComprobanteCnt[0][0]);
                if(!empty($tipoComprobanteC[0][0])){
                    #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                    $tipocau =$tipoComprobanteC[0][0];
                    $numeroCausacion=$numeroPago;
                    #Descripción del comprobante
                    $descripcion= '"Comprobante de causación recaudo factura N° '.$df[0][1].' '.$df[0][4].'"';
                    #Insertamos el comprobante
                    $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                            . "values('$numeroCausacion','$fecha',$descripcion,$tipocau,$panno,$responsable,'1',$compania)";
                    $resultInsertC=$mysqli->query($sqlInsertC);
                    #Consultamos el ultimo comprobante ingresado
                    $idCau=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocau and numero=$numeroCausacion");
                    $id_causacion = $idCau[0][0];

                }
            }
            # Buscar4 Valor Factura
            $vf = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura
            WHERE factura = $factura ");
            $valor = $vf[0][0];
            $dp = guardarPagoFactura('',$factura,$pago, $valor);
            if($dp>0){
                if($n_doc_com=='890206033'){
                    $cus = $con->Listar("SELECT df.* FROM gp_detalle_factura
                        LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                        WHERE df.factura = $factura AND c.nombre like '%uso de suelo%'");
                    if(count($cus)>0){
                        $email = enviarEmail($responsable, 2);
                    }
                }
                if (empty($id_cnt)){
                    $id_cnt =0;
                }
                if (empty($id_pptal)){
                    $id_pptal =0;
                }
                if (empty($id_causacion)){
                    $id_causacion =0;
                }
                $reg=registrarDetallesPago($pago,$id_cnt,$id_pptal,$id_causacion);
                #** Buscar si aplica cupo
                $ac = $con->Listar("SELECT * FROM gp_tipo_pago WHERE aplica_cupo = 1 AND id_unico = $tipoPago");
                if(!empty($ac[0][0])){
                    #Buscar si en la factura existe concepto configurado donde validacion = 1 
                    $cf = $con->Listar("SELECT cf.id_unico, df.cantidad  FROM gf_configuracion_facturacion cf 
                        LEFT JOIN gp_detalle_factura df ON cf.concepto = df.concepto_tarifa 
                        WHERE cf.validacion = 1 AND df.factura = $factura");
                    if(!empty($cf[0][0])){
                        #** Buscar en la tabla gb_establecimiento el del tercero 
                        $es = $con->Listar("SELECT * FROM gb_establecimiento WHERE propietario = $responsable");
                        if(!empty($es[0][0])){
                            #** Guardar Cupo
                            $sql_cons ="INSERT INTO `gb_cupos_apartados`
                                ( `proveedor`, `fecha_ingreso`, `numero_animales`,`observaciones`,`estado`)
                            VALUES (:proveedor, :fecha_ingreso, :numero_animales,:observaciones,:estado)";
                            $sql_dato = array(
                                array(":proveedor",$es[0][0]),
                                array(":fecha_ingreso", date('Y-m-d')),
                                array(":numero_animales",$cf[0][1]),
                                array(":observaciones",'Pago de Factura'),
                                array(":estado",'Apartado'),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                        }
                        
                        
                    }
                }
                  
            }
        }

        echo $rta;
    break;
    #Validar Que Tenga Todas Las Cuentas Configuradas
    case 5:
        $factura = $_REQUEST['factura'];
        $ff = $con->Listar("SELECT fecha_factura FROM gf_factura WHERE id_unico = $factura");
        $fecha_factura = $ff[0][0];
        $html ="";
        $concept    = validarConfiguracion($factura, $fecha_factura, $fecha_factura);
        $count      = count(explode(",", $concept));
        if($count>1){
            $html.="Existen Conceptos Sin Configurar";
            $rta = 1;
        } else {
            $rta = 0;
        }

        $datos = array("msj"=>$html,"rta"=>$rta);
        echo json_encode($datos);
    break;
    # Validar Factura Tenga Pago
    case 6:
        $factura = $_REQUEST['id_unico'];
        $se = $con->Listar("SELECT * FROM gp_detalle_pago WHERE detalle_factura = $factura");
        if(count($se)>0){
            $re = 0;
        } else {
            $re =1;
        }
        echo $re;
    break;
    #Numero Pago
    case 7:
        $tipo =$_REQUEST['tipo'];
        $tp=$con->Listar("SELECT MAX(numero_pago) FROM gp_pago "
                . "WHERE tipo_pago=$tipo AND parametrizacionanno = $panno ");
        if(!empty($tp[0][0])){
            $numero=$tp[0][0]+1;
        }else{
            $numero=$anno.'000001';
        }
        echo trim($numero);
    break;
    #Cargar Recaudos
    case 8:
        $tipo = $_REQUEST['tipo'];
        $sqlB = "SELECT     pg.id_unico,
            pg.numero_pago,
            tpg.nombre,
            IF(CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)
            IS NULL OR CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) = '',
            (tr.razonsocial),
            CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)) AS NOMBRE,
            tr.numeroidentificacion ,
            DATE_FORMAT(pg.fecha_pago, '%d/%m/%Y')
        FROM        gp_pago pg
        LEFT JOIN   gp_tipo_pago tpg    ON tpg.id_unico = pg.tipo_pago
        LEFT JOIN   gf_tercero tr      ON tr.id_unico = pg.responsable
        LEFT JOIN   gf_tipo_identificacion ti   ON ti.id_unico = tr.tipoidentificacion
        WHERE pg.parametrizacionanno = $panno AND tpg.id_unico = $tipo
        ORDER BY    pg.numero_pago DESC";
        $resultB = $mysqli->query($sqlB);
        if(mysqli_num_rows($resultB)>0){
            echo '<option value="">Recaudos</option>';
            while ($rowB = mysqli_fetch_row($resultB)) {
                $sqlVal = " SELECT  SUM(valor+iva)
                        FROM    gp_detalle_pago
                        WHERE   pago = $rowB[0]";
                $resultVal = $mysqli->query($sqlVal);
                $val = mysqli_fetch_row($resultVal);
                echo "<option value=".$rowB[0].">".$rowB[1].' '.mb_strtoupper($rowB[2]).' '.$rowB[5].' '.ucwords(mb_strtolower($rowB[3])).' - '.$rowB[4].' '."$".number_format($val[0],2,',','.')."</option>";
            }
        } else {
            echo '<option value="">No Hay Recaudos</option>';
        }
    break;
    #***** Buscar Recaudo *******#
    case 9:
        $pago = $_POST['pago'];
        //Consultamos su relación con cnt y pptal
        $sql = "SELECT cn.id_unico as cnt, cp.id_unico as ptal
                FROM gp_pago p
                LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
                LEFT JOIN gf_detalle_comprobante dc ON dc.id_unico = dp.detallecomprobante
                LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico
                LEFT JOIN gf_detalle_comprobante_pptal dpt ON dc.detallecomprobantepptal = dpt.id_unico
                LEFT JOIN gf_comprobante_pptal cp ON dpt.comprobantepptal = cp.id_unico
                WHERE p.id_unico = $pago";
        $result = $mysqli->query($sql);
        $row = $result->fetch_row();
        if(!empty($row[0])){
            //Imprimimos la url para que se redireccione la pagina
            if(!empty($row[1])){
                echo "registrar_GF_RECAUDO_FACTURACION_2.php?recaudo=".md5($pago)."&cnt=".md5($row[0])."&pptal=".md5($row[1]);
            }else {
                echo "registrar_GF_RECAUDO_FACTURACION_2.php?recaudo=".md5($pago)."&cnt=".md5($row[0]);
            }
        } else {
            #Buscar Por Número Y Tipo
            $sql2 = "SELECT cn.id_unico as cnt, cp.id_unico as ptal
                FROM gp_pago p
                LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
                LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico
                LEFT JOIN gf_tipo_comprobante tc ON tp.tipo_comprobante = tc.id_unico
                LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico AND cn.numero = p.numero_pago
                LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = p.numero_pago
                WHERE p.id_unico =$pago";
            $result2 = $mysqli->query($sql2);
            $row2 = $result2->fetch_row();
            if(!empty($row2[0])){
                //Imprimimos la url para que se redireccione la pagina
                if(!empty($row2[1])){
                    echo "registrar_GF_RECAUDO_FACTURACION_2.php?recaudo=".md5($pago)."&cnt=".md5($row2[0])."&pptal=".md5($row2[1]);
                } else {
                    echo "registrar_GF_RECAUDO_FACTURACION_2.php?recaudo=".md5($pago)."&cnt=".md5($row2[0]);
                }
            }else{
                echo "registrar_GF_RECAUDO_FACTURACION_2.php?recaudo=".md5($pago);
            }

        }
        break;
    #******* Id, Facturas Valores Tabla Recaudo Cliente ******#
    case 10:
        require_once './gf_style_tabla.php';
        $tercero         = $_REQUEST['tercero'];
        $valor_ingresado = $_REQUEST['valor_d'];
        $fact = 0;
        $vls  = 0;
        $html = "";
        $facN = 0;
        $arrayfacturasNS = array();
        if(!empty($_REQUEST['facturasnm'])){
            $arrayfacturasNS = explode(",", $_REQUEST['facturasnm']);
        }

        $fac = $con->Listar("SELECT DISTINCT
            f.id_unico, tf.id_unico, tf.prefijo,
            f.numero_factura,
            tf.nombre,
            DATE_FORMAT(f.fecha_factura,'%d/%m/%Y'),
            GROUP_CONCAT(df.id_unico), SUM(df.valor_total_ajustado),
            f.fecha_factura
            FROM gp_factura f
            LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico
            LEFT JOIN gp_detalle_factura df ON df.factura =f.id_unico
            WHERE f.tercero = $tercero
            GROUP BY df.factura
            ORDER BY f.fecha_factura");
        if(count($fac)>0){
            $html .= '<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">';
            $html .= '<div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">';
            $html .= '<table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<td style="display: none;">Identificador</td>';
            $html .= '<td width="30px" align="center"></td>';
            $html .= '<td><strong>Tipo Factura</strong></td>';
            $html .= '<td><strong>Número Factura</strong></td>';
            $html .= '<td><strong>Fecha</strong></td>';
            $html .= '<td><strong>Valor Factura</strong></td>';
            $html .= '<td><strong>Valor A Recaudar</strong></td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<th style="display: none;">Identificador</th>';
            $html .= '<th width="7%"></th>';
            $html .= '<th>Tipo Factura</th>';
            $html .= '<th>Número Factura</th>';
            $html .= '<th>Fecha</th>';
            $html .= '<th>Valor Factura</th>';
            $html .= '<th>Valor A Recaudar</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            $saldo  = $valor_ingresado;
            for ($i = 0; $i < count($fac); $i++) {
                $ids_det    = $fac[$i][6];
                $valor_f    = $fac[$i][7];
                #**** Buscar Recaudos ****#
                $rec = $con->Listar("SELECT
                    (SUM(valor)+SUM(iva)+SUM(impoconsumo)+SUM(ajuste_peso)) as total
                    FROM gp_detalle_pago
                    WHERE detalle_factura IN($ids_det)");
                $t_rec = $rec[0][0];
                if(round($valor_f - $t_rec) > 0){
                    if($_REQUEST['tipo']==1){
                        $html .= '<tr>';
                        $html .= '<td style="display: none;">'.$fac[$i][8].'</td>';
                        $html .= '<td>';
                        $html .= '<input type="checkbox" name="seleccion'.$fac[$i][0].'" id="seleccion'.$fac[$i][0].'" class="check_select" title="Seleccione" required style="width: 35%" onchange="cambiovalor('.$fac[$i][0].')"/>';
                        $html .= '</td>';
                        $html .= '<td>'.$fac[$i][2].' - '.$fac[$i][4].'</td>';
                        $html .= '<td>'.$fac[$i][3].'</td>';
                        $html .= '<td>'.$fac[$i][5].'</td>';
                        $html .= '<td>'.number_format($valor_f,2,'.',',').'</td>';
                        $html .= '<td><input type="hidden" name="valort'.$fac[$i][0].'" id="valort'.$fac[$i][0].'" value="'.($valor_f-$t_rec).'">'.number_format(($valor_f-$t_rec),2,'.',',').'</td>';
                        $html .= '</tr>';
                    } else {
                        if(in_array($fac[$i][0], $arrayfacturasNS)) {
                            $facN .= ','.$fac[$i][0];
                            $html .= '<tr>';
                            $html .= '<td style="display: none;">'.$fac[$i][8].'</td>';
                            $html .= '<td>';
                            $html .= '<input type="checkbox" name="seleccion'.$fac[$i][0].'" id="seleccion'.$fac[$i][0].'" class="check_select" title="Seleccione" required style="width: 35%"  onchange="cambiovalorVl('.$fac[$i][0].')"/>';
                            $html .= '</td>';
                            $html .= '<td>'.$fac[$i][2].' - '.$fac[$i][4].'</td>';
                            $html .= '<td>'.$fac[$i][3].'</td>';
                            $html .= '<td>'.$fac[$i][5].'</td>';
                            $html .= '<td>'.number_format($valor_f,2,'.',',').'</td>';
                            $html .= '<td><input type="hidden" name="valort'.$fac[$i][0].'" id="valort'.$fac[$i][0].'" value="'.($valor_f-$t_rec).'">'.number_format(($valor_f-$t_rec),2,'.',',').'</td>';
                            $html .= '</tr>';
                        } else {
                            $saldo_factura = $valor_f - $t_rec;
                            $saldo -=$saldo_factura;
                            if($saldo >= 0 ){
                                $fact .= ','.$fac[$i][0];
                                $vls  += ($valor_f-$t_rec);
                                $html .= '<tr>';
                                $html .= '<td style="display: none;">'.$fac[$i][8].'</td>';
                                $html .= '<td>';
                                $html .= '<input type="checkbox" name="seleccion'.$fac[$i][0].'" id="seleccion'.$fac[$i][0].'" class="check_select" title="Seleccione" required style="width: 35%" checked="checked" onchange="cambiovalorVl('.$fac[$i][0].')"/>';
                                $html .= '</td>';
                                $html .= '<td>'.$fac[$i][2].' - '.$fac[$i][4].'</td>';
                                $html .= '<td>'.$fac[$i][3].'</td>';
                                $html .= '<td>'.$fac[$i][5].'</td>';
                                $html .= '<td>'.number_format($valor_f,2,'.',',').'</td>';
                                $html .= '<td><input type="hidden" name="valort'.$fac[$i][0].'" id="valort'.$fac[$i][0].'" value="'.($valor_f-$t_rec).'">'.number_format(($valor_f-$t_rec),2,'.',',').'</td>';
                                $html .= '</tr>';
                            } else {
                                $saldo +=$saldo_factura;
                                if(round($saldo)>0){
                                    $fact .= ','.$fac[$i][0];
                                    $vls  += ($saldo);
                                    $html .= '<tr>';
                                    $html .= '<td style="display: none;">'.$fac[$i][8].'</td>';
                                    $html .= '<td>';
                                    $html .= '<input type="checkbox" name="seleccion'.$fac[$i][0].'" id="seleccion'.$fac[$i][0].'" class="check_select" title="Seleccione" required style="width: 35%" checked="checked" onchange="cambiovalorVl('.$fac[$i][0].')"/>';
                                    $html .= '</td>';
                                    $html .= '<td>'.$fac[$i][2].' - '.$fac[$i][4].'</td>';
                                    $html .= '<td>'.$fac[$i][3].'</td>';
                                    $html .= '<td>'.$fac[$i][5].'</td>';
                                    $html .= '<td>'.number_format($valor_f,2,'.',',').'</td>';
                                    $html .= '<td><input type="hidden" name="valort'.$fac[$i][0].'" id="valort'.$fac[$i][0].'" value="'.($saldo).'">'.number_format(($saldo),2,'.',',').'</td>';
                                    $html .= '</tr>';
                                    $saldo =0;
                                    $i--;
                                } else {
                                    $html .= '<tr>';
                                    $html .= '<td style="display: none;">'.$fac[$i][8].'</td>';
                                    $html .= '<td>';
                                    $html .= '<input type="checkbox" name="seleccion'.$fac[$i][0].'" id="seleccion'.$fac[$i][0].'" class="check_select" title="Seleccione" required style="width: 35%" disabled="disabled"/>';
                                    $html .= '</td>';
                                    $html .= '<td>'.$fac[$i][2].' - '.$fac[$i][4].'</td>';
                                    $html .= '<td>'.$fac[$i][3].'</td>';
                                    $html .= '<td>'.$fac[$i][5].'</td>';
                                    $html .= '<td>'.number_format($valor_f,2,'.',',').'</td>';
                                    $html .= '<td><input type="hidden" name="valort'.$fac[$i][0].'" id="valort'.$fac[$i][0].'" value="'.($valor_f-$t_rec).'">'.number_format(($valor_f-$t_rec),2,'.',',').'</td>';
                                    $html .= '</tr>';
                                    $saldo =0;
                                }

                            }
                        }

                    }
                }
            }
            $html .= '</tbody>';
            $html .= '</table> ';
            $html .= '</div> ';
            $html .= '</div> ';
            $html .= '<input type="hidden" name="facValor" id="facValor" value="'.$fact.'"/>';
            $html .= '<input type="hidden" name="SelValor" id="SelValor" value="'.$vls.'"/>';
            $html .= '<input type="hidden" name="facturasN" id="facturasN" value="'.$facN.'"/>';
        }
        echo $html;
    break;
    #********** Cargar Rubro Fuente ***********#
    case 11:
        $concepto_r = $_REQUEST['valor'] ;
        $row_rf = $con->Listar("SELECT rf.id_unico, rb.codi_presupuesto, rb.nombre, f.nombre
            FROM gf_concepto_rubro cr
            LEFT JOIN gf_rubro_pptal rb ON cr.rubro = rb.id_unico
            LEFT JOIN gf_rubro_fuente rf ON rb.id_unico = rf.rubro
            LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico
            WHERE cr.id_unico = $concepto_r");
        if(count($row_rf)>0){
            for ($i = 0;$i < count($row_rf);$i++) {
                echo '<option value="'.$row_rf[$i][0].'">'.$row_rf[$i][1].' - '.ucwords(mb_strtolower($row_rf[$i][2])).' '.ucwords(mb_strtolower($row_rf[$i][3])).'</option>';
            }
        }
    break;
        #***** Guardar, Modificar, Eliminar Configuración Facturación ****#
    case 12:
        $panno ;
        $tipo_c         = $_REQUEST['tipo_c'];
        $concepto_fact  = $_REQUEST['concepto'];
        if(empty($_REQUEST['con_ru']) && empty($_REQUEST['rub_fu']))
        #*** Validar Si Ya Existe Configuración Para El Concepto, Tipo, Cartera Y Año ****#
        $cf = $con->Listar("SELECT * FROM gp_configuracion_concepto
                WHERE concepto= $concept_fact AND parametrizacionanno = $panno
                AND tipo_c = $tipo_c");
        if(count($cf)>0){

        }


    break;
    #********** Guardar Recaudo Facturación Por Cliente ***********#
    case 13:
        $factura    = $_REQUEST['id_factura'];
        $tipoPago   = $_REQUEST['tipoRecaudo'];
        $banco      = $_REQUEST['banco'];
        $fecha      = fechaC($_REQUEST['fecha']);
        $responsable= $_REQUEST['tercero'];
        $numeroPago = $_REQUEST['numero'];
        $estado     = 1;
        $rta        = 0;
        $sql = "INSERT INTO gp_pago
                (numero_pago,
                tipo_pago,
                responsable,
                fecha_pago,
                banco,
                estado, parametrizacionanno, usuario)
                VALUES('$numeroPago',
                $tipoPago,$responsable,
                '$fecha',$banco,$estado, $panno, $usuario_t)";
        $resultadoP = $mysqli->query($sql);

        if($resultadoP==true){
            #********* Buscar el Registro Pago Realizado **************#
            $idPago = $con->Listar("SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=$tipoPago");
            $pago = $idPago[0][0];
            #************ Registrar Comprobante CNT***************#
            $tipoComprobanteCnt=$con->Listar("select tipo_comprobante from gp_tipo_pago where id_unico=$tipoPago");
            if(!empty($tipoComprobanteCnt[0][0])){
                #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                $tipocnt =$tipoComprobanteCnt[0][0];
                $numeroCnt=$con->Listar("select max(numero) from gf_comprobante_cnt "
                        . "where tipocomprobante=$tipocnt AND parametrizacionanno = $panno ");
                if(!empty($numeroCnt[0][0])){
                    $numeroC=$numeroCnt[0][0]+1;
                }else{
                    $numeroC=$anno.'00001';
                }
                #Descripción del comprobante
                $descripcion= '"Comprobante de Recaudo Factura Por Cliente Recaudo N° '.$numeroPago.'"';
                #Insertamos el comprobante
                $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                        . "values('$numeroC','$fecha',$descripcion,$tipocnt,$panno,$responsable,'1',$compania)";
                $resultInsertC=$mysqli->query($sqlInsertC);
                #Consultamos el ultimo comprobante ingresado
                $idCnt=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocnt and numero=$numeroC");
                $id_cnt = $idCnt[0][0];

                #*********** Comprobante Pptal ***********#
                #Validamos que el tipo de comprobante cnt contenga asocidado un tipo de comprobante cnt o el campo comprobante_pptal no este vacio
                $tipoComPtal=$con->Listar("select comprobante_pptal from gf_tipo_comprobante where id_unico=$tipocnt");
                #Validamos que el tipo de comprobante no venga vacio
                if(!empty($tipoComPtal[0][0])){
                    $tipopptal = $tipoComPtal[0][0];
                    #Consultamos el ultmo número registrado de acuerdo al tipo de comprobante pptal
                    $numeroP=$con->Listar("select max(numero) from gf_comprobante_pptal where tipocomprobante=$tipopptal AND parametrizacionanno = $panno");
                    #Validamos si el valor consultado viene vacio que inicialize el conteo, de lo contrarop que sume uno al valor obtenido
                    if(!empty($numeroP[0][0])){
                        $numeroPp=$numeroP[0][0]+1;
                    }else{
                        $numeroPp=$anno.'00001';
                    }
                    #Insertamos los datos en comprobante pptal
                    $insertPptal="insert into "
                            . "gf_comprobante_pptal(numero,fecha,fechavencimiento,descripcion,parametrizacionanno,tipocomprobante,tercero,estado,responsable) "
                            . "values('$numeroPp','$fecha','$fecha',$descripcion,$panno,$tipopptal,$responsable,'1',$responsable)";
                    $resultInsertPptal=$mysqli->query($insertPptal);
                    #Consultamos el ultimo comprobante pptal insertado
                    $idPPAL=$con->Listar("select id_unico from gf_comprobante_pptal where tipocomprobante=$tipopptal and numero=$numeroPp");
                    $id_pptal = $idPPAL[0][0];
                }
                #************ Registrar Comprobante Causación***************#
                $tipoComprobanteC=$con->Listar("select tipo_comp_hom from gf_tipo_comprobante where id_unico=".$tipoComprobanteCnt[0][0]);
                if(!empty($tipoComprobanteC[0][0])){
                    #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                    $tipocau =$tipoComprobanteC[0][0];
                    $numeroCa=$con->Listar("select max(numero) from gf_comprobante_cnt "
                            . "where tipocomprobante=$tipocau AND parametrizacionanno = $panno ");
                    if(!empty($numeroCa[0][0])){
                        $numeroCausacion=$numeroCa[0][0]+1;
                    }else{
                        $numeroCausacion=$anno.'00001';
                    }
                    #Descripción del comprobante
                    $descripcion= '"Comprobante Causación Recaudo Factura Por Cliente Recaudo N° '.$numeroPago.'"';
                    #Insertamos el comprobante
                    $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                            . "values('$numeroCausacion','$fecha',$descripcion,$tipocau,$panno,$responsable,'1',$compania)";
                    $resultInsertC=$mysqli->query($sqlInsertC);
                    #Consultamos el ultimo comprobante ingresado
                    $idCau=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocau and numero=$numeroCausacion");
                    $id_causacion = $idCau[0][0];

                }
            }
            #************* Registrar Detalles Pago *********************#
            $sqlValor = "SELECT     id_unico,
                                    dtf.valor,
                                    dtf.iva,
                                    dtf.impoconsumo,
                                    dtf.ajuste_peso,
                                    dtf.concepto_tarifa,
                                    dtf.cantidad
                        FROM        gp_detalle_factura dtf
                        WHERE       dtf.factura = $factura ORDER BY id_unico ASC ";
            $resultValor = $mysqli->query($sqlValor);


            $vpt = 0;
            while($rowValor = mysqli_fetch_row($resultValor)){
                $valor  = $rowValor[1] * $rowValor[6];
                $iva    = (double) $rowValor[2];
                $impo   = (double) $rowValor[3];
                $ajuste = (double) $rowValor[4];

                $sqlc=$con->Listar("SELECT
                    cp.id_unico,
                    c.id_unico ,
                    cr.id_unico,
                    rf.id_unico,
                    crc.cuenta_debito,
                    cd.naturaleza,
                    crc.cuenta_credito,
                    cc.naturaleza,
                    crc.cuenta_iva,
                    civ.naturaleza,
                    crc.cuenta_impoconsumo,
                    ci.naturaleza
                FROM gp_concepto cp
                LEFT JOIN gf_concepto c ON cp.concepto_financiero = c.id_unico
                LEFT JOIN gf_concepto_rubro cr ON cr.concepto = c.id_unico
                LEFT JOIN gf_concepto_rubro_cuenta crc ON cr.id_unico = crc.concepto_rubro
                LEFT JOIN gf_rubro_fuente rf ON cr.rubro = rf.rubro
                LEFT JOIN gf_cuenta cd ON crc.cuenta_debito = cd.id_unico
                LEFT JOIN gf_cuenta cc ON crc.cuenta_credito = cc.id_unico
                LEFT JOIN gf_cuenta civ ON civ.id_unico = crc.cuenta_iva
                LEFT JOIN gf_cuenta ci ON ci.id_unico = crc.cuenta_impoconsumo
                WHERE cp.id_unico =$rowValor[5]");
                if(count($sqlc)>0){
                    $conceptorubro  = $sqlc[0][2];
                    $rubrofuente    = $sqlc[0][3];
                    #********** Detalle Pptal*****************#
                    $insertP = "INSERT INTO gf_detalle_comprobante_pptal
                            (valor, comprobantepptal, conceptorubro,
                            tercero, proyecto, rubrofuente)
                            VALUES(($valor+$ajuste), $id_pptal, $conceptorubro,
                            $responsable, 2147483647, $rubrofuente)";
                    $resultP = $mysqli->query($insertP);
                    $id_dp = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante_pptal WHERE comprobantepptal = $id_pptal");
                    $id_dp = $id_dp[0][0];
                    ##********** Detalle Cnt*****************#
                    #cuenta credito
                    $cc =$sqlc[0][6];
                    #cuenta debito
                    $cd =$sqlc[0][4];
                    $naturalezad = $sqlc[0][5];
                    #Verificar Naturaleza
                    $naturalezac = $sqlc[0][7];
                    $vpt += $valor+$ajuste;
                    if($naturalezac==1){
                        $valorc = ($valor+$ajuste)*-1;

                    } else {
                        $valorc = ($valor+$ajuste);
                    }
                    #Insertar Detalle Cnt
                    $insertD = "INSERT INTO gf_detalle_comprobante
                            (fecha, valor,
                            comprobante, naturaleza, cuenta,
                            tercero, proyecto, centrocosto,
                            detallecomprobantepptal)
                            VALUES('$fecha', $valorc,
                            $id_cnt, $naturalezac, $cc,
                            $responsable,  2147483647, $centrocosto, $id_dp)";
                    $resultado = $mysqli->query($insertD);

                    $id_dc = $con->Listar("SELECT MAX(id_unico) FROM gf_detalle_comprobante WHERE comprobante = $id_cnt");
                    $id_dc = $id_dc[0][0];

                    #Insertar Detalle Causacion
                    ##Debito
                    if($naturalezad==1){
                        $valord = ($valor+$ajuste);
                    } else {
                        $valord = ($valor+$ajuste)*-1;
                    }
                    if($cd == $cc){

                    } else {
                        $insertD = "INSERT INTO gf_detalle_comprobante
                                (fecha, valor,
                                comprobante, naturaleza, cuenta,
                                tercero, proyecto, centrocosto,
                                detalleafectado)
                                VALUES('$fecha', $valord,
                                $id_causacion, $naturalezad, $cd,
                                $responsable,  2147483647, $centrocosto, $id_dc)";
                        $resultado = $mysqli->query($insertD);
                        #** Credito

                        $insertD = "INSERT INTO gf_detalle_comprobante
                                (fecha, valor,
                                comprobante, naturaleza, cuenta,
                                tercero, proyecto, centrocosto,
                                detalleafectado)
                                VALUES('$fecha', $valorc,
                                $id_causacion, $naturalezac, $cc,
                                $responsable,  2147483647, $centrocosto, $id_dc)";
                        $resultado = $mysqli->query($insertD);

                    }

                    #********** Detalle Pago*****************#
                    $sql = "INSERT INTO gp_detalle_pago (detalle_factura,
                    valor, iva, impoconsumo, ajuste_peso, pago, saldo_credito, detallecomprobante)
                    VALUES ($rowValor[0], $valor, $iva, $impo, $ajuste, $pago, 0, $id_dc)";
                    $resultado = $mysqli->query($sql);
                    #Registrar Cuenta Iva
                    if($iva !="" || $iva !=0){
                        #Verificar Naturaleza
                        $civa           = $sqlc[0][8];
                        $naturalezaci   = $sqlc[0][9];
                        $vpt += $iva;
                        if($naturalezaci==1){
                            $valorci = $iva*-1;
                        } else {
                            $valorci = $iva;
                        }
                        $insertD = "INSERT INTO gf_detalle_comprobante
                            (fecha, valor,
                            comprobante, naturaleza, cuenta,
                            tercero, proyecto, centrocosto)
                            VALUES('$fecha', $valorci,
                            $id_cnt, $naturalezaci, $civa,
                            $responsable,  2147483647, $centrocosto)";
                        $resultado = $mysqli->query($insertD);

                    }
                    #Registrar Cuenta Impoconsumo
                    if($impo !="" || $impo !=0){
                        #Verificar Naturaleza
                        $cimpo           = $sqlc[0][10];
                        $naturalezacim   = $sqlc[0][11];
                        $vpt += $impo;
                        if($naturalezacim==1){
                            $valorcim = $impo*-1;
                        } else {
                            $valorcim = $impo;
                        }
                        $insertD = "INSERT INTO gf_detalle_comprobante
                            (fecha, valor,
                            comprobante, naturaleza, cuenta,
                            tercero, proyecto, centrocosto)
                            VALUES('$fecha', $valorcim,
                            $id_cnt, $naturalezacim, $cimpo,
                            $responsable,  2147483647, $centrocosto)";
                        $resultado = $mysqli->query($insertD);
                    }

                } else {
                    #********** Detalle Pago*****************#
                   $sql = "INSERT INTO gp_detalle_pago (detalle_factura,
                    valor, iva, impoconsumo, ajuste_peso, pago)
                    VALUES ($rowValor[0], $valor, $iva, $impo, $ajuste, $pago)";
                    $resultado = $mysqli->query($sql);
                }


            }

            $sqlBanco = $con->Listar("SELECT cb.cuenta, c.naturaleza
                    FROM gf_cuenta_bancaria cb
                    LEFT JOIN gf_cuenta c ON cb.cuenta = c.id_unico
                    WHERE cb.id_unico = $banco");
            $cuentaB = $sqlBanco[0][0];
            $Ncuenta = $sqlBanco[0][1];
            #Registrar Cuenta de Banco
            if($Ncuenta ==1){
                $vpt =$vpt;
            } else {
                $vpt = $vpt*-1;
            }
            $insertD = "INSERT INTO gf_detalle_comprobante
                (fecha, valor,
                comprobante, naturaleza, cuenta,
                tercero, proyecto, centrocosto)
                VALUES('$fecha', $vpt,
                $id_cnt, $Ncuenta, $cuentaB,
                $responsable,  2147483647, $centrocosto)";
            $resultado = $mysqli->query($insertD);

            } else {
                $rta =1;
            }

        echo $rta;
    break;
    #********** Guardar Tipo Cartera ***********#
    case 14:
        $nombre = $_REQUEST['nombre'];
        $dia_i  = $_REQUEST['diasI'];
        $dia_f  = $_REQUEST['diasF'];
        $sql_cons ="INSERT INTO `gp_tipo_cartera`
            ( `nombre`, `dia_inicial`, `dia_final`)
        VALUES (:nombre, :dia_inicial, :dia_final)";
        $sql_dato = array(
            array(":nombre",$nombre),
            array(":dia_inicial",$dia_i),
            array(":dia_final",$dia_f),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta =1;
        } else {
            $rta =2;
        }
        echo $rta;
    break;
    #********** Modificar Tipo Cartera ***********#
    case 15:
        $nombre = $_REQUEST['nombre'];
        $dia_i  = $_REQUEST['diasI'];
        $dia_f  = $_REQUEST['diasF'];
        $id     = $_REQUEST['id'];
        $sql_cons ="UPDATE `gp_tipo_cartera`
        SET `nombre`=:nombre,
        `dia_inicial`=:dia_inicial,
        `dia_final`=:dia_final
        WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":nombre",$nombre),
            array(":dia_inicial",$dia_i),
            array(":dia_final",$dia_f),
            array(":id_unico",$id)
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta =1;
        } else {
            $rta =2;
        }
        echo $rta;
    break;
    #********** Eliminar Tipo Cartera ***********#
    case 16:
        $id     = $_REQUEST['id'];
        $sql_cons ="DELETE FROM  `gp_tipo_cartera`
        WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":id_unico",$id)
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta =1;
        } else {
            $rta =2;
        }
        echo $rta;
    break;
    #********** Recostruir Contabilidad Recaudo ***********#
    case 17:
        $idCnt  =$_REQUEST['idCnt'];
        $idPptal=$_REQUEST['idPptal'];
        $idPago =$_REQUEST['idPago'];
        #** Actualizar Detalels Pago **#
        $sql_cons ="UPDATE  `gp_detalle_pago`
        SET `detallecomprobante` =:detallecomprobante
        WHERE `pago`=:pago ";
        $sql_dato = array(
            array(":detallecomprobante",NULL),
            array(":pago",$idPago),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        $recon = 0;
        if(empty($resp)){
            #*** Buscar Si Tiene Causación
            $cs = causacion($idCnt);
            if(!empty($cs[0][0])){
                $id_causacion =$cs;
                $ec = eliminardetallescnt($id_causacion);
                if($ec==1){
                    $ecn = eliminardetallescnt($idCnt);
                    if($ecn==1){
                        $epp = eliminardetallespptal($idPptal);
                        if($epp==1){
                            $recon = 1;
                        }
                    }
                }
            } else {
                #*** Buscar Si Existe Relacion Por Numero Y Tipo
                $ccs = $con->Listar("SELECT cnc.id_unico
                    FROM gf_comprobante_cnt cn
                    LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                    LEFT JOIN gf_comprobante_cnt cnc ON cn.numero = cnc.numero AND cnc.tipocomprobante = tc.tipo_comp_hom
                    WHERE cn.id_unico =$idCnt");
                $id_causacion =$ccs[0][0];
                $ecn = eliminardetallescnt($idCnt);
                if($ecn==1){
                    $epp = eliminardetallespptal($idPptal);
                    #var_dump($epp);
                    if($epp==1){
                        $recon = 1;
                    }
                }
            }
            #var_dump($recon);
            if($recon==1){
                $reg=registrarDetallesPago($idPago,$idCnt,$idPptal,$id_causacion);
                if($reg==true){
                    #***** Buscar Detalles Actualizar Comprobantes
                    $df = $con->Listar("SELECT DISTINCT f.id_unico,
                        f.numero_factura,f.descripcion , pg.fecha_pago, f.tercero 
                    FROM gp_factura f
                    LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura
                    LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura
                    LEFT JOIN gp_pago pg ON pg.id_unico = dp.pago
                    WHERE dp.pago = $idPago");

                    $fecha_act = $df[0][3];
                    $descp_act = 'Comprobante De Recaudo. Factura N°'.$df[0][1].' '.$df[0][2] ;
                    $descp_act2= 'Comprobante Causación De Recaudo. Factura N°'.$df[0][1].' '.$df[0][2] ;

                    #***** Actualizar Comprobantes
                    #cnt
                    $sql_cons ="UPDATE  `gf_comprobante_cnt`
                    SET `descripcion` =:descripcion,
                    `fecha` =:fecha
                    WHERE `id_unico`=:id_unico ";
                    $sql_dato = array(
                        array(":descripcion",$descp_act),
                        array(":fecha",$fecha_act),
                        array(":id_unico",$idCnt),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    #pptal
                    $sql_cons ="UPDATE  `gf_comprobante_pptal`
                    SET `descripcion` =:descripcion,
                    `fecha` =:fecha
                    WHERE `id_unico`=:id_unico ";
                    $sql_dato = array(
                        array(":descripcion",$descp_act),
                        array(":fecha",$fecha_act),
                        array(":id_unico",$idPptal),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);

                    #causacion
                    $sql_cons ="UPDATE  `gf_comprobante_cnt`
                    SET `descripcion` =:descripcion,
                    `fecha` =:fecha
                    WHERE `id_unico`=:id_unico ";
                    $sql_dato = array(
                        array(":descripcion",$descp_act2),
                        array(":fecha",$fecha_act),
                        array(":id_unico",$id_causacion),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);


                   $rta=true;
                    if($n_doc_com=='890206033'){
                        $cus = $con->Listar("SELECT df.* FROM gp_detalle_factura
                            LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                            WHERE df.factura = $factura AND c.nombre like '%uso de suelo%'");
                        if(count($cus)>0){
                            $email = enviarEmail($df[0][4], 2);
                        }
                    }
                } else {
                    $rta = false;
                }
            } else {
                $rta = false;
            }

        } else {
            $rta =false;
        }
        echo $rta;
    break;
    #********** Guardar/Modificar/Eliminar Configuración Concepto ************#
    case 18:

        $concepto       = $_REQUEST['concepto'];
        $tipo           = $_REQUEST['tipo'];
        $rta            ="";
        if(!empty($_REQUEST['con_rubro'.$concepto]) && !empty($_REQUEST['rubro_fuente'.$concepto])){
            $concepto_r     = $_REQUEST['con_rubro'.$concepto];
            $rubro_fuente   = $_REQUEST['rubro_fuente'.$concepto];
            # *** Buscar Si Existe Configuración ***#
            $cf = $con->Listar("SELECT * FROM gp_configuracion_concepto
                    WHERE tipo_cartera = $tipo
                    AND concepto = $concepto
                    AND parametrizacionanno = $panno");
            if(count($cf)>0){
                $sql_cons ="UPDATE  `gp_configuracion_concepto`
                SET `concepto_rubro` =:concepto_rubro,
                `rubro_fuente` =:rubro_fuente
                WHERE `id_unico`=:id_unico ";
                $sql_dato = array(
                    array(":concepto_rubro",$concepto_r),
                    array(":rubro_fuente",$rubro_fuente),
                    array(":id_unico",$cf[0][0]),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($resp)){
                    $rta ='Información Modificada Correctamente';
                } else {
                    $rta ='No Se Ha Podido Modificar La Información';
                }

            } else {
                $sql_cons ="INSERT INTO `gp_configuracion_concepto`
                ( `concepto`, `concepto_rubro`, `rubro_fuente`,
                `tipo_cartera`,`parametrizacionanno`)
                VALUES (:concepto, :concepto_rubro, :rubro_fuente,
                :tipo_cartera,:parametrizacionanno)";
                $sql_dato = array(
                    array(":concepto",$concepto),
                    array(":concepto_rubro",$concepto_r),
                    array(":rubro_fuente",$rubro_fuente),
                    array(":tipo_cartera",$tipo),
                    array(":parametrizacionanno",$panno),

                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($resp)){
                    $rta ='Información Guardada Correctamente';
                } else {
                    $rta ='No Se Ha Podido Guardar La Información';
                }
            }

        } else {
            $sql_cons ="DELETE FROM `gp_configuracion_concepto`
            WHERE `concepto`=:concepto
            AND `tipo_cartera`=:tipo_cartera
            AND `parametrizacionanno`=:parametrizacionanno";
            $sql_dato = array(
                array(":concepto",$concepto),
                array(":tipo_cartera",$tipo),
                array(":parametrizacionanno",$panno),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $rta ='Información Eliminada Correctamente';
            } else {
                $rta ='No Se Ha Podido Eliminar La Información';
            }
        }
        echo $rta;
    break;
    #********* Cargar Rubro y Concepto Factura *****#
    case 19:
        $concepto = $_POST['concepto'];
        if(!empty($concepto)){
            #*** Buscar Tipo Cartera Min *** #
            $tipo_c = carteradia(0);
            $row = $con->Listar("SELECT cf.id_unico,
                cr.id_unico, rf.id_unico,
                LOWER(rb.nombre), rb.codi_presupuesto,
                LOWER(f.nombre)
                FROM gp_configuracion_concepto cf
                LEFT JOIN gf_concepto_rubro cr ON cf.concepto_rubro = cr.id_unico
                LEFT JOIN gf_rubro_fuente rf ON cf.rubro_fuente = rf.id_unico
                LEFT JOIN gf_rubro_pptal rb ON rf.rubro = rb.id_unico
                LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico
                WHERE cf.concepto = $concepto
                AND cf.tipo_cartera =$tipo_c
                AND cf.parametrizacionanno = $panno");
            for ($i = 0; $i < count($row); $i++) {
                echo '<option value="'.$row[$i][2].'">'.$row[$i][4].' - '.ucwords($row[$i][3]).'</option>;'.$row[$i][1];
            }
        }
    break;
    #****** Saldo Factura Por Concepto *******#
    case 20:
        if(!empty($_POST['factura'])) {
            $factura  = $_POST['factura'];
            #Buscar si el tipo tiene tipo_cambio 
            $tc = 0;
            $tf = $con->Listar("SELECT * FROM gp_factura f LEFT JOIN gp_tipo_factura tf ON tf.id_unico = f.tipofactura WHERE f.id_unico = $factura AND tf.tipo_cambio IS NOT NULL");
            if(!empty($tf[0][0])){
                $tf = 1;
            }

            if(!empty($_POST['concepto'])){
                $concepto = $_POST['concepto'];
                $sql = "SELECT    SUM(dtf.valor_total_ajustado), GROUP_CONCAT(dtf.id_unico) , SUM(dtf.valor_conversion) 
                        FROM      gp_factura fat
                        LEFT JOIN gp_detalle_factura dtf           ON dtf.factura = fat.id_unico
                        LEFT JOIN gf_detalle_comprobante dtc       ON dtf.detallecomprobante = dtc.id_unico
                        LEFT JOIN gf_detalle_comprobante_pptal dtp ON dtc.detallecomprobantepptal = dtp.id_unico
                        WHERE     fat.id_unico         = $factura
                        AND       dtf.concepto_tarifa = $concepto ";
                $result = $mysqli->query($sql);
                $row = mysqli_fetch_row($result);
                $conteo = mysqli_num_rows($result);
                if($conteo > 0){
                    $sqlDP = "SELECT SUM(valor+iva+impoconsumo+ajuste_peso), SUM(valor_conversion) FROM gp_detalle_pago WHERE detalle_factura IN( $row[1])";
                    $rsDP = $mysqli->query($sqlDP);
                    if($tf == 1){
                        if(mysqli_num_rows($rsDP) > 0){
                            $rowDP = mysqli_fetch_row($rsDP);
                            echo $row[2] - $rowDP[1];
                        } else {
                            echo ($row[2]);
                        }
                    } else {
                        if(mysqli_num_rows($rsDP) > 0){
                            $rowDP = mysqli_fetch_row($rsDP);
                            echo $row[0] - $rowDP[0];
                        } else {
                            echo ($row[0]);
                        }
                    }
                }
            } else {
                $sumDF  = 0; 
                $sumDP  = 0;
                $valorD = 0;
                $sqlDF = $con->Listar("SELECT SUM(dtf.valor_total_ajustado), SUM(dtf.valor_conversion)  
                          FROM   gp_detalle_factura dtf
                          WHERE  dtf.factura = $factura");
                
                $sqlDP = $con->Listar("SELECT SUM(dp.valor+dp.iva+dp.impoconsumo+dp.ajuste_peso), SUM(dp.valor_conversion) 
                    FROM gp_detalle_pago dp
                    LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico
                    WHERE df.factura=$factura");
                
                if($tf == 1){
                    if(count($sqlDP) > 0){
                        $sumDP += $sqlDP[0][1];
                    } 
                    $sumDF += $sqlDF[0][1];
                    
                } else {
                    if(count($sqlDP) > 0){
                        $sumDP += $sqlDP[0][0];
                    } 
                    $sumDF += $sqlDF[0][0];
                }
                $valorD = $sumDF - $sumDP;
                echo ($valorD);
            }
        }
    break;
    #****** Modificar Recaudo ***#
    case 21:
        $pago   = $_REQUEST['id'];
        $cnt    = $_REQUEST['cnt'];
        $pptal  = $_REQUEST['pptal'];
        $banco  = $_REQUEST['banco'];
        $terce  = $_REQUEST['tercero'];
        $fecha  = fechaC($_REQUEST['fecha']);
        $rta    = 0;
        # Actualizar Pago
        $sql_cons ="UPDATE  `gp_pago`
        SET  `banco`=:banco,
        `responsable`=:responsable,
        `fecha_pago`=:fecha_pago
        WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":banco",$banco),
            array(":responsable",$terce),
            array(":fecha_pago",$fecha),
            array(":id_unico",$pago),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            # Actualizar Cnt
            $sql_cons ="UPDATE  `gf_comprobante_cnt`
            SET  `tercero`=:tercero,
            `fecha`=:fecha
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":tercero",$terce),
                array(":fecha",$fecha),
                array(":id_unico",$cnt),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            # Actualizar Pptal
            $sql_cons ="UPDATE  `gf_comprobante_pptal`
            SET  `tercero`=:tercero,
            `fecha`=:fecha
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":tercero",$terce),
                array(":fecha",$fecha),
                array(":id_unico",$pptal),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            #*** Buscar Comprobante Causacion
            $id_c = causacion($cnt);
            if(!empty($id_c)){
                # Actualizar Cnt Causacion
                $sql_cons ="UPDATE  `gf_comprobante_cnt`
                SET  `tercero`=:tercero,
                `fecha`=:fecha
                WHERE `id_unico`=:id_unico ";
                $sql_dato = array(
                    array(":tercero",$terce),
                    array(":fecha",$fecha),
                    array(":id_unico",$id_c),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
        } else {
            $rta= 1;
        }
        echo $rta;
    break;
    #********** Eliminar Recaudo ***********#
    case 22:
        $idCnt  =$_REQUEST['idCnt'];
        $idPptal=$_REQUEST['idPptal'];
        $idPago =$_REQUEST['idPago'];
        $rta = 0;
        #*** Buscar Si Tiene Causación
        $cs = causacion($idCnt);
        if(!empty($cs[0][0])){
            $id_causacion =$cs;
            $ec = eliminardetallescnt($id_causacion);
        }
        if(!empty($idPago)){
            $sql_cons ="DELETE FROM  `gp_detalle_pago`
            WHERE `pago`=:pago ";
            $sql_dato = array(
                array(":pago",$idPago)
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            #var_dump($resp);
            if(empty($resp)){
                if(!empty($idCnt)){
                    $ecn = eliminardetallescnt($idCnt);
                    $ecc = eliminarDetallesRetencion($idCnt);
                }
                if(!empty($idPptal)){
                    $epp = eliminardetallespptal($idPptal);
                }
                ### *** Actualizar FDacturas En Recaudo Cliente ** #
                $upd = $sql_cons ="UPDATE `gp_recaudos_cliente`
                SET `facturas`=:facturas
                WHERE `pago`=:pago ";
                $sql_dato = array(
                    array(":facturas",NULL),
                    array(":pago",$idPago)
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $rta = 1;
            }
        }
        echo $rta;
    break;
    #********** Guardar Recaudo X Cliente Cabecera***********#
    case 23:
        $tercero    = $_REQUEST['tercero_s'];
        $banco      = $_REQUEST['banco'];
        $tipo_r     = $_REQUEST['tipoRecaudo'];
        $numero     = $_REQUEST['numero'];
        $fecha      = fechaC($_REQUEST['fecha']);
        $estado     = 1;
        $rta        = 0;
        $url        = "";
        $id_cnt     = "";
        $id_pptal   = "";
        $id_causac  = "";
        #************* Guardar Pago ************#
        $sql_cons ="INSERT INTO `gp_pago`
        ( `numero_pago`, `tipo_pago`,
        `responsable`,`fecha_pago`,
        `banco`,`parametrizacionanno`,`estado`,`usuario`)
        VALUES (:numero_pago, :tipo_pago,
        :responsable, :fecha_pago,
        :banco,:parametrizacionanno,:estado, :usuario)";
        $sql_dato = array(
            array(":numero_pago",$numero),
            array(":tipo_pago",$tipo_r),
            array(":responsable",$tercero),
            array(":fecha_pago",$fecha),
            array(":banco",$banco),
            array(":parametrizacionanno",$panno),
            array(":estado",$estado),
            array(":usuario",$usuario_t),

        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);

        if(empty($resp)){
            #* Buscar Id Pago *#
            $pg = $con->Listar("SELECT MAX(id_unico) FROM gp_pago
                WHERE numero_pago=$numero AND tipo_pago=$tipo_r");
            $id_pago = $pg[0][0];
            #* Tipo de comprobante cnt asociado al Pago
            $tipo_cnt = $con->Listar("SELECT tipo_comprobante FROM gp_tipo_pago
                WHERE id_unico=$tipo_r");
            $tipo_cnt = $tipo_cnt[0][0];
            if(!empty($tipo_cnt)){
                #************* Guardar Cnt ************#
                $sql_cons ="INSERT INTO `gf_comprobante_cnt`
                ( `numero`, `tipocomprobante`,
                `tercero`,`fecha`,
                `parametrizacionanno`,`estado`,`compania`)
                VALUES (:numero, :tipocomprobante,
                :tercero, :fecha,
                :parametrizacionanno,:estado,:compania)";
                $sql_dato = array(
                    array(":numero",$numero),
                    array(":tipocomprobante",$tipo_cnt),
                    array(":tercero",$tercero),
                    array(":fecha",$fecha),
                    array(":parametrizacionanno",$panno),
                    array(":estado",$estado),
                    array(":compania",$compania),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                if(empty($resp)){
                    #* Buscar Id Cnt *#
                    $pg = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_cnt
                        WHERE numero=$numero AND tipocomprobante=$tipo_cnt");
                    $id_cnt = $pg[0][0];
                    #* Buscar Tipo Ppptal
                    $tipo_p = $con->Listar("SELECT comprobante_pptal, tipo_comp_hom
                        FROM gf_tipo_comprobante WHERE id_unico =$tipo_cnt");
                    $tipo_pptal = $tipo_p[0][0];
                    $tipo_caus  = $tipo_p[0][1];
                    if(!empty($tipo_pptal)){
                        #************* Guardar Pptal ************#
                        $sql_cons ="INSERT INTO `gf_comprobante_pptal`
                        ( `numero`, `tipocomprobante`,
                        `tercero`,`fecha`,
                        `parametrizacionanno`,`estado`)
                        VALUES (:numero, :tipocomprobante,
                        :tercero, :fecha,
                        :parametrizacionanno,:estado)";
                        $sql_dato = array(
                            array(":numero",$numero),
                            array(":tipocomprobante",$tipo_pptal),
                            array(":tercero",$tercero),
                            array(":fecha",$fecha),
                            array(":parametrizacionanno",$panno),
                            array(":estado",$estado),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        if(empty($resp)){
                            #* Buscar Id Pptal *#
                            $pg = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_pptal
                                WHERE numero=$numero AND tipocomprobante=$tipo_pptal");
                            $id_pptal = $pg[0][0];
                            if(!empty($tipo_caus)){
                                #************* Guardar Cnt ************#
                                $sql_cons ="INSERT INTO `gf_comprobante_cnt`
                                ( `numero`, `tipocomprobante`,
                                `tercero`,`fecha`,
                                `parametrizacionanno`,`estado`)
                                VALUES (:numero, :tipocomprobante,
                                :tercero, :fecha,
                                :parametrizacionanno,:estado)";
                                $sql_dato = array(
                                    array(":numero",$numero),
                                    array(":tipocomprobante",$tipo_caus),
                                    array(":tercero",$tercero),
                                    array(":fecha",$fecha),
                                    array(":parametrizacionanno",$panno),
                                    array(":estado",$estado),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                                if(empty($resp)){
                                    #* Buscar Id Cnt *#
                                    $pg = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_cnt
                                        WHERE numero=$numero AND tipocomprobante=$tipo_caus");
                                    $id_causac = $pg[0][0];
                                } else {
                                    #*** Eliminar Pago ***#
                                    $sql_cons ="DELETE FROM  `gp_pago`
                                    WHERE `id_unico`=:id_unico ";
                                    $sql_dato = array(
                                        array(":id_unico",$id_pago)
                                    );
                                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                                    #*** Eliminar Cnt ***#
                                    $sql_cons ="DELETE FROM  `gf_comprobante_cnt`
                                    WHERE `id_unico`=:id_unico ";
                                    $sql_dato = array(
                                        array(":id_unico",$id_cnt)
                                    );
                                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                                    #*** Eliminar Pptal ***#
                                    $sql_cons ="DELETE FROM  `gf_comprobante_pptal`
                                    WHERE `id_unico`=:id_unico ";
                                    $sql_dato = array(
                                        array(":id_unico",$id_pptal)
                                    );
                                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                                    $rta =1;
                                }
                            }
                        } else {
                            #*** Eliminar Pago ***#
                            $sql_cons ="DELETE FROM  `gp_pago`
                            WHERE `id_unico`=:id_unico ";
                            $sql_dato = array(
                                array(":id_unico",$id_pago)
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            #*** Eliminar Cnt ***#
                            $sql_cons ="DELETE FROM  `gf_comprobante_cnt`
                            WHERE `id_unico`=:id_unico ";
                            $sql_dato = array(
                                array(":id_unico",$id_cnt)
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);

                            $rta =1;
                        }
                    }
                }  else {
                    #*** Eliminar Pago ***#
                    $sql_cons ="DELETE FROM  `gp_pago`
                    WHERE `id_unico`=:id_unico ";
                    $sql_dato = array(
                        array(":id_unico",$id_pago)
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);

                    $rta =1;
                }
            }

        } else {
            $rta =1;
        }

        if($rta==0){
            #*** Guardar En Tabla Recaudo ***#
            if(empty($id_cnt)){
                $id_cnt =NULL;
            }
            if(empty($id_pptal)){
                $id_pptal =NULL;
            }
            $sql_cons ="INSERT INTO `gp_recaudos_cliente`
            ( `tercero`, `fecha`,
            `pago`,`cnt`,
            `pptal`,`usuario`,
            `fecha_elaboracion`,`parametrizacionanno`)
            VALUES (:tercero, :fecha,
            :pago, :cnt,
            :pptal,:usuario,
            :fecha_elaboracion,:parametrizacionanno)";
            $sql_dato = array(
                array(":tercero",$tercero),
                array(":fecha",$fecha),
                array(":pago",$id_pago),
                array(":cnt",$id_cnt),
                array(":pptal",$id_pptal),
                array(":usuario",$usuario),
                array(":fecha_elaboracion",date('y-m-d')),
                array(":parametrizacionanno",$panno),

            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                #** Buscar Id Registro
                $id_r = $con->Listar("SELECT MAX(id_unico) FROM gp_recaudos_cliente WHERE pago = $id_pago AND parametrizacionanno = $panno");
                $id_r = $id_r[0][0];
                $url ='GF_RECAUDO_CLIENTE.php?tercero='.$tercero.'&id='.$id_r;
            } else {
                $rta=1;
            }
        }
        $datos = array("url"=>$url,"rta"=>$rta);
        echo json_encode($datos);
    break;
    #****** Guardar Detalles Recaudo ***#
    case 24:
        # *** Captura Variables *** #
        $factura = $_POST['sltFactura2'];
        $pago    = $_POST['txtIdRecaudo'];
        $tc      = 0;
        $diferen = 0;
        $tf = $con->Listar("SELECT tf.tipo_cambio FROM gp_factura f LEFT JOIN gp_tipo_factura tf ON tf.id_unico = f.tipofactura WHERE f.id_unico = $factura AND tf.tipo_cambio IS NOT NULL");
        $trmA   =  0;
        if(!empty($tf[0][0])){
            $trmA    = trmA($tf[0][0]);
            $trmF    = trmF($factura);
            $valorF  = $_POST['txtValor'];
            $valrt   = $valorF * $trmF;
            $valor   = $valorF * $trmA;
            $diferen = ROUND(($valor - $valrt),2);
        } else {
            $valor   = $_POST['txtValor'];
        }
        
        #********* Validar Si Escogio o No Concepto ********#
        if(empty($_POST['sltConcepto'])){
            $rta = guardarPagoFactura('',$factura,$pago, $valor);
        } else {
            $concepto = $_POST['sltConcepto'];
            $rta = guardarPagoFactura($concepto,$factura,$pago, $valor);
        }
        if($diferen !=0){
            // Buscar Concepto Ajuste
            if($diferen>0){
                $s ='+';
            } else {
                $s ='-';
            }
            $ca = $con->Listar("SELECT * FROM gp_concepto WHERE ajuste ='$s'");
            $da = $con->Listar("SELECT MAX(id_unico) FROM gp_detalle_factura WHERE factura = $factura AND concepto_tarifa = ".$ca[0][0]);
            IF(empty($da[0][0])){
                $sql_cons ="INSERT INTO `gp_detalle_factura`
                ( `factura`, `concepto_tarifa`,
                `valor`,`cantidad`,`iva`,`impoconsumo`,
                `ajuste_peso`,`valor_total_ajustado`)
                VALUES (:factura,:concepto_tarifa, :valor,:cantidad,
                :iva, :impoconsumo,
                :ajuste_peso,:valor_total_ajustado)";
                $sql_dato = array(
                    array(":factura",$factura),
                    array(":concepto_tarifa",$ca[0][0]),
                    array(":valor",0),
                    array(":cantidad",1),
                    array(":iva",0),
                    array(":impoconsumo",0),
                    array(":ajuste_peso",0),
                    array(":valor_total_ajustado",0),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $da = $con->Listar("SELECT MAX(id_unico) FROM gp_detalle_factura WHERE factura = $factura AND concepto_tarifa = ".$ca[0][0]);
            }
            $sql_cons ="INSERT INTO `gp_detalle_pago`
            ( `detalle_factura`, `valor`,
            `iva`,`impoconsumo`,
            `ajuste_peso`,`saldo_credito`,
            `pago`)
            VALUES (:detalle_factura, :valor,
            :iva, :impoconsumo,
            :ajuste_peso,:saldo_credito,
            :pago)";
            $sql_dato = array(
                array(":detalle_factura",$da[0][0]),
                array(":valor",$diferen),
                array(":iva",0),
                array(":impoconsumo",0),
                array(":ajuste_peso",0),
                array(":saldo_credito",0),
                array(":pago",$pago),

            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        if(!empty($tf[0][0])){
            $upd = $sql_cons ="UPDATE `gp_detalle_pago`
                SET valor_conversion=ROUND((valor/$trmA),2), 
                `valor_trm`=:valor_trm  
                WHERE `pago`=:pago ";
                $sql_dato = array(
                    array(":valor_trm",$trmA),
                    array(":pago",$pago)
                );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        echo $rta;
    break;
    #****** Guardar Detalles Recaudo Por cliente***#
    case 25:
        # *** Captura Variables *** #
        $fact    = $_REQUEST['facturas'];
        $cou     = count(explode(",", $fact));
        $facturas= explode(",", $fact);
        $pago    = $_REQUEST['pago'];
        $valor   = $_REQUEST['valor'];
        for ($i = 0; $i < ($cou); $i++) {
            $factura = $facturas[$i];
            $rta = guardarPagoFactura('',$factura,$pago, $valor);
            # Buscar Valor De La Factura Registrada
            $sld    = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura WHERE factura = $factura");
            $sld    = $sld[0][0];
            $valor -= $sld;
        }
        $idCnt          = $_REQUEST['cnt'];
        $idPptal        = $_REQUEST['pptal'];
        $idPago         = $_REQUEST['pago'];
        $id_causacion   = $_REQUEST['causacion'];
        $recon = 0;
        $reg = registrarDetallesPago($idPago,$idCnt,$idPptal,$id_causacion);
        if($reg==true){
            #** Actualizar Tabla Recaudo Cliente ** #
            $upd = $sql_cons ="UPDATE `gp_recaudos_cliente`
            SET `facturas`=:facturas
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":facturas",$fact),
                array(":id_unico",$_REQUEST['rcliente'])
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
           $rta=true;
        } else {
            $rta = false;
        }
        echo $rta;
    break;
    #****** Validar Configuración De Los Conceptos De Facturación Recaudo Cliente*****#
    case 26:
        $fact    = $_REQUEST['facturas'];
        $pago    = $_REQUEST['pago'];
        #** Fecha Pago **#
        $ff      = $con->Listar("SELECT fecha_pago FROM gp_pago WHERE id_unico = $pago");
        $fecha_pago = $ff[0][0];
        $cou     = count(explode(",", $fact));
        $facturas= explode(",", $fact);
        $num     = 0;
        $html    = "";
        $rta     = 0;
        for ($i = 1; $i < ($cou); $i++) {
            $factura    = $facturas[$i];
            #** Buscar Fecha Factura ** #
            $ff = $con->Listar("SELECT fecha_factura FROM gf_factura WHERE id_unico = $factura");
            $fecha_factura = $ff[0][0];
            $concept    = validarConfiguracion($factura, $fecha_factura, $fecha_pago);
            $count      = count(explode(",", $concept));
            if($count>1){
                $rta = 1;
            } else {
                $rta = 0;
            }
        }

    break;
    
    #************** Reconstruir Recaudo Facturación ********#
    case 27:
        #********Buscar Facturas Entre fechas *******#
        $fechaI         = $_POST['fechaI'];
        $fechaF         = $_POST['fechaF'];
        $fechaI         = fechaC($fechaI);
        $fechaF         = fechaC($fechaF);
        #** Facturas Automaticas
        $rowfc          = $con->Listar("SELECT DISTINCT f.id_unico, tr.id_unico, 
            tc.id_unico, tc.comprobante_pptal, tc.tipo_comp_hom , tr.cuenta_bancaria, 
            f.vendedor , f.fecha_factura  
        FROM gp_factura f 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
        LEFT JOIN gp_tipo_pago tr ON tf.tipo_recaudo = tr.id_unico 
        LEFT JOIN gf_tipo_comprobante tc ON tr.tipo_comprobante = tc.id_unico 
        WHERE tf.automatico = 1 
        AND f.fecha_factura BETWEEN '$fechaI' AND '$fechaF' 
        AND f.parametrizacionanno = $panno ");
        
        for ($f = 0; $f < count($rowfc); $f++) {
            $factura = $rowfc[$f][0];
            #*** Reconstruir Contabilidad factura ****#
            #1. Verificar si el tipo de factura tiene comprobante asociado
            $tfca = $con->Listar("SELECT tf.tipo_comprobante  
                FROM gp_factura f  
                LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
                WHERE f.id_unico =$factura");
            if(count($tfca)>0){
                if(!empty($tfca[0][0])){
                    #*** Verificar Si Tiene Comprobante Cnt Asociado ***#
                    $rowcna = $con->Listar("SELECT DISTINCT cn.id_unico 
                    FROM gf_detalle_comprobante dc 
                    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                    LEFT JOIN gp_detalle_factura df ON dc.id_unico = df.detallecomprobante 
                    LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                    WHERE f.id_unico = $factura");
                    if(count($rowcna)>0){
                        #** Verificar Si el Comprobante está descuadrado **#
                        $rowcd = $con->Listar("SELECT DISTINCT 
                                    cn.id_unico,
                                    cn.numero,
                                    tc.sigla,
                                    tc.nombre,
                                    date_format(cn.fecha,'%d/%m/%Y'),
                                    (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0) AS debito1,
                                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 ) AS credito2,
                                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0) AS credito, 
                                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0) AS debito2  
                                FROM
                                gf_comprobante_cnt cn 
                                LEFT JOIN
                                gf_tipo_comprobante tc
                                ON
                                cn.tipocomprobante = tc.id_unico  
                                WHERE 
                                cn.id_unico=".$rowcna[0][0]);
                        $debito1    = $rowcd[0][5];
                        $debitoN    = $rowcd[0][8]*-1;
                        $credito1   = $rowcd[0][7];
                        $creditoN   = $rowcd[0][6]*-1;
                        $debito     = $debito1+$debitoN;
                        $credito    = $credito1+$creditoN;

                        $diferencia = ROUND(($debito -$credito),2);

                        if($diferencia != '0' || $diferencia !='-0' || $diferencia != "") {
                            $sql_cons ="UPDATE  `gp_detalle_factura`
                            SET `detallecomprobante` =:detallecomprobante 
                            WHERE `factura`=:factura ";
                            $sql_dato = array(
                                array(":detallecomprobante",NULL),
                                array(":factura",$factura),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $sql_cons ="UPDATE  `gf_detalle_comprobante`
                            SET `detalleafectado` =:detalleafectado 
                            WHERE `comprobante`=:comprobante ";
                            $sql_dato = array(
                                array(":detalleafectado",NULL),
                                array(":comprobante",$rowcna[0][0]),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            eliminardetallescnt($rowcna[0][0]);
                            reconstruirComprobantesFactura($factura);
                        }
                    } else {
                        $sql_cons ="UPDATE  `gp_detalle_factura`
                        SET `detallecomprobante` =:detallecomprobante 
                        WHERE `factura`=:factura ";
                        $sql_dato = array(
                            array(":detallecomprobante",NULL),
                            array(":factura",$factura),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        $reconstrur = reconstruirComprobantesFactura($factura);
                        
                    }
                    
                }
            }
            
            #*****************************************#
            $usuario_c = $rowfc[$f][6];
            $saldof  = saldoFactura($factura);
            $tipoPago = $rowfc[$f][1];
            $banco    = $rowfc[$f][5];
            if(round($saldof)>0){
                #*** Buscar Pago ***#
                $rowdp = $con->Listar("SELECT DISTINCT p.id_unico, SUM(dp.valor+dp.iva+dp.impoconsumo+dp.ajuste_peso)
                FROM gp_pago p 
                LEFT JOIN gp_detalle_pago dp ON dp.pago = p.id_unico 
                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                WHERE f.id_unico = $factura 
                GROUP BY p.id_unico");
                
                if(!empty($tipoPago) && !empty($banco)){ 
                    if(count($rowdp)>0){
                        $pago = $rowdp[0][0];
                        eliminarPago($pago);
                    } else {
                        #Buscar Datos Factura
                        $df = $con->Listar("SELECT f.id_unico,
                                        f.numero_factura, tp.nombre,
                                        f.tercero, f.descripcion, f.fecha_factura, f.centrocosto
                                    FROM gp_factura f LEFT JOIN gp_tipo_factura tp ON tp.id_unico = f.tipofactura
                                    WHERE f.id_unico = $factura");
                        $fecha      = $df[0][5];
                        $responsable= $df[0][3];
                        $centrocosto      = $df[0][6];
                        #Calcular Numero Pago
                        $fac = $con->Listar("SELECT * FROM gp_pago WHERE tipo_pago = $tipoPago AND parametrizacionanno = $panno");
                        if(count($fac)>0){
                            $sql = $con->Listar("SELECT MAX(numero_pago)  FROM gp_pago WHERE tipo_pago = $tipoPago AND parametrizacionanno = $panno");
                            $numeroPago = $sql[0][0] + 1;
                        } else {
                            $numeroPago = $anno. '000001';
                        }
                        $estado = 1;
                        $sql = "INSERT INTO gp_pago
                                (numero_pago,
                                tipo_pago,
                                responsable,
                                fecha_pago,
                                banco,
                                estado, parametrizacionanno, usuario)
                                VALUES('$numeroPago',
                                $tipoPago,$responsable,
                                '$fecha',$banco,$estado, $panno, $usuario_c)";
                        $resultadoP = $mysqli->query($sql);

                        if($resultadoP==true){
                            #********* Buscar el Registro Pago Realizado **************#
                            $idPago = $con->Listar("SELECT MAX(id_unico) FROM gp_pago WHERE numero_pago=$numeroPago AND tipo_pago=$tipoPago");
                            $pago = $idPago[0][0];
                            #************ Registrar Comprobante CNT***************#
                            $tipoComprobanteCnt = $con->Listar("select tipo_comprobante from gp_tipo_pago where id_unico=$tipoPago");
                            if(!empty($tipoComprobanteCnt[0][0])){
                                #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                                $tipocnt =$tipoComprobanteCnt[0][0];
                                $numeroC=$numeroPago;
                                #Descripción del comprobante
                                $descripcion= '"Comprobante de recaudo factura N° '.$df[0][1].' '.$df[0][4].'"';
                                #Insertamos el comprobante
                                $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                                        . "values('$numeroC','$fecha',$descripcion,$tipocnt,$panno,$responsable,'1',$compania)";
                                $resultInsertC=$mysqli->query($sqlInsertC);
                                #Consultamos el ultimo comprobante ingresado
                                $idCnt=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocnt and numero=$numeroC");
                                $id_cnt = $idCnt[0][0];

                                #*********** Comprobante Pptal ***********#
                                #Validamos que el tipo de comprobante cnt contenga asocidado un tipo de comprobante cnt o el campo comprobante_pptal no este vacio
                                $tipoComPtal=$con->Listar("select comprobante_pptal from gf_tipo_comprobante where id_unico=$tipocnt");
                                #Validamos que el tipo de comprobante no venga vacio
                                if(!empty($tipoComPtal[0][0])){
                                    $tipopptal = $tipoComPtal[0][0];
                                    $numeroPp=$numeroPago;
                                    #Insertamos los datos en comprobante pptal
                                    $insertPptal="insert into "
                                            . "gf_comprobante_pptal(numero,fecha,fechavencimiento,descripcion,parametrizacionanno,tipocomprobante,tercero,estado,responsable) "
                                            . "values('$numeroPp','$fecha','$fecha',$descripcion,$panno,$tipopptal,$responsable,'1',$responsable)";
                                    $resultInsertPptal=$mysqli->query($insertPptal);
                                    #Consultamos el ultimo comprobante pptal insertado
                                    $idPPAL=$con->Listar("select id_unico from gf_comprobante_pptal where tipocomprobante=$tipopptal and numero=$numeroPp");
                                    $id_pptal = $idPPAL[0][0];
                                }
                                #************ Registrar Comprobante Causación***************#
                                $tipoComprobanteC=$con->Listar("select tipo_comp_hom from gf_tipo_comprobante where id_unico=".$tipoComprobanteCnt[0][0]);
                                if(!empty($tipoComprobanteC[0][0])){
                                    #Consultamos el ultimo numero de acuerdo al tipo de comprobante
                                    $tipocau =$tipoComprobanteC[0][0];
                                    $numeroCausacion=$numeroPago;
                                    #Descripción del comprobante
                                    $descripcion= '"Comprobante de causación recaudo factura N° '.$df[0][1].' '.$df[0][4].'"';
                                    #Insertamos el comprobante
                                    $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                                            . "values('$numeroCausacion','$fecha',$descripcion,$tipocau,$panno,$responsable,'1',$compania)";
                                    $resultInsertC=$mysqli->query($sqlInsertC);
                                    #Consultamos el ultimo comprobante ingresado
                                    $idCau=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocau and numero=$numeroCausacion");
                                    $id_causacion = $idCau[0][0];

                                }
                            }
                        }
                    }
                }
                
                # Buscar4 Valor Factura
                $vf = $con->Listar("SELECT SUM(valor_total_ajustado) FROM gp_detalle_factura
                WHERE factura = $factura ");
                $valor = $vf[0][0];
                $dp = guardarPagoFactura('',$factura,$pago, $valor);
                if($dp>0){
                    if (empty($id_cnt)){
                        $id_cnt =0;
                    }
                    if (empty($id_pptal)){
                        $id_pptal =0;
                    }
                    if (empty($id_causacion)){
                        $id_causacion =0;
                    }
                    $reg=registrarDetallesPago($pago,$id_cnt,$id_pptal,$id_causacion);
                }
                #********** Actualizar Estado Factura *************#
                $sql = "UPDATE gp_factura 
                    SET estado = 5 
                    WHERE id_unico=$factura";
                $resultadoP = $mysqli->query($sql);
            }
            else {
                #*** Buscar Pago ***#
                $rowdp = $con->Listar("SELECT DISTINCT p.id_unico, SUM(dp.valor+dp.iva+dp.impoconsumo+dp.ajuste_peso) 
                FROM gp_pago p 
                LEFT JOIN gp_detalle_pago dp ON dp.pago = p.id_unico 
                LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
                LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                WHERE f.id_unico = $factura 
                GROUP BY p.id_unico");
                for ($p = 0; $p < count($rowdp); $p++) {
                    $id_pagoF = $rowdp[$p][0];
                    #** Buscar Contabilidad del Pago **#
                    $cnt    = 0;
                    $pptal  = 0;
                    $id_causacion =0;
                    $row = $con->Listar("SELECT cn.id_unico as cnt, cp.id_unico as ptal
                            FROM gp_pago p
                            LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
                            LEFT JOIN gf_detalle_comprobante dc ON dc.id_unico = dp.detallecomprobante
                            LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico
                            LEFT JOIN gf_detalle_comprobante_pptal dpt ON dc.detallecomprobantepptal = dpt.id_unico
                            LEFT JOIN gf_comprobante_pptal cp ON dpt.comprobantepptal = cp.id_unico
                            WHERE p.id_unico = $id_pagoF");
                    if(count($row)>0){
                        $cnt    = $row[0][0];
                        $pptal  = $row[0][1];
                    } else {
                        #Buscar Por Número Y Tipo
                        $row2 = $con->Listar("SELECT cn.id_unico as cnt, cp.id_unico as ptal
                            FROM gp_pago p
                            LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
                            LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico
                            LEFT JOIN gf_tipo_comprobante tc ON tp.tipo_comprobante = tc.id_unico
                            LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico AND cn.numero = p.numero_pago
                            LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = p.numero_pago
                            WHERE p.id_unico =$id_pagoF");
                        if(count($row2)>0){
                            $cnt    = $row2[0][0];
                            $pptal  = $row2[0][1];
                        }

                    }
                    #*** Buscar Si Tiene Causación
                    $cs = causacion($cnt);
                    if(!empty($cs[0][0])){
                        $id_causacion =$cs;
                    }
                    
                    #** Verificar Si el Comprobante está descuadrado **#
                    $rowcd = $con->Listar("SELECT DISTINCT 
                                cn.id_unico,
                                cn.numero,
                                tc.sigla,
                                tc.nombre,
                                date_format(cn.fecha,'%d/%m/%Y'),
                                (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                 WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0) AS debito1,
                                 (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                 WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 ) AS credito2,
                                 (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                 WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0) AS credito, 
                                 (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                 WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0) AS debito2  
                            FROM
                            gf_comprobante_cnt cn 
                            LEFT JOIN
                            gf_tipo_comprobante tc
                            ON
                            cn.tipocomprobante = tc.id_unico  
                            WHERE 
                            cn.id_unico=".$cnt);
                    $debito1    = $rowcd[0][5];
                    $debitoN    = $rowcd[0][8]*-1;
                    $credito1   = $rowcd[0][7];
                    $creditoN   = $rowcd[0][6]*-1;
                    $debito     = $debito1+$debitoN;
                    $credito    = $credito1+$creditoN;

                    $diferencia = ROUND(($debito -$credito),2);

                    if($diferencia != '0' || $diferencia !='-0' || $diferencia != "") {
                        $upd = $sql_cons ="UPDATE `gp_detalle_pago`
                            SET `detallecomprobante`=:detallecomprobante
                            WHERE `pago`=:pago ";
                            $sql_dato = array(
                                array(":detallecomprobante",NULL),
                                array(":pago",$id_pagoF)
                            );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        eliminardetallescnt($id_causacion);
                        eliminardetallescnt($cnt);
                        eliminarDetallesRetencion($cnt);
                        eliminardetallespptal($pptal);
                        $reg=registrarDetallesPago($id_pagoF,$cnt,$pptal,$id_causacion);
                    } elseif(count($rowcd)<=0){
                        $upd = $sql_cons ="UPDATE `gp_detalle_pago`
                            SET `detallecomprobante`=:detallecomprobante
                            WHERE `pago`=:pago ";
                            $sql_dato = array(
                                array(":detallecomprobante",NULL),
                                array(":pago",$id_pagoF)
                            );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        eliminardetallescnt($id_causacion);
                        eliminardetallescnt($cnt);
                        eliminarDetallesRetencion($cnt);
                        eliminardetallespptal($pptal);
                        $reg=registrarDetallesPago($id_pagoF,$cnt,$pptal,$id_causacion);
                    }
                }
                
            }
        }
        
        #** No Automaticas
        $rowfc          = $con->Listar("SELECT DISTINCT f.id_unico, tr.id_unico, 
            tc.id_unico, tc.comprobante_pptal, tc.tipo_comp_hom , tr.cuenta_bancaria, 
            f.vendedor , f.fecha_factura  
        FROM gp_factura f 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
        LEFT JOIN gp_tipo_pago tr ON tf.tipo_recaudo = tr.id_unico 
        LEFT JOIN gf_tipo_comprobante tc ON tr.tipo_comprobante = tc.id_unico 
        WHERE (tf.automatico != 1  OR tf.automatico IS NULL) 
        AND f.fecha_factura BETWEEN '$fechaI' AND '$fechaF' 
        AND f.parametrizacionanno = $panno");
        
        for ($f = 0; $f < count($rowfc); $f++) {
            $factura = $rowfc[$f][0];
            #*** Reconstruir Contabilidad factura ****#
            #1. Verificar si el tipo de factura tiene comprobante asociado
            $tfca = $con->Listar("SELECT tf.tipo_comprobante  
                FROM gp_factura f  
                LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
                WHERE f.id_unico =$factura");
            if(count($tfca)>0){
                if(!empty($tfca[0][0])){
                    #*** Verificar Si Tiene Comprobante Cnt Asociado ***#
                    $rowcna = $con->Listar("SELECT DISTINCT cn.id_unico 
                    FROM gf_detalle_comprobante dc 
                    LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico 
                    LEFT JOIN gp_detalle_factura df ON dc.id_unico = df.detallecomprobante 
                    LEFT JOIN gp_factura f ON df.factura = f.id_unico 
                    WHERE f.id_unico = $factura");
                    if(count($rowcna)>0){
                        #** Verificar Si el Comprobante está descuadrado **#
                        $rowcd = $con->Listar("SELECT DISTINCT 
                                    cn.id_unico,
                                    cn.numero,
                                    tc.sigla,
                                    tc.nombre,
                                    date_format(cn.fecha,'%d/%m/%Y'),
                                    (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0) AS debito1,
                                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 ) AS credito2,
                                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0) AS credito, 
                                     (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                                     WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0) AS debito2  
                                FROM
                                gf_comprobante_cnt cn 
                                LEFT JOIN
                                gf_tipo_comprobante tc
                                ON
                                cn.tipocomprobante = tc.id_unico  
                                WHERE 
                                cn.id_unico=".$rowcna[0][0]);
                        $debito1    = $rowcd[0][5];
                        $debitoN    = $rowcd[0][8]*-1;
                        $credito1   = $rowcd[0][7];
                        $creditoN   = $rowcd[0][6]*-1;
                        $debito     = $debito1+$debitoN;
                        $credito    = $credito1+$creditoN;

                        $diferencia = ROUND(($debito -$credito),2);

                        if($diferencia != '0' || $diferencia !='-0' || $diferencia != "") {
                            $sql_cons ="UPDATE  `gp_detalle_factura`
                            SET `detallecomprobante` =:detallecomprobante 
                            WHERE `factura`=:factura ";
                            $sql_dato = array(
                                array(":detallecomprobante",NULL),
                                array(":factura",$factura),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            $sql_cons ="UPDATE  `gf_detalle_comprobante`
                            SET `detalleafectado` =:detalleafectado 
                            WHERE `comprobante`=:comprobante ";
                            $sql_dato = array(
                                array(":detalleafectado",NULL),
                                array(":comprobante",$rowcna[0][0]),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            eliminardetallescnt($rowcna[0][0]);
                            reconstruirComprobantesFactura($factura);
                        }
                    } else {
                        $sql_cons ="UPDATE  `gp_detalle_factura`
                        SET `detallecomprobante` =:detallecomprobante 
                        WHERE `factura`=:factura ";
                        $sql_dato = array(
                            array(":detallecomprobante",NULL),
                            array(":factura",$factura),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                        reconstruirComprobantesFactura($factura);
                    }
                    
                }
            }
            
            #*****************************************#
            $usuario_c = $rowfc[$f][6];
            $saldof  = saldoFactura($factura);
            $tipoPago = $rowfc[$f][1];
            $banco    = $rowfc[$f][5];
            #*** Buscar Pago ***#
            $rowdp = $con->Listar("SELECT DISTINCT p.id_unico, SUM(dp.valor+dp.iva+dp.impoconsumo+dp.ajuste_peso) 
            FROM gp_pago p 
            LEFT JOIN gp_detalle_pago dp ON dp.pago = p.id_unico 
            LEFT JOIN gp_detalle_factura df ON dp.detalle_factura = df.id_unico 
            LEFT JOIN gp_factura f ON df.factura = f.id_unico 
            WHERE f.id_unico = $factura 
            GROUP BY p.id_unico");
            for ($p = 0; $p < count($rowdp); $p++) {
                $id_pagoF = $rowdp[$p][0];
                #** Buscar Contabilidad del Pago **#
                $cnt    = 0;
                $pptal  = 0;
                $id_causacion =0;
                $row = $con->Listar("SELECT cn.id_unico as cnt, cp.id_unico as ptal
                        FROM gp_pago p
                        LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
                        LEFT JOIN gf_detalle_comprobante dc ON dc.id_unico = dp.detallecomprobante
                        LEFT JOIN gf_comprobante_cnt cn ON dc.comprobante = cn.id_unico
                        LEFT JOIN gf_detalle_comprobante_pptal dpt ON dc.detallecomprobantepptal = dpt.id_unico
                        LEFT JOIN gf_comprobante_pptal cp ON dpt.comprobantepptal = cp.id_unico
                        WHERE p.id_unico = $id_pagoF");
                if(count($row)>0){
                    $cnt    = $row[0][0];
                    $pptal  = $row[0][1];
                } else {
                    #Buscar Por Número Y Tipo
                    $row2 = $con->Listar("SELECT cn.id_unico as cnt, cp.id_unico as ptal
                        FROM gp_pago p
                        LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
                        LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico
                        LEFT JOIN gf_tipo_comprobante tc ON tp.tipo_comprobante = tc.id_unico
                        LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico AND cn.numero = p.numero_pago
                        LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = p.numero_pago
                        WHERE p.id_unico =$id_pagoF");
                    if(count($row2)>0){
                        $cnt    = $row2[0][0];
                        $pptal  = $row2[0][1];
                    }

                }
                #*** Buscar Si Tiene Causación
                $cs = causacion($cnt);
                if(!empty($cs[0][0])){
                    $id_causacion =$cs;
                }

                #** Verificar Si el Comprobante está descuadrado **#
                $rowcd = $con->Listar("SELECT DISTINCT 
                            cn.id_unico,
                            cn.numero,
                            tc.sigla,
                            tc.nombre,
                            date_format(cn.fecha,'%d/%m/%Y'),
                            (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND  dc1.valor>0) AS debito1,
                             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=1 AND dc1.valor<0 ) AS credito2,
                             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor>0) AS credito, 
                             (SELECT ROUND(SUM(dc1.valor),2) FROM gf_detalle_comprobante dc1 LEFT JOIN gf_cuenta c1 ON dc1.cuenta = c1.id_unico 
                             WHERE cn.id_unico = dc1.comprobante AND c1.naturaleza=2 AND dc1.valor<0) AS debito2  
                        FROM
                        gf_comprobante_cnt cn 
                        LEFT JOIN
                        gf_tipo_comprobante tc
                        ON
                        cn.tipocomprobante = tc.id_unico  
                        WHERE 
                        cn.id_unico=".$cnt);
                $debito1    = $rowcd[0][5];
                $debitoN    = $rowcd[0][8]*-1;
                $credito1   = $rowcd[0][7];
                $creditoN   = $rowcd[0][6]*-1;
                $debito     = $debito1+$debitoN;
                $credito    = $credito1+$creditoN;

                $diferencia = ROUND(($debito -$credito),2);

                if($diferencia != '0' || $diferencia !='-0' || $diferencia != "") {
                    
                    $upd = $sql_cons ="UPDATE `gp_detalle_pago`
                        SET `detallecomprobante`=:detallecomprobante
                        WHERE `pago`=:pago ";
                        $sql_dato = array(
                            array(":detallecomprobante",NULL),
                            array(":pago",$id_pagoF)
                        );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    eliminardetallescnt($id_causacion);
                    eliminardetallescnt($cnt);
                    eliminarDetallesRetencion($cnt);
                    eliminardetallespptal($pptal);
                    $reg=registrarDetallesPago($id_pagoF,$cnt,$pptal,$id_causacion);
                    
                }elseif(count($rowcd)<=0){
                    $upd = $sql_cons ="UPDATE `gp_detalle_pago`
                        SET `detallecomprobante`=:detallecomprobante
                        WHERE `pago`=:pago ";
                        $sql_dato = array(
                            array(":detallecomprobante",NULL),
                            array(":pago",$id_pagoF)
                        );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                    eliminardetallescnt($id_causacion);
                    eliminardetallescnt($cnt);
                    eliminarDetallesRetencion($cnt);
                    eliminardetallespptal($pptal);
                    $reg=registrarDetallesPago($id_pagoF,$cnt,$pptal,$id_causacion);
                }
                
            }
        }
        
        
        echo 1;
    break;
    
    #************** Buscar Recaudos Por Mes ********#
    case 28:
        $tipo = $_REQUEST['tipo'];
        $mes  = $_REQUEST['mes'];
        $sqlB = "SELECT     pg.id_unico,
            pg.numero_pago,
            tpg.nombre,
            IF(CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)
            IS NULL OR CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos) = '',
            (tr.razonsocial),
            CONCAT_WS(' ',
            tr.nombreuno,
            tr.nombredos,
            tr.apellidouno,
            tr.apellidodos)) AS NOMBRE,
            tr.numeroidentificacion ,
            DATE_FORMAT(DATE_FORMAT(pg.fecha_pago, '%d/%m/%Y'), '%d/%m/%Y')
        FROM        gp_pago pg
        LEFT JOIN   gp_tipo_pago tpg    ON tpg.id_unico = pg.tipo_pago
        LEFT JOIN   gf_tercero tr      ON tr.id_unico = pg.responsable
        LEFT JOIN   gf_tipo_identificacion ti   ON ti.id_unico = tr.tipoidentificacion
        WHERE pg.parametrizacionanno = $panno AND tpg.id_unico = $tipo 
        AND MONTH(pg.fecha_pago) ='$mes' 
        ORDER BY    pg.numero_pago DESC";
        $resultB = $mysqli->query($sqlB);
        if(mysqli_num_rows($resultB)>0){
            echo '<option value="">Recaudos</option>';
            while ($rowB = mysqli_fetch_row($resultB)) {
                $sqlVal = " SELECT  SUM(valor)
                        FROM    gp_detalle_pago
                        WHERE   pago = $rowB[0]";
                $resultVal = $mysqli->query($sqlVal);
                $val = mysqli_fetch_row($resultVal);
                echo "<option value=".$rowB[0].">".$rowB[1].' '.mb_strtoupper($rowB[2]).' '.$rowB[5].' '.ucwords(mb_strtolower($rowB[3])).' - '.$rowB[4].' '."$".number_format($val[0],2,',','.')."</option>";
            }
        } else {
            echo '<option value="">No Hay Recaudos</option>';
        }
    break;
    # **** Buscar Factura  Por Mes *****#
    case 29:
        $tipo = $_REQUEST['tipo'];
        $mes  = $_REQUEST['mes'];
        echo "<option value>Buscar Factura</option>";
        $sqlB = "SELECT     fat.id_unico,
                    fat.numero_factura,
                    tpf.prefijo,
                    IF(CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))='' OR CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos)) IS NULL ,(ter.razonsocial),                                            CONCAT(IF(ter.nombreuno='','',ter.nombreuno),' ',IF(ter.nombredos IS NULL,'',ter.nombredos),' ',IF(ter.apellidouno IS NULL,'',IF(ter.apellidouno IS NULL,'',ter.apellidouno)),' ',IF(ter.apellidodos IS NULL,'',ter.apellidodos))) AS 'NOMBRE',
                    CONCAT(ter.numeroidentificacion) AS 'TipoD', 
                    DATE_FORMAT(fat.fecha_factura, '%d/%m/%Y')
        FROM        gp_factura fat
        LEFT JOIN   gp_tipo_factura tpf ON tpf.id_unico = fat.tipofactura
        LEFT JOIN   gf_tercero ter ON ter.id_unico = fat.tercero
        LEFT JOIN   gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion 
        WHERE fat.parametrizacionanno = $panno AND fat.tipofactura = $tipo 
        AND MONTH(fat.fecha_factura)='$mes'  
        ORDER BY numero_factura  DESC ";
        $resultB = $mysqli->query($sqlB);
        while ($rowB = mysqli_fetch_row($resultB)) {
            $sqlDF = "SELECT SUM(dtf.valor) FROM gp_detalle_factura dtf WHERE factura = $rowB[0]";
            $resultDF = $mysqli->query($sqlDF);
            $valDF = mysqli_fetch_row($resultDF);
            echo "<option value=".$rowB[0].">".$rowB[1]." ".$rowB[2]." ".$rowB[5]." ".ucwords(mb_strtolower($rowB[3]))." - ".ucwords(mb_strtolower($rowB[4]))." "."$".number_format($valDF[0],2,'.',',')."</option>";
        }
    break;
    
    #******************Configuración Facturación **************#
    #* Guardar *#
    case 30:
        
        $sql_cons ="INSERT INTO `gf_configuracion_facturacion`
        ( `tipo_factura`, `concepto`,
        `concepto_asociado`,`entidad`,
        `cuenta_bancaria`,`principal`,`validacion`)
        VALUES (:tipo_factura, :concepto,
        :concepto_asociado, :entidad,
        :cuenta_bancaria,:principal, :validacion)";
        $sql_dato = array(
            array(":tipo_factura",$_REQUEST['tipo_factura']),
            array(":concepto",$_REQUEST['concepto']),
            array(":concepto_asociado",$_REQUEST['conceptoa']),
            array(":entidad",$_REQUEST['entidad']),
            array(":cuenta_bancaria",$_REQUEST['cuentab']),
            array(":principal",$_REQUEST['principal']),
            array(":validacion",$_REQUEST['validacion']),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        //var_dump($resp);
        if(empty($resp)){
            echo 0;
        } else {
            echo 2;
        }
    break;
    #* Eliminar *#
    case 31:
        $id     = $_REQUEST['id'];
        $sql_cons ="DELETE FROM  `gf_configuracion_facturacion`
        WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":id_unico",$id)
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta =0;
        } else {
            $rta =2;
        }
        echo $rta;
        
    break;
    
    #****************************DETALLES **********************"
    case 32:
        #** Buscar Si el tipo de factura y el concepto tienen configuración
        $concepto         = $_POST['sltConcepto'];
        $factura          = $_POST['txtIdFactura'];
        $iva              = $_POST['txtIva'];
        $impoconsumo      = $_POST['txtImpoconsumo'];
        $ajustePeso       = $_POST['txtAjustePeso'];
        $valorTotalAjuste = $_POST['txtValorA'];
        $tercero          = $_POST['txtTercero'];
        $centrocosto      = $_POST['txtCentroCosto'];
        $descripcion      = $_POST['txtDescr'];
        $proyecto         = '2147483647';
        $fecha            = $_POST['txtFecha'];
        @session_start();
        if($_SESSION['tipo_compania']==2){
            $valor    = $_POST['txtValorX'];
            if(!empty($_POST['txtXDescuento'])){
                $descuento        = $_POST['txtXDescuento'];
            } else {
                $descuento =0;
            }
        }else {
            if (!empty($_POST['sltValor'])) {
                $div = explode("/", $_POST['sltValor']);
                $valor = $div[0];
            } else {
                $valor = $_POST['txtValor'];
            }            
        }
        

        if (empty($_POST['txtCantidad'])) {
            $cantidad = 1;
        } else {
            $cantidad = $_POST['txtCantidad'];
        }

        list($id_detalle_ptal, $id_pptal, $id_cnt, $detalleD, $detalleC, $detalle_i, $detalle_x, $detalle_y)
            = array("NULL", 0, 0, "NULL", "NULL", "NULL", "NULL", "NULL");
        $r = 0;
        #****** Guardar Concepto En Factura ******#
        $sql_cons ="INSERT INTO `gp_detalle_factura` 
                ( `factura`, `concepto_tarifa`, `valor`,
            `cantidad`,`iva`,`impoconsumo`,
            `ajuste_peso`,`valor_total_ajustado`) 
        VALUES  (:factura,  :concepto_tarifa, :valor, 
            :cantidad,:iva,:impoconsumo,
            :ajuste_peso,:valor_total_ajustado)";
        $sql_dato = array(
                array(":factura",$factura),
                array(":concepto_tarifa",$concepto),
                array(":valor",$valor),
                array(":cantidad",$cantidad),
                array(":iva",$iva),
                array(":impoconsumo",$impoconsumo),
                array(":ajuste_peso",$ajustePeso),
                array(":valor_total_ajustado",$valorTotalAjuste),
        );
        $resp       = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $bd = $con->Listar("SELECT MAX(id_unico) FROM gp_detalle_factura 
                WHERE factura = $factura AND concepto_tarifa= $concepto ");
            $id_detalle = $bd[0][0];
            $c = reconstruirComprobantesFacturaDetalle($factura, $id_detalle);
            
        }
        $str = $con->Listar("SELECT cf.concepto FROM gf_configuracion_facturacion cf 
                    LEFT JOIN gp_factura f ON cf.tipo_factura = f.tipofactura 
                    WHERE f.id_unico = $factura 
                    AND (cf.concepto_asociado = $concepto)");
        if(!empty($str[0][0])){
            for ($c = 0; $c < count($str); $c++) {
                $concepto = $str[$c][0];            
                #** Buscar tarifa 
                $tr = $con->Listar("SELECT ct.id_unico, t.valor, t.porcentaje_iva, t.porcentaje_impoconsumo  
                    FROM gp_concepto_tarifa ct 
                    LEFT JOIN gp_tarifa t ON ct.tarifa = t.id_unico 
                    WHERE ct.concepto = $concepto 
                        AND ct.parametrizacionanno = $panno");
                if(!empty($tr[0][0])){
                    $valor              = $tr[0][1];
                    $valorTotalAjuste   = $cantidad * $valor;
                    $iva                = 0;
                    $impoconsumo        = 0;
                    $ajustePeso         = 0;
                    $sql_cons ="INSERT INTO `gp_detalle_factura` 
                            ( `factura`, `concepto_tarifa`, `valor`,
                        `cantidad`,`iva`,`impoconsumo`,
                        `ajuste_peso`,`valor_total_ajustado`) 
                    VALUES  (:factura,  :concepto_tarifa, :valor, 
                        :cantidad,:iva,:impoconsumo,
                        :ajuste_peso,:valor_total_ajustado)";
                    $sql_dato = array(
                            array(":factura",$factura),
                            array(":concepto_tarifa",$concepto),
                            array(":valor",$valor),
                            array(":cantidad",$cantidad),
                            array(":iva",$iva),
                            array(":impoconsumo",$impoconsumo),
                            array(":ajuste_peso",$ajustePeso),
                            array(":valor_total_ajustado",$valorTotalAjuste),
                    );
                    $resp       = $con->InAcEl($sql_cons,$sql_dato);
                    if(empty($resp)){
                        $bd = $con->Listar("SELECT MAX(id_unico) FROM gp_detalle_factura 
                            WHERE factura = $factura AND concepto_tarifa= $concepto ");
                        $id_detalle = $bd[0][0];
                        reconstruirComprobantesFacturaDetalle($factura, $id_detalle);
                    }
                } 
            }
        }
        echo 1;
    break;
    
    #* Validar Si hay algún tipo de factura trm
    case 33:
        $row = $con->Listar("SELECT * FROM gp_tipo_factura WHERE tipo_cambio IS NOT NULL");
        if(!empty($row[0][0])){
            #Buscar si ya hay trm registrado fecha actual
            $trm = $con->Listar("SELECT * FROM gf_trm WHERE fecha ='".date('Y-m-d')."'");
            if(!empty($trm[0][0])){
                echo 0;
            } else {
                echo 1;
            }            
        } else {
            echo 0;
        }
    break;
    #* Validar Si hay algún tipo de factura trm
    case 34:
        $id_f = $_REQUEST['factura'];
        $row = $con->Listar("SELECT f.fecha_factura FROM gp_factura f 
        LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
        WHERE tf.tipo_cambio IS NOT NULL AND f.id_unico = $id_f");
        
        if(!empty($row[0][0])){
            #Buscar si ya hay trm registrado fecha actual
            $trm = $con->Listar("SELECT * FROM gf_trm WHERE fecha ='".$row[0][0]."'");
            if(!empty($trm[0][0])){
                echo 0;
            } else {
                echo 1;
            }            
        } else {
            echo 0;
        }
    break;
    #* Reconstruir Comprobantes 
    case 35:
        $rta = 0;
        $fechaI = fechaC($_REQUEST['fechaI']);
        $fechaF = fechaC($_REQUEST['fechaF']);
        #* Facturación
        if($_REQUEST['tipo']==1){
            $tipo_factura = $_REQUEST['tipof'];
            $dtf          = $con->Listar("SELECT tf.id_unico, tc.id_unico, tc.comprobante_pptal 
                FROM gp_tipo_factura tf 
                LEFT JOIN gf_tipo_comprobante tc ON tf.tipo_comprobante = tc.id_unico 
                WHERE tf.id_unico = $tipo_factura");
            if(!empty($dtf[0][1]) || !empty($dtf[0][2])){
                $row = $con->Listar("SELECT DISTINCT id_unico 
                    FROM gp_factura WHERE fecha_factura BETWEEN '$fechaI' and '$fechaF' 
                        and numero_factura >'2019012016'
                        and numero_factura <'2019012085'
                    AND tipofactura = $tipo_factura ");
                for ($i = 0; $i < count($row); $i++) {
                    $id_factura = $row[$i][0];
                    reconstruirComprobantesFactura($id_factura);
                    $rta +=1;
                }
            }
        #* Recaudo    
        } elseif($_REQUEST['tipo']==2){
            $tipo_pago    = $_REQUEST['tipoP'];
            $dtf          = $con->Listar("SELECT tf.id_unico, tc.id_unico, tc.comprobante_pptal 
                FROM gp_tipo_pago tf 
                LEFT JOIN gf_tipo_comprobante tc ON tf.tipo_comprobante = tc.id_unico 
                WHERE tf.id_unico = $tipo_pago");
            if(!empty($dtf[0][1]) || !empty($dtf[0][2])){
                $row = $con->Listar("SELECT DISTINCT id_unico, numero_pago, fecha_pago, responsable  
                    FROM gp_pago WHERE fecha_pago BETWEEN '$fechaI' and '$fechaF' 
                    AND tipo_pago = $tipo_pago ");
                for ($i = 0; $i < count($row); $i++) {
                    $id_pago    = $row[$i][0];
                    $id_cnt     = 0;
                    $id_pptal   = 0;
                     #************ Registrar Comprobante CNT***************#
                    if(!empty($dtf[0][1])){
                        $tipocnt    =   $dtf[0][1];
                        $numeroC    =   $row[$i][1];
                        #Descripción del comprobante
                        $descripcion= '"Comprobante de recaudo  N° '.$row[$i][1].'"';
                        #Insertamos el comprobante
                        $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,
                            tipocomprobante,parametrizacionanno,tercero,estado,compania) 
                            values('$numeroC','".$row[$i][2]."',$descripcion,$tipocnt,$panno,
                            ".$row[$i][3]." ,'1',$compania)";
                        $resultInsertC=$mysqli->query($sqlInsertC);
                        #Consultamos el ultimo comprobante ingresado
                        $idCnt=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocnt and numero=$numeroC");
                        $id_cnt = $idCnt[0][0];
                    }
                    if(!empty($dtf[0][2])){
                        $tipopptal  = $dtf[0][2];
                        $numeroPp   = $row[$i][1];
                        #Insertamos los datos en comprobante pptal
                        $insertPptal="insert into "
                                . "gf_comprobante_pptal(numero,fecha,fechavencimiento,"
                                . "descripcion,parametrizacionanno,tipocomprobante,tercero,estado,responsable) "
                                . "values('$numeroPp','".$row[$i][2]."','".$row[$i][2]."',"
                                . "$descripcion,$panno,$tipopptal,".$row[$i][3].",'1',".$row[$i][3].")";
                        $resultInsertPptal=$mysqli->query($insertPptal);
                        #Consultamos el ultimo comprobante pptal insertado
                        $idPPAL=$con->Listar("select id_unico from gf_comprobante_pptal where tipocomprobante=$tipopptal and numero=$numeroPp");
                        $id_pptal = $idPPAL[0][0];
                    }
                    var_dump($id_pago,$id_cnt,$id_pptal,0);
                    registrarDetallesPago($id_pago,$id_cnt,$id_pptal,0);
                    $rta +=1;
                }
            }
        }
        
        
        echo $rta;
    break;    
    #Fecha Vencimiento
    case 36:
        $fecha = fechaC($_REQUEST['fecha']);
        $dias  = $_REQUEST['dias'];
        $fechan =  sumar_dias($fecha, $dias);
        $fechav = trim(c_fecha($fechan));
        echo $fechav;
    break;
    #Recostruir Facturas Y Ppagos
    case 37:
        $fechaI = fechac($_REQUEST['fechaI']);
        $fechaF = fechac($_REQUEST['fechaF']);
        $tipo   = $_REQUEST['tipo'];
        IF($tipo==1){
            $tipofactura = $_REQUEST['tipof'];
            #BSCAR FACTURAS 
            $row = $con->Listar("SELECT DISTINCT id_unico FROM gp_factura 
                WHERE fecha_factura BETWEEN '$fechaI' AND '$fechaF'
                AND tipofactura= $tipofactura AND parametrizacionanno = $panno ");
            for ($i = 0; $i < count($row); $i++) {
                $id_factura = $row[$i][0];
                #** Buscar Detalles Factura
                $fc = $con->Listar("SELECT f.*, tc.id_unico as cnt, tcp.id_unico as pptal, 
                    tc.tipo_comp_hom as csc 
                    FROM gp_factura f 
                    LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
                    LEFT JOIN gf_tipo_comprobante tc ON tf.tipo_comprobante = tc.id_unico 
                    LEFT JOIN gf_tipo_comprobante_pptal tcp ON tc.comprobante_pptal = tcp.id_unico 
                    WHERE f.id_unico = $id_factura");
                $numero             = $fc[0]['numero_factura'];
                $tercero            = $fc[0]['tercero'];
                $fecha              = $fc[0]['fecha_factura'];
                $descripcion        = $fc[0]['descripcion'];
                $tipo               = $fc[0]['tipofactura'];
                $tipocnt            = $fc[0]['cnt'];
                $tipopptal          = $fc[0]['pptal'];
                $tipocausacion      = $fc[0]['csc'];
                #** Buscar si existe pptal
                $cnt        = "";
                $pptal      = "";
                $causacion  = "";
                if(!empty($tipopptal)){
                    #** Buscar Si existe **#
                    $idpp = $con->Listar("SELECT * FROM gf_comprobante_pptal 
                        WHERE tipocomprobante = $tipopptal AND numero = '$numero' 
                        AND parametrizacionanno = $panno");
                    if(count($idpp)>0){
                        $pptal =$idpp[0][0];
                        #* Actualizar 
                        $sql_cons ="UPDATE  `gf_comprobante_pptal`
                        SET `fecha` =:fecha , `tercero`=:tercero, `descripcion`=:descripcion 
                        WHERE `id_unico`=:id_unico ";
                        $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":tercero",$tercero),
                            array(":descripcion",$descripcion),
                            array(":id_unico",$pptal),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                    
                }
                if(!empty($tipocnt)){
                    #** Buscar Si existe **#
                    $idpp = $con->Listar("SELECT * FROM gf_comprobante_cnt 
                        WHERE tipocomprobante = $tipocnt AND numero = '$numero' 
                        AND parametrizacionanno = $panno");
                    if(count($idpp)>0){
                        $cnt = $idpp[0][0];
                        #* Actualizar 
                        $sql_cons ="UPDATE  `gf_comprobante_cnt`
                        SET `fecha` =:fecha , `tercero`=:tercero, `descripcion`=:descripcion 
                        WHERE `id_unico`=:id_unico ";
                        $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":tercero",$tercero),
                            array(":descripcion",$descripcion),
                            array(":id_unico",$cnt),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                }
                if(!empty($tipocausacion)){
                    #** Buscar Si existe **#
                    $idpp = $con->Listar("SELECT * FROM gf_comprobante_cnt 
                        WHERE tipocomprobante = $tipocausacion 
                        AND numero = $numero AND parametrizacionanno = $panno");
                    if(count($idpp)>0){
                        $causacion = $idpp[0][0];
                        #* Actualizar 
                        $sql_cons ="UPDATE  `gf_comprobante_cnt`
                        SSET `fecha` =:fecha , `tercero`=:tercero, `descripcion`=:descripcion 
                        WHERE `id_unico`=:id_unico ";
                        $sql_dato = array(
                            array(":fecha",$fecha),
                            array(":tercero",$tercero),
                            array(":descripcion",$descripcion),
                            array(":id_unico",$causacion),
                        );
                        $resp = $con->InAcEl($sql_cons,$sql_dato);
                    }
                }
                $sql_cons ="UPDATE  `gp_detalle_factura`
                SET `detallecomprobante` =:detallecomprobante 
                WHERE `factura`=:factura ";
                $sql_dato = array(
                    array(":detallecomprobante",NULL),
                    array(":factura",$id_factura),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);      
                
                eliminardetallescnt($causacion);
                eliminardetallescnt($cnt);
                eliminardetallespptal($pptal);
                          
                $rec =reconstruirComprobantesFactura($id_factura);
            }   
        }
        elseif($tipo==2){
            $tipopago = $_REQUEST['tipoP'];
            #BSCAR PAGOSA 
            $row = $con->Listar("SELECT DISTINCT id_unico, numero_pago,
                fecha_pago,responsable FROM gp_pago 
                WHERE fecha_pago BETWEEN '$fechaI' AND '$fechaF'
                AND tipo_pago= $tipopago AND parametrizacionanno = $panno ");
            for ($i = 0; $i < count($row); $i++) {
                $idCnt  = 0;
                $idPptal= 0;
                $idPago = 0;
                $idPago = $row[$i][0];
                $numero = $row[$i][1];
                $fecha  = $row[$i][2];
                $resp   = $row[$i][3];
                $sql2 = "SELECT cn.id_unico as cnt, cp.id_unico as ptal
                FROM gp_pago p
                LEFT JOIN gp_detalle_pago dp ON p.id_unico = dp.pago
                LEFT JOIN gp_tipo_pago tp ON p.tipo_pago = tp.id_unico
                LEFT JOIN gf_tipo_comprobante tc ON tp.tipo_comprobante = tc.id_unico
                LEFT JOIN gf_comprobante_cnt cn ON cn.tipocomprobante = tc.id_unico AND cn.numero = p.numero_pago
                LEFT JOIN gf_comprobante_pptal cp ON cp.tipocomprobante = tc.comprobante_pptal AND cp.numero = p.numero_pago
                WHERE p.id_unico = $idPago";
                $result2 = $mysqli->query($sql2);
                $row2 = $result2->fetch_row();
                if(!empty($row2[0])){
                    if(!empty($row2[1])){
                       $idCnt   = $row2[0];
                       $idPptal = $row2[1];
                    } else {
                        $idCnt = $row2[0];
                    }
                }
                $tipoc = $con->Listar("SELECT tc.id_unico, tc.comprobante_pptal, tc.tipo_comp_hom
                    FROM gp_tipo_pago tp 
                    LEFT JOIN gf_tipo_comprobante tc ON tp.tipo_comprobante = tc.id_unico 
                    WHERE tp.id_unico  = $tipopago");
                if(!empty($tipoc[0][0])){
                    $tipo_cnt = $tipoc[0][0];
                    if($idCnt==0){
                        $descripcion= '"Comprobante de recaudo"';
                        $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,
                            tipocomprobante,parametrizacionanno,tercero,estado,compania) 
                            values('$numero','$fecha',$descripcion,$tipo_cnt,
                            $panno,$resp,'1',$compania)";
                        $resultInsertC=$mysqli->query($sqlInsertC);
                        $idCnt  = $con->Listar("select max(id_unico) from gf_comprobante_cnt 
                            where tipocomprobante=$tipo_cnt and numero=$numero");
                        $idCnt = $idCnt[0][0];
                    }
                }
                if(!empty($tipoc[0][1])){
                    if($idPptal==0){
                        $tipopptal = $tipoc[0][1];
                        $insertPptal="insert into 
                            gf_comprobante_pptal(numero,fecha,fechavencimiento,descripcion,
                            parametrizacionanno,tipocomprobante,tercero,estado,responsable) 
                            values('$numero','$fecha','$fecha',$descripcion,$panno,$tipopptal,
                               $resp,'1',$resp)";
                        $resultInsertPptal=$mysqli->query($insertPptal);
                        $idPPAL=$con->Listar("select id_unico from gf_comprobante_pptal 
                            where tipocomprobante=$tipopptal and numero=$numero");
                        $idPptal = $idPPAL[0][0];
                    }
                }
                if(!empty($tipoc[0][2])){
                    $tipo_cas= $tipoc[0][2];

                    $descripcion= '"Comprobante de recaudo"';
                    $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,
                        tipocomprobante,parametrizacionanno,tercero,estado,compania) 
                        values('$numero','$fecha',$descripcion,$tipo_cas,
                        $panno,$resp,'1',$compania)";
                    $resultInsertC=$mysqli->query($sqlInsertC);
                    $idCnt  = $con->Listar("select max(id_unico) from gf_comprobante_cnt 
                        where tipocomprobante=$tipo_cas and numero=$numero");
                    $id_causacion = $idCnt[0][0];
                }
                #** Actualizar Detalels Pago **#
                $sql_cons ="UPDATE  `gp_detalle_pago`
                SET `detallecomprobante` =:detallecomprobante
                WHERE `pago`=:pago ";
                $sql_dato = array(
                    array(":detallecomprobante",NULL),
                    array(":pago",$idPago),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $recon = 0;
                if(empty($resp)){
                    #*** Buscar Si Tiene Causación
                    $cs = causacion($idCnt);
                    if(!empty($cs[0][0])){
                        $id_causacion =$cs;
                        $ec = eliminardetallescnt($id_causacion);
                        if($ec==1){
                            $ecn = eliminardetallescnt($idCnt);
                            if($ecn==1){
                                $epp = eliminardetallespptal($idPptal);
                                if($epp==1){
                                    $recon = 1;
                                }
                            }
                        }
                    } else {
                        #*** Buscar Si Existe Relacion Por Numero Y Tipo
                        $ccs = $con->Listar("SELECT cnc.id_unico
                            FROM gf_comprobante_cnt cn
                            LEFT JOIN gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico
                            LEFT JOIN gf_comprobante_cnt cnc ON cn.numero = cnc.numero AND cnc.tipocomprobante = tc.tipo_comp_hom
                            WHERE cn.id_unico =$idCnt");
                        $id_causacion =$ccs[0][0];
                        $ecn = eliminardetallescnt($idCnt);
                        if($ecn==1){
                            $epp = eliminardetallespptal($idPptal);
                            #var_dump($epp);
                            if($epp==1){
                                $recon = 1;
                            }
                        }
                    }
                    #var_dump($recon);
                    if($recon==1){
                        $reg=registrarDetallesPago($idPago,$idCnt,$idPptal,$id_causacion);
                        if($reg==true){
                            #***** Buscar Detalles Actualizar Comprobantes
                            $df = $con->Listar("SELECT DISTINCT f.id_unico,
                                f.numero_factura,f.descripcion , pg.fecha_pago, f.tercero 
                            FROM gp_factura f
                            LEFT JOIN gp_detalle_factura df ON f.id_unico = df.factura
                            LEFT JOIN gp_detalle_pago dp ON df.id_unico = dp.detalle_factura
                            LEFT JOIN gp_pago pg ON pg.id_unico = dp.pago
                            WHERE dp.pago = $idPago");

                            $fecha_act = $df[0][3];
                            $descp_act = 'Comprobante De Recaudo. Factura N°'.$df[0][1].' '.$df[0][2] ;
                            $descp_act2= 'Comprobante Causación De Recaudo. Factura N°'.$df[0][1].' '.$df[0][2] ;

                            #***** Actualizar Comprobantes
                            #cnt
                            $sql_cons ="UPDATE  `gf_comprobante_cnt`
                            SET `descripcion` =:descripcion,
                            `fecha` =:fecha
                            WHERE `id_unico`=:id_unico ";
                            $sql_dato = array(
                                array(":descripcion",$descp_act),
                                array(":fecha",$fecha_act),
                                array(":id_unico",$idCnt),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);
                            #pptal
                            $sql_cons ="UPDATE  `gf_comprobante_pptal`
                            SET `descripcion` =:descripcion,
                            `fecha` =:fecha
                            WHERE `id_unico`=:id_unico ";
                            $sql_dato = array(
                                array(":descripcion",$descp_act),
                                array(":fecha",$fecha_act),
                                array(":id_unico",$idPptal),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);

                            #causacion
                            $sql_cons ="UPDATE  `gf_comprobante_cnt`
                            SET `descripcion` =:descripcion,
                            `fecha` =:fecha
                            WHERE `id_unico`=:id_unico ";
                            $sql_dato = array(
                                array(":descripcion",$descp_act2),
                                array(":fecha",$fecha_act),
                                array(":id_unico",$id_causacion),
                            );
                            $resp = $con->InAcEl($sql_cons,$sql_dato);


                           $rta=true;
                            if($n_doc_com=='890206033'){
                                $cus = $con->Listar("SELECT df.* FROM gp_detalle_factura
                                    LEFT JOIN gp_concepto c ON df.concepto_tarifa = c.id_unico 
                                    WHERE df.factura = $factura AND c.nombre like '%uso de suelo%'");
                                if(count($cus)>0){
                                    $email = enviarEmail($df[0][4], 2);
                                }
                            }
                        } else {
                            $rta = false;
                        }
                    } else {
                        $rta = false;
                    }

                } else {
                    $rta =false;
                }
            }
            
        }
        echo 1;
    break;
    
    #Guardar Resolución
    case 38:
        $tipo           = $_REQUEST['tipo'];
        $numero         = $_REQUEST['numero'];
        $fechaInicial   = fechaC($_REQUEST['fechaInicial']);
        $numeroInicial  = $_REQUEST['numeroInicial'];
        $numeroFinal    = $_REQUEST['numeroFinal'];
        if(empty($_REQUEST['fechaFinal'])){
            $fechaFinal     = 'NULL';
        } else {
            $fechaFinal     = fechaC($_REQUEST['fechaFinal']);
        }
        if(empty($_REQUEST['descripcion'])){
            $descripcion     = NULL;
        } else {
            $descripcion     = $_REQUEST['descripcion'];
        }
        
        $sql_cons ="INSERT INTO `gp_resolucion_factura` 
            ( `tipo_factura`, `fecha_inicial`, `fecha_final`, 
            `numero_inicial`,`numero_final`,`descripcion`, `numero_resolucion`) 
        VALUES (:tipo_factura, :fecha_inicial ,:fecha_final,
        :numero_inicial,:numero_final,:descripcion, :numero_resolucion)";
        $sql_dato = array(
            array(":tipo_factura",$tipo),
            array(":fecha_inicial",$fechaInicial),
            array(":fecha_final",$fechaFinal),
            array(":numero_inicial",$numeroInicial),
            array(":numero_final",$numeroFinal),
            array(":descripcion",$descripcion),
            array(":numero_resolucion",$numero),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    #Modificar Resolución
    case 39:
        $id             = $_REQUEST['id'];
        $tipo           = $_REQUEST['tipo'];
        $numero         = $_REQUEST['numero'];
        $fechaInicial   = fechaC($_REQUEST['fechaInicial']);
        $numeroInicial  = $_REQUEST['numeroInicial'];
        $numeroFinal    = $_REQUEST['numeroFinal'];
        if(empty($_REQUEST['fechaFinal'])){
            $fechaFinal     = 'NULL';
        } else {
            $fechaFinal     = fechaC($_REQUEST['fechaFinal']);
        }
        if(empty($_REQUEST['descripcion'])){
            $descripcion     = NULL;
        } else {
            $descripcion     = $_REQUEST['descripcion'];
        }
        
        $sql_cons ="UPDATE `gp_resolucion_factura` 
                SET  `tipo_factura`=:tipo_factura, `fecha_inicial`=:fecha_inicial ,
                `fecha_final`=:fecha_final,`numero_inicial`=:numero_inicial,
                `numero_final`=:numero_final,`descripcion`=:descripcion, 
                `numero_resolucion` =:numero_resolucion 
                WHERE `id_unico`=:id_unico";
        $sql_dato = array(
            array(":tipo_factura",$tipo),
            array(":fecha_inicial",$fechaInicial),
            array(":fecha_final",$fechaFinal),
            array(":numero_inicial",$numeroInicial),
            array(":numero_final",$numeroFinal),
            array(":descripcion",$descripcion),
            array(":numero_resolucion",$numero),
            array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    
    #* Eliminar Resoluciones
    case 40:
        $id     = $_POST['id'];
        $sql_cons ="DELETE FROM `gp_resolucion_factura` 
                WHERE `id_unico` =:id_unico";
        $sql_dato = array(
                array(":id_unico",$id),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato); 
        if(empty($resp)){
            $e=1;
        } else {
            $e=0;
        }
        echo $e;
    break;
    
    #* Guardar Factura 
    case 41:
        ///CALCULAR NUMERO
        $tipofactura = $_POST['sltTipoFactura'];
        $cotizacion = $_POST['cot'];
        $id_fac_coti = $_POST['id'];
        $cons = $con->Listar("SELECT sigue_consecutivo FROM gp_tipo_factura WHERE id_unico = $tipofactura");
        $res  = $con->Listar("SELECT numero_inicial FROM `gp_resolucion_factura` WHERE tipo_factura = $tipofactura ORDER BY id_unico DESC LIMIT 1");
        if ($cons[0][0] == 1) {
            $fac = $con->Listar("SELECT * FROM gp_factura WHERE tipofactura = $tipofactura limit 1 ");
            if(count($fac)>0){
                $sql = $con->Listar("SELECT MAX(cast(numero_factura as unsigned))+1 FROM gp_factura where tipofactura = $tipofactura ");

                $numero = $sql[0][0];
            } else {
                if(count($res)>0){
                    $numero = $res[0][0];
                } else {
                    $numero = $anno. '000001';
                }
            }
        } else {

            $fac = $con->Listar("SELECT * FROM gp_factura WHERE tipofactura = $tipofactura AND parametrizacionanno = $panno");
            if(count($fac)>0){
                $sql = $con->Listar("SELECT REPLACE(MAX(numero_factura), MAX(cast(numero_factura as unsigned)),MAX(cast(numero_factura as unsigned))+1) FROM gp_factura where tipofactura = $tipofactura AND parametrizacionanno = $panno");
                $numero = $sql[0][0];
            } else {
                if(count($res)>0){
                    $numero = $res[0][0];
                } else {
                    $numero = $anno. '000001';
                }
            }
        }

        $n_factura = trim($numero);
        #* Movimiento Hotel 
        if(!empty($_REQUEST['id_ingreso'])){
            $ingreso = $_REQUEST['id_ingreso'];
        } else {
            $ingreso = NULL;
        }
        if(!empty($_REQUEST['tipo_cb']) && !empty($_REQUEST['tipo_cambio'])){
            $tipo_cambio = $_REQUEST['tipo_cambio'];
        } else {
            $tipo_cambio = NULL;
        }
        if(empty($_REQUEST['txtDescuento'])){
            $desc = 0;
        } else {
            $desc = $_REQUEST['txtDescuento'];
        }

        if(empty($_REQUEST['sltUsuario'])){
            $uvms = NULL;
        } else {
            $uvms = $_REQUEST['sltUsuario'];
        }

        if(!empty($_REQUEST['txtCuotas'])){
            $cuotas = $_REQUEST['txtCuotas'];
        } else {
            $cuotas = NULL;
        }

        if(!empty($_REQUEST['txtAbono'])){
            $abono = $_REQUEST['txtAbono'];
        } else {
            $abono = NULL;
        }


        $sql_cons ="INSERT INTO `gp_factura`
        ( `numero_factura`, `tipofactura`, `tercero`, 
        `fecha_factura`, `fecha_vencimiento`, `centrocosto`, 
        `descripcion`, `estado_factura`, `responsable`, 
        `vendedor`, `parametrizacionanno`, `descuento`, `fechaE`,  
        `observaciones`,  `proyecto`,`forma_pago`,`metodo_pago`,`elaboro`, 
        `mov_hotel`, `tipo_cambio`, `unidad_vivienda_servicio`, 
        `cuotas`, `abono`)
        VALUES (:numero_factura, :tipofactura, :tercero, 
        :fecha_factura, :fecha_vencimiento, :centrocosto, 
        :descripcion, :estado_factura, :responsable, 
        :vendedor, :parametrizacionanno, :descuento,  :fechaE,  
        :observaciones, :proyecto, :forma_pago, :metodo_pago, :elaboro, 
        :mov_hotel,:tipo_cambio,:unidad_vivienda_servicio, 
        :cuotas, :abono)";
        $sql_dato = array(
            array(":numero_factura",$n_factura),
            array(":tipofactura",$_REQUEST['sltTipoFactura']),
            array(":tercero",$_REQUEST['sltTercero']),
            array(":fecha_factura",fechaC($_REQUEST['fechaF'])),
            array(":fecha_vencimiento",fechaC($_REQUEST['fechaV'])),
            array(":centrocosto",$_REQUEST['sltCentroCosto']),
            array(":descripcion",$_REQUEST['txtDescripcion']),
            array(":estado_factura",4),
            array(":responsable",$usuario_t),
            array(":vendedor",$_REQUEST['sltVendedor']),
            array(":parametrizacionanno",$panno),
            array(":descuento",$desc),
            array(":fechaE",date('Y-m-d')),
            array(":observaciones",NULL),
            array(":proyecto",$proyecto),
            array(":forma_pago",$_REQUEST['txtFormaP']),
            array(":metodo_pago",$_REQUEST['sltMetodo']),
            array(":elaboro",$_SESSION['usuario_tercero']),
            array(":mov_hotel",$ingreso),
            array(":tipo_cambio",$tipo_cambio),
            array(":unidad_vivienda_servicio",$uvms),
            array(":cuotas",$cuotas),
            array(":abono",$abono),
            
        );
        
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        //var_dump($resp);
        if(empty($resp)){
            
            
            $bf = $con->Listar("SELECT id_unico FROM gp_factura 
                WHERE numero_factura ='".$n_factura."' 
                AND tipofactura ='".$_REQUEST['sltTipoFactura']."' 
                AND parametrizacionanno = $panno");
            #* Movimiento Hotel 
            if(!empty($_REQUEST['id_ingreso'])){
                $id_f = $bf[0][0];
                $rowd = $con->Listar("SELECT dmv.id_unico,
                     cp.id_unico, uf.id_unico, if((tar.porcentaje_iva + tar.porcentaje_impoconsumo)>0, ROUND(dmv.valor / (1+((tar.porcentaje_iva + tar.porcentaje_impoconsumo)/100))) , dmv.valor) as VB, 
                    DATEDIFF(mv.fechaFinal,mv.fechaInicio) cantidad, 
                    if(tar.porcentaje_iva >0, ROUND((dmv.valor / (1+((tar.porcentaje_iva + tar.porcentaje_impoconsumo)/100)))*(tar.porcentaje_iva / 100)) , 0) as VI, 
                    if(tar.porcentaje_impoconsumo >0, ROUND((dmv.valor / (1+((tar.porcentaje_iva + tar.porcentaje_impoconsumo)/100)))*(tar.porcentaje_impoconsumo / 100)) , 0) as VIM,
                    0, (dmv.valor *DATEDIFF(mv.fechaFinal,mv.fechaInicio) ) as VTA, 
                    dmv.valor,
                    0, 0, 0, 0, '', '', 0
                FROM gh_detalle_mov dmv
                LEFT JOIN gp_tarifa tar ON dmv.tarifa = tar.id_unico
                LEFT JOIN gp_concepto_tarifa cpt ON tar.id_unico = cpt.tarifa
                LEFT JOIN gp_concepto cp ON cpt.concepto = cp.id_unico
                LEFT JOIN gf_elemento_unidad eu ON cpt.elemento_unidad = eu.id_unico 
                LEFT JOIN gf_unidad_factor uf ON eu.unidad_empaque = uf.id_unico 
                LEFT JOIN gh_movimiento mv ON dmv.movimiento = mv.id_unico 
                WHERE  dmv.movimiento = $ingreso");
                
                for ($d = 0; $d < count($rowd); $d++) {
                    $sql_cons ="INSERT INTO `gp_detalle_factura`
                    ( `factura`, `concepto_tarifa`, `unidad_origen`,`valor`, `cantidad`, `iva`, 
                    `impoconsumo`, `ajuste_peso`, `valor_total_ajustado`, `valor_origen`, `descuento`, 
                    `valoru_conversion`, `valor_conversion`, `valor_trm`, `descripcion`,
                    `tipo_descuento`,`valor_descuento`)
                    VALUES (:factura, :concepto_tarifa, :unidad_origen, :valor, :cantidad, :iva, 
                    :impoconsumo, :ajuste_peso, :valor_total_ajustado, :valor_origen, :descuento, 
                    :valoru_conversion, :valor_conversion, :valor_trm, :descripcion,
                     :tipo_descuento, :valor_descuento)";
                    $sql_dato = array(
                        array(":factura",$id_f),
                        array(":concepto_tarifa",$rowd[0][1]),
                        array(":unidad_origen",$rowd[0][2]),
                        array(":valor",$rowd[0][3]),
                        array(":cantidad",$rowd[0][4]),
                        array(":iva",$rowd[0][5]),
                        array(":impoconsumo",$rowd[0][6]),
                        array(":ajuste_peso",0),
                        array(":valor_total_ajustado",$rowd[0][8]),
                        array(":valor_origen",$rowd[0][9]),
                        array(":descuento",0),
                        array(":valoru_conversion",0),
                        array(":valor_conversion",0),
                        array(":valor_trm",0),
                        array(":descripcion",NULL),
                        array(":tipo_descuento",NULL),
                        array(":valor_descuento",0),
                    );
                    $resp = $con->InAcEl($sql_cons,$sql_dato);
                }
            }
            #* Buscar Datos Relacionados con el tipo de factura 
            $bdtf = $con->Listar("SELECT tc.id_unico, tc.comprobante_pptal, tf.tipo_movimiento 
                FROM gp_tipo_factura tf 
                LEFT JOIN gf_tipo_comprobante tc ON tf.tipo_comprobante = tc.id_unico 
                WHERE tf.id_unico =".$_REQUEST['sltTipoFactura']);
            #* Insertar Comprobante Cnt 
            if(!empty($bdtf[0][0])){
                $sql_cons ="INSERT INTO `gf_comprobante_cnt`
                ( `numero`, `fecha`, `descripcion`, `tipocomprobante`, `compania`, `parametrizacionanno`, 
                `tercero`, `estado`,  `usuario`, `fecha_elaboracion`)
                VALUES (:numero, :fecha, :descripcion, 
                :tipocomprobante, :compania, :parametrizacionanno, 
                :tercero, :estado, :usuario, 
                :fecha_elaboracion)";
                $sql_dato = array(
                    array(":numero",$n_factura),
                    array(":fecha",fechaC($_REQUEST['fechaF'])),
                    array(":descripcion",$_REQUEST['txtDescripcion']),
                    array(":tipocomprobante",$bdtf[0][0]),
                    array(":compania",$compania),
                    array(":parametrizacionanno",$panno),
                    array(":tercero",$_REQUEST['sltTercero']),
                    array(":estado",1),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y-m-d')),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $sqlCompCnt = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_cnt 
                WHERE numero='$n_factura' AND tipocomprobante=".$bdtf[0][0]);
                $id_cntC = $sqlCompCnt[0][0] ;
            }
            #* Insertar Comprobante Pptal 
            if(!empty($bdtf[0][1])){
                $sql_cons ="INSERT INTO `gf_comprobante_pptal`
                ( `numero`, `fecha`, `fechavencimiento`, `descripcion`, 
                `parametrizacionanno`, `tipocomprobante`, `tercero`, `estado`, `responsable`,
                `compania`, `usuario`, `fecha_elaboracion`)
                VALUES (:numero, :fecha, :fechavencimiento, :descripcion, 
                :parametrizacionanno, :tipocomprobante, :tercero, :estado, :responsable, 
                :compania, :usuario, :fecha_elaboracion)";
                $sql_dato = array(
                    array(":numero",$n_factura),
                    array(":fecha",fechaC($_REQUEST['fechaF'])),
                    array(":fechavencimiento",fechaC($_REQUEST['fechaV'])),
                    array(":descripcion",$_REQUEST['txtDescripcion']),
                    array(":parametrizacionanno",$panno),                    
                    array(":tipocomprobante",$bdtf[0][1]),
                    array(":tercero",$_REQUEST['sltTercero']),
                    array(":estado",3),
                    array(":responsable",$usuario_t),
                    array(":compania",$compania),
                    array(":usuario",$usuario),
                    array(":fecha_elaboracion",date('Y-m-d')),
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $sqlCompPptal = $con->Listar("SELECT MAX(id_unico) FROM gf_comprobante_pptal 
                WHERE numero='$n_factura' AND tipocomprobante=".$bdtf[0][1]);
                $id_pptalC = $sqlCompPptal[0][0];
            }
            #* Insertar Comprobante Movimiento 
            if(!empty($bdtf[0][2])){
                $dpd = $con->Listar("SELECT dependencia FROM gf_dependencia_responsable WHERE responsable = ".$_SESSION['usuario_tercero']);
                $sql_cons ="INSERT INTO `gf_movimiento`
                ( `numero`, `fecha`, `descripcion`, `tipomovimiento`, `parametrizacionanno`, 
                `compania`, `tercero`, `tercero2`, `dependencia`, `centrocosto`,  `estado`, 
                `descuento`, `fecha_hora`, `factura`, `proyecto`)
                VALUES (:numero, :fecha, :descripcion, :tipomovimiento, :parametrizacionanno,
                :compania, :tercero, :tercero2, :dependencia, :centrocosto, 
                :estado, :descuento, :fecha_hora, :factura, :proyecto)";
                $sql_dato = array(
                    array(":numero",$n_factura),
                    array(":fecha",fechaC($_REQUEST['fechaF'])),
                    array(":descripcion",$_REQUEST['txtDescripcion']),
                    array(":tipomovimiento",$bdtf[0][2]),
                    array(":parametrizacionanno",$panno),
                    array(":compania",$compania),
                    array(":tercero",$_REQUEST['sltTercero']),
                    array(":tercero2",$_REQUEST['sltVendedor']),
                    array(":dependencia",$dpd[0][0]),
                    array(":centrocosto",$_REQUEST['sltCentroCosto']),
                    array(":estado",2),
                    array(":descuento",$desc),
                    array(":fecha_hora",date('Y-m-d H:i:s')),
                    array(":factura",$bf[0][0]),
                    array(":proyecto",$proyecto), 
                );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
                $sqlMov = $con->Listar("SELECT MAX(id_unico) FROM gf_movimiento 
                WHERE numero='$n_factura' AND tipomovimiento=".$bdtf[0][2]);
                $id_movi = $sqlMov[0][0];
            }


            if ($cotizacion==1) {

                $rowd = $con->Listar("SELECT dtf.id_unico,
                dtf.factura, dtf.concepto_tarifa, cnp.nombre, 
                uf.nombre, dtf.valor, dtf.cantidad, dtf.iva, dtf.impoconsumo, 
                dtf.ajuste_peso, dtf.valor_total_ajustado, dtf.valor_origen, 
                dtf.descuento, dtf.valoru_conversion, dtf.valor_conversion, 
                dtf.valor_trm, dtf.descripcion, 
                if(dtf.tipo_descuento=1,'Porcentaje', IF(dtf.tipo_descuento=2,'Cantidad',
                IF(dtf.tipo_descuento=3,'Valor',''))), dtf.valor_descuento,dtf.unidad_origen,dtf.tipo_descuento 
                FROM      gp_detalle_factura dtf
                LEFT JOIN gp_concepto cnp ON cnp.id_unico = dtf.concepto_tarifa
                LEFT JOIN gf_unidad_factor uf ON dtf.unidad_origen = uf.id_unico
                WHERE     dtf.factura = $id_fac_coti");
                       $id_fa = $bf[0][0];
                   for ($i = 0; $i < count($rowd); $i++) {
                      
                       $val_aj_t=$rowd[$i][10];
                       $val=$rowd[$i][5];
                       $valor_or=$rowd[$i][11];
                       $val_iva=$rowd[$i][7];
                       $val_impo=$rowd[$i][8];
                       $val_trm=$rowd[$i][15];
                       $concepto_t=$rowd[$i][2];
                       $unidad=$rowd[$i][19];
                       $cant=$rowd[$i][6];
                       $ajust_peso=$rowd[$i][9];
                       $descuent=$rowd[$i][12];
                       $descrip=$rowd[$i][16];
                       $tipo_des=$rowd[$i][20];
                       $id_detalleC=$rowd[$i][0];

                           list($valoru_conversion,$v_conversion, $valor_trm ) = array(0,0,0);
        
                           list($valorTotalAjuste,$valor, $valor_origen, $iva, $impoconsumo ) = array($val_aj_t,$val,$valor_or,$val_iva,$val_impo);
                           if($val_trm>0){
                               $valor_trm          = $val_trm;
                               $vc                 = $val_trm;
                               $v_conversion       = $valorTotalAjuste;
                               $valoru_conversion  = $valor;
                               $valor              = ROUND($vc*$valor, 4);
                               $valor_origen       = ROUND($vc*$valor_origen, 4);
                               $valorTotalAjuste   = ROUND($vc*$valorTotalAjuste, 4);
                               $iva                = ROUND($vc*$iva, 4);
                               $impoconsumo        = ROUND($vc*$impoconsumo, 4);
                           }

                           $descuento = 0;
                           $val_descuento=$rowd[$i][18];
                           if(!empty($val_descuento)){
                               $descuento =$val_descuento;
                           }
  
                           $sql_cons ="INSERT INTO `gp_detalle_factura`
                           ( `factura`, `concepto_tarifa`, `unidad_origen`,`valor`, `cantidad`, `iva`, 
                           `impoconsumo`, `ajuste_peso`, `valor_total_ajustado`, `valor_origen`, `descuento`, 
                           `valoru_conversion`, `valor_conversion`, `valor_trm`, `descripcion`,
                           `tipo_descuento`,`valor_descuento`)
                           VALUES (:factura, :concepto_tarifa, :unidad_origen, :valor, :cantidad, :iva, 
                           :impoconsumo, :ajuste_peso, :valor_total_ajustado, :valor_origen, :descuento, 
                           :valoru_conversion, :valor_conversion, :valor_trm, :descripcion,
                            :tipo_descuento, :valor_descuento)";
                           $sql_dato = array(
                               array(":factura",$id_fa),
                               array(":concepto_tarifa",trim($concepto_t)),
                               array(":unidad_origen",$unidad),
                               array(":valor",$valor),
                               array(":cantidad",$cant),
                               array(":iva",$iva),
                               array(":impoconsumo",$impoconsumo),
                               array(":ajuste_peso",$ajust_peso),
                               array(":valor_total_ajustado",$valorTotalAjuste),
                               array(":valor_origen",$valor_origen),
                               array(":descuento",$descuent),
                               array(":valoru_conversion",$valoru_conversion),
                               array(":valor_conversion",$v_conversion),
                               array(":valor_trm",$valor_trm),
                               array(":descripcion",$descrip),
                               array(":tipo_descuento",$tipo_des),
                               array(":valor_descuento",$descuento),
                           );
                           $resp = $con->InAcEl($sql_cons,$sql_dato);
                           //var_dump($resp);
                           if(empty($resp)){
                               $rta = 1;
                               $id_df = $con->Listar("SELECT MAX(id_unico) FROM gp_detalle_factura 
                               WHERE factura =$id_fa AND concepto_tarifa=".$concepto_t);
                               $id_detalle = $id_df[0][0] ;
                               if(!empty($id_cntC) || !empty($id_pptalC)){
                                   reconstruirComprobantesFacturaDetalle($id_fa, $id_detalle);
                                   actualizardetalleAfectadoCotizacion($id_detalle,$id_detalleC);
                               }
                               if(!empty($id_movi)){
                                   reconstruirSalidaDetalle($id_movi, $id_detalle, $concepto_t,$unidad,$cant);
                               }
        
                               #ACTUALIZAR ABONO 
                               $rowc = $con->Listar("SELECT f.cuotas, SUM(df.valor_total_ajustado) FROM gp_detalle_factura df LEFT JOIN gp_factura f ON df.factura = f.id_unico WHERE  df.factura =$id_fa");
                               if(empty($rowc[0][0])){
                                   $cuotas = 1;
                               }elseif($rowc[0][0]==0){
                                   $cuotas = 1;
                               } else {
                                   $cuotas = $rowc[0][0];
                               }
        
                               if(empty($rowc[0][1])){
                                   $abono = 0;
                               }elseif($rowc[0][1]==0){
                                   $abono = 0;
                               } else {
                                   $abono = $rowc[0][1];
                               }
                               //echo $rowc[0][1].'AB'.$abono.'CTAS'.$cuotas;
                               if($abono !=0){
                                   $abono = ROUND($abono/$cuotas);
                               }
                               $sql_cons ="UPDATE `gp_factura`
                                   SET `abono`=:abono 
                                   WHERE `id_unico`=:id_unico ";
                                   $sql_dato = array(
                                       array(":abono",$abono),
                                       array(":id_unico",$id_fa),
                                   );
                               $resp = $con->InAcEl($sql_cons,$sql_dato);
                           }
                       
                    }
                }

            echo $bf[0][0];
        } else {
            echo 0;
        }
    break;
    
    #* Buscar Factura 
    case 42:
        $factura = trim($_POST['factura']);
        $html    = "&factura=".md5($factura);
        $row   = $con->Listar("SELECT      cnt.id_unico as cnt,ptal.id_unico as ptal, 
                                mto.id_unico as mov 
                    FROM        gp_factura pg 
                    LEFT JOIN   gp_tipo_factura tpg ON pg.tipofactura = tpg.id_unico 
                    LEFT JOIN   gf_tipo_comprobante tpc ON tpc.id_unico = tpg.tipo_comprobante 
                    LEFT JOIN   gf_comprobante_cnt cnt ON cnt.tipocomprobante = tpc.id_unico 
                                AND pg.numero_factura = cnt.numero 
                    LEFT JOIN   gf_tipo_comprobante_pptal tcp ON tpc.comprobante_pptal = tcp.id_unico 
                    LEFT JOIN   gf_comprobante_pptal ptal ON ptal.tipocomprobante = tcp.id_unico 
                                AND pg.numero_factura = ptal.numero 
                    LEFT JOIN   gf_movimiento mto ON tpg.tipo_movimiento = mto.tipomovimiento 
                                AND pg.numero_factura = mto.numero 
                    WHERE pg.id_unico =  $factura");
        if(!empty($row[0][0])){
            $html .='&cnt='.md5($row[0][0]);
        }
        if(!empty($row[0][1])){
            $html .='&pptal='.md5($row[0][1]);
        }
        if(!empty($row[0][2])){
            $html .='&mov='.md5($row[0][2]);
        }
        
        echo $html;
    break;
    
    #* Modificar Datos Encabezados
    case 43:
        $id_factura = $_REQUEST['id'];
        if(!empty($_REQUEST['tipo_cb']) && !empty($_REQUEST['tipo_cambio'])){
            $tipo_cambio = $_REQUEST['tipo_cambio'];
        } else {
            $tipo_cambio = NULL;
        }
        if(!empty($_REQUEST['txtCuotas'])){
            $cuotas = $_REQUEST['txtCuotas'];
        } else {
            $cuotas = NULL;
        }

        if(!empty($_REQUEST['txtAbono'])){
            $abono = $_REQUEST['txtAbono'];
        } else {
            $abono = NULL;
        }

        if(empty($_REQUEST['sltUsuario'])){
            $uvms = NULL;
        } else {
            $uvms = $_REQUEST['sltUsuario'];
        }

        $sql_cons ="UPDATE `gp_factura`
            SET `tercero`=:tercero,
            `fecha_factura`=:fecha_factura,
            `fecha_vencimiento`=:fecha_vencimiento,
            `centrocosto`=:centrocosto,
            `descripcion`=:descripcion,
            `vendedor`=:vendedor, 
            `forma_pago`=:forma_pago, 
            `metodo_pago`=:metodo_pago, 
            `tipo_cambio`=:tipo_cambio, 
            `cuotas`=:cuotas,
            `unidad_vivienda_servicio` =:unidad_vivienda_servicio,
            `abono`=:abono

            WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":tercero",$_REQUEST['sltTercero']),
            array(":fecha_factura",fechaC($_REQUEST['fechaF'])),
            array(":fecha_vencimiento",fechaC($_REQUEST['fechaV'])),
            array(":centrocosto",$_REQUEST['sltCentroCosto']),
            array(":descripcion",$_REQUEST['txtDescripcion']),
            array(":vendedor",$_REQUEST['sltVendedor']),
            array(":forma_pago",$_REQUEST['txtFormaP']),
            array(":metodo_pago",$_REQUEST['sltMetodo']),
            array(":tipo_cambio",$tipo_cambio),
            array(":cuotas",$cuotas),
            array(":abono",$abono),
            array(":unidad_vivienda_servicio",$uvms),
            array(":id_unico",$id_factura),
        );

        $resp = $con->InAcEl($sql_cons,$sql_dato);
        //var_dump($resp.'UPDATEs'.$uvms) ;
               
        if(!empty($_REQUEST['idcnt'])){
            
            $sql_cons ="UPDATE `gf_comprobante_cnt`
            SET `tercero`=:tercero,
            `fecha`=:fecha,
            `descripcion`=:descripcion
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":tercero",$_REQUEST['sltTercero']),
                array(":fecha",fechaC($_REQUEST['fechaF'])),
                array(":descripcion",$_REQUEST['txtDescripcion']),
                array(":id_unico",$_REQUEST['idcnt']),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        if(!empty($_REQUEST['idpptal'])){                    
            $sql_cons ="UPDATE `gf_comprobante_pptal`
            SET `tercero`=:tercero,
            `fecha`=:fecha,
            `fechavencimiento`=:fechavencimiento,
            `descripcion`=:descripcion
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":tercero",$_REQUEST['sltTercero']),
                array(":fecha",fechaC($_REQUEST['fechaF'])),
                array(":fechavencimiento",fechaC($_REQUEST['fechaV'])),
                array(":descripcion",$_REQUEST['txtDescripcion']),
                array(":id_unico",$_REQUEST['idpptal']),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        if(!empty($_REQUEST['idmov'])){
            $sql_cons ="UPDATE `gf_movimiento`
            SET `tercero`=:tercero,
            `tercero2`=:tercero2,
            `fecha`=:fecha,
            `centrocosto`=:centrocosto,
            `descripcion`=:descripcion 
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":tercero",$_REQUEST['sltTercero']),
                array(":tercero2",$_REQUEST['sltVendedor']),
                array(":fecha",fechaC($_REQUEST['fechaF'])),
                array(":centrocosto",$_REQUEST['sltCentroCosto']),
                array(":descripcion",$_REQUEST['txtDescripcion']),
                array(":id_unico",$_REQUEST['idmov']),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
        }
        
        echo $id_factura;
        
    break;
    
    #* Eliminar Datos 
    case 44:
        $te = 0;
        $te += eliminardetallesFactura($_REQUEST['id']);
        $te += eliminardetallesMov($_REQUEST['idmov']);
        $te += eliminardetallescnt($_REQUEST['idcnt']);
        $te += eliminarDetallesRetencion($_REQUEST['idcnt']);
        $te += eliminardetallespptal($_REQUEST['idpptal']);
        if($te >5) {echo 0;} else { echo 2;}
    break;
    
    #* Guardar Detalles
    case 45:
        $rta = 0;
        $id_factura = $_REQUEST['id'];
        $id_cnt     = $_REQUEST['idcnt'];
        $id_pptal   = $_REQUEST['idpptal'];
        $id_mov     = $_REQUEST['idmov'];
        
        if(!empty($id_factura)){
            list($valoru_conversion,$v_conversion, $valor_trm ) = array(0,0,0);

            list($valorTotalAjuste,$valor, $valor_origen, $iva, $impoconsumo ) = array($_REQUEST['txtValorA'],$_REQUEST['txtValorB'],$_REQUEST['txtValorX'], $_REQUEST['txtIva'], $_REQUEST['txtImpoconsumo']);
            if($_REQUEST['trm']>0){
                $valor_trm          = $_REQUEST['trm'];
                $vc                 = $_REQUEST['trm'];
                $v_conversion       = $valorTotalAjuste;
                $valoru_conversion  = $valor;
                $valor              = ROUND($vc*$valor, 4);
                $valor_origen       = ROUND($vc*$valor_origen, 4);
                $valorTotalAjuste   = ROUND($vc*$valorTotalAjuste, 4);
                $iva                = ROUND($vc*$iva, 4);
                $impoconsumo        = ROUND($vc*$impoconsumo, 4);
            }
            $descuento = 0;
            if(!empty($_REQUEST['txtValorDescuento'])){
                $descuento = $_REQUEST['txtValorDescuento'];
            }
            $sql_cons ="INSERT INTO `gp_detalle_factura`
            ( `factura`, `concepto_tarifa`, `unidad_origen`,`valor`, `cantidad`, `iva`, 
            `impoconsumo`, `ajuste_peso`, `valor_total_ajustado`, `valor_origen`, `descuento`, 
            `valoru_conversion`, `valor_conversion`, `valor_trm`, `descripcion`,
            `tipo_descuento`,`valor_descuento`)
            VALUES (:factura, :concepto_tarifa, :unidad_origen, :valor, :cantidad, :iva, 
            :impoconsumo, :ajuste_peso, :valor_total_ajustado, :valor_origen, :descuento, 
            :valoru_conversion, :valor_conversion, :valor_trm, :descripcion,
             :tipo_descuento, :valor_descuento)";
            $sql_dato = array(
                array(":factura",$id_factura),
                array(":concepto_tarifa",trim($_REQUEST['sltConcepto'])),
                array(":unidad_origen",$_REQUEST['sltUnidad']),
                array(":valor",$valor),
                array(":cantidad",$_REQUEST['txtCantidad']),
                array(":iva",$iva),
                array(":impoconsumo",$impoconsumo),
                array(":ajuste_peso",$_REQUEST['txtAjustePeso']),
                array(":valor_total_ajustado",$valorTotalAjuste),
                array(":valor_origen",$valor_origen),
                array(":descuento",$_REQUEST['txtXDescuento']),
                array(":valoru_conversion",$valoru_conversion),
                array(":valor_conversion",$v_conversion),
                array(":valor_trm",$valor_trm),
                array(":descripcion",$_REQUEST['descripcion']),
                array(":tipo_descuento",$_REQUEST['sltTipoDes']),
                array(":valor_descuento",$descuento),
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            //var_dump($resp);
            if(empty($resp)){
                $rta = 1;
                $id_df = $con->Listar("SELECT MAX(id_unico) FROM gp_detalle_factura 
                WHERE factura =$id_factura AND concepto_tarifa=".$_REQUEST['sltConcepto']);
                $id_detalle = $id_df[0][0] ;
                if(!empty($id_cnt) || !empty($id_pptal)){
                    reconstruirComprobantesFacturaDetalle($id_factura, $id_detalle);
                }
                if(!empty($id_mov)){
                    reconstruirSalidaDetalle($id_mov, $id_detalle, $_REQUEST['sltConcepto'],$_REQUEST['sltUnidad'],$_REQUEST['txtCantidad']);
                }

                #ACTUALIZAR ABONO 
                $rowc = $con->Listar("SELECT f.cuotas, SUM(df.valor_total_ajustado) FROM gp_detalle_factura df LEFT JOIN gp_factura f ON df.factura = f.id_unico WHERE  df.factura =$id_factura");
                if(empty($rowc[0][0])){
                    $cuotas = 1;
                }elseif($rowc[0][0]==0){
                    $cuotas = 1;
                } else {
                    $cuotas = $rowc[0][0];
                }

                if(empty($rowc[0][1])){
                    $abono = 0;
                }elseif($rowc[0][1]==0){
                    $abono = 0;
                } else {
                    $abono = $rowc[0][1];
                }
                //echo $rowc[0][1].'AB'.$abono.'CTAS'.$cuotas;
                if($abono !=0){
                    $abono = ROUND($abono/$cuotas);
                }
                $sql_cons ="UPDATE `gp_factura`
                    SET `abono`=:abono 
                    WHERE `id_unico`=:id_unico ";
                    $sql_dato = array(
                        array(":abono",$abono),
                        array(":id_unico",$id_factura),
                    );
                $resp = $con->InAcEl($sql_cons,$sql_dato);
            }
        }
        
        echo $rta;
    break;
    
    #* Eliminar Detalles
    case 46:
        $rta = 0;
        $id_detalle = $_REQUEST['id'];
        //Buscar Detalles Afectados
        $row = $con->Listar("SELECT df.detallemovimiento, 
            df.detallecomprobante, dc.detalleafectado, dc.detallecomprobantepptal 
            FROM gp_detalle_factura df 
            LEFT JOIN gf_detalle_comprobante dc ON df.detallecomprobante = dc.id_unico 
            WHERE df.id_unico  =$id_detalle");
        $sql_cons ="DELETE FROM  `gp_detalle_factura`
        WHERE `id_unico`=:id_unico ";
        $sql_dato = array(
            array(":id_unico",$id_detalle)
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            $rta +=1;
        }
        if(!empty($row[0][0])){
            $sql_cons ="DELETE FROM  `gf_detalle_movimiento`
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":id_unico",$row[0][0])
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $rta +=1;
            }
        }
        if(!empty($row[0][1])){
            $sql_cons ="DELETE FROM  `gf_detalle_comprobante`
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":id_unico",$row[0][1])
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $rta +=1;
            }
        }
        if(!empty($row[0][2])){
            $sql_cons ="DELETE FROM  `gf_detalle_comprobante`
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":id_unico",$row[0][2])
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $rta +=1;
            }
        }
        if(!empty($row[0][3])){
            $sql_cons ="DELETE FROM  `gf_detalle_comprobante_pptal`
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":id_unico",$row[0][3])
            );
            $resp = $con->InAcEl($sql_cons,$sql_dato);
            if(empty($resp)){
                $rta +=1;
            }
        }
        
        echo $rta;
    break;
    
    #* Reconstruir Salidas Almacén
    case 47:
        $fechaI = fechaC($_REQUEST['fechaI']);
        $fechaF = fechaC($_REQUEST['fechaF']);
        $tipof  = $_REQUEST['tipof'];   
        $bf = $con->Listar("SELECT DISTINCT id_unico 
            FROM gp_factura f 
            -- WHERE f.fecha_factura BETWEEN '$fechaI' AND '$fechaF' 
            ORDER BY f.fecha_factura ASC ");
        $f = 0;
        for ($i = 0; $i < count($bf); $i++) {
            $id_factura = $bf[$i][0];
            $f = reconstruirSalida($id_factura);
        }
        echo $f;
    break;
    #* Buscar Conceptos
    case 48:
        $codigo =  $_GET['term'];
        $query = "SELECT    DISTINCTROW cnp.id_unico, cnp.nombre as nombre_concepto, unf.nombre, pln.id_unico as id_pi 
            FROM      gp_concepto_tarifa AS cont
            LEFT JOIN gp_concepto        AS cnp ON cont.concepto           = cnp.id_unico
            LEFT JOIN gf_plan_inventario AS pln ON cnp.plan_inventario     = pln.id_unico
            LEFT JOIN gf_unidad_factor   AS unf ON pln.unidad              = unf.id_unico
            WHERE     cnp.id_unico IS NOT NULL 
            and cnp.compania = $compania 
            AND (pln.codigo_barras LIKE '%$codigo%'
            OR  cnp.nombre LIKE '%$codigo%'
            OR  pln.nombre LIKE '%$codigo%'
            OR  pln.codi LIKE '%$codigo%') LIMIT 20";
    $result = $mysqli->query($query);
    $data = array();
    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            $id_pi  = $row['id_pi'];
            #* Cantidad 
            $cantidad   = 0;
            $n_entrada  = 0;
            $n_salida   = 0;
            #*Entrada
            $ent = $con->Listar("SELECT   SUM(dtm.cantidad) 
                        FROM      gf_detalle_movimiento  dtm
                        LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                        LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                        LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                        LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                        WHERE (dtm.planmovimiento= $id_pi)
                        AND   (pro.baja IS NULL OR pro.baja = 0)
                        AND   (tpm.clase = 2)");
            if(count($ent)>0){
                $n_entrada = $ent[0][0];
            }
            
            #Salida 
            $sal = $con->Listar("SELECT   SUM(dtm.cantidad) 
                        FROM      gf_detalle_movimiento  dtm
                        LEFT JOIN gf_movimiento_producto mpr ON mpr.detallemovimiento = dtm.id_unico
                        LEFT JOIN gf_producto            pro ON mpr.producto          = pro.id_unico
                        LEFT JOIN gf_movimiento          mov ON dtm.movimiento        = mov.id_unico
                        LEFT JOIN gf_tipo_movimiento     tpm ON mov.tipomovimiento    = tpm.id_unico
                        WHERE (dtm.planmovimiento= $id_pi)
                        AND   (pro.baja IS NULL OR pro.baja = 0)
                        AND   (tpm.clase = 3)");
            if(count($sal)>0){
                $n_salida =$sal[0][0];
            }
            $cantidad   = $n_entrada -$n_salida;
            $data[] = $row['nombre_concepto'].' - Cantidad:'.$cantidad;
        }
        echo json_encode($data);
    }
    break;
    case 49:
        $codigo     =  $_REQUEST['codigo'];
        $codigo_s   = explode(" - Cantidad:", $codigo);
        $codigo     = $codigo_s[0];
        IF(!empty($codigo)){
            $query ="SELECT    DISTINCTROW cnp.id_unico as id_con, cnp.nombre as nombre_concepto, unf.nombre
            FROM      gp_concepto_tarifa AS cont
            LEFT JOIN gp_concepto        AS cnp ON cont.concepto           = cnp.id_unico
            LEFT JOIN gf_plan_inventario AS pln ON cnp.plan_inventario     = pln.id_unico
            LEFT JOIN gf_unidad_factor   AS unf ON pln.unidad              = unf.id_unico
            WHERE     cnp.id_unico IS NOT NULL 
            and cnp.compania = $compania 
            AND cnp.nombre = '$codigo'";

        $result = $mysqli->query($query);
         if($result->num_rows > 0){
          $row = $result->fetch_assoc();
           $id=$row['id_con'];
           echo $id;
         }else{
           echo 0;
         } 
        }
    break;
    case 50:
        $codigo =  $_REQUEST['codigo'];
        $codigo_s   = explode(" - Cantidad:", $codigo);
        $codigo     = $codigo_s[0];
        IF(!empty($codigo)){
            $query = $con->Listar("SELECT    DISTINCTROW cnp.id_unico, cnp.nombre as nombre_concepto, unf.nombre
            FROM      gp_concepto_tarifa AS cont
            LEFT JOIN gp_concepto        AS cnp ON cont.concepto           = cnp.id_unico
            LEFT JOIN gf_plan_inventario AS pln ON cnp.plan_inventario     = pln.id_unico
            LEFT JOIN gf_unidad_factor   AS unf ON pln.unidad              = unf.id_unico
            WHERE     cnp.id_unico IS NOT NULL 
            and cnp.compania = $compania 
            AND pln.codigo_barras = '$codigo'");
            IF(count($query)>0){
                echo trim($query[0][1]);
            } else {
                echo 0;
            }
        }
    break;
    #* Verificar Datos Factura E
    case 51:
        $rta        = 0;
        $html       = '';
        $tercero    = $_REQUEST['tercero'];
        $tipo_f     = $_REQUEST['tipo_f'];
        $dtf        = $con->Listar("SELECT facturacion_e 
            FROM gp_tipo_factura WHERE id_unico =$tipo_f");
        if($dtf[0][0]==1){
            $rowf =$con->Listar("SELECT t.id_unico, t.email , tl.valor, dr.ciudad_direccion 
                FROM gf_tercero t 
                LEFT JOIN gf_telefono tl on tl.tercero = t.id_unico 
                LEFT JOIN gf_direccion dr ON dr.tercero = t.id_unico 
                WHERE t.id_unico = $tercero");
            if(trim($rowf[0][1])=='' || empty($rowf[0][1])){
                $rta  += 1;
                $html .= 'Por favor configurar el correo electrónico del tercero asociado a la factura'.'<br/>';
            }
            if(strlen($rowf[0][2])<7 ||strlen($rowf[0][2])>10){
                $rta  += 1;
                $html .= 'Por favor verificar el teléfono del tercero asociado a la factura, solo debe tener entre 7 y 10 caracteres'.'<br/>';
            }
            if (ctype_digit($rowf[0][2])) {}else{
                $rta  += 1;
                $html .= 'Por favor verificar el teléfono del tercero asociado a la factura, solo debe contener números'.'<br/>';
            }  
        }
        $datos = array("html"=>$html,"rta"=>$rta);
        echo json_encode($datos);
        
        
    break;
    
     #* Buscar Plan Inventario Almacen CB
    case 52:
        $codigo =  $_GET['term'];
        $query = "SELECT    DISTINCTROW pln.id_unico, CONCAT_WS(' ',pln.codi, pln.nombre) as nombre_concepto, 
            unf.nombre
            FROM      gf_plan_inventario AS pln 
            LEFT JOIN gf_unidad_factor   AS unf ON pln.unidad  = unf.id_unico
            WHERE  pln.tienemovimiento = 2 AND pln.compania = $compania 
            AND (pln.codigo_barras LIKE '%$codigo%'
            OR  pln.nombre LIKE '%$codigo%'
            OR  pln.codi LIKE '%$codigo%')";
    $result = $mysqli->query($query);
    $data = array();
    if($result->num_rows > 0){
        while ($row = $result->fetch_assoc()){
            $data[] = $row['nombre_concepto'];
        }
        echo json_encode($data);
    }
    break;
    case 53:
        $codigo =  $_REQUEST['codigo'];
        IF(!empty($codigo)){
            $query = $con->Listar("SELECT    DISTINCTROW pln.id_unico, 
                CONCAT_WS(' ',pln.codi, pln.nombre) as nombre_concepto, unf.nombre
            FROM      gf_plan_inventario AS pln 
            LEFT JOIN gf_unidad_factor   AS unf ON pln.unidad              = unf.id_unico
            WHERE    pln.compania = $compania 
            AND CONCAT_WS(' ',pln.codi, pln.nombre) = '$codigo'");
            IF(count($query)>0){
                echo $query[0][0];
            } else {
                echo 0;
            }
        }
    break;
    case 54:
        $codigo =  $_REQUEST['codigo'];
        IF(!empty($codigo)){
            $query = $con->Listar("SELECT    DISTINCTROW pln.id_unico, 
                CONCAT_WS(' ',pln.codi, pln.nombre) as nombre_concepto, unf.nombre
            FROM      gf_plan_inventario AS pln 
            LEFT JOIN gf_unidad_factor   AS unf ON pln.unidad              = unf.id_unico
            WHERE     pln.compania = $compania 
            AND pln.codigo_barras = '$codigo'");
            IF(count($query)>0){
                echo $query[0][1];
            } else {
                echo 0;
            }
        }
    break;
    
    #* Modificar Factura Electrónica
    case 55:
        $id     = $_REQUEST['id'];
        $cufe   = $_REQUEST['cufe'.$id];
        $zipid  = $_REQUEST['zipid'.$id];
        $fecha  = $_REQUEST['fecha'.$id];
        $hora   = $_REQUEST['hora'.$id];
        $hora   = date("g:i A",strtotime($hora));

       
        $sql_cons ="UPDATE `gp_factura`
            SET `cufe`=:cufe,
            `zip_id`=:zip_id,
            `issue_date`=:issue_date,
            `issue_time`=:issue_time
            WHERE `id_unico`=:id_unico ";
            $sql_dato = array(
                array(":cufe",$cufe),
                array(":zip_id",$zipid),
                array(":issue_date",$fecha),
                array(":issue_time",$hora),
                array(":id_unico",$id),
            );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        if(empty($resp)){
            echo 0;
        } else {
            echo 2;
        }
    break;
    #* Revisar si el tipo de factura tiene asociado Tipo Cambio
    case 56:
        $tipo = $_REQUEST['tipo'];
        $html = '';
        $rta  = 0;
        $id   = '';
        $row  = $con->Listar("SELECT tc.nombre, tc.id_unico  FROM gp_tipo_factura tf 
            LEFT JOIN gf_tipo_cambio tc ON tf.tipo_cambio = tc.id_unico 
            WHERE tf.id_unico = $tipo AND tc.id_unico IS NOT NULL");
        if(count($row)>0){
            $html = $row[0][0];
            $rta  = 1;
            $id   = $row[0][1];
        }
        $datos = array("msj"=>$html,"rta"=>$rta,"id"=>$id);
        //var_dump($datos);
        echo json_encode($datos);
    break;
    #Tarifas 
    case 57:
        $concepto = $_REQUEST['concepto'];
        $unidad   = $_REQUEST['unidad'];
        $html = "";
        $html .= "";
        $data = $con->Listar("SELECT    gtf.valor, gtr.id_unico
                    FROM      gp_concepto_tarifa AS gtr
                    LEFT JOIN gf_elemento_unidad AS geu ON gtr.elemento_unidad = geu.id_unico
                    LEFT JOIN gp_tarifa          AS gtf ON gtr.tarifa          = gtf.id_unico
                    WHERE     gtr.concepto       = $concepto
                    AND       geu.unidad_empaque = $unidad ORDER BY  gtf.valor DESC");
        if(count($data) > 0){
            for ($i=0; $i < count($data); $i++){ 
                $html .= "<option value='".$data[$i][0].'/'.$data[$i][1]."'>".$data[$i][0]."</option>";
            }
        }
        echo $html;
    break;
    #* Tipo Inventario
    case 58:
        $concepto = $_REQUEST['concepto'];
        $ti       = $con->Listar("SELECT pi.tipoinventario FROM gp_concepto c 
            LEFT JOIN gf_plan_inventario pi ON c.plan_inventario = pi.id_unico
            WHERE c.id_unico= $concepto ");
        echo $ti[0][0];
    break;

    #* Usuarios
    case 59:
        $referencia =  $_REQUEST['term'];
        $query = "SELECT uvms.id_unico, uv.codigo_ruta, CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos, t.razonsocial, t.numeroidentificacion)  FROM gp_unidad_vivienda uv 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        WHERE uvms.id_unico IS NOT NULL
        AND (t.razonsocial LIKE '%$referencia%' 
        OR CONCAT_WS(' ',t.nombreuno,
            t.nombredos,
            t.apellidouno,
            t.apellidodos) LIKE '%$referencia%' 
        OR t.numeroidentificacion LIKE '%$referencia%'
        OR uv.codigo_ruta LIKE '%$referencia%')
            LIMIT 20";
        $result = $mysqli->query($query);
        $option = '';
        if($result->num_rows > 0){
            while ($row = $result->fetch_row()){
                $option .= '<option value="'.$row[0].'">'.$row[1].' - '.$row[2].'</option>';
            }
        }
        echo $option;
    break;

    case 60:
        $id_uvms =  $_REQUEST['uvms'];
        $query = "SELECT t.id_unico, CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos, t.razonsocial, t.numeroidentificacion)  FROM gp_unidad_vivienda uv 
        LEFT JOIN gp_unidad_vivienda_servicio uvs ON uvs.unidad_vivienda = uv.id_unico 
        LEFT JOIN gp_unidad_vivienda_medidor_servicio uvms ON uvms.unidad_vivienda_servicio = uvs.id_unico 
        LEFT JOIN gf_tercero t ON uv.tercero = t.id_unico 
        WHERE uvms.id_unico = $id_uvms";
        $result = $mysqli->query($query);
        $option = '';
        if($result->num_rows > 0){
            while ($row = $result->fetch_row()){
                $option .= '<option value="'.$row[0].'">'.$row[1].'</option>';
            }
        }
        echo $option;
    break;
   
}
