<?php
###################################################################################################
#
#04/04/2017 creado por Nestor B 
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
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<style>
    label #sltEmpleado-error, #sltPeriodo-error {
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

   <title>Generación Volante de Pago</title>
   
       
    </head>
    <body>
        <div class="container-fluid text-center">
        <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-8 text-left">
                <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Generación de Volante de Pago de Prima de Vacaciones</h2>
               
                <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((mb_strtolower($datosTercero)));?></h5>
                <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">        
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/generar_INF_VOLANTE_PRIMAVAC.php" target="_blank">
                        <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline" style="margin-left: 40px">
                        <div class="form-group form-inline " style="margin-top: -25px;"> 
        
        <!--<div class="container-fluid text-center">
              <div class="row content">
                  <?php #require_once 'menu.php'; ?>
                  <div class="col-sm-8 text-left" style="margin-top: 0px">
                      <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Liquidación Nómina</h2>
                      <a href="<?php #echo 'listar_GN_VACACIONES.php?id='.$_GET['idE'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php #echo $a?>;margin-top:-5px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                      <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:92%; display:<?php #echo $a?>; margin-bottom: 10px; margin-right: 4px; margin-left: 4px;  background-color: #0e315a; color: white; border-radius: 5px"><?php #echo ucwords((mb_strtolower($datosTercero)));?></h5> 
                      <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px">
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/liquidarNominaJson.php">
                              <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p> -->                                         
<!--------------------------------------------------------------------------------------------------------------------- -->
                     
                          <?php 
                       if(empty($idT))
                        {
                         $emp = "SELECT                         
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT_WS(' ',t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico != 2 ORDER BY e.id_unico";
                            $idTer = "";
                        }
                        else
                        {
                        $emp = "SELECT                      
                                                        e.id_unico,
                                                        e.tercero,
                                                        t.id_unico,
                                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos)
                            FROM gn_empleado e
                            LEFT JOIN gf_tercero t ON e.tercero = t.id_unico WHERE e.id_unico = 0";
                        $idTer = $idT;
                        }
                        $empleado = $mysqli->query($emp);
                        ?>
                          <label for="sltEmpleado" class="control-label col-sm-1" style="margin-top: 15px">
                                <strong style="color:#03C1FB;">*</strong>Empleado:
                            </label>
                            <select name="sltEmpleado" id="sltEmpleado" title="Seleccione Empleado" style="width: 200px;height: 30px; margin-top: 15px; margin-left: 35px;" class="select2_single form-control col-sm-1" required>
                                <option value="<?php echo $idTer?>"><?php echo $tercero?></option>
                                <?php 
                                    while($rowE = mysqli_fetch_row($empleado))
                                    {
                                        echo "<option value=".md5($rowE[0]).">".$rowE[3]."</option>";
                                    }
                                ?>                                                          
                            </select>
                          <!--------------------------------------------------------------------- -->
                            <?php
                                $per = "SELECT  id_unico, codigointerno FROM gn_periodo WHERE tipoprocesonomina = 7 AND parametrizacionanno = '$vig'";

                                $periodo = $mysqli->query($per);
                            ?>

                            
                          <label for="sltPeriodo" class="control-label col-sm-1" style="margin-top: 15px; margin-left: 60px;">
                                <strong style="color:#03C1FB;">*</strong>Periodo:
                            </label>
                          <select  name="sltPeriodo" id="sltPeriodo" title="Seleccione Periodo" style="width: 140px;height: 30px; margin-top: 15px; margin-left: 35px;" class="select2_single form-control col-sm-1" required>
                                <option value="">Periodo</option>
                                <?php 
                                    while($rowE = mysqli_fetch_row($periodo))
                                    {
                                        echo "<option value=".md5($rowE[0]).">".$rowE[1]."</option>";
                                    }
                                ?>       
                            </select>
                                                        
                             <button  class="btn sombra btn-primary" title="Generar reporte PDF" style="margin-top: 15px;"><i>Generar</li></button>
                           <!-- <input type="hidden" name="MM_insert" > -->                     
                          <!-- <label for="txtdiasT" class="col-sm-2 control-label">
                                <strong class="obligado"></strong>Días Trabajados:
                            </label>
                            <input  name="txtdiasT" id="txtdiasT" title="Ingrese Número de días trabajados" type="number" style="width: 140px;height: 30px" class="form-control col-sm-1" placeholder="Días trabajados"> -->


                                
                        </div>
                          
                        </div>
                  </div>
                
