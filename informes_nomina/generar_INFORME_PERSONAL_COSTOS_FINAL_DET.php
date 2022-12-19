<?php                                                                                     
##########################################################################################################################################################################
#13/10/2021 | Elkin O. | Se crea informe para conteo del personal 
##########################################################################################################################################################################

require_once("../Conexion/conexion.php");
require_once ('../Conexion/ConexionPDO.php');
session_start();

$con    = new ConexionPDO();
$compania = $_SESSION['compania'];
$panno = $_SESSION['anno'];
$anno=$_POST['sltAnnio'];
$formato=$_POST['sltExportar'];

$separador  = $_POST['separador'];
if($separador == 'tab') {   
    $separador = "\t";      
}


if ($formato==1) {
    // code...

header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Personal_Costos_Nomina_Detallado.xls");
ini_set('max_execution_time', 0);
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Personal</title>
</head>
<body>
<table width="30%" border="1" cellspacing="0" cellpadding="0">



            <tr>
                <td colspan="2"><center><strong>Concepto</strong></center></td>
                <td colspan="2"><center><strong>Grado</strong></center></td>
                <td colspan="2"><center><strong>Consecutivo</strong></center></td>
                 <td colspan="2"><center><strong>Cedula</strong></center></td>
                 <td colspan="2"><center><strong>Nombre empleado</strong></center></td>
                <td colspan="2"><center><strong>Denominacion</strong></center></td>
                <td colspan="2"><center><strong>Tipo Vinculacion</strong></center></td>
                <td colspan="2"><center><strong>Asig_Basica</strong></center></td>
                <td colspan="2"><center><strong>Gastos_Representacion</strong></center></td>
                <td colspan="2"><center><strong>PrimaTS</strong></center></td>
                <td colspan="2"><center><strong>Prima_Gestion</strong></center></td>
                <td colspan="2"><center><strong>Prima_Localizacion</strong></center></td>
                <td colspan="2"><center><strong>Prima_Coordinacion</strong></center></td>
                <td colspan="2"><center><strong>Prima_Riesgo</strong></center></td>
                <td colspan="2"><center><strong>Prima_Extraordinaria</strong></center></td>
                <td colspan="2"><center><strong>Prima_Altomando</strong></center></td>
                <td colspan="2"><center><strong>Prima_Sub_Alimentacion</strong></center></td>
                <td colspan="2"><center><strong>Auxilio_Transporte</strong></center></td>
                <td colspan="2"><center><strong>Prima_Antiguedad</strong></center></td>
                <td colspan="2"><center><strong>Prima_Servicios</strong></center></td>
                <td colspan="2"><center><strong>Prima_Navidad</strong></center></td>
                <td colspan="2"><center><strong>Bon_Servicios</strong></center></td>
                <td colspan="2"><center><strong>Bon_Recreacion</strong></center></td>
                <td colspan="2"><center><strong>Prima_vacaciones</strong></center></td>
                <td colspan="2"><center><strong>Prima_Actividad</strong></center></td>
                <td colspan="2"><center><strong>Otras_Primas</strong></center></td>
                <td colspan="2"><center><strong>Cesantias</strong></center></td>
                <td colspan="2"><center><strong>Interese_Cesantias</strong></center></td>
            </tr>
       
       
          

             <?php

                $equivalentesPERV="SELECT pc.concepto,
                                          pc.grado,
                                          pc.consecutivo,
                                          pc.Cedula,
                                          pc.nombre_Empleado,
                                          pc.denominacion,
                                          pc.tipo_vinculacion,
                                          pc.asig_basica,
                                          pc.gastos_representacion,
                                          pc.primats,
                                          pc.prima_gestion,
                                          pc.prima_localizacion,
                                          pc.prima_coordinacion,
                                          pc.prima_riesgo,
                                          pc.prima_extraordinaria,
                                          pc.prima_altomando,
                                          pc.prima_sub_alimentacion,
                                          pc.auxilio_transporte,
                                          pc.prima_antiguedad,
                                          pc.prima_servicios,
                                          pc.prima_navidad,
                                          pc.bon_servicios,
                                          pc.bon_recreacion,
                                          pc.prima_vacaciones,
                                          pc.prima_actividad,
                                          pc.otras_primas,
                                          pc.cesantias,
                                          pc.intereses_cesantias
                                          FROM   gn_personal_costos pc";
             $equivalentesPER = $mysqli->query($equivalentesPERV);
             
   

              while ($filasEq = mysqli_fetch_row($equivalentesPER)) {


               ?>
               <tr>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[0];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[1];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[2];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[3];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[4];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[5];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[6];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[7];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[8];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[9];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[10];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[11];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[12];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[13];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[14];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[15];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[16];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[17];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[18];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[19];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[20];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[21];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[22];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[23];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[24];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[25];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[26];?></strong></td>
                <td rowspan="2" colspan="2" align="center""><strong><?php echo $filasEq[27];?></strong></td>
                
<tr>

              

             <?php 


              }

            

             ?>


</table>
</body>
</html>
<?php }elseif ($formato==2) {

                $equivalentesPERV="SELECT pc.concepto,
                                          pc.grado,
                                          pc.consecutivo,
                                          pc.Cedula,
                                          pc.nombre_Empleado,
                                          pc.denominacion,
                                          pc.tipo_vinculacion,
                                          pc.asig_basica,
                                          pc.gastos_representacion,
                                          pc.primats,
                                          pc.prima_gestion,
                                          pc.prima_localizacion,
                                          pc.prima_coordinacion,
                                          pc.prima_riesgo,
                                          pc.prima_extraordinaria,
                                          pc.prima_altomando,
                                          pc.prima_sub_alimentacion,
                                          pc.auxilio_transporte,
                                          pc.prima_antiguedad,
                                          pc.prima_servicios,
                                          pc.prima_navidad,
                                          pc.bon_servicios,
                                          pc.bon_recreacion,
                                          pc.prima_vacaciones,
                                          pc.prima_actividad,
                                          pc.otras_primas,
                                          pc.cesantias,
                                          pc.intereses_cesantias
                                          FROM   gn_personal_costos pc";
             $equivalentesPER = $mysqli->query($equivalentesPERV);
           
               $html="";

              while ($filasEq = mysqli_fetch_row($equivalentesPER)) {


               $html .=$filasEq[0]."$separador";   
               $html .=$filasEq[1]."$separador";   
               $html .=$filasEq[2]."$separador";   
               $html .=$filasEq[3]."$separador";   
               $html .=$filasEq[4]."$separador";   
               $html .=$filasEq[5]."$separador";             
               $html .=$filasEq[6]."$separador";   
               $html .=$filasEq[7]."$separador";   
               $html .=$filasEq[8]."$separador";   
               $html .=$filasEq[9]."$separador";   
               $html .=$filasEq[10]."$separador";   
               $html .=$filasEq[11]."$separador";   
               $html .=$filasEq[12]."$separador";   
               $html .=$filasEq[13]."$separador";   
               $html .=$filasEq[14]."$separador";   
               $html .=$filasEq[15]."$separador";   
               $html .=$filasEq[16]."$separador";   
               $html .=$filasEq[17]."$separador";   
               $html .=$filasEq[18]."$separador";   
               $html .=$filasEq[19]."$separador";   
               $html .=$filasEq[20]."$separador";   
               $html .=$filasEq[21]."$separador";   
               $html .=$filasEq[22]."$separador";   
               $html .=$filasEq[23]."$separador";   
               $html .=$filasEq[24]."$separador";   
               $html .=$filasEq[25]."$separador";   
               $html .=$filasEq[26]."$separador";   
               $html .=$filasEq[27]."$separador";   
               $html .= "\n";





              

              }
             
            header("Content-Disposition: attachment; filename=Personal_Costos_Nomina_Detallado.csv");
              ini_set('max_execution_time', 0);
              echo $html;

             ?>

<?php }elseif($formato==3){

       $equivalentesPERV="SELECT pc.concepto,
                                          pc.grado,
                                          pc.consecutivo,
                                          pc.Cedula,
                                          pc.nombre_Empleado,
                                          pc.denominacion,
                                          pc.tipo_vinculacion,
                                          pc.asig_basica,
                                          pc.gastos_representacion,
                                          pc.primats,
                                          pc.prima_gestion,
                                          pc.prima_localizacion,
                                          pc.prima_coordinacion,
                                          pc.prima_riesgo,
                                          pc.prima_extraordinaria,
                                          pc.prima_altomando,
                                          pc.prima_sub_alimentacion,
                                          pc.auxilio_transporte,
                                          pc.prima_antiguedad,
                                          pc.prima_servicios,
                                          pc.prima_navidad,
                                          pc.bon_servicios,
                                          pc.bon_recreacion,
                                          pc.prima_vacaciones,
                                          pc.prima_actividad,
                                          pc.otras_primas,
                                          pc.cesantias,
                                          pc.intereses_cesantias
                                          FROM   gn_personal_costos pc";
             $equivalentesPER = $mysqli->query($equivalentesPERV);
           
               $html="";

              while ($filasEq = mysqli_fetch_row($equivalentesPER)) {


               $html .=$filasEq[0]."$separador";   
               $html .=$filasEq[1]."$separador";   
               $html .=$filasEq[2]."$separador";   
               $html .=$filasEq[3]."$separador";   
               $html .=$filasEq[4]."$separador";   
               $html .=$filasEq[5]."$separador";             
               $html .=$filasEq[6]."$separador";   
               $html .=$filasEq[7]."$separador";   
               $html .=$filasEq[8]."$separador";   
               $html .=$filasEq[9]."$separador";   
               $html .=$filasEq[10]."$separador";   
               $html .=$filasEq[11]."$separador";   
               $html .=$filasEq[12]."$separador";   
               $html .=$filasEq[13]."$separador";   
               $html .=$filasEq[14]."$separador";   
               $html .=$filasEq[15]."$separador";   
               $html .=$filasEq[16]."$separador";   
               $html .=$filasEq[17]."$separador";   
               $html .=$filasEq[18]."$separador";   
               $html .=$filasEq[19]."$separador";   
               $html .=$filasEq[20]."$separador";   
               $html .=$filasEq[21]."$separador";   
               $html .=$filasEq[22]."$separador";   
               $html .=$filasEq[23]."$separador";   
               $html .=$filasEq[24]."$separador";   
               $html .=$filasEq[25]."$separador";   
               $html .=$filasEq[26]."$separador";   
               $html .=$filasEq[27]."$separador";   
               $html .= "\n";





              

              }
             
                header("Content-Disposition: attachment; filename=Personal_Costos_Nomina_Detallado.txt");
                ini_set('max_execution_time', 0);
                echo $html;
}
             ?>