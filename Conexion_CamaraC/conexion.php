<?php
ini_set('max_execution_time', 0);
ini_set("soap.wsdl_cache_enabled", 0);
ini_set('soap.wsdl_cache_ttl', 0);
$usuario  = "714643";
$contrase = "975663";
extract($_REQUEST);
$tipo   = "ObtenerConceptos";
$url    = "https://www.sintramites.com/ServicioConveniosCCB/ServicioCCB.svc?singleWsdl";
$client = new SoapClient($url);
$fcs    = $client->__getFunctions();
$hoy    = date('Y-m-d');

function ObtenerEmpresas($usuario,$clave){
    require '../Conexion/conexion.php';
    global $url;
    global $hoy;
    $parametros = array('usuario' => "$usuario", 'contraseña' => "$clave");
    $client     = new SoapClient($url);
    $valor      = 1;
    $cont       = 0;
    while($valor > 0){
        $result = $client->ObtenerEmpresas($parametros);
        $v = count($result);
        foreach ($result as $empresas){
            $v = count($empresas);
            foreach ($empresas as $empresa){
                if(empty($empresa->Inscrito)){
                    $v= 0;    
                }else{
                    $v = count($empresa);
                }
                foreach ($empresa as $inscrito){
                    foreach ($inscrito as $data){
                        foreach ($data as $info){
                            if(!empty($info->Matricula)){
                                $XXXX = $info->Nro_Identificacion;
                                $nxxx = strlen($XXXX);
                                $Necu = strpos($XXXX,'-');
                                if($Necu === false){
                                    $quita = 0;
                                }else{
                                    $Necu1 = $Necu +  1;
                                    $quita = $nxxx - $Necu1;
                                    $ident = substr($XXXX,0,$Necu);
                                    $verif = substr($XXXX,$Necu1,$quita);
                                }
                                $BuscaCont     = "SELECT    c.* FROM gc_contribuyente c 
                                                  LEFT JOIN gf_tercero t ON c.tercero = t.id_unico   
                                                  WHERE     t.numeroidentificacion    = '$ident'";
                                $EncuentraCont = $mysqli->query($BuscaCont);
                                $nres          = mysqli_num_rows($EncuentraCont);
                                $cod_mat       = $info->Matricula;
                                if($nres < 1){
                                    $BuscaTer     = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = $ident ";
                                    $EncuentraTer = $mysqli->query($BuscaTer);
                                    $nresT        = mysqli_num_rows($EncuentraTer);
                                    $direccion    = "'$info->Direccion_Notificacion'";
                                    $telefono     = "'$info->Telefono1_Comercial";
                                    if($nresT < 1){
                                        if(!empty($info->Nombre1)){
                                            $razonS = "null";
                                            $nom1   = "'$info->Nombre1'";
                                            $nom2   = "'$info->Nombre2'";
                                            $ape1   = "'$info->Apellido1'";
                                            $ape2   = "'$info->Apellido2'";
                                            $tipoI  = 1;
                                        }else {
                                            $razonS = "'$info->Razon_Social'";
                                            $nom1   = "null";
                                            $nom2   = "null";
                                            $ape1   = "null";
                                            $ape2   = "null";
                                            $tipoI  = 2;
                                        }
                                        $ciudad       = $info->Ciudad_Comercial;
                                        $departamento = substr($ciudad,0,2);
                                        $ciudad       = substr($ciudad,2);
                                        $BuscaDepart  = "SELECT id_unico FROM gf_departamento WHERE rss = '$departamento'";
                                        $resultDep    = $mysqli->query($BuscaDepart);
                                        $resDep       = mysqli_fetch_row($resultDep);
                                        $BuscaCiudad  = "SELECT id_unico FROM gf_ciudad WHERE departamento = '$resDep[0]' AND rss = '$ciudad'";
                                        $resultCiudad = $mysqli->query($BuscaCiudad);
                                        $resCiu       = mysqli_fetch_row($resultCiudad);
                                        $insertSQL    = "INSERT INTO gf_tercero(nombreuno, nombredos, apellidouno, apellidodos,razonsocial, numeroidentificacion, compania, 
                                                                                tipoidentificacion,ciudadidentificacion,migradoCCB,digitoverificacion)
                                              VALUES($nom1, $nom2, $ape1, $ape2, $razonS, $ident,1,$tipoI,$resCiu[0],1,$verif)";
                                        $resultado    = $mysqli->query($insertSQL);
                                        $sqlC         = "SELECT MAX(id_unico) AS id FROM gf_tercero ";
                                        $resultadoC   = $mysqli->query($sqlC);
                                        $rowC         = mysqli_fetch_row($resultadoC);
                                        $idCont       = $rowC[0];
                                        if($tipoI == 1){
                                            $perfil = 3;
                                        }else{
                                            $perfil = 4;
                                        }
                                        $insertSQL = "INSERT INTO gf_perfil_tercero(perfil,tercero)VALUES('$perfil',$idCont)";
                                        $resultado = $mysqli->query($insertSQL);
                                        $insertSQL = "INSERT INTO gc_contribuyente(codigo_mat,cod_camaraC, tercero,estado,migradoCCB,fechainscripcion,dir_correspondencia,telefono,fecha_migra)VALUES($info->Matricula, $idCont, 1,1,'$info->Fecha_Matricula','$info->Direccion_Principal',$telefono,'$hoy')";
                                        $resultado = $mysqli->query($insertSQL);
                                    }else{
                                        $rowC      = mysqli_fetch_row($EncuentraTer);
                                        $idCont    = $rowC[0];
                                        $insertSQL = "INSERT INTO gc_contribuyente(codigo_mat,cod_camaraC,tercero,estado,migradoCCB,fechainscripcion,dir_correspondencia)VALUES($info->Matricula, $idCont, 1,1,'$info->Fecha_Matricula','$info->Direccion_Principal',$telefono,'$hoy')";
                                        $resultado = $mysqli->query($insertSQL);
                                    }
                                    $cod_mat = $info->Matricula;
                                    $camara  = $info->Camara;
                                }else{
                                    $res     = mysqli_fetch_row($EncuentraCont);
                                    $camara  = $info->Camara;
                                    $cod_mat = $info->Matricula;
                                }
                                $CollectionInscritoRecibido[] = array("Matricula_Inscrito"=>$cod_mat,"Camara_Inscrito"=>$camara); 
                            }else{
                                foreach ($info as $esta){
                                    $cuantosEst =  count($esta);
                                    if($cuantosEst > 1){
                                        foreach ($esta as $EST){
                                            $BuscaCont    = "SELECT id_unico FROM gc_contribuyente WHERE cod_camaraC = '$cod_mat'";
                                            $resultC      = $mysqli->query($BuscaCont);
                                            $rowC         = mysqli_fetch_row($resultC);
                                            $ciudad       = $EST->Ciudad_Comercial;
                                            $matriEst     = $EST->Matricula_establecimiento;
                                            $departamento = substr($ciudad,0,2);
                                            $ciudad       = substr($ciudad,2);
                                            $BuscaDepart  = "SELECT id_unico FROM gf_departamento WHERE rss = '$departamento'";
                                            $resultDep    = $mysqli->query($BuscaDepart);
                                            $resDep       = mysqli_fetch_row($resultDep);
                                            $BuscaCiudad  = "SELECT id_unico FROM gf_ciudad WHERE departamento = '$resDep[0]' AND rss = '$ciudad'";
                                            $resultCiudad = $mysqli->query($BuscaCiudad);
                                            $resCiu       = mysqli_fetch_row($resultCiudad);
                                            $fechaI       = "'$EST->Fecha_Matricula'";
                                            $nombreE      = "'$EST->Nombre_Comercial'";
                                            $direccionC   = "'$EST->Direccion_Comercial'";
                                            $ExisteEst    = "SELECT  contribuyente FROM gc_establecimiento WHERE cod_mat = $matriEst";
                                            $ExisEst      = $mysqli->query($ExisteEst);
                                            $nexisE       = mysqli_num_rows($ExisEst);
                                            if($nexisE < 1){
                                                $insertSQL = "INSERT INTO gc_establecimiento(contribuyente,cod_mat,nombre,fechainscripcion,direccion,ciudad,migradoCCB,fecha_migra)VALUES($rowC[0],$matriEst,$nombreE,$fechaI,$direccionC,$resCiu[0],1,'$hoy')";
                                                $resultado = $mysqli->query($insertSQL);
                                            }else{
                                                $EEst = mysqli_fetch_row($ExisEst);
                                            }
                                            $CollectionEstablecimientoRecibido[] = array("Matricula_Establecimiento"=>$matriEst);    
                                        }
                                    }else{
                                        $BuscaCont    = "SELECT id_unico FROM gc_contribuyente WHERE cod_camaraC = '$cod_mat'";
                                        $resultC      = $mysqli->query($BuscaCont);
                                        $rowC         = mysqli_fetch_row($resultC);
                                        $matriEst     = $esta->Matricula_establecimiento;
                                        $ciudad       = $esta->Ciudad_Comercial;
                                        $departamento = substr($ciudad,0,2);
                                        $ciudad       = substr($ciudad,2);
                                        $BuscaDepart  = "SELECT id_unico FROM gf_departamento WHERE rss = '$departamento'";
                                        $resultDep    = $mysqli->query($BuscaDepart);
                                        $resDep       = mysqli_fetch_row($resultDep);
                                        $BuscaCiudad  = "SELECT id_unico FROM gf_ciudad WHERE departamento = '$resDep[0]' AND rss = '$ciudad'";
                                        $resultCiudad = $mysqli->query($BuscaCiudad);
                                        $resCiu       = mysqli_fetch_row($resultCiudad);
                                        $fechaI       = "'$esta->Fecha_Matricula'";
                                        $nombreE      = "'$esta->Nombre_Comercial'";
                                        $direccionC   = "'$esta->Direccion_Comercial'";
                                        $ExisteEst    = "SELECT  contribuyente FROM gc_establecimiento WHERE cod_mat = $matriEst";
                                        $ExisEst      = $mysqli->query($ExisteEst);
                                        $nexisE       = mysqli_num_rows($ExisEst);
                                        if($nexisE < 1){
                                            $insertSQL = "INSERT INTO gc_establecimiento(contribuyente,cod_mat,nombre,fechainscripcion,direccion,ciudad,migradoCCB)
                                                          VALUES($rowC[0],$matriEst,$nombreE,$fechaI,$direccionC,$resCiu[0],1)";
                                            $resultado = $mysqli->query($insertSQL);
                                        }else{
                                            $EEst = mysqli_fetch_row($ExisEst);
                                        }
                                        $CollectionEstablecimientoRecibido[] = array("Matricula_Establecimiento"=>$matriEst);    
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $valor = 0;
    }
}

function NovedadInscrito($usuario,$clave){
    global $url;
    global $hoy;
    require '../Conexion/conexion.php';
    $parametros = array('usuario' => "$usuario", 'contraseña' => "$clave");
    $client     = new SoapClient($url);
    $valor      = 1;
    $cont       = 0;
    $nombreI    = "Contribuyentes_No_Encontrados";	//Nombre del informe
    $consulta   = "";					         	//Variable con la consulta a realizar
    $num_filas  = 0;								//Número de filas
    $num_cols   = 0;								//Número de columnas
    $errores    = "";								//Variable de captura de errores
    $info_campo = "";						        //variable para obtener los nombres de los campos
    $cols_nom   = array();                          //Array para capturar los nombres de las columnas
    $nom_cols   = "";								//String de captura de los mombres de las columnas de manera lineal
    $csv        = "";							    //Variable para generar csv
    $shtml      = "";								//Variable de armado de html
    $separador  = ",";								//Variable para recibir el separador
    $lineas     = "";								//Variable para obtener las lineas del archivo txt
    $txtName    = $nombreI.".txt";
    $sfile      = '../documentos/generador_informes/txt/'.$txtName;
    while($valor > 0){
        $result = $client->ObtenerNovedades_Inscrito($parametros);
        foreach ($result as $novedadI){
            foreach ($novedadI as $valorN){
                foreach ($valorN as $noved){
                    foreach ($noved as $novInc){
                        $XXXX = $novInc->Nro_Identificacion_Padre;
                        if(!empty($XXXX)){
                            $Necu          = strpos($XXXX,'-');
                            $ident         = substr($XXXX,0,$Necu);
                            $BuscaCont     = "SELECT    c.* FROM gc_contribuyente c 
                                              LEFT JOIN gf_tercero t ON c.tercero = t.id_unico   
                                              WHERE     t.numeroidentificacion    = '$ident'";
                            $EncuentraCont = $mysqli->query($BuscaCont);
                            $nres          = mysqli_num_rows($EncuentraCont);
                            if($nres < 1){
                                $Num_Mat        = $novInc->Matricula;
                                $Num_ident      = $novInc->Nro_Identificacion_Padre;
                                $espacio        = "\t";
                                $lineas         .= $Num_Mat.$espacio.$Num_ident."\r\n";
                                $BuscaTer       = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = '$ident' ";
                                $EncuentraTer   = $mysqli->query($BuscaTer);
                                $nresT          = mysqli_num_rows($EncuentraTer);
                                if($nresT < 1){
                                    if(!empty($info->Nombre1)){
                                        $razonS = "null";
                                        $nom1   = "'$info->Nombre1'";
                                        $nom2   = "'$info->Nombre2'";
                                        $ape1   = "'$info->Apellido1'";
                                        $ape2   = "'$info->Apellido2'";
                                        $tipoI  = 1;
                                    }else{
                                        $razonS = "'$info->Razon_Social'";
                                        $nom1   = "null";
                                        $nom2   = "null";
                                        $ape1   = "null";
                                        $ape2   = "null";
                                        $tipoI  = 2;
                                    }
                                    $insertSQL = "INSERT INTO gf_tercero(nombreuno, nombredos, apellidouno, apellidodos,razonsocial, numeroidentificacion, compania, tipoidentificacion,migradoCCB)
                                            VALUES($nom1, $nom2, $ape1, $ape2, $razonS, $ident,1,$tipoI,1)";

                                    $resltado   = $mysqli->query($insertSQL);
                                    $sqlC       = "SELECT MAX(id_unico) AS id FROM gf_tercero ";
                                    $resultadoC = $mysqli->query($sqlC);
                                    $rowC       = mysqli_fetch_row($resultadoC);
                                    $idCont     = $rowC[0];
                                    if($tipoI == 1){
                                        $perfil = 3;
                                    }else{
                                        $perfil = 4;
                                    }
                                    $insertSQL = "INSERT INTO gf_perfil_tercero(perfil,tercero)VALUES('$perfil',$idCont)";
                                    $resultado = $mysqli->query($insertSQL);
                                    $insertSQL = "INSERT INTO gc_contribuyente(codigo_mat,tercero,estado,migradoCCB,fecha_migra)VALUES($info->Matricula, $idCont, 1,1,'$hoy')";
                                    $resultado = $mysqli->query($insertSQL);
                                }else{
                                    $rowC      = mysqli_fetch_row($EncuentraTer);
                                    $idCont    = $rowC[0];
                                    $insertSQL = "INSERT INTO gc_contribuyente(codigo_mat,tercero,estado,migradoCCB,fecha_migra)VALUES($info->Matricula, $idCont, 1,1,'$hoy')";
                                    $resultado = $mysqli->query($insertSQL);
                                }
                            }else{
                                $rowCont    = mysqli_fetch_row($EncuentraCont);
                                $MatC       = $novInc->Matricula;
                                $direccion  = $novInc->Direccion_Principal;
                                $codigoP    = $novInc->Codigo_Postal_Principal;
                                $telefonoP  = $novInc->Telefono1_Principal;
                                $camaraI    = $novInc->Camara;
                                $FechaNov   = $novInc->Fecha_Novedad;
                                if($rowCont[2] != $MatC){
                                    $actualiza = "UPDATE gc_contribuyente SET cod_camaraC = $MatC WHERE id_unico = '$rowCont[0]'";
                                    $resultado = $mysqli->query($actualiza);
                                    $campo     = "cod_camaraC";
                                    $mutacion  = "INSERT INTO gc_mutaciones(tipo_mut,campo,valor_act,valor_muta,id,fecha)VALUES(1,$campo,'$rowCont[2]','$MatC',$rowCont[0],'$hoy')";
                                    $resultado = $mysqli->query($mutacion);
                                }
                                if($rowCont[11] != $direccion){
                                    $actualiza = "UPDATE gc_contribuyente SET dir_correspondencia = $direccion WHERE id_unico = '$rowCont[0]'";
                                    $resultado = $mysqli->query($actualiza);
                                    $campo     = "dir_correspondencia";
                                    $mutacion  = "INSERT INTO gc_mutaciones(tipo_mut,campo,valor_act,valor_muta,id,fecha)VALUES(1,$campo,'$rowCont[11]','$direccion',$rowCont[0],'$hoy')";
                                    $resultado = $mysqli->query($mutacion);
                                }
                                if($rowCont[5] != $codigoP){
                                    $actualiza = "UPDATE gc_contribuyente SET cod_postal = $codigoP WHERE id_unico = '$rowCont[0]'";
                                    $resultado = $mysqli->query($actualiza);
                                    $campo     = "cod_postal";
                                    $mutacion  = "INSERT INTO gc_mutaciones(tipo_mut,campo,valor_act,valor_muta,id,fecha)VALUES(1,$campo,'$rowCont[5]','$codigoP',$rowCont[0],'$hoy')";
                                    $resultado = $mysqli->query($mutacion);
                                }
                                if($rowCont[12] != $telefonoP){
                                    $actualiza = "UPDATE gc_contribuyente SET telefono = $telefonoP WHERE id_unico = '$rowCont[0]'";
                                    $resultado = $mysqli->query($actualiza);
                                    $campo     = "telefono";
                                    $mutacion  = "INSERT INTO gc_mutaciones(tipo_mut,campo,valor_act,valor_muta,id,fecha)VALUES(1,$campo,'$rowCont[12]','$telefonoP',$rowCont[0],'$hoy')";
                                    $resultado = $mysqli->query($mutacion);
                                }
                            }
                            $cont ++;
                            $CollectionInscritoRecibido[] = array("Matricula_Inscrito"=>$MatC,"Camara_Inscrito"=>$camaraI,"FechaNovedad"=>$FechaNov);  
                            $array1                       = array("usuario" => "$usuario", "contraseña" => "$clave", "CollectionInscritoRecibido" => $CollectionInscritoRecibido);
                            $response1                    = $client->__soapCall("ActualizarSw_CaeNovedades_Inscritos", array($array1));
                        }else{
                            $cont = 0;
                        } 
                    }
                }    
            }
        }
        $valor = $cont;
    }
    
    $fp=fopen($sfile,"w" );                                                     //Abrimos el archivo en modo de escritura
    fwrite($fp,$lineas);                                                        //Escribimos el html del archivo
    fclose($fp);                                                                //Cerramos el archivo
    return $sfile;
}

function ObtenerNovedadEstablecimiento($usuario,$clave){
    global $url;
    global $hoy;
    require '../Conexion/conexion.php';
    $parametros = array('usuario' => "$usuario", 'contraseña' => "$clave");
    $client     = new SoapClient($url);
    $valor      = 1;
    $cont       = 0;
    $nombreI    = "Establecimientos_No_Encontrados";//Nombre del informe
    $consulta   = "";								//Variable con la consulta a realizar
    $num_filas  = 0;								//Número de filas
    $num_cols   = 0;								//Número de columnas
    $errores    = "";								//Variable de captura de errores
    $info_campo = "";								//variable para obtener los nombres de los campos
    $cols_nom   = array();							//Array para capturar los nombres de las columnas
    $nom_cols   = "";								//String de captura de los mombres de las columnas de manera lineal
    $csv        = "";								//Variable para generar csv
    $shtml      = "";								//Variable de armado de html
    $separador  = ",";								//Variable para recibir el separador
    $lineas1    = "";								//Variable para obtener las lineas del archivo txt
    $txtName    = $nombreI.".txt";
    $sfile1     = '../documentos/generador_informes/txt/'.$txtName;
    while($valor > 0){
        $result = $client->ObtenerNovedades_Establecimiento($parametros);
        foreach ($result as $novedadE){
            foreach ($novedadE as $nEstablecimiento) {
                foreach ($nEstablecimiento as $NovEsta) {
                    foreach ($NovEsta as $CON) {
                        $ident = $CON->Identificacion_Padre;
                        if(!empty($ident)){
                            $ident                = substr($ident,0,-2);
                            $ident                = $ident;
                            $Num_Mat1             = $CON->Matricula_establecimiento;
                            $Num_ident1           = $CON->Identificacion_Padre;
                            $fechaN               = $CON->Fecha_Novedad;
                            $BuscaEstablecimiento = "SELECT   e.* FROM gc_establecimiento e
                                                    LEFT JOIN gc_contribuyente c ON e.contribuyente = c.id_unico
                                                    LEFT JOIN gf_tercero t       ON c.tercero = t.id_unico  
                                                    WHERE     t.numeroidentificacion = '$ident' 
                                                    AND       e.cod_mat              = '$Num_Mat1'";
                            $resultEsta           = $mysqli->query($BuscaEstablecimiento);
                            $nresE                = mysqli_num_rows($resultEsta);
                            if($nresE < 1){
                                $espacio  = "\t";
                                $lineas1 .= $Num_Mat1.$espacio.$Num_ident1."\r\n";
                            }else{
                                $rowEsta      = mysqli_fetch_row($resultEsta);
                                $nombreE      = $CON->Nombre_Comercial;
                                $direccionC   = $CON->Direccion_Comercial;
                                $ciudad       = $CON->Ciudad_Comercial;
                                $fechaN       = $CON->Fecha_Novedad;
                                $departamento = substr($ciudad,0,2);
                                $ciudad       = substr($ciudad,2);
                                $BuscaDepart  = "SELECT id_unico FROM gf_departamento WHERE rss = '$departamento'";
                                $resultDep    = $mysqli->query($BuscaDepart);
                                $resDep       = mysqli_fetch_row($resultDep);
                                $BuscaCiudad  = "SELECT id_unico FROM gf_ciudad WHERE departamento = '$resDep[0]' AND rss = '$ciudad'";
                                $resultCiudad = $mysqli->query($BuscaCiudad);
                                $resCiu       = mysqli_fetch_row($resultCiudad);
                                if($rowEsta[3] != $nombreE ){
                                    $actualiza = "UPDATE gc_establecimiento SET nombre = $nombreE WHERE id_unico = '$rowEsta[0]'";
                                    $resultado = $mysqli->query($actualiza);
                                    $campo     = "nombre";
                                    $mutacion  = "INSERT INTO gc_mutaciones(tipo_mut,campo,valor_act,valor_muta,id,fecha)VALUES(1,$campo,'$rowEsta[3]','$nombreE',$rowEsta[0],'$hoy')";
                                    $resultado = $mysqli->query($mutacion);
                                }
                                if($rowEsta[7] != $direccionC ){
                                    $actualiza = "UPDATE gc_establecimiento SET direccion = $direccionC WHERE id_unico = '$rowEsta[0]'";
                                    $resultado = $mysqli->query($actualiza);
                                    $campo     = "direccion";
                                    $mutacion  = "INSERT INTO gc_mutaciones(tipo_mut,campo,valor_act,valor_muta,id,fecha)VALUES(1,$campo,'$rowEsta[7]','$direccionC',$rowEsta[0],'$hoy')";
                                    $resultado = $mysqli->query($mutacion);
                                }
                                if($rowEsta[9] != $resCiu[0] ){
                                    $actualiza = "UPDATE gc_establecimiento SET ciudad = $resCiu WHERE id_unico = '$rowEsta[0]'";
                                    $resultado = $mysqli->query($actualiza);
                                    $campo     = "ciudad";
                                    $mutacion  = "INSERT INTO gc_mutaciones(tipo_mut,campo,valor_act,valor_muta,id,fecha)VALUES(1,$campo,'$rowEsta[9]','$resCiu',$rowEsta[0],'$hoy')";
                                    $resultado = $mysqli->query($mutacion);
                                }
                            }
                            $cont ++;
                            $CollectionEstablecimientoRecibido[] = array("Matricula_Establecimiento"=>$Num_Mat1,"FechaNovedad"=>$fechaN);
                            $array2                              = array("usuario" => "$usuario", "contraseña" => "$clave", "CollectionEstablecimientoRecibido" => $CollectionEstablecimientoRecibido);
                        }else{
                            $cont = 0;
                        }
                    }
                }
            }
        }
        $valor = $cont;
    }
    $fp1=fopen($sfile1,"w" );                                             //Abrimos el archivo en modo de escritura
    fwrite($fp1,$lineas1);                                                      //Escribimos el html del archivo
    fclose($fp1);                                                               //Cerramos el archivo
    return $sfile1;
}
$var   = ObtenerEmpresas($usuario,$contrase);
$var1  = NovedadInscrito($usuario,$contrase);
$var2  = ObtenerNovedadEstablecimiento($usuario,$contrase);
echo "resultado: ".$var."; $var1; $var2";
?>
