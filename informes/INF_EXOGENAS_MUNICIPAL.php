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
$rowC = $con->Listar("SELECT 	ter.id_unico,
                ter.razonsocial,
                UPPER(ti.nombre),
                ter.numeroidentificacion,
                dir.direccion,
                tel.valor,
                ter.ruta_logo, 
                c.rss, 
                c2.rss, d1.rss, d2.rss
FROM gf_tercero ter
LEFT JOIN 	gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
LEFT JOIN       gf_direccion dir ON dir.tercero = ter.id_unico
LEFT JOIN 	gf_telefono  tel ON tel.tercero = ter.id_unico
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

if(($ciudadR=='010' || $ciudadI =='010') && ($deptR=='85' || $deptI=='85')){
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
        GROUP_CONCAT(DISTINCT d.direccion), 
        GROUP_CONCAT(DISTINCT tel.valor),
        GROUP_CONCAT(DISTINCT c.nombre),
        GROUP_CONCAT(DISTINCT dp.nombre)        
        FROM gf_retencion r 
        LEFT JOIN gf_tipo_retencion tr ON r.tiporetencion = tr.id_unico 
        LEFT JOIN gf_clase_retencion cl ON tr.claseretencion = cl.id_unico 
        LEFT JOIN gf_comprobante_cnt cn ON r.comprobante = cn.id_unico 
        LEFT JOIN gf_tercero t ON cn.tercero = t.id_unico 
        LEFT JOIN gf_direccion d ON d.tercero = t.id_unico
        LEFT JOIN gf_ciudad c ON d.ciudad_direccion = c.id_unico 
        LEFT JOIN gf_telefono tel ON t.id_unico = tel.tercero 
        LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
        LEFT JOIN gf_departamento dp ON c.departamento = dp.id_unico  
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
            echo '<td colspan="14"><center><strong>PROCESO: GESTIÓN FINANCIERA </strong></center></td>';
            echo '</tr>';
            echo '<tr>';
            echo '<td colspan="14"><center><strong>SECRETARIA DE HACIENDA </strong></center></td>';
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
            echo '<td colspan="5">Versión: 01 </td>';
            echo '</tr>';
            echo '<tr>';
            echo "<td colspan='11'><center>Fecha de Creación: ".date('d/M/Y')."</center></td>";
            echo '</tr>';
            echo '<tr>';
            echo '<td><center><strong>APELLIDOS Y NOMBRES Y/O RAZON SOCIAL DEL BENEFICIARIO DEL PAGO O ABONO EN CUENTA </strong></center></td>';
            echo '<td><center><strong>TIPO DE IDENTIFICACIÓN</strong></center></td>';
            echo '<td><center><strong>NUMERO DE IDENTIFICACION </strong></center></td>';
            echo '<td><center><strong>DV </strong></center></td>';
            echo '<td><center><strong>FECHA DE PAGO O RETENCION </strong></center></td>';
            echo '<td><center><strong>CODIGO ACTIVIDAD</strong></center></td>';
            echo '<td><center><strong>ACTIVIDAD</strong></center></td>';
            echo '<td><center><strong>TARIFA </strong></center></td>';
            echo '<td><center><strong>BASE GRAVABLE</strong></center></td>';
            echo '<td><center><strong>VALOR RETENIDO </strong></center></td>';
            echo '<td><center><strong>DIRECCION  </strong></center></td>';
            echo '<td><center><strong>TELEFONO </strong></center></td>';
            echo '<td><center><strong>CIUDAD </strong></center></td>';
            echo '<td><center><strong>DEPARTAMENTO </strong></center></td>';
            
            echo '</tr>';
            echo '<tbody>';
            $r =1;
            for ($i = 0; $i < count($row); $i++) {
                echo '<tr>';
                echo '<td>'.$row[$i][0].'</td>';
                echo '<td>'.$row[$i][1].'</td>';
                echo '<td>'.$row[$i][2].'</td>';
                echo '<td>'.$row[$i][3].'</td>';
                echo '<td>'.$row[$i][4].'</td>';
                echo "<td style='mso-number-format:\@'>".$row[$i][5]."</td>";
                echo '<td>'.$row[$i][6].'</td>';
                echo '<td>'.$row[$i][7].'</td>';
                echo '<td>'.$row[$i][8].'</td>';
                echo '<td>'.$row[$i][9].'</td>';
                echo '<td>'.$row[$i][10].'</td>';
                echo '<td>'.$row[$i][11].'</td>';
                echo '<td>'.$row[$i][12].'</td>';
                echo '<td>'.$row[$i][13].'</td>';
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
$row = $con->Listar("SELECT 	tr.cod_exogena, 
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
