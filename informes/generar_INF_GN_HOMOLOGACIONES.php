<?php
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Informe_Homologaciones.xls");
require_once("../Conexion/conexion.php");
session_start();
$anno = $_SESSION['anno'];
ini_set('max_execution_time', 0);
$tipoI        = $mysqli->real_escape_string(''.$_POST["tipoInf"].'');
$informe        = $mysqli->real_escape_string(''.$_POST["nombre"].'');

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
           $idReport=$informe;
           $sqlColO = "SELECT  tbH.columna_origen,tbH.tabla_origen,tbH.select_table_origen
                        FROM    gn_tabla_homologable tbH
                        WHERE   tbH.informe = $idReport";
            $resultColO = $mysqli->query($sqlColO);
            $rowColO = $resultColO->fetch_row();
            $colO = $rowColO[0];            //Captura de columna origen
            $tablaOrigen = $rowColO[1];     //Captura del nombre de la tabla
            $consultaTablaO = str_replace("@", $anno,$rowColO[2]);  //Captura de select de la tabla origen
            ?>
            <th><?php echo $rowColO[1];?> </th>
            <th><?php echo $rowColO[1];?> </th>
            <?php 
            $sqlTableH = "SELECT  tbH.columna_destino,tbH.tabla_destino,tbH.select_table_destino,tbH.id
                        FROM    gn_tabla_homologable tbH
                        WHERE   tbH.informe = $idReport";
            $resultTableH = $mysqli->query($sqlTableH);
            while ($rowTH = $resultTableH->fetch_row()) {  //Impresión de valores devueltos por la consulta
                $colD++;  //Contador de columnas de destino
                ?>
                <th><?php echo ($rowTH[1])?></th>
                <?php 
                $columnasDestino.= $rowTH[0].","; //Captura de columnas destino
                $tablasDestino.= $rowTH[1].",";   //Captura de tablas destino
                $consultasTablaD.=str_replace("@", $anno,$rowTH[2]).";";  //Captura de consultas de tabla destino
                $idTH.=$rowTH[3].",";             //Captura de ids de tabla homologable
            }
            ?>
            </tr>
    </thead>
    <tbody>
        <?php 
            $columnasDestino = substr($columnasDestino,0,strlen($columnasDestino)-1);   //Quitamos la ultima coma
            $tablasDestino = substr($tablasDestino,0,strlen($tablasDestino)-1);         //Quitamos la ultima coma
            $idTH = substr($idTH,0,strlen($idTH)-1);                                    //Quitamos la ultima coma
            $columnD = explode(",",$columnasDestino);                                   //Array de columnas destino
            $tbD = explode(",", $tablasDestino);                                        //Array de tablas destino
            $selectDestino = explode(";",$consultasTablaD);                             //Array de selects destino
            $idTablaH = explode(",", $idTH);                                            //Array de las id de tabla homologable
           
            $sqlT = "$consultaTablaO ";
            $resultT = $mysqli->query($sqlT);
            $cantidad = mysqli_num_rows($resultT);
            $y = 0; //Contador de filas
            while($rowT = mysqli_fetch_row($resultT)){  //Impresión de valores devueltos por la consulta
                ++$y; //Contamos las filas
                ?>
                <tr>
                    <td><?php echo $rowT[0];?></td>
                    <td> <?php echo ucwords(mb_strtolower($rowT[1]))?></td>
                    <?php 
                   for ($a=0; $a <= $colD-1; ++$a) { //Ciclo de impresión para select ?>
                    
                   <td>
                   <?php  
                   
                   $sqlTD = $selectDestino[$a];    //Consulta de la tabla destino
                    $resultTD = $mysqli->query($sqlTD);
                    while($rowTD = mysqli_fetch_row($resultTD)){ //Impresión de valores
                        //Consulta para saber cuales registros o valores estan en gn_homologaciones
                        $sqlHom = "SELECT hom.id FROM gn_homologaciones hom
                                  LEFT JOIN
                                    gn_tabla_homologable th1 on hom.origen = th1.id
                                  LEFT JOIN
                                    gn_informe i on th1.informe = i.id
                                  WHERE hom.id_origen = '$rowT[0]' 
                                  AND hom.id_destino = '$rowTD[0]' 
                                  AND hom.origen = $idTablaH[$a]
                                  AND hom.destino = $idTablaH[$a]
                                  AND th1.informe = $idReport";
                        $resultHom = $mysqli->query($sqlHom);
                        $c = mysqli_fetch_row($resultHom);//Se carga el valor de la consulta
                        if(!empty($c[0])){//Se valida que es diferente de vacio
                            $pos = strpos($selectDestino[$a],"order"); //Buscamos la palabra order by en la Consulta
                            if(!empty($pos)) {//Validamos que no venga vacia
                              $str1 = substr($selectDestino[$a],0,$pos);  //Tomamos la Consulta desde la posición 0 hasta la posicion en la que se encontro la palabra
                              $str2 = substr($selectDestino[$a],$pos);    //Tomamos la consulta desde la posición en que se hayo la palbra
                              $sqlTD1 = $str1." AND id_unico = '$rowTD[0]' $str2"; //Armamos nuestra query para obtener el valor especifico
                            }else{
                              $sqlTD1 = $selectDestino[$a]." AND id_unico = '$rowTD[0]'";    //Consulta de la tabla destino cuando existe valor en la tabla gn_homologaciones
                            }
                            //echo $sqlTD1;
                            $resultTD1 = $mysqli->query($sqlTD1);
                            $rowTD1 = mysqli_fetch_row($resultTD1);//Carga de valores
                            echo ucwords(mb_strtolower($rowTD1[1]));
                            
                        }
                        
                    } ?>
                    </td>
                <?php } ?> 
                </tr>         
           <?php } ?>
           <tbody>           
           
</table>
</body>
</html>