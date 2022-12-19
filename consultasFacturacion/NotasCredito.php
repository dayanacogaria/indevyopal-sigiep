<?php
require_once '../Conexion/conexion.php';
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
@session_start();
$id_factura = $_GET['id'];
$compania   = $_SESSION['compania'];

$query      = "SELECT numeroidentificacion FROM gf_tercero where id_unico = $compania ";
$resul      = $mysqli->query($query);
$roww       = $resul->fetch_assoc();
$companiaa  = $roww['numeroidentificacion'];
$nit        = $companiaa;

$Mensaje    = '';


//-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// validar si hay token en la bd
$sqlTkn = "SELECT token_fe FROM `gf_tercero` where id_unico='" . $compania . "'";
$resultTkn = $mysqli->query($sqlTkn);
$rowTkn = $resultTkn->fetch_assoc();

$token = $rowTkn["token_fe"];

if ($token == null) {
    Token($compania, $mysqli);
}



$sqlNumFactura      = "SELECT DISTINCT tfaf.prefijo,faf.numero_factura,faf.id_unico FROM gp_detalle_factura df 
LEFT JOIN gp_detalle_factura dfa ON df.detalleafectado = dfa.id_unico 
LEFT JOIN gp_factura faf ON dfa.factura = faf.id_unico 
LEFT JOIN gp_tipo_factura tfaf ON faf.tipofactura = tfaf.id_unico 
WHERE  df.factura = '".$id_factura."'";
$resulNumFactura    = $mysqli->query($sqlNumFactura);
$rowwNumFactura     = $resulNumFactura->fetch_assoc();
$numeroFacturaAsociada =$rowwNumFactura['prefijo'].$rowwNumFactura['numero_factura'];
$id =$rowwNumFactura['id_unico'];


//CONSULTAS valores de factura
$sqlFv = "SELECT gp_tipo_factura.prefijo,fecha_factura, fecha_vencimiento, gf_forma_pago.codigo_dian,gp_factura.descripcion as observaciones, 
    gp_factura.forma_pago,gf_tercero.razonsocial,gf_tipo_identificacion.codigo_fe,gf_tercero.numeroidentificacion,
    gf_direccion.direccion,gf_telefono.valor,gf_tercero.email,gf_tipo_regimen.codigo as regimenContable,gp_resolucion_factura.numero_resolucion,
    CONCAT(gf_departamento.rss, gf_ciudad.rss)as ciudad,
    gf_tercero.id_unico, IF(gf_tercero.razonsocial IS NULL OR gf_tercero.razonsocial ='', CONCAT_WS(' ', gf_tercero.nombreuno, gf_tercero.nombredos, gf_tercero.apellidouno, gf_tercero.apellidodos), gf_tercero.razonsocial) AS Nombre_R, 
    (SELECT SUM(df.valor_total_ajustado) FROM gp_detalle_factura df WHERE gp_factura.id_unico = df.factura) as total, 
    gp_factura.numero_factura  as numero_factura
    FROM gp_factura 
    LEFT JOIN gp_tipo_factura on gp_tipo_factura.id_unico = gp_factura.tipofactura 
    LEFT JOIN gp_resolucion_factura on gp_resolucion_factura.tipo_factura = gp_tipo_factura.id_unico
    LEFT JOIN gf_forma_pago on gp_factura.metodo_pago = gf_forma_pago.id_unico 
    LEFT join gf_tercero on gp_factura.tercero = gf_tercero.id_unico 
    LEFT JOIN gf_tipo_identificacion on gf_tercero.tipoidentificacion = gf_tipo_identificacion.id_unico 
    LEFT JOIN gf_direccion on gf_tercero.id_unico=gf_direccion.tercero 
    LEFT JOIN gf_ciudad on gf_direccion.ciudad_direccion = gf_ciudad.id_unico 
    LEFT JOIN gf_departamento on gf_ciudad.departamento = gf_departamento.id_unico 
    LEFT JOIN gf_telefono on gf_tercero.id_unico = gf_telefono.tercero 
    left join gf_tipo_regimen on gf_tercero.tiporegimen = gf_tipo_regimen.id_unico
    WHERE gp_factura.id_unico = '" . $id_factura . "' LIMIT 1";
