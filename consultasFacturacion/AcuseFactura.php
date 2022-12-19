<?php
require_once '../Conexion/conexion.php';
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
@session_start();

$tipoEventoF             = $_REQUEST['tipoEvento'];

if ($tipoEventoF==1) {
    $id_factura = $_REQUEST['id_fac'];
    $tipoIdentificacionR    = $_REQUEST['sltTipoId'];
    $numero_identificacionR = $_REQUEST['txtNumeroI'];
    $nombre1R   = $_REQUEST['txtPrimerN'];
    $nombre2R   = $_REQUEST['txtSegundoN'];
    $apellido1R = $_REQUEST['txtPrimerA'];
    $apellido2R = $_REQUEST['txtSegundoA'];
    $cargoR     = $_REQUEST['txtCargo'];
    $areaR     = $_REQUEST['txtArea'];
    $razonsocialEv     = $_REQUEST['txtRazonSocial'];
}else{
    $id_factura = $_REQUEST['id_facR'];
    $tipoIdentificacionR    = $_REQUEST['sltTipoIdR'];
    $numero_identificacionR = $_REQUEST['txtNumeroIR'];
    $nombre1R   = $_REQUEST['txtPrimerNR'];
    $nombre2R   = $_REQUEST['txtSegundoNR'];
    $apellido1R = $_REQUEST['txtPrimerAR'];
    $apellido2R = $_REQUEST['txtSegundoAR'];
    $cargoR     = $_REQUEST['txtCargoR'];
    $areaR     = $_REQUEST['txtAreaR'];
    $razonsocialEv = $_REQUEST['txtRazonSocialR'];
}




$compania  = $_SESSION['compania'];

$query      = "SELECT numeroidentificacion FROM gf_tercero where id_unico = $compania ";
$resul      = $mysqli->query($query);
$roww       = $resul->fetch_assoc();
$companiaa  = $roww['numeroidentificacion'];
$nit        = $companiaa;

$Mensaje    = '';


//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
//token
$sqlTkn = "SELECT token_fe FROM `gf_tercero` where id_unico='" . $compania . "'";
$resultTkn = $mysqli->query($sqlTkn);
$rowTkn = $resultTkn->fetch_assoc();

$token = $rowTkn["token_fe"];
if ($token == null) {
    Token($compania, $mysqli);
}

// CONSULTAR PARÁMETRO PARA OBSERVACIONES
$sqlp      = "SELECT id_unico, valor FROM `gs_parametros_basicos` where indicador = 'FE000002'";
$resultP   = $mysqli->query($sqlp);
$rowPr     = $resultP->fetch_assoc();
$notaFE    = '';
if(!empty($rowPr['valor'])){
   $notaFE = $rowPr['valor'];    
}

//CONSULTAS valores de factura
$sqlFv = "SELECT f.prefijo_factura as prefijo, f.numero_factura  as numero_factura,f.cufe_factura as cufe, f.fecha_generacion as fecha, 
          f.hora_generacion as hora,f.numero_autorizacion as numero_resolucion,f.fecha_generacion as fecha_factura,
          f.fecha_vencimiento as fecha_vencimiento,f.fecha_inicio_autorizacion as inicioAutorizacion, f.fecha_fin_autorizacion as finAutorizacion,
          f.rango_inicio_autorizacion as rangoInicioAutorizacion, f.rango_fin_autorizacion as rangoFinAutorizacion,ter.nombreuno as vendedor1,
          ter.nombredos as vendedor2, ter.apellidouno as vendedor3, ter.apellidodos as vendedor4, ter.razonsocial as razonvendedor,
          ter.numeroidentificacion as numeroIdvendedor,tiv.codigo_fe as tipodocVendedor,ter.id_unico as idVendedor,ter.email as emailVendedor,
          f.hora_generacion as horaG,f.hora_vencimiento as horaV
FROM gf_factura_compra f
LEFT JOIN gf_tercero ter on ter.id_unico = f.emisor_factura
LEFT JOIN gf_tipo_identificacion tiv on tiv.id_unico = ter.tipoidentificacion
WHERE f.id_unico = '".$id_factura."' LIMIT 1";

