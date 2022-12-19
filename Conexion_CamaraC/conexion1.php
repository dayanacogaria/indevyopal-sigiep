<?php
    ini_set('max_execution_time', 0);
    ini_set("soap.wsdl_cache_enabled", 0);
    ini_set('soap.wsdl_cache_ttl', 0);
    //require_once '../NuSOAP/lib/nusoap.php';
    require '../Conexion/conexion.php';

    $url     = 'http://www.sintramites.com/ServicioConveniosCCB/ServicioCCB.svc?singleWsdl';
    $user     = "714643";
    $password = "975663";

    $url2 = 'http://www.sintramites.com/ServicioConveniosCCB/ServicioCCB.svc?wsdl';
    //$parameters['usuario']   = $user;
    //$parameters['contraseña'] = $password;
    $client = new SoapClient($url);
    $client->__getFunctions();
    $ws  = new SoapClient($url);
    #$ws2 = new SoapClient($url2);
    $param =  array('usuario'=>$user,'contraseña'=>$password);
    //echo "<h1 align='center'>Pruebas</h1>";
    $banks = $ws->ObtenerEmpresas($param);

     var_dump($banks);
     echo "<br/>";
      #                  echo "<br/>";
    //$retro = $ws->ActualizarSwitch_Cae_Inscritos($param,$banks);
    $arregloCont = array();
    $arregloEsta = array();
    $j = 0;
    $i = 0;
    /*foreach ($banks as $empresas) {
        #var_dump($empresas);
        
        foreach ($empresas as $empresa) {
            foreach ($empresa as $inscrito) {
                foreach ($inscrito as $data) {
                    foreach ($data as $info) {
                        #var_dump($info);
                        #echo "<br/>";
                        #echo "<br/>";

                        if(!empty($info->Matricula)){

                            #Consulta si el contribuyente existe
                            $BuscaCont = "SELECT * FROM gc_contribuyente WHERE codigo_mat = '$info->Matricula'";
                            $EncuentraCont = $mysqli->query($BuscaCont);
                            $nres = mysqli_num_rows($EncuentraCont);
                            $cod_mat = $info->Matricula;
                            #valida si el si no existe el contribuyente
                            if($nres < 1){

                                $ident = $info->Nro_Identificacion;
                                $ident = substr($ident,0,-2);
                                $ident = "'$ident'";

                                #consulta si existe el tercero
                                $BuscaTer = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = $ident ";
                                $EncuentraTer = $mysqli->query($BuscaTer);
                                $nresT = mysqli_num_rows($EncuentraTer);

                                #valida si el no existe el tercero
                                $direccion = "'$info->Direccion_Notificacion'";
                                if($nresT < 1){
                                    if(!empty($info->Nombre1)){
                                        $razonS = "null";
                                        $nom1 = "'$info->Nombre1'";
                                        $nom2 = "'$info->Nombre2'";
                                        $ape1 = "'$info->Apellido1'";
                                        $ape2 = "'$info->Apellido2'";
                                        $tipoI = 1;
                                    }else {
                                        $razonS = "'$info->Razon_Social'";
                                        $nom1 = "null";
                                        $nom2 = "null";
                                        $ape1 = "null";
                                        $ape2 = "null";
                                        $tipoI = 2;
                                    }

                                    $ciudad = $info->Ciudad_Comercial;

                                    $departamento = substr($ciudad,0,2);
                                    $ciudad = substr($ciudad,2);

                                    $BuscaDepart = "SELECT id_unico FROM gf_departamento WHERE rss = '$departamento'";
                                    $resultDep = $mysqli->query($BuscaDepart);
                                    $resDep = mysqli_fetch_row($resultDep);

                                    $BuscaCiudad = "SELECT id_unico FROM gf_ciudad WHERE departamento = '$resDep[0]' AND rss = '$ciudad'";
                                    $resultCiudad = $mysqli->query($BuscaCiudad);
                                    $resCiu = mysqli_fetch_row($resultCiudad);
                            
                                    $insertSQL = "INSERT INTO gf_tercero(nombreuno, nombredos, apellidouno, apellidodos,razonsocial, numeroidentificacion, compania,        tipoidentificacion,ciudadidentificacion,migradoCCB)
                                          VALUES($nom1, $nom2, $ape1, $ape2, $razonS, $ident,1,$tipoI,$resCiu[0],1)";
                            
                                    $resltado = $mysqli->query($insertSQL);

                                    $sqlC="SELECT MAX(id_unico) AS id FROM gf_tercero ";
                                    $resultadoC = $mysqli->query($sqlC);
                                    $rowC=mysqli_fetch_row($resultadoC);
                                    $idCont = $rowC[0];

                                    if($tipoI == 1){
                                        $perfil = 3;
                                    }else{
                                        $perfil = 4;
                                    }

                                    $insertSQL = "INSERT INTO gf_perfil_tercero(perfil,tercero)VALUES('$perfil',$idCont)";
                                    $resultado = $mysqli->query($insertSQL);

                                    $BuscaTipoDireccion = "SELECT Id_unico FROM gf_tipo_direccion WHERE Nombre = 'Comercial'";
                                    $resultTipoDir = $mysqli->query($BuscaTipoDireccion);
                                    $nresTipoD = mysqli_num_rows($resultTipoDir);

                                    if($nresTipoD > 0){
                                        $resTD = mysqli_fetch_row($resultTipoDir);
                                    }else{
                                        $insertSQL = "INSERT INTO gf_tipo_direccion(Nombre)VALUES('Comercial')";
                                        $resultado = $mysqli->query($insertSQL);

                                        $sqlTD="SELECT MAX(Id_unico) AS id FROM gf_tipo_direccion ";
                                        $resultadoTD = $mysqli->query($sqlTD);
                                        $rowTD=mysqli_fetch_row($resultadoTD);

                                        $resTD[0] = $rowTD[0];
                                    }

                                    $insertSQL = "INSERT INTO gf_direccion(direccion, tipo_direccion,ciudad_direccion,tercero)VALUES($direccion,$resTD[0],$resCiu[0],$idCont)";
                                    $resultado = $mysqli->query($insertSQL);
                                    #echo "<br/>";
                            
                                    $insertSQL = "INSERT INTO gc_contribuyente(codigo_mat,tercero,estado,migradoCCB)VALUES($info->Matricula, $idCont, 1,1)";
                                    $resultado = $mysqli->query($insertSQL);
                            
                                }else{
                                    $rowC = mysqli_fetch_row($EncuentraTer);
                                    $idCont = $rowC[0];

                                    $insertSQL = "INSERT INTO gc_contribuyente(codigo_mat,tercero,estado,migradoCCB)VALUES($info->Matricula, $idCont, 1,1)";
                                    $resultado = $mysqli->query($insertSQL);
                                }

                                $cod_mat = $info->Matricula;
                                $camara = $info->Camara;
                            }else{
                                $res = mysqli_fetch_row($EncuentraCont);

                                //echo "el contribuyente con matricula: ".$info->Matricula." ya se encuentra Registrado ";
                                //echo "<br/>";
                                $camara = $info->Camara;
                                $cod_mat = $info->Matricula;
                            }
                        
                            $InscritosRecibido[$i] = array("matricula_Inscrito"=>$cod_mat,"camara_Inscrito"=>$camara);
                            $i++;
                        }else{

                            foreach ($info as $esta){
                                #var_dump($esta);
                                    #echo "<br/>";
                                    #echo "<br/>";
                                    #echo "<br/>";
                                    #echo "<br/>";
                                $cuantosEst =  count($esta);
                                if($cuantosEst > 1){
                                    foreach ($esta as $EST) {
                                    
                                        $BuscaCont = "SELECT id_unico FROM gc_contribuyente WHERE codigo_mat = '$cod_mat'";
                                        $resultC   = $mysqli->query($BuscaCont);
                                        $rowC      = mysqli_fetch_row($resultC);
                                        #echo "<br/>";
                                        #var_dump($EST);
                                        #echo "<br/>";
                                       
                                        $ciudad = $EST->Ciudad_Comercial;
                                        $matriEst = $EST->Matricula_establecimiento;
                                        #echo "<br/>";
                                        $departamento = substr($ciudad,0,2);
                                        $ciudad = substr($ciudad,2);

                                        $BuscaDepart = "SELECT id_unico FROM gf_departamento WHERE rss = '$departamento'";
                                        $resultDep = $mysqli->query($BuscaDepart);
                                        $resDep = mysqli_fetch_row($resultDep);

                                        $BuscaCiudad = "SELECT id_unico FROM gf_ciudad WHERE departamento = '$resDep[0]' AND rss = '$ciudad'";
                                        $resultCiudad = $mysqli->query($BuscaCiudad);
                                        $resCiu = mysqli_fetch_row($resultCiudad);

                                        $fechaI = "'$EST->Fecha_Matricula'";

                                        $nombreE = "'$EST->Nombre_Comercial'";
                                        $direccionC = "'$EST->Direccion_Comercial'";

                                        $ExisteEst = "SELECT  contribuyente FROM gc_establecimiento WHERE cod_mat = $matriEst";
                                        $ExisEst = $mysqli->query($ExisteEst);
                                        $nexisE = mysqli_num_rows($ExisEst);

                                        if($nexisE < 1){
                                            $insertSQL = "INSERT INTO gc_establecimiento(contribuyente,cod_mat,nombre,fechainscripcion,direccion,ciudad,migradoCCB)VALUES($rowC[0],$matriEst,$nombreE,$fechaI,$direccionC,$resCiu[0],1)";
                                            $resultado = $mysqli->query($insertSQL);
                                        }else{
                                            $EEst = mysqli_fetch_row($ExisEst);
                                       #echo "el establemcimiento con matricula ".$matriEst." ya se encuentra registrado a nombre del contribuyente: ".$EEst[0];
                                        }
                                        
                                        //echo $ciudad;
                                        #echo "<br/>";

                                        $arregloEsta[$j] = array('matricula_Establecimiento'=>$matriEst);
                                        $j++;
                                    }  
                                }else{
                                        $BuscaCont = "SELECT id_unico FROM gc_contribuyente WHERE codigo_mat = '$cod_mat'";
                                        $resultC   = $mysqli->query($BuscaCont);
                                        $rowC      = mysqli_fetch_row($resultC);
                                        #echo "<br/>";
                                        #var_dump($EST);
                                        #echo "<br/>";
                                        $matriEst = $esta->Matricula_establecimiento;
                                        $ciudad = $esta->Ciudad_Comercial;
                                        #echo "<br/>";
                                        $departamento = substr($ciudad,0,2);
                                        $ciudad = substr($ciudad,2);

                                        $BuscaDepart = "SELECT id_unico FROM gf_departamento WHERE rss = '$departamento'";
                                        $resultDep = $mysqli->query($BuscaDepart);
                                        $resDep = mysqli_fetch_row($resultDep);

                                        $BuscaCiudad = "SELECT id_unico FROM gf_ciudad WHERE departamento = '$resDep[0]' AND rss = '$ciudad'";
                                        $resultCiudad = $mysqli->query($BuscaCiudad);
                                        $resCiu = mysqli_fetch_row($resultCiudad);

                                        $fechaI = "'$esta->Fecha_Matricula'";

                                        $nombreE = "'$esta->Nombre_Comercial'";
                                        $direccionC = "'$esta->Direccion_Comercial'";
                                        
                                        $ExisteEst = "SELECT  contribuyente FROM gc_establecimiento WHERE cod_mat = $matriEst";
                                        $ExisEst = $mysqli->query($ExisteEst);
                                        $nexisE = mysqli_num_rows($ExisEst);

                                        if($nexisE < 1){
                                            $insertSQL = "INSERT INTO gc_establecimiento(contribuyente,cod_mat,nombre,fechainscripcion,direccion,ciudad,migradoCCB)VALUES($rowC[0],$matriEst,$nombreE,$fechaI,$direccionC,$resCiu[0],1)";
                                            $resultado = $mysqli->query($insertSQL);
                                        }else{
                                            $EEst = mysqli_fetch_row($ExisEst);
                                            #echo "el establemcimiento con matricula ".$matriEst." ya se encuentra registrado a nombre del contribuyente: ".$EEst[0];
                                        }
                                      
                                        //echo $ciudad;
                                        #echo "<br/>";
                                        $EstablecimientosRecibido[$j] = array('matricula_Establecimiento'=>$matriEst);
                                        $j++;
                                }
                                
                            }
                        }
                    }
                }
            }
        }
    }
    /*echo "Array De contribuyentes o empresas para enviar en el método de ActualizarSw_Cae_Inscritos(Esta información es la que llega del método ObternerEmpresas)";
    echo "<br/>";
     echo "<br/>";
    var_dump($InscritosRecibido);
    echo "<br/>";
    echo "<br/>";
     echo "<br/>";
    echo "Array de estableciminetos para enviar en el método de  ActualizarSw_Cae_Establecimientos(Esta información es la que llega del métodoObternerEmpresas)";
    echo "<br/>";
     echo "<br/>";
    var_dump($EstablecimientosRecibido);
    echo "<br/>";
    echo "<br/>";
     echo "<br/>";*/
    #$arregloCont = array(334250,5);
    $usuario = array('usuario'=>$user);
    $clave =  array('contrasseña' =>$password);
    
    function Actualizar_Contribuyentes($usuario, $contraseña, $client, $mysqli){
        global $InscritosRecibido;
        $array = array("usuario" => "$usuario", "contraseña" => "$contraseña", "CollectionInscritoRecibido"=>$InscritosRecibido);
        $response = $client->__soapCall("ActualizarSw_Cae_Inscritos", array($array));
    }

    $array1 = array("usuario" => "$user", "contraseña" => "$password", "CollectionInscritoRecibido"=>$InscritosRecibido);
    $array2 = array("usuario" => "$user", "contraseña" => "$password", "CollectionEstablecimientoRecibido"=>$EstablecimientosRecibido);
    
    $retroC = Actualizar_Contribuyentes($array);    
    #$retroC = $ws->ActualizarSw_Cae_Inscritos($user,$password,$coleccion1);
    $retroE = $ws->ActualizarSw_Cae_Establecimientos($array2);
    /*echo"Resultado del método ActualizarSw_Cae_Inscritos";
    echo "<br/>";
     echo "<br/>";
    var_dump($retroC);
    echo "<br/>";
    echo "<br/>";
     echo "<br/>";
    echo"Resultado del método ActualizarSw_Cae_Establecimientos";
    echo "<br/>";
     echo "<br/>";
      var_dump($retroE);*/
    #var_dump($retroE);
    #echo "contribuyentes: ".$retroC;

    $nombreI = "Contribuyentes_No_Encontrados";										//Nombre del informe
    $consulta = "";										//Variable con la consulta a realizar
    $num_filas = 0;										//Número de filas
    $num_cols = 0;										//Número de columnas
    $errores = "";										//Variable de captura de errores
    $info_campo = "";									//variable para obtener los nombres de los campos
    $cols_nom = array();								//Array para capturar los nombres de las columnas
    $nom_cols = "";										//String de captura de los mombres de las columnas de manera lineal
    $csv = "";											//Variable para generar csv
    $shtml = "";										//Variable de armado de html
    $separador = ",";									//Variable para recibir el separador
    $lineas = "";										//Variable para obtener las lineas del archivo txt
    $txtName = $nombreI.".txt";
    $sfile = '../documentos/generador_informes/txt/'.$txtName;
    $NovInsct = $ws->ObtenerNovedades_Inscrito($param);
  
    foreach ($NovInsct as $novedadI){
        foreach ($novedadI as $valor) {
          foreach ($valor as $noved) {
            foreach ($noved as $novInc) {
              $BuscaCont = "SELECT * FROM gc_contribuyente WHERE codigo_mat = '$novInc->Matricula'";
              $EncuentraCont = $mysqli->query($BuscaCont);
              $nres = mysqli_num_rows($EncuentraCont);
              
              var_dump($novInc);
              echo "<br/>";
              echo "<br/>";
    
              #valida si el si no existe el contribuyente
              if($nres < 1){



                $Num_Mat        = $novInc->Matricula;
                $Num_ident      = $novInc->Nro_Identificacion_Padre;
                $espacio        = "           ";
                //echo "matricula: ".$Num_Mat."  ident: ".$Num_ident;
                $lineas .= $Num_Mat.$espacio.$Num_ident."\r\n";

                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                            //Validamos que el archivo exista
                            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                
                $ident = $info->Nro_Identificacion;
                $ident = substr($ident,0,-2);

                #consulta si existe el tercero
                $BuscaTer = "SELECT id_unico FROM gf_tercero WHERE numeroidentificacion = '$ident' ";
                $EncuentraTer = $mysqli->query($BuscaTer);
                $nresT = mysqli_num_rows($EncuentraTer);

                #valida si el no existe el tercero
                if($nresT < 1){
                  if(!empty($info->Nombre1)){
                    $razonS = "null";
                    $nom1 = "'$info->Nombre1'";
                    $nom2 = "'$info->Nombre2'";
                    $ape1 = "'$info->Apellido1'";
                    $ape2 = "'$info->Apellido2'";
                    $tipoI = 1;
                  }else {
                    $razonS = "'$info->Razon_Social'";
                    $nom1 = "null";
                    $nom2 = "null";
                    $ape1 = "null";
                    $ape2 = "null";
                    $tipoI = 2;
                  }

                  $insertSQL = "INSERT INTO gf_tercero(nombreuno, nombredos, apellidouno, apellidodos,razonsocial, numeroidentificacion, compania, tipoidentificacion,migradoCCB)
                                VALUES($nom1, $nom2, $ape1, $ape2, $razonS, $ident,1,$tipoI,1)";
                  $resltado = $mysqli->query($insertSQL);

                  $sqlC="SELECT MAX(id_unico) AS id FROM gf_tercero ";
                  $resultadoC = $mysqli->query($sqlC);
                  $rowC=mysqli_fetch_row($resultadoC);
                  $idCont = $rowC[0];

                  if($tipoI == 1){
                    $perfil = 3;
                  }else{
                    $perfil = 4;
                  }

                  $insertSQL = "INSERT INTO gf_perfil_tercero(perfil,tercero)VALUES('$perfil',$idCont)";
                  $resultado = $mysqli->query($insertSQL);

                  $insertSQL = "INSERT INTO gc_contribuyente(codigo_mat,tercero,estado,migradoCCB)VALUES($info->Matricula, $idCont, 1,1)";
                  $resultado = $mysqli->query($insertSQL);
                }else{
                  $rowC = mysqli_fetch_row($EncuentraTer);
                  $idCont = $rowC[0];

                  $insertSQL = "INSERT INTO gc_contribuyente(codigo_mat,tercero,estado,migradoCCB)VALUES($info->Matricula, $idCont, 1,1)";
                  $resultado = $mysqli->query($insertSQL);
                }
              }else{
                //echo "matricula Encontrada: ".$Num_Mat."  ident Encontrada: ".$Num_ident;
                 $rowCont = mysqli_fetch_row($EncuentraCont);
              }

            }
          }
        }
    }

    $fp=fopen($sfile,"w" ); 				//Abrimos el archivo en modo de escritura
    fwrite($fp,$lineas); 					//Escribimos el html del archivo
    fclose($fp); 							//Cerramos el archivo

    $nombreI = "Establecimientos_No_Encontrados";										//Nombre del informe
    $consulta = "";										//Variable con la consulta a realizar
    $num_filas = 0;										//Número de filas
    $num_cols = 0;										//Número de columnas
    $errores = "";										//Variable de captura de errores
    $info_campo = "";									//variable para obtener los nombres de los campos
    $cols_nom = array();								//Array para capturar los nombres de las columnas
    $nom_cols = "";										//String de captura de los mombres de las columnas de manera lineal
    $csv = "";											//Variable para generar csv
    $shtml = "";										//Variable de armado de html
    $separador = ",";									//Variable para recibir el separador
    $lineas = "";										//Variable para obtener las lineas del archivo txt
    $txtName = $nombreI.".txt";
    $sfile1 = '../documentos/generador_informes/txt/'.$txtName;

    $NovedadEsta = $ws->ObtenerNovedades_Establecimiento($param);
    foreach ($NovedadEsta as $novedadE){
        foreach ($novedadE as $nEstablecimiento) {
          foreach ($nEstablecimiento as $NovEsta) {
            foreach ($NovEsta as $CON) {
              var_dump($CON);
              echo "<br/>";
              echo "<br/>";
                $ident = $NovEsta->Identificacion_Padre;
                $ident = substr($ident,0,-2);
                $ident = "'$ident'";
                $BuscaEstablecimiento = "SELECT e.id_unico FROM gc_establecimiento e.
                                        LEFT JOIN gc_contribuyente c ON e.contribuyente = c.id_unico
                                        LEFT JOIN gf_tercero t ON c.tercero = t.id_unico  WHERE t.numeroidentificacion = '$ident'  ";

                $resultEsta = $mysqli->query($BuscaEstablecimiento);
                $nresE = mysqli_num_rows($resultEsta);

                if($nresE < 1){
                  $Num_Mat1        = $CON->Matricula_establecimiento;
                  $Num_ident1      = $CON->Identificacion_Padre;
                  $espacio        = "           ";
                  #echo "matricula: ".$Num_Mat1."  ident: ".$Num_ident1;
                  $lineas1 .= $Num_Mat1.$espacio.$Num_ident1."\r\n";
                }

            }
          }
        }
    }

    $fp1=fopen($sfile1,"w" ); 				//Abrimos el archivo en modo de escritura
    fwrite($fp1,$lineas1); 					//Escribimos el html del archivo
    fclose($fp1); 							//Cerramos el archivo


    if(file_exists($sfile)){
      echo  "1;$sfile;$sfile1";					//Imprimimos verdaderos y el archivo
    }



  $Conceptos = $ws->ObtenerConceptos($param);

  foreach ($Conceptos as $concep){
    foreach ($concep as $valor) {
      foreach ($valor as $conce) {
        foreach ($conce as $CON) {

            var_dump($CON);
            echo "<br/>";
            echo "<br/>";
        }
      }
    }
  }
?>