$resultFv   = $mysqli->query($sqlFv);
$rowFv      = $resultFv->fetch_assoc();

$numeroResolucion           = $rowFv['numero_resolucion'];
$numeroDocumentoComercial   = $rowFv['prefijo'].$rowFv['numero_factura'];
$fechaGeneracion            = $rowFv['fecha_factura'];
$fechaVencimiento           = $rowFv['fecha_vencimiento'];
$mediosPago                 = $rowFv['codigo_dian'];
$observacion                = $rowFv['observaciones'];
$formaPago                  = $rowFv['forma_pago'];
$total                      = $rowFv['total'];
$nombreRazonSocial          = $rowFv['Nombre_R'];
$tipoIdentificacion         = $rowFv['codigo_fe'];
$numIdentificacion          = $rowFv['numeroidentificacion'];

if ($rowFv['codigo_fe'] == "NI") {
    $Naturaleza = "JURIDICAS";
} else {
    $Naturaleza = "NATURALES";
}

$direccion              = $rowFv['direccion'];
$ciudad                 = $rowFv['ciudad'];
$telefono               = $rowFv['valor'];
$email                  = $rowFv['email'];
$idTercero              = $rowFv['id_unico'];
$fechaGeneracio         = date('Y-m-d\TH:i:s', strtotime($fechaGeneracion));
$fechaVencimient        = date('Y-m-d\TH:i:s', strtotime($fechaVencimiento));
$regimenContable        = $rowFv['regimenContable'];
$id_Cliente              = $rowFv['id_unico'];  

$sqlFiscal     = "SELECT  GROUP_CONCAT(gf_responsabilidad_fiscal.codigo) FROM `gf_tercero_responsabilidad` 
INNER join gf_responsabilidad_fiscal on gf_tercero_responsabilidad.responsabilidad = gf_responsabilidad_fiscal.id_unico 
WHERE tercero = '".$id_Cliente."'"; 
$resulFiscal      = $mysqli->query($sqlFiscal);
while ($rowwFiscal = mysqli_fetch_row($resulFiscal)) {
    $responsabilidadesFiscales  = $responsabilidadesFiscals = $rowwFiscal[0];
}





//valores detallefactura

$sqlDtf = "SELECT gp_concepto.nombre,
	IF(df.valor_origen<0, df.valor_origen*-1 ,df.valor_origen) as valor_origen,
        df.cantidad,gf_plan_inventario.codi,
        df.descripcion,gf_unidad_factor.codigo_fe,
        IF(df.iva<0, df.iva*-1 ,df.iva) as iva,
        IF(df.impoconsumo<0, df.impoconsumo*-1 ,df.impoconsumo) as impoconsumo,
        IF(df.valor_descuento<0, df.valor_descuento*-1 ,df.valor_descuento) as valor_descuento,
        IF(df.valor_total_ajustado<0, df.valor_total_ajustado*-1 ,df.valor_total_ajustado) as valor_total_ajustado,
        IF(df.valor<0, df.valor*-1 ,df.valor) as valor,
        IF(df.ajuste_peso<0, df.ajuste_peso*-1 ,df.ajuste_peso) as ajuste_peso 
    FROM gp_detalle_factura  df LEFT JOIN gp_concepto ON gp_concepto.id_unico = df.concepto_tarifa 
    LEFT JOIN gf_plan_inventario ON gp_concepto.plan_inventario = gf_plan_inventario.id_unico
    LEFT JOIN gf_unidad_factor ON df.unidad_origen = gf_unidad_factor.id_unico
    WHERE df.factura = '".$id_factura."' and df.valor_total_ajustado != 0";

