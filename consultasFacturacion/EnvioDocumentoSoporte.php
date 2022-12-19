<?php
require_once '../Conexion/conexion.php';
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
@session_start();
$id_documento = $_GET['id'];
$compania   = $_SESSION['compania'];

$query      = "SELECT numeroidentificacion FROM gf_tercero where id_unico = $compania ";
$resul      = $mysqli->query($query);
$roww       = $resul->fetch_assoc();
$companiaa  = $roww['numeroidentificacion'];
$nit       = $companiaa;
#Pruebas nit
//$nit       = '40008490';
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
//CONSULTAS valores de documento
$sqlDc = "SELECT tde.sigla as prefijo, de.fecha as fecha, de.fecha_vencimiento, de.descripcion as observaciones,
                de.fecha as fecha_generacion, de.fecha_vencimiento,tde.sigla as sigla, de.numero as numero,
                (CASE WHEN de.forma_pago=1 THEN 'CONTADO' ELSE 'CREDITO' END) as forma_pago,fp.codigo_dian as mediosPago,
                ti.codigo_fe as tipo_identificacion, ter.numeroidentificacion,
                IF(ter.razonsocial IS NULL OR ter.razonsocial ='', CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos), ter.razonsocial) AS Nombre_R,
                d.direccion,CONCAT(dep.rss, c.rss)as ciudad,ter.email,tel.valor as telefono, ter.id_unico, tr.codigo as regimenContable,tde.numero_resolucion,
                (SELECT round(SUM(dde.valor_total), 4) FROM gf_detalle_documento_equivalente dde WHERE de.id_unico = dde.documento_equivalente) as total, ter.procedencia
                FROM gf_documento_equivalente de
                LEFT JOIN gf_tipo_documento_equivalente tde ON tde.id_unico=de.tipo  
                LEFT JOIN gf_forma_pago fp ON fp.id_unico=de.metodo_pago
                LEFT JOIN gf_tercero ter ON ter.id_unico = de.tercero
                LEFT JOIN gf_tipo_identificacion ti ON ter.tipoidentificacion = ti.id_unico 
                LEFT JOIN gf_direccion d ON ter.id_unico=d.tercero 
                LEFT JOIN gf_ciudad c ON d.ciudad_direccion = c.id_unico 
                LEFT JOIN gf_departamento dep ON c.departamento = dep.id_unico 
                LEFT JOIN gf_telefono tel ON tel.tercero=ter.id_unico
                LEFT JOIN gf_tipo_regimen tr ON ter.tiporegimen = tr.id_unico
                WHERE de.id_unico ='".$id_documento."' LIMIT 1";

$resultDc   = $mysqli->query($sqlDc);
$rowDc      = $resultDc->fetch_assoc();

$num= trim($rowDc['numero'], " ");
$doc=$rowDc['sigla'].$num;
$numeroResolucion           = $rowDc['numero_resolucion'];
$numeroDocumentoComercial   = $doc;
$fechaGeneracion            = $rowDc['fecha'];
$fechaVencimiento           = $rowDc['fecha_vencimiento'];
$mediosPago                 = $rowDc['mediosPago'];
$observacion                = $rowDc['observaciones'];
$formaPago                  = $rowDc['forma_pago'];
$total                      = (int)$rowDc['total'];
$nombreRazonSocial          = $rowDc['Nombre_R'];
$tipoIdentificacion         = $rowDc['tipo_identificacion'];
$numIdentificacion          = $rowDc['numeroidentificacion'];

if ($rowFv['tipo_identificacion'] == "NI") {
    $Naturaleza = "JURIDICAS";
} else {
    $Naturaleza = "NATURALES";
}

$direccion              = $rowDc['direccion'];
$ciudad                 = $rowDc['ciudad'];
$telefono               = $rowDc['telefono'];
$email                  = $rowDc['email'];
$idTercero              = $rowDc['id_unico'];
$regimenContable        = $rowDc['regimenContable'];
$fechaGeneracio         = date('Y-m-d\ 00:i:s', strtotime($fechaGeneracion));
$fechaVencimient        = date('Y-m-d\ 00:i:s', strtotime($fechaVencimiento));
$id_Cliente             = $rowDc['id_unico'];  
$procedencia            = $rowDc['procedencia'];

if ($procedencia=="No residente") {
    $procedencia="NO_RESIDENTE";
}else{
    $procedencia="RESIDENTE";
}

$sqlFiscal     = "SELECT  GROUP_CONCAT(gf_responsabilidad_fiscal.codigo) FROM `gf_tercero_responsabilidad` 
INNER join gf_responsabilidad_fiscal on gf_tercero_responsabilidad.responsabilidad = gf_responsabilidad_fiscal.id_unico 
WHERE tercero = '".$id_Cliente."'"; 
$resulFiscal      = $mysqli->query($sqlFiscal);
while ($rowwFiscal = mysqli_fetch_row($resulFiscal)) {
    $responsabilidadesFiscales  = $responsabilidadesFiscals = $rowwFiscal[0];
}

$sqlTribu     = "SELECT  GROUP_CONCAT(gf_responsabilidad_tributaria.codigo) FROM `gf_tercero_responsabilidad` 
INNER join gf_responsabilidad_tributaria on gf_tercero_responsabilidad.responsabilidad_tributaria = gf_responsabilidad_tributaria.id_unico 
WHERE tercero = '".$id_Cliente."'"; 
$resulTribu      = $mysqli->query($sqlTribu);
while ($rowwTribu = mysqli_fetch_row($resulTribu)) {
    $responsabilidadesTributarias  = $responsabilidadesTribut = $rowwTribu[0];
}

