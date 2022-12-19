<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Relacion_Egresos_Con_Presupuesto.xls");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php"); 
session_start();
#************Datos Recibe************#
$fechaIn = $_POST['fechaini'];
$fechaFi= $_POST['fechafin'];
$fechaI = fechaC($fechaIn);
$fechaF = fechaC($fechaFi);
if(empty($_POST['sltTi'])){
    $terceroI =0;
} else {
    $terceroI =$_POST['sltTi'];
}

if(empty($_POST['sltTf']))    {
    $terceroF =9;
} else {
    $terceroF =$_POST['sltTf'];
}

#************Datos Compañia************#
$compania = $_SESSION['compania'];
$sqlC = "SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania";
$resultC = $mysqli->query($sqlC);
$rowC = mysqli_fetch_row($resultC);
$razonsocial = $rowC[1];
$nombreIdent = $rowC[2];
$numeroIdent = $rowC[3];
$direccinTer = $rowC[4];
$telefonoTer = $rowC[5];



#****************Verificacar en donde se aplican las retenciones***************#
 $cc = "SELECT DISTINCT clasecontable FROM gf_tipo_comprobante WHERE retencion =1";
$cc = $mysqli->query($cc);
$cc = mysqli_fetch_row($cc);
$clase = $cc[0];
$ids="";
#****Si la clase contable es cuenta por pagar****#
if($clase ==13){
     #**********Buscar Egresos Entre valores recibidos************###
    IF($terceroF==9){
        $egresos = "SELECT cp.id_unico 
            FROM gf_comprobante_cnt cp
            LEFT JOIN gf_tipo_comprobante tp ON cp.tipocomprobante = tp.id_unico
            LEFT JOIN gf_tercero t ON t.id_unico = cp.tercero 
            LEFT JOIN gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tp.comprobante_pptal 
            WHERE fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND t.numeroidentificacion >= '$terceroI' 
           AND tp.clasecontable = 14 AND tcpp.vigencia_actual =1  AND tp.comprobante_pptal IS NOT NULL ORDER BY cp.fecha, cp.numero ASC ";
    } else {
        $egresos = "SELECT cp.id_unico 
            FROM gf_comprobante_cnt cp
            LEFT JOIN gf_tipo_comprobante tp ON cp.tipocomprobante = tp.id_unico
            LEFT JOIN gf_tercero t ON t.id_unico = cp.tercero 
            LEFT JOIN gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tp.comprobante_pptal 
            WHERE fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND t.numeroidentificacion BETWEEN '$terceroI' AND '$terceroF' 
           AND tp.clasecontable = 14 AND tcpp.vigencia_actual =1 AND tp.comprobante_pptal IS NOT NULL ORDER BY cp.fecha, cp.numero ASC ";
    }
  
    $egresos = $mysqli->query($egresos);
    if(mysqli_num_rows($egresos)>0){
        while($row = mysqli_fetch_row($egresos)){
            #****Buscar la cuenta por pagar*****#
            $comprobante = $row[0];
            $comp = "SELECT DISTINCT dca.comprobante 
                   FROM
                     gf_detalle_comprobante dc
                   LEFT JOIN
                     gf_detalle_comprobante dca ON dc.detalleafectado = dca.id_unico 
                   WHERE
                     dc.comprobante = '$comprobante' ";
             $comp1 = $mysqli->query($comp);
             if(mysqli_num_rows($comp1)>0){
             while($compcn = mysqli_fetch_row($comp1)){
                #***Array ids cuentas por pagar
                 if(!empty($compcn[0])){
                 $ids =$ids.','.$compcn[0];
                 }
             }  
            }
        }    
    }
}

