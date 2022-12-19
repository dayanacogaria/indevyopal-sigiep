<?php                                                                                     
##########################################################################################################################################################################
#13/10/2021 | Elkin O. | Se crea informe para conteo del personal 
##########################################################################################################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Personal_Categoria_SUI.xls");
require_once("../Conexion/conexion.php");
session_start();
ini_set('max_execution_time', 0);
$compania = $_SESSION['compania'];
$panno = $_SESSION['anno'];
$anno=$_POST['sltAnno'];
$fechaIn='01/01/'.$anno;
$fechaFin='31/12/'.$anno;

$fechaIni=$anno.'-01-01';
$fechaFini=$anno.'-12-31';


?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Personal</title>
</head>
<body>
<table width="30%" border="1" cellspacing="0" cellpadding="0">
<tr>
        <th colspan="10" align="center" ><strong>
        Personal por Categoria de Empleo: ALCANTARILLADO-<?php echo $anno ?>
        </th>
  </tr>
  <tr>
        <th colspan="10" align="center" ><strong>
            PERIODO COMPRENDIDO ENTRE:
        <br/><?php echo $fechaIn.' al '.$fechaFin ?>
        </th>
  </tr>


        <tr>    
         <td rowspan="2" colspan="2" ><center><strong>Estructura de Personal </strong></center></td>
         <td rowspan="2" colspan="2" ><center><strong>Numero <br/>de Personas  </strong></center></td>
            <td colspan="6"><center><strong>Valor en Pesos</strong></center></td>
            </tr>
            <tr>
                <td colspan="2"><center><strong>Sueldo</strong></center></td>
                <td colspan="2"><center><strong>Otros Pagos Servicios <br/>Personales</strong></center></td>
                <td colspan="2"><center><strong>Prestaciones<br/> Legales</strong></center></td>
            </tr>
  
          <tr>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;" ><strong>Total de Personal</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Alcantarillado')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND ca.tipo_persona_sui IN ('Nomina','Temporal')
                      AND te.id_unico IN (3,4,5,6,7,8)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui IN ('Nomina','Temporal')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui IN ('Nomina','Temporal')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui IN ('Nomina','Temporal')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr> 
          <tr>
          </tr> 
  <tr>
        <td rowspan="2" colspan="2" align="center" style="background-color: Gray;" ><strong>Total Personal Nomina</strong></td>
       
      <?php
          
       $empleados="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Alcantarillado'
                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                   AND ca.tipo_persona_sui IN ('Nomina')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmple = $mysqli->query($empleados);
        $rowcount=mysqli_num_rows($countEmple);

    
             $sqlSueldo="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmp = $mysqli->query($sqlSueldo);
           $filaS = mysqli_fetch_row($sueldoEmp);

            $otrosPagos="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPag = $mysqli->query($otrosPagos);
           $filaO = mysqli_fetch_row($otrPag);

            $presLe="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLeg = $mysqli->query($presLe);
           $filaP = mysqli_fetch_row($presLeg);

        ?>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo $rowcount?></strong></td>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaS[1],2,'.',',');?></strong></td>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaO[1],2,'.',',')?></strong></td>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaP[1],2,'.',',')?></strong></td>
    </tr> 
    <tr>
        </tr>
        </tr>
     <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Directivo</strong></td>