<!---------------------------------------------------------------------------------------------------->                        
 
    <div class="form-group form-inline" style="margin-top:5px; display:<?php echo $a?>">
                <?php require_once './menu.php'; 
                $sql = "SELECT          v.id_unico,
                                        v.fechainicio,
                                        v.fechafin,
                                        v.fechainiciodisfrute,
                                        v.fechafindisfrute,
                                        v.numeroacto,
                                        v.fechaacto,
                                        v.empleado,
                                        e.id_unico,
                                        e.tercero,
                                        t.id_unico,
                                        CONCAT(t.nombreuno,' ',t.nombredos,' ',t.apellidouno,' ',t.apellidodos),
                                        v.tiponovedad,
                                        tn.id_unico,
                                        tn.nombre
                                FROM gn_vacaciones v
                                LEFT JOIN   gn_empleado e           ON v.empleado       = e.id_unico
                                LEFT JOIN   gf_tercero t            ON e.tercero        = t.id_unico
                                LEFT JOIN   gn_tipo_novedad tn      ON v.tiponovedad = tn.id_unico
                                WHERE v.empleado = $idTer";
                    $resultado = $mysqli->query($sql);
                ?>
                <div class="col-sm-12 text-left" style=""> 
                    <div class="table-responsive" style="margin-top:0px;">
                        <div class="table-responsive" style="">
                            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                        <!-- Actualización 24 / 02 16:43 No es necesario mostrar el nombre del empleado
                                        <td class="cabeza"><strong>Empleado</strong></td>
                                        -->
                                        <td class="cabeza"><strong>Número Acto</strong></td>
                                        <td class="cabeza"><strong>Fecha Acto</strong></td>
                                        <td class="cabeza"><strong>Tipo Novedad</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio</strong></td>
                                        <td class="cabeza"><strong>Fecha Fin</strong></td>
                                        <td class="cabeza"><strong>Fecha Inicio Disfrute</strong></td>
                                        <td class="cabeza"><strong>Fecha Fin Disfrute</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th class="cabeza" width="7%"></th>
                                        <!-- Actualización 24 / 02 16:43 No es necesario mostrar el nombre del empleado
                                        <th class="cabeza">Empleado</th>
                                        -->
                                        <th class="cabeza">Número Acto</th>
                                        <th class="cabeza">Fecha Acto</th>
                                        <th class="cabeza">Tipo Novedad</th>
                                        <th class="cabeza">Fecha Inicio</th>
                                        <th class="cabeza">Fecha Fin</th>
                                        <th class="cabeza">Fecha Inicio Disfrute</th>
                                        <th class="cabeza">Fecha Fin Disfrute</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                    while ($row = mysqli_fetch_row($resultado)) {                                         
                                             $vfi = $row[1];
                                             if(!empty($row[1])||$row[1]!=''){
                                            $vfi = trim($vfi, '"');
                                            $fecha_div = explode("-", $vfi);
                                            $anioi = $fecha_div[0];
                                            $mesi  = $fecha_div[1];
                                            $diai  = $fecha_div[2];
                                            $vfi   = $diai.'/'.$mesi.'/'.$anioi;
                                        }else{
                                            $vfi='';
                                        }
                                        
                                            $vff = $row[2];
                                            if(!empty($row[2])||$row[2]!=''){
                                            $vff = trim($vff, '"');
                                            $fecha_div = explode("-", $vff);
                                            $aniof = $fecha_div[0];
                                            $mesf = $fecha_div[1];
                                            $diaf = $fecha_div[2];
                                            $vff = $diaf.'/'.$mesf.'/'.$aniof;
                                        }else{
                                            $vff='';
                                        }

                                            $vfid = $row[3];
                                            if(!empty($row[3])||$row[3]){
                                            $vfid = trim($vfid, '"');
                                            $fecha_div = explode("-", $vfid);
                                            $anioid = $fecha_div[0];
                                            $mesid  = $fecha_div[1];
                                            $diaid  = $fecha_div[2];
                                            $vfid   = $diaid.'/'.$mesid.'/'.$anioid;
                                        }else{
                                            $vfid='';
                                        }
                                        
                                            $vffd = $row[4];
                                            if(!empty($row[4])||$row[4]!=''){
                                            $vffd = trim($vffd, '"');
                                            $fecha_div = explode("-", $vffd);
                                            $aniofd = $fecha_div[0];
                                            $mesfd = $fecha_div[1];
                                            $diafd = $fecha_div[2];
                                            $vffd = $diafd.'/'.$mesfd.'/'.$aniofd;
                                        }else{
                                            $vffd='';
                                        }
                                        
                                            $vfa = $row[6];
                                            if(!empty($row[6])||$row[6]!=''){
                                            $vfa = trim($vfa, '"');
                                            $fecha_div = explode("-", $vfa);
                                            $aniofa = $fecha_div[0];
                                            $mesfa = $fecha_div[1];
                                            $diafa = $fecha_div[2];
                                            $vfa = $diafa.'/'.$mesfa.'/'.$aniofa;
                                        }else{
                                            $vfa='';
                                        }

                                                                                                                            
                                            $vid    = $row[0];
                                            #$vfi    = $row[1];
                                            #$vff    = $row[2];
                                            #$vfid   = $row[3];
                                            #$vffd   = $row[4];
                                            $vnact  = $row[5];
                                            #$vfact  = $row[6];
                                            $vemp   = $row[7];
                                            $empid  = $row[8];
                                            $empter = $row[9];
                                            $terid  = $row[10];
                                            $ternom = $row[11];
                                            $vtip   = $row[12];
                                            $tnid   = $row[13];
                                            $tnnom  = $row[14];
                                        ?>
                                    <tr>
                                        <td style="display: none;"><?php echo $row[0]?></td>
                                        <td>
                                            <a href="#" onclick="javascript:eliminar(<?php echo $row[0];?>);">
                                                <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                            </a>
                                            <a href="modificar_GN_VACACIONES.php?id=<?php echo md5($row[0]);?>">
                                                <i title="Modificar" class="glyphicon glyphicon-edit" ></i>
                                            </a>
                                        </td>                                        
                                        <!-- Actualización 24 / 02 16:47 No es necesario mostrar el nombre del empleado
                                        <td class="campos"><?php #echo $ternom?></td>                
                                        -->               
                                        <td class="campos"><?php echo $vnact?></td>                
                                        <td class="campos"><?php echo $vfa?></td>                
                                        <td class="campos"><?php echo $tnnom?></td>                
                                        <td class="campos"><?php echo $vfi?></td>                
                                        <td class="campos"><?php echo $vff?></td>                   
                                        <td class="campos"><?php echo $vfid?></td>                
                                        <td class="campos"><?php echo $vffd?></td>                
                                    </tr> 
                                    <?php }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
               </div> 
            </div>
           
      </div>                                    
    </div>
   <div>


<?php require_once './footer.php'; ?>
        <div class="modal fade" id="myModal" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>¿Desea eliminar el registro seleccionado de Vacaciones?</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
          <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal1" role="dialog" align="center">
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>Información eliminada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver1" onclick="recargar()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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

<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript"> 
         $("#sltEmpleado").select2();
        </script>
<script type="text/javascript"> 
         $("#sltPeriodo").select2();
        </script>
</body>
</html>