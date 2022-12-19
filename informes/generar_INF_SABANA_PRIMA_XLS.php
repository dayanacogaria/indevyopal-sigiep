<?php
require_once("../Conexion/ConexionPDO.php");
require_once("../Conexion/conexion.php");
ini_set('max_execution_time', 0);
session_start();
$con = new ConexionPDO();
$anno = $_SESSION['anno'];
#   ************   Datos Compañia   ************    #
$compania = $_SESSION['compania'];
//Datos de compañia
$rowC = $con->Listar
        ("
SELECT ter.id_unico,
    ter.razonsocial,
    UPPER(ti.nombre),
    ter.numeroidentificacion,
    dir.direccion,
    tel.valor,
    ter.ruta_logo,
    IF(CONCAT_WS(' ',
    ter.nombreuno,
    ter.nombredos,
    ter.apellidouno,
    ter.apellidodos)
    IS NULL OR CONCAT_WS(' ',
    ter.nombreuno,
    ter.nombredos,
    ter.apellidouno,
    ter.apellidodos) = '',
    (ter.razonsocial),
    CONCAT_WS(' ',
    ter.nombreuno,
    ter.nombredos,
    ter.apellidouno,
    ter.apellidodos)) AS NOMBRE
FROM gf_tercero ter
    LEFT JOIN   gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
    LEFT JOIN   gf_direccion dir ON dir.tercero = ter.id_unico
    LEFT JOIN   gf_telefono  tel ON tel.tercero = ter.id_unico
WHERE ter.id_unico = $compania
");
$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][7];
$telefonoTer = $rowC[0][5];
$ruta_logo = $rowC[0][6];

#Datos POST
$periodo  = $_POST['sltPeriodo'];
$unidad   = $_POST['sltUnidadE'];
$grupog   = $_POST['sltGrupoG'];
$where    = "";
if (!empty($unidad)){
    $where .= " AND e.unidadejecutora = ".$unidad;
}

if (!empty($grupog)){
    $where .= " AND e.grupogestion = ".$grupog;
}

#Consulta Periodo
$consulta1 = "  SELECT * FROM gn_periodo WHERE id_unico = $periodo";
    $per = $mysqli->query($consulta1);
    $pe = mysqli_fetch_row($per);
    $prd = $pe[1];



