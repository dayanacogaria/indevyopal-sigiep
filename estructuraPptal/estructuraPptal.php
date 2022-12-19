<?php
###########MODIFICACIONES####################
#13/03/2017 | ERICA G. | AÑADIR INFORMES PDF EXCEL
# Desarrollado por Ferney Pérez Cano // 18/02/2017. Se modificó case 2 para añadir el rubro fuente a la tabla gf_rubro_fuente
# en caso de que no exista. Se añadió el caso 5 donde se verifica si el rubro la fuente seleccioandos ya existen en la tabla gf_rubro_fuente.
		
require_once '../Conexion/conexion.php';
session_start();

$estruc = $_POST['estruc'];
	
switch ($estruc) 
{
	case 1: // Insertar resgistros de traslado en la tabla gf_comprobante_pptal.
		$res = 0;
		$numero  = '"'.$mysqli->real_escape_string(''.$_POST['numero'].'').'"';
  		$fecha  = '"'.$mysqli->real_escape_string(''.$_POST['fecha'].'').'"';
  		$fechaVen  = '"'.$mysqli->real_escape_string(''.$_POST['fechaVen'].'').'"';
  		$descripcion = '"'.$mysqli->real_escape_string(''.$_POST['descripcion'].'').'"';
  		$tipocomprobante = '"'.$mysqli->real_escape_string(''.$_POST['tipocomprobante'].'').'"';

  		//Converción de fecha del formato dd/mm/aaaa al formato aaaa-mm-dd.
		$fecha = trim($fecha, '"');
		$fecha_div = explode("/", $fecha);
		$dia = $fecha_div[0];
		$mes = $fecha_div[1];
		$anio = $fecha_div[2];
			  
		$fecha = $anio.'-'.$mes.'-'.$dia;
		$fecha = '"'.$mysqli->real_escape_string(''.$fecha.'').'"';

		//Converción de fecha del formato dd/mm/aaaa al formato aaaa-mm-dd.
		$fechaVen = trim($fechaVen, '"');
		$fecha_div = explode("/", $fechaVen);
		$dia = $fecha_div[0];
		$mes = $fecha_div[1];
		$anio = $fecha_div[2];
			  
		$fechaVen = $anio.'-'.$mes.'-'.$dia;
		$fechaVen = '"'.$mysqli->real_escape_string(''.$fechaVen.'').'"';

		//Consulta del ID de la tabla gf_tercero .
		$queryVario = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = 9999999999";
		$vario = $mysqli->query($queryVario);
		$row = mysqli_fetch_row($vario);
		$tercero = '"'.$mysqli->real_escape_string(''.$row[0].'').'"';
		$responsable = '"'.$mysqli->real_escape_string(''.$row[0].'').'"';

		$parametroAnno = $_SESSION['anno'];
		$parametroAnno = '"'.$mysqli->real_escape_string(''.$parametroAnno.'').'"';

		if($descripcion == '""')
		{
			$insertSQL = 'INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, parametrizacionanno,
                                                                tipocomprobante, tercero,  responsable) 
			  VALUES('.$numero.', '.$fecha.', '.$fechaVen.', '.$parametroAnno.', '.$tipocomprobante.', '.$tercero.',  '.$responsable.')';
		}
		else
		{
			$insertSQL = 'INSERT INTO gf_comprobante_pptal (numero, fecha, fechavencimiento, descripcion, parametrizacionanno, 
                                                                tipocomprobante, tercero,  responsable) 
			  VALUES('.$numero.', '.$fecha.', '.$fechaVen.', '.$descripcion.', '.$parametroAnno.', '.$tipocomprobante.', '.$tercero.', '.$responsable.')';
		}
                
		$resultado = $mysqli->query($insertSQL);

		if($resultado == true)
		{
			$queryUltComp = 'SELECT MAX(id_unico) FROM gf_comprobante_pptal';
    		$ultimComp = $mysqli->query($queryUltComp);
    		$rowUC = mysqli_fetch_row($ultimComp);
			$res = $rowUC[0];
			$res = ($res);
		}
		
		echo $res;
		break;
	case 2:  // Insertar registros de traslado en la tabla gf_detalle_comprobante_pptal.
		$res = 0;
		$valor = '';
		
  		 $credito  = $_POST['credito'];
  		 $contraCredito  = $_POST['contraCredito'];
  		 $idComp = $_POST['idComp'];

  		$rubro  = $_POST['rubro'];
		$fuente  = $_POST['fuente'];


		/////////////////////////////

		$resultado = true;

		$queryRubFue = "SELECT id_unico 
		    FROM gf_rubro_fuente 
		    WHERE rubro = $rubro    
		    AND fuente = $fuente";

		$rubroFuente = $mysqli->query($queryRubFue);
                if(mysqli_num_rows($rubroFuente)>0){ 
                    
		$row = mysqli_fetch_row($rubroFuente);
		$id_rub_fue = $row[0];
               $rubroFuente =  $id_rub_fue;
                } else {
		   
		    $insertSQL = 'INSERT INTO gf_rubro_fuente (rubro, fuente) 
		      VALUES('.$rubro.', '.$fuente.')';
		    $resultado = $mysqli->query($insertSQL);

		    if($resultado == true)
		    {
		      $queryMaxID = "SELECT MAX(id_unico) FROM gf_rubro_fuente";
		      $maxID = $mysqli->query($queryMaxID);
		      $row2 = mysqli_fetch_row($maxID);
		      $rubroFuente = $row2[0];
		    }

		}

		///////////////////////////////

  		$queryCompPtal = 'SELECT descripcion, tercero, id_unico   
  			FROM gf_comprobante_pptal 
  			WHERE (id_unico) = '.$idComp;
        $compPtal = $mysqli->query($queryCompPtal);
        $rowCP = mysqli_fetch_row($compPtal);
        $descripcion = '"'.$mysqli->real_escape_string(''.$rowCP[0].'').'"';
        $tercero = '"'.$mysqli->real_escape_string(''.$rowCP[1].'').'"';
        $idComp = '"'.$mysqli->real_escape_string(''.$rowCP[2].'').'"';

        if($credito == 0)
        {
        	$valor = $contraCredito;
        }
        elseif($contraCredito == 0)
        {
        	$valor = $credito;
        }

        
        if($descripcion == '""')
        {
        	$descripcion = 'NULL';
        }

        $proyecto = '"'.$mysqli->real_escape_string('2147483647').'"';
       
       $insertSQL = 'INSERT INTO gf_detalle_comprobante_pptal (descripcion, valor, comprobantepptal, tercero, proyecto, rubrofuente) 
              VALUES ('.$descripcion.', '.$valor.', '.$idComp.', '.$tercero.', '.$proyecto.', '.$rubroFuente.')';
      	$resultado = $mysqli->query($insertSQL);
      	if($resultado == true)
      	{
      		$res = 1;
      	}

  		echo $res;
		break;
	case 3: // Modificar el valor del traslado en la tabla gf_detalle_comprobante_pptal.
		$res = 0;
		$id_val = $_POST['id_val'];
		$valor = $_POST['valor'];
		$credito = $_POST['credito'];

		if($valor == 0)
		{
			$valorPptal = '"'.$mysqli->real_escape_string(''.$credito.'').'"';
		}
		elseif ($credito == 0) 
		{
			$valorPptal = '"'.$mysqli->real_escape_string(''.$valor.'').'"';	
		}

		$updateSQL = 'UPDATE gf_detalle_comprobante_pptal SET valor = '.$valorPptal.' WHERE id_unico = '.$id_val;
  		$resultado = $mysqli->query($updateSQL);

  		if($resultado == true)
  		{
  			$res = 1;
  		}

  		echo $res;
		break;
	case 4: // Buscar un traslado por el número digitado por el usuario en buscar registro.
		$fragSql = '';
		$signo = '';
		$num = 0;

		$natural = array(2, 3, 5, 7, 10); 
	  	$juridica = array(1, 4, 6, 8, 9);

		$numero =  $_POST['numero'];  
		$clase =  $_POST['clase'];  

		if(!empty($_POST['tipoOp']))
		{
			$tipoOp = $_POST['tipoOp']; 
			$signo = $_POST['signo'];
			$fragSql = " AND tipoCom.tipooperacion $signo $tipoOp ";
		}
		
		//SELECT  com.id_unico 0, com.numero numero 1, tipoCom.codigo 2, com.fecha 3, com.tercero 4
		$queryComp ="SELECT  com.id_unico, com.numero numero, tipoCom.codigo, com.fecha, com.tercero, com.descripcion      
	  		FROM gf_comprobante_pptal com
	 		left join gf_tipo_comprobante_pptal tipoCom on tipoCom.id_unico = com.tipocomprobante
	  		WHERE tipoCom.clasepptal = $clase 
	  		$fragSql 
			and com.numero like '$numero%'";

		$resultado = $mysqli->query($queryComp);

	    while ($row = mysqli_fetch_row($resultado))
	    {
	    	$fecha = $row[3];
			$fecha_div = explode("-", $fecha);
			$dia = $fecha_div[2];
			$mes = $fecha_div[1];
			$anio = $fecha_div[0];
			$fecha = $dia.'/'.$mes.'/'.$anio;

			$queryTerc = 'SELECT ter.id_unico, ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos, ter.razonsocial, ter.numeroidentificacion, perTer.perfil     
				    FROM gf_tercero ter 
				    LEFT JOIN gf_perfil_tercero perTer ON perTer.tercero = ter.id_unico 
				    WHERE ter.id_unico = \''.$row[4].'\'';
	        $terc = $mysqli->query($queryTerc);
	        $rowTer = mysqli_fetch_row($terc);

	        if(in_array($rowTer[7], $natural))
	        {
	            $tercero = ucwords(mb_strtolower($rowTer[1])).' '.ucwords(mb_strtolower($rowTer[2])).' '.ucwords(mb_strtolower($rowTer[3])).' '.ucwords(mb_strtolower($rowTer[4]));
	        }
	        elseif (in_array($rowTer[7], $juridica))
	        {
	            $tercero = ucwords(mb_strtolower($rowTer[5])); 
	        }

	        $sqlValor = 'SELECT SUM(valor) 
	        	FROM gf_detalle_comprobante_pptal 
	        	WHERE comprobantepptal = '.$row[0];
	        $valor = $mysqli->query($sqlValor);
	        $rowV = mysqli_fetch_row($valor);


	        echo '<div class="itemLista" style="padding: 3px;" onmouseover="this.style.backgroundColor=\'#5499c7\'" onmouseout="this.style.backgroundColor=\'#fff\'" align="left"> <span style="cursor: default" data="'.$row[1].' '.utf8_encode($row[2]).'" id="'.($row[0]).'" >'.$row[1].' '.utf8_encode($row[2]).' '.$fecha.' '.$tercero.' $'.number_format($rowV[0], 2, '.', ',').'</span></div>';
	    }
		break;
	case 5:
		$fuente  = '"'.$mysqli->real_escape_string(''.$_POST['fuente'].'').'"';
  		$rubro  = '"'.$mysqli->real_escape_string(''.$_POST['rubro'].'').'"';

	  	$queryRubFue = 'SELECT id_unico 
	    	FROM gf_rubro_fuente 
	    	WHERE rubro = '.$rubro.' 
	    	AND fuente = '.$fuente;

	  	$rubroFuente = $mysqli->query($queryRubFue);
	  	$row = mysqli_fetch_row($rubroFuente);
	  	$id_rub_fue = $row[0];

	  	echo (int)$id_rub_fue;
		break;
		
}