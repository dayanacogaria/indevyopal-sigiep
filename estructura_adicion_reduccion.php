<?php
	require_once('Conexion/conexion.php');
	session_start();

	$estruc = $_POST['estruc'];
	
	switch ($estruc) 
	{
		case 1:
			//$_SESSION['id_comp_pptal_ED'] = "";
			//$_SESSION['nuevo_ED'] = "";
			$numero  = '"'.$mysqli->real_escape_string(''.$_POST['numero'].'').'"';
  			$fecha  = '"'.$mysqli->real_escape_string(''.$_POST['fecha'].'').'"';
  			$fechaVen  = '"'.$mysqli->real_escape_string(''.$_POST['fechaVen'].'').'"';
  			$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
  			$estado = '"'.$mysqli->real_escape_string(''.$_POST['estado'].'').'"';
  			$tipocomprobante = '"'.$mysqli->real_escape_string(''.$_POST['tipocomprobante'].'').'"';
  			$sesion = $_POST['sesion'];

  			//Converción de fecha del formato dd/mm/aaaa al formato aaaa-mm-dd.
			$fecha = trim($fecha, '"');
			$fecha_div = explode("/", $fecha);
			$dia = $fecha_div[0];
			$mes = $fecha_div[1];
			$anio = $fecha_div[2];
			  
			$fecha = $anio.'-'.$mes.'-'.$dia;
                        ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
                        $ci="SELECT
                        cp.id_unico
                        FROM
                        gs_cierre_periodo cp
                        LEFT JOIN
                        gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
                        LEFT JOIN
                        gf_mes m ON cp.mes = m.id_unico
                        WHERE
                        pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2";
                        $ci =$mysqli->query($ci);
                        if(mysqli_num_rows($ci)>0){ 
                            echo 2;
                        } else {
			//Converción de fecha del formato dd/mm/aaaa al formato aaaa-mm-dd.
			$fechaVen = trim($fechaVen, '"');
			$fecha_div = explode("/", $fechaVen);
			$dia = $fecha_div[0];
			$mes = $fecha_div[1];
			$anio = $fecha_div[2];
			  
			$fechaVen = $anio.'-'.$mes.'-'.$dia;

			//Consulta del ID de la tabla gf_tercero .
			$queryVario = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999";
			$vario = $mysqli->query($queryVario);
			$row = mysqli_fetch_row($vario);
			$tercero = $row[0];
			$responsable = $row[0];

			$parametroAnno = $_SESSION['anno'];

			 if($descripcion == '""')
			  {
			    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, parametrizacionanno, tipocomprobante, tercero, estado, responsable) 
			  VALUES($numero, '$fecha', '$fechaVen', $parametroAnno, $tipocomprobante, $tercero, $estado, $responsable)";
			  }
			  else
			  {
			    $insertSQL = "INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, descripcion, parametrizacionanno, tipocomprobante, tercero, estado, responsable) 
			  VALUES($numero, '$fecha', '$fechaVen', $descripcion, $parametroAnno, $tipocomprobante, $tercero, $estado, $responsable)";
			  }
			  $resultado = $mysqli->query($insertSQL);


			  if($resultado == true)
			  {
			  	$queryUltComp = "SELECT MAX(id_unico) FROM gf_comprobante_pptal";
    			$ultimComp = $mysqli->query($queryUltComp);
    			$rowUC = mysqli_fetch_row($ultimComp);
    			$_SESSION[$sesion] = $rowUC[0]; 
			  	echo 1;
			  }
			  else
			  {
			  	echo 0;
			  }
                        }
			break;
		case 2: 
			$sesion = $_POST['sesion'];
			unset($_SESSION[$sesion]); 
			break;
		case 3:
			$fuente = $_POST['id_fuente']; 
			$rubro = $_POST['id_rubro'];

			$queryRubFue = 'SELECT id_unico 
    			FROM gf_rubro_fuente 
    			WHERE rubro = '.$rubro.'   
    			AND fuente = '.$fuente;

  			$rubroFuente = $mysqli->query($queryRubFue);
  			$row = mysqli_fetch_row($rubroFuente);
  			$id_rubro_fuente = $row[0];
  			echo $id_rubro_fuente;
			break;
    #######MODIFICAR COMPROBANTE ADICION APROPIACION###########                
    case 4:
        $id=  $_POST['comprobante'];
        
        ##FECHA
        $fecha  = $_POST['fecha'];
        $fecha = trim($fecha, '"');
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $fecha = $anio.'-'.$mes.'-'.$dia;
        
        ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
        $ci="SELECT
        cp.id_unico
        FROM
        gs_cierre_periodo cp
        LEFT JOIN
        gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
        LEFT JOIN
        gf_mes m ON cp.mes = m.id_unico
        WHERE
        pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2 ";
        $ci =$mysqli->query($ci);
        if(mysqli_num_rows($ci)>0){ 
            echo 2;
        } else {
            $fechaVen  = $_POST['fechaVen'];
            $descripcion =$_POST['descripcion'];
            if(empty($descripcion)){
                $descripcion = NULL;
            }


            ##FECHA VENCIMIENTO
            $fechaVen = trim($fechaVen, '"');
            $fecha_div = explode("/", $fechaVen);
            $dia = $fecha_div[0];
            $mes = $fecha_div[1];
            $anio = $fecha_div[2];
            $fechaVen = $anio.'-'.$mes.'-'.$dia;
            $upd= "UPDATE gf_comprobante_pptal SET fecha='$fecha', fechavencimiento ='$fechaVen', "
                    . "descripcion = '$descripcion' WHERE id_unico = $id";
            $result=$mysqli->query($upd);
            //ACTUALIZAR DETALLES
            $udpd="UPDATE gf_detalle_comprobante_pptal SET descripcion = '$descripcion' WHERE comprobantepptal = $id";
            $udpd =$mysqli->query($udpd);
            if($result==true || $result==1){
                echo 1;
            } else {
                echo 0;
            }
        }
        break;                    
}

?>