//valores detalle_documento
$sqlDde = " SELECT dde.descripcion as nombreItem,dde.cantidad as cantidad ,(dde.valor_unitario+dde.valor_iva) as precioUnitario,
            uf.codigo_fe as unidad, dde.codigo as codigo, ti.codigo as codigoImpuesto,dde.descripcion_concepto as desc_concepto
           FROM gf_documento_equivalente de
           LEFT JOIN gf_tipo_documento_equivalente tde ON tde.id_unico=de.tipo  
           LEFT JOIN gf_detalle_documento_equivalente dde ON dde.documento_equivalente =de.id_unico  
           LEFT JOIN gf_unidad_factor uf ON uf.id_unico=dde.unidad_origen 
           LEFT JOIN gf_tarifas_iva ti ON ti.id_unico=dde.codigo_impuesto
           WHERE dde.documento_equivalente = '" . $id_documento . "' and dde.valor_total > 0";
$resultDde = $mysqli->query($sqlDde);
while ($rowDde = mysqli_fetch_row($resultDde)) {
    $nombreItem= $rowDde[0];
    $cant=(int)$rowDde[1];
    $precioUnitario=(int)$rowDde[2];
    $precioReferencia = 0;
    $descuento = 0;
    $cargo = 0;
    $unidad=$rowDde[3];
    $codigoP=$rowDde[4];
    $codigoImpuesto=$rowDde[5];
    $formaTransmision = "POR_OPERACION";
    $descripcion_item=$rowDde[6];
    //items
    $detalle_documento[] = [  
        "nombreItem"        => $nombreItem,
        "cantidad"          => $cant,
        "precioUnitario"    => $precioUnitario,
        "precioReferencia"  => $precioReferencia,
        "descuento"         => $descuento,
        "cargo"             => $cargo,
        "codigo"            => $codigoP,
        "unidad"            => $unidad,
        "codigoImpuesto"    => $codigoImpuesto,
        "fechaCompra"       => $fechaGeneracion,
        "formaTransmision"  => $formaTransmision,
        "observacion"       => $descripcion_item
    ];
}


//Estructura Json
$documento = [
    "nit"                           => $nit,
    "numeroResolucion"              => $numeroResolucion,
    "fechaGeneracion"               => $fechaGeneracio,
    "fechaVencimiento"              => $fechaVencimient,
    "numeroDtoComercial"            => $numeroDocumentoComercial,
    "formaPago"                     => $formaPago,
    "mediosPago"                    => $mediosPago,
    "total"                         => $total,
    "items"                         => $detalle_documento,
    "proveedor" => [
        "tipoIdentificacion"         => $tipoIdentificacion,
        "numIdentificacion"          => $numIdentificacion,
        "razonSocial"                => $nombreRazonSocial,
        "naturaleza"                 => $Naturaleza,
        "responsabilidadesFiscales"  => $responsabilidadesFiscales,
        "direccion"                  => $direccion,
        "ciudad"                     => $ciudad,
        "email"                      => $email,
        "telefono"                   => $telefono,
        "procedencia"                => $procedencia,
        "respTributarias"            => $responsabilidadesTributarias 
    ],
    "observacion" => $observacion
];

  $datosCodificados = json_encode($documento);
//var_dump($datosCodificados);



$Respuesta = documento($datosCodificados, $token, $mysqli, $id_documento);
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
    $usuario = $usu;
    $contrasenia = $contra;
    #Credenciales Para pruebas.
    //$usuario = 'demo_api';
    //$contrasenia = '12345678';
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

function documento($datosCodificados, $token, $mysqli, $id_documento) {


    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    curl_setopt_array($curl, [
        CURLOPT_PORT => "8443",
        CURLOPT_URL => "https://csi.clarisa.co:8443/facturas/rest/api/v1/docsoporte",
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

        if ($soluc2 == "Documento soporte reportado") {

            $soluc3 = $solu['data'];
            $solu5 = $soluc3['cuds'];
            $solu4 = $soluc3['numero'];

            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            $sqledit = "Update gf_documento_equivalente set cuds = '" . $solu5 . "', estado_envio ='" . $soluc2 . "', issue_date ='" . $fecha . "',issue_time ='" . $hora . "',json='$datosCodificados'  where id_unico = '" . $id_documento . "'";
            if ($mysqli->query($sqledit) != true) {
                echo "Error";
            }
            $Mensaje = $soluc2 . " " . $solu4;
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

    $Respuestaa = documento($datosCodificados, $token, $mysqli, $id_documento);
    $Mensaje = $Respuestaa;
}

if ($Mensaje == "Error en credenciales de Usuario Su sesión ha expirado, por favor vuelva a iniciar sesión US05") {
    $token = Token($compania, $mysqli);

    $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
    $resultTkn = $mysqli->query($sqlEdiTknn);

    $Respuestaa = documento($datosCodificados, $token, $mysqli, $id_documento);
    $Mensaje = $Respuestaa;
}

if($Mensaje == "Error en credenciales de Usuario No se ha enviado correctamente el token de acceso US03") {
    $token = Token($compania, $mysqli);

    $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
    $resultTkn = $mysqli->query($sqlEdiTknn);

    $Respuestaa = documento($datosCodificados, $token, $mysqli, $id_documento);
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
        window.location = '../documentosSoporteEnviados.php';
    });
</script>
