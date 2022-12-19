<?php
###################################################################################################
#
#08/08/2017 creado por Nestor B
#
####################################################################################################

require_once ('head_listar.php');
require_once ('./Conexion/conexion.php');
#session_start();
$vig = $_SESSION['anno'];
@$id = $_GET['idE'];
$emp = "SELECT e.id_unico, e.tercero, CONCAT( t.nombreuno, ' ', t.nombredos, ' ', t.apellidouno,' ', t.apellidodos ) , t.tipoidentificacion, ti.id_unico, CONCAT(ti.nombre,' ',t.numeroidentificacion)
FROM gn_empleado e
LEFT JOIN gf_tercero t ON e.tercero = t.id_unico
LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico
WHERE md5(e.id_unico) = '$id'";
$bus = $mysqli->query($emp);
$busq = mysqli_fetch_row($bus);
$idT = $busq[0];
$datosTercero= $busq[2].' ('.$busq[5].')';
$a = "none";
if(empty($idT))
{
    $tercero = "Empleado";
}
else
{
    $tercero = $datosTercero;
    $a="inline-block";
}



?>

<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style>
    label #sltPeriodo-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;
        font-size: 10px
    }

    body{
        font-size: 11px;
    }

   /* Estilos de tabla*/
   table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
   table.dataTable tbody td,table.dataTable tbody td{padding:1px}
   .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
       font-family: Arial;}
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
<script src="js/jquery-ui.js"></script>

   <title>Sábana de Provisiones</title>
    <link href="css/select/select2.min.css" rel="stylesheet">

    </head>
    <body>
        <div class="container-fluid text-center">
        <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Sábana de Nómina</h2>

                <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5>
                <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/generar_INF_SABANA_PROVISIONES.php" target="_blank">
                        <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

        <!--<div class="container-fluid text-center">
              <div class="row content">
                  <?php #require_once 'menu.php'; ?>
                  <div class="col-sm-8 text-left" style="margin-top: 0px">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Sábana de Provisiones</h2>
                      <a href="<?php #echo 'listar_GN_VACACIONES.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php #echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                      <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php #echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php #echo ucwords((mb_strtolower($datosTercero)));?></h5>
                      <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/liquidarNominaJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p> -->
<!--------------------------------------------------------------------------------------------------------------------- -->
                                <?php
                                    $uniE = " SELECT id_unico, nombre FROM gn_unidad_ejecutora";
                                    $Ueje = $mysqli->query($uniE);
                                ?>
                                <div class="form-group" style="margin-top: -5px">
                                    <div class="clasUni" style="margin-top: -5px">
                                        <label for="sltUnidadE" class="control-label col-sm-5" style="margin-top: 0px">
                                            Grupo de Gestión:
                                        </label>
                                        <select name="sltUnidadE" id="sltUnidadE" title="Seleccione Unidad Ejecutora" style="height: 30px; " class="select2_single form-control " >
                                            <option value="">Unidad Ejecutora</option>
                                                <script type="text/javascript">
                                                    $(document).ready(function(){
                                                        $.ajax({
                                                            type: "POST",
                                                            url: "unidad_ejecutora.php",
                                                            success: function(response){
                                                                $('.clasUni select').html(response).fadeIn();
                                                                $('#sltUnidadE').css('display','none');
                                                            }
                                                        });

                                                    });
                                                </script>
                                        </select>
                                    </div>
                                </div>
                               <?php
                                    $grupoG = " SELECT id_unico, nombre FROM gn_grupo_gestion";
                                    $Grupo = $mysqli->query($grupoG);
                                ?>
                                <div class="form-group" style="margin-top: -5px">

                                    <label for="sltGrupoG" class="control-label col-sm-5" style="margin-top: 0px">
                                        Grupo de Gestión:
                                    </label>
                                    <select name="sltGrupoG" id="sltGrupoG" title="Seleccione Grupo de Gestion" style="height: 30px; " class="select2_single form-control " >
                                        <option value="">Grupo de Gestión</option>
                                            <?php
                                                while($GG = mysqli_fetch_row($Grupo))
                                                {
                                                    echo "<option value=".$GG[0].">".$GG[1]."</option>";
                                                }
                                            ?>
                                    </select>
                                     <script type="text/javascript">

                                                $("#sltUnidadE").change(function(){
                                                    console.log("hola");
                                                    var opcion = '<option value="" >Grupo Gestion</option>';
                                                    console.log(opcion);
                                                    var form_data = {
                                                        is_ajax: 1,
                                                        id_unidad: +$("#sltUnidadE").val()
                                                    };
                                                    $.ajax({
                                                        type: "POST",
                                                        url: "grupo_gestion.php",
                                                        data: form_data,
                                                        success: function(response){
                                                            opcion += response;
                                                            $("#sltGrupoG").html(opcion).focus();

                                                        }
                                                    });
                                                });

                                            </script>
                                </div>
                          <!--------------------------------------------------------------------- -->


                                <?php
                                    $per = "SELECT  id_unico, codigointerno FROM gn_periodo WHERE id_unico !=1 AND tipoprocesonomina = 1 AND parametrizacionanno = '$vig'";
                                    $periodo = $mysqli->query($per);
                                ?>

                                <div class="form-group" style="margin-top: -5px">
                                    <label for="sltPeriodo" class="control-label col-sm-5" style="margin-top: 0px; ">
                                        Periodo:
                                    </label>
                                    <select  name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="height: 30px;" class="select2_single form-control" >
                                        <option value="">Periodo</option>
                                            <?php
                                                while($rowE = mysqli_fetch_row($periodo))
                                                {
                                                    echo "<option value=".$rowE[0].">".$rowE[1]."</option>";
                                                }
                                            ?>
                                    </select>
                                </div>

                                <div class="form-group" style="margin-top: -5px">
                                    <label for="no" class="col-sm-5 control-label"></label>
                                    <button  class="btn sombra btn-primary" title="Generar reporte PDF" style="margin-top: 15px;"><i>Generar</li></button>
                                </div>





                  </div>


            </div>

      </div>
    </div>
   <div>

<?php require_once './footer.php'; ?>




  <!--Script que dan estilo al formulario-->

  <script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
<!--Scrip que envia los datos para la eliminación-->
<script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              $.ajax({
                  type:"GET",
                  url:"json/eliminarVacacionesJson.php?id="+id,
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
  </script>

  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
<script type="text/javascript">
      function recargar()
      {
        window.location.reload();
      }
  </script>
    <!--Actualiza la página-->
  <script type="text/javascript">

      $('#ver1').click(function(){
         reload();
        //window.location= '../registrar_GN_ACCIDENTE.php?idE=<?php #echo md5($_POST['sltEmpleado'])?>';
        //window.location='../listar_GN_ACCIDENTE.php';
        window.history.go(-1);
      });

  </script>

  <script type="text/javascript">
      $('#ver2').click(function(){
        window.history.go(-1);
      });
  </script>
</div>

<script>
function reportePdf(){
    $('form').attr('action', 'informes/generar_INF_VOLANTE_PAGO.php');
}
</script>
<script src="js/select/select2.full.js"></script>

<script type="text/javascript">
         $("#sltPeriodo").select2();

         $("#sltGrupoG").select2();

         $("#sltUnidadE").select2();

         $("#sltConcepto").select2();

</script>
</body>
</html>