$resultDtf = $mysqli->query($sqlDtf);
while ($rowDtf = mysqli_fetch_row($resultDtf)) {
    //calcular descuento porcentaje
    if ($rowDtf[8] > 0) {
        //valordescuento *100)/(valorunitario/1.19)*cantidad
        $Descuento = ($rowDtf[8] * 100) / (($rowDtf[1]/1.19) * $rowDtf[2]);
    } else {
        $Descuento = 0;
    }

    //calcular iva
    if ($rowDtf[6] > 0) {
        $iv = round(($rowDtf[6] * 100) / $rowDtf[10], 0);
        $impuesto = "IVA_" . $iv . "";
    } else {
        //calcular impoconsumo
        if ($rowDtf[7] > 0) {
            $impo = round(($rowDtf[7] * 100) / $rowDtf[10], 0);
            $impuesto = "IMPUESTO_CONSUMO_" . $impo . "";
        } else {
            $impuesto = "SIN_IMPUESTO";
        }
    }


    //items
    $Detalle_Factura[] = [
        "nombreItemVenta"       => $rowDtf[0],
        "precioVentaUnitario"   => $rowDtf[1],
        "cantidad"              => $rowDtf[2],
        "codigo"                => $rowDtf[3],
        "descuentoPorcentaje"   => $Descuento,
        "observacion"           => $rowDtf[4],
        "unidad"                => $rowDtf[5],
        "codigoDescuento"       => 11,
        "impuesto"              => $impuesto
    ];
}



//Estructura Json
$factura = [
    "nit"                       => $nit,
    "numeroFacturaAsociada"     => $numeroFacturaAsociada,
    "numeroDocumentoComercial"  => $numeroDocumentoComercial,
    "tipo"                      => "DEVOLUCION_BIENES",
    "fechaGeneracion"           => $fechaGeneracio,
    "fechaVencimiento"          => $fechaVencimient,
    "mediosPago"                => $mediosPago,
    "observacion"               => $observacion,
    "formaPago"                 => $formaPago,
    "total"                     => $total*-1,
    "cliente" => [
        "nombreRazonSocial"     => $nombreRazonSocial,
        "tipoIdentificacion"    => $tipoIdentificacion,
        "numIdentificacion"     => $numIdentificacion,
        "naturaleza"            => $Naturaleza,
        "direccion"             => $direccion,
        "ciudad"                => $ciudad,
        "telefono"              => $telefono,
        "email"                 => $email,
        "responsabilidadesFiscales" =>  $responsabilidadesFiscales,
        "regimenContable"           =>  $regimenContable
    ],
    "items" => $Detalle_Factura
];

$datosCodificados = json_encode($factura);

//var_dump($datosCodificados);

$Respuesta = factura($datosCodificados, $token, $mysqli, $id_factura);
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
    
    //API url:
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
    if ($result === FALSE) { /* Handle error */
    }
//compiling  JSON
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


function factura($datosCodificados, $token, $mysqli, $id_factura) {


    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_PORT => "8443",
        CURLOPT_URL => "https://csi.clarisa.co:8443/facturacion/rest/api/v1/notaCredito", 
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $datosCodificados,
        CURLOPT_COOKIE => "JSESSIONID=T-BnFgT-56AicuIZjUdJwv5k6ZwZBOi_UAu0olV7.ip-172-31-9-34",
        CURLOPT_HTTPHEADER => [
            "Authorization: '" . $token . "'",
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

        //var_dump($solu);




        $soluc2 = $solu['textResponse'];
        if ($soluc2 == "Nota Crédito creada") { 

            $soluc3 = $solu['data'];
            $solu4 = $soluc3['numeroFactura'];
            $solu5 = $soluc3['cufe'];

            $fecha = date("Y-m-d");
            $hora = date("H:i:s");
            $sqledit = "Update gp_factura set cufe = '" . $solu5 . "', zip_id ='" . $soluc2 . "', issue_date ='" . $fecha . "',issue_time ='" . $hora . "'  where id_unico = '" . $id_factura . "'";
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
    return $Mensaje;
}






if ($Mensaje == "Error en credenciales de Usuario Su sesión ha expirado, por favor vuelva a iniciar sesión US05") {
    $token = Token($compania, $mysqli);

    $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
    $resultTkn = $mysqli->query($sqlEdiTknn);

    $Respuestaa = factura($datosCodificados, $token, $mysqli, $id_factura);
    $Mensaje = $Respuestaa;
}

if($Mensaje == "Error en credenciales de Usuario No se ha enviado correctamente el token de acceso US03") {
    $token = Token($compania, $mysqli);

    $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
    $resultTkn = $mysqli->query($sqlEdiTknn);

    $Respuestaa = factura($datosCodificados, $token, $mysqli, $id_factura);
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
        window.location = '../facturacionNotasCredito.php';
    });
</script>