#****Si la clase es egreso****#
elseif($clase ==14){
#**********Buscar Egresos Entre valores recibidos************###
    IF($terceroF==9){
        $egresos = "SELECT cp.id_unico 
            FROM gf_comprobante_cnt cp
            LEFT JOIN gf_tipo_comprobante tp ON cp.tipocomprobante = tp.id_unico
            LEFT JOIN gf_tercero t ON t.id_unico = cp.tercero 
            LEFT JOIN gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tp.comprobante_pptal 
            WHERE fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND t.numeroidentificacion >= '$terceroI' 
           AND tp.clasecontable = 14 AND tcpp.vigencia_actual =1 AND tp.comprobante_pptal IS NOT NULL ORDER BY cp.fecha, cp.numero ASC ";
    } else {
        $egresos = "SELECT cp.id_unico 
            FROM gf_comprobante_cnt cp
            LEFT JOIN gf_tipo_comprobante tp ON cp.tipocomprobante = tp.id_unico
            LEFT JOIN gf_tercero t ON t.id_unico = cp.tercero 
            LEFT JOIN gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tp.comprobante_pptal 
            WHERE fecha BETWEEN '$fechaI' AND '$fechaF' 
            AND t.numeroidentificacion BETWEEN '$terceroI' AND '$terceroF' 
           AND tp.clasecontable = 14 AND tcpp.vigencia_actual =1 AND tp.comprobante_pptal IS NOT NULL ORDER BY cp.fecha, cp.numero ASC ";
    }
    $egresos = $mysqli->query($egresos);
    if(mysqli_num_rows($egresos)>0){
        while($row = mysqli_fetch_row($egresos)){
           $comprobante = $row[0];
           #***Array ids Egresos
           if(!empty($comprobante)){
             $ids =$ids.','.$comprobante;
           }
        }    
    }
}


