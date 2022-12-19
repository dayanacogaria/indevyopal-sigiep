<?php
#####################################################################################
#     ************************** MODIFICACIONES **************************          #                                                                                                      Modificaciones
#####################################################################################
#16/08/2018 | Erica G. | Agrupar Por Fecha Exogenas Aguazul
#07/03/2018 | Erica G. | Archivo Creado
#####################################################################################
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
require_once("../jsonPptal/funcionesPptal.php");
require_once('../numeros_a_letras.php');
ini_set('max_execution_time', 0);
session_start();
$con    = new ConexionPDO(); 
$anno   = $_SESSION['anno'];

$exportar = $_REQUEST['tipo'];
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
$rowC = $con->Listar("SELECT    ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo, 
                c.rss, 
                c2.rss, d1.rss, d2.rss
FROM gf_tercero ter
LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN       gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
LEFT JOIN       gf_ciudad c ON ter.ciudadresidencia = c.id_unico 
LEFT JOIN       gf_ciudad c2 ON ter.ciudadidentificacion = c2.id_unico 
LEFT JOIN       gf_departamento d1 ON c.departamento = d1.id_unico 
LEFT JOIN       gf_departamento d2 ON c2.departamento = d2.id_unico 
WHERE ter.id_unico = $compania");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];
$ciudadR     = $rowC[0][7];
$ciudadI     = $rowC[0][8];
$deptR       = $rowC[0][9];
 $deptI       = $rowC[0][10];
#*******************************************************************************#

#   ************    Datos Recibe    ************    #
$annio      = $_POST['sltAnnio'];  
$claseR     = $_POST['claseR'];  
$nanno  = anno($annio);
#   ************    Nombre clase Retención    ************    #
$ncr = $con->Listar("SELECT UPPER(nombre) FROM gf_clase_retencion WHERE id_unico = $claseR");
$nombreRetencion = ($ncr[0][0]);

#*******************************************************************************#    

#*******************************************************************************#    

if(($ciudadR=='010' || $ciudadI =='010' || $ciudadI =='001' ||$ciudadR=='001' ) && ($deptR=='85' || $deptI=='85')){
    # ************** CONSULTA PRINCIPAL **************#
    $row = $con->Listar("SELECT          
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
         ti.nombre, 
         t.numeroidentificacion, 
         t.digitoverficacion, 
         cn.fecha, 
         tr.cod_exogena, tr.nombre, 
        (tr.porcentajeaplicar*1000), 
        SUM(r.retencionbase), 
        SUM(r.valorretencion),
        (SELECT DISTINCT dir.direccion FROM gf_direccion dir
        LEFT JOIN  gf_tercero tr ON tr.id_unico=dir.tercero
        WHERE dir.tercero=t.id_unico LIMIT 1) as direc,
         (SELECT DISTINCT te.valor FROM gf_telefono te
        LEFT JOIN  gf_tercero tr ON tr.id_unico=te.tercero
        WHERE tr.id_unico=t.id_unico LIMIT 1) as tel,
        '' as NA,
        '' as N2,
         ti.id_unico as tipodoc,
         (SELECT DISTINCT dep.rss FROM gf_departamento dep
         LEFT JOIN  gf_ciudad ci ON ci.departamento=dep.id_unico
         LEFT JOIN gf_direccion dir ON dir.ciudad_direccion=ci.id_unico
         WHERE dir.tercero=t.id_unico LIMIT 1) as departamento,
        (SELECT DISTINCT ci.rss FROM gf_ciudad ci
         LEFT JOIN  gf_direccion dri ON ci.id_unico=dri.ciudad_direccion
         WHERE dri.tercero=t.id_unico LIMIT 1) as ciudad,
        tr.concepto_pago as conceptoP,
        r.comprobanteretencion as comproP
        FROM gf_retencion r 
        LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico 
        LEFT JOIN gf_clase_retencion cl ON tr.claseretencion = cl.id_unico 
        LEFT JOIN gf_comprobante_cnt cn ON r.comprobante = cn.id_unico 
        LEFT JOIN gf_tercero t ON cn.tercero = t.id_unico
        LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
        WHERE tr.parametrizacionanno = $annio 
        AND cl.id_unico =  $claseR AND cn.parametrizacionanno = $annio 
        GROUP BY t.id_unico, tr.id_unico, cn.fecha");
     switch ($exportar){
        # *** Generar xls **#
        case 2:
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Informe_Exogenas_Municipal.xls");
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            echo '<html xmlns="http://www.w3.org/1999/xhtml">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo '<title>Informe Exógenas Municipal</title>';
            echo '</head>';
            echo '<body>';
            echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
            echo '<tr>';
            echo '<td colspan="16"><center><strong>PROCESO: GESTIÓN FINANCIERA </strong></center></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="16"><center><strong>SECRETARIA DE HACIENDA </strong></center></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td rowspan="3"></td>';
            echo '<td colspan="11" align="center"><strong>';
            echo '<br/>PAGOS Y RETENCIONES DE INDUSTRIA Y COMERCIO ICA';
            echo '<br/>&nbsp;</strong>';
            echo '</td>';
            echo '<td rowspan="3" colspan="3"></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="6">Código: A-GI-F22 </td>';
            echo '<td colspan="10">Versión: 01 </td>';
            echo '</tr>';
            echo '<tr>';
            echo "<td colspan='11'><center>Fecha de Creación: ".date('d/M/Y')."</center></td>";
            echo '</tr>';
            echo '<tr>';
            echo '<td><center><strong>VIGENCIA FISCAL REPORTADA </strong></center></td>';
            echo '<td><center><strong>APELLIDOS Y NOMBRES O RAZÓN SOCIAL</strong></center></td>';
            echo '<td><strong>TIPO DE DOCUMENTO<BR>
                                      1. TARJETA DE IDENTIDAD<BR>
                                      2. CEDULA DE CIUDADANÍA<BR>
                                      3. NIT<BR>
                                      4. CEDULA DE EXTRANJERÍA<BR>
                                      5. SIN IDENTIFICACIÓN DEL EXTERIOR</strong></td>';
            echo '<td><center><strong>NUMERO DE IDENTIFICACION </strong></center></td>';
            echo '<td><center><strong>DV </strong></center></td>';
            echo '<td><center><strong>DIRECCION  </strong></center></td>';
            echo '<td><center><strong>TELEFONO </strong></center></td>';
            echo '<td><center><strong>CODIGO DE DEPARTAMENTO </strong></center></td>';
            echo '<td><center><strong>CODIGO DE MUNICIPIO </strong></center></td>';
            echo '<td><strong>CONCEPTO DE PAGO<BR>
                                      1. PAGO NOMINA<BR>
                                      2. INDUSTRIA<BR>
                                      3. COMERCIAL<BR>
                                      4. SERVICIOS<BR>
                                      5. FINANCIERO </strong></td>';
            echo '<td><center><strong>CÓDIGO ICA<BR>
                                      DEL BIEN O<BR>
                                      SERVICIO<BR>
                                      ADQUIRIDO.</strong></center></td>';
            echo '<td><center><strong>VALOR CANCELADO </strong></center></td>';
            echo '<td><center><strong>FECHA DE PAGO O<BR>
                                      ABONO EN CUENTA<BR>
                                      EN LA FUENTE<BR>
                                      PRACTICADA A<BR>
                                      TÍTULO DE<BR>
                                      INDUSTRIA Y<BR>
                                      COMERCIO<BR>
                                      (AAAAMMDD)</strong></center></td>';
            echo '<td><center><strong>BASE GRAVABLE<BR>
                                      SUJETA A<BR>
                                      RETENCIÓN EN LA<BR>
                                      FUENTE </strong></center></td>';
            echo '<td><center><strong>VALOR DE LA<BR>
                                      RETENCIÓN EN LA<BR>
                                      FUENTE<BR>
                                      PRACTICADA A<BR>
                                      TÍTULO DE<BR>
                                      INDUSTRIA Y<BR>
                                      COMERCIO </strong></center></td>';
            echo '<td><center><strong>VALOR DE<BR>
                                      DEVOLUCIÓN<BR>
                                      EN LA FUENTE<BR>
                                      PRACTICADA A<BR>
                                      TÍTULO DE<BR>
                                      INDUSTRIA Y<BR>
                                      COMERCIO </strong></center></td>';                          
            /*echo '<td><center><strong>FECHA DE PAGO O RETENCION </strong></center></td>';
            echo '<td><center><strong>CODIGO ACTIVIDAD</strong></center></td>';
            echo '<td><center><strong>ACTIVIDAD</strong></center></td>';
            echo '<td><center><strong>TARIFA </strong></center></td>';
            echo '<td><center><strong>BASE GRAVABLE</strong></center></td>';
            echo '<td><center><strong>VALOR RETENIDO </strong></center></td>';
            echo '<td><center><strong>CIUDAD </strong></center></td>';
            echo '<td><center><strong>DEPARTAMENTO </strong></center></td>';*/
            
            echo '</tr>';
            echo '<tbody>';
            $r =1;
            for ($i = 0; $i < count($row); $i++) {
                if ($row[$i][14]==1) {
                   $tipo_doc="2";
                }elseif($row[$i][14]==2){
                    $tipo_doc="3";
                }elseif($row[$i][14]==4){
                    $tipo_doc="4";
                }elseif($row[$i][14]==5){
                    $tipo_doc="1";
                }elseif($row[$i][14]==11){
                    $tipo_doc="5";
                }
                if ($row[$i][17]=="PAGO NOMINA") {
                   $conceptoP="1";
                }elseif($row[$i][17]=="INDUSTRIA"){
                    $conceptoP="2";
                }elseif($row[$i][17]=="COMERCIAL"){
                    $conceptoP="3";
                }elseif($row[$i][17]=="SERVICIOS"){
                    $conceptoP="4";
                }elseif($row[$i][17]=="FINANCIERO"){
                    $conceptoP="5";
                }
                $id_pptal=$row[$i][18];
                #Consulta para obtener Valor Cancelado
                 $sqlV = $con->Listar("SELECT DISTINCT 
                       tcar.codigo, tcar.nombre, car.numero, tcar.clasepptal,car.id_unico,car.tipocomprobante
                       FROM 
                               gf_comprobante_pptal c
                       LEFT JOIN 
                               gf_detalle_comprobante_pptal dc ON c.id_unico =dc.comprobantepptal 
                       LEFT JOIN 
                               gf_tipo_comprobante_pptal tc ON c.tipocomprobante = tc.id_unico 
                       LEFT JOIN 
                               gf_detalle_comprobante_pptal dcr ON dcr.comprobanteafectado = dc.id_unico 
                       LEFT JOIN 
                               gf_comprobante_pptal car ON dcr.comprobantepptal = car.id_unico 
                       LEFT JOIN 
                               gf_tipo_comprobante_pptal tcar ON car.tipocomprobante = tcar.id_unico 
                       LEFT JOIN 
                               gf_detalle_comprobante_pptal dcop ON dcop.comprobanteafectado = dcr.id_unico 
                       LEFT JOIN 
                               gf_comprobante_pptal cop ON dcop.comprobantepptal = cop.id_unico 
                       LEFT JOIN 
                               gf_tipo_comprobante_pptal tcop ON cop.tipocomprobante = tcop.id_unico 
                       LEFT JOIN 
                               gf_detalle_comprobante_pptal dccxp ON dccxp.comprobanteafectado = dcop.id_unico 
                       LEFT JOIN 
                               gf_comprobante_pptal ccxp ON dccxp.comprobantepptal = ccxp.id_unico 
                       LEFT JOIN 
                               gf_tipo_comprobante_pptal tccxp ON ccxp.tipocomprobante = tccxp.id_unico 
                       LEFT JOIN 
                               gf_detalle_comprobante_pptal dcegr ON dcegr.comprobanteafectado = dccxp.id_unico 
                       LEFT JOIN 
                               gf_comprobante_pptal cegr ON dcegr.comprobantepptal = cegr.id_unico 
                       LEFT JOIN 
                               gf_tipo_comprobante_pptal tcegr ON cegr.tipocomprobante = tcegr.id_unico 
                       WHERE 
                       c.id_unico = $id_pptal");
                       $tipo_cnt=$sqlV[0][5];
                       $numero_cnt=$sqlV[0][2];
 
                 #Buscar tipo comprobante CNT
                    $sqlTipoCnt = $con->Listar("SELECT id_unico FROM gf_tipo_comprobante where comprobante_pptal = $tipo_cnt");
                    $tipo_comp_cnt=$sqlTipoCnt[0][0];
                 #Buscar Comprobante CNT EGRESO
                    $sqlCnt = $con->Listar("SELECT id_unico FROM gf_comprobante_cnt where numero = $numero_cnt AND tipocomprobante = $tipo_comp_cnt");
                    $id_cnt=$sqlCnt[0][0];
                 #Sacamos valor Cancelado
                    $sqlValC= $con->Listar("SELECT SUM(valor),naturaleza FROM gf_detalle_comprobante
                                            WHERE comprobante=$id_cnt
                                            AND detallecomprobantepptal IS NOT NULL");
                     #SI LA NATURALEZA ES DEBITO

                    $nat=$sqlValC[0][1];
                    $valor=$sqlValC[0][0];
                     if ($nat == '1') {
                         if ($valor < 0) {
                             $val = (float) substr($valor, '1');
                         }else{
                            $val=$valor;
                         }
                       
                       $valorCancelado=$val;
                         #SI LA NATURALEZA ES CREDITO
                     } else {
                         if ($valor < 0) {
                             $val = (float) substr($valor, '1');
                         }else{
                            $val=$valor;
                         }
                        $valorCancelado=$val;
                     }

                echo '<tr>';
                echo '<td>'.$nanno.'</td>';
                echo '<td>'.$row[$i][0].'</td>';
                echo '<td style="text-align:left;">'.$tipo_doc.'</td>';
                echo '<td>'.$row[$i][2].'</td>';
                echo '<td>'.$row[$i][3].'</td>';
                echo '<td>'.$row[$i][10].'</td>';
                echo '<td>'.$row[$i][11].'</td>';
                echo '<td>'.$row[$i][15].'</td>';
                echo '<td>'.$row[$i][16].'</td>';
                echo '<td>'.$conceptoP.'</td>';
                echo '<td>'.$row[$i][5].'</td>';
                echo '<td>'.$valorCancelado.'</td>';
                echo '<td>'.$row[$i][4].'</td>';
                echo '<td>'.$row[$i][8].'</td>';
                echo '<td>'.$row[$i][9].'</td>';
                echo '<td>0</td>';
                /*echo '<td>'.$row[$i][6].'</td>';
                echo '<td>'.$row[$i][6].'</td>';
                echo '<td>'.$row[$i][7].'</td>';
                echo '<td>'.$row[$i][8].'</td>';
                
                echo '<td>'.$row[$i][12].'</td>';
                echo '<td>'.$row[$i][13].'</td>';*/
                echo '</tr>';
                $r++;
            }
            echo '</tbody>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
        break;
    }
} 

elseif(($ciudadR=='238' || $ciudadI =='238') && ($deptR=='15' || $deptI=='15')){
    # ************** CONSULTA PRINCIPAL **************#
$row = $con->Listar("SELECT     tr.cod_exogena, 
        t.numeroidentificacion, 
        t.apellidouno,
        t.apellidodos,
        t.nombreuno,
        t.nombredos,
        t.razonsocial, 
        GROUP_CONCAT(DISTINCT d.direccion), 
        GROUP_CONCAT(DISTINCT c.nombre),
        GROUP_CONCAT(DISTINCT tel.valor),
        '', 
        (tr.porcentajeaplicar*1000),
        SUM(r.retencionbase), 
        SUM(r.valorretencion) 
        FROM gf_retencion r 
        LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico 
        LEFT JOIN gf_clase_retencion cl ON tr.claseretencion = cl.id_unico 
        LEFT JOIN gf_comprobante_cnt cn ON r.comprobante = cn.id_unico 
        LEFT JOIN gf_tercero t ON cn.tercero = t.id_unico 
        LEFT JOIN gf_direccion d ON d.tercero = t.id_unico
        LEFT JOIN gf_ciudad c ON d.ciudad_direccion = c.id_unico 
        LEFT JOIN gf_telefono tel ON t.id_unico = tel.tercero 
        WHERE tr.parametrizacionanno = $annio 
        AND cl.id_unico =  $claseR AND cn.parametrizacionanno = $annio 
        GROUP BY t.id_unico, tr.id_unico");
     switch ($exportar){
        # *** Generar xls **#
        case 2:
            header("Content-type: application/vnd.ms-excel");
            header("Content-Disposition: attachment; filename=Informe_Exogenas_Municipal.xls");
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
            echo '<html xmlns="http://www.w3.org/1999/xhtml">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
            echo '<title>Informe Exógenas Municipal</title>';
            echo '</head>';
            echo '<body>';
            echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
            echo '<th colspan="16" align="center"><strong>';
            echo '<br/>FORMATO 1  RETENCIONES DE INDUSTRIA Y COMERCIO PRACTICADAS';
            echo '<br/>&nbsp;</strong>';
            echo '</th>';
            echo '<tr>';
            echo '<td colspan="7"><strong>NOMBRE DE QUIEN REPORTA LA INFORMACION: </strong></td>';
            echo '<td colspan="9"><strong>'.$razonsocial.'</strong></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="7"><strong>NIT O CEDULA: </strong></td>';
            echo "<td colspan='9' style='mso-number-format:\@'><strong>".$numeroIdent."</strong></td>";
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="7"><strong>AÑO GRAVABLE:  </strong></td>';
            echo "<td colspan='9' style='mso-number-format:\@'><strong>".$nanno."</strong></td>";
            echo '</tr>';
            echo '<tr>';
            echo '<td><center><strong>ITEM </strong></center></td>';
            echo '<td><center><strong>CONCEPTO </strong></center></td>';
            echo '<td><center><strong>IDENTIFICACION Y/O NIT  </strong></center></td>';
            echo '<td><center><strong>PRIMER APELLIDO </strong></center></td>';
            echo '<td><center><strong>SEGUNDO APELLIDO </strong></center></td>';
            echo '<td><center><strong>PRIMER NOMBRE </strong></center></td>';
            echo '<td><center><strong>OTROS NOMBRES </strong></center></td>';
            echo '<td><center><strong>RAZON SOCIAL (PERSONA JURIDICA) </strong></center></td>';
            echo '<td><center><strong>DIRECCION  </strong></center></td>';
            echo '<td><center><strong>CIUDAD </strong></center></td>';
            echo '<td><center><strong>TELEFONO </strong></center></td>';
            echo '<td><center><strong>EMAIL </strong></center></td>';
            echo '<td><center><strong>TARIFA DE RETENCION  </strong></center></td>';
            echo '<td><center><strong>BASE DE RETENCION  </strong></center></td>';
            echo '<td><center><strong>VALOR RETENIDO </strong></center></td>';
            echo '<td><center><strong>OBSERVACIONES </strong></center></td>';       
            
            echo '</tr>';
            echo '<tbody>';
            $r =1;
            for ($i = 0; $i < count($row); $i++) {
                echo '<tr>';
                echo '<td>'.$r.'</td>';
                echo "<td style='mso-number-format:\@'>".$row[$i][0]."</td>";
                echo '<td>'.$row[$i][1].'</td>';
                echo '<td>'.$row[$i][2].'</td>';
                echo '<td>'.$row[$i][3].'</td>';
                echo '<td>'.$row[$i][4].'</td>';
                echo '<td>'.$row[$i][5].'</td>';
                echo '<td>'.$row[$i][6].'</td>';
                echo '<td>'.$row[$i][7].'</td>';
                echo '<td>'.$row[$i][8].'</td>';
                echo '<td>'.$row[$i][9].'</td>';
                echo '<td>'.$row[$i][10].'</td>';
                echo '<td>'.$row[$i][11].'</td>';
                echo '<td>'.$row[$i][12].'</td>';
                echo '<td>'.$row[$i][13].'</td>';
                echo '<td></td>';
                echo '</tr>';
                $r++;
            }
            echo '</tbody>';
            echo '</table>';
            echo '</body>';
            echo '</html>';
        break;
    }
}
