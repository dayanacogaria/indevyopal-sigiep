<?php



require_once './Conexion/conexion.php';
require_once ('./Conexion/conexion.php');
#session_start();
require_once './head_listar.php';


if(!empty($_GET['v'])){

$idVigencia=$_GET['v'];
$sql="SELECT vc.id_unico,
             DATE_FORMAT(vc.fecha_inicial,'%d-%m-%Y') AS fechaInicio,
             DATE_FORMAT(vc.fecha_final,'%d-%m-%Y') AS fechaFinal,
             DATE_FORMAT(vc.fecha_inicio_inter,'%d-%m-%Y') AS fechaII,
             DATE_FORMAT(vc.fecha_limite_decl,'%d-%m-%Y') AS fechaLimiDec,
             vc.vigencia,
             ac.vigencia
      FROM  gc_vencimiento_comercial vc
      LEFT JOIN gc_anno_comercial ac ON ac.id_unico=vc.vigencia
      WHERE md5(vc.vigencia)='$idVigencia'"; 
  $resultado=$mysqli->query($sql);

}/*else{

$sql="SELECT vc.id_unico,
             DATE_FORMAT(vc.fecha_inicial,'%d-%m-%Y') AS fechaInicio,
             DATE_FORMAT(vc.fecha_final,'%d-%m-%Y') AS fechaFinal,
             DATE_FORMAT(vc.fecha_inicio_inter,'%d-%m-%Y') AS fechaII,
             DATE_FORMAT(vc.fecha_limite_decl,'%d-%m-%Y') AS fechaLimiDec,
             vc.vigencia,
             ac.vigencia
      FROM  gc_vencimiento_comercial vc
      LEFT JOIN gc_anno_comercial ac ON ac.id_unico=vc.vigencia
      "; 
  $resultado=$mysqli->query($sql);

}          */               

?>

<title>Establecimientos y Vehículos Contribuyente</title>
</head>

<script>

    $(function(){
        var fecha = new Date();
        var dia = fecha.getDate();
        var mes = fecha.getMonth() + 1;
        if(dia < 10){
            dia = "0" + dia;
        }
        if(mes < 10){
            mes = "0" + mes;
        }
        var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: '',
            changeYear: true
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);


        $("#fechaInicial").datepicker({changeMonth: true,}).val(fecAct);
        $("#fechaFinal").datepicker({changeMonth: true}).val(fecAct);
        $("#fechaInicioInteres").datepicker({changeMonth: true}).val(fecAct);
        $("#fechaLimiteDeclaracion").datepicker({changeMonth: true}).val(fecAct);


    });
</script>
<body>
<!-- Librerias de carga para el datepicker -->
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>

<script>
  function hb(){

           var v = document.getElementById("sc").value;
           if(v!=""){
                 $("#btnpdf,#btnexcel").attr('disabled',false);

           }else{
                 $("#btnpdf,#btnexcel").attr('disabled',true);

           }

  }
</script>