$numtiporetencion= "SELECT COUNT(DISTINCT tiporetencion)  FROM gf_retencion WHERE comprobante IN (0".$ids.")";
$numtiporetencion = $mysqli->query($numtiporetencion);
$numtiporetencion = mysqli_fetch_row($numtiporetencion);
$numtiporetencion = $numtiporetencion[0];
$campos=0;
if($numtiporetencion==0){
    $campos=1;
} else {
    $campos = $numtiporetencion;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Relación Egresos Con Presupuesto</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="<?php echo $campos+12?>" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>RELACION EGRESOS CON PRESUPUESTO
           <br/> Fechas entre <?php echo $fechaIn.' y '.$fechaFi;?>
           <br/>&nbsp;

        </th>
  </tr>
  <tr>
      <td rowspan="2" align="center"><strong>FECHA</strong></td>
    <td rowspan="2" align="center"><strong>TIPO EGRESO</strong></td>
    <td rowspan="2" align="center"><strong>NÚMERO EGRESO</strong></td>
    <td rowspan="2" align="center"><strong>TIPO CUENTA POR PAGAR</strong></td>
    <td rowspan="2" align="center"><strong>NÚMERO CUENTA POR PAGAR</strong></td>
    <td rowspan="2" align="center"><strong>BENEFICIARIO</strong></td>
    <td rowspan="2" align="center"><strong>CONCEPTO</strong></td>
    <td rowspan="2" align="center"><strong>VALOR NETO</strong></td>
    <td colspan="<?php echo $campos?>"><strong><center>DESCUENTOS</center></strong></td>
    <td rowspan="2" align="center"><strong>TOTAL DESCUENTOS</strong></td>
    <td rowspan="2" align="center"><strong>NETO A PAGAR</strong></td>
    <td rowspan="2" align="center"><strong>BANCO</strong></td>
    <td rowspan="2" align="center"><strong>N° CHEQUE</strong></td>
  </tr>
    <tr>
        <?php 
        $tiporetencion= "SELECT DISTINCT r.tiporetencion, tr.porcentajeaplicar, tr.nombre 
                                            FROM gf_retencion r 
                                            LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico 
                                            WHERE comprobante IN (0".$ids.") ORDER BY tr.nombre ASC";
         $tiporetencion = $mysqli->query($tiporetencion);
         $i=0;
         $arrayTipoRetencion  = array();
         
         if(mysqli_num_rows($tiporetencion)>0){
             
             $tiporet ="";
             while ($row1 = mysqli_fetch_row($tiporetencion)) {
                echo "<td><strong>$row1[2] %$row1[1]</strong></td>";
                $tiporet[$i] = $row1[0];
                if(in_array($row1[0], $arrayTipoRetencion)) {

                } else {
                    array_push ( $arrayTipoRetencion , $row1[0] );
                }
                $i++;
             }
         }
       
        ?>
    </tr>
    <?php 
    #****************Verificacar en donde se aplican las retenciones***************#
    $cc = "SELECT DISTINCT clasecontable FROM gf_tipo_comprobante WHERE retencion =1";
    $cc = $mysqli->query($cc);
    $cc = mysqli_fetch_row($cc);
    $clase = $cc[0];
    $ids="";
    #****Si la clase contable es cuenta por pagar****#
    if($clase ==13){
        #**********Buscar Egresos Entre valores recibidos************###
       if($terceroF==9){
           $egresos = "SELECT 
                       cp.id_unico, 
                       DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
                       cp.numero, 
                       tcpp.codigo , 
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
                            t.apellidodos)) AS NOMBRE, t.numeroidentificacion, t.digitoverficacion , cp.descripcion 
               FROM gf_comprobante_cnt cp
               LEFT JOIN gf_tipo_comprobante tp ON cp.tipocomprobante = tp.id_unico
               LEFT JOIN gf_tercero t ON t.id_unico = cp.tercero 
              LEFT JOIN gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tp.comprobante_pptal 
               WHERE fecha BETWEEN '$fechaI' AND '$fechaF' 
               AND t.numeroidentificacion >= '$terceroI' 
              AND tp.clasecontable = 14 AND tcpp.vigencia_actual =1 AND tp.comprobante_pptal IS NOT NULL ORDER BY cp.fecha, cp.numero ASC ";
       } else {
           $egresos = "SELECT 
                       cp.id_unico, 
                       DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
                       cp.numero, 
                       tcpp.codigo , 
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
                            t.apellidodos)) AS NOMBRE, t.numeroidentificacion, t.digitoverficacion , cp.descripcion 
               FROM gf_comprobante_cnt cp
               LEFT JOIN gf_tipo_comprobante tp ON cp.tipocomprobante = tp.id_unico
               LEFT JOIN gf_tercero t ON t.id_unico = cp.tercero 
                LEFT JOIN gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tp.comprobante_pptal 
               WHERE fecha BETWEEN '$fechaI' AND '$fechaF' 
               AND t.numeroidentificacion BETWEEN '$terceroI' AND '$terceroF' 
              AND tp.clasecontable = 14 AND tcpp.vigencia_actual =1 AND tp.comprobante_pptal IS NOT NULL ORDER BY cp.fecha, cp.numero ASC ";
       }
       
    $egresos = $mysqli->query($egresos);
    if(mysqli_num_rows($egresos)>0){
        $x=0;
        while($row = mysqli_fetch_row($egresos)){ 
            if(empty($row[6])){
                $tercero = ucwords(mb_strtolower($row[4])).' - '.$row[5];
            } else {
                $tercero = ucwords(mb_strtolower($row[4])).' - '.$row[5].' '.$row[6];
            }
            ##***Imprimir los datos del egreso?>
            <tr>
                <td  align="left"><?php echo $row[1]?></td>
                <td align="left"><?php echo mb_strtoupper($row[3])?></td>
                <td align="left"><?php echo $row[2]?></td>
            <?php    
            #****Buscar la cuenta por pagar*****#
            $comprobante = $row[0];
            $comp = "SELECT DISTINCT
                     dca.comprobante, cpcnt.numero, tcpp.codigo 
                   FROM
                     gf_detalle_comprobante dc
                   INNER JOIN
                     gf_detalle_comprobante dca ON dc.detalleafectado = dca.id_unico 
                   LEFT JOIN 
                     gf_comprobante_cnt cpcnt ON cpcnt.id_unico = dca.comprobante  
                   LEFT JOIN 
                      gf_tipo_comprobante tc ON  tc.id_unico = cpcnt.tipocomprobante 
                   LEFT JOIN 
                      gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tc.comprobante_pptal 
                   WHERE
                    tcpp.vigencia_actual =1 AND dc.comprobante = '$comprobante' ";
             $comp1 = $mysqli->query($comp);
            $numeroCxp ="";
            $tipoCxp="";
            $idcnt =0;
             if(mysqli_num_rows($comp1)>0){
             while($compcn = mysqli_fetch_row($comp1)){
                #***Array ids cuentas por pagar
                 if(!empty($compcn[0])){
                    $numeroCxp = $numeroCxp.'<br/>'.$compcn[1];
                    $tipoCxp = $tipoCxp.'<br/>'.$compcn[2];
                    $idcnt = $idcnt.' , '.$compcn[0];
                 }
             }
            } else {
                #****Buscar la cuenta por pagar por el detalle pptal*****#
            
                $comp = "SELECT DISTINCT
                    cn.id_unico, cn.numero, tcpp.codigo 
                 FROM
                    gf_detalle_comprobante dc
                 LEFT JOIN
                      gf_detalle_comprobante_pptal dca ON dc.detallecomprobantepptal = dca.id_unico 
                 LEFT JOIN 
                         gf_detalle_comprobante dcp ON dcp.detallecomprobantepptal = dca.id_unico 
                 LEFT JOIN 
                         gf_comprobante_cnt cn ON dcp.comprobante = cn.id_unico 
                 LEFT JOIN 
                         gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                 LEFT JOIN 
                      gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tc.comprobante_pptal 
                   WHERE
                    tcpp.vigencia_actual =1 AND 
                  tc.clasecontable = 13 AND dc.comprobante = '$comprobante' ";
                $comp1 = $mysqli->query($comp);
               $numeroCxp ="";
               $tipoCxp="";
               $idcnt =0;
                if(mysqli_num_rows($comp1)>0){
                   while($compcn = mysqli_fetch_row($comp1)){
                      #***Array ids cuentas por pagar
                       if(!empty($compcn[0])){
                          $numeroCxp = $numeroCxp.'<br/>'.$compcn[1];
                          $tipoCxp = $tipoCxp.'<br/>'.$compcn[2];
                          $idcnt = $idcnt.' , '.$compcn[0];
                       }
                   }
                }
            } 
            #*****Imprimir Cuenta por pagar ?>
           <td align="left"><?php echo mb_strtoupper($tipoCxp)?></td>
          <td align="left"><?php echo $numeroCxp?></td>
          <td align="left"><?php echo $tercero?></td>
          <td align="left"><?php echo $row[7]?></td>
          <?php 
          #*********Buscar el valor de la cuenta por pagar***********###
            $totalcomp="SELECT DISTINCT dtc.id_unico, 
                                cnt.naturaleza, 
                                dtc.valor 
                                FROM gf_detalle_comprobante dtc 
                LEFT JOIN gf_cuenta cnt ON dtc.cuenta = cnt.id_unico 
                WHERE dtc.comprobante IN($idcnt) ";
            $totalcomp = $mysqli->query($totalcomp);
            $sumar=0;
            if(mysqli_num_rows($totalcomp)>0){
                while($rowsum = mysqli_fetch_row($totalcomp)) {
                    ##########DEBITOS###########
                    if($rowsum[1] == 1) {
                        if($rowsum[2] >= 0){
                            $sumar += $rowsum[2];
                        }
                    }else if($rowsum[1] == 2){
                        if($rowsum[2] <= 0){
                            $x = (float) substr($rowsum[2],'1');
                            $sumar += $x;
                        }
                    }
                }
            }
            #*****Imprimir Neto
            ?>
          <td><?php echo number_format($sumar,2,'.',',')?></td>
          <?php
          #*********Buscar el valor retencion de la cuenta por pagar***********###
          $totaldescuentos =0;
         if($numtiporetencion==0){
                    echo "<td></td>";
            } else {
                
                for($j=0; $j<count($arrayTipoRetencion); $j++ ){
                    $valorRetencion =0;
                    $valorR = "SELECT SUM(valorretencion) FROM gf_retencion WHERE tiporetencion = $arrayTipoRetencion[$j] AND comprobante IN($idcnt)";
                    $valorR = $mysqli->query($valorR); 
                    $valorR = mysqli_fetch_row($valorR);
                    if($valorR[0]==""){
                        $valorRetencion=0;
                    } else {
                        $valorRetencion = $valorR[0];
                    }
                    $totaldescuentos +=$valorRetencion;
                    #*****Imprimir Valor Retencion?>
                    <td><?php echo number_format($valorRetencion,2,'.',',')?></td>
                 <?php   
                }
           }
            #*********Buscar el valor del egreso***********###
            $totalE="SELECT DISTINCT dtc.id_unico, 
                                cnt.naturaleza, 
                                dtc.valor 
                                FROM gf_detalle_comprobante dtc 
                LEFT JOIN gf_cuenta cnt ON dtc.cuenta = cnt.id_unico 
                WHERE dtc.comprobante IN($comprobante) ";
            $totalE = $mysqli->query($totalE);
            $totalEgr=0;
            if(mysqli_num_rows($totalE)>0){
                while($rowsumE = mysqli_fetch_row($totalE)) {
                    ##########DEBITOS###########
                    if($rowsumE[1] == 1) {
                        if($rowsumE[2] >= 0){
                            $totalEgr += $rowsumE[2];
                        }
                    }else if($rowsumE[1] == 2){
                        if($rowsumE[2] <= 0){
                            $xE = (float) substr($rowsumE[2],'1');
                            $totalEgr += $xE;
                        }
                    }
                }
            }
          
          $totalapagar =$totalEgr;
          #***Imprimir total descuentos y total a pagar
          ?>
          <td><?php echo number_format($totaldescuentos,2,'.',',')?></td>
          <td><?php echo number_format($totalapagar,2,'.',',')?></td>
          <?php ######Buscar Bancos 
          $cb = "SELECT DISTINCT t.razonsocial FROM 
                    gf_detalle_comprobante dc 
                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico      
                    LEFT JOIN gf_cuenta_bancaria cb ON c.id_unico = cb.cuenta 
                    LEFT JOIN gf_tercero t ON t.id_unico = cb.banco 
                    WHERE  (c.clasecuenta = 11 OR c.clasecuenta = 12)  AND dc.comprobante = '$comprobante' ORDER BY dc.id_unico ASC ";
          $cb = $mysqli->query($cb);
          $banco = "";
          if(mysqli_num_rows($cb)>0){
              while ($row2 = mysqli_fetch_row($cb)) {
                  $banco = $banco.'<br/>'.$row2[0];
              }
          }?>
          <td><?php echo $banco;?></td>
           <?php ######Buscar Cheques 
          $cb = "SELECT DISTINCT dcv.id_unico, dcv.numero 
                    FROM gf_detalle_comprobante_mov dcv 
                    LEFT JOIN gf_detalle_comprobante dc ON dcv.comprobantecnt = dc.id_unico 
                    WHERE  dc.comprobante = '$comprobante' ORDER BY dc.id_unico ASC";
          $cb = $mysqli->query($cb);
          $cheque = "";
          if(mysqli_num_rows($cb)>0){
              while ($row2 = mysqli_fetch_row($cb)) {
                  $cheque = $cheque.'<br/>'.$row2[1];
              }
          }?>
          <td><?php echo $cheque;?></td>
          
         </tr>   
 <?php  }    
    }
}

