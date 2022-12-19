<?php
require 'Conexion/ConexionPDO.php';
require 'Conexion/conexion.php';
require 'jsonPptal/funcionesPptal.php';

@session_start();
$con        = new ConexionPDO();
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$panno      = $_SESSION['anno'];
$usuario_t  = $_SESSION['usuario_tercero'];
$anno       = anno($panno);
$fechaI     ='2018-12-01';
$fechaF     ='2018-12-31';
#** No Automaticas
$rowfc          = $con->Listar("SELECT DISTINCT f.id_unico, tr.id_unico, 
    tc.id_unico, tc.comprobante_pptal, tc.tipo_comp_hom , tr.cuenta_bancaria, 
    f.vendedor , f.fecha_factura  
FROM gp_factura f 
LEFT JOIN gp_tipo_factura tf ON f.tipofactura = tf.id_unico 
LEFT JOIN gp_tipo_pago tr ON tf.tipo_recaudo = tr.id_unico 
LEFT JOIN gf_tipo_comprobante tc ON tr.tipo_comprobante = tc.id_unico 
WHERE  f.fecha_factura BETWEEN '$fechaI' AND '$fechaF' 
AND f.parametrizacionanno = $panno");
$rec = 0;
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
                    $rec =reconstruirComprobantesFactura($factura);
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
                $rec  = reconstruirComprobantesFactura($factura);
            }

        }
    }
    echo 'Factura'.$rec.'<br/>';
   
}



$rowfc  = $con->Listar("SELECT     pg.id_unico,
    pg.tipo_pago,
    pg.numero_pago, 
    pg.fecha_pago, 
    pg.responsable 
FROM        gp_pago pg
WHERE pg.parametrizacionanno = $panno 
AND pg.fecha_pago BETWEEN '$fechaI' AND '$fechaF' ");
for ($i = 0; $i < count($rowfc); $i++) {
    $pago       = $rowfc[$i][0];
    $tipoPago   = $rowfc[$i][1];
    $numeroC    = $rowfc[$i][2];
    $fecha      = $rowfc[$i][3];
    $responsable= $rowfc[$i][4];
    $numeroPp   = $rowfc[$i][2];
    #************ Registrar Comprobante CNT***************#
    $tipoComprobanteCnt = $con->Listar("select tipo_comprobante from gp_tipo_pago where id_unico=$tipoPago");
    if(!empty($tipoComprobanteCnt[0][0])){
        #Consultamos el ultimo numero de acuerdo al tipo de comprobante
        $tipocnt =$tipoComprobanteCnt[0][0];
        #Descripción del comprobante
        $descripcion= '"Comprobante de recaudo factura"';
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
            $descripcion= '"Comprobante de causación recaudo factura"';
            #Insertamos el comprobante
            $sqlInsertC="insert into gf_comprobante_cnt(numero,fecha,descripcion,tipocomprobante,parametrizacionanno,tercero,estado,compania) "
                    . "values('$numeroCausacion','$fecha',$descripcion,$tipocau,$panno,$responsable,'1',$compania)";
            $resultInsertC=$mysqli->query($sqlInsertC);
            #Consultamos el ultimo comprobante ingresado
            $idCau=$con->Listar("select max(id_unico) from gf_comprobante_cnt where tipocomprobante=$tipocau and numero=$numeroCausacion");
            $id_causacion = $idCau[0][0];

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
    $sql_cons ="UPDATE  `gp_detalle_pago`
        SET `detallecomprobante` =:detallecomprobante
        WHERE `pago`=:pago ";
        $sql_dato = array(
            array(":detallecomprobante",NULL),
            array(":pago",$pago),
        );
        $resp = $con->InAcEl($sql_cons,$sql_dato);
        $recon = 0;
        if(empty($resp)){
            $ec = eliminardetallescnt($id_causacion);
            if($ec==1){
                $ecn = eliminardetallescnt($id_cnt);
                if($ecn==1){
                    $epp = eliminardetallespptal($id_pptal);
                    if($epp==1){
                        $recon = 1;
                    }
                }
            }
        }
    $reg=registrarDetallesPago($pago,$id_cnt,$id_pptal,$id_causacion);
    
    echo 'Recaudo'.$reg.'<br/>';
}