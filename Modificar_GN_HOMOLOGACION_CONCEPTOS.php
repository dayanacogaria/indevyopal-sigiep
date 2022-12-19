<?php 
##############################################################################################################################
#                                                                                                           MODIFICACIONES
##############################################################################################################################                                                                                                           
#24/07/2017 |Erica G. |Archivo Creado
##############################################################################################################################
require_once 'head.php';
require_once('Conexion/conexion.php');
######Consultas######
$compania   = $_SESSION['compania'];
$anno       = $_SESSION['anno'];
#Datos Homologación
 $dh ="SELECT cnf.id_unico, cn.id_unico, CONCAT(cn.codigo,' - ',LOWER(cn.descripcion)), "
                . "cf.id_unico,CONCAT(LOWER(c.nombre),' - ', rcon.codi_presupuesto, ' ', LOWER(rcon.nombre)),"
                . "gg.id_unico,  LOWER(gg.nombre), "
                . "tr.id_unico, IF(CONCAT_WS(' ',
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
                      tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.digitoverficacion, cn.clase, 
                      rf.id_unico, CONCAT(rp.codi_presupuesto, ' ', LOWER(rp.nombre), ' - ', f.id_unico ,' ', LOWER(f.nombre)) , rf.rubro , rcon.id_unico  "
        . "FROM gn_concepto_nomina_financiero cnf "
        . "LEFT JOIN gn_concepto cn ON cnf.concepto_nomina = cn.id_unico "
        . "LEFT JOIN gf_concepto_rubro cf ON cnf.concepto_financiero = cf.id_unico "
        . "LEFT JOIN gf_concepto c ON cf.concepto = c.id_unico "
        . "LEFT JOIN gf_rubro_pptal rcon ON rcon.id_unico = cf.rubro "
        . "LEFT JOIN gf_rubro_fuente rf ON cnf.rubro_fuente = rf.id_unico "
        . "LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
        . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
        . "LEFT JOIN gn_grupo_gestion gg ON cnf.grupo_gestion = gg.id_unico "
        . "LEFT JOIN gf_tercero tr ON cnf.tercero = tr.id_unico "
        . "WHERE md5(cnf.id_unico )='".$_GET['id']."'";
$dh = $mysqli->query($dh);
$dh = mysqli_fetch_row($dh);
#Concepto Nómina
$id = $dh[0];
$idcn = $dh[1];
$nomcn = $dh[2];
$cn = "SELECT id_unico, codigo, LOWER(descripcion) FROM gn_concepto WHERE compania = $compania AND id_unico != $idcn   AND (clase =1 OR clase =2 OR clase = 7 OR clase = 5)  AND unidadmedida = 1 ORDER BY codigo ASC";
$cn = $mysqli->query($cn);
#Concepto Financiero
$idcf = $dh[3];
$nomcf = $dh[4];
switch ($dh[11]){
    case (1):
         $conf = "SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                    . "LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                    . "LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                    . " WHERE c.clase_concepto =2 AND c.parametrizacionanno = $anno AND cr.id_unico != $idcf  ";
    break;
    case (7):
         $conf = "SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                    . "LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                    . "LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                    . " WHERE c.clase_concepto =2 AND c.parametrizacionanno = $anno AND cr.id_unico != $idcf  ";
    break;
    case (2):
         $conf = "SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                    . "LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                    . "LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                    . " WHERE c.clase_concepto =3 AND c.parametrizacionanno = $anno AND cr.id_unico != $idcf  ";
    break;
    case (5):
        $conf = "SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                    . "LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                    . "LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                    . " WHERE c.clase_concepto =3 AND c.parametrizacionanno = $anno AND cr.id_unico != $idcf  ";
        break;
    default :
       $conf = "SELECT cr.id_unico, CONCAT(LOWER(c.nombre),' - ', rp.codi_presupuesto, ' ', LOWER(rp.nombre)) FROM gf_concepto_rubro cr "
                    . "LEFT JOIN gf_concepto c ON c.id_unico = cr.concepto "
                    . "LEFT JOIN gf_rubro_pptal rp ON cr.rubro = rp.id_unico "
                    . " WHERE c.clase_concepto =0 AND c.parametrizacionanno = $anno AND cr.id_unico != $idcf  ";
    break;
}
$cf = $mysqli->query($conf);
#Rubro Fuente
$idrf =$dh[12];
$rf = $dh[13];
$rubro = $dh[15];
if(!empty($idrf)){
$rubrof = "SELECT rf.id_unico, CONCAT(rp.codi_presupuesto, ' ', LOWER(rp.nombre),' - ', f.id_unico, ' ',LOWER(f.nombre)) "
                . "FROM gf_rubro_fuente rf LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
                . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                . "WHERE rf.rubro = $rubro AND rf.id_unico != $idrf";
} else{
    $rubrof = "SELECT rf.id_unico, CONCAT(rp.codi_presupuesto, ' ', LOWER(rp.nombre),' - ', f.id_unico, ' ',LOWER(f.nombre)) "
                . "FROM gf_rubro_fuente rf LEFT JOIN gf_rubro_pptal rp ON rf.rubro = rp.id_unico "
                . "LEFT JOIN gf_fuente f ON rf.fuente = f.id_unico "
                . "WHERE rf.rubro = $rubro ";
}