#****Si la clase es egreso****#
elseif($clase ==14){
     #**********Buscar Egresos Entre valores recibidos************###
       if($terceroF==9){
           $egresos = "SELECT 
                       cp.id_unico, 
                       DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
                       cp.numero, 
                       tp.sigla , 
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
                            t.apellidodos)) AS NOMBRE, t.numeroidentificacion, t.digitoverficacion , cp.descripcion 
               FROM gf_comprobante_cnt cp
               LEFT JOIN gf_tipo_comprobante tp ON cp.tipocomprobante = tp.id_unico
               LEFT JOIN gf_tercero t ON t.id_unico = cp.tercero 
              LEFT JOIN gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tp.comprobante_pptal 
               WHERE fecha BETWEEN '$fechaI' AND '$fechaF' 
               AND t.numeroidentificacion >= '$terceroI' 
              AND tp.clasecontable = 14 AND tcpp.vigencia_actual =1 AND tp.comprobante_pptal IS NOT NULL ORDER BY cp.fecha, cp.numero ASC ";
       } else {
           $egresos = "SELECT 
                       cp.id_unico, 
                       DATE_FORMAT(cp.fecha,'%d/%m/%Y'), 
                       cp.numero, 
                       tp.sigla, 
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
                            t.apellidodos)) AS NOMBRE, t.numeroidentificacion, t.digitoverficacion , cp.descripcion 
               FROM gf_comprobante_cnt cp
               LEFT JOIN gf_tipo_comprobante tp ON cp.tipocomprobante = tp.id_unico
               LEFT JOIN gf_tercero t ON t.id_unico = cp.tercero 
                LEFT JOIN gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tp.comprobante_pptal 
               WHERE fecha BETWEEN '$fechaI' AND '$fechaF' 
               AND t.numeroidentificacion BETWEEN '$terceroI' AND '$terceroF' 
              AND tp.clasecontable = 14 AND tcpp.vigencia_actual =1 AND tp.comprobante_pptal IS NOT NULL ORDER BY cp.fecha, cp.numero ASC ";
       }
       
    $egresos = $mysqli->query($egresos);
    if(mysqli_num_rows($egresos)>0){
        $x=0;
        while($row = mysqli_fetch_row($egresos)){ 
            $idegreso = $row[0];
            if(empty($row[6])){
                $tercero = ucwords(mb_strtolower($row[4])).' - '.$row[5];
            } else {
                $tercero = ucwords(mb_strtolower($row[4])).' - '.$row[5].' '.$row[6];
            }
            ##***Imprimir los datos del egreso?>
            <tr>
                <td  align="left"><?php echo $row[1]?></td>
                <td align="left"><?php echo mb_strtoupper($row[3])?></td>
                <td align="left"><?php echo $row[2]?></td>
            <?php    
            #****Buscar la cuenta por pagar*****#
            $comprobante = $row[0];
            #****Buscar la cuenta por pagar por el detalle cnt*****#
            $comp = "SELECT DISTINCT
                     dca.comprobante, cpcnt.numero, tcpp.codigo 
                   FROM
                     gf_detalle_comprobante dc
                   INNER JOIN
                     gf_detalle_comprobante dca ON dc.detalleafectado = dca.id_unico 
                   LEFT JOIN 
                     gf_comprobante_cnt cpcnt ON cpcnt.id_unico = dca.comprobante  
                   LEFT JOIN 
                      gf_tipo_comprobante tc ON  tc.id_unico = cpcnt.tipocomprobante 
                   LEFT JOIN 
                      gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tc.comprobante_pptal 
                   WHERE
                    tcpp.vigencia_actual =1 AND 
                     dc.comprobante = '$comprobante' ";
             $comp1 = $mysqli->query($comp);
            $numeroCxp ="";
            $tipoCxp="";
            $idcnt =0;
             if(mysqli_num_rows($comp1)>0){
                while($compcn = mysqli_fetch_row($comp1)){
                   #***Array ids cuentas por pagar
                    if(!empty($compcn[0])){
                       $numeroCxp = $numeroCxp.'<br/>'.$compcn[1];
                       $tipoCxp = $tipoCxp.'<br/>'.$compcn[2];
                       $idcnt = $idcnt.' , '.$compcn[0];
                    }
                }
            } else {
                #****Buscar la cuenta por pagar por el detalle pptal*****#
            
                $comp = "SELECT DISTINCT
                    cn.id_unico, cn.numero, tcpp.codigo 
                 FROM
                    gf_detalle_comprobante dc
                 LEFT JOIN
                      gf_detalle_comprobante_pptal dca ON dc.detallecomprobantepptal = dca.id_unico 
                 LEFT JOIN 
                         gf_detalle_comprobante dcp ON dcp.detallecomprobantepptal = dca.id_unico 
                 LEFT JOIN 
                         gf_comprobante_cnt cn ON dcp.comprobante = cn.id_unico 
                 LEFT JOIN 
                         gf_tipo_comprobante tc ON cn.tipocomprobante = tc.id_unico 
                  LEFT JOIN 
                      gf_tipo_comprobante_pptal tcpp ON tcpp.id_unico = tc.comprobante_pptal 
                   WHERE
                    tcpp.vigencia_actual =1 AND 
                  tc.clasecontable = 13 AND dc.comprobante = '$comprobante' ";
                $comp1 = $mysqli->query($comp);
               $numeroCxp ="";
               $tipoCxp="";
               $idcnt =0;
                if(mysqli_num_rows($comp1)>0){
                   while($compcn = mysqli_fetch_row($comp1)){
                      #***Array ids cuentas por pagar
                       if(!empty($compcn[0])){
                          $numeroCxp = $numeroCxp.'<br/>'.$compcn[1];
                          $tipoCxp = $tipoCxp.'<br/>'.$compcn[2];
                          $idcnt = $idcnt.' , '.$compcn[0];
                       }
                   }
                }
            } 
            #*****Imprimir Cuenta por pagar ?>
           <td align="left"><?php echo mb_strtoupper($tipoCxp)?></td>
          <td align="left"><?php echo $numeroCxp?></td>
          <td align="left"><?php echo $tercero?></td>
          <td align="left"><?php echo $row[7];?></td>
          <?php 
          #*********Buscar el valor de la cuenta por pagar***********###
            $totalcomp="SELECT DISTINCT dtc.id_unico, 
                                cnt.naturaleza, 
                                dtc.valor 
                                FROM gf_detalle_comprobante dtc 
                LEFT JOIN gf_cuenta cnt ON dtc.cuenta = cnt.id_unico 
                WHERE dtc.comprobante IN($idcnt) ";
            $totalcomp = $mysqli->query($totalcomp);
            $sumar=0;
            if(mysqli_num_rows($totalcomp)>0){
                while($rowsum = mysqli_fetch_row($totalcomp)) {
                    ##########DEBITOS###########
                    if($rowsum[1] == 1) {
                        if($rowsum[2] >= 0){
                            $sumar += $rowsum[2];
                        }
                    }else if($rowsum[1] == 2){
                        if($rowsum[2] <= 0){
                            $x = (float) substr($rowsum[2],'1');
                            $sumar += $x;
                        }
                    }
                }
            }
            #*****Imprimir Neto
            ?>
          <td><?php echo number_format($sumar,2,'.',',')?></td>
          <?php
          #*********Buscar el valor retencion del egreso***********###
          $totaldescuentos =0;
          if($numtiporetencion==0){
                    echo "<td></td>";
            } else {
                
                for($j=0; $j< count($arrayTipoRetencion); $j++ ){
                    $valorRetencion =0;
                    $valorR = "SELECT SUM(valorretencion) FROM gf_retencion WHERE tiporetencion = $arrayTipoRetencion[$j] AND comprobante IN($idegreso)";
                    $valorR = $mysqli->query($valorR); 
                    $valorR = mysqli_fetch_row($valorR);
                    if($valorR[0]==""){
                        $valorRetencion=0;
                    } else {
                        $valorRetencion = $valorR[0];
                    }
                    $totaldescuentos +=$valorRetencion;
                    #*****Imprimir Valor Retencion?>
                    <td><?php echo number_format($valorRetencion,2,'.',',')?></td>
                 <?php   
                }
            }
          
            #*********Buscar el valor del egreso***********###
            $totalE="SELECT DISTINCT dtc.id_unico, 
                                cnt.naturaleza, 
                                dtc.valor 
                                FROM gf_detalle_comprobante dtc 
                LEFT JOIN gf_cuenta cnt ON dtc.cuenta = cnt.id_unico 
                WHERE dtc.comprobante IN($comprobante) ";
            $totalE = $mysqli->query($totalE);
            $totalEgr=0;
            if(mysqli_num_rows($totalE)>0){
                while($rowsumE = mysqli_fetch_row($totalE)) {
                    ##########DEBITOS###########
                    if($rowsumE[1] == 1) {
                        if($rowsumE[2] >= 0){
                            $totalEgr += $rowsumE[2];
                        }
                    }else if($rowsumE[1] == 2){
                        if($rowsumE[2] <= 0){
                            $xE = (float) substr($rowsumE[2],'1');
                            $totalEgr += $xE;
                        }
                    }
                }
            }
          
          $totalapagar =$totalEgr -$totaldescuentos;
          #***Imprimir total descuentos y total a pagar
          ?>
          <td><?php echo number_format($totaldescuentos,2,'.',',')?></td>
          <td><?php echo number_format($totalapagar,2,'.',',')?></td>
          <?php ######Buscar Bancos 
          $cb = "SELECT DISTINCT t.razonsocial FROM 
                    gf_detalle_comprobante dc 
                    LEFT JOIN gf_cuenta c ON dc.cuenta = c.id_unico      
                    LEFT JOIN gf_cuenta_bancaria cb ON c.id_unico = cb.cuenta 
                    LEFT JOIN gf_tercero t ON t.id_unico = cb.banco 
                    WHERE  (c.clasecuenta = 11 OR c.clasecuenta = 12)  AND dc.comprobante = '$comprobante' ORDER BY dc.id_unico ASC ";
          $cb = $mysqli->query($cb);
          $banco = "";
          if(mysqli_num_rows($cb)>0){
              while ($row2 = mysqli_fetch_row($cb)) {
                  $banco = $banco.'<br/>'.$row2[0];
              }
          }?>
          <td><?php echo $banco;?></td>
           <?php ######Buscar Cheques 
          $cb = "SELECT DISTINCT dcv.id_unico, dcv.numero 
                    FROM gf_detalle_comprobante_mov dcv 
                    LEFT JOIN gf_detalle_comprobante dc ON dcv.comprobantecnt = dc.id_unico 
                    WHERE  dc.comprobante = '$comprobante' ORDER BY dc.id_unico ASC";
          $cb = $mysqli->query($cb);
          $cheque = "";
          if(mysqli_num_rows($cb)>0){
              while ($row2 = mysqli_fetch_row($cb)) {
                  $cheque = $cheque.'<br/>'.$row2[1];
              }
          }?>
          <td><?php echo $cheque;?></td>
         </tr>   
 <?php  }    
    }
    
}
 ?>   
    
</table>
</body>
</html>