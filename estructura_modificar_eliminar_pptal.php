<?php 
#18/05/2017 | ERICA G. | MODIFICACION EGRESO, AÑADIDO TERCERO
#17/05/2017 | ERICA G. | VALIDACION DE RETENCION POR EGRESO O CUENTA POR PAGAR, ELIMINAR CASE 7
#21/03/2017 |ERICA G. | CASE 2 MODIFICAR DIS, FECHA VEN, EGRESO
#13/03/2017 |ERICA G. | CASE 2, MODIFICAR DIS, CASE 4 ELIMINAR DETALLES.
#08/03/2017 |ERICA G. | MODIFICACION EGRESO
  #Modificado 10/02/2017 09:32 Ferney Pérez // Case 1: Modificacda consulta. Se eliminó el group by.
	#Modificado 07/02/2017 15:11 Ferney Pérez
	#Modificado 26/01/2017 12:52 Ferney Pérez
	require_once('Conexion/conexion.php');
	session_start();

	$estruc = $_POST['estruc'];
	
	switch ($estruc) 
	{
            ####BUSCA AFECTACIONES DEL COMPROBANTE#####
		case 1:
			$id_com = $_POST['id_com'];
			$num = 0;

			$sqlBuscarAfec = 'SELECT COUNT(cps.id_unico) 
					FROM gf_comprobante_pptal cp
					LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.comprobantepptal = cp.id_unico
					LEFT JOIN gf_detalle_comprobante_pptal dcps ON dcps.comprobanteafectado = dcp.id_unico
					LEFT JOIN gf_comprobante_pptal cps ON dcps.comprobantepptal = cps.id_unico
					WHERE cp.id_unico = '.$id_com;
    		$buscarAfectacion = $mysqli->query($sqlBuscarAfec);
			$rowBA = mysqli_fetch_row($buscarAfectacion);
                $num = (int)$rowBA[0];
                echo $num;
    		break;
        #####MODIFICAR DISPONIBILIDAD######
    	case 2:
    		$id_com = $_POST['id_com']; 
    		$descripcion = "'".$_POST['descripcion']."'";
                $fecha =$_POST['fecha'];
                $fecha = trim($fecha, '"');
                $fecha_div = explode("/", $fecha);
                $dia = $fecha_div[0];
                $mes = $fecha_div[1];
                $anio = $fecha_div[2];
                $fecha = $anio.'-'.$mes.'-'.$dia;
                
                $fechaVen =$_POST['fechaVen'];
                $fechaVen = trim($fechaVen, '"');
                $fecha_divV = explode("/", $fechaVen);
                $diaV = $fecha_divV[0];
                $mesV = $fecha_divV[1];
                $anioV = $fecha_divV[2];
                $fechaV = $anioV.'-'.$mesV.'-'.$diaV;
                $id_com = $_POST['id_com'];
			$num = 0;

			$sqlBuscarAfec = 'SELECT COUNT(cps.id_unico) 
					FROM gf_comprobante_pptal cp
					LEFT JOIN gf_detalle_comprobante_pptal dcp ON dcp.comprobantepptal = cp.id_unico
					LEFT JOIN gf_detalle_comprobante_pptal dcps ON dcps.comprobanteafectado = dcp.id_unico
					LEFT JOIN gf_comprobante_pptal cps ON dcps.comprobantepptal = cps.id_unico
					WHERE cp.id_unico = '.$id_com;
    		$buscarAfectacion = $mysqli->query($sqlBuscarAfec);
			$rowBA = mysqli_fetch_row($buscarAfectacion);
                $num = (int)$rowBA[0];
                if($num>0){
                    $updateComPtal = "UPDATE gf_comprobante_pptal 
                            SET descripcion = $descripcion, fecha ='$fecha' , fechavencimiento='$fechaV' 
                            WHERE id_unico = ".$id_com;
                    
                } else {
                    if(empty($_REQUEST['tercero'])){
                        $updateComPtal = "UPDATE gf_comprobante_pptal 
                            SET descripcion = $descripcion, fecha ='$fecha' , fechavencimiento='$fechaV' 
                            WHERE id_unico = ".$id_com;
                    } else {
                        $updateComPtal = "UPDATE gf_comprobante_pptal 
                            SET descripcion = $descripcion, fecha ='$fecha' , 
                            tercero =". $_REQUEST['tercero'].", fechavencimiento='$fechaV' 
                            WHERE id_unico = ".$id_com;
                    }
                    if(!empty($_REQUEST['responsable'])){
                        $updateComPtalR = "UPDATE gf_comprobante_pptal 
                            SET responsable = ".$_REQUEST['responsable']." 
                            WHERE id_unico = ".$id_com;
                        $resultadoUpComR = $mysqli->query($updateComPtalR);
                    }
                }
                //echo $updateComPtal;
    		$resultadoUpCom = $mysqli->query($updateComPtal);
			if($resultadoUpCom == TRUE)
    		{
    			$updateDetComPtal = "UPDATE gf_detalle_comprobante_pptal SET descripcion =$descripcion 
    				WHERE comprobantepptal = ".$id_com;
    			$resultadoUpDet = $mysqli->query($updateDetComPtal);
                        
                        echo 1;
    		}
    		else
    		{
    			echo 2;
    		}
    		break;
        ######MODIFICAR REGISTRO#####
    	case 3:
    		$id_com = $_POST['id_com']; 
    		$fecha = $_POST['fecha']; 
    		$fechaVen = $_POST['fechaVen']; 
    		$descripcion = $_POST['descripcion'];
                if(empty($_POST['ncontrato'])|| $_POST['ncontrato']==''){
                $ncontrato=NULL;
                } else { 
                $ncontrato=$_POST['ncontrato'];
                }
                if(empty($_POST['clase'])|| $_POST['clase']==''){
                $clase=NULL;
                } else { 
                $clase=$_POST['clase'];
                }
                
                #FECHA#
                $fecha_div1 = explode("/", $fecha);
                $anio1 = $fecha_div1[0];
                $mes1 = $fecha_div1[1];
                $dia1 = $fecha_div1[2];
                $fecha = $dia1."/".$mes1."/".$anio1;
                
                #FECHA VEN#
                $fecha_div = explode("/", $fechaVen);
                $anio = $fecha_div[0];
                $mes = $fecha_div[1];
                $dia = $fecha_div[2];
                $fechaVen = $dia."/".$mes."/".$anio;
                
    		if(!empty($_POST['tercero'])){
                    $tercero = $_POST['tercero'];
                     $updateComPtal = "UPDATE gf_comprobante_pptal 
                            SET descripcion = '$descripcion', tercero = $tercero, fecha = '$fecha', "
                            . "fechavencimiento = '$fechaVen', numerocontrato ='$ncontrato', clasecontrato='$clase' 
                            WHERE id_unico = ".$id_com;
                    $resultadoUpCom = $mysqli->query($updateComPtal);
                    if($resultadoUpCom == TRUE)
                    {
                        $updateDetComPtal = "UPDATE gf_detalle_comprobante_pptal SET descripcion ='$descripcion' , "
                                . "tercero = '$tercero' WHERE comprobantepptal = ".$id_com;
                        $resultadoUpDet = $mysqli->query($updateDetComPtal);
                    echo 1;
                    }
                    else
                    {
                       echo 2;
                    }
                } else {
                    $updateComPtal = "UPDATE gf_comprobante_pptal 
                            SET descripcion = '$descripcion'  
                            WHERE id_unico = ".$id_com;
                    $resultadoUpCom = $mysqli->query($updateComPtal);
                    if($resultadoUpCom == true)
                    {
                        $updateDetComPtal = "UPDATE gf_detalle_comprobante_pptal SET descripcion ='$descripcion' "
                                . "WHERE comprobantepptal = ".$id_com;
                        $resultadoUpDet = $mysqli->query($updateDetComPtal);
                        echo 1;
                    }
                    else
                    {
                       echo 2;
                    }
                }
    		break;
      case 4:
          #######ELIMINAR DETALLES PPTALES DISPONIBILIDAD Y REGISTRO#######
        $id_com = $_POST['id_com']; 
        $sesion = $_POST['sesion']; 
        $numDet = $_POST['numDet'];

        $sesion_array = explode("|", $sesion);

        if($numDet != 0)
        { 
            $compMov = "SELECT id_unico FROM gf_detalle_comprobante_pptal  WHERE comprobantepptal = ".$id_com;
            $compMov = $mysqli->query($compMov);
            if(mysqli_num_rows($compMov)>0){
                while ($row = mysqli_fetch_row($compMov)) {
                    ###ELIMINAR DETALLES MOV###
                    $idMov= mysqli_fetch_row($compMov);
                    $deleteMov = "DELETE
                    FROM gf_detalle_comprobante_mov 
                    WHERE comprobantepptal = ".$row[0];
                    $deleteMov = $mysqli->query($deleteMov);
                    ###########################
                }
            }
            ####ELIMINAR DETALLES######
            
          $deleteDetallPtal = 'DELETE
            FROM gf_detalle_comprobante_pptal 
            WHERE comprobantepptal = '.$id_com;
          $resultadoDelDet = $mysqli->query($deleteDetallPtal);
          ###########################
        } 
        
        if($resultadoDelDet == TRUE)
        {
          echo 1;
        }
        else
        {
          echo 0;
        }
        break;
        #########MODIFICAR EGRESO############
      case 5: 
        $id_com = $_POST['id_com']; 
        $fecha = $_POST['fecha'];
        $fecha_div = explode("/", $fecha);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];
        $tercero =$_POST['tercero'];
        $formaP = $_POST['formaP'];
        if(empty($_POST['descripcion'])){
        $descripcion ="NULL";
        } else {
            $descripcion ="'".$_POST['descripcion']."'";
        }
        $fecha = $anio."-".$mes."-".$dia;
        $cnt = $_POST['cnt'];
        $updateComPtal = "UPDATE gf_comprobante_pptal 
          SET fecha = '$fecha' , descripcion =$descripcion, tercero =$tercero "
                . "WHERE id_unico = ".$id_com;
        $resultadoUpCom = $mysqli->query($updateComPtal);
        if($resultadoUpCom == TRUE)
        {
          $updateComPtal = "UPDATE gf_detalle_comprobante_pptal 
          SET  descripcion =$descripcion, tercero =$tercero WHERE comprobantepptal = ".$id_com;
            $resultadoUpCom = $mysqli->query($updateComPtal);
          
          if(!empty($_POST['cnt'])){
              $updateComc = "UPDATE gf_comprobante_cnt
                SET fecha = '$fecha', descripcion =$descripcion,tercero =$tercero,formapago = $formaP WHERE id_unico = ".$cnt;
            $resultadoUpCnt = $mysqli->query($updateComc);
            $updateDetCnt = "UPDATE gf_detalle_comprobante SET fecha = '$fecha' 
            WHERE comprobante = ".$cnt;
            $resultadoUpDetcnt = $mysqli->query($updateDetCnt);
          }
          echo 1;
        }
        else
        {
          echo 2;
        }
        break;
        #########ELIMINAR DETALLES EGRESO########## 
        case 6: 
                
                if(!empty($_POST['idcnt'])){
                    
                    $idcnt = $_POST['idcnt'];
                    $buscarcnt = "SELECT id_unico FROM gf_detalle_comprobante WHERE comprobante =$idcnt";
                    $buscarcnt = $mysqli->query($buscarcnt);
                    if(mysqli_num_rows($buscarcnt)){
                        while ($row1C = mysqli_fetch_row($buscarcnt)) {
                            $delDeMovC ="DELETE FROM gf_detalle_comprobante_mov WHERE comprobantecnt = '$row1C[0]'";
                            $delDeMovC =$mysqli->query($delDeMovC);
                            $delDetcnt ="DELETE FROM gf_detalle_comprobante WHERE id_unico = '$row1C[0]'";
                            $delDetcnt =$mysqli->query($delDetcnt);
                            
                        }
                       
                    }
                    ##ELIMINAR RETENCIONES
                    $delret ="DELETE FROM gf_retencion WHERE comprobante = '$idcnt'";
                    $delret =$mysqli->query($delret); 
                    ##ELIMINAR COMPROBANTE
                    $delcnt1 ="DELETE FROM gf_comprobante_cnt WHERE id_unico = '$idcnt'";
                    $delcnt =$mysqli->query($delcnt1); 
                    
                }
                if(!empty($_POST['idpptal'])){
                    $idcnt =$_POST['idpptal'];
                    $buscarcnt = "SELECT id_unico FROM gf_detalle_comprobante_pptal WHERE comprobantepptal =$idcnt";
                    $buscarcnt = $mysqli->query($buscarcnt);
                    if(mysqli_num_rows($buscarcnt)){
                        while ($row1C = mysqli_fetch_row($buscarcnt)) {
                            $delDetcnt ="DELETE FROM gf_detalle_comprobante_pptal WHERE id_unico = '$row1C[0]'";
                            $delDetcnt =$mysqli->query($delDetcnt);
                            
                        }
                       
                    }
                }
                
                echo $delcnt;
        break;
        case 7: 
            $egp =$_POST['idpptalE'];
            $te ="SELECT id_unico, numero, tipocomprobante, fecha, descripcion,numerocontrato, "
                  . "parametrizacionanno,clasecontrato, tercero,estado,responsable "
                  . "FROM gf_comprobante_pptal "
                    . "WHERE id_unico =$egp";
            $te=$mysqli->query($te);
            $te = mysqli_fetch_row($te);
            $id=$te[0];
            $numero=$te[1];
            $tipo = $te[2];
            $fecha =$te[3];
            if(empty($te[4])){
                $descripcion='NULL';
            } else {
                $descripcion ='"'.$te[4].'"';
            }
            if(empty($te[5])){
                $numerocontrato='NULL';
            } else {
                $numerocontrato ='"'.$te[5].'"';
            }
            
            if(empty($te[7])){
                $clasecontrato='NULL';
            } else {
                $clasecontrato ='"'.$te[7].'"';
            }
            
            $parametrizacion =$te[6];
            $tercero =$te[8];
            $estado =$te[9];
            $compania =$_SESSION['compania'];
            
            ###INSERTAR COMPROBANTE CNT#####
            ###BUSCAR TIPÓ CNT ###
            $tipoc = "SELECT id_unico FROM gf_tipo_comprobante WHERE comprobante_pptal = $tipo";
            $tipoc =$mysqli->query($tipoc);
            $tipoc = mysqli_fetch_row($tipoc);
            $tipoc =$tipoc[0];
            
            $insertc ="INSERT INTO gf_comprobante_cnt (numero, fecha, "
                    . "descripcion, valorbase, valorbaseiva, valorneto, numerocontrato,"
                    . "tipocomprobante, compania, parametrizacionanno, tercero, estado, "
                    . "clasecontrato, formapago) "
                    . "VALUES ('$numero', '$fecha', "
                    . "$descripcion, 0,0,0, $numerocontrato, "
                    . "$tipoc, $compania, $parametrizacion, $tercero, 1, "
                    . "$clasecontrato, NULL)";
            $insertc =$mysqli->query($insertc);
            if($insertc==true){
                $sqlUltComC      = "SELECT MAX(id_unico) FROM gf_comprobante_cnt "
                        . "WHERE tipocomprobante = $tipoc AND numero =$numero ";
                $ultComC         = $mysqli->query($sqlUltComC);
                $rowUC           = mysqli_fetch_row($ultComC);
                $ultimoComproCnt = $rowUC[0];
                ####CONSULTAR LA CUENTA POR PAGAR#####
                $cxp ="SELECT DISTINCT
                        dccxp.comprobante
                      FROM
                        gf_detalle_comprobante_pptal dcp
                      LEFT JOIN
                        gf_detalle_comprobante_pptal dcxp ON dcp.comprobanteafectado = dcxp.id_unico
                      LEFT JOIN
                        gf_detalle_comprobante dccxp ON dccxp.detallecomprobantepptal = dcxp.id_unico
                      WHERE
                        dcp.comprobantepptal =$egp";
                $cxp =$mysqli->query($cxp);
                if(mysqli_num_rows($cxp)>0){
                    while ($row1 = mysqli_fetch_row($cxp)) {
                        $id_comp_cnt=$row1[0];
                        // Selección de los datos  del comprobante cnt de cuenta por pagar para posterior inserción en gf_comprobante_cnt como egreso.
                        $sqlDetCom       = "SELECT detCom.id_unico, detCom.valor, detCom.proyecto, 
                               detCom.cuenta, detCom.naturaleza, detCom.centrocosto, detCom.detallecomprobantepptal, 
                               detCom.tercero 
                                FROM gf_detalle_comprobante detCom 
                                LEFT JOIN gf_comprobante_cnt com ON com.id_unico = detCom.comprobante 
                                LEFT JOIN gf_cuenta CT ON detCom.cuenta = CT.id_unico 
                                LEFT JOIN gf_clase_cuenta clacu ON clacu.id_unico = CT.clasecuenta 
                                WHERE ( com.id_unico = $id_comp_cnt and clacu.id_unico = 4) 
                                OR ( com.id_unico = $id_comp_cnt and clacu.id_unico = 8)";
                        $detComp         = $mysqli->query($sqlDetCom);
                        while ($rowDC = mysqli_fetch_row($detComp)) {
                            $terdeta                 = $rowDC[7];
                            $valor                   = $rowDC[1];
                            $valor                   = $valor * -1;
                            $proyecto                = $rowDC[2];
                            $cuenta                  = $rowDC[3];
                            $naturaleza              = $rowDC[4];
                            $centrocosto             = $rowDC[5];
                            $detallecomprobantepptal = $rowDC[6];
                            //var_dump(empty($rowDC[6]));
                            if (empty($rowDC[6])) {
                                $detallecomprobantepptal = 'NULL';
                            } //empty($rowDC[6])
                            ##DETALLE CNT AFECTADO##
                            $afec = "SELECT id_unico FROM gf_detalle_comprobante " . "WHERE comprobante ='$id_comp_cnt' AND cuenta = '$cuenta' AND valor = $rowDC[1]";
                            $afec = $mysqli->query($afec);
                            if (mysqli_num_rows($afec) > 0) {
                                $afec = mysqli_fetch_row($afec);
                                $afec = $afec[0];
                            } //mysqli_num_rows($afec) > 0
                            else {
                                $afec = 'NULL';
                            }
                            if (empty($descripcion)) {
                                $descripcion = 'NULL';
                            } //empty($descripcion)
                            $sqlDetComCntSal    = "INSERT INTO gf_detalle_comprobante (descripcion, fecha, valor, valorejecucion, comprobante, 
                                    cuenta, naturaleza, tercero, proyecto, centrocosto,  detalleafectado, detallecomprobantepptal )  
                                                        VALUES($descripcion, '$fecha', $valor, $valor, $ultimoComproCnt, $cuenta, $naturaleza, " . "$terdeta, $proyecto, $centrocosto,$afec, $detallecomprobantepptal )";
                            $resultadoDetComCnt = $mysqli->query($sqlDetComCntSal);
                            
                        } //$rowDC = mysqli_fetch_row($detComp)
                    }
                    $_SESSION['idCompCnt']           = $ultimoComproCnt;
                    $res                             = $ultimoComproCnt;
                    //AGREGADO ERICA
                    $_SESSION['idCompCnt']           = $ultimoComproCnt;
                    $_SESSION['cntEgreso']           = $ultimoComproCnt;
                    $_SESSION['id_comp_pptal_GE']    = $egp;
                    $_SESSION['nuevo_GE']            = 1;
                    $_SESSION['terceroGuardado']     = $tercero;
                    $_SESSION['comprobanteGenerado'] = $ultimoComproCnt;
                    $_SESSION['idCompCntV']          = $ultimoComproCnt;
                    $resultado=1;
                } else {
                    $resultado=1;
                }
            } else {
                $resultado=2;
            }
           echo $resultado; 
            
        break;
    }


?>