$resultFv   = $mysqli->query($sqlFv);
$rowFv      = $resultFv->fetch_assoc();
if ($tipoEventoF==1) {
    $tipoEvento="ACUSE_RECIBO_FACTURA_ELECTRONICA";
    $observacionP="Acuse Factura ".$rowFv['prefijo'].$rowFv['numero_factura'];
}else{
    $tipoEvento="RECIBO_BIEN_SERVICIO";
    $observacionP="Recibo factura ".$rowFv['prefijo'].$rowFv['numero_factura'];
}


$cufe                     =$rowFv['cufe'];
$numeroDocumentoComercial =$rowFv['prefijo'].$rowFv['numero_factura'];
$fechaGenera              =$rowFv['fecha_factura'];
$fechaVenci               =$rowFv['fecha_vencimiento'];
$fechaGeneracion         = $fechaGenera.' '.$rowFv['horaG'];
$fechaVencimiento        = $fechaVenci.' '.$rowFv['horaV'];
$numeroResolucion         = $rowFv['numero_resolucion'];
$inicioAutorizacion       = $rowFv['inicioAutorizacion'];
$finAutorizacion          = $rowFv['finAutorizacion'];
$prefijoAutorizacion      = $rowFv['prefijo'];
$rangoInicioAutorizacion  = $rowFv['rangoInicioAutorizacion'];
$rangoFinAutorizacion     = $rowFv['rangoFinAutorizacion'];
if ($rowFv['tipodocVendedor'] == "NI") {
    $Naturaleza = "JURIDICAS";
} else {
    $Naturaleza = "NATURALES";
}
$id_vendedor=$rowFv['idVendedor'];

$sqlTribu     = "SELECT  GROUP_CONCAT(gf_responsabilidad_tributaria.codigo) FROM `gf_tercero_responsabilidad` 
INNER join gf_responsabilidad_tributaria on gf_tercero_responsabilidad.responsabilidad_tributaria = gf_responsabilidad_tributaria.id_unico 
WHERE tercero = '".$id_vendedor."'"; 
$resulTribu      = $mysqli->query($sqlTribu);
while ($rowwTribu = mysqli_fetch_row($resulTribu)) {
    $responsabilidadesTributarias  = $responsabilidadesTribut = $rowwTribu[0];
}
$primerApellidoEmisor=$rowFv['vendedor3'];
if ($primerApellidoEmisor==NULL) {
    $primerApellidoEmisor="";
}
$segundoApellidoEmisor=$rowFv['vendedor4'];
if ($segundoApellidoEmisor==NULL) {
    $segundoApellidoEmisor="";
}
$primerNombreEmisor=$rowFv['vendedor1'];
if ($primerNombreEmisor==NULL) {
    $primerNombreEmisor="";
}
$otrosNombresEmisor=$rowFv['vendedor2'];
if ($otrosNombresEmisor==NULL) {
    $otrosNombresEmisor="";
}
$razonSocialEmisor=$rowFv['razonvendedor'];
if ($razonSocialEmisor==NULL) {
    $razonSocialEmisor="";
}
$numeroDocumentoEmisor=$rowFv['numeroIdvendedor'];

$tipoDocumentoEmisor=$rowFv['tipodocVendedor'];
$emailVendedor=$rowFv['emailVendedor'];


$sqlTipoDocR = "SELECT codigo_fe FROM gf_tipo_identificacion 
                WHERE id_unico=$tipoIdentificacionR";
$resultTipoDocR = $mysqli->query($sqlTipoDocR);
$rowTipoDocR = $resultTipoDocR->fetch_assoc();
$tipoDocumentoRecep = $rowTipoDocR["codigo_fe"];

//Validamos el receptor
if ($nombre1R=="-" && $nombre2R=="-"  && $apellido1R=="-" && $apellido2R=="-" && $razonsocialEv!="") {
    $nombre1R=$razonsocialEv;
    $nombre2R="";
    $apellido1R="";
    $apellido2R="";
}

