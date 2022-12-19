<?php 
require_once('Conexion/conexion.php');
require_once ('./head_listar.php');
$anno       = $_SESSION['anno'];
$compania   = $_SESSION['compania'];
#* Concepto rubro
$idCR= $_GET['id'];
$busC= "SELECT cr.id_unico, c.id_unico, c.nombre, r.id_unico, r.nombre , "
        . "r.codi_presupuesto, c.clase_concepto "
        . "FROM gf_concepto_rubro cr "
        . "LEFT JOIN gf_concepto c ON cr.concepto= c.id_unico "
        . "LEFT JOIN gf_rubro_pptal r ON cr.rubro = r.id_unico "
        . "WHERE md5(cr.id_unico) = '$idCR'";
$cr= $mysqli->query($busC);
$conR = mysqli_fetch_row($cr);
#* Cuenta débito
$cuenD= "SELECT id_unico, codi_cuenta, nombre "
        . "FROM `gf_cuenta` "
        . "WHERE (movimiento='1' OR auxiliartercero='1' OR auxiliarproyecto='1' OR centrocosto='1') "
        . "AND parametrizacionanno = $anno "
        . "ORDER BY codi_cuenta ASC";
$qcuenD= $mysqli->query($cuenD);
#* Cuenta crédito
$cuenC= "SELECT id_unico, codi_cuenta, nombre "
        . "FROM `gf_cuenta` "
        . "WHERE (movimiento='1' OR auxiliartercero='1' OR centrocosto='1' OR auxiliarproyecto='1') "
        . "AND parametrizacionanno = $anno "
        . "ORDER BY codi_cuenta ASC";
$qcuenC= $mysqli->query($cuenC);
#* Centro costo
$cc = "SELECT id_unico, nombre FROM gf_centro_costo "
        . "WHERE nombre !='Varios' AND parametrizacionanno = $anno ORDER BY nombre ASC";
$resulCc= $mysqli->query($cc);
#* Asignacion defecto 
$busc = "SELECT id_unico, nombre FROM gf_centro_costo WHERE nombre ='Varios' AND parametrizacionanno = $anno ";
$busqc= $mysqli->query($busc);
$busquedaC= mysqli_fetch_row($busqc);

#* Proyecto
$pro = "SELECT id_unico, nombre FROM gf_proyecto WHERE nombre != 'Varios' AND compania = $compania ORDER BY nombre ASC";
$resulPr= $mysqli->query($pro);
#*
$bus = "SELECT id_unico, nombre FROM gf_proyecto WHERE nombre = 'Varios' AND compania = $compania ";
$busq= $mysqli->query($bus);
$busquedaP= mysqli_fetch_row($busq);
   