<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once './menu.php'; ?>
    <!--inicio-->
  <div class="col-sm-10 text-left">

          <h2 id="forma-titulo3" align="center" style="margin-top: 0px;margin-right: 4px; margin-left: 4px;">Establecimientos y Vehículos Contribuyente</h2>

          <!--buscar vigencia-->
          <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;margin-bottom: 0.2%;" class="client-form">
                <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" style="margin-top: -1%;margin-bottom: 3%;">

                              <p align="center" style="margin-top: 25px; margin-left: 30px; font-size: 80%; color: white">Los campos marcados con <strong style="color:white;">*</strong> son obligatorios.</p>



                        <?php
                        $cuentaI = "SELECT c.id_unico,
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
                                        t.apellidodos)) AS NOMBRETERCEROCONTRIBUYENTE, 
                                        t.numeroidentificacion
                                        
                                FROM gc_contribuyente c
                                LEFT JOIN gf_tercero t ON t.id_unico=c.tercero";
                        $rsctai = $mysqli->query($cuentaI);
                        ?>
                        <div class="form-group" style="margin-top: -10px;margin-left: -43px;">
                            <label for="sc" class="col-sm-5 control-label">Contribuyente:</label>
                                  <div class="col-sm-3 col-md-3 col-lg-3">

                            <select style="margin-bottom: 13px;" name="contribuyente" id="sc" required style="height: auto" class="select2 form-control" title="Seleccione Contribuyente" onchange="hb()">

                                <option value="">Contribuyente</option>

                                    <?php while($row=mysqli_fetch_array($rsctai)){ ?>
                                                 <option value="<?php echo $row['id_unico']?>"><?php echo $row['numeroidentificacion']." - ".ucwords(mb_strtolower($row['NOMBRETERCEROCONTRIBUYENTE'] )) ?></option>
                                    <?php } ?>
                            </select>
                            </div>
                        </div>
                            <div class="col-sm-10" style="margin-top:0px;margin-left:600px" >

                                <button id="btnpdf" onclick="reportePdf()" class="btn sombra btn-primary" title="Generar reporte PDF"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></button>

                                <button id="btnexcel" style="margin-left:10px;" onclick="reporteExcel()" class="btn sombra btn-primary" title="Generar reporte Excel"><i class="fa fa-file-excel-o" aria-hidden="true"></i></button>
                                    <!-- Script para los botones  -->
                                   
                 
                                <script type="text/javascript">
                                        /*Recarga la pagina, bloqueo botones por tipo de informe ninguno*/
                                            $("#btnpdf,#btnexcel").attr('disabled',true);
                                   
                                    </script>        
                                          
                                      
                                   
                            
                            </div><br>

                </form>

          </div>



    </div>
  </div>
</div>

    <?php require_once './footer.php'; ?>
    <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Vencimiento Comercial?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
          <p>Información eliminada correctamente.</p>
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
          <p>No se pudo eliminar la información, el registo seleccionado está siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal20" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Ya existe un vencimiento comercial con esa vigencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver20" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>


 <?php require 'GC_VENCIMIENTO_COMERCIAL_MODAL.php' ; ?>

  <!--Script que dan estilo al formulario-->
  <!--<script type="text/javascript" src="js/menu.js"></script>-->
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
                  url:"jsonComercio/eliminarVencimientoComercialJson.php?id="+id,
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

      function open_modal_modificar(id){  

            var form_data={                            
              id:id 
            };
            $.ajax({
                type:"POST",
                url: "GC_VENCIMIENTO_COMERCIAL_MODAL.php#mdlModificar",
                data:form_data,
                success: function (data) { 
                  $("#mdlModificar").html(data);
                  $(".modalvc").modal('show');
               }
           })  
      }

  </script>

  <script type="text/javascript">
      function modal()
      {
         $("#myModal").modal('show');
      }
  </script>
    <!--Actualiza la página-->
  <script type="text/javascript">
    
      $('#ver1').click(function(){
          window.history.go(0);

      });
    
  </script>

  <script type="text/javascript">    
      $('#ver2').click(function(){
      window.history.go(0);
        
      });    
  </script>
      <!-- Llamado al pie de pagina -->
    <script>
        function reporteExcel(){
            $('form').attr('action', 'informesComercio/generar_INF_ESTABLECIMIENTO_VEHICULO_CONTRIBUYENTE_EXCEL.php');
        }

        function reportePdf(){
            $('form').attr('action', 'informesComercio/generar_INF_ESTABLECIMIENTO_VEHICULO_CONTRIBUYENTE.php');

        }
    </script>
    </body>
</html>




<link href="css/select2.css" rel="stylesheet">
<link href="css/select2-bootstrap.min.css" rel="stylesheet">


<script src="js/select2.js"></script>
<script src="js/md5.js"></script>
</head>
<script>
    
      $("#sc").select2({
        allowClear: true
      });
    
</script>


<style>
    label #nombre-error{
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;

    }
</style>

  <?php require_once 'footer.php'; ?>

</html>

