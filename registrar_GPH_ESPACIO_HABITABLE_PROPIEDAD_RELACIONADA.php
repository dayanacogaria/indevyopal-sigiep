<?php
#

require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
#session_start();
@$id = $_GET['id'];
@$anno = $_SESSION['anno'];
//
@$tipo = $_GET['tipo'];

?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script type="text/javascript" src="js/bsn.AutoSuggest_2.1.3.js" charset="utf-8"></script>

<link rel="stylesheet" href="css/autosuggest_inquisitor.css" type="text/css" media="screen" charset="utf-8" />

<style >
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
                                    font-family: Arial;}
    </style>
    <script type="text/javascript" src="../jquery.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#enviar').click(function () {
                
                var tp = $('#sltTipo').val();
                var id_eh = $('#sltEspacio2').val();
                //vehiclulo
                var pl = $('#placa').val();
                var mar = $('#marca').val();
                var colr = $('#color').val();
                //mascota
                var esp = $('#especie').val();
                var raz = $('#raza').val();

                if(tp==='' || id_eh===''){
                    $("#myModalcomp2").modal('show');
                    
                }else{
                    if(pl==='' || esp===''){
                        $("#myModalcomp").modal('show');
                    }else{
                        if(tp==='1'){
                            //es vehiculo
                            window.location = 'json/registrarEspacioHabitablePropiedadJson.php?tipo='+tp+'&id_espacio=' + id_eh + '&placa=' + pl +'&marca=' + mar + '&color=' + colr;    
                        }else if(tp==='2'){
                            //es mascota
                            window.location = 'json/registrarEspacioHabitablePropiedadJson.php?tipo='+tp+'&id_espacio=' + id_eh + '&especie=' + esp +'&raza=' + raz; 
                        }
                        
                        
                    }                    
                }
                
                return false;
            });
        });
    </script>


    <script src="js/jquery-ui.js"></script>

    <script>

        $(function () {
            var fecha = new Date();
            var dia = fecha.getDate();
            var mes = fecha.getMonth() + 1;
            if (dia < 10) {
                dia = "0" + dia;
            }
            if (mes < 10) {
                mes = "0" + mes;
            }
            var fecAct = dia + "/" + mes + "/" + fecha.getFullYear();
            $.datepicker.regional['es'] = {
                closeText: 'Cerrar',
                prevText: 'Anterior',
                nextText: 'Siguiente',
                currentText: 'Hoy',
                monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                monthNamesShort: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
                dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
                dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: '',
                changeYear: true
            };
            $.datepicker.setDefaults($.datepicker.regional['es']);

            $("#sltFechaE").datepicker({changeMonth: true, }).val();
            $("#sltFechaS").datepicker({changeMonth: true, }).val();


        });
    </script>

    <title>Registrar Espacio Habitable Propiedad Relacionada</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
    <div class="container-fluid text-center">
    <div class="row content">
