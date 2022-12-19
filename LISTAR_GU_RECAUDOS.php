<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#04/07/2018 |Erica G. | Archivo Creado 
#######################################################################################################
require_once './Conexion/conexion.php';
require_once './Conexion/ConexionPDO.php';
require_once './jsonPptal/funcionesPptal.php';
require_once './head_listar.php';
$con = new ConexionPDO();
$t="";
if(!empty($_GET['s'])){
    $sc = $con->Listar("SELECT LOWER(nombre) FROM gf_sucursal WHERE id_unico =".$_GET['s']);
    $t = "Sucursal: ".ucwords($sc[0][0]);
}
?>
<html>
    <head>    
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
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
       
        
        $("#fechaini").datepicker({changeMonth: true,}).val();
        $("#fechafin").datepicker({changeMonth: true}).val();
        
        
});
</script>
        <title>Listar Recaudos</title>
    </head>
    <body>
        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once './menu.php'; ?>
                <div class="col-sm-10 text-left">
                    <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Listar Recaudos <?php echo $t;?></h2>
                        <?php if(empty($_GET['s'])){ ?>
                        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px; margin-top: -3px;" class="client-form">         
                            <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="" >  
                                <p align="center" style="margin-bottom: 25px; margin-top:5px;  font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                                <div class="form-group" style="margin-top: -5px">
                                    <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong><strong class="obligado">*</strong>Sucursal:</label>
                                    <div class="form-group form-inline  col-md-3 col-lg-3">
                                    <select name="sucursal" id="sucursal" class="form-control select2" title="Seleccione Sucursal" style="height: auto " required>
                                        <?php 
                                            echo '<option value="">Sucursal</option>';
                                            $tr = $con->Listar("SELECT DISTINCT id_unico, nombre 
                                                FROM gf_sucursal 
                                                ORDER BY nombre");
                                            for ($i = 0; $i < count($tr); $i++) {
                                               echo '<option value="'.$tr[$i][0].'">'.ucwords(mb_strtolower($tr[$i][1])).'</option>'; 
                                            }
                                        ?>
                                    </select>
                                    </div>
                                    <div class="form-group" style="margin-top: -10px;">
                                        <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicial:</label>
                                        <input class="col-md-3 col-lg-3 input-sm" style="width: 250px;" type="text" name="fechaini" id="fechaini" readonly="true" >
                                    </div>
                                    <div class="form-group" style="margin-top: -10px;">
                                        <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Final:</label>
                                        <input class="col-md-3 col-lg-3 input-sm" style="width: 250px;" type="text" name="fechafin" id="fechafin" readonly="true">
                                    </div>
                                    <div class="col-sm-1" style="margin-left:480px">
                                        <button type ="button" id="buscar" class="btn sombra btn-primary" title="Imprimir">Listar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <script>
                            $("#buscar").click(function(){
                                if($("#sucursal").val()!=""){
                                    var s  = $("#sucursal").val();
                                    var fi = $("#fechaini").val();
                                    var ff = $("#fechafin").val();
                                    document.location ='LISTAR_GU_RECAUDOS.php?s='+s+'&fi='+fi+'&ff='+ff;
                                }
                            })
                        </script>
                        <?php }  else { 
                            if(!empty($_GET['fi'])){
                                $fi = fechaC($_GET['fi']);
                                if(!empty($_GET['ff'])){
                                    $ff = fechaC($_GET['ff']);
                                }else {
                                    $ff =date('YY-mm-dd');
                                }
                                $row = $con->Listar("SELECT id_unico, 
                                        DATE_FORMAT(fecha_recaudo, '%d/%m/%Y') as fecha_recaudo, 
                                        cuenta_recaudo, 
                                        comparendo, 
                                        identificacion, 
                                        divipo, 
                                        municipio, 
                                        departamento, 
                                        tipo_recaudo,
                                        valor_recaudo,
                                        valor_tercero 
                                    FROM gu_recaudos  
                                    WHERE sucursal = ".$_GET['s']." 
                                    AND fecha_recaudo BETWEEN '$fi' AND '$ff'");
                            }else{
                                $row = $con->Listar("SELECT id_unico, 
                                        DATE_FORMAT(fecha_recaudo, '%d/%m/%Y') as fecha_recaudo, 
                                        cuenta_recaudo, 
                                        comparendo, 
                                        identificacion, 
                                        divipo, 
                                        municipio, 
                                        departamento, 
                                        tipo_recaudo,
                                        valor_recaudo,
                                        valor_tercero 
                                    FROM gu_recaudos 
                                    WHERE sucursal = ".$_GET['s']);
                            }
                            ?>
                        <!---------- Si Seleccion Sucursal----------------!-->
                        <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;margin-top:-10px;">
                            <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
                                <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <td style="display: none;">Identificador</td>
                                            <td width="7%" class="cabeza"></td>               
                                            <td class="cabeza"><strong>Fecha Recaudo</strong></td>
                                            <td class="cabeza"><strong>Cuenta Recaudo</strong></td>
                                            <td class="cabeza"><strong>Comparendo</strong></td>
                                            <td class="cabeza"><strong>Cédula</strong></td>
                                            <td class="cabeza"><strong>Divipo</strong></td>
                                            <td class="cabeza"><strong>Municipio</strong></td>
                                            <td class="cabeza"><strong>Departamento</strong></td>
                                            <td class="cabeza"><strong>Tipo Recaudo</strong></td>
                                            <td class="cabeza"><strong>Valor Recaudo</strong></td>
                                            <td class="cabeza"><strong>Valor Tercero</strong></td>

                                        </tr>
                                        <tr>
                                            <th class="cabeza" style="display: none;">Identificador</th>
                                            <th width="7%"></th>               
                                            <th class="cabeza">Fecha Recaudo</th>
                                            <th class="cabeza">Cuenta Recaudo</th>
                                            <th class="cabeza">Comparendo</th>
                                            <th class="cabeza">Cédula</th>
                                            <th class="cabeza">Divipo</th>
                                            <th class="cabeza">Municipio</th>
                                            <th class="cabeza">Departamento</th>
                                            <th class="cabeza">Tipo Recaudo</th>
                                            <th class="cabeza">Valor Recaudo</th>
                                            <th class="cabeza">Valor Tercero</th>
                                        </tr>
                                    </thead>    
                                    <tbody>
                                        <?php for ($i = 0; $i < count($row); $i++) { ?>
                                        <tr>
                                            <td style="display: none;"><?php echo $row[$i][0]?></td>
                                            <td>
                                                <a href="#" onclick="javascript:eliminar(<?php echo $row[$i][0];?>);">
                                                    <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                </a>
                                            </td>                                        
                                            <td class="campos"><?php echo $row[$i]['fecha_recaudo']?></td>                
                                            <td class="campos"><?php echo $row[$i]['cuenta_recaudo']?></td>                
                                            <td class="campos"><?php echo $row[$i]['comparendo']?></td>                
                                            <td class="campos"><?php echo $row[$i]['identificacion']?></td>                
                                            <td class="campos"><?php echo $row[$i]['divipo']?></td>     
                                            <td class="campos"><?php echo $row[$i]['municipio']?></td>                
                                            <td class="campos"><?php echo $row[$i]['departamento']?></td>
                                            <td class="campos"><?php echo $row[$i]['tipo_recaudo']?></td>
                                            <td class="campos"><?php echo number_format($row[$i]['valor_recaudo'],2,',','.')?></td>
                                            <td class="campos"><?php echo number_format($row[$i]['valor_tercero'],2,',','.')?></td>
                                            

                                        </tr>
                                        <?php }
                                        ?>
                                    </tbody>
                                </table>
                                <div align="right">
                                    <br/>
                                    <button onclick="location.href='LISTAR_GU_RECAUDOS.php'" style="margin-left:0px;" type="button"  class="btn sombra btn-primary" title="Nuevo"><i class="glyphicon glyphicon-plus" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    
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
          <p>¿Desea eliminar el registro seleccionado?</p>
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
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/select2.js"></script>
    <script type="text/javascript"> 
        $("#sucursal").select2();

    </script>
    <script type="text/javascript">
      function eliminar(id)
      {
         var result = '';
         $("#myModal").modal('show');
         $("#ver").click(function(){
              $("#mymodal").modal('hide');
              var form_data = {action:4, id:id};
              $.ajax({
                  type:"POST",
                  url:"jsonPptal/gu_ComparendosJson.php",
                  data: form_data,
                  success: function (data) {
                      console.log(data);
                    result = data;
                    if(result==1) { 
                        $("#myModal1").modal('show');
                        $("#ver1").click(function(){
                          document.location.reload();   
                        })
                    } else { 
                        $("#myModal2").modal('show');
                        $("#ver2").click(function(){
                          document.location.reload();   
                        })
                    }
                  }
              });
          });
      }
    </script>
    </body>
</html>