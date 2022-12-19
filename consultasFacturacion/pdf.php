<?php
require_once '../Conexion/conexion.php';
setlocale(LC_ALL,"es_ES");
date_default_timezone_set("America/Bogota");
@session_start();
$id_factura = $_GET['id'];
$compania   = $_SESSION['compania'];


$queryNFa      = "SELECT gp_tipo_factura.prefijo,gp_factura.numero_factura FROM gp_factura LEFT JOIN gp_tipo_factura on gp_tipo_factura.id_unico = gp_factura.tipofactura where gp_factura.id_unico = ".$id_factura."";
$resul      = $mysqli->query($queryNFa);
$roww       = $resul->fetch_assoc();
$numeroDocumentoComercial   = $roww['prefijo'].$roww['numero_factura'];


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




function Token($compania, $mysqli) {

    $qury   = "SELECT usuario_fe,contrasena_fe FROM `gf_tercero` where id_unico='".$compania."' ";
    $resl   = $mysqli->query($qury);
    $rowwu  = $resl->fetch_assoc();
    $usu    = $rowwu['usuario_fe'];
    $contra = $rowwu['contrasena_fe'];

    $usuario        = $usu;
    $contrasenia    = $contra;
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
    }
    return $token;
}
   
    
$Mensaje = pdf($token,$nit,$numeroDocumentoComercial);

if ($Mensaje == '') {
    $token = Token($compania, $mysqli);

    $sqlEdiTknn = "UPDATE gf_tercero set token_fe ='" . $token . "' where id_unico='" . $compania . "'";
    $resultTkn = $mysqli->query($sqlEdiTknn);

    $Mensaje = pdf($token,$nit,$numeroDocumentoComercial);
}


function pdf($token,$nit,$numeroDocumentoComercial){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    
    curl_setopt_array($curl, [
      CURLOPT_PORT => "8443",
      CURLOPT_URL => "https://csi.clarisa.co/reportes/rest/api/v1/pdf/factura?nit=".$nit."&numeroFactura=".$numeroDocumentoComercial."",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_POSTFIELDS => "",
      CURLOPT_COOKIE => "JSESSIONID=T-BnFgT-56AicuIZjUdJwv5k6ZwZBOi_UAu0olV7.ip-172-31-9-34",
      CURLOPT_HTTPHEADER => [
        "Authorization:'".$token."'",
        "Content-Type: application/json"
      ],
    ]);
    
    $response   = curl_exec($curl);
    $err        = curl_error($curl);
    
    curl_close($curl);
   

    if ($err) {
      //echo "cURL Error #:" . $err;
      $solu3 = "cURL Error #:" . $err;
    } else {
        $solu = json_decode($response, true);
        $solu2 = $solu['data'];
      
      $Mensaje = $solu2;
    }
    return $Mensaje;
}
if ($Mensaje == '') { 
  header('Location: ../informes/inf_factura_electronica.php?factura='.md5($id_factura).'&t='.$_REQUEST['t']);
} else { 

$archivo = fopen("datos.txt","w+");
$contenido = $Mensaje;
fwrite($archivo,$contenido);


$pdf_base64 = "datos.txt";

$pdf_base64_handler = fopen($pdf_base64,'r');
$pdf_content = fread ($pdf_base64_handler,filesize($pdf_base64));
fclose ($pdf_base64_handler);
$pdf_decoded = base64_decode ($pdf_content);
$pdf = fopen ('FE.pdf','w');
fwrite ($pdf,$pdf_decoded);
fclose ($pdf);


header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=documento.pdf");
readfile("FE.pdf");

} ?>    
