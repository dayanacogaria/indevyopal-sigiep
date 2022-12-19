<?php
require_once('../Conexion/conexion.php');
require '../ExcelR/Classes/PHPExcel/IOFactory.php';              
session_start();
setlocale(LC_ALL,"es_ES");
date_default_timezone_set('America/Bogota');
$anno = $_SESSION['anno'];
$documento  = $_FILES['file'];
$name       = $_FILES['file']['name'];
$ext        = pathinfo($name, PATHINFO_EXTENSION);
$directorio ='../documentos/facturaxml/';
$nombre     = $name;
$nombreArchivo= pathinfo($name, PATHINFO_FILENAME);
$subir      = move_uploaded_file($_FILES['file']['tmp_name'],$directorio.$nombre);
$ruta   = $directorio.$nombre;
$xml=file_get_contents($ruta);
$xml=str_replace('<?xml version="1.0" encoding="utf-8" standalone="no"?>',"",$xml);
$xml=str_replace('xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2"',"",$xml);
$xml=str_replace('xmlns="urn:oasis:names:specification:ubl:schema:xsd:AttachedDocument-2"',"",$xml);
$xml=str_replace('xmlns:ccts="urn:un:unece:uncefact:data:specification:CoreComponentTypeSchemaModule:2"',"",$xml);
$xml=str_replace('xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2"',"",$xml);
$xml=str_replace('xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2"',"",$xml);
$xml=str_replace('xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2"',"",$xml);
$xml=str_replace('xmlns:sts="dian:gov:co:facturaelectronica:Structures-2-1"',"",$xml);
$xml=str_replace('xmlns:ipt="urn:clarisa:names:specification:ubl:colombia:schema:xsd:InteroperabilidadPT-1"',"",$xml);
$xml=str_replace('xmlns:ds="http://www.w3.org/2000/09/xmldsig#"',"",$xml);
$xml=str_replace('xmlns="urn:oasis:names:specification:ubl:schema:xsd:ApplicationResponse-2"',"",$xml);
$xml=str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>',"",$xml);
$xml=str_replace('<![CDATA[',"",$xml);
$xml=str_replace(']]>',"",$xml);

 $xml=$xml;
 $fh = fopen($directorio.'xml.txt', 'w') or die("Se produjo un error al crear el archivo");
$texto = <<<_END
$xml
_END;
  fwrite($fh, $texto) or die("No se pudo escribir en el archivo");
  fclose($fh);
$subir1      = move_uploaded_file($xml,$directorio.$nombre);
$xml1=file_get_contents($directorio.'xml.txt');

$xmldata = simplexml_load_string($xml1);
//$xml= new \SimpleXMLElement($ruta, null, true);
//var_dump($xmldata);
$json = json_encode($xmldata);
$fac = json_decode($json, true);

$factura=$fac['cac:Attachment'];
$factura1=$factura['cac:ExternalReference'];
$factura2=$factura1['cbc:Description'];
$factura3=$factura2['Invoice'];
$factura4=$factura3['ext:UBLExtensions'];
$factura5=$factura4['ext:UBLExtension'];
$factura6=$factura5[0]; 
$factura7=$factura6['ext:ExtensionContent']; 
$factura8=$factura7['sts:DianExtensions']; 
$factura9=$factura8['sts:InvoiceControl']; 
$factura10=$factura9['sts:AuthorizedInvoices'];
//PREFIJO 
$prefijo=$factura10['sts:Prefix']; 

//Obtener numero factura del numero documento completo
$numDoc=$fac['cbc:ID'];
$numero_factura=str_replace($prefijo,"",$numDoc);

//Fecha Generacion
$fecha_generacion=$factura3['cbc:IssueDate'];

//Hora generacion
$hora_generacion=$factura3['cbc:IssueTime'];
$hora_generacion = substr($hora_generacion, 0, -6); 

//Fecha Vencimiento
$fecha_vencimiento=$factura3['cbc:DueDate'];

//Hora vencimiento
$hora_vencimiento="00:00:00";

//Numero autorizacion/Resolución
$numero_autorizacion=$factura9['sts:InvoiceAuthorization'];

//Fecha Inicio y Fin autorizacion 
$fechas_autorizacion=$factura9['sts:AuthorizationPeriod'];
$fecha_inicio_autorizacion=$fechas_autorizacion['cbc:StartDate'];
$fecha_fin_autorizacion=$fechas_autorizacion['cbc:EndDate'];

//Rango Inicial y final autorizacion
$rango_inicio_autorizacion=$factura10['sts:From']; 
$rango_fin_autorizacion=$factura10['sts:To']; 

//Cedula Emisor Factura
$datos_emisor=$factura3['cac:AccountingSupplierParty'];
$dat_e=$datos_emisor['cac:Party'];
$data=$dat_e['cac:PartyTaxScheme'];
$numero_identificacion_e=$data['cbc:CompanyID'];
$sqlE="SELECT id_unico FROM gf_tercero WHERE numeroidentificacion='$numero_identificacion_e'";
$resultE=$mysqli->query($sqlE);
    if (mysqli_num_rows($resultE)>0) {
      $indicador=2;
      $rowE = mysqli_fetch_row($resultE);
      $idEmisor = $rowE[0];
    }else{
      $indicador=1;
    }


