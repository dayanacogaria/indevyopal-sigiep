<?php
require_once ('Conexion/conexion.php');
require_once 'head.php'; 
$id = $_GET['id'];
#Datos a modificar
$result= "SELECT  IF(CONCAT(tu.nombreuno,' ', tu.nombredos,' ',tu.apellidouno,' ',tu.apellidodos)='', 
        (tu.razonsocial),(CONCAT(tu.nombreuno,' ',tu.nombredos,' ',tu.apellidouno,' ',tu.apellidodos))) AS NOMBRE , 
         IF(CONCAT(td.nombreuno,' ', td.nombredos,' ',td.apellidouno,' ',td.apellidodos)='', 
        (td.razonsocial),(CONCAT(td.nombreuno,' ',td.nombredos,' ',td.apellidouno,' ',td.apellidodos))) AS NOMBRE2,
        gr.id_unico, 
        tu.id_unico, tu.numeroidentificacion, 
        td.id_unico, td.numeroidentificacion 
        FROM gg_gestion_responsable gr 
        LEFT JOIN gf_tercero tu ON gr.tercero_uno=tu.id_unico 
        LEFT JOIN gf_tercero td ON gr.tercero_dos= td.id_unico 
        WHERE md5(gr.id_unico)='$id'";
$result = $mysqli->query($result);
$row=  mysqli_fetch_row($result);

#Tercero
$responsable1 = "SELECT  IF(CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos)='', 
        (t.razonsocial),(CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos))) AS NOMBRE , 
        t.id_unico, t.numeroidentificacion FROM gf_tercero t 
        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
        WHERE pt.perfil = '2' AND t.id_unico != '$row[3]' ORDER BY NOMBRE ASC";
$responsable1 = $mysqli->query($responsable1);

#Tercero
$responsable2 = "SELECT  IF(CONCAT(t.nombreuno,' ', t.nombredos,' ',t.apellidouno,' ',t.apellidodos)='', 
        (t.razonsocial),(CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos))) AS NOMBRE , 
        t.id_unico, t.numeroidentificacion FROM gf_tercero t 
        LEFT JOIN gf_perfil_tercero pt ON t.id_unico = pt.tercero 
        WHERE pt.perfil = '2' AND t.id_unico != '$row[5]' AND t.id_unico != '$row[3]' ORDER BY NOMBRE ASC";
$responsable2= $mysqli->query($responsable2);
?>
<!-- select2 -->
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>
label#responsable1-error, #responsable2-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

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

<title>Modificar Gestión Responsable</title>
</head>
<div class="container-fluid text-center">
    <div class="row content">
    <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left" style="margin-left: -16px;margin-top: -22px; ">
        <h2 class="tituloform" align="center" >Modificar Gestión Responsable</h2>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -5px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonProcesos/modificar_GG_GESTION_RESPONSABLEJson.php">
                <p align="center" style="margin-bottom: 20px; margin-top: 5px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                <input type="hidden" value="<?php echo $row[2]?>" name="id" id="id">
                    <div class="form-group" style="margin-top: 0px;">
                        <label for="responsable1" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Responsable 1:</label>
                        <input type="hidden" name="responsable1" id="responsable1" required="required" title="Seleccione Responsable 1" value="<?php echo $row[3]?>">
                        <select name="responsable11" id="responsable11" required="required" style="margin-left: 10px; margin-right: 10px;"   class="select2_single form-control col-sm-1" title="Seleccione Responsable 1" required="required">
                            <option value="<?php echo $row[3]?>"><?php echo ucwords(strtolower($row[0].'('.$row[4]))?></option>
                            <?php while($row1 = mysqli_fetch_row($responsable1)){?>
                            <option value="<?php echo $row1[1] ?>"><?php echo ucwords((strtolower($row1[0].'('.$row1[2].')')));}?></option>;
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: 0px;">
                        <label for="responsable2" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Responsable 2:</label>
                        <input type="hidden" name="responsable2" id="responsable2" required="required" title="Seleccione Responsable 2" value="<?php echo $row[5]?>">
                        <select name="responsable22" id="responsable22" style="margin-left: 10px; margin-right: 10px;"   class="select2_single form-control col-sm-1" title="Seleccione Responsable 2"onchange="llenar2();">
                            <option value="<?php echo $row[5]?>"><?php echo ucwords(strtolower($row[1].'('.$row[6]))?></option>
                            <?php while($row2 = mysqli_fetch_row($responsable2)){?>
                            <option value="<?php echo $row2[1] ?>"><?php echo ucwords((strtolower($row2[0].'('.$row2[2].')')));}?></option>;
                            <script type="text/javascript" >
                                $("#responsable11").change(function(){
                                    var ter1= document.getElementById('responsable11').value;
                                    document.getElementById('responsable1').value=(ter1);
                                    var ter2= document.getElementById('responsable2').value;
                                    if(ter1==ter2){
                                        document.getElementById('responsable2').value='';
                                        $("#responsable22 option[value='<?php echo $row[5]?>']").css('display','none');
                                    }
                                    var form_data={
                                        existente:8,
                                        tercero:$("#responsable11").val()
                                    };

                                    $.ajax({
                                        type: 'POST',
                                        url: "consultasBasicas/consultarNumeros.php",
                                        data:form_data,
                                        success: function (data) { 
                                            $("#responsable22").html(data).fadeIn();
                                            $("#responsable22").css('display','none');
                                           
                                        }
                                    });
                                });
                            </script>
                            <script>
                                function llenar2()
                                {
                                   var ter2= document.getElementById('responsable22').value;
                                    document.getElementById('responsable2').value=(ter2); 
                                }
                            </script>
                        </select> 
                    </div>
                    <div class="form-group" style="margin-top: 20px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Los responsables deben ser diferentes.</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
            </div>
        </div>
    </div>
</div>
<?php require_once 'footer.php';?>
</body>
<script src="js/select/select2.full.js"></script>

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>