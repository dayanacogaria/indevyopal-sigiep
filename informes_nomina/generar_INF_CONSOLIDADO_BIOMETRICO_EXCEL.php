<?php                                                                                     
##########################################################################################################################################################################
#************ 22/10/2021- Elkin O- Se crea informe donde se muestran los conceptos de horas extras y se compara con
# con la info del archivo biometrico que se ingresa, dando asi la diferencia dependiendo el empleado***********#
##########################################################################################################################################################################
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Consolidad_Biométrico.xls");
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
ini_set('max_execution_time', 0); 
session_start();
$con  = new ConexionPDO();
$compania = $_SESSION['compania'];
$periodo=$_POST['sltPeriodo'];
$periodoM="SELECT codigointerno FROM gn_periodo
WHERE  id_unico='$periodo'";
$pe=$mysqli->query($periodoM);
$rowP=mysqli_fetch_row($pe);
$empleadoI=$_POST['sltEmpleadoI'];
$empleadoF=$_POST['sltEmpleadoF']; 
$docBioI="SELECT numeroidentificacion
 FROM gf_tercero t 
 LEFT JOIN gn_empleado e ON e.tercero=t.id_unico
 WHERE e.id_unico=$empleadoI";
 $eI=$mysqli->query($docBioI);
 $docI=mysqli_fetch_row($eI);

 $docBioF="SELECT numeroidentificacion
 FROM gf_tercero t 
 LEFT JOIN gn_empleado e ON e.tercero=t.id_unico
 WHERE e.id_unico=$empleadoI";
 $eF=$mysqli->query($docBioF);
 $docF=mysqli_fetch_row($eF);

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
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Auxiliar Presupuestal Gastos</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
    <tr>
        <th colspan="5" align="center"><strong>
            <br/>&nbsp;
            <br/><?php echo $razonsocial ?>
            <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
           <br/>&nbsp;
           <br/>CONSOLIDADO BIOMÉTRICO
           <br/>Periodo:<?php echo $rowP[0] ?>
            </strong>
        </th>
  </tr>

    
    
<?php
 $nombreEmp="SELECT CONCAT(nombreuno,' ',nombredos,' ',apellidouno,' ',apellidodos),numeroidentificacion,e.id_unico
 FROM gf_tercero t 
 LEFT JOIN gn_empleado e ON e.tercero=t.id_unico
 WHERE e.id_unico BETWEEN '$empleadoI' and '$empleadoF'";
 $nomE=$mysqli->query($nombreEmp);
 while($rowNom = mysqli_fetch_row($nomE)){

     $sqlConfig="SELECT concepto, nombre_campo 
     FROM gn_homologacion_biometrico";
     $config=$mysqli->query($sqlConfig);

        ?>
    <tr>
        <td colspan="5"><strong><i>Nombre Empleado: <?php echo $rowNom[0];?></i></strong></td>
       
    </tr>
    <tr>
      
        <td ><strong><i>CONCEPTO NÓMINA</i></strong></td>
        <td ><strong><i>CONCEPTO BIOMÉTRICO </i></strong></td>
        <td ><strong><i>VALOR CONCEPTO NÓMINA </i></strong></td>
        <td ><strong><i>VALOR CONCEPTO BIOMÉTRICO </i></strong></td>
        <td ><strong><i>DIFERENCIA </i></strong></td>
    </tr>
    
    <?php
     while($rowA = mysqli_fetch_row($config)){
        $sqlBio="SELECT 
                SUM(REPLACE($rowA[1], ',', '.')) AS extras
                from gn_empleado_asistencia
                where periodo=$periodo
                and numerodocumento='$rowNom[1]'";
        $bio=$mysqli->query($sqlBio);
        $rowBi = mysqli_fetch_row($bio);
        
        $sqlNo="SELECT n.valor,c.descripcion,c.codigo
                from gn_novedad n 
                 LEFT JOIN gn_concepto c ON c.id_unico=n.concepto
                 where periodo=$periodo
                 and n.empleado= '$rowNom[2]'
                 and n.concepto=$rowA[0]";
         $nov=$mysqli->query($sqlNo);
         $rowNo = mysqli_fetch_row($nov);    

        
        ?>
         
        <tr>
            <td align="left"><?php
            if($rowNo[1]==null){
                $sqlcf="SELECT c.descripcion,c.codigo
                from gn_concepto c 
                WHERE c.id_unico=$rowA[0]";
                $nomcc=$mysqli->query($sqlcf);
                $rowNocc = mysqli_fetch_row($nomcc);
                echo $rowNocc[1].'-'.$rowNocc[0];
            }else{
                echo $rowNo[2].'-'.$rowNo[1];
            }
           ?></td>
              <td align="left"><?php echo $rowA[1];?></td>
            <td align="right"><?php
            
            if($rowNo[0]==null){

              echo '0';
            
            }else{
                echo $rowNo[0];
            }
            ?></td>
    
            
         
            <td align="right"><?php
            if($rowBi[0]==null){
                   echo '0';
                 }else{
                    echo $rowBi[0];
                 }
           ?></td> 
            <td align="right"><strong><?php 
           $total=$rowNo[0]-$rowBi[0];
           echo $total;?></strong></td>
            </tr>
 
        <?php
     }
 }
?>
    
</table>
</body>
</html>