$sqlconceptos = "
SELECT
DISTINCT
n.concepto, 
c.descripcion,
c.id_unico
FROM gn_novedad n 
LEFT JOIN gn_concepto c ON n.concepto = c.id_unico
LEFT JOIN gn_empleado e ON n.empleado = e.id_unico
WHERE n.periodo = $periodo 
ORDER BY c.orden";
    $conceptos  = $mysqli->query($sqlconceptos);
    $cantidadcp = mysqli_num_rows($conceptos);
    $cantidadcp += 4;

    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Informe_Sabana_Prima.xls");    ?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <title>Sábana Prima de servicios</title>
        </head>
        <body>
            <table width="100%" border="1" cellspacing="0" cellpadding="0">
                <th colspan="<?php echo $cantidadcp; ?>" align="center"><strong>
                        <br/><?php echo $razonsocial ?>
                        <br/><?php echo $nombreIdent . ' : ' . $numeroIdent ?>
                        <br/><?php echo utf8_decode('SÁBANA DE PRIMA DE SERVICIOS'); ?>
                        <br/>NÓMINA: <?php echo $prd; ?>
                        <br/>&nbsp;</strong>
                </th>                
                <tbody>
                    <?php
                        $html = '';
                        $sqlgg ="SELECT DISTINCT  gg.id_unico, gg.nombre FROM gn_novedad n 
                            LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_grupo_gestion gg oN e.grupogestion = gg.id_unico 
                            WHERE n.periodo = $periodo $where ";
                        $sqlgg = $mysqli->query($sqlgg);
                        while ($rowue = mysqli_fetch_row($sqlgg)) {
                                echo '<tr><td style=" height: 50px; vertical-align: middle;" colspan="'.$cantidadcp.'" align="left"><strong> Grupo de Gestión: '.$rowue[1].'</strong></td></tr>
                                    </tr>
                                    <tr>
                                    <td><center><strong>CÉDULA</strong></center></td>
                                    <td><center><strong>NOMBRE</strong></center></td>';
                                $conceptos  = $mysqli->query($sqlconceptos);
                                while ($rowc = mysqli_fetch_row($conceptos)) {#Cargar Encabezado
                                    echo "<td><center><strong>$rowc[1]</strong></center></td>";
                                }
                                echo "<td><center><strong>FIRMA</strong></center></td>";
                                echo "</tr>";
                                
                                $sqlempleado = "
                                SELECT DISTINCT  
                                e.id_unico, 
                                e.codigointerno, 
                                e.tercero, 
                                t.id_unico,
                                t.numeroidentificacion, 
                                (
                                    IF(
                                    CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = ' ',
                                    t.razonsocial,
                                    CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)
                                    )
                                ) as tercero,
                                ca.salarioactual 
                                FROM gn_empleado e 
                                LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                                LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                                LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                                LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico
                                LEFT JOIN gn_novedad n ON n.empleado = e.id_unico
                                WHERE e.id_unico !=2 AND n.periodo =$periodo  AND e.grupogestion  = $rowue[0]";
                                $empleados  = $mysqli->query($sqlempleado);                    
                                while ($rowe = mysqli_fetch_row($empleados)) { #Cargar Datos
                                    echo '<tr>
                                    <td align="right">'.$rowe[4].'</td>
                                    <td align="left">'.$rowe[5].'</td>';
                                    $conceptos2  = $mysqli->query($sqlconceptos);
                                    while($rowc2 = mysqli_fetch_row($conceptos2)){
                                        $novco = "
                                        SELECT n.id_unico, n.valor 
                                        FROM gn_novedad n 
                                        LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico 
                                        LEFT JOIN gn_empleado e ON n.empleado = e.id_unico  
                                        WHERE c.id_unico = '$rowc2[2]' AND e.id_unico = '$rowe[0]' AND n.periodo = $periodo  
                                        ORDER BY c.orden";
                                        $cnov = $mysqli->query($novco);
                                        $num_con = mysqli_num_rows($cnov);
                                        if($num_con > 0){
                                            $novec = mysqli_fetch_row($cnov);
                                            echo "<td align='right'>".number_format($novec[1],2,'.',',')."</td>";
                                        }else {
                                            echo "<td align='right'>".number_format(0,2,'.',',')."</td>";
                                        }                            
                                    }
                                    echo "<td></td>";
                                    echo "</tr>";                        
                                }
                                echo "<tr><td  colspan='2'><center><strong><br/>TOTAL <br/>".mb_strtoupper($rowue[1])."</strong></center></td>";
                                $conceptos3  = $mysqli->query($sqlconceptos);
                                while($rowc3 = mysqli_fetch_row($conceptos3)){ #Totales
                                    $sumco = "
                                    SELECT SUM(n.valor),n.concepto 
                                    FROM gn_novedad n LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                                    LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
                                    WHERE c.id_unico = '$rowc3[2]'  AND n.periodo = $periodo 
                                    AND e.grupogestion  = $rowue[0]     
                                    ORDER BY c.orden";  
                                    $snov = $mysqli->query($sumco);            
                                    while($sumanov = mysqli_fetch_row($snov)){
                                        echo "<td align='right'><strong>".number_format($sumanov[0],2,'.',',')."</strong></td>";
                                    }
                                }
                                
                                echo '<td></td></tr>';
                            }
                        echo '<tr>';
                        echo "<td  colspan='2'><center><strong><br/>TOTALES<br/>.</strong></center></td>";
                        $conceptos3  = $mysqli->query($sqlconceptos);
                        while($rowc3 = mysqli_fetch_row($conceptos3)){ #Totales
                            $sumco = "
                            SELECT SUM(n.valor),n.concepto 
                            FROM gn_novedad n LEFT JOIN  gn_concepto c ON n.concepto = c.id_unico LEFT JOIN gn_empleado e ON n.empleado = e.id_unico 
                            LEFT JOIN gn_vinculacion_retiro vr ON vr.empleado = e.id_unico 
                            WHERE c.id_unico = '$rowc3[2]'  AND n.periodo = $periodo    
                            ORDER BY c.orden";  
                            $snov = $mysqli->query($sumco);            
                            while($sumanov = mysqli_fetch_row($snov)){
                                echo "<td align='right'><strong>".number_format($sumanov[0],2,'.',',')."</strong></td>";
                            }
                        }
                        echo '<td></td></tr>';
                    ?>
                </tbody>
            </table>
        </body>
    </html>