#* Listar
 $buscar= "SELECT    crc.id_unico, 
                    cd.id_unico, 
                    CONCAT (cd.codi_cuenta,' - ',cd.nombre) AS cuenta_debito, 
                    cc.id_unico, 
                    CONCAT(cc.codi_cuenta,' - ' ,cc.nombre) AS cuenta_credito, 
                    cec.id_unico, 
                    cec.nombre, 
                    p.id_unico, 
                    p.nombre,
                    crc.cuenta_iva,
                    CONCAT(ctaI.codi_cuenta,' - ',ctaI.nombre) AS cuenta_IVA,
                    crc.cuenta_impoconsumo,
                    CONCAT(ctim.codi_cuenta,' - ',ctim.nombre) AS cuenta_IM, 
                    crc.cuenta_debito_seguro, 
                    CONCAT(ctaDebSeg.codi_cuenta,' - ' ,ctaDebSeg.nombre) AS cuenta_debito_S, 
                    crc.cuenta_credito_seguro, 
                    CONCAT (ctaCredSeg.codi_cuenta,' - ', ctaCredSeg.nombre) AS cuenta_credito_S, 
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
                    tr.apellidodos)) AS NOMBRE,   tr.numeroidentificacion, 
                    crc.cuenta_debito_provision, 
                    CONCAT(ctaDebProv.codi_cuenta,' - ', ctaDebProv.nombre) AS cuenta_debito_P, 
                    crc.cuenta_credito_provision, 
                    CONCAT (ctaCredProv.codi_cuenta,' - ', ctaCredProv.nombre) AS cuenta_credito_P  , 
                    tr.id_unico, 
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
                        t.apellidodos)) AS NOMBRE, t.numeroidentificacion , t.id_unico , 
                    CONCAT(ctaDcto.codi_cuenta,' - ', ctaDcto.nombre) AS ctaDcto  
        FROM        gf_concepto_rubro_cuenta crc 
        LEFT JOIN   gf_cuenta cd          ON crc.cuenta_debito  = cd.id_unico 
        LEFT JOIN   gf_cuenta cc          ON crc.cuenta_credito = cc.id_unico 
        LEFT JOIN   gf_centro_costo cec   ON crc.centrocosto    = cec.id_unico 
        LEFT JOIN   gf_proyecto p         ON crc.proyecto       = p.id_unico
        LEFT JOIN   gf_cuenta ctaI        ON ctaI.id_unico      = crc.cuenta_iva
        LEFT JOIN   gf_cuenta ctim        ON ctim.id_unico      = crc.cuenta_impoconsumo
        LEFT JOIN   gf_cuenta ctaDebSeg   ON ctaDebSeg.id_unico = crc.cuenta_debito_seguro 
        LEFT JOIN   gf_cuenta ctaCredSeg  ON ctaCredSeg.id_unico = crc.cuenta_credito_seguro 
        LEFT JOIN   gf_cuenta ctaDebProv  ON ctaDebProv.id_unico = crc.cuenta_debito_provision
        LEFT JOIN   gf_cuenta ctaCredProv ON ctaCredProv.id_unico = crc.cuenta_credito_provision 
        LEFT JOIN   gf_cuenta ctaDcto     ON ctaDcto.id_unico   = crc.cuenta_descuento
        LEFT JOIN   gf_tercero tr         ON tr.id_unico        = crc.tercero_seguro 
        LEFT JOIN   gf_tercero t          ON t.id_unico         = crc.tercero 
        WHERE       crc.concepto_rubro    = $conR[0]";
$resultado = $mysqli->query($buscar);
?>
<!-- select2 -->
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>