$rubrof= $mysqli->query($rubrof);

#Grupo Gestión
$idgg = $dh[5];
$nomgg = $dh[6];
$gg = "SELECT id_unico, LOWER(nombre) FROM gn_grupo_gestion  WHERE id_unico != $idgg ORDER BY nombre ASC";
$gg= $mysqli->query($gg);
#Tercero
if(!empty($dh[7])){ 
    $idtr = $dh[7];
    $nomtr = ucwords(mb_strtolower($dh[8]));
    if(empty($dh[10])){ 
        $numI = $dh[9];
    } else {
        $numI = $dh[9].'-'.$dh[10];
    }
$tr= "SELECT tr.id_unico, IF(CONCAT_WS(' ',
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
     tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.digitoverficacion 
    FROM gf_tercero tr WHERE tr.id_unico !=$idtr ORDER BY NOMBRE ASC ";
$tr= $mysqli->query($tr);
} else {
    $tr= "SELECT tr.id_unico, IF(CONCAT_WS(' ',
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
     tr.apellidodos)) AS NOMBRE, tr.numeroidentificacion, tr.digitoverficacion 
    FROM gf_tercero tr ORDER BY NOMBRE ASC ";
$tr= $mysqli->query($tr);
}

?>
<title>Modificar Homologación Concepto Nómina Finanaciera</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<style>
    label #conceptoN-error, #conceptoF-error, #grupoG-error, #tercero-error, #rubroF-error {
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}

body{
    font-size: 12px;
}

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

   <style>
    .form-control {font-size: 12px;}
    
