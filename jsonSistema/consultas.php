<?php
@session_start();
require_once('../Conexion/conexion.php');
require_once('../Conexion/ConexionPDO.php');
require_once('../jsonPptal/funcionesPptal.php');

$case = $_REQUEST['case'];
$con = new ConexionPDO();

switch ($case) {
    case 1:
        ###LISTADO MESES DEL AÑO##    
        $compania = $_SESSION['compania'];
        $anno     = $_SESSION['anno'];
        $anno = $_SESSION['anno'];
        $idpa = $_POST['param'];
        $ms = "SELECT id_unico, mes FROM gf_mes WHERE parametrizacionanno='$idpa' ORDER BY numero ASC";
        $ms = $mysqli->query($ms);
        if (mysqli_num_rows($ms) > 0) {
            while ($row = mysqli_fetch_row($ms)) {
                echo "<option value='$row[0]'>" . ucwords(mb_strtolower($row[1])) . "</option>";
            }
        } else {
            echo 0;
        }
        break;
    case 2:
        $compania = $_SESSION['compania'];
        $anno     = $_SESSION['anno'];
        ###ROMPER SESIONES##    
        ###BUSCAR SESIONES INICIO###
        $anio = $_SESSION['anno'];
        $compania = $_SESSION['compania'];
        $user = $_SESSION['usuario'];
        $userT = $_SESSION['usuario_tercero'];
        $userid = $_SESSION['id_usuario'];
        $tipo_c = $_SESSION['tipo_compania'];
        $n_tercero = $_SESSION['num_usuario'];
        ##ROMPER SESIONES#
        session_unset();
        ###ESTABLECER SESIONES DE INICIO DE NUEVO###
        $_SESSION['anno'] = $anio;
        $_SESSION['compania'] = $compania;
        $_SESSION['usuario'] = $user;
        $_SESSION['usuario_tercero'] = $userT;
        $_SESSION['id_usuario'] = $userid;
        $_SESSION['tipo_compania'] = $tipo_c;
        $_SESSION['num_usuario'] = $n_tercero;
        echo json_decode(1);
        break;
    case 3:
        $compania = $_SESSION['compania'];
        $anno     = $_SESSION['anno'];
        $anno = $_SESSION['anno'];
        $fc   = $_POST['fecha'];
        ####VERIFICAR SI LA FECHA  YA ESTA CERRADA###
        ##DIVIDIR FECHA
        $fecha_div = explode("-", $fc);
        $anio = $fecha_div[0];
        $mes = $fecha_div[1];
        $dia = $fecha_div[2];

        ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
        $ci = "SELECT
        cp.id_unico
        FROM
        gs_cierre_periodo cp
        LEFT JOIN
        gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
        LEFT JOIN
        gf_mes m ON cp.mes = m.id_unico
        WHERE
        pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2 
        AND cp.anno =$anno AND pa.compania = $compania ";
        $ci = $mysqli->query($ci);
        if (mysqli_num_rows($ci) > 0) {
            $result = 1;
        } else {
            $result = 2;
        }
        echo json_decode($result);
        break;
    //Validar Cierre
    case 4:
        $compania = $_SESSION['compania'];
        $anno     = $_SESSION['anno'];
        $anno = $_SESSION['anno'];
        ####VERIFICAR SI LA FECHA  YA ESTA CERRADA CHANGE DATAPICKER###
        $fc = $_POST['fecha'];
        ##DIVIDIR FECHA
        $fecha_div = explode("/", $fc);
        $dia = $fecha_div[0];
        $mes = $fecha_div[1];
        $anio = $fecha_div[2];

        ##BUSCAR SI EXISTE CIERRE PARA ESTA FECHA
        $ci = "SELECT cp.id_unico
        FROM gs_cierre_periodo cp
        LEFT JOIN gf_parametrizacion_anno pa ON pa.id_unico = cp.anno
        LEFT JOIN gf_mes m ON cp.mes = m.id_unico
        WHERE pa.anno = '$anio' AND m.numero = '$mes' AND cp.estado =2 
        AND pa.compania = $compania";
        $ci = $mysqli->query($ci);
        if (mysqli_num_rows($ci) > 0) {
            $result = 1;
        } else {
            $result = 2;
        }
        echo json_decode($result);
        break;
    ####cargar año index####
    case 5:
        $ter = $_POST['tercero'];
        $annio = "SELECT id_unico, anno 
                FROM gf_parametrizacion_anno 
                WHERE compania = $ter ORDER BY anno DESC ";
        $annio = $mysqli->query($annio);
        while ($row1 = mysqli_fetch_row($annio)) {
            echo "<option value='$row1[0]'>$row1[1]</option>";
        }
        break;
    #** Cargar Companias Index ***#
    case 6:
        
        $ter = $_REQUEST['tercero'];
        $sql = "SELECT DISTINCT t.id_unico, 
            IF(CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) IS NULL 
            OR CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos) = '', 
            (t.razonsocial), 
            CONCAT_WS(' ', t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos)) AS NOMBRE, 
            IF(t.digitoverficacion IS NULL OR t.digitoverficacion='', t.numeroidentificacion, 
            CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) 
            FROM  gs_usuario u 
            LEFT JOIN gf_tercero tr ON u.tercero = tr.id_unico 
            LEFT JOIN gf_tercero t ON tr.compania = t.id_unico 
            WHERE CONCAT_WS(' - ',  
            IF(CONCAT_WS(' ',
             tr.nombreuno,
             tr.nombredos,
             tr.apellidouno,
             tr.apellidodos) 
             IS NULL OR CONCAT_WS(' ',
             tr.nombreuno,
             tr.nombredos,
             tr.apellidouno,
             tr.apellidodos) = '',
             (tr.razonsocial),
             CONCAT_WS(' ',
             tr.nombreuno,
             tr.nombredos,
             tr.apellidouno,
             tr.apellidodos)), 
            IF(tr.digitoverficacion IS NULL OR tr.digitoverficacion='',
                 tr.numeroidentificacion, 
            CONCAT(tr.numeroidentificacion, ' - ', tr.digitoverficacion)) )  
            LIKE '%$ter%'";
        $sql = $mysqli->query($sql);
        while ($row1 = mysqli_fetch_row($sql)) {
            echo '<option value="' . $row1[0] . '">' . ucwords(mb_strtolower($row1[1])) . ' - ' . $row1[2] . '</option>';
        }
        break;
    case 7:
        $texto = $_GET['term'];
        $sql = "SELECT DISTINCT CONCAT_WS(' - ',  
        IF(CONCAT_WS(' ',
         t.nombreuno,
         t.nombredos,
         t.apellidouno,
         t.apellidodos) 
         IS NULL OR CONCAT_WS(' ',
         t.nombreuno,
         t.nombredos,
         t.apellidouno,
         t.apellidodos) = '',
         (t.razonsocial),
         CONCAT_WS(' ',
         t.nombreuno,
         t.nombredos,
         t.apellidouno,
         t.apellidodos)), 
        IF(t.digitoverficacion IS NULL OR t.digitoverficacion='',
             t.numeroidentificacion, 
        CONCAT(t.numeroidentificacion, ' - ', t.digitoverficacion)) ) 
         FROM gs_usuario u 
         LEFT JOIN gf_tercero t ON t.id_unico = u.tercero 
        WHERE CONCAT_WS(' ',t.nombreuno,t.nombredos,t.apellidouno,t.apellidodos) LIKE '%$texto%' 
        OR  t.razonsocial LIKE '%$texto%' 
        OR t.numeroidentificacion LIKE '%$texto%' ";
        $result = $mysqli->query($sql);
        if ($result->num_rows > 0) {
            while ($fila = mysqli_fetch_row($result)) {
                $terceros[] = ucwords(mb_strtolower($fila[0]));
            }
            echo json_encode($terceros);
        }
    break;
    #* Registrar Fecha Contrato
    case 8:
        $compania = $_SESSION['compania'];
        $anno     = $_SESSION['anno'];
        $fecha = fechaC($_REQUEST['fecha']);
        $insert = "UPDATE gf_parametrizacion_anno pa LEFT JOIN gf_tercero t ON pa.compania = t.id_unico SET t.fecha_contrato = '$fecha'";
        $insert = $mysqli->query($insert);
        $fecha_div = explode("-", $fecha);
        $anio = $fecha_div[0];
        $mes = intval(date('m'));
        if($mes>6){
            $version = $anio.'-02';
        } else {
            $version = $anio.'-01';
        }

        if($insert==true || $insert==1){
            #Buscar parametro versión
            $vr = $con->Listar("SELECT * FROM gs_parametros_basicos_sistema WHERE nombre = 'version'");
            if(count($vr)>0){
                #Actualiza parametro
                $insert = "UPDATE gs_parametros_basicos_sistema SET valor = '$version' WHERE nombre = 'version'";
                $insert = $mysqli->query($insert);
            } ELSE {
                #Crearlo
                $insert = "INSERT INTO gs_parametros_basicos_sistema (nombre, valor, compania) VALUES ('version', '$version', 1)";
                $insert = $mysqli->query($insert);
            }
            $result=1;
        }else {
            $result=2;
        }
        echo $result;
    break;
    
    case 9:
        // Consultar Fecha Contrato
        $compania = $_SESSION['compania'];
        $anno     = $_SESSION['anno'];
        $rta = 0;
        $cfc  = $con->Listar("SELECT fecha_contrato, DATE_FORMAT(fecha_contrato, '%d/%m/%Y') FROM gf_tercero WHERE id_unico = ".$_SESSION['compania']);
        if(!empty($cfc)>0){
            if($cfc[0][0]< date('Y-m-d')){
                $rta = $cfc[0][1];
            }
        }
        echo $rta; 
    break;
    #Comprobantes Sin Fechas
    case 10:
        $compania = $_SESSION['compania'];
        $anno     = $_SESSION['anno'];
        $rta  = 0;
        $html = 'Por favor revisar la fecha de los siguientes comprobantes:<br/>';
        if($_REQUEST['tipo']==1){
            $row = $con->Listar("SELECT DISTINCT tc.codigo, tc.nombre, cp.numero  FROM gf_comprobante_pptal cp 
                LEFT JOIN gf_tipo_comprobante_pptal tc ON cp.tipocomprobante = tc.id_unico 
                WHERE cp.parametrizacionanno = $anno AND (cp.fecha = '0000-00-00' OR cp.fecha IS NULL)");
        } elseif($_REQUEST['tipo']==2){
            $row = $con->Listar("SELECT DISTINCT tc.sigla,tc.nombre, cp.numero  FROM gf_comprobante_cnt cp 
                LEFT JOIN gf_tipo_comprobante tc ON cp.tipocomprobante = tc.id_unico 
                WHERE cp.parametrizacionanno = $anno AND (cp.fecha = '0000-00-00' OR cp.fecha IS NULL)");
        }
        for ($i=0; $i < count($row); $i++) { 
            $html .=$row[$i][0].' '.$row[$i][1].' - '.$row[$i][2].'<br/>';
            $rta  +=1;
        }
        $datos = array("rta"=>$rta,"html"=>$html);
        echo json_encode($datos);

    break;

    #Llenar tercero 
    case 11:
        $numero_ident = $_REQUEST['identificacion'];

        $sql = "SELECT numeroidentificacion,nombreuno,nombredos,apellidouno,apellidodos,c.nombre,
                       ti.id_unico,ti.nombre,ti.sigla,ter.razonsocial
                FROM gf_tercero ter
                LEFT JOIN gf_tipo_identificacion ti ON ter.tipoidentificacion=ti.id_unico
                LEFT JOIN gf_cargo_tercero ct ON ct.tercero=ter.id_unico
                LEFT JOIN gf_cargo c ON c.id_unico=ct.cargo
                WHERE ter.numeroidentificacion='$numero_ident'";
        $rs = $mysqli->query($sql);
        if(mysqli_num_rows($rs)>0){
            $resTer = mysqli_fetch_row($rs);
            $numeroidentificacion = $resTer[0];
            if ($numeroidentificacion==NULL) {
                $numeroidentificacion="";
            }
            $nombreuno   = $resTer[1];
            if ($nombreuno==NULL) {
                $nombreuno="";
            }
            $nombredos   = $resTer[2];
            if ($nombredos==NULL) {
                $nombredos="";
            }
            $apellidouno = $resTer[3];
            if ($apellidouno==NULL) {
                $apellidouno="";
            }
            $apellidodos = $resTer[4];
            if ($apellidodos==NULL) {
                $apellidodos="";
            }
            $cargo       = $resTer[5];
            if ($cargo==NULL) {
                $cargo="";
            }
            $optionIden='<option selected value="'.$resTer[6].'">'.ucwords(mb_strtolower($resTer[7])).'-'.$resTer[8].'</option>';
            $razonsocial = $resTer[9];
            if ($razonsocial==NULL) {
                $razonsocial="";
            }
            $resultado =1;
        } else {
            $resultado =2;
            $numeroidentificacion="";
            $nombreuno="";
            $nombredos="";
            $apellidouno="";
            $apellidodos="";
            $cargo="";
            $optionIden="";
            $razonsocial="";
        }

        $datos = array("respuesta"=>$resultado,"numeroidentificacion"=>$numeroidentificacion,
        "nombreuno"=>$nombreuno,"nombredos"=>$nombredos,"apellidouno"=>$apellidouno,
        "apellidodos"=>$apellidodos,"cargo"=>$cargo,"option"=>$optionIden,"razonsocial"=>$razonsocial);

        echo json_encode($datos); 
    break;   
    case 12:
        $ms = "SELECT id_unico, nombre,sigla FROM gf_tipo_identificacion 
               ORDER BY Nombre ASC";
        $ms = $mysqli->query($ms);
        if (mysqli_num_rows($ms) > 0) {
            while ($fila3 = mysqli_fetch_row($ms)) {
                echo '<option value="'.$fila3[0].'">'.ucwords(mb_strtolower($fila3[1])).'-'.$fila3[2].'</option>';
            }
        } else {
            echo 0;
        }
    break; 

}