<?php
     $empleadosD="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Alcantarillado'
                   AND ni.equivalente_sui IN ('Personal Directivo')
                   AND ca.tipo_persona_sui IN ('Nomina')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleD = $mysqli->query($empleadosD);
        $rowcountD=mysqli_num_rows($countEmpleD);

          $sqlSueldoD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpD = $mysqli->query($sqlSueldoD);
           $filaSD = mysqli_fetch_row($sueldoEmpD);

             $otrosPagosD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                 AND ca.tipo_persona_sui IN ('Nomina')
                                 AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagD = $mysqli->query($otrosPagosD);
           $filaOD = mysqli_fetch_row($otrPagD);

            $presLeD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegD = $mysqli->query($presLeD);
           $filaPD = mysqli_fetch_row($presLegD);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountD?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSD[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOD[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPD[1],2,'.',',')?></strong></td>

    </tr>
    
    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Administrativo</strong></td>
<?php
     $empleadosA="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Alcantarillado'
                   AND ni.equivalente_sui IN ('Personal Administrativo')
                   AND ca.tipo_persona_sui IN ('Nomina')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleA = $mysqli->query($empleadosA);
        $rowcountA=mysqli_num_rows($countEmpleA);

          $sqlSueldoA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpA = $mysqli->query($sqlSueldoA);
           $filaSA = mysqli_fetch_row($sueldoEmpA);

             $otrosPagosA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagA = $mysqli->query($otrosPagosA);
           $filaOA = mysqli_fetch_row($otrPagA);

            $presLeA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegA = $mysqli->query($presLeA);
           $filaPA = mysqli_fetch_row($presLegA);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountA?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSA[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOA[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPA[1],2,'.',',')?></strong></td>

    </tr>

    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Tecnico - Operativo</strong></td>
<?php
     $empleadosT="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Alcantarillado'
                   AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                   AND ca.tipo_persona_sui IN ('Nomina')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleT = $mysqli->query($empleadosT);
        $rowcountT=mysqli_num_rows($countEmpleT);

          $sqlSueldoT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpT = $mysqli->query($sqlSueldoT);
           $filaST = mysqli_fetch_row($sueldoEmpT);

             $otrosPagosT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagT = $mysqli->query($otrosPagosT);
           $filaOT = mysqli_fetch_row($otrPagT);

            $presLeT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegT = $mysqli->query($presLeT);
           $filaPT = mysqli_fetch_row($presLegT);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountT?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaST[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOT[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPT[1],2,'.',',')?></strong></td>

    </tr>
     <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" style="background-color: Gray;" ><strong>Total Personal Temporal</strong></td>
     <?php
          
          $empleadosTEMP="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Alcantarillado')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND ca.tipo_persona_sui='Temporal'
                      AND te.id_unico IN (3,4,5,6,7,8)";
                      
           $countEmpleTEMP = $mysqli->query($empleadosTEMP);
           $rowcountTEMP=mysqli_num_rows($countEmpleTEMP);
   
       
                $sqlSueldoTEMP="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui='Temporal'
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $sueldoEmpTEMP = $mysqli->query($sqlSueldoTEMP);
              $filaSTEMP = mysqli_fetch_row($sueldoEmpTEMP);
   
               $otrosPagosTEMP="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui='Temporal'
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $otrPagTEMP = $mysqli->query($otrosPagosTEMP);
              $filaOTEMP = mysqli_fetch_row($otrPagTEMP);
   
               $presLeTEMP="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui='Temporal'
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $presLegTEMP= $mysqli->query($presLeTEMP);
              $filaPTEMP = mysqli_fetch_row($presLegTEMP);
   
           ?>





       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo $rowcountTEMP?></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaSTEMP[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaOTEMP[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaPTEMP[1],2,'.',',')?></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Directivo</strong></td>
<?php
     $empleadosD="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Alcantarillado'
                   AND ni.equivalente_sui IN ('Personal Directivo')
                   AND ca.tipo_persona_sui IN ('Temporal')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleD = $mysqli->query($empleadosD);
        $rowcountD=mysqli_num_rows($countEmpleD);

          $sqlSueldoD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND ca.tipo_persona_sui IN ('Temporal')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpD = $mysqli->query($sqlSueldoD);
           $filaSD = mysqli_fetch_row($sueldoEmpD);

             $otrosPagosD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                 AND ca.tipo_persona_sui IN ('Temporal')
                                 AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagD = $mysqli->query($otrosPagosD);
           $filaOD = mysqli_fetch_row($otrPagD);

            $presLeD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Alcantarillado'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND ca.tipo_persona_sui IN ('Temporal')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegD = $mysqli->query($presLeD);
           $filaPD = mysqli_fetch_row($presLegD);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountD?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSD[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOD[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPD[1],2,'.',',')?></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Administrativo</strong></td>
<?php
     $empleadosPA="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui IN ('Alcantarillado')
                   AND ni.equivalente_sui IN ('Personal Administrativo')
                   AND ca.tipo_persona_sui='Temporal'
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmplePA = $mysqli->query($empleadosPA);
        $rowcountPA=mysqli_num_rows($countEmplePA);

          $sqlSueldoPA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui IN ('Alcantarillado')
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpPA = $mysqli->query($sqlSueldoPA);
           $filaSPA = mysqli_fetch_row($sueldoEmpPA);

             $otrosPagosPA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui IN ('Alcantarillado')
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagPA = $mysqli->query($otrosPagosPA);
           $filaOPA = mysqli_fetch_row($otrPagPA);

            $presLePA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui IN ('Alcantarillado')
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegPA = $mysqli->query($presLePA);
           $filaPPA = mysqli_fetch_row($presLegPA);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountPA?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSPA[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOPA[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPPA[1],2,'.',',')?></strong></td>

    </tr>


    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Tecnico - Operativo</strong></td>
<?php
     $empleadosPT="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui IN ('Alcantarillado')
                   AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                   AND ca.tipo_persona_sui='Temporal'
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmplePT = $mysqli->query($empleadosPT);
        $rowcountPT=mysqli_num_rows($countEmplePT);

          $sqlSueldoPT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui IN ('Alcantarillado')
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpPT = $mysqli->query($sqlSueldoPT);
           $filaSPT = mysqli_fetch_row($sueldoEmpPT);

             $otrosPagosPT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui IN ('Alcantarillado')
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagPT = $mysqli->query($otrosPagosPT);
           $filaOPT = mysqli_fetch_row($otrPagPT);

            $presLePT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui IN ('Alcantarillado')
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegPT = $mysqli->query($presLePT);
           $filaPPT = mysqli_fetch_row($presLegPT);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountPT?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSPT[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOPT[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPPT[1],2,'.',',')?></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;" ><strong>Total Categoria de Empleados</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Alcantarillado')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (3,4,5,6,7,8)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr> 
          <tr>
    </tr>
    <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Libre Nombramiento Y Remocion</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Alcantarillado')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (3)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr>
          <tr>
          </tr>
          <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Tecnico</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Alcantarillado')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (4)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (4)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (4)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (4)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr>  
          <tr>
          </tr>
          <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Publicos</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Alcantarillado')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (5)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (5)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (5)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (5)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr> 
          <tr>
          </tr>
          <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Trabajador Oficial</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Alcantarillado')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (6)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (6)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (6)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (6)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr>
          <tr>
          </tr>
          <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Supernumerarios</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Alcantarillado')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (7)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (7)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (7)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (7)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr>
          <tr>
          </tr>
          <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Aprendiz Sena</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Alcantarillado')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (8)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (8)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (8)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Alcantarillado')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (8)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr> 
    <tr>
    </tr>
    <tr>
    </tr>
    <tr>
        <th colspan="10" align="center" ><strong>
        Personal por Categoria de Empleo: ACUEDUCTO-<?php echo $anno ?>
        </th>
  </tr>
  <tr>
        <th colspan="10" align="center" ><strong>
            PERIODO COMPRENDIDO ENTRE:
        <br/><?php echo $fechaIn.' al '.$fechaFin ?>
        </th>
  </tr>


        <tr>    
         <td rowspan="2" colspan="2" ><center><strong>Estructura de Personal </strong></center></td>
         <td rowspan="2" colspan="2" ><center><strong>Numero <br/>de Personas  </strong></center></td>
            <td colspan="6"><center><strong>Valor en Pesos</strong></center></td>
            </tr>
            <tr>
                <td colspan="2"><center><strong>Sueldo</strong></center></td>
                <td colspan="2"><center><strong>Otros Pagos Servicios <br/>Personales</strong></center></td>
                <td colspan="2"><center><strong>Prestaciones<br/> Legales</strong></center></td>
            </tr>

    <tr>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;" ><strong>Total de Personal</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Acueducto')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND ca.tipo_persona_sui IN ('Nomina','Temporal')
                      AND te.id_unico IN (3,4,5,6,7,8)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui IN ('Nomina','Temporal')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui IN ('Nomina','Temporal')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui IN ('Nomina','Temporal')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr> 
           <tr>
            </tr> 
        
   
  <tr>
        <td rowspan="2" colspan="2" align="center" style="background-color: Gray;" ><strong>Total Personal Nomina</strong></td>
       
      <?php
     

   
 /*     while($filaM = mysqli_fetch_row($meses)){
        $diaF = cal_days_in_month($calendario, $filaM[0] , $anno); 
        $fechaI=$anno.'-'.$filaM[0].'-01';
        $fechaF=$anno.'-'.$filaM[0].'-'.$diaF;
     */
    
    
       
       $empleados="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Acueducto'
                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                   AND ca.tipo_persona_sui IN ('Nomina')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmple = $mysqli->query($empleados);
        $rowcount=mysqli_num_rows($countEmple);

    
             $sqlSueldo="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmp = $mysqli->query($sqlSueldo);
           $filaS = mysqli_fetch_row($sueldoEmp);

            $otrosPagos="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPag = $mysqli->query($otrosPagos);
           $filaO = mysqli_fetch_row($otrPag);

            $presLe="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLeg = $mysqli->query($presLe);
           $filaP = mysqli_fetch_row($presLeg);

        ?>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo $rowcount?></strong></td>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaS[1],2,'.',',');?></strong></td>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaO[1],2,'.',',')?></strong></td>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaP[1],2,'.',',')?></strong></td>
    </tr> 
    <tr>
        </tr>
        </tr>
     <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Directivo</strong></td>
<?php
     $empleadosD="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Acueducto'
                   AND ni.equivalente_sui IN ('Personal Directivo')
                   AND ca.tipo_persona_sui IN ('Nomina')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleD = $mysqli->query($empleadosD);
        $rowcountD=mysqli_num_rows($countEmpleD);

          $sqlSueldoD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpD = $mysqli->query($sqlSueldoD);
           $filaSD = mysqli_fetch_row($sueldoEmpD);

             $otrosPagosD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagD = $mysqli->query($otrosPagosD);
           $filaOD = mysqli_fetch_row($otrPagD);

            $presLeD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegD = $mysqli->query($presLeD);
           $filaPD = mysqli_fetch_row($presLegD);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountD?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSD[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOD[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPD[1],2,'.',',')?></strong></td>

    </tr>
    
    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Administrativo</strong></td>
<?php
     $empleadosA="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Acueducto'
                   AND ni.equivalente_sui IN ('Personal Administrativo')
                   AND ca.tipo_persona_sui IN ('Nomina')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleA = $mysqli->query($empleadosA);
        $rowcountA=mysqli_num_rows($countEmpleA);

          $sqlSueldoA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpA = $mysqli->query($sqlSueldoA);
           $filaSA = mysqli_fetch_row($sueldoEmpA);

             $otrosPagosA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagA = $mysqli->query($otrosPagosA);
           $filaOA = mysqli_fetch_row($otrPagA);

            $presLeA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegA = $mysqli->query($presLeA);
           $filaPA = mysqli_fetch_row($presLegA);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountA?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSA[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOA[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPA[1],2,'.',',')?></strong></td>

    </tr>

    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Tecnico - Operativo</strong></td>
<?php
     $empleadosT="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Acueducto'
                   AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                   AND ca.tipo_persona_sui IN ('Nomina')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleT = $mysqli->query($empleadosT);
        $rowcountT=mysqli_num_rows($countEmpleT);

          $sqlSueldoT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpT = $mysqli->query($sqlSueldoT);
           $filaST = mysqli_fetch_row($sueldoEmpT);

             $otrosPagosT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagT = $mysqli->query($otrosPagosT);
           $filaOT = mysqli_fetch_row($otrPagT);

            $presLeT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui IN ('Nomina')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegT = $mysqli->query($presLeT);
           $filaPT = mysqli_fetch_row($presLegT);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountT?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaST[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOT[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPT[1],2,'.',',')?></strong></td>

    </tr>
     <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" style="background-color: Gray;" ><strong>Total Personal Temporal</strong></td>
     <?php
          
          $empleadosTEMP="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Acueducto')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND ca.tipo_persona_sui='Temporal'
                      AND te.id_unico IN (3,4,5,6,7,8)";
                      
           $countEmpleTEMP = $mysqli->query($empleadosTEMP);
           $rowcountTEMP=mysqli_num_rows($countEmpleTEMP);
   
       
                $sqlSueldoTEMP="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui='Temporal'
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $sueldoEmpTEMP = $mysqli->query($sqlSueldoTEMP);
              $filaSTEMP = mysqli_fetch_row($sueldoEmpTEMP);
   
               $otrosPagosTEMP="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui='Temporal'
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $otrPagTEMP = $mysqli->query($otrosPagosTEMP);
              $filaOTEMP = mysqli_fetch_row($otrPagTEMP);
   
               $presLeTEMP="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui='Temporal'
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $presLegTEMP= $mysqli->query($presLeTEMP);
              $filaPTEMP = mysqli_fetch_row($presLegTEMP);
   
           ?>





       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo $rowcountTEMP?></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaSTEMP[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaOTEMP[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaPTEMP[1],2,'.',',')?></strong></td>

    </tr>
    <tr>
    </tr>
    </tr>
        </tr>
     <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Directivo</strong></td>
<?php
     $empleadosD="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Acueducto'
                   AND ni.equivalente_sui IN ('Personal Directivo')
                   AND ca.tipo_persona_sui IN ('Temporal')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleD = $mysqli->query($empleadosD);
        $rowcountD=mysqli_num_rows($countEmpleD);

          $sqlSueldoD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND ca.tipo_persona_sui IN ('Temporal')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpD = $mysqli->query($sqlSueldoD);
           $filaSD = mysqli_fetch_row($sueldoEmpD);

             $otrosPagosD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND ca.tipo_persona_sui IN ('Temporal')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagD = $mysqli->query($otrosPagosD);
           $filaOD = mysqli_fetch_row($otrPagD);

            $presLeD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Acueducto'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND ca.tipo_persona_sui IN ('Temporal')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegD = $mysqli->query($presLeD);
           $filaPD = mysqli_fetch_row($presLegD);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountD?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSD[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOD[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPD[1],2,'.',',')?></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Administrativo</strong></td>
<?php
     $empleadosPA="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui IN ('Acueducto')
                   AND ni.equivalente_sui IN ('Personal Administrativo')
                   AND ca.tipo_persona_sui='Temporal'
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmplePA = $mysqli->query($empleadosPA);
        $rowcountPA=mysqli_num_rows($countEmplePA);

          $sqlSueldoPA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui IN ('Acueducto')
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpPA = $mysqli->query($sqlSueldoPA);
           $filaSPA = mysqli_fetch_row($sueldoEmpPA);

             $otrosPagosPA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui IN ('Acueducto')
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagPA = $mysqli->query($otrosPagosPA);
           $filaOPA = mysqli_fetch_row($otrPagPA);

            $presLePA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui IN ('Acueducto')
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegPA = $mysqli->query($presLePA);
           $filaPPA = mysqli_fetch_row($presLegPA);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountPA?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSPA[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOPA[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPPA[1],2,'.',',')?></strong></td>

    </tr>


    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Tecnico - Operativo</strong></td>
<?php
     $empleadosPT="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui IN ('Acueducto')
                   AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                   AND ca.tipo_persona_sui='Temporal'
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmplePT = $mysqli->query($empleadosPT);
        $rowcountPT=mysqli_num_rows($countEmplePT);

          $sqlSueldoPT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui IN ('Acueducto')
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpPT = $mysqli->query($sqlSueldoPT);
           $filaSPT = mysqli_fetch_row($sueldoEmpPT);

             $otrosPagosPT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui IN ('Acueducto')
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagPT = $mysqli->query($otrosPagosPT);
           $filaOPT = mysqli_fetch_row($otrPagPT);

            $presLePT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui IN ('Acueducto')
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegPT = $mysqli->query($presLePT);
           $filaPPT = mysqli_fetch_row($presLegPT);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountPT?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSPT[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOPT[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPPT[1],2,'.',',')?></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;" ><strong>Total Categoria de Empleados</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Acueducto')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (3,4,5,6,7,8)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" style="background-color: orange;"><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr> 
          <tr>
    </tr>
    <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Libre Nombramiento Y Remocion</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Acueducto')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (3)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (3)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr>
          <tr>
          </tr>
          <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Tecnico</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Acueducto')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (4)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (4)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (4)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (4)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr>  
          <tr>
          </tr>
          <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Publicos</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Acueducto')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (5)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (5)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (5)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (5)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr> 
          <tr>
          </tr>
          <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Trabajador Oficial</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Acueducto')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (6)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (6)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (6)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (6)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr>
          <tr>
          </tr>
          <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Supernumerarios</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Acueducto')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (7)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (7)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (7)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (7)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr>
          <tr>
          </tr>
          <tr>
             <td rowspan="2" colspan="2" align="center"  ><strong>Aprendiz Sena</strong></td>

             <?php
          
          $empleadosTO="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                      LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Acueducto')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND te.id_unico IN (8)";
                      
           $countEmpleTO = $mysqli->query($empleadosTO);
           $rowcountTO=mysqli_num_rows($countEmpleTO);
   
       
                $sqlSueldoTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (8)";
             $sueldoEmpTO = $mysqli->query($sqlSueldoTO);
              $filaSTO = mysqli_fetch_row($sueldoEmpTO);
   
               $otrosPagosTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (8)";
             $otrPagTO = $mysqli->query($otrosPagosTO);
              $filaOTO = mysqli_fetch_row($otrPagTO);
   
               $presLeTO="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Acueducto')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND te.id_unico IN (8)";
             $presLegTO = $mysqli->query($presLeTO);
              $filaPTO = mysqli_fetch_row($presLegTO);
   
           ?>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo $rowcountTO?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaSTO[1],2,'.',',');?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaOTO[1],2,'.',',')?></strong></td>
             <td rowspan="2" colspan="2" align="center" ><strong><?php echo number_format($filaPTO[1],2,'.',',')?></strong></td>
          </tr> 

    <tr>
    </tr>
    <tr>
    </tr>
    <tr>
        <th colspan="10" align="center" ><strong>
        Personal por Categoria de Empleo: ASEO-<?php echo $anno ?>
        </th>
  </tr>
  <tr>
        <th colspan="10" align="center" ><strong>
            PERIODO COMPRENDIDO ENTRE:
        <br/><?php echo $fechaIn.' al '.$fechaFin ?>
        </th>
  </tr>


        <tr>    
         <td rowspan="2" colspan="2" ><center><strong>Estructura de Personal </strong></center></td>
         <td rowspan="2" colspan="2" ><center><strong>Numero <br/>de Personas  </strong></center></td>
            <td colspan="6"><center><strong>Valor en Pesos</strong></center></td>
            </tr>
            <tr>
                <td colspan="2"><center><strong>Sueldo</strong></center></td>
                <td colspan="2"><center><strong>Otros Pagos Servicios <br/>Personales</strong></center></td>
                <td colspan="2"><center><strong>Prestaciones<br/> Legales</strong></center></td>
            </tr>
  
        
   
  <tr>
        <td rowspan="2" colspan="2" align="center" style="background-color: Gray;" ><strong>Total Personal Nomina</strong></td>
       
      <?php
     

   
 /*     while($filaM = mysqli_fetch_row($meses)){
        $diaF = cal_days_in_month($calendario, $filaM[0] , $anno); 
        $fechaI=$anno.'-'.$filaM[0].'-01';
        $fechaF=$anno.'-'.$filaM[0].'-'.$diaF;
     */
    
    
       
       $empleados="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Aseo'
                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmple = $mysqli->query($empleados);
        $rowcount=mysqli_num_rows($countEmple);

    
             $sqlSueldo="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmp = $mysqli->query($sqlSueldo);
           $filaS = mysqli_fetch_row($sueldoEmp);

            $otrosPagos="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPag = $mysqli->query($otrosPagos);
           $filaO = mysqli_fetch_row($otrPag);

            $presLe="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLeg = $mysqli->query($presLe);
           $filaP = mysqli_fetch_row($presLeg);

        ?>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo $rowcount?></strong></td>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaS[1],2,'.',',');?></strong></td>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaO[1],2,'.',',')?></strong></td>
    <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaP[1],2,'.',',')?></strong></td>
    </tr> 
    <tr>
        </tr>
        </tr>
     <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Directivo</strong></td>
<?php
     $empleadosD="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Aseo'
                   AND ni.equivalente_sui IN ('Personal Directivo')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleD = $mysqli->query($empleadosD);
        $rowcountD=mysqli_num_rows($countEmpleD);

          $sqlSueldoD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpD = $mysqli->query($sqlSueldoD);
           $filaSD = mysqli_fetch_row($sueldoEmpD);

             $otrosPagosD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagD = $mysqli->query($otrosPagosD);
           $filaOD = mysqli_fetch_row($otrPagD);

            $presLeD="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Directivo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegD = $mysqli->query($presLeD);
           $filaPD = mysqli_fetch_row($presLegD);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountD?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSD[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOD[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPD[1],2,'.',',')?></strong></td>

    </tr>
    
    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Administrativo</strong></td>
<?php
     $empleadosA="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Aseo'
                   AND ni.equivalente_sui IN ('Personal Administrativo')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleA = $mysqli->query($empleadosA);
        $rowcountA=mysqli_num_rows($countEmpleA);

          $sqlSueldoA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpA = $mysqli->query($sqlSueldoA);
           $filaSA = mysqli_fetch_row($sueldoEmpA);

             $otrosPagosA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagA = $mysqli->query($otrosPagosA);
           $filaOA = mysqli_fetch_row($otrPagA);

            $presLeA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegA = $mysqli->query($presLeA);
           $filaPA = mysqli_fetch_row($presLegA);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountA?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSA[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOA[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPA[1],2,'.',',')?></strong></td>

    </tr>

    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Tecnico - Operativo</strong></td>
<?php
     $empleadosT="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui='Aseo'
                   AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmpleT = $mysqli->query($empleadosT);
        $rowcountT=mysqli_num_rows($countEmpleT);

          $sqlSueldoT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpT = $mysqli->query($sqlSueldoT);
           $filaST = mysqli_fetch_row($sueldoEmpT);

             $otrosPagosT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagT = $mysqli->query($otrosPagosT);
           $filaOT = mysqli_fetch_row($otrPagT);

            $presLeT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui='Aseo'
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegT = $mysqli->query($presLeT);
           $filaPT = mysqli_fetch_row($presLegT);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountT?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaST[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOT[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPT[1],2,'.',',')?></strong></td>

    </tr>

    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" style="background-color: Gray;" ><strong>Total Personal Temporal</strong></td>
     <?php
          
          $empleadosTEMP="SELECT DISTINCT   n.empleado
                      FROM gn_novedad n 
                      LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                      LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                      LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                      LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                      LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                      LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                      LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                      LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                      WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                      AND p.tipoprocesonomina=1
                      AND n.concepto=1
                      AND gg.servicio_sui IN ('Aseo')
                      AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                      AND ca.tipo_persona_sui='Temporal'
                      AND te.id_unico IN (3,4,5,6,7,8)";
                      
           $countEmpleTEMP = $mysqli->query($empleadosTEMP);
           $rowcountTEMP=mysqli_num_rows($countEmpleTEMP);
   
       
                $sqlSueldoTEMP="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Sueldo'
                                   AND gg.servicio_sui IN ('Aseo')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui='Temporal'
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $sueldoEmpTEMP = $mysqli->query($sqlSueldoTEMP);
              $filaSTEMP = mysqli_fetch_row($sueldoEmpTEMP);
   
               $otrosPagosTEMP="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                   AND gg.servicio_sui IN ('Aseo')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui='Temporal'
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $otrPagTEMP = $mysqli->query($otrosPagosTEMP);
              $filaOTEMP = mysqli_fetch_row($otrPagTEMP);
   
               $presLeTEMP="SELECT  n.id_unico,SUM(n.valor)
                                   FROM gn_novedad n 
                                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                   LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                   AND p.tipoprocesonomina=1
                                   AND c.equivalante_sui='Prestaciones Legales'
                                   AND gg.servicio_sui IN ('Aseo')
                                   AND ni.equivalente_sui IN ('Personal Directivo','Personal Tecnico - Operativo','Personal Administrativo')
                                   AND ca.tipo_persona_sui='Temporal'
                                   AND te.id_unico IN (3,4,5,6,7,8)";
             $presLegTEMP= $mysqli->query($presLeTEMP);
              $filaPTEMP = mysqli_fetch_row($presLegTEMP);
   
           ?>





       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo $rowcountTEMP?></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaSTEMP[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaOTEMP[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong><?php echo number_format($filaPTEMP[1],2,'.',',')?></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
       <td rowspan="2" colspan="2" align="center" ><strong>Personal Directivo</strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Administrativo</strong></td>
<?php
     $empleadosPA="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui IN ('Aseo')
                   AND ni.equivalente_sui IN ('Personal Administrativo')
                   AND ca.tipo_persona_sui='Temporal'
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmplePA = $mysqli->query($empleadosPA);
        $rowcountPA=mysqli_num_rows($countEmplePA);

          $sqlSueldoPA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui IN ('Aseo')
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpPA = $mysqli->query($sqlSueldoPA);
           $filaSPA = mysqli_fetch_row($sueldoEmpPA);

             $otrosPagosPA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui IN ('Aseo')
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagPA = $mysqli->query($otrosPagosPA);
           $filaOPA = mysqli_fetch_row($otrPagPA);

            $presLePA="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui IN ('Aseo')
                                AND ni.equivalente_sui IN ('Personal Administrativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegPA = $mysqli->query($presLePA);
           $filaPPA = mysqli_fetch_row($presLegPA);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountPA?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSPA[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOPA[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPPA[1],2,'.',',')?></strong></td>

    </tr>


    <tr>
    </tr>
    <tr>
     <td rowspan="2" colspan="2" align="center" ><strong>Personal Tecnico - Operativo</strong></td>
<?php
     $empleadosPT="SELECT DISTINCT   n.empleado
                   FROM gn_novedad n 
                   LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                   LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                   LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                   LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                   LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                   LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                   LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                   LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                   WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                   AND p.tipoprocesonomina=1
                   AND n.concepto=1
                   AND gg.servicio_sui IN ('Aseo')
                   AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                   AND ca.tipo_persona_sui='Temporal'
                   AND te.id_unico IN (3,4,5,6,7,8)";
                   
        $countEmplePT = $mysqli->query($empleadosPT);
        $rowcountPT=mysqli_num_rows($countEmplePT);

          $sqlSueldoPT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Sueldo'
                                AND gg.servicio_sui IN ('Aseo')
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $sueldoEmpPT = $mysqli->query($sqlSueldoPT);
           $filaSPT = mysqli_fetch_row($sueldoEmpPT);

             $otrosPagosPT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Otros Pagos Servicios Personales'
                                AND gg.servicio_sui IN ('Aseo')
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $otrPagPT = $mysqli->query($otrosPagosPT);
           $filaOPT = mysqli_fetch_row($otrPagPT);

            $presLePT="SELECT  n.id_unico,SUM(n.valor)
                                FROM gn_novedad n 
                                LEFT JOIN gn_periodo p ON p.id_unico= n.periodo
                                LEFT JOIN gn_empleado e ON e.id_unico=n.empleado
                                LEFT JOIN gf_tercero t ON t.id_unico=e.tercero
                                LEFT JOIN gn_grupo_gestion gg ON gg.id_unico=e.grupogestion
                                LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                                LEFT JOIN gn_tercero_categoria tc ON tc.empleado=e.id_unico
                                LEFT JOIN gn_categoria ca ON ca.id_unico=tc.categoria
                                LEFT JOIN gn_nivel ni ON ni.id_unico=ca.nivel
                                LEFT JOIN gn_empleado_tipo et ON et.empleado=e.id_unico
                                   LEFT JOIN gn_tipo_empleado te ON te.id_unico=et.tipo
                                WHERE p.fechainicio BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.fechafin BETWEEN '$fechaIni' AND '$fechaFini'
                                AND p.tipoprocesonomina=1
                                AND c.equivalante_sui='Prestaciones Legales'
                                AND gg.servicio_sui IN ('Aseo')
                                AND ni.equivalente_sui IN ('Personal Tecnico - Operativo')
                                AND ca.tipo_persona_sui='Temporal'
                                AND te.id_unico IN (3,4,5,6,7,8)";
          $presLegPT = $mysqli->query($presLePT);
           $filaPPT = mysqli_fetch_row($presLegPT);
    
?>





       <td rowspan="2" colspan="2" align="center"><strong><?php echo $rowcountPT?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaSPT[1],2,'.',',');?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaOPT[1],2,'.',',')?></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong><?php echo number_format($filaPPT[1],2,'.',',')?></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong>Total Personal Contratistas</strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
       <td rowspan="2" colspan="2" align="center" ><strong>Personal Directivo</strong></td>
       <td rowspan="2" colspan="2" align="center"></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
       <td rowspan="2" colspan="2" align="center" ><strong>Personal Administrativo</strong></td>
       <td rowspan="2" colspan="2" align="center"></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
       <td rowspan="2" colspan="2" align="center" ><strong>Personal Tecnico - Operativo</strong></td>
       <td rowspan="2" colspan="2" align="center"></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>

    </tr>

    <tr>
    </tr>
    <tr>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong>Vacantes</strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center" style="background-color: Gray;"><strong></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
       <td rowspan="2" colspan="2" align="center" ><strong>Personal Directivo</strong></td>
       <td rowspan="2" colspan="2" align="center"></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
       <td rowspan="2" colspan="2" align="center" ><strong>Personal Administrativo</strong></td>
       <td rowspan="2" colspan="2" align="center"></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>

    </tr>
    <tr>
    </tr>
    <tr>
       <td rowspan="2" colspan="2" align="center" ><strong>Personal Tecnico - Operativo</strong></td>
       <td rowspan="2" colspan="2" align="center"></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>
       <td rowspan="2" colspan="2" align="center"><strong></strong></td>

    </tr>
    <tr>
    </tr>

</table>
</body>
</html>