<style>
    label #cuentaC-error, #cuentaD-error, #sltCuentaI-error, #sltCuentaImpo-error ,#sltCuentaAjuste-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
        font-size: 10px
    }
    body{ font-size: 11px;}
    .form-control {font-size: 12px;}
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px}    
</style>
<script>
$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<title>Concepto Rubro Cuenta</title>
</head>
<body> 
    <div class="container-fluid text-center">
	<div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-10 text-left">
                <h2 align="center" class="tituloform" style="margin-top:-3px">Concepto Rubro Cuenta</h2>
                <a href="Modificar_GF_CONCEPTO_RUBRO.php?id=<?php echo $idCR;?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Rubro:<?php echo ucwords((mb_strtolower($conR[4]))); ?> - Concepto:<?php echo ucwords((mb_strtolower($conR[2]))); ?> </h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrar_GF_CONCEPTO_RUBRO_CUENTAJson.php" style="margin-bottom: -12px">
                        <p align="center" style="margin-bottom: 25px; margin-left: 40px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        
                        <div class="form-group form-inline " style="margin-top: -25px; margin-left: 10px"> 
                            <input type="hidden" id="conceptoRubro" value="<?php echo $conR[0]?>" name="conceptoRubro">
                            <label for="cuentaD" class="control-label col-sm-2" style="width:120px;"><strong style="color:#03C1FB;">*</strong>Cuenta Débito:</label>
                            <select name="cuentaD" id="cuentaD"  style="width:200px;" class="select2_single form-control col-sm-2" title="Seleccione cuenta débito" required>
                                <option value="">Cuenta Débito</option>
                                <?php while($rowCd = mysqli_fetch_row($qcuenD)){?>
                                <option value="<?php echo $rowCd[0] ?>"><?php echo ucwords((mb_strtolower($rowCd[1].' - '.$rowCd[2])));}?></option>;
                            </select> 
                            <label for="cuentaC" class="control-label col-sm-2" style="width:120px;"><strong style="color:#03C1FB;">*</strong>Cuenta Crédito:</label>
                            <select name="cuentaC" id="cuentaC"  style="width:200px;" class="select2_single form-control col-sm-2" title="Seleccione cuenta crédito" required>
                                <option value="">Cuenta Crédito</option>
                                <?php while($rowCc = mysqli_fetch_row($qcuenC)){?>
                                <option value="<?php echo $rowCc[0] ?>"><?php echo ucwords((mb_strtolower($rowCc[1].' - '.$rowCc[2])));}?></option>;
                            </select>                         
                            <label for="sltCuentaI" class="control-label col-sm-2" style="width: 120px"><strong class="obligado"></strong>Cuenta Iva:</label>
                            <select name="sltCuentaI" id="sltCuentaI" style="width: 200px" class=" form-control col-sm-1 select2_single" title="Seleccione cuenta iva">
                                <?php 
                                echo "<option value=''>Cuenta Iva</option>"; 
                                $cuenI= "SELECT id_unico, codi_cuenta, nombre "
                                        . "FROM `gf_cuenta` "
                                        . "WHERE parametrizacionanno = $anno AND (movimiento='1' OR auxiliartercero='1' OR auxiliarproyecto='1' OR centrocosto='1')  "
                                        . "ORDER BY codi_cuenta ASC";
                                $qcuenI= $mysqli->query($cuenI);                                
                                while ($rowCI = mysqli_fetch_row($qcuenI)) {
                                    echo "<option value=".$rowCI[0].">".ucwords(mb_strtolower($rowCI[1].PHP_EOL.$rowCI[2]))."</option>";
                                }
                                ?> 
                            </select> 
                        </div>
                        <br/>
                        <div class="form-group form-inline " style="margin-top: -20px;margin-left: 10px"> 
                            <label for="sltCuentaImpo" class="control-label col-sm-1" style="width: 120px"><strong class="obligado"></strong>Cuenta Impoconsumo:</label>
                            <select name="sltCuentaImpo" id="sltCuentaImpo" style="width: 200px" class="form-control col-sm-1 select2_single" title="Seleccione cuenta de impoconsumo" class="control-label col-sm-1">
                                <?php 
                                echo "<option value=''>Cuenta Impoconsumo</option>";
                                $cuenImp= "SELECT id_unico, codi_cuenta, nombre "
                                        . "FROM `gf_cuenta` "
                                        . "WHERE parametrizacionanno = $anno AND (movimiento='1' OR auxiliartercero='1' OR auxiliarproyecto='1' OR centrocosto='1')"
                                        . "ORDER BY codi_cuenta ASC";
                                $qcuenImp= $mysqli->query($cuenImp);                                
                                while ($rowCI = mysqli_fetch_row($qcuenImp)) {
                                    echo "<option value=".$rowCI[0].">".ucwords(mb_strtolower($rowCI[1].PHP_EOL.$rowCI[2]))."</option>";
                                }
                                 ?>
                            </select> 
                            <label for="sltCuentaD" class="control-label col-sm-1" style="width:120px;">Cuenta Descuento :</label>
                            <select name="sltCuentaD" id="sltCuentaD" title="Seleccione Cuenta Descuento" style="width:200px;" class="form-control col-sm-1 select2_single" style="width: 15%">
                                <?php 
                                echo "<option value=\"\">Cuenta Descuento</option>";
                                $sqlCd = "SELECT id_unico, codi_cuenta, nombre 
                                        FROM `gf_cuenta` 
                                        WHERE parametrizacionanno = $anno AND (movimiento='1' OR auxiliartercero='1' OR auxiliarproyecto='1' OR centrocosto='1')
                                        ORDER BY codi_cuenta ASC";
                                $resultCd = $mysqli->query($sqlCd);
                                while($rowCd = mysqli_fetch_row($resultCd)){
                                    echo "<option value=\"".$rowCd[0]."\">".ucwords(mb_strtolower($rowCd[1].' - '.$rowCd[2]))."</option>";
                                }
                                ?>
                            </select>
                            <label for="centroC" class="control-label col-sm-1" style="width:120px;"><strong style="color:#03C1FB;">*</strong>Centro Costo:</label>
                            <select name="centroC" id="centroC"  style="width:200px;" class="form-control col-sm-1 select2_single" title="Seleccione centro de costo" required>
                                <option value="<?php echo $busquedaC[0]?>"><?php echo ucwords(mb_strtolower($busquedaC[1]))?></option>
                                <?php while($rowCec = mysqli_fetch_row($resulCc)){?>
                                <option value="<?php echo $rowCec[0] ?>"><?php echo ucwords((mb_strtolower($rowCec[1])));}?></option>;
                            </select>                         
                                                    
                            <button type="submit" class="btn btn-primary sombra col-sm-1" style="margin-top:-2px;width: 40px;left: -15px"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                            <input type="hidden" name="MM_insert" >
                        </div>
                        <!--SOLO FFAMA-->
                        <?php if($conR[5]=='91') { ?>
                        <br/>
                        <div class="form-group form-inline " style="margin-top: -20px;margin-left: 10px"> 
                            <label for="cuentaDebitoSeguro" class="control-label col-sm-1" style="width: 120px"><strong class="obligado"></strong>Cuenta Débito Seguro:</label>
                            <select name="cuentaDebitoSeguro" id="cuentaDebitoSeguro" style="width: 200px" class="select2_single form-control col-sm-1" title="Seleccione cuenta débito seguro" class="control-label col-sm-1">
                                <?php 
                                echo "<option value=''>Cuenta Débito Seguro</option>";
                                $cuenDebitoSeguro= "SELECT id_unico, codi_cuenta, nombre "
                                        . "FROM `gf_cuenta` "
                                        . "WHERE parametrizacionanno = $anno AND (movimiento='1' OR auxiliartercero='1' OR auxiliarproyecto='1' OR centrocosto='1')"
                                        . "ORDER BY codi_cuenta ASC";
                                $cuenDebitoSeguro= $mysqli->query($cuenDebitoSeguro);                                
                                while ($rowDS = mysqli_fetch_row($cuenDebitoSeguro)) {
                                    echo "<option value=".$rowDS[0].">".ucwords(mb_strtolower($rowDS[1].' - '.$rowDS[2]))."</option>";
                                }
                                 ?>
                            </select>                                                                                
                            <label for="cuentaCreditoSeguro" class="control-label col-sm-1" style="width:120px;"><strong style="color:#03C1FB;"></strong>Cuenta Crédito Seguro:</label>
                            <select name="cuentaCreditoSeguro" id="cuentaCreditoSeguro"  style="width:200px;" class="select2_single form-control col-sm-1" title="Seleccione cuenta crédito seguro">
                                <?php 
                                echo "<option value=''>Cuenta Débito Seguro</option>";
                                $cuenCreditoSeguro= "SELECT id_unico, codi_cuenta, nombre "
                                        . "FROM `gf_cuenta` "
                                        . "WHERE parametrizacionanno = $anno AND (movimiento='1' OR auxiliartercero='1' OR auxiliarproyecto='1' OR centrocosto='1')"
                                        . "ORDER BY codi_cuenta ASC";
                                $cuenCreditoSeguro= $mysqli->query($cuenCreditoSeguro);                                
                                while ($rowCS = mysqli_fetch_row($cuenCreditoSeguro)) {
                                    echo "<option value=".$rowCS[0].">".ucwords(mb_strtolower($rowCS[1].' - '.$rowCS[2]))."</option>";
                                }
                                 ?>
                            </select>                         
                           <label for="terceroSeguro" class="control-label col-sm-1" style="width:120px"><strong style="color:#03C1FB;"></strong>Tercero Seguro:</label>
                            <select name="terceroSeguro" id="terceroSeguro" style="width:200px;" class="select2_single form-control col-sm-1" title="Seleccione tercero seguro" >
                                 <?php $tercero = "SELECT IF(CONCAT_WS(' ',
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
                                                tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.id_unico , tr.digitoverficacion  
                                            FROM gf_tercero tr ";
                                 $tercero = $mysqli->query($tercero);
                                 echo "<option value=''>Tercero Seguro</option>";
                                 while ($rowT = mysqli_fetch_row($tercero)) {
                                     if(!empty($rowT[3])) { 
                                        echo "<option value='$rowT[2]'>".ucwords(mb_strtolower($rowT[0])).' ('.$rowT[1].' - '.$rowT[3].')'."</option>";
                                     } else {
                                        echo "<option value='$rowT[2]'>".ucwords(mb_strtolower($rowT[0])).' ('.$rowT[1].')'."</option>"; 
                                     }
                                 }
                                 ?>
                            </select>                         
                            
                        </div>
                        <?php } ?>
                        <?php if($conR[6]=='1') { ?>
                         <br/>
                        <div class="form-group form-inline " style="margin-top: -20px;margin-left: 10px"> 
                            <label for="cuentaDebitoProvision" class="control-label col-sm-1" style="width: 120px"><strong class="obligado"></strong>Cuenta Débito Provisión:</label>
                            <select name="cuentaDebitoProvision" id="cuentaDebitoProvision" style="width: 200px" class="select2_single form-control col-sm-1" title="Seleccione cuenta débito provisión" class="control-label col-sm-1">
                                <?php 
                                echo "<option value=''>Cuenta Débito Provisión</option>";
                                $cuenDebProv= "SELECT id_unico, codi_cuenta, nombre "
                                        . "FROM `gf_cuenta` "
                                        . "WHERE parametrizacionanno = $anno AND (movimiento='1' OR auxiliartercero='1' OR auxiliarproyecto='1' OR centrocosto='1')"
                                        . "ORDER BY codi_cuenta ASC";
                                $cuenDebProv= $mysqli->query($cuenDebProv);                                
                                while ($rowProvD = mysqli_fetch_row($cuenDebProv)) {
                                    echo "<option value=".$rowProvD[0].">".ucwords(mb_strtolower($rowProvD[1].PHP_EOL.$rowProvD[2]))."</option>";
                                }
                                 ?>
                            </select>                                                                                
                            <label for="cuentaCreditoProvision" class="control-label col-sm-1" style="width:120px;"><strong style="color:#03C1FB;"></strong>Cuenta Crédito Provisión:</label>
                            <select name="cuentaCreditoProvision" id="cuentaCreditoProvision"  style="width:200px;" class="select2_single form-control col-sm-1" title="Seleccione cuenta crédito provisión">
                                <?php echo "<option value=''>Cuenta Crédito Provisión</option>";
                                $cuenCreProv= "SELECT id_unico, codi_cuenta, nombre "
                                        . "FROM `gf_cuenta` "
                                        . "WHERE parametrizacionanno = $anno AND (movimiento='1' OR auxiliartercero='1' OR auxiliarproyecto='1' OR centrocosto='1')"
                                        . "ORDER BY codi_cuenta ASC";
                                $cuenCreProv= $mysqli->query($cuenCreProv);                                
                                while ($rowProvC = mysqli_fetch_row($cuenCreProv)) {
                                    echo "<option value=".$rowProvC[0].">".ucwords(mb_strtolower($rowProvC[1].PHP_EOL.$rowProvC[2]))."</option>";
                                }
                                 ?>
                            </select>                          
                            
                        </div>   
                        <?php }?>
                         <br/>
                        <div class="form-group form-inline " style="margin-top: -20px;margin-left: 10px"> 
                            <label for="proyecto" class="control-label col-sm-1" style="width:120px"><strong style="color:#03C1FB;">*</strong>Proyecto:</label>
                            <select name="proyecto" id="proyecto" style="width:200px;" class="form-control col-sm-1 select2_single" title="Seleccione proyecto" required>
                                 <option value="<?php echo $busquedaP[0]?>"><?php echo ucwords(mb_strtolower($busquedaP[1]))?></option>
                                <?php while($rowP = mysqli_fetch_row($resulPr)){?>
                                <option value="<?php echo $rowP[0] ?>"><?php echo ucwords((mb_strtolower($rowP[1])));}?></option>;
                            </select> 
                            
                            <label for="sltTercero" class="control-label col-sm-1" style="width:120px;">Tercero :</label>
                            <select name="sltTercero" id="sltTercero" title="Seleccione tercero" style="width:200px;" class="form-control col-sm-1 select2_single" style="width: 15%">
                                <?php 
                                echo "<option value=\"\">Tercero</option>";
                                $sqlTT = "SELECT    IF  (CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos) IS NULL 
                                                    OR  CONCAT_WS(' ', ter.nombreuno, ter.nombredos, ter.apellidouno, ter.apellidodos)='' ,
                                                    (ter.razonsocial),
                                                    CONCAT_WS(' ',ter.nombreuno,ter.nombredos,ter.apellidouno,ter.apellidodos)) AS 'NOMBRE', 
                                                    ter.id_unico, 
                                                    CONCAT(ter.numeroidentificacion) AS 'TipoD' 
                                        FROM        gf_tercero ter
                                        LEFT JOIN   gf_tipo_identificacion ti ON ti.id_unico = ter.tipoidentificacion";
                                $resultTT = $mysqli->query($sqlTT);
                                while($rowTT = mysqli_fetch_row($resultTT)){
                                    echo "<option value=\"".$rowTT[1]."\">".ucwords(mb_strtolower($rowTT[0]))."</option>";
                                }
                                ?>
                            </select>
                            
                        </div>
                         
                        <!--FIN SOLO FFAMA -->
                        
                    </form>
                </div>
                <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
                    <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                        <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <td class="cabeza" style="display: none;">Identificador</td>
                                    <td class="cabeza" width="30px"></td>
                                    <td class="cabeza"><strong>Cuenta Débito</strong></td>
                                    <td class="cabeza"><strong>Cuenta Crédito</strong></td>
                                    <td class="cabeza"><strong>Cuenta Iva</strong></td>
                                    <td class="cabeza"><strong>Cuenta Impoconsumo</strong></td>
                                    <td class="cabeza"><strong>Cuenta Descuento</strong></td>
                                    <td class="cabeza"><strong>Centro Costo</strong></td>
                                    <td class="cabeza"><strong>Proyecto</strong></td>
                                    <?php if($conR[5]=='91') { ?>
                                    <td class="cabeza"><strong>Cuenta Débito Seguro</strong></td>
                                    <td class="cabeza"><strong>Cuenta Crédito Seguro</strong></td>
                                    <td class="cabeza"><strong>Tercero Seguro</strong></td>
                                    <?php } ?>
                                    <?php if($conR[6]=='1') { ?>
                                    <td class="cabeza"><strong>Cuenta Débito Provisión</strong></td>
                                    <td class="cabeza"><strong>Cuenta Crédito Provisión</strong></td>
                                    
                                    <?php } ?>
                                    <td class="cabeza"><strong>Tercero </strong></td>
                                </tr>
                                <tr>
                                    <th class="cabeza" style="display: none;">Identificador</th>
                                    <th class="cabeza" width="7%"></th>
                                    <th class="cabeza">Cuenta Débito</th>
                                    <th class="cabeza">Cuenta Crédito</th>
                                    <th class="cabeza">Cuenta Iva</th>
                                    <th class="cabeza">Cuenta Impoconsumo</th>
                                    <th class="cabeza">Cuenta Descuento</th>
                                    <th class="cabeza">Centro Costo</th>
                                    <th class="cabeza">Proyecto</th>
                                    <?php if($conR[5]=='91') { ?>
                                    <th class="cabeza">Cuenta Débito Seguro</th>
                                    <th class="cabeza">Cuenta Crédito Seguro</th>
                                    <th class="cabeza">Tercero Seguro</th>
                                    <?php } ?>
                                    <?php if($conR[6]=='1') { ?>
                                    <th class="cabeza">Cuenta Débito Provisión</th>
                                    <th class="cabeza">Cuenta Crédito Provisión</th>
                                    
                                    <?php } ?>
                                    <th class="cabeza">Tercero</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = mysqli_fetch_row($resultado)){?>
                                <tr>
                                    <td  class="campos" style="display: none;"><?php echo $row[0]?></td>    
                                    <td class="campos" ><a class="campos" style="cursor: pointer;" onclick="javascript:eliminar(<?php echo $row[0]?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                                    </td>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[2]));?></td>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[4]));?></td>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[10]));?></td>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[12]));?></td> 
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[27]));?></td>                                    
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[6])) ?></td>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[8])) ?></td>
                                    <?php if($conR[5]=='91') { ?>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[14]));?></td>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[16]));?></td>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[17].' - '.$row[18]));?></td>
                                    <?php }?>
                                    <?php if($conR[6]=='1') { ?>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[20]));?></td>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[22]));?></td>                                    
                                    <?php } ?>
                                    <td class="campos" ><?php echo ucwords(mb_strtolower($row[24])).' - '.$row[25];?></td>
                                    
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
	</div>
    </div>
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>¿Desea eliminar el registro seleccionado de concepto rubro cuenta?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Información eliminada correctamente</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal2" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>No se pudo eliminar la información, el registro seleccionado esta siendo usado por otra dependencia.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="js/select2.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script src="js/select/select2.full.js"></script>
    <script>
        $(document).ready(function() 
        {
            $(".select2_single").select2(
            {
                allowClear: true
            });
        });
    </script>
    <div class="modal fade" id="myModal7" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>Registro ingresado ya existe.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
    <?php require_once 'footer.php'; ?>
    