//Cedula Receptor Factura
$datos_receptor=$factura3['cac:AccountingCustomerParty'];
$dat_r=$datos_receptor['cac:Party'];
$data_r=$dat_r['cac:PartyTaxScheme'];
$numero_identificacion_r=$data_r['cbc:CompanyID'];
$sqlR="SELECT id_unico FROM gf_tercero  WHERE numeroidentificacion='$numero_identificacion_r'";
$resultR=$mysqli->query($sqlR);
    if (mysqli_num_rows($resultR)>0) {
      $indicadorR=2;
      $rowR = mysqli_fetch_row($resultR);
      $idReceptor = $rowR[0];
    }else{
      $indicadorR=1;
    }

//Cufe factura de compra
$cufe_factura=$factura3['cbc:UUID'];

//Valor a pagar factura
$valorF=$factura3['cac:LegalMonetaryTotal'];
$valorTotal=$valorF['cbc:PayableAmount'];

$sqlFcA="SELECT * FROM gf_factura_compra  WHERE prefijo_factura='$prefijo' AND numero_factura='$numero_factura'";
$resultRca=$mysqli->query($sqlFcA);
    if (mysqli_num_rows($resultRca)>0) {
      $existe=1;
    }else{
      $existe=2;
    }

//Insertar datos a la tabla factura_compra
if ($indicador==2 && $indicadorR==2 && $existe==2) {
      if ($numero_factura!=NULL && $prefijo!=NULL && $fecha_generacion!=NULL && $hora_generacion!=NULL &&  $fecha_vencimiento!=NULL
        && $hora_vencimiento!=NULL && $numero_autorizacion!=NULL &&  $fecha_inicio_autorizacion!=NULL &&  $fecha_fin_autorizacion!=NULL
        && $rango_inicio_autorizacion!=NULL && $rango_fin_autorizacion!=NULL && $idEmisor!=NULL && $idReceptor!=NULL &&  $cufe_factura!=NULL && $valorTotal!=NULL) {
         
         $insertFc="INSERT INTO `gf_factura_compra` (`numero_factura`, `prefijo_factura`, 
         `fecha_generacion`, `hora_generacion`, `fecha_vencimiento`, 
         `hora_vencimiento`, `numero_autorizacion`, `fecha_inicio_autorizacion`, 
         `fecha_fin_autorizacion`, `rango_inicio_autorizacion`, 
         `rango_fin_autorizacion`, `emisor_factura`, `receptor_factura`, `cufe_factura`,
         `parametrizacionanno`,`valor_factura`) 
          VALUES ('$numero_factura', '$prefijo', '$fecha_generacion', '$hora_generacion', '$fecha_vencimiento', '$hora_vencimiento', 
         '$numero_autorizacion', '$fecha_inicio_autorizacion', '$fecha_fin_autorizacion', '$rango_inicio_autorizacion', 
          '$rango_fin_autorizacion', $idEmisor, $idReceptor, '$cufe_factura', $anno,$valorTotal)";
          $insert=$mysqli->query($insertFc);
      }
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
<body>
</body>
</html>
<!--Modal para informar al usuario que se ha registrado-->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información del archivo cargada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <!--Modal para informar al usuario que no se ha podido registrar -->
  <div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido cargar la información del archivo.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--Modal para informar al usuario que no se ha podido encontrar uno de los terceros leidos en el xml -->
  <div class="modal fade" id="myModalX" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido cargar la información del archivo debido a que no esta registrado uno de los terceros asociados a la factura de compra.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <!--Modal para informar al usuario que ya existe la factura cargada. -->
  <div class="modal fade" id="myModalYa" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se ha podido cargar la información del archivo debido a que ya existe la factura, valide nuevamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
<!--lnks para el estilo de la pagina-->
<script type="text/javascript" src="../js/menu.js"></script>
  <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
  <script src="../js/bootstrap.min.js"></script>
<!--Abre nuevamente la pagina de listar para mostrar la informacion guardada-->
<?php 

if ($indicador==1 || $indicadorR==1) {?>
 <script type="text/javascript">
  $("#myModalX").modal('show');
    $("#ver3").click(function(){
    $("#myModalX").modal('hide');      
    window.location='../cargarFacturaXML.php';
  });
</script>
<?php 
}else{

if ($existe==1) { ?>
  <script type="text/javascript">
  $("#myModalYa").modal('show');
  $("#ver5").click(function(){
    $("#myModalYa").modal('hide');      
    window.location='../cargarFacturaXML.php';
  });
</script>
<?php
}else{

if($insert==true){ ?>
<script type="text/javascript">
  $("#myModal1").modal('show');
  $("#ver1").click(function(){
    $("#myModal1").modal('hide');      
    window.location='../EventosFacturaRADIAN.php';
  });
</script>
<?php }else{ ?>
<script type="text/javascript">
  $("#myModal2").modal('show');
    $("#ver2").click(function(){
    $("#myModal2").modal('hide');      
    window.location='../cargarFacturaXML.php';
  });
</script>
<?php } 
    }
  }
?>