</style>
</head>
<body> 
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-10 text-left">
    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-right: 4px; margin-left: 4px;">Modificar Homologación Concepto Nómina Finanaciera</h2>
    <a href="LISTAR_GN_CONCEPTOS_HOMOLOGACION.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
      <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">  <?php echo ucwords($nomcn).' - '.ucwords($nomcf)?></h5>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
          <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:modificar()">
          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <!--Ingresa la información-->
            <input type="hidden" name="id" id="id" value="<?php echo $id?>">
            <div class="form-group">
              <label for="conceptoN" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto Nómina:</label>
              <select name="conceptoN" id="conceptoN" class="select2_single form-control" title="Seleccione Concepto Nómina" required>
                  <option value="<?php echo $idcn?>"><?php echo ucwords($nomcn)?></option>
                <?php while($rowCn = mysqli_fetch_row( $cn)){?>
                <option value="<?php echo $rowCn[0] ?>"><?php echo $rowCn[1].' - '.ucwords($rowCn[2]);?></option>
                <?php } ?>
              </select> 
            </div>
            <div class="form-group">
              <label for="conceptoF" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto Financiero:</label>
              <select name="conceptoF" id="conceptoF" class="select2_single form-control" title="Seleccione Concepto Financiero" required>
                <option value="<?php echo $idcf?>"><?php echo ucwords($nomcf)?></option>
                <?php while($rowCf = mysqli_fetch_row( $cf)){?>
                <option value="<?php echo $rowCf[0] ?>"><?php echo ucwords($rowCf[1]);?></option>
                <?php } ?>
              </select> 
            </div>
            <script>
                $("#conceptoN").change(function(){
                    var concepto = $("#conceptoN").val();
                    if(concepto ==""){
                        
                    } else {
                        //Verificar si es concepto devengo no es necesario Rubro Fuente
                        var form_data= {action: 11, concepto:concepto};
                        $.ajax({
                            type:"POST",
                            url: "jsonPptal/gn_nomina_financieraJson.php",
                            data: form_data,
                             success: function(response){
                                 console.log(response);
                                if(response ==2 || response ==5){
                                    $("#rubroF").attr("required", false);
                                } else {
                                    $("#rubroF").attr("required", true);
                                }
                              }
                            });
                         $("#conceptoF").val("");
                         $("#rubroF").val("");
                        var form_data= {action: 6, concepto:concepto};
                        var opcion = '<option value="" >Concepto Financiero</option>';
                        $.ajax({
                            type:"POST",
                            url: "jsonPptal/gn_nomina_financieraJson.php",
                            data: form_data,
                             success: function(response){
                                 opcion +=response;
                                 $("#conceptoF").html(opcion).focus();
                                 $("#conceptoF").select2({
                                    allowClear:true
                                });
                             }
                        })
                    }
                })
            </script>
            <div class="form-group">
              <label for="rubroF" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Rubro Fuente:</label>
              <select name="rubroF" id="rubroF" class="form-control" title="Seleccione Rubro Fuente" >
                  <?php if(!empty($idrf)){ ?>
                  <option value="<?php echo $idrf?>"><?php echo ucwords($rf)?></option>
                  <?php } else { ?> 
                  <option value="">-</option>
                  <?php } ?>
                  <?php while($rowrf= mysqli_fetch_row( $rubrof)){?>
                <option value="<?php echo $rowrf[0] ?>"><?php echo ucwords($rowrf[1]);?></option>
                <?php } ?>
              </select> 
            </div>
            <script>
                $("#conceptoF").change(function(){
                    var concepto = $("#conceptoF").val();
                    if(concepto ==""){
                        
                    } else {
                         $("#rubroF").val("");
                        var form_data= {action: 8, concepto:concepto};
                        var opcion = '<option value="" >Rubro Fuente</option>';
                        $.ajax({
                            type:"POST",
                            url: "jsonPptal/gn_nomina_financieraJson.php",
                            data: form_data,
                             success: function(response){
                                 console.log(response );
                                 opcion +=response;
                                 $("#rubroF").html(opcion).focus();
                                 $("#rubroF").select2({
                                    allowClear:true
                                });
                             }
                        })
                    }
                })
            </script>
            <div class="form-group">
              <label for="grupoG" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Grupo Gestión:</label>
              <select name="grupoG" id="grupoG" class="select2_single form-control" title="Seleccione Grupo Gestión" required>
                <option value="<?php echo $idgg?>"><?php echo ucwords($nomgg)?></option>
                <?php while($rowGg = mysqli_fetch_row( $gg)){?>
                <option value="<?php echo $rowGg[0] ?>"><?php echo ucwords($rowGg[1]);?></option>
                <?php } ?>
              </select> 
            </div>
            <div class="form-group">
              <label for="tercero" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tercero:</label>
              <select name="tercero" id="tercero" class="select2_single form-control" title="Seleccione Tercero" >
                  <?php if(!empty($dh[7])) { ?>
                  <option value="<?php echo $idtr ?>"><?php echo $nomtr.' - '.$numI;?></option>
                  <option value="">-</option>
                  <?php }  else {  ?>
                <option value="">-</option>
                  <?php } ?>
                <?php while($rowT = mysqli_fetch_row( $tr)){ 
                    if(empty($rowT[3])) { 
                        $numeroI =$rowT[2];
                    } else {
                        $numeroI =$rowT[2].'-'.$rowT[3];;
                    }?>
                <option value="<?php echo $rowT[0] ?>"><?php echo ucwords(mb_strtolower($rowT[1])).' - '.$numeroI;?></option>
                <?php } ?>
              </select> 
            </div>
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert" >
          </form>
        </div>

      
      
    </div>

  </div>
</div>
<script>
    function modificar(){
        var form_data = { action: 3, id:$("#id").val(), conceptoN:$("#conceptoN").val(), conceptoF:$("#conceptoF").val(), rubroF:$("#rubroF").val(), 
            grupoG:$("#grupoG").val(),tercero:$("#tercero").val()};
            $.ajax({
              type: "POST",
              url: "jsonPptal/gn_nomina_financieraJson.php",
              data: form_data,
              success: function(response)
              { 
                  console.log(response);
                  if(response ==true){
                     $("#myModal1").modal('show');
                        $("#ver1").click(function(){
                          $("#myModal1").modal('hide');
                          document.location = 'LISTAR_GN_CONCEPTOS_HOMOLOGACION.php';
                        });  
                  } else {
                       $("#myModal2").modal('show');
                        $("#ver2").click(function(){
                           $("#myModal2").modal('hide');
                        })
                     
                  }
              }//Fin succes.
            }); 
    }
</script> 
<?php require_once 'footer.php';?>
<script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
<div class="modal fade" id="myModal1" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información modificada correctamente.</p>
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
          <p>No se ha podido modificar la información. </p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  

</body>
</html>