//Estructura Json
$evento = [
     //"nit"                           => $nit,
    "nit"                           => "40008490",
    "tipoEvento"                    => $tipoEvento,
    "conceptoRechazo"               => "",
    "observacion"                   => $observacionP,
    "factura" => [
        "cufe"                      => $cufe,
        "numeroDocumentoComercial"  => $numeroDocumentoComercial,
        "fechaGeneracion"           => $fechaGeneracion,
        "fechaVencimiento"          => $fechaVencimiento,
            "datosResolucion" => [
                "numeroAutorizacion"            => $numeroResolucion,
                "inicioAutorizacion"            => $inicioAutorizacion,
                "finAutorizacion"               => $finAutorizacion,
                "prefijoAutorizacion"           => $prefijoAutorizacion,
                "rangoInicioAutorizacion"       => $rangoInicioAutorizacion,
                "rangoFinAutorizacion"          => $rangoFinAutorizacion
            ],
            "emisorFactura" => [
                "primerApellido"            => $primerApellidoEmisor,
                "segundoApellido"           => $segundoApellidoEmisor,
                "primerNombre"              => $primerNombreEmisor,
                "otrosNombres"              => $otrosNombresEmisor,
                "razonSocial"               => $razonSocialEmisor,
                "numeroDocumento"           => $numeroDocumentoEmisor,
                "tipoDocumento"             => $tipoDocumentoEmisor,
                "naturaleza"                => $Naturaleza,
                "responTributaria"          => $responsabilidadesTributarias,
                "email"                     => $emailVendedor
            ]
    ],
    "receptorFacturaBienes" => [
        "tipoDocumento"            => $tipoDocumentoRecep,
        "numeroDocumento"          => $numero_identificacionR,
        "primerApellido"           => $apellido1R,
        "segundoApellido"          => $apellido2R,
        "primerNombre"             => $nombre1R,
        "otrosNombres"             => $nombre2R,
        "cargo"                    => $cargoR,
        "area"                     => $areaR
    ]
];


$datosCodificados = json_encode($evento);
 //var_dump($datosCodificados);
if ($tipoEventoF==1) {
    $Respuesta = eventoAc($datosCodificados, $token, $mysqli, $id_factura);
}else{
    $Respuesta = eventoRe($datosCodificados, $token, $mysqli, $id_factura);
}

$Mensaje = $Respuesta;


//_____________________________________________________________________________________________________________________________________________________
//FUNCIONES

function Token($compania, $mysqli) {

$qury      = "SELECT usuario_fe,contrasena_fe FROM `gf_tercero` where id_unico='".$compania."' ";
$resl      = $mysqli->query($qury);
$rowwu       = $resl->fetch_assoc();
$usu  = $rowwu['usuario_fe'];
$contra  = $rowwu['contrasena_fe'];

    //LOGUIN - SACAR TOKEN
    //parametros :

    #Credenciales Para pruebas.
     //$usuario = 'demo_api';
     //$contrasenia = '12345678';
    #Credenciales Produccion
    $usuario = $usu;
    $contrasenia = $contra;
    #URL Pruebas
    //$url='https://central.clarisacloud.com:8443/seguridad/rest/api/v1/login/';
    #URL produccion
    $url = 'https://csi.clarisa.co:8443/seguridad/rest/api/v1/login/';

    //JSON
    $data = array(
        'usuario' => $usuario,
        'contrasenia' => $contrasenia
    );

    //configuraciones del json
    $options = array(
        'http' => array(
            'header' => "Content-Type: application/json",
            'method' => 'POST',
            'content' => json_encode($data)
        )
    );

    //engine:
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { 
    }

    $resultData = json_decode($result, TRUE);
    $re = $resultData["data"];
    $token = $re["token"];

    $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
    $resultTkn = $mysqli->query($sqlEdiTknn);
    if ($resultTkn == false) {
        echo "ERROR";
    } else {
        
    }

    return $token;
}

function eventoAc($datosCodificados, $token, $mysqli, $id_factura) {


    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt_array($curl, [
        CURLOPT_PORT => "8443",
        CURLOPT_URL => "https://csi.clarisa.co:8443/contabilidad/rest/api/v1/generar/evento",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $datosCodificados,
        CURLOPT_COOKIE => "JSESSIONID=T-BnFgT-56AicuIZjUdJwv5k6ZwZBOi_UAu0olV7.ip-172-31-9-34",
        CURLOPT_HTTPHEADER => [
            "Authorization: '".$token."'",
            "Content-Type: application/json"
        ],
    ]);

     $response = curl_exec($curl);
     $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
         $Mensaje = $err;
    } else {
         $solu = json_decode($response, true);

        if(is_null($solu)){
            $Mensaje = '';
        }else{

        $soluc2 = $solu['textResponse'];

        if ($soluc2 == "Evento a factura generado exitosamente") {

            $soluc3 = $solu['data'];
            $solu4 = $soluc3['cude'];
            $solu5="Evento acuse factura generado exitosamente";
            $sqledit = "UPDATE gf_factura_compra SET acuse_factura = '$solu5', cude_acuse_factura='" .$solu4. "' WHERE id_unico = '" . $id_factura . "'";
            if ($mysqli->query($sqledit) != true) {
                echo "Error";
            }
            $Mensaje = $solu5;
        } else {
            
            $error1 = $solu['errores'];
            $error2 = $error1['errores'];
            $error3 = $error2[0];
            $Error5 = $error3['codError'];
            $Error4 = $error3['errorMessage'];
            $Mensaje = $soluc2 . " " . $Error4 . " " . $Error5;
        }
    }
}
    return $Mensaje;
}