<?php require_once 'menu.php'; ?>
        <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
            <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Registrar Espacio Habitable Propiedad Relacionada</h2>

            <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 10px">
                <form id="formid" name="formid" class="form-horizontal" method="POST"  enctype="multipart/form-data" >
                    <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                    <!-------------------------------------------------------------------------------------------------------------------- -->
                    

                    <div class="form-group form-inline" style="margin-top:-20px">                        
                        <!--tipo relacion-->
                        <?php
                        if($tipo==''){
                             $tip = "SELECT * FROM gph_tipo_relacion order by nombre asc";
                                $t[0] = "";
                                $t[1] = "Tipo Relación";     
                        }else{
                            $tip = "SELECT * from gph_tipo_relacion where id_unico!=$tipo order by nombre asc";
                            $tx="SELECT * from gph_tipo_relacion where id_unico= $tipo order by nombre asc";
                            $tipoa = $mysqli->query($tx);
                            $t = mysqli_fetch_row($tipoa);
                        }
                       

                        $tipon = $mysqli->query($tip);
                        ?> 
                        <label for="sltTipo" class="col-sm-2 control-label">
                            <strong class="obligado">*</strong>Tipo Relación:
                        </label>
                        <select   name="sltTipo" id="sltTipo" title="Seleccione Tipo Relación" 
                                  style="width: 150px;height: 30px" class="form-control col-sm-2" required>
                            <option value="<?php echo $t[0]; ?>"><?php echo $t[1]; ?></option>

                            <?php
                            while ($rowEV = mysqli_fetch_row($tipon)) {
                                echo "<option  value=" . $rowEV[0] . ">" . $rowEV[1] . "</option >";
                            }
                            ?>                                                       
                        </select>

                        <script>
                            
                                  $("#sltTipo").change(function(){
                                    var ti = $('#sltTipo').val();
                                                                     
                                    window.location='registrar_GPH_ESPACIO_HABITABLE_PROPIEDAD_RELACIONADA.php?tipo='+ti;
                                  });

                        </script>

                        <!--Espacio Habitable-->
                                    <label for="sltEspacio" class="col-sm-2 control-label">
                                          <strong class="obligado">*</strong>Espacio Habitable:
                                    </label>
                                    
                                        <input style="width: 390px;height: 30px" type="text" id="sltEspacio" value="" name="sltEspacio" title="Ingrese Espacio Habitable" 
                                        class="form-control col-sm-1" placeholder="Espacio Habitable"/> 
                                        <input style="width: 140px;height: 30px" type="hidden" id="sltEspacio2" value="" name="sltEspacio2" /> 
                                   
                              <script type="text/javascript">
                                            
                                            var options = {
                                                    
                                                    script:"test_apartamento.php?json=true&limit=8&",
                                                    varname:"input",
                                                    json:true,
                                                    shownoresults:false,
                                                    maxresults:10,
                                                    callback: function (obj) { 
                                                        document.getElementById('sltEspacio').value = obj.value;
                                                        document.getElementById('sltEspacio2').value = obj.id;
                                                       // window.location='registrar_GA_ACUERDO.php?sltTiposelect='+tp+'&sltContS='+obj.id+'&fecha_acuerdo='+fch+'&dis="mostrar"';
                                                    }
                                            };
                                             var as_json = new bsn.AutoSuggest('sltEspacio', options);
                                              
                                                
                                            

                                    </script>  

                    </div>
                    

                    <?php

                    if($tipo=='1'){
                        ?>
                        <!--1 es vehiculo-->
                        <div class="form-group form-inline" >  
                        <strong style=" font-size: 12px; color:#1075C1; margin-left: 52px; margin-right: 5px;margin-top:10px; margin-bottom: 10px;">Detalle Vehiculo</strong>
                    </div>
                        <div class="form-group form-inline" style="margin-top:15px">    
                            <!--Placa-->
                            <label for="placa" class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Placa:
                            </label>
                            <input style="width: 150px;height: 30px" type="text" id="placa" value="" name="placa" title="Ingrese Placa" class="form-control col-sm-1" placeholder="Placa" onkeyup="javascript:this.value=this.value.toUpperCase();"/> 

                            <!--Marca-->
                            <label for="marca" class="col-sm-1 control-label">
                                <strong class="obligado"></strong>Marca:
                            </label>
                            <input style="width: 200px;height: 30px" type="text" id="marca" value="" name="marca" title="Ingrese Marca" class="form-control col-sm-1" placeholder="Marca" onkeyup="javascript:this.value=this.value.toUpperCase();"/> 
                            
                            <!--Color-->
                            <label for="color" class="col-sm-1 control-label">
                                <strong class="obligado"></strong>Color:
                            </label>
                            <input style="width: 190px;height: 30px" type="text" id="color" value="" name="color" title="Ingrese Color" class="form-control col-sm-1" placeholder="Color" onkeyup="javascript:this.value=this.value.toUpperCase();"/> 

                        </div>
                        <?php
                    }else if($tipo=='2'){
                        ?>
                        <!--2 es mascota-->
                        <div class="form-group form-inline" >  
                            <strong style=" font-size: 12px; color:#1075C1; margin-left: 52px; margin-right: 5px;margin-top:10px; margin-bottom: 10px;">Detalle Mascota</strong>
                        </div>
                        <div class="form-group form-inline" style="margin-top:15px">    
                            <!--Especie-->
                            <label for="especie" class="col-sm-2 control-label">
                                <strong class="obligado">*</strong>Especie:
                            </label>
                            <input style="width: 250px;height: 30px" type="text" id="especie" value="" name="especie" title="Ingrese Especie" class="form-control col-sm-1" placeholder="Especie" onkeyup="javascript:this.value=this.value.toUpperCase();"/> 

                            <!--Raza-->
                            <label for="raza" class="col-sm-1 control-label">
                                <strong class="obligado"></strong>Raza:
                            </label>
                            <input style="width: 363px;height: 30px" type="text" id="raza" value="" name="raza" title="Ingrese Raza" class="form-control col-sm-1" placeholder="Raza" onkeyup="javascript:this.value=this.value.toUpperCase();"/> 
                            
                        </div>
                        <?php
                    }
                    ?>


                    <!-- ----------------------------------------------------------------------  -->

                    <div class="form-group form-inline" style="margin-top:5px; display:<?php echo $a ?>">
    
                    </div>
                    


                    <!--------------------------------------------------------------------------------------------------- -->                              

                    <div class="form-group form-inline" style="margin-top:-5px">                            

                        <!-- <label for="No" class="col-sm-2 control-label"></label>-->
                        <button id="enviar" type="submit" class="btn btn-primary sombra col-sm-1" 
                                style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 800px ; ">
                            <li class="glyphicon glyphicon-floppy-disk"></li></button>                              
                    </div>
                </form>
                
                   
            </div>


            <!---------------------------------------------------------------------------------------------------->                        

            <!-- </div> -->   

        </div>
        <div class="col-sm-8 col-sm-2" style="margin-top:-22px">
            <!--<table class="tablaC table-condensed text-center" align="center">
                    <thead>
                        <tr>
                            <tr>                                        
                                <th>
                                    <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                                </th>
                            </tr>
                    </thead>
                    <tbody>
                        <tr>                                    
                            <td>
                                <a class="btn btn-primary btnInfo" href="registrar_GA_FACTURA_ACUERDO.php?nacuerdo=2&sltTiposelect=1">FACTURA ACUERDO</a>
                            </td>
                        </tr>
                        <tr>                                    
                            <td>
                               <a class="btn btn-primary btnInfo" href="registrar_GN_CAUSA_RETIRO.php">CAUSA RETIRO</a>
                            </td>
                        </tr>                                                        
                        <!--<tr>   
                        no es necesario mostrar el estado porque solo pueden ser dos vinculacion retiro                                 
                            <td>
                                <a class="btn btn-primary btnInfo" href="registrar_GN_ESTADO_VINCULACION_RETIRO.php">ESTADO</a>
                            </td>
                        </tr>                                                      
                        <tr>                                    
                            <td>
                               <a class="btn btn-primary btnInfo" href="registrar_GN_TIPO_VINCULACION.php">TIPO VINCULACION</a>
                            </td>
                        </tr>
            </table>-->
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
                    <p>¿Desea eliminar el registro seleccionado de Vinculación Retiro?</p>
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
    <div class="modal fade" id="myModalcomp" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Asegurese que los campos obligatorios esten diligenciados.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver8"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModalcomp2" role="dialog" align="center">
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Asegurese que este seleccionado el espacio habitable o el tipo de relación</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver8"  class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
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
                      $("#ver").click(function () {
                          $("#mymodal").modal('hide');
                          $.ajax({
                              type: "GET",
                              url: "json/eliminarVinculacionRetiroJson.php?id=" + id,
                              success: function (data) {
                                  result = JSON.parse(data);
                                  if (result == true)
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

        $('#ver1').click(function () {
            reload();

            window.history.go(-1);
        });

    </script>

    <script type="text/javascript">
        $('#ver2').click(function () {
            window.history.go(-1);
        });
    </script>

</div>
<script>
    function fechaInicial() {
        var fechain = document.getElementById('sltFechaA').value;
        var fechafi = document.getElementById('sltFecha').value;
        var fi = document.getElementById("sltFechaA");
        fi.disabled = false;


        $("#sltFecha").datepicker("destroy");
        $("#sltFecha").datepicker({changeMonth: true, minDate: fechain});

    }
</script>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript">
$("#sltVinculacion").select2();
</script>

<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript">
    $("#sltCausa").select2();
</script>
<script type="text/javascript" src="js/select2.js"></script>
<script type="text/javascript">
    $("#sltTipo").select2();
    //sltBanco
    $("#sltTipoV").select2();
    //$("#sltContribuyente").select2();
</script>
<?php
if (empty($cont_sel)) {
    
    ?>
    <script>
        $('#txtNumeroD').prop("disabled",false);
        $('#sltTipo').prop("disabled",false);
        $('#txtNombreC').prop("disabled",false);
        $('#txtPlaca').prop("disabled",false);
        $('#sltTipoV').prop("disabled",false);
        $('#sltApartamento').prop("disabled",false);
        $('#sltFechaE').prop("disabled",false);
        $('#txtHoraE').prop("disabled",false);
        $('#sltFechaS').prop("disabled",false);
        $('#txtHoraS').prop("disabled",false);
        $('#txtObser').prop("disabled",false);
        $('#sltParqueadero').prop("disabled",false);
    </script>
    <?php
}else{
    ?>
    <script>
        $('#txtNumeroD').prop("disabled",true);
        $('#sltTipo').prop("disabled",true);
        $('#txtNombreC').prop("disabled",true);
        $('#txtPlaca').prop("disabled",false);
        $('#sltTipoV').prop("disabled",false);
        $('#sltApartamento').prop("disabled",false);
        $('#sltFechaE').prop("disabled",false);
        $('#txtHoraE').prop("disabled",false);
        $('#sltFechaS').prop("disabled",false);
        $('#txtHoraS').prop("disabled",false);
        $('#txtObser').prop("disabled",false);
        $('#sltParqueadero').prop("disabled",false);
    </script>
    <?php
}
?>
</body>
</html>