<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminar_GF_CONCEPTO_RUBRO_CUENTAJson.php?id="+id,
                  success: function (data) {
                  result = JSON.parse(data);
                  if(result==true)
                      $("#myModal1").modal('show');
                 else
                      $("#myModal2").modal('show');
                  }
              });
          });
      }
      
      function modificarModal(id,cuentaD,cuentaC,centro, proyecto,cuentaI,cuentaIM, 
      cuentaDS, CuentaCS, CuentaDP, CuentaCP, tercero, tercero2, tipo1, tipo2){
            
            document.getElementById('cuentaDM').value= cuentaD;
            document.getElementById('cuentaCM').value= cuentaC;
            document.getElementById('centroM').value= centro;
            document.getElementById('proyectoM').value= proyecto;
            document.getElementById('cuentaCI').value=cuentaI;
            document.getElementById('cuentaCIM').value = cuentaIM;
            if(cuentaDS!=''){
            document.getElementById('cuentaDS').value = cuentaDS;
            }
            if(CuentaCS!=''){
            document.getElementById('cuentaCS').value = CuentaCS;
            }
            if(CuentaDP!=''){
            document.getElementById('cuentaDP').value = CuentaDP;
            }
            if(CuentaCP!=''){
            document.getElementById('cuentaCP').value = CuentaCP;
            }
            if(tercero!=''){
            document.getElementById('terceroMod').value = tercero;
            }
            if(tercero2!=''){
            document.getElementById('tercero').value = tercero2;
            }
            
            document.getElementById('tipo1').value = tipo1;
            document.getElementById('tipo2').value = tipo2;
            
            document.getElementById('id').value= id;
              $("#myModalUpdate").modal('show');
          }
      </script>
    </script>
    <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
    </script>  
    <script type="text/javascript">    
      $('#ver1').click(function(){
        document.location = 'GF_CONCEPTO_RUBRO_CUENTA.php?id=<?php echo $idCR;?>';
      });    
    </script>
    <script type="text/javascript">    
      $('#ver2').click(function(){
        document.location = 'GF_CONCEPTO_RUBRO_CUENTA.php?id=<?php echo $idCR;?>';
      });
    </script>
    

</body>
</html>


