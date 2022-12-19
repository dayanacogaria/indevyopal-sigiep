<?php 
################################################################################################################################
# Fecha de creación 13/02/2017
# Creado por Jhon Numpaque
################################################################################################################################
# Modificaciones
# Fecha         :   21/02/2017
# Hora          :   02:47
# Modifico      :   Jhon Numpaque
# Descripción   :   Se verifico validación de campos vacios
################################################################################################################################
# 14/02/2017 | Jhon Numpaque
# Descripción: Se incluyo campo valor
 ?>
<div class="modal fade modalEditar" id="modalEditD" role="dialog" align="center" >
    <div class="modal-dialog">
        <?php 
            require_once('Conexion/conexion.php');
            #Declaración de variables
            $id = "";
            $tipoP = "";
            $fechaP = "";
            $tipoDoc = "";
            $ndo = "";
            $desc = "";
            $valor = "";
            $fechaC = "";
            $conciliado = "";
            #Validación de id no vacio
            if(!empty($_POST['id'])){
                $id = $_POST['id'];
                $tipoP = $_POST['tipoP'];
                $fechaP = $_POST['fechaP'];
                $tipoDoc = $_POST['tipoDoc'];
                $ndo = $_POST['numDoc'];
                $desc = $_POST['desc'];
                $valor = $_POST['valor'];
                $fechaCP = $_POST['fechac'];
                $fechaC = date('d/m/Y');
                $conciliado = $_POST['conciliado'];
            } 
        ?>
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">          
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
                <div class="col-sm-offset-11" style="margin-top:-30px;margin-right: -30px">
                    <button type="button" class="btn btn-xs" style="color: #000;" data-dismiss="modal" ><li class="glyphicon glyphicon-remove"></li></button>
                </div>
            </div>            
            <div class="modal-body" style="margin-top: 8px">
                <div class="row form-horizontal">
                    <input type="hidden" id="id" name="id" value="<?php echo $id; ?>">
                    <div class="form-group text-left">
                        <label for="sltTipoPartida" class="control-label col-sm-4">
                            Tipo Partida:
                        </label>
                        <select name="sltTipoPtda" id="sltTipoPtda" class="form-control col-sm-1 detalle" style="width: 250px;height: 30px" title="Tipo Partida" >
                            <?php 
                            if(!empty($tipoP)){
                                $sqlT = "SELECT id_unico,nombre FROM gf_tipo_partida WHERE id_unico=$tipoP";
                                $resultT = $mysqli->query($sqlT);
                                $tp = mysqli_fetch_row($resultT);
                                echo "<option value=".$tp[0].">".ucwords(strtolower($tp[1]))."</option>";
                                $sqlT1 = "SELECT id_unico,nombre FROM gf_tipo_partida WHERE id_unico!=$tipoP";
                                $resultT1 = $mysqli->query($sqlT1);
                                while($tp1 = mysqli_fetch_row($resultT1)){
                                    echo "<option value=".$tp1[0].">".ucwords(strtolower($tp1[1]))."</option>";
                                }                                
                            }
                             ?>                            
                        </select>
                    </div>
                    <div class="form-group text-left">
                        <label for="txtFecha" class="control-label col-sm-4">
                            Fecha Partida:
                        </label>
                        <input type="text" class="form-control col-sm-1 detalle" value="<?php echo $fechaP; ?>" id="txtFechaPtda" name="txtFechaPtda" title="Fecha Partida" style="width: 250px;height: 30px" required>
                         <script type="text/javascript">
                            $("#txtFechaPtda").change(function()
                            {
                              var fecha = $("#txtFechaPtda").val();
                                var form_data = { case: 4, fecha:fecha};
                                $.ajax({
                                  type: "POST",
                                  url: "jsonSistema/consultas.php",
                                  data: form_data,
                                  success: function(response)
                                  { 
                                      if(response ==1){
                                          $("#periodoC1").modal('show');
                                          $("#txtFechaPtda").val("").focus();

                                      } else {
                                          
                                      }
                                  }
                                });   


                            });
                        </script>
                    </div>
                    <div class="form-group text-left">
                        <label for="txtFecha" class="control-label col-sm-4">
                            Fecha Conciliación:
                        </label>
                        <input type="text" class="form-control col-sm-1 detalle" value="<?php if(empty($conciliado)){echo $fechaC;}else{echo $fechaCP;} ?>" id="txtFechaC" name="txtFechaC" title="Fecha Partida" style="width: 250px;height: 30px" required>
                        <script type="text/javascript">
                            $("#txtFechaC").change(function()
                            {
                              var fecha = $("#txtFechaC").val();
                                var form_data = { case: 4, fecha:fecha};
                                $.ajax({
                                  type: "POST",
                                  url: "jsonSistema/consultas.php",
                                  data: form_data,
                                  success: function(response)
                                  { 
                                      if(response ==1){
                                          $("#periodoC1").modal('show');
                                          $("#txtFechaC").val("").focus();

                                      } else {
                                          
                                      }
                                  }
                                });   


                            });
                        </script>
                    </div>
                    <div class="form-group text-left">
                        <label for="sltTipoDocP" class="col-sm-4 control-label">
                            Tipo Documento:
                        </label>
                        <select name="sltTipoDocP" id="sltTipoDocP" class="form-control col-sm-1 detalle" title="Seleccione tipo documento" style="width: 250px;height: 30px" required>
                            <?php
                            if(!empty($tipoDoc)){
                                $sqlTD1 = "SELECT id_unico,nombre FROM gf_tipo_documento WHERE id_unico = $tipoDoc ORDER BY id_unico ASC";
                                $resultTD1 = $mysqli->query($sqlTD1);
                                $rowTD1 = mysqli_fetch_row($resultTD1);
                                echo "<option value=".$rowTD1[0].">".ucwords(strtolower($rowTD1[1]))."</option>";
                                
                                $sqlTD = "SELECT id_unico,nombre FROM gf_tipo_documento WHERE id_unico != $tipoDoc ORDER BY id_unico ASC";
                                $resultTD = $mysqli->query($sqlTD);
                                while($rowTD = mysqli_fetch_row($resultTD)){
                                    echo "<option value=".$rowTD[0].">".ucwords(strtolower($rowTD[1]))."</option>";
                                }                                
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group text-left">
                        <label for="txtDoc" class="control-label col-sm-4">
                            No Documento:
                        </label>
                        <input type="text" id="txtNumDocP" name="txtNumDocP" class="form-control col-sm-1 detalle" value="<?php echo $ndo; ?>" title="Número de documento" style="width: 250px;height: 30px" placeholder="Numero documento" required>
                    </div>
                    <div class="form-group text-left">
                        <label for="txtValoP" class="control-label col-sm-4">
                            Valor:
                        </label>
                        <input type="text" id="txtValorP" onkeypress="return txtValida(event,'decimales')" name="txtValorP" title="Valor" class="form-control col-sm-1 detalle" style="width: 250px;height: 30px" value="<?php echo $valor; ?>">
                    </div>
                    <div class="form-group text-left">
                        <label for="optConciliar" class="control-label col-sm-4">
                            Conciliado:
                        </label>
                        <div class="col-sm-2">
                            <?php 
                            switch ($conciliado) {
                                case 1: ?>
                                    SI<input type="radio" class="detalle radio-inline" id="optConC1" name="optConciliado" title="Es conciliado" value="1" checked>
                                    NO<input type="radio" class="detalle radio-inline" id="optConC2" name="optConciliado" title="No se concilió" value="NULL">
                                <?php break;
                                
                                case 2: ?>
                                    SI<input type="radio" class="detalle radio-inline" id="optConC1" name="optConciliado" title="Es conciliado" value="1">
                                    NO<input type="radio" class="detalle radio-inline" id="optConC2" name="optConciliado" title="No se concilió" value="NULL" checked>
                                <?php break;

                                default : ?>
                                    SI<input type="radio" class="detalle radio-inline" id="optConC1" name="optConciliado" title="Es conciliado" value="1">
                                    NO<input type="radio" class="detalle radio-inline" id="optConC2" name="optConciliado" title="No se concilió" value="NULL" checked>
                                <?php break;
                            }
                             ?>
                        </div>
                    </div>
                    <div class="form-group text-left">
                        <label for="txtDes" class="control-label col-sm-4">
                            Descripción:
                        </label>
                        <textarea name="txtDescripcionP" id="txtDescripcionP" title="Descripción" class="form-control col-ms-1" style="width: 250px;height: 100px"><?php echo $desc; ?></textarea>
                    </div>
                </div>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="btnModificarDetalle" onclick="return guardarCambios()" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Guardar</button>
            </div>
        </div>
    </div>
</div>
<script>
    $("#sltTipoPtda").select2({
        placeholder: "Tipo Partida",
        allowClear: true 
    });
    $("#sltTipoDocP").select2({
        allowClear: true
    });
</script>
<script type="text/javascript">
/*Función para ejecutar el datapicker en en el campo fecha*/
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
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $("#txtFechaPtda").datepicker({changeMonth: true}).val();
    $("#txtFechaC").datepicker({changeMonth: true}).val();
});
//Función para guardar los cambios
function guardarCambios(){
    //Captura de variables
    var id = $("#id").val();
    var sltTipoPtda = $("#sltTipoPtda").val();
    var txtFechaP = $("#txtFechaPtda").val();
    var sltTipoDocP = $("#sltTipoDocP").val();
    var txtNumDocP = $("#txtNumDocP").val();
    var txtDescripcionP = $("#txtDescripcionP").val();
    var action = 'editar';
    var txtValorP = $("#txtValorP").val();
    var fechac = $("#txtFechaC").val();
    var con = "";
    if($("#optConC1").is(':checked')){
        con = $("#optConC1").val();
    }else if($("#optConC2").is(':checked')){
        con = $("#optConC2").val();
    }    
    //Array con la carga de los valores
    var form_data = {
        id:id,
        sltTipoPtda:sltTipoPtda,
        txtFechaP:txtFechaP,
        sltTipoDocP:sltTipoDocP,
        txtNumDocP:txtNumDocP,
        txtDescripcionP:txtDescripcionP,
        action:action,
        txtValorP:txtValorP,
        optConciliado:con,
        txtFechaPCon:fechac
    };
    var result = '';
    //Envio por ajax
    $.ajax({
        type:'POST',
        url:'json/registrarGFDetallePartidaC.php',
        data:form_data,
        success: function(data){            
            result = JSON.parse(data);
            if(result==true){
                $("#modalModificar").modal('show');
            }else{
                $("#modalNoMod").modal('show');
            }
        }
    });

}
</script>
<div class="modal fade" id="periodoC1" role="dialog" align="center" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Periodo ya ha sido cerrado, escoja nuevamente la fecha</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="periodoCA" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >
                Aceptar
                </button>
            </div>
        </div>
    </div>
</div>