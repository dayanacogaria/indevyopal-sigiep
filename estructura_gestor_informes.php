<?php
#############################################################################################
#       ***************************     Modificaciones      ***************************     #
#############################################################################################
#19/04/2018 | Erica G.  | Parametrizacion
#27/07/2017 | Erica González | Nombre de informe para los encabezados
#############################################################################################
# Modificado Ferney Pérez Cano // 21/02/2017. // Modificado Case 9, consulta y array.
	require_once('Conexion/conexion.php');
    require_once('Conexion/ConexionPDO.php');
	session_start();
    $con = new ConexionPDO();
    $anno = $_SESSION['anno'];
    $compania = $_SESSION['compania'];
	$no_sentencias = array('delete', 'update', 'insert', 'create', 'alter', 'drop', 'truncate', 'mysql', 'show', 'databases', 'replace', 'optimize', 'grant', 'revoke', 'flush', 'explain', ' tables', 'lock', 'set', 'start', 'analyze', 'check', 'handler', 'kill', 'load ', 'reset ');
	$estruc = $_POST['estruc'];
	switch ($estruc) {
		case 1:
			$tabla = $_POST['tabla'];

			$sqlColDestino = "SELECT DISTINCT column_name
                FROM INFORMATION_SCHEMA.COLUMNS
				WHERE table_name = '$tabla'";
            $colDestino = $mysqli->query($sqlColDestino);
            while($rowCD = mysqli_fetch_row($colDestino)) {
            	echo '<option value="'.$rowCD[0].'">'.(($rowCD[0])).'</option>';
            }
			break;
		case 2:
			$id = $_POST['id'];
			$tipoInf = $_POST['tipoInf'];
			$consulta = $_POST['consulta'];
			$consulta = str_replace("@", $anno,$consulta);
            $consulta = str_replace("$", $compania,$consulta);
			$errores = "";
			$num_filas = 0;
			$num_columnas = 0;

			$sqlConsulta = $consulta;
            $consultaF = $mysqli->query($sqlConsulta);
            $errores = $mysqli->error;

            if($errores == "")
            {
            	$num_filas = $consultaF->num_rows;
				if($num_filas > 0)
				{
					$num_columnas = $mysqli->field_count;
					$columnas_gi = array();
  					for ($i=0; $i < $num_columnas; $i++)
  					{
  						$info_campo = mysqli_fetch_field_direct($consultaF, $i);
    					$columnas_gi[$i] = $info_campo->name;
  					}
  					$columnas_gi_ser = serialize($columnas_gi);

  					$_SESSION['columnas_gi'] = $columnas_gi_ser;
					$_SESSION['consulta_gi'] = $consulta;
					$_SESSION['id_gi'] = $id;
					$_SESSION['tipoInf_gi'] = $tipoInf;
					echo 1;
				}
				else
				{
					echo 0;
            	}
            }
            else
            {
            	echo $errores;
            }
			break;
		case 3:
			$nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
			$idTipoInforme = '"'.$mysqli->real_escape_string(''.$_POST['idTipoInforme'].'').'"';
                                                                $clase = $_POST['clase'];
                                                                if(!empty($_POST['nombreEncabezado'])){
                                                                    $nomE = "'".$_POST['nombreEncabezado']."'";
                                                                } else {
                                                                    $nomE = 'NULL';
                                                                }
			$sqlInsert = "INSERT INTO gn_informe (nombre, tipo_informe, clase_informe, nombre_informe)
    			VALUES($nombre, $idTipoInforme, $clase, $nomE)";
  			$resultado = $mysqli->query($sqlInsert);
  			if($resultado == true)
  			{
  				echo 1;
  			}
  			else
  			{
  				echo 0;
  			}
			break;
		case 4:
			$nombre  = '"'.$mysqli->real_escape_string(''.$_POST['nombre'].'').'"';
			$nombre  = ($nombre);
			$id = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';
                                                                $clase = $_POST['clase'];
                                                                if(!empty($_POST['nombreE'])){
                                                                    $nomE = "'".$_POST['nombreE']."'";
                                                                } else {
                                                                    $nomE = 'NULL';
                                                                }
			 $update = "UPDATE gn_informe
    			SET nombre = $nombre, clase_informe = $clase, nombre_informe = $nomE 
    			WHERE id = $id";
  			$resultado = $mysqli->query($update);
  			if($resultado == true)
  			{
                echo 1;
  			}
  			else
  			{
                echo 0;
  			}
			break;
		case 5:
			$tipoInf = $_POST['tipoInf'];

			$sqlInforme = "SELECT id, nombre
				FROM gn_informe
				WHERE tipo_informe = '$tipoInf'";
            $informe = $mysqli->query($sqlInforme);
            while($rowI = mysqli_fetch_row($informe))
            {
            	echo '<option value="'.$rowI[0].'">'.(($rowI[1])).'</option>';
            }
			break;
		case 6:
			$id_informe = $_POST['nombre'];

			$sqlConsultaE = "SELECT select_table
				FROM gn_informe
				WHERE id = '$id_informe'";
            $consultaE = $mysqli->query($sqlConsultaE);
            $rowCE = mysqli_fetch_row($consultaE);
            echo $rowCE[0];
			break;
		case 7:
			$consulta  = '"'.$mysqli->real_escape_string(''.$_POST['consulta'].'').'"';
			$consulta  = ($consulta);
            foreach ($no_sentencias as  $value)
            {
                $consulta = str_replace($value, '', $consulta);
            }

			$id = '"'.$mysqli->real_escape_string(''.$_POST['id'].'').'"';

			$update = "UPDATE gn_informe
    			SET select_table = $consulta
    			WHERE id = $id";
  			$resultado = $mysqli->query($update);
  			if($resultado == true)
  			{
  				echo 1;
  			}
  			else
  			{
  				echo 0;
  			}
			break;
        case 8:
            $id_tipo_inf  = '"'.$mysqli->real_escape_string(''.$_POST['tipoInf'].'').'"';
            $sqlInforme = "SELECT id, nombre
                FROM gn_informe
                WHERE tipo_informe = $id_tipo_inf
                ORDER BY nombre ASC";
            $informe = $mysqli->query($sqlInforme);
            while($rowI = mysqli_fetch_row($informe))
            {
                echo '<option value="'.$rowI[0].'">'.(($rowI[1])).'</option>';
            }
            break;
        case 9:
            $id_informe  = '"'.$mysqli->real_escape_string(''.$_POST['informe'].'').'"';

            $sql = 'SELECT tabHom.id, tabHom.tabla_origen, tabHom.columna_origen, tabHom.tabla_destino, tabHom.columna_destino, per.nombre
                FROM gn_tabla_homologable tabHom
                LEFT JOIN gn_periodicidad per ON per.id = tabHom.periodicidad
                WHERE tabHom.informe = '.$id_informe;
            $resultado = $mysqli->query($sql);

            $arreglo["data"] = [];
            while($row = mysqli_fetch_assoc($resultado))
            {
                $arreglo["data"][] = array_map("utf8_encode", $row);
            }
            echo json_encode($arreglo);
            break;
        case 10:
            $tabla = $_POST['laTabla'];
            $consultaSQL = "SELECT ";
            $num = 0;
            $cont = 0;

            $sqlcolTab = "SELECT column_name
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE table_name = '$tabla'";
            $colTab = $mysqli->query($sqlcolTab);
            $num = $colTab->num_rows;
            while($rowCT = mysqli_fetch_row($colTab))
            {
                $consultaSQL .= $rowCT[0];
                $cont ++;
                if($cont < $num)
                {
                    $consultaSQL .= ", ";
                }
            }

            $consultaSQL .= " FROM ".$tabla;

            echo $consultaSQL;
            break;
        #   ***********     Validar Consulta      ***********     #    
        case 11:
            $consulta = $_POST['consulta'];
            $consulta = trim($consulta);
            $consulta = str_replace("@", $anno,$consulta);
            $consulta = str_replace("$", $compania,$consulta);
            //echo $consulta;
            $errores = "";
            $num_filas = 0;
            $num_columnas = 0;

            $sqlConsulta = $consulta;
            $consultaF = $mysqli->query($sqlConsulta);
            $errores = $mysqli->error;

            if($errores == "")
            {
                $num_filas = $consultaF->num_rows;
			    if($num_filas > 0)
                {
                    echo 1;
                }
			    else
			    {
                    echo 0;
                }
            }
            else
            {
                echo $errores;
            }
			break;
        case 12:
            $tabOrig  = '"'.$mysqli->real_escape_string(''.$_POST['tabOrig'].'').'"';
            $colOrg  = '"'.$mysqli->real_escape_string(''.$_POST['colOrg'].'').'"';
            $tabDes  = '"'.$mysqli->real_escape_string(''.$_POST['tabDes'].'').'"';
            $colDes  = '"'.$mysqli->real_escape_string(''.$_POST['colDes'].'').'"';
            $informe  = '"'.$mysqli->real_escape_string(''.$_POST['informe'].'').'"';
            $periodicidad  = '"'.$mysqli->real_escape_string(''.$_POST['periodicidad'].'').'"';
            $tipo = $_POST["tipoInforme"];

            $select_table_origen  = '"'.$mysqli->real_escape_string(''.$_POST['select_table_origen'].'').'"';
            $select_table_destino  = '"'.$mysqli->real_escape_string(''.$_POST['select_table_destino'].'').'"';

            $num = 0;
            $sqlBuscar = "SELECT id
                FROM gn_tabla_homologable
                WHERE informe       = $informe
                AND tabla_origen    = $tabOrig
                AND tabla_destino   = $tabDes
                AND columna_origen  != $colOrg
                AND columna_destino = $colDes";
            $buscar = $mysqli->query($sqlBuscar);
            $num = $buscar->num_rows;

            if($num == 0)
            {
                $origenCap = "";
                $origenDat = "";

                $destinoCap = "";
                $destinoDat = "";

                if($select_table_origen != '""')
                {
                    $select_table_origen = ($select_table_origen);

                    foreach ($no_sentencias as  $value)
                    {
                        $select_table_origen = str_replace($value, '', $select_table_origen);
                    }

                    $origenCap = ", select_table_origen";
                    $origenDat = ", ".$select_table_origen;
                }

                if($select_table_destino != '""')
                {
                    $select_table_destino = ($select_table_destino);

                        foreach ($no_sentencias as  $value)
                    {
                        $select_table_destino = str_replace($value, '', $select_table_destino);
                    }

                    $destinoCap = ", select_table_destino";
                    $destinoDat = ", ".$select_table_destino;
                }

               $sqlInsert = "INSERT INTO gn_tabla_homologable (tipo, tabla_origen, columna_origen, tabla_destino, columna_destino, informe, periodicidad $origenCap $destinoCap)
                    VALUES($tipo,$tabOrig, $colOrg, $tabDes, $colDes, $informe, $periodicidad $origenDat $destinoDat)";
                $resultado = $mysqli->query($sqlInsert);

                if($resultado == true)
                {
                    echo 1;
                }
                else
                {
                    echo 2;
                }
            }
            else
            {
                echo 3;
            }
            break;
        case 13:
            $id = $_POST['id'];
            $query = 'DELETE FROM gn_tabla_homologable WHERE id ='. $id;
            $resultado = $mysqli->query($query);
            if($resultado == true)
            {
                echo 1;
            }
            else
            {
                echo 2;
            }
            break;
        case 14:
            $id = $_POST['id'];
            $res = '';

            $sqlCons = 'SELECT tabla_origen, columna_origen, select_table_origen, tabla_destino, columna_destino, select_table_destino, periodicidad
                FROM gn_tabla_homologable
                WHERE id = '.$id;
            $consulta = $mysqli->query($sqlCons);
            $row = mysqli_fetch_row($consulta);

            for($i = 0; $i < 7; $i++)
            {
                $res .= $row[$i].'|';
            }
            echo $res;
            break;
        case 15:
            $idTablaHomol = $_POST['idTablaHomol'];
            $tabOrig  = '"'.$mysqli->real_escape_string(''.$_POST['tabOrig'].'').'"';
            $colOrg  = '"'.$mysqli->real_escape_string(''.$_POST['colOrg'].'').'"';
            $tabDes  = '"'.$mysqli->real_escape_string(''.$_POST['tabDes'].'').'"';
            $colDes  = '"'.$mysqli->real_escape_string(''.$_POST['colDes'].'').'"';
            $informe  = '"'.$mysqli->real_escape_string(''.$_POST['informe'].'').'"';
            $periodicidad  = '"'.$mysqli->real_escape_string(''.$_POST['periodicidad'].'').'"';

            $select_table_origen  = '"'.$mysqli->real_escape_string(''.$_POST['select_table_origen'].'').'"';
            $select_table_destino  = '"'.$mysqli->real_escape_string(''.$_POST['select_table_destino'].'').'"';

            $num = 0;
            $sqlBuscar = 'SELECT id
                FROM gn_tabla_homologable
                WHERE informe = '.$informe.'
                AND tabla_origen = '.$tabOrig.'
                AND tabla_destino = '.$tabDes.'
                AND id != '.$idTablaHomol;
            $buscar = $mysqli->query($sqlBuscar);
            $num = $buscar->num_rows;

            if($num == 0)
            {
                $select_table_origen = ($select_table_origen);
                $select_table_destino = ($select_table_destino);
                foreach ($no_sentencias as  $value)
                {
                    $select_table_origen = str_replace($value, '', $select_table_origen);

                    $select_table_destino = str_replace($value, '', $select_table_destino);
                }

                $sqlUpdate = 'UPDATE gn_tabla_homologable
                    SET tabla_origen = '.$tabOrig.', columna_origen = '.$colOrg.', tabla_destino = '.$tabDes.', columna_destino = '.$colDes.', periodicidad = '.$periodicidad.', select_table_origen = '.$select_table_origen.', select_table_destino = '.$select_table_destino.'
                    WHERE id = '.$idTablaHomol;
                $resultado = $mysqli->query($sqlUpdate);

                if($resultado == true)
                {
                    echo 1;
                }
                else
                {
                    echo 2;
                }
            }
            else
            {
                echo 3;
            }
            break;

        #* INFORMES CUIPO
        case 16:
            $id_informe = $_REQUEST['id'];
            $row = $con->Listar("TRUNCATE TABLE tmp_cuipo "); 

            //TIPO ENTIDAD 
            $te = $con->Listar("SELECT valor FROM gs_parametros_basicos_sistema WHERE nombre ='cuipo'");
            switch( $te[0][0] ){
                #Municipios
                case '1':
                    switch($id_informe){
                        case 35:
                            $row = $con->Listar("SELECT DISTINCT rubro_fuente,  ptto_inicial, presupuesto_dfvo, recaudos 
                                FROM temporal_consulta_pptal_gastos 
                                WHERE  rubro_fuente !='' ");
                            for ($i=0; $i < count($row); $i++) { 
                                $id_rf      = $row[$i][0];
                                $ptoI       = $row[$i][1];
                                $ptoD       = $row[$i][2];
                                $recaudos   = $row[$i][3];

                                #Concepto 
                                $rowc = $con->Listar("SELECT DISTINCT h.id_origen, h.id_destino 
                                    FROM gn_homologaciones h where h.id_origen = $id_rf AND origen = 74");
                                $sql_cons ="INSERT INTO `tmp_cuipo` 
                                    ( `id_rf`, `cuipo_ingresos`, `presupuesto_inicial`, `presupuesto_definitivo`) 
                                VALUES (:id_rf, :cuipo_ingresos , :presupuesto_inicial, :presupuesto_definitivo)";
                                $sql_dato = array(
                                    array(":id_rf",$id_rf),
                                    array(":cuipo_ingresos",$rowc[0][1]),
                                    array(":presupuesto_inicial",$ptoI),
                                    array(":presupuesto_definitivo",$ptoD),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }    
                            echo 'CASE35';        
                        break;
                        case 36:
                            $row = $con->Listar("SELECT DISTINCT rubro_fuente,  ptto_inicial, presupuesto_dfvo, recaudos 
                                FROM temporal_consulta_pptal_gastos 
                                WHERE  rubro_fuente !='' ");
                            for ($i=0; $i < count($row); $i++) { 
                                $id_rf      = $row[$i][0];
                                $ptoI       = $row[$i][1];
                                $ptoD       = $row[$i][2];
                                $recaudos   = $row[$i][3];

                                #Concepto 
                                $rowc = $con->Listar("SELECT DISTINCT h.id_origen, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 74) as concepto, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 75) as cpc , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 76) as fuentes, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 77) as Aplicadestinacione , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 78) as Tipo_Norma , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 79) as Norma , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 80) as Fecha_Norma , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 81) as Terceros , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 82) as PoliticaPublica , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 83) as situacion_fondo , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 97) as cuipo_detalle_s  
                                FROM gn_homologaciones h where h.id_origen = $id_rf");

                                $sql_cons ="INSERT INTO `tmp_cuipo` 
                                    ( `id_rf`, `cuipo_ingresos`, `cpc`,`fuentes`,
                                    `destinacion`,`tipo_norma`,`norma`,`fecha_norma`,
                                    `tercero`,`politicas`,`situacion_fondo`,`recaudos`,`cuipo_detalle_s`) 
                                VALUES (:id_rf, :cuipo_ingresos , :cpc, :fuentes,
                                    :destinacion, :tipo_norma, :norma, :fecha_norma,
                                    :tercero, :politicas, :situacion_fondo, :recaudos, :cuipo_detalle_s)";
                                $sql_dato = array(
                                    array(":id_rf",$id_rf),
                                    array(":cuipo_ingresos",$rowc[0][1]),
                                    array(":cpc",$rowc[0][2]),
                                    array(":fuentes",$rowc[0][3]),
                                    array(":destinacion",$rowc[0][4]),
                                    array(":tipo_norma",$rowc[0][5]),
                                    array(":norma",$rowc[0][6]),
                                    array(":fecha_norma",$rowc[0][7]),
                                    array(":tercero",$rowc[0][8]),
                                    array(":politicas",$rowc[0][9]),
                                    array(":situacion_fondo",$rowc[0][10]),
                                    array(":recaudos",$recaudos),
                                    array(":cuipo_detalle_s",$rowc[0][11]),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }    
                            echo 'CASE36';         
                        break;
                        case 37:
                            $row = $con->Listar("SELECT DISTINCT rubro_fuente,  ptto_inicial, presupuesto_dfvo
                                FROM temporal_consulta_pptal_gastos 
                                WHERE  rubro_fuente !='' ");
                            for ($i=0; $i < count($row); $i++) { 
                                $id_rf      = $row[$i][0];
                                $ptoI       = $row[$i][1];
                                $ptoD       = $row[$i][2];

                                #Concepto 
                                $rowc = $con->Listar("SELECT DISTINCT h.id_origen, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 84) as concepto, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 85) as vigencia , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 86) as seccion_presup, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 87) as programat_mga1 , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 91) as bpin  
                                FROM gn_homologaciones h where h.id_origen = $id_rf");

                                $sql_cons ="INSERT INTO `tmp_cuipo` 
                                    ( `id_rf`, `cuipo_gastos`, `vigencia_gasto`,`seccion_p`,
                                    `programa_mga1`,`bpin`,`presupuesto_inicial`, `presupuesto_definitivo` ) 
                                VALUES (:id_rf, :cuipo_gastos , :vigencia_gasto, :seccion_p,
                                    :programa_mga1, :bpin, :presupuesto_inicial, :presupuesto_definitivo)";
                                $sql_dato = array(
                                    array(":id_rf",$id_rf),
                                    array(":cuipo_gastos",$rowc[0][1]),
                                    array(":vigencia_gasto",$rowc[0][2]),
                                    array(":seccion_p",$rowc[0][3]),
                                    array(":programa_mga1",$rowc[0][4]),
                                    array(":bpin",$rowc[0][5]),
                                    array(":presupuesto_inicial",$ptoI),
                                    array(":presupuesto_definitivo",$ptoD),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }    
                            echo 'CASE37';        
                        break;
                        case 38:
                            $row = $con->Listar("SELECT DISTINCT rubro_fuente, registros, total_obligaciones, total_pagos
                                FROM temporal_consulta_pptal_gastos 
                                WHERE  rubro_fuente !='' ");
                            for ($i=0; $i < count($row); $i++) { 
                                $id_rf        = $row[$i][0];
                                $compromisos  = $row[$i][1];
                                $obligaciones = $row[$i][2];
                                $pagos        = $row[$i][3];

                                #Concepto 
                                $rowc = $con->Listar("SELECT DISTINCT h.id_origen, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 84) as concepto, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 85) as vigencia , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 86) as seccion_presup, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 88) as programat_mga2 , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 89) as cpc, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 90) as fuentes, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 91) as bpin  , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 92) as situacion_fondo , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 93) as PoliticaPublica , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 94) as Terceros  
                                FROM gn_homologaciones h where h.id_origen = $id_rf");

                                $sql_cons ="INSERT INTO `tmp_cuipo` 
                                    ( `id_rf`, `cuipo_gastos`, `vigencia_gasto`,`seccion_p`,
                                    `programa_mga2`,`cpc`,`fuentes`,
                                    `bpin`,`situacion_fondo`,`politicas`,`tercero`, 
                                    `registros`,`obligaciones`,`pagos` ) 
                                VALUES (:id_rf, :cuipo_gastos , :vigencia_gasto, :seccion_p,
                                    :programa_mga2,:cpc, :fuentes,  
                                    :bpin, :situacion_fondo, :politicas, :tercero, 
                                    :registros, :obligaciones, :pagos)";
                                $sql_dato = array(
                                    array(":id_rf",$id_rf),
                                    array(":cuipo_gastos",$rowc[0][1]),
                                    array(":vigencia_gasto",$rowc[0][2]),
                                    array(":seccion_p",$rowc[0][3]),
                                    array(":programa_mga2",$rowc[0][4]),
                                    array(":cpc",$rowc[0][5]),
                                    array(":fuentes",$rowc[0][6]),
                                    array(":bpin",$rowc[0][7]),
                                    array(":situacion_fondo",$rowc[0][8]),
                                    array(":politicas",$rowc[0][9]),
                                    array(":tercero",$rowc[0][10]),
                                    array(":registros",$compromisos),
                                    array(":obligaciones",$obligaciones),
                                    array(":pagos",$pagos),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }    
                            echo 'CASE38';        
                        break;
                    }
                break;
                #Otras Entidades
                case '2':
                    switch($id_informe){
                        case 35:
                            $row = $con->Listar("SELECT DISTINCT rubro_fuente,  ptto_inicial, presupuesto_dfvo, recaudos 
                                FROM temporal_consulta_pptal_gastos 
                                WHERE  rubro_fuente !='' ");
                            for ($i=0; $i < count($row); $i++) { 
                                $id_rf      = $row[$i][0];
                                $ptoI       = $row[$i][1];
                                $ptoD       = $row[$i][2];
                                $recaudos   = $row[$i][3];

                                #Concepto 
                                $rowc = $con->Listar("SELECT DISTINCT h.id_origen, h.id_destino 
                                    FROM gn_homologaciones h where h.id_origen = $id_rf AND origen = 74");
                                $sql_cons ="INSERT INTO `tmp_cuipo` 
                                    ( `id_rf`, `cuipo_ingresos`, `presupuesto_inicial`, `presupuesto_definitivo`) 
                                VALUES (:id_rf, :cuipo_ingresos , :presupuesto_inicial, :presupuesto_definitivo)";
                                $sql_dato = array(
                                    array(":id_rf",$id_rf),
                                    array(":cuipo_ingresos",$rowc[0][1]),
                                    array(":presupuesto_inicial",$ptoI),
                                    array(":presupuesto_definitivo",$ptoD),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }    
                            echo 'CASE35';        
                        break;
                        case 36:
                            $row = $con->Listar("SELECT DISTINCT rubro_fuente,  ptto_inicial, presupuesto_dfvo, recaudos 
                                FROM temporal_consulta_pptal_gastos 
                                WHERE  rubro_fuente !='' ");
                            for ($i=0; $i < count($row); $i++) { 
                                $id_rf      = $row[$i][0];
                                $ptoI       = $row[$i][1];
                                $ptoD       = $row[$i][2];
                                $recaudos   = $row[$i][3];

                                #Concepto 
                                $rowc = $con->Listar("SELECT DISTINCT h.id_origen, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 74) as concepto, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 75) as cpc , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 76) as fuentes, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 77) as Aplicadestinacione , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 78) as Tipo_Norma , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 79) as Norma , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 80) as Fecha_Norma , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 81) as Terceros , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 82) as PoliticaPublica , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 83) as situacion_fondo  
                                FROM gn_homologaciones h where h.id_origen = $id_rf");

                                $sql_cons ="INSERT INTO `tmp_cuipo` 
                                    ( `id_rf`, `cuipo_ingresos`, `cpc`,`fuentes`,
                                    `destinacion`,`tipo_norma`,`norma`,`fecha_norma`,
                                    `tercero`,`politicas`,`situacion_fondo`,`recaudos`) 
                                VALUES (:id_rf, :cuipo_ingresos , :cpc, :fuentes,
                                    :destinacion, :tipo_norma, :norma, :fecha_norma,
                                    :tercero, :politicas, :situacion_fondo, :recaudos)";
                                $sql_dato = array(
                                    array(":id_rf",$id_rf),
                                    array(":cuipo_ingresos",$rowc[0][1]),
                                    array(":cpc",$rowc[0][2]),
                                    array(":fuentes",$rowc[0][3]),
                                    array(":destinacion",$rowc[0][4]),
                                    array(":tipo_norma",$rowc[0][5]),
                                    array(":norma",$rowc[0][6]),
                                    array(":fecha_norma",$rowc[0][7]),
                                    array(":tercero",$rowc[0][8]),
                                    array(":politicas",$rowc[0][9]),
                                    array(":situacion_fondo",$rowc[0][10]),
                                    array(":recaudos",$recaudos),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }    
                            echo 'CASE36';         
                        break;
                        case 37:
                            $row = $con->Listar("SELECT DISTINCT rubro_fuente,  ptto_inicial, presupuesto_dfvo
                                FROM temporal_consulta_pptal_gastos 
                                WHERE  rubro_fuente !='' ");
                            for ($i=0; $i < count($row); $i++) { 
                                $id_rf      = $row[$i][0];
                                $ptoI       = $row[$i][1];
                                $ptoD       = $row[$i][2];

                                #Concepto 
                                $rowc = $con->Listar("SELECT DISTINCT h.id_origen, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 84) as concepto, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 85) as vigencia , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 86) as seccion_presup, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 87) as programat_mga1 , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 91) as bpin, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 87) as sector     
                                FROM gn_homologaciones h where h.id_origen = $id_rf"); 

                                $sql_cons ="INSERT INTO `tmp_cuipo` 
                                    ( `id_rf`, `cuipo_gastos`, `vigencia_gasto`,`seccion_p`,
                                    `programa_mga1`,`bpin`,`presupuesto_inicial`, `presupuesto_definitivo`, `sector` ) 
                                VALUES (:id_rf, :cuipo_gastos , :vigencia_gasto, :seccion_p,
                                    :programa_mga1, :bpin, :presupuesto_inicial, :presupuesto_definitivo, :sector)";
                                $sql_dato = array(
                                    array(":id_rf",$id_rf),
                                    array(":cuipo_gastos",$rowc[0][1]),
                                    array(":vigencia_gasto",$rowc[0][2]),
                                    array(":seccion_p",$rowc[0][3]),
                                    array(":programa_mga1",$rowc[0][4]),
                                    array(":bpin",$rowc[0][5]),
                                    array(":presupuesto_inicial",$ptoI),
                                    array(":presupuesto_definitivo",$ptoD),
                                    array(":sector",$rowc[0][6]),
                                );
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }    
                            echo 'CASE37';        
                        break;
                        case 38:
                            $row = $con->Listar("SELECT DISTINCT rubro_fuente, registros, total_obligaciones, total_pagos
                                FROM temporal_consulta_pptal_gastos 
                                WHERE  rubro_fuente !='' ");
                            for ($i=0; $i < count($row); $i++) { 
                                $id_rf        = $row[$i][0];
                                $compromisos  = $row[$i][1];
                                $obligaciones = $row[$i][2];
                                $pagos        = $row[$i][3];

                                #Concepto 
                                $rowc = $con->Listar("SELECT DISTINCT h.id_origen, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 84) as concepto, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 85) as vigencia , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 86) as seccion_presup, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 88) as programat_mga2 , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 89) as cpc, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 90) as fuentes, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 91) as bpin  , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 92) as situacion_fondo , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 93) as PoliticaPublica , 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 94) as Terceros, 
                                    (SELECT h2.id_destino from gn_homologaciones h2 WHERE h2.id_origen = h.id_origen AND origen = 87) as sector    
                                FROM gn_homologaciones h where h.id_origen = $id_rf");

                                $sql_cons ="INSERT INTO `tmp_cuipo` 
                                    ( `id_rf`, `cuipo_gastos`, `vigencia_gasto`,`seccion_p`,
                                    `programa_mga2`,`cpc`,`fuentes`,
                                    `bpin`,`situacion_fondo`,`politicas`,`tercero`, 
                                    `registros`,`obligaciones`,`pagos`, `sector` ) 
                                VALUES (:id_rf, :cuipo_gastos , :vigencia_gasto, :seccion_p,
                                    :programa_mga2,:cpc, :fuentes,  
                                    :bpin, :situacion_fondo, :politicas, :tercero, 
                                    :registros, :obligaciones, :pagos, :sector)";
                                $sql_dato = array(
                                    array(":id_rf",$id_rf),
                                    array(":cuipo_gastos",$rowc[0][1]),
                                    array(":vigencia_gasto",$rowc[0][2]),
                                    array(":seccion_p",$rowc[0][3]),
                                    array(":programa_mga2",$rowc[0][4]),
                                    array(":cpc",$rowc[0][5]),
                                    array(":fuentes",$rowc[0][6]),
                                    array(":bpin",$rowc[0][7]),
                                    array(":situacion_fondo",$rowc[0][8]),
                                    array(":politicas",$rowc[0][9]),
                                    array(":tercero",$rowc[0][10]),
                                    array(":registros",$compromisos),
                                    array(":obligaciones",$obligaciones),
                                    array(":pagos",$pagos),
                                    array(":sector",$rowc[0][11]),
                                ); 
                                $resp = $con->InAcEl($sql_cons,$sql_dato);
                            }    
                            echo 'CASE38';        
                        break;
                    }
                break;
            }
        break;
	}

 ?>
