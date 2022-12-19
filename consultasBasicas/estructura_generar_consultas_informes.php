<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//MODIFICACIONES
//27/07/2017 | Erica González | Poner variables de session para la fecha Inicial y final para encabezados
//19/07/2017 | Erica González |  Fechas Trimestre acumulado de ejecuciones
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// // Fecha Creación : 26/04/2017
// Creado por     : Alexander Numpaque
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Inicializamos session
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
session_start();
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Archivos abjuntos
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
require ('../Conexion/conexion.php');
require ('../funciones/funciones_consulta.php');
$panno = $_SESSION['anno'];
ini_set('max_execution_time', 0);
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Capturamos la variable enviada por el post
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$session = $_POST['session'];
$_SESSION['fechaI']="";
$_SESSION['fechaF']="";
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Switch case para definir a que proceso se redirige
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
switch ($session) {
  case 1:
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Validamos que la variable de informe no este vacia
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if(!empty($_POST['informe'])) {
      $id_report = $_POST['informe'];     //Capturamos el id del informe
      $sql = "SELECT periodicidad FROM gn_tabla_homologable WHERE informe = $id_report"; //Consultamos la periodicidad del informe
      $result = $mysqli->query($sql);
      $row = mysqli_fetch_row($result);
      echo $row[0];   //Imprimimos el valor retornado por la consulta
    }
    break;
  case 2:
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cuando la periodicidad es anual,validamos que la variable de informe no este vacia
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if(!empty($_POST['informe'])){
      $informe = $_POST['informe'];                                       //Capturamos el valor del informe enviado por post
      $anno = $_POST['anno'];                                             //Capturamos el anno seleccionado
      $sqlClase = "SELECT clase_informe FROM gn_informe WHERE id = $informe"; //Consulta para obtener la clase del informe
      $resultClase = $mysqli->query($sqlClase);
      $clase = mysqli_fetch_row($resultClase);
      $fechaInicial = "$anno-01-01";                                      //Fecha Inicial
      $fechaFinal = "$anno-12-31";                                        //Fecha Final
      $_SESSION['fechaI']=$fechaInicial;
      $_SESSION['fechaF']=$fechaFinal;
      
      switch ($clase[0]) {
        case '1':          
          $i = start_process_execution_I($fechaInicial,$fechaFinal,$i=0);
          echo $i;
          break;
        case '2':          
          $g = start_process_execution_G($fechaInicial,$fechaFinal,$i=0);
          echo $g;
          break;
        case '3':
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Consultamos el codigo de la primer cuenta
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $sqlCI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta ASC LIMIT 0,1";
          $resultCI = $mysqli->query($sqlCI);
          $rowCI = mysqli_fetch_row($resultCI); 
          $codigoI = $rowCI[0];
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Consultamos el codigo de la ultima cuenta
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $sqlFI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta DESC LIMIT 0,1";
          $resultCF = $mysqli->query($sqlFI);
          $rowFI = mysqli_fetch_row($resultCF);
          $codigoF = $rowFI[0];
          $b = generarBalance($anno, $fechaInicial,$fechaFinal, $codigoI,$codigoF,1);
          //$b = start_test_balance($codigoI,$codigoF,$fechaInicial,$fechaFinal,$i=0);
          echo $b;
        break;
        case '4':            
            $cg = start_process_execution_G_I($fechaInicial,$fechaFinal,$i=0);
            echo $cg;
            break;
        case '5':
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Consultamos el codigo de la primer cuenta
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $sqlCI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta ASC LIMIT 0,1";
          $resultCI = $mysqli->query($sqlCI);
          $rowCI = mysqli_fetch_row($resultCI);
          $codigoI = $rowCI[0];
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Consultamos el codigo de la ultima cuenta
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $sqlFI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta DESC LIMIT 0,1";
          $resultCF = $mysqli->query($sqlFI);
          $rowFI = mysqli_fetch_row($resultCF);
          $codigoF = $rowFI[0];
          $b = start_test_balancecng($codigoI,$codigoF,$fechaInicial,$fechaFinal,$i=0);
          echo $b;
        break;
        case '6':            
        $cg = start_process_execution_RCP($fechaInicial,$fechaFinal,$i=0);
        echo $cg;
        break;
        case '7':            
        $cg = start_process_execution_RCP($fechaInicial,$fechaFinal,$i=0);
        echo $cg;
        case '7':            
        $cg = start_process_execution_RCP($fechaInicial,$fechaFinal,$i=0);
        echo $cg;
        break;
        case '8':            
        $cg = nomina(); 
        echo $cg;
        break;
      }
    }
    break;
  case 3:
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cuando la periodicidad es mensual,validamos que la variable de informe no este vacia
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if(!empty($_POST['informe'])){
      $calendario = CAL_GREGORIAN;                                                    //Calendario gregoriano
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Capturamos las variables enviada
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $informe = $_POST['informe'];                                                   //Capturamos el valor del informe enviado por post
      $mesA1 = explode("/",$_POST['mesI']); $mesF1 = explode("/",$_POST['mesF']);     //Dividmos el valor envia el cual incluye el año de la parametrización
      $anno1 = $mesA1[1];$mesI = $mesA1[0];                                           //Capturamos el año y capturamos el mes incial
      $anno2 = $mesF1[1];$mesF = $mesF1[0];                                           //Capturamos el año y capturamos el mes final
      $dia = cal_days_in_month($calendario, $mesF, $anno2);                           //Ultimo dia del mes
      $fechaInicial = date('Y-m-d', mktime(0,0,0, $mesI, 1, $anno1));         //Creamos el formato de la fecha incial con el mes inicial
      $fechaFinal = "$anno2-$mesF-$dia";                                           //Creamos la fecha final
      $_SESSION['fechaI']=$fechaInicial;
      $_SESSION['fechaF']=$fechaFinal;
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Consultamos la clase del informe
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $sqlClase = "SELECT clase_informe FROM gn_informe WHERE id = $informe";         //Consulta para obtener la clase del informe
      $resultClase = $mysqli->query($sqlClase);
      $clase = mysqli_fetch_row($resultClase);
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // validamos la clase informe para realizar el proceso de llenado de las tablas
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      switch ($clase[0]) {
        case '1':          
          $i = start_process_execution_I($fechaInicial,$fechaFinal,$i=0);
          echo $i;
          break;
        case '2':          
          $g = start_process_execution_G($fechaInicial,$fechaFinal,$i=0);
          echo $g;
          break;
          case '4':            
            $cg = start_process_execution_G_I($fechaInicial,$fechaFinal,$i=0);
            echo $cg;
            break;
          case '3':
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Consultamos el codigo de la primer cuenta
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sqlCI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta ASC LIMIT 0,1";
            $resultCI = $mysqli->query($sqlCI);
            $rowCI = mysqli_fetch_row($resultCI);
            $codigoI = $rowCI[0];
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            // Consultamos el codigo de la ultima cuenta
            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $sqlFI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta DESC LIMIT 0,1";
            $resultCF = $mysqli->query($sqlFI);
            $rowFI = mysqli_fetch_row($resultCF);
            $codigoF = $rowFI[0];
            $b = start_test_balance($codigoI,$codigoF,$fechaInicial,$fechaFinal,$i=0);
            echo $b;
            break;
        case '5':
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Consultamos el codigo de la primer cuenta
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $sqlCI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta ASC LIMIT 0,1";
          $resultCI = $mysqli->query($sqlCI);
          $rowCI = mysqli_fetch_row($resultCI);
          $codigoI = $rowCI[0];
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Consultamos el codigo de la ultima cuenta
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $sqlFI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta DESC LIMIT 0,1";
          $resultCF = $mysqli->query($sqlFI);
          $rowFI = mysqli_fetch_row($resultCF);
          $codigoF = $rowFI[0];
          $b = start_test_balancecng($codigoI,$codigoF,$fechaInicial,$fechaFinal,$i=0);
          echo $b;
        break;
        case '6':            
        $cg = start_process_execution_RCP($fechaInicial,$fechaFinal,$i=0);
        echo $cg;
        break;
        case '7':            
        $cg = start_process_execution_RCP($fechaInicial,$fechaFinal,$i=0);
        echo $cg;
        break;
        }
    }
    break;
  case 4:
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Cuando la periodicidad es trimestral,validamos que la variable de informe no este vacia
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if(!empty($_POST['informe'])){
      $informe = $_POST['informe'];                                                   //Capturamos el valor del informe enviado por post
      $param = $_SESSION['anno'];                                                      //Capturamos la variable de session
      $sqlP = "SELECT anno FROM gf_parametrizacion_anno WHERE id_unico = $param";     //Consultamos el anno de la parametrizacionanno
      $resultP = $mysqli->query($sqlP);
      $rowP = mysqli_fetch_row($resultP);
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Validamos que trimestre recibe y generamos las fechas incial y final
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      switch ($_POST['trimestre']) {
        case '1':
          $fechaInicial = "$rowP[0]-01-01";
          $fechaFinal   = "$rowP[0]-03-31";
          $fechaInicialM = "$rowP[0]-01-01";
          break;
        case '2':
          $fechaInicial = "$rowP[0]-01-01";
          $fechaFinal   = "$rowP[0]-06-30";
          $fechaInicialM = "$rowP[0]-04-01";
          break;
        case '3':
          $fechaInicial = "$rowP[0]-01-01";
          $fechaFinal   = "$rowP[0]-09-30";
          $fechaInicialM = "$rowP[0]-07-01";
          break;
        case '4':
          $fechaInicial = "$rowP[0]-01-01";
          $fechaFinal   = "$rowP[0]-12-31";
          $fechaInicialM = "$rowP[0]-10-01";
          break;
          
      }
      $_SESSION['fechaI']=$fechaInicialM;
      $_SESSION['fechaF']=$fechaFinal;
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // Consultamos la clase del informe
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      $sqlClase = "SELECT clase_informe FROM gn_informe WHERE id = $informe";         //Consulta para obtener la clase del informe
      $resultClase = $mysqli->query($sqlClase);
      $clase = mysqli_fetch_row($resultClase);
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      // validamos la clase informe para realizar el proceso de llenado de las tablas
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      switch ($clase[0]) {
        case '1':          
          $i = start_process_execution_I($fechaInicial,$fechaFinal,$i=0);
          echo $i;
          break;
        case '2':          
          $g = start_process_execution_G($fechaInicial,$fechaFinal,$i=0);
          echo $g;
          break;
          case '4':            
            $cg = start_process_execution_G_I($fechaInicial,$fechaFinal,$i=0);
            echo $cg;
            break;
        case '6':            
        $cg = start_process_execution_RCP($fechaInicial,$fechaFinal,$i=0);
        echo $cg;
        break;
        case '7':            
        $cg = start_process_execution_RCP($fechaInicial,$fechaFinal,$i=0);
        echo $cg;
        break;
        case '3':
           switch ($_POST['trimestre']) {
            case '1':
              $fechaInicial = "$rowP[0]-01-01";
              $fechaFinal   = "$rowP[0]-03-31";
              break;
            case '2':
              $fechaInicial = "$rowP[0]-04-01";
              $fechaFinal   = "$rowP[0]-06-30";
              break;
            case '3':
              $fechaInicial = "$rowP[0]-07-01";
              $fechaFinal   = "$rowP[0]-09-30";
              break;
            case '4':
              $fechaInicial = "$rowP[0]-10-01";
              $fechaFinal   = "$rowP[0]-12-31";
              break;
          }
          $_SESSION['fechaI']=$fechaInicial;
          $_SESSION['fechaF']=$fechaFinal;
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Consultamos el codigo de la primer cuenta
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $sqlCI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta ASC LIMIT 0,1";
          $resultCI = $mysqli->query($sqlCI);
          $rowCI = mysqli_fetch_row($resultCI);
          $codigoI = $rowCI[0];
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Consultamos el codigo de la ultima cuenta
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $sqlFI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta DESC LIMIT 0,1";
          $resultCF = $mysqli->query($sqlFI);
          $rowFI = mysqli_fetch_row($resultCF);
          $codigoF = $rowFI[0];
          $b = start_test_balance($codigoI,$codigoF,$fechaInicial,$fechaFinal,$i=0);
          echo $b;
          break;
      case '5':
         switch ($_POST['trimestre']) {
            case '1':
              $fechaInicial = "$rowP[0]-01-01";
              $fechaFinal   = "$rowP[0]-03-31";
              break;
            case '2':
              $fechaInicial = "$rowP[0]-04-01";
              $fechaFinal   = "$rowP[0]-06-30";
              break;
            case '3':
              $fechaInicial = "$rowP[0]-07-01";
              $fechaFinal   = "$rowP[0]-09-30";
              break;
            case '4':
              $fechaInicial = "$rowP[0]-10-01";
              $fechaFinal   = "$rowP[0]-12-31";
              break;
          }
          $_SESSION['fechaI']=$fechaInicial;
          $_SESSION['fechaF']=$fechaFinal;
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Consultamos el codigo de la primer cuenta
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $sqlCI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta ASC LIMIT 0,1";
          $resultCI = $mysqli->query($sqlCI);
          $rowCI = mysqli_fetch_row($resultCI);
          $codigoI = $rowCI[0];
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // Consultamos el codigo de la ultima cuenta
          /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $sqlFI = "SELECT codi_cuenta  from gf_cuenta WHERE parametrizacionanno = $panno ORDER BY codi_cuenta DESC LIMIT 0,1";
          $resultCF = $mysqli->query($sqlFI);
          $rowFI = mysqli_fetch_row($resultCF);
          $codigoF = $rowFI[0];
          $b = start_test_balancecng($codigoI,$codigoF,$fechaInicial,$fechaFinal,$i=0);
          echo $b;
        break;
      }
    }
    break;
}
?>
