<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Sabana_Nomina.xls");
require_once("../Conexion/conexion.php");
session_start();
ini_set('max_execution_time', 0);
@$periodo       = $_GET['periodo'];

$compania = $_SESSION['compania'];
$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Informe_Homologaciones</title>
</head>
<body>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
<?php

    $colO = "";                 //Nombre de columna origen
    $colD = 0;                  //Contador de columnas destino
    $columnasDestino = "";      //Nombres de columnas Destino
    $tablaOrigen = "";          //Nombres de tabla Origen
    $tablasDestino = "";        //Nombres de tablas Destino
    $consultasTablaD = "";      //Consultas de tabla destino
    $idTH = "";                 //Id de las tablas homologables
    $consultaTablaO = "";       //Consulta de la tabla de origen
    ?>
    <thead>
        <tr>
            <?php

                $consulta = "SELECT         t.razonsocial as traz,
                                            t.tipoidentificacion as tide,
                                            ti.id_unico as tid,
                                            ti.nombre as tnom,
                                            t.numeroidentificacion tnum
                            FROM gf_tercero t
                            LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
                            WHERE t.id_unico = $compania";
                $cmp = $mysqli->query($consulta);
                while ($fila = mysqli_fetch_array($cmp)){
                
                        $nomcomp = mb_strtoupper($fila['traz']);       
                        $tipodoc = mb_strtoupper($fila['tnom']);       
                        $numdoc  = mb_strtoupper($fila['tnum']);   
            ?>

            <th>
                <?php echo $nomcomp ; ?>
            </th>
            <?php
                }
            ?>
        </tr>
        <tr>

        <?php
            $sqlper = "SELECT   p.id_unico, 
                                p.codigointerno, 
                                p.fechainicio, 
                                p.fechafin 
                        FROM gn_periodo p 
                        WHERE p.id_unico = $periodo";
            $per = $mysqli->query($sqlper);

            while($pern = mysqli_fetch_row($per)){
        ?>
            <th>
                Periodo:
            </th>
            <th>
                <?php echo $pern[1]; ?>
            </th>
            <th>
                Fecha Inicial:
            </th>
            <th>
                <?php echo $pern[2]; ?>
            </th>
            <th>
                Fecha Final:
            </th>
            <th>
                <?php echo $pern[3]; ?>
            </th>
        <?php 
            }
        ?>
        </tr>
        <tr>
           <?php 
           
           

            $sqlcon = "SELECT DISTINCT      n.concepto, 
                                            c.descripcion 
                        FROM gn_novedad n 
                        LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                            WHERE c.clase = 1 AND n.periodo = $periodo OR c.clase=2 AND n.periodo = $periodo OR c.clase = 3 AND n.periodo = $periodo OR c.clase = 4 AND n.periodo = $periodo OR c.clase = 5 AND n.periodo = $periodo
                            ORDER BY c.clase,c.id_unico";
            $con = $mysqli->query($sqlcon);
            $ncon = mysqli_num_rows($con);
            ?>
            <th>
                Código
            </th>
            <th>
                Número de Identificación
            </th>
            <th>
                Empleado
            </th>
             <th>
                Sal. Base
            </th>
            <?php
           while ($Tcon = mysqli_fetch_row($con)) {?>
               <th>

                   <?php echo $Tcon[1];?>
               </th>
           <?php }
            ?>
            </tr>
    </thead>
    <tbody>

          
            <?php
                $sqlemp =" SELECT    e.id_unico, 
                                    e.codigointerno, 
                                    e.tercero, 
                                    t.id_unico,
                                    t.numeroidentificacion, 
                                    CONCAT(t.nombreuno,'  ',t.nombredos,'  ',t.apellidouno,'  ',t.apellidodos),
                                    ca.salarioactual 
                            FROM gn_empleado e 
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
                            LEFT JOIN gn_tercero_categoria tc ON e.id_unico = tc.empleado
                            LEFT JOIN gn_categoria ca ON tc.categoria = ca.id_unico
                            WHERE e.id_unico !=2 ";

                $emp = $mysqli->query($sqlemp);

                while ($Cemp = mysqli_fetch_row($emp)) { ?>
                   <tr> 
                    <td>
                        <?php echo $Cemp[1];?>
                    </td>
                    <td>
                        <?php echo $Cemp[4];?>
                    </td>
                    <td>
                        <?php echo $Cemp[5];?>
                    </td>
                    <td>
                        <?php echo $Cemp[6];?>
                    </td>
                <?php

                    $sqlcon = "SELECT DISTINCT     n.concepto, 
                                                   c.descripcion 
                                FROM gn_novedad n 
                                LEFT JOIN gn_concepto c ON n.concepto = c.id_unico 
                                WHERE c.clase = 1 AND n.periodo = $periodo OR c.clase=2 AND n.periodo = $periodo OR c.clase = 3 AND n.periodo = $periodo OR c.clase = 4 AND n.periodo = $periodo OR c.clase = 5 AND n.periodo = $periodo 
                                ORDER BY c.clase,c.id_unico";
                    $con = $mysqli->query($sqlcon);
                        
                        while($Tcon = mysqli_fetch_array($con)){    
                            
                            $sqlconc = "SELECT  n.concepto, 
                                                n.valor
                                        
                                        FROM gn_novedad n 
                                        WHERE n.concepto = $Tcon[0] AND n.empleado = $Cemp[0]";
                            $con1  = $mysqli->query($sqlconc);
                            $ncon1 = mysqli_num_rows($con1);

                            if($ncon1 >= 1){

                                $vcon1 = mysqli_fetch_row($con1);
                ?>              
                                <td>
                                    <?php echo $vcon1[1]; ?>
                                </td>            
                <?php
                            }else{
                ?>
                                <td>
                                    0
                                </td>
                <?php                
                            }
                        }
                ?>
                </tr>
                <?php
                }

                ?>

    </tbody>  
           
</table>
</body>
</html>