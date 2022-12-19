<?php
session_start();
ini_set('max_execution_time', 0);
require_once('../funciones/funciones_informes.php');
require_once('../Conexion/conexion.php');
switch ($_REQUEST['x']) {
    case 1:
    	$texto     = $mysqli->real_escape_string(''.$_REQUEST['texto'].'');
      	$id_report = $mysqli->real_escape_string(''.$_REQUEST['id_report'].'');
        $xtexto    = $mysqli->real_escape_string(''.$_REQUEST['needle'].'');
      	#Declaramos variables en vacio para crear bosquejo y esqueleto en html para retornar al pedido
      	$html      = ""; $head = ""; $body = "";
      	$head      .= "<thead>";
       	$head      .= "\n\t\t</tr>";
      	#Función para obtener los valores del reporte
      	$report    = obtener_informe($id_report);
      	#Asignamos los valores encontrados a variables
		$id_inf    = $report[0]; $nom_inf = $report[1]; $qry_inf = $report[2];
		#Función para obtener los valores de la columna origen en la tabla gn_tabla_homologable
		$origen    = obtener_origen($id_inf);
		#Asignamos los valores devueltos en variables
		$id_tbh    = $origen[0]; $tb_ori  = $origen[1]; $col_ori = $origen[2]; $qry_ori = $origen[3];
		#Fila conocidad, creamos su estrucuta html
		$head      .= "\n\t\t\t<th class=\"cabeza cursor\" title=\"Tabla de Origen : ".ucwords($tb_ori)."\">".ucfirst(ucwords($col_ori))."</th>";
		#Función para obtener los valores de la columna destino en la tabla gn_tabla_homologable
		$destino   = obtener_destino($id_inf);
		#Creamos variables vacias para recibir los valores devueltos por el array que retornara
		$xdes      = 0; $colsDes = ""; $tabDes = ""; $querysDes = ""; $idtbh = "";
		if($destino->num_rows > 0){
			while($rowD = $destino->fetch_row()){
				$xdes++;
				$idtbh     .= $rowD[0].",";
				$colsDes   .= $rowD[1].",";
				$tabDes    .= $rowD[2].",";
				$querysDes .= $rowD[3].";";
				$head      .= "\n\t\t\t<th class=\"cabeza cursor danger\" width='100px'>".ucfirst(ucwords($rowD[1].PHP_EOL.'(Tabla: '.$rowD[2].')'))."</th>";
			}
		}
		$head    .= "\n\t\t</tr>";
		$head    .= "\n\t</thead>";
		#Quitamos la ultima coma a los string armados en el while anterior
		$idtbh   = substr($idtbh, 0, strlen($idtbh) - 1);
		$colsDes = substr($colsDes, 0, strlen($colsDes)- 1);
		$tabDes  = substr($tabDes, 0, strlen($tabDes) - 1);
		$querysDes = substr($querysDes, 0, strlen($querysDes) - 1);
		#Convertimos en array las variables
		$idtbh   = explode(",", $idtbh);
		$colsDes = explode(",", $colsDes);
		$tabDes  = explode(",", $tabDes);
		$querysDes = explode(";", $querysDes);
		$needle  = " $xtexto like '%$texto%' ";
		#Constrcción de la consulta para incluirle la variable $needle
 		$x1      = stripos(ucwords($qry_ori), "where");                 #Posición en donde encontramos la palabrea where dentro de la query
		$substr1 = substr($qry_ori, 0, $x1);                            #Extraemos un string de la consulta de la posición 0 hasta $x1
		$substr2 = substr($qry_ori, $x1,strlen($qry_ori));              #Extraemos un string desde $x1 hasta el tamaño total del string $qry_ori
		$x2      = stripos($substr2, "order");                          #Buscamos la posición en donde se encuentre la palabra order
		$substr3 = substr($substr2, 0, $x2);                            #Obtenemos un substring desde 0 hasta $x2 de $substr2
		$substr3 = substr($substr3, 5);                                 #A substr3 le quitamos la palabra where la cual es la posición de 0 a 5
		$substr4 = substr($substr2, $x2, strlen($substr2));             #Obtenemos un $substr de 2 desde $x2 hasta el total de $substr obteniendo el order
	    $substr  = $substr1."where".$needle." and ".$substr3.$substr4;  #Armamos el string de la consulta incluyendo nuestro $needle

		$res     = ejecutar_consulta($substr);
		$body    .= "\n\t<tbody>";
		$y       = 0;
		$x       = 0;
		if($res->num_rows > 0){
			while ($rowO = $res->fetch_row()) {
				$y++;
				$body  .= "\n\t\t<tr>";
				$body  .= "\n\t\t\t<td class='info' style=\"width:250px\"><span name=\"Origen$rowO[0]\" id=\"Origen$rowO[0]\">".(ucwords(mb_strtolower($rowO[1])))."</span>";
				$body  .= "\n\t\t\t</td>";
				for ($a = 0; $a <= $xdes-1; $a++) {
					$x++;
					$body .= "\n\t\t\t<td style='width:150px'>";
					$body .= "\n\t\t\t\t<input type=\"hidden\" id=\"txt$colsDes[$a]$x\" value=\"\">";
					$body .= "\n\t\t\t\t<select class=\"select2 form-control col-sm-1\" onchange=\"guardarHomologacion($rowO[0],this.value,".$idtbh[$a].",".$idtbh[$a].","."$('#txt".$colsDes[$a].$x."').val()".",'txt".$colsDes[$a].$x."');\" name=\"$colsDes[$a]$y\" id=\"$colsDes[$a]$y\" style=\"width:150px;align:center\">";
					$body.= "\n\t\t\t\t<option value=''>".ucfirst(ucwords($colsDes[$a]))."</option>"; //opción con el nombre del campo
					$sqlTD = $querysDes[$a];
                    $resultTD = $mysqli->query($sqlTD);
                    while($rowTD = $resultTD->fetch_row()){   //Impresión de valores
                    	if(!empty($rowTD[0])){
                    		$sqlHom = "SELECT    hom.id FROM gn_homologaciones hom
                                       LEFT JOIN gn_tabla_homologable th1 on hom.origen = th1.id
                                       LEFT JOIN gn_informe i on th1.informe = i.id
                                       WHERE     hom.id_origen  = '$rowO[0]'
                                       AND       hom.id_destino = '$rowTD[0]'
                                       AND       hom.origen     = $idtbh[$a]
                                       AND       hom.destino = $idtbh[$a]
                                       AND       th1.informe = $id_inf";
                            $resultHom = $mysqli->query($sqlHom);
                            $c = mysqli_fetch_row($resultHom);//Se carga el valor de la consulta
                            if(!empty($c[0])){                //Se valida que es diferente de vacio
                                $pos = strpos($querysDes[$a],"order"); //Buscamos la palabra order by en la Consulta
                            if(!empty($pos)) {//Validamos que no venga vacia
                                $str1 = substr($querysDes[$a],0,$pos);  //Tomamos la Consulta desde la posición 0 hasta la posicion en la que se encontro la palabra
                                $str2 = substr($querysDes[$a],$pos);    //Tomamos la consulta desde la posición en que se hayo la palbra
                                $sqlTD1 = $str1." WHERE id_unico = '$rowTD[0]' $str2"; //Armamos nuestra query para obtener el valor especifico
                            }else{
                                $sqlTD1 = $querysDes[$a]." WHERE id_unico = '$rowTD[0]'";    //Consulta de la tabla destino cuando existe valor en la tabla gn_homologaciones
                            }
                            $resultTD1 = $mysqli->query($sqlTD1);
                            $rowTD1 = mysqli_fetch_row($resultTD1);//Carga de valores
                                $body      .= "\n\t\t\t\t\t<option value=".$rowTD1[0]." selected>".ucwords(mb_strtolower($rowTD1[1]))."</option>";//Option con el valor optenido cuando exsite en la base de datos
                                $body      .= "\n\t\t\t\t\t<script>";
                                $body      .= "\n\t\t\t\t\t\t$(document).ready(function(){";
                                $body      .= "\n\t\t\t\t\t\t\tvar fila$x = $c[0];";
                                $body      .= "\n\t\t\t\t\t\t\t$(\"#txt$colsDes[$a]$x\").val(fila$x);";
                                $body      .= "\n\t\t\t\t\t\t});\n";
                                $body      .= "\n\t\t\t\t\t</script>";
                            }
                    	}
                    	$body.= "<option value='".$rowTD[0]."'>".ucwords(mb_strtolower($rowTD[1]))."</option>"; //Opción impresa
                    }
                    $body    .= "\n\t\t\t\t\t<option value=''>".ucfirst(ucwords($colsDes[$a]))."</option>";
					$body    .= "\n\t\t\t\t</select>";
					$body    .= "\n\t\t\t</td>";
				}
				$body  .= "\n\t\t</tr>";
			}
		}
		$body    .= "\n\t</tbody>";
		$html .= $head.$body;
		$html.= "<script>\n";            //Script para carga la libreria select2 en los combos o campos de seleccion
        $html.= "$('.select2').select2({";
        $html.= "allowClear:true";
        $html.= "});";
        $html.= "</script>\n";
		echo $html;
		break;
    case 2:
    	if(!empty($_REQUEST['report'])){ //Validamos que si la variable report no viene vacia
            //colO para capturar en un string el nombre de la columna de origen y tablaOrigen para capturar el nombre de tabla de origen
            $idReport = $_REQUEST['report'];
            //Consulta para obtener columna de origen por informe
            $sqlColO = "SELECT  select_table_origen
                        FROM    gn_tabla_homologable tbH
                        WHERE   tbH.informe = $idReport";
            $resultColO = $mysqli->query($sqlColO);
            $rowColO = $resultColO->fetch_row();
            //Captura de valores devueltos en la consulta
            $sqlT = $rowColO[0];        //Consulta origen
            //Consulta de tabla origen
            $resultT = $mysqli->query($sqlT);
            $fila    = $resultT->num_rows;//Cantidad de filas existente en la consulta
            //Impresión de valores retornados
            echo $fila;
        }
        break;
}

 ?>