function eventoRe($datosCodificados, $token, $mysqli, $id_factura) {


    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt_array($curl, [
        CURLOPT_PORT => "8443",
        CURLOPT_URL => "https://csi.clarisa.co:8443/contabilidad/rest/api/v1/generar/evento",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $datosCodificados,
        CURLOPT_COOKIE => "JSESSIONID=T-BnFgT-56AicuIZjUdJwv5k6ZwZBOi_UAu0olV7.ip-172-31-9-34",
        CURLOPT_HTTPHEADER => [
            "Authorization: '".$token."'",
            "Content-Type: application/json"
        ],
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
        //echo "cURL Error #:" . $err;
         $Mensaje = $err;
    } else {
         $solu = json_decode($response, true);

        if(is_null($solu)){
            $Mensaje = '';
        }else{

        $soluc2 = $solu['textResponse'];

        if ($soluc2 == "Evento a factura generado exitosamente") {

            $soluc3 = $solu['data'];
            $solu4 = $soluc3['cude'];
            $solu5="Evento recibo factura generado exitosamente";
            $sqledit = "UPDATE gp_factura SET recibo_factura = '$solu5', cude_recibo_factura='" .$solu4. "' WHERE id_unico = '" . $id_factura . "'";
            if ($mysqli->query($sqledit) != true) {
                echo "Error";
            }
            $Mensaje = $solu5;
        } else {
            
            $error1 = $solu['errores'];
            $error2 = $error1['errores'];
            $error3 = $error2[0];
            $Error5 = $error3['codError'];
            $Error4 = $error3['errorMessage'];
            $Mensaje = $soluc2 . " " . $Error4 . " " . $Error5;
        }
    }
}
    return $Mensaje;
}







if ($Mensaje == '') {
    $token = Token($compania, $mysqli);

    $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
    $resultTkn = $mysqli->query($sqlEdiTknn);
    if ($tipoEventoF==1) {
        $Respuestaa = eventoAc($datosCodificados, $token, $mysqli, $id_factura);
    }else{
        $Respuestaa = eventoRe($datosCodificados, $token, $mysqli, $id_factura);
    }
    $Mensaje = $Respuestaa;
}

if ($Mensaje == "Error en credenciales de Usuario Su sesión ha expirado, por favor vuelva a iniciar sesión US05") {
    $token = Token($compania, $mysqli);

    $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
    $resultTkn = $mysqli->query($sqlEdiTknn);
    if ($tipoEventoF==1) {
        $Respuestaa = eventoAc($datosCodificados, $token, $mysqli, $id_factura);
    }else{
        $Respuestaa = eventoRe($datosCodificados, $token, $mysqli, $id_factura);
    }
    $Mensaje = $Respuestaa;
}

if($Mensaje == "Error en credenciales de Usuario No se ha enviado correctamente el token de acceso US03") {
    $token = Token($compania, $mysqli);

    $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
    $resultTkn = $mysqli->query($sqlEdiTknn);

    if ($tipoEventoF==1) {
        $Respuestaa = eventoAc($datosCodificados, $token, $mysqli, $id_factura);
    }else{
        $Respuestaa = eventoRe($datosCodificados, $token, $mysqli, $id_factura);
    }
    $Mensaje = $Respuestaa;
}
?>



<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
        <script src="../js/md5.pack.js"></script>
        <script src="../js/jquery.min.js"></script>
        <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
        <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>
    </head>
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p> <?=$Mensaje; ?> </p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div> 
    </div>
</div>

<link rel="stylesheet" href="../css/bootstrap-theme.min.css">
<script src="../js/bootstrap.min.js"></script>
<script type="text/javascript">
    $("#myModal1").modal('show');
    $("#ver1").click(function () {
        $("#myModal1").modal('hide');
        window.location = '../EventosFacturaRADIAN.php';
    });
</script>
