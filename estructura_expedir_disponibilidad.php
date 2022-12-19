<?php 
#########################MODIFICACIONES####################################
#23/03/2017 | ERICA G. | CALCULO DE FECHA DE VENCIMIENTO, ARREGLO, NO ESTABA CALCULANDO CASE 4
#01/03/2017 | ERICA G. | AGREGAR VARIABLE DE SESION PARA VALIDAR SI ESTA REGISTRADO EL COMPROBANTE MODIFICAR DETALLES /CASE 13
#############################################################################
	require_once('Conexion/conexion.php');
	session_start();

	$estruc = $_REQUEST['estruc'];
	
	switch ($estruc) 
	{
		case 1:
			$_SESSION['id_comp_pptal_ED'] = "";
			$_SESSION['nuevo_ED'] = "";
			break;
			
		case 2:
			$id_comp = $_REQUEST['id_comp'];
			$_SESSION['id_comp_pptal_ED'] = $id_comp;
			$_SESSION['nuevo_ED'] = "";
			break;

		case 3:
			$id_tip_comp = $_REQUEST['id_tip_comp'];

			$parametroAnno = $_SESSION['anno'];
			$sqlAnno = 'SELECT anno 
				FROM gf_parametrizacion_anno 
				WHERE id_unico = '.$parametroAnno;
			$paramAnno = $mysqli->query($sqlAnno);
			$rowPA = mysqli_fetch_row($paramAnno);
			$numero = $rowPA[0];

			$queryNumComp = 'SELECT MAX(numero) 
				FROM gf_comprobante_pptal 
				WHERE tipocomprobante = '.$id_tip_comp .'
				AND numero LIKE \''.$numero.'%\'';
			$numComp = $mysqli->query($queryNumComp);
			$row = mysqli_fetch_row($numComp);
			if($row[0] == 0)
			{
				$numero .= '000001';
			}
			else
			{
				$numero = $row[0] + 1;
			}
			
			echo $numero;
			break;

		case 4:
			$idComPptal = $_POST['idComPptal'];
			$tipComPal = $_POST['tipComPal'];
			$fecha = $_POST['fecha']; //Seleccionada.
			$nuevaFecha = '';

			$parametroAnno = $_SESSION['anno'];
			$sqlAnno = 'SELECT anno 
				FROM gf_parametrizacion_anno 
				WHERE id_unico = '.$parametroAnno;
			$paramAnno = $mysqli->query($sqlAnno);
			$rowPA = mysqli_fetch_row($paramAnno);
			$numero = $rowPA[0];

			$queryFechComp = 'SELECT fecha 
				FROM gf_comprobante_pptal 
				WHERE id_unico = (
					SELECT MAX(id_unico) 
				    FROM gf_comprobante_pptal 
				    WHERE tipocomprobante = '.$tipComPal.'
				    AND numero LIKE \''.$numero.'%\'
				    )';
			$fechComp = $mysqli->query($queryFechComp);
			$row = mysqli_fetch_row($fechComp);
			$fechaPrev = $row[0];

			$fecha_div = explode("/", $fecha);
  			$dia = $fecha_div[0];
  			$mes = $fecha_div[1];
  			$anio = $fecha_div[2];
  
  			$fecha = $anio."-".$mes."-".$dia;


  			if($fechaPrev != 0)
			{

  			$fecha_prev = new DateTime($fechaPrev);
  			$fecha_ = new DateTime($fecha);
  			}
  			else
  			{
  				$fechaPrev = $fecha;
  				$fecha_prev = new DateTime($fechaPrev);
  				$fecha_ = new DateTime($fecha);
  			}


  			if($fecha_prev <= $fecha_)
  			{

	  			$queryFechComp = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = '$idComPptal'";
				$fechComp = $mysqli->query($queryFechComp);
				$row = mysqli_fetch_row($fechComp);
				$fechaPrev = $row[0];

				if($fechaPrev != 0)
				{
					$fecha_prev = new DateTime($fechaPrev);
				}
  				else
  				{
  					$fechaPrev = $fecha;
  					$fecha_prev = new DateTime($fechaPrev);
  					$fecha_ = new DateTime($fecha);
  				}

				if($fecha_prev <= $fecha_)
  				{

	  				$querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
					$sumDias = $mysqli->query($querySum);
                                        if(mysqli_num_rows($sumDias)>0) {
					$rowS = mysqli_fetch_row($sumDias);
					$sumarDias = $rowS[0];
                                        } else {
                                            $sumarDias = 30;
                                        }

					$fecha_->modify('+'.$sumarDias.' day');
					$nuevaFecha = (string)$fecha_->format('Y-m-d');


					$fecha_div = explode("-", $nuevaFecha);
	    			$anio = $fecha_div[0];
	    			$mes = $fecha_div[1];
	    			$dia = $fecha_div[2];
	  
	    			$nuevaFecha = $dia."/".$mes."/".$anio;
    			}
  				else
  				{
  					$nuevaFecha = 1;
  				}

  			}
  			else
  			{
  				$nuevaFecha = 1;
  			}
  			echo $nuevaFecha;
  			break;

  		case 5: 
			$_SESSION['id_comp_pptal_ER'] = "";
			$_SESSION['nuevo_ER'] = "";
			$_SESSION['id_comp_pptal_ER_Detalle'] = "";
			$_SESSION['agregar_ER'] = '';
			break;
			
		case 6: 
			$id_comp = $_REQUEST['id_comp'];
			$_SESSION['id_comp_pptal_ER'] = $id_comp;
			$_SESSION['nuevo_ER'] = "";
			$_SESSION['id_comp_pptal_ER_Detalle'] = "";
			$_SESSION['agregar_ER'] = '';
			break;

		case 7: 
			$_SESSION['id_comp_pptal_EO'] = "";
			$_SESSION['nuevo_EO'] = "";
			break;
			
		case 8: 
			$id_comp = $_REQUEST['id_comp'];
			$_SESSION['id_comp_pptal_EO'] = $id_comp;
			$_SESSION['nuevo_EO'] = "";
			break;

		case 9: 
			$_SESSION['id_comp_pptal_RP'] = "";
			$_SESSION['nuevo_RP'] = "";
			break;
			
		case 10: 
			$id_comp = $_REQUEST['id_comp'];
			$_SESSION['id_comp_pptal_RP'] = $id_comp;
			$_SESSION['nuevo_RP'] = "";
			break;

		case 11: 
			$_SESSION['id_comp_pptal_OP'] = "";
			$_SESSION['nuevo_OP'] = "";
			break;
			
		case 12: 
			$id_comp = $_REQUEST['id_comp'];
			$_SESSION['id_comp_pptal_OP'] = $id_comp;
			$_SESSION['nuevo_OP'] = "";
			break;

		case 13:
			$_SESSION['id_comp_pptal_MD'] = "";
			$_SESSION['nuevo_MD'] = "";
                        $_SESSION['mod'] = "";
			break;
			
		case 14:
			$id_comp = $_REQUEST['id_comp'];
			$_SESSION['id_comp_pptal_MD'] = $id_comp;
			$_SESSION['nuevo_MD'] = "";
			break;

		case 15:
			$_SESSION['id_comp_pptal_MR'] = "";
			$_SESSION['nuevo_MR'] = "";
			break;
			
		case 16:
			$id_comp = $_REQUEST['id_comp'];
			$_SESSION['id_comp_pptal_MR'] = $id_comp;
			$_SESSION['nuevo_MR'] = "";
			break;

		case 17:
			$_SESSION['id_comprobante_pptal'] = "";
			break;

		case 18:
			$id_comp = $_REQUEST['id_comp'];
			$_SESSION['id_comprobante_pptal'] = $id_comp;
			break;

		case 19:
			$_SESSION['id_comp_pptal_CP'] = "";
			break;

		case 20:
			$id_comp = $_REQUEST['id_comp'];
			$_SESSION['id_comp_pptal_CP'] = $id_comp;
			break;

		case 21:
			$fechaAct = $_POST['fecha'];
			$clase = $_POST['clase'];

			$queryMaxNum = "SELECT MAX(compPtal.id_unico) 
            	FROM gf_comprobante_pptal compPtal
                left join gf_tipo_comprobante_pptal tipComPtal on tipComPtal.id_unico = compPtal.tipocomprobante 
                left join gf_clase_pptal claPtal on claPtal.id_unico = tipComPtal.clasepptal
                where claPtal.id_unico = $clase"; 
            $maxNum = $mysqli->query($queryMaxNum);
			$rowMN = mysqli_fetch_row($maxNum);
			$idComPtal = $rowMN[0];

			if($idComPtal != 0)
			{

			$queryFecPrev = "SELECT fecha FROM gf_comprobante_pptal WHERE id_unico = $idComPtal";
			$fecPrev = $mysqli->query($queryFecPrev);
			$rowFP = mysqli_fetch_row($fecPrev);
			$fechaPrev = $rowFP[0];

			$fecha_div = explode("/", $fechaAct);
  			$dia = $fecha_div[0];
  			$mes = $fecha_div[1];
  			$anio = $fecha_div[2];
  
  			$fechaAct = $anio."-".$mes."-".$dia;

			$fecha_ = new DateTime($fechaAct);
  			$fecha_prev = new DateTime($fechaPrev);

  			if($fecha_prev <= $fecha_)
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
  				echo 1;
  			}
  			break;
  		case 22: 
			$tipComPal = $_POST['tipComPal'];
			$fecha = $_POST['fecha']; //Seleccionada.
			$nuevaFecha = '';

                        $queryFechComp = "SELECT fecha 
				FROM gf_comprobante_pptal 
				WHERE id_unico = (
					SELECT MAX(id_unico) 
				    FROM gf_comprobante_pptal 
				    WHERE tipocomprobante = $tipComPal)";
			$fechComp = $mysqli->query($queryFechComp);
			$row = mysqli_fetch_row($fechComp);
			$fechaPrev = $row[0];

			

			$fecha_div = explode("/", $fecha);
  			$dia = $fecha_div[0];
  			$mes = $fecha_div[1];
  			$anio = $fecha_div[2];
  
  			$fecha = $anio."-".$mes."-".$dia;

  			if($fechaPrev != 0)
			{

  			$fecha_prev = new DateTime($fechaPrev);
  			$fecha_ = new DateTime($fecha);

  			}
  			else
  			{
  				$fechaPrev = $fecha;
  				$fecha_prev = new DateTime($fechaPrev);
  				$fecha_ = new DateTime($fecha);
  			}

  			if($fecha_prev <= $fecha_)
  			{

	  			$querySum = "SELECT valor FROM gs_parametros_basicos WHERE nombre = 'Días Vencimiento Disponibilidad'";
				$sumDias = $mysqli->query($querySum);
                                if(mysqli_num_rows($sumDias)>0) {
				$rowS = mysqli_fetch_row($sumDias);
				$sumarDias = $rowS[0];
                                } else {
                                    $sumarDias=30;
                                }
                                
				$fecha_->modify('+'.$sumarDias.' day');
				$nuevaFecha = (string)$fecha_->format('Y-m-d');


				$fecha_div = explode("-", $nuevaFecha);
	    		$anio = $fecha_div[0];
	    		$mes = $fecha_div[1];
	    		$dia = $fecha_div[2];
	  
	    		$nuevaFecha = $dia."/".$mes."/".$anio;

  			}
  			else
  			{
  				$nuevaFecha = 1;
  			}
  			echo $nuevaFecha;
  			break;
  		case 23:
  			if($_SESSION['agregar_ER'] = 3)
  			{
  				$id_comp = $_POST['id_comp']; //Nuevo comprobante para el detalle.
  				$id_comp_pptal_ER = $_POST['id_comp_pptal_ER']; //Antiguo comprobante.

  				$_SESSION['id_comp_pptal_ER'] = $id_comp_pptal_ER;
  				$_SESSION['id_comp_pptal_ER_Detalle'] = $id_comp;
  				$_SESSION['nuevo_ER'] = "";
  			}
  			else
  			{
  				$id_comp = $_REQUEST['id_comp'];
				$_SESSION['id_comp_pptal_ER'] = $id_comp;
				$_SESSION['nuevo_ER'] = "";
				$_SESSION['id_comp_pptal_ER_Detalle'] = "";
  			}
  			break;
  		case 24: //Buscar 
  			$num = 0;
  			$id_com = $_REQUEST['numero'];
  			$_SESSION['id_comp_pptal_ER'] = $id_com;
  			$_SESSION['id_comp_pptal_ER_Detalle'] = "";

  			$sqlBuscarDet = 'SELECT id_unico 
  				FROM gf_detalle_comprobante_pptal 
  				WHERE comprobantepptal = '.$id_com;
  			$busDet = $mysqli->query($sqlBuscarDet);
			$num = $busDet->num_rows; //Número de filas que retorna la consulta.
			if($num == 0)
			{
				$_SESSION['agregar_ER'] = 3;
				$_SESSION['nuevo_ER'] = 1;
			}
			else
			{
				$_SESSION['agregar_ER'] = '';
				$_SESSION['nuevo_ER'] = 1;

			}
  			break;

	}

	/*

$id_comp = $_REQUEST['id_comp'];
			$_SESSION['id_comp_pptal_ER'] = $id_comp;
			$_SESSION['nuevo_ER'] = "";
			$_SESSION['id_comp_pptal_ER_Detalle'] = "";

	*/

 ?>

