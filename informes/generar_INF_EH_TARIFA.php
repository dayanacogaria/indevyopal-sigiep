<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Concepto_Tarifa.xls");
require_once("../Conexion/conexion.php");
require_once("../Conexion/ConexionPDO.php");
session_start();
ini_set('max_execution_time', 0);
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
$usuario    = $_SESSION['usuario'];
$con = new ConexionPDO();
#***********************Datos Compañia***********************#
$rowC = $con->Listar("SELECT 
            ter.id_unico,
            ter.razonsocial,
            UPPER(ti.nombre),
            IF(ter.digitoverficacion IS NULL OR ter.digitoverficacion='',
                ter.numeroidentificacion, 
                CONCAT(ter.numeroidentificacion, ' - ', ter.digitoverficacion)),
            dir.direccion,
            tel.valor,
            ter.ruta_logo 
        FROM            
            gf_tercero ter
        LEFT JOIN 	
            gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico
        LEFT JOIN       
            gf_direccion dir ON dir.tercero = ter.id_unico
        LEFT JOIN 	
            gf_telefono  tel ON tel.tercero = ter.id_unico
        WHERE 
            ter.id_unico = $compania");

$razonsocial = $rowC[0][1];
$nombreIdent = $rowC[0][2];
$numeroIdent = $rowC[0][3];
$direccinTer = $rowC[0][4];
$telefonoTer = $rowC[0][5];
$ruta_logo   = $rowC[0][6];



#** Consulta Para conceptos ***#
$tf = $con->Listar("SELECT DISTINCT c.id_unico, c.nombre FROM gph_espacio_habitable_tarifa eht 
LEFT JOIN gph_espacio_habitable_concepto ehc ON eht.id_espacio_habitable_concepto = ehc.id_unico 
LEFT JOIN gp_concepto c ON ehc.id_concepto = c.id_unico 
WHERE eht.ano = $anno
ORDER BY c.nombre ASC");
$nc = count($tf)+3;

$row = $con->Listar("SELECT DISTINCT eh.id_unico, eh.codigo, eh.descripcion, eh.asociado 
FROM gph_espacio_habitable_tarifa eht 
LEFT JOIN gph_espacio_habitable_concepto ehc ON eht.id_espacio_habitable_concepto = ehc.id_unico 
LEFT JOIN gh_espacios_habitables eh ON ehc.id_espacio_habitable = eh.id_unico 
where eht.ano = $anno");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Informe Espacio Habitable Tarifa</title>
    </head>
    <body>
        <table width="100%" border="1" cellspacing="0" cellpadding="0">
            <th colspan="<?php echo $nc;?>" align="center"><strong>
                <br/>&nbsp;
                <br/><?php echo $razonsocial ?>
                <br/><?php echo $nombreIdent.' : '.$numeroIdent."<br/>".$direccinTer.' Tel:'.$telefonoTer ?>
                <br/>&nbsp;
                <br/>ESPACIO CONCEPTO TARIFA        
                <br/>&nbsp;                 
                </strong>
            </th>
            <tr></tr>
            <tr>
                <th>Asociado</th>
                <th>Código</th>
                <th>Descripción</th>                    
                <?php for ($c = 0; $c < count($tf); $c++) {
                    echo '<th>'.$tf[$c][1].'</th>';
                }?> 
            </tr>
            <tbody>
                <?php  for ($i = 0; $i < count($row); $i++) {
                    $id_espacio = $row[$i][0];
                    echo '<tr>';
                    #** Traer el asociado Mayor
                    $x          = 0;
                    $asociado   = "";
                    $id_asociado= $row[$i][3];
                    while($x==0){
                        #** Buscar Si el asociado tiene asociado 
                        $ba = $con->Listar("SELECT asociado FROM gh_espacios_habitables WHERE id_unico = $id_asociado");
                        if(!empty($ba[0][0])){
                            $id_asociado =$ba[0][0];
                        } else {
                            $x = 1;
                            $na = $con->Listar("SELECT codigo, descripcion  FROM gh_espacios_habitables WHERE id_unico = $id_asociado ");
                            $asociado = $na[0][0].' - '.$na[0][1];
                        }
                    }
                    echo '<td>'.$asociado.'</td>';
                    echo '<td>'.$row[$i][1].'</td>';
                    echo '<td>'.$row[$i][2].'</td>';
                    
                    for ($c = 0; $c < count($tf); $c++) {
                        echo '<td>';
                        $concepto = $tf[$c][0];
                        #** Buscar Concpeto tarifa 
                        $vc = $con->Listar("SELECT eht.valor FROM gph_espacio_habitable_tarifa eht 
                            LEFT JOIN gph_espacio_habitable_concepto eh ON eht.id_espacio_habitable_concepto = eh.id_unico 
                            WHERE eh.id_espacio_habitable = $id_espacio AND eh.id_concepto = $concepto  
                               AND eht.ano = $anno");
                        if(count($vc)>0){
                            for ($v = 0; $v < count($vc); $v++) {
                                echo number_format($vc[$v][0], 2, '.',',').'<br/>';
                            }
                            
                        } else {
                            echo '';
                        }
                        echo '</td>';
                    }
                    
                    echo '</tr>';
                }?>       
            </tbody>  
        </table>
    </body>
</html>