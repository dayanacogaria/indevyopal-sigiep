<?php
#

require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
#session_start();
@$id = $_GET['id'];
@$anno = $_SESSION['anno'];
//
@$nacuerdo = $_GET['nacuerdo'];
@$tipo = $_GET['sltTiposelect'];
@$ntipo = $_GET['sltTiposelect'];
@$cont_sel = $_GET['sltContS'];
@$ncont_sel = $_GET['sltContS'];
@$fecha_ac = $_GET['fecha_acuerdo'];
@$disp = $_GET['dis'];
@$array = array();
@$nuevo=false;

$sql = "SELECT 
        dv.id_unico,
        v.numeroidentificacion,
        v.nombrecompleto,
        v.id_tipo_documento,
        ti.nombre as nom_tipo_identificacion,
        dv.placa,
        dv.id_tipoV,
        tv.nombre as tipo_vehi,
        dv.id_apartamento,
        concat(ehap.codigo,' - ',ehap.descripcion) as descripcion_apto,
        dv.fechae,
        dv.horae,
        dv.fechas,
        dv.horas,
        dv.observaciones,
        dv.id_parqueadero,
        concat(ehpq.codigo,' - ',ehpq.descripcion) as descripcion_pq,
        concat(v.numeroidentificacion,' - ',v.nombrecompleto) as visitante

        from gph_detalle_visitante dv
        left join gph_visitante v on v.id_unico=dv.id_visitante
        left join gf_tipo_identificacion ti on ti.id_unico=v.id_tipo_documento
        left join gph_tipo_vehiculo tv on tv.id_unico=dv.id_tipoV
        left join gh_espacios_habitables ehap on ehap.id_unico=dv.id_apartamento
        left join gh_espacios_habitables ehpq on ehpq.id_unico=dv.id_parqueadero
        where md5(dv.id_unico) = '$id'";

$resultado = $mysqli->query($sql);
$res = mysqli_fetch_row($resultado);

if (empty($disp)) {
    $a = "none";
} else {
    $a = "inline-block";
}
if (empty($nacuerdo)) {
    $a2 = "none";
} else {
    $a2 = "inline-block";
}
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
                
                var vist = $('#sltVisitante2').val();
                //visitante
                var ndoc = $('#txtNumeroD').val();
                var tp_doc = $('#sltTipo').val();
                var nom_C = $('#txtNombreC').val();
                //detalle visitante
                var plac = $('#txtPlaca').val();
                var tp_V = $('#sltTipoV').val();
                var apto = $('#sltApartamento2').val();
                var fe = $('#sltFechaE').val();
                var he = $('#txtHoraE').val();
                var fs = $('#sltFechaS').val();
                var hs = $('#txtHoraS').val();
                var obsr = $('#txtObser').val();
                var parq = $('#sltParqueadero2').val();
                var id_dv = $('#id_detalle').val();

                if(vist===''){
                    if(ndoc==='' || tp_doc===''||nom_C===''){
                        $("#myModalcomp2").modal('show');
                    }else{
                        if(apto==='' || fe===''||he===''){
                            $("#myModalcomp").modal('show');
                        }else{
                        window.location = 'json/modificarVisitanteJSON.php?placa=' + plac + '&tipoV=' + tp_V + 
                                '&fs='+fs+'&hs='+hs+'&observa='+obsr+'&parq='+parq+'&id_detalle='+id_dv;
                        }
                    }
                }else{
                    if(apto==='' || fe===''||he===''){
                            $("#myModalcomp").modal('show');
                        }else{
                                window.location = 'json/modificarVisitanteJSON.php?placa=' + plac + '&tipoV=' + tp_V + 
                                '&fs='+fs+'&hs='+hs+'&observa='+obsr+'&parq='+parq+'&id_detalle='+id_dv;
                        }
                }
                
                return false;
            });
        });
    </script>
<script type="text/javascript">
        $(document).ready(function () {
            $('#nuevo').click(function () {
                
                $('#sltVisitante').prop("disabled",true);
                $('#sltVisitante').val="";
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
                
                document.getElementById("sltVisitante").value = "";
                 window.location = 'registrar_GPH_VISITANTES.php';
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

    <title>Visitante</title>
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
</head>
<body>
    <div class="container-fluid text-center">
    <div class="row content">
<?php require_once 'menu.php'; ?>
        <div class="col-sm-8 col-md-8 col-lg-8 text-left" style="margin-top: 0px">
            <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Visitante</h2>
            <a href="<?php echo 'listar_GPH_VISITANTE.php';?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:8px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords(("Datos"));?></h5>
            <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 10px">
                <form id="formid" name="formid" class="form-horizontal" method="POST"  enctype="multipart/form-data" >
                    <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                    <!-------------------------------------------------------------------------------------------------------------------- -->
                    <div class="form-group form-inline" style="margin-top:-20px">
                        <!--Visitante-->
                        <label for="sltVisitante" class="col-sm-3 control-label">
                            <strong class="obligado">*</strong>Visitante:
                        </label>
                            <?php
                            if ($cont_sel == "") {

                            } else {
                                $sql_tercer="SELECT t.id_unico, 
                                            concat(t.numeroidentificacion,' - ',t.nombrecompleto) as nom_completo

                                            from gph_visitante  as t where 
                                            concat(t.numeroidentificacion,' - ',t.nombrecompleto) is not null 
                                            and  t.id_unico='$cont_sel'"; 

                                $resul = $mysqli->query($sql_tercer);
                                $terc = $rowDF = mysqli_fetch_array($resul);

                                $vlue = $terc[0];
                                $nomb = $terc[1];
                            }
                            if ($cont_sel != null) {
                                ?>
                            <input style="width: 350px;height: 30px" type="text" id="sltVisitante" value="<?php echo $nomb ?>" name="sltVisitante" title="Ingrese Visitante" 
                                   class="form-control col-sm-1" placeholder="Tercero" disabled/> 
                            <input style="width: 140px;height: 30px" type="hidden" id="sltVisitante2" value="<?php echo $vlue ?>" name="sltVisitante2" /> 
                            <input style="width: 140px;height: 30px" type="hidden" id="id_detalle" value="<?php echo $res[0] ?>" name="id_detalle" /> 
                            <?php
                            } else {
                            ?>
                            <input style="width: 350px;height: 30px" type="text" id="sltVisitante" value="" name="sltVisitante" title="Ingrese Visitante" 
                                   class="form-control col-sm-1" placeholder="Tercero" disabled/> 
                            <input style="width: 140px;height: 30px" type="hidden" id="sltVisitante2" value="" name="sltVisitante2" /> 
                           <input style="width: 140px;height: 30px" type="hidden" id="id_detalle" value="<?php echo $res[0] ?>" name="id_detalle" /> 
                            <?php
                            }
                            ?>
                        <script type="text/javascript">
                            
                            var options = {

                                script: "test_visitante.php?json=true&limit=8&",
                                varname: "input",
                                json: true,
                                shownoresults: false,
                                maxresults: 8,
                                callback: function (obj) {
                                    document.getElementById('sltVisitante').value = obj.value;
                                    document.getElementById('sltVisitante2').value = obj.id;
                                    window.location = 'registrar_GPH_VISITANTES.php?sltContS=' + obj.id+'&hab=';
                                                                       
                                }
                            };
                            var as_json = new bsn.AutoSuggest('sltVisitante', options);

                        </script>
                        <!--boton de nuevo-->
                       <!-- <div align="right"><a id="nuevo" class="btn btn-primary sombra" style=" box-shadow: 0px 2px 5px 1px gray;color: #fff; border-color: #1075C1; margin-top: -2px; margin-right: 200px;">Nuevo</a> </div>          -->
                    </div>

                    <div class="form-group form-inline" >                                     

                        <!--numero de documento-->    

                        <label for="txtNumeroD" class="col-sm-2 control-label">
                            <strong class="obligado">*</strong>Nº Documento:
                        </label>
                        <input  name="txtNumeroD" id="txtNumeroD" title="Ingrese Número Documento" 
                                type="text" style="width: 90px;height: 30px" class="form-control col-sm-1" 
                                placeholder="Número Documento" value="<?php echo $res[1] ?>" disabled required>

                        <!--tipo identificacion-->
                        <?php
                        $tip = "SELECT id_unico, nombre FROM gf_tipo_identificacion";
                        $t[0] = "";
                        $t[1] = "Tipo Documento";

                        $tipon = $mysqli->query($tip);
                        ?> 
                        <label for="sltTipo" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Tipo Documento:
                        </label>
                        <select   name="sltTipo" id="sltTipo" title="Seleccione Tipo Documento" 
                                  style="width: 150px;height: 30px" class="form-control col-sm-2" disabled required>
                            <option value="<?php echo $res[3]; ?>"><?php echo $res[4]; ?></option>
                                                      
                        </select>
                        <!--nombre completo-->    

                        <label for="txtNombreC" class="col-sm-2 control-label">
                            <strong class="obligado">*</strong>Nombre Completo:
                        </label>
                        <input  name="txtNombreC" id="txtNombreC" title="Ingrese Nombre Completo" 
                                type="text" style="width: 250px;height: 30px" class="form-control col-sm-1" 
                                placeholder="Nombre Completo" value="<?php echo $res[2] ?>" onkeyup="javascript:this.value=this.value.toUpperCase();" disabled required>   

                    </div>
                    <div class="form-group form-inline" >  
                        <strong style=" font-size: 12px; color:#1075C1; margin-left: 52px; margin-right: 5px;margin-top:10px; margin-bottom: 10px;">Detalle Visitante</strong>
                    </div>
                    <div class="form-group form-inline" >   

                        <!--placa vehiculo-->    

                        <label for="txtPlaca" class="col-sm-2 control-label">
                            <strong class="obligado"></strong>Placa Vehiculo:
                        </label>
                        <input  name="txtPlaca" id="txtPlaca" title="Ingrese Placa" 
                                type="text" style="width: 120px;height: 30px" class="form-control col-sm-1" 
                                placeholder="Placa" value="<?php echo $res[5] ?>" onkeyup="javascript:this.value=this.value.toUpperCase();" disabled required>  

                        <!--tipo vehiculo-->
                        <?php
                        if(empty($res[6])){
                            $tip = "SELECT id_unico, nombre FROM gph_tipo_vehiculo";
                            $t[0] = "";
                            $t[1] = "Tipo Vehiculo";    
                        }else{
                            $tip = "SELECT id_unico, nombre FROM gph_tipo_vehiculo where id_unico!=$res[6]";
                            $t[0] = $res[6];
                            $t[1] = $res[7];
                        }

                        $tipon = $mysqli->query($tip);
                        ?> 
                        <label for="sltTipoV" class="col-sm-1 control-label">
                            <strong class="obligado"></strong>Tipo Vehiculo:
                        </label>
                        <select   name="sltTipoV" id="sltTipoV" title="Seleccione Tipo Vehiculo" 
                                  style="width: 150px;height: 30px" class="form-control col-sm-2" disabled required>
                            <option value="<?php echo $t[0]; ?>"><?php echo $t[1]; ?></option>

                        <?php
                        while ($rowEV = mysqli_fetch_row($tipon)) {
                            echo "<option  value=" . $rowEV[0] . ">" . $rowEV[1] . "</option >";
                        }
                        ?>                                                       
                        </select>   

                        <!--Espacio Habitable-->

                        <label for="sltApartamento" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Espacio Habitable:
                        </label>

                        <input style="width: 290px;height: 30px" type="text" id="sltApartamento" value="<?php echo $res[9] ?>" name="sltApartamento" title="Ingrese Espacio Habitable" 
                               class="form-control col-sm-1" placeholder="Apartamento" disabled/> 
                        <input style="width: 140px;height: 30px" type="hidden" id="sltApartamento2" value="<?php echo $res[8] ?>" name="sltApartamento2" /> 

                        <script type="text/javascript">
                            
                            var options = {

                                script: "test_apartamento.php?json=true&limit=8&",
                                varname: "input",
                                json: true,
                                shownoresults: false,
                                maxresults: 8,
                                callback: function (obj) {
                                    document.getElementById('sltApartamento').value = obj.value;
                                    document.getElementById('sltApartamento2').value = obj.id;
                                    //window.location = 'registrar_GPH_VISITANTES.php?sltApart=' + obj.id;
                                }
                            };
                            var as_json = new bsn.AutoSuggest('sltApartamento', options);
                        </script>


                        <!--Fecha-->

                        <!----------Script para invocar Date Picker-->
                        <script type="text/javascript">
                            $(document).ready(function () {
                                $("#datepicker").datepicker();
                            });
                        </script>

                        <label for="sltFechaE" class="col-sm-2 control-label">
                            <strong class="obligado">*</strong>Fecha Entrada:
                        </label>
                        <input name="sltFechaE" id="sltFechaE" title="Ingrese Fecha Entrada" 
                               type="text" style="width: 120px;height: 30px" class="form-control col-sm-1"  
                               placeholder="Ingrese la fecha" 
                               value="<?php echo $res[10]; ?>" disabled required>  

                        <!--Hora entrada-->    

                        <label for="txtHoraE" class="col-sm-1 control-label">
                            <strong class="obligado">*</strong>Hora Entrada:
                        </label>
                        
                        <input  name="txtHoraE" id="txtHoraE" title="Ingrese Hora Entrada" 
                                type="time" style="width: 150px;height: 30px" class="form-control col-sm-1" 
                                placeholder="Hora Entrada" value="<?php echo $res[11]; ?>" disabled required>  

                        <!--Fecha Salida-->

                        <!----------Script para invocar Date Picker-->
                        <script type="text/javascript">
                            $(document).ready(function () {
                                $("#datepicker").datepicker();
                            });
                        </script>

                        <label for="sltFechaS" class="col-sm-1 control-label">
                            <strong class="obligado"></strong>Fecha Salida:
                        </label>
                        <input name="sltFechaS" id="sltFechaS" title="Ingrese Fecha Salida" 
                               type="text" style="width: 110px;height: 30px" class="form-control col-sm-1"  
                               placeholder="Ingrese la fecha" 
                               value="<?php echo $res[12] ?>" disabled required>  

                        <!--Hora Salida-->    

                        <label for="txtHoraS" class="col-sm-1 control-label">
                            <strong class="obligado"></strong>Hora Salida:
                        </label>
                        <input  name="txtHoraS" id="txtHoraS" title="Ingrese Hora Salida" 
                                type="time" style="width: 106px;height: 30px" class="form-control col-sm-1" 
                                placeholder="Hora Entrada" value="<?php echo $res[13] ?>"  disabled>  

                    </div>
                    <div class="form-group form-inline">  

                        <!--observaciones-->

                        <label for="txtObser" class="col-sm-2 control-label">
                            Observaciones:
                        </label>
                        <input  name="txtObser" id="txtObser" title="Ingrese Observaciones" 
                                type="text" style="width: 500px;height: 30px" class="form-control col-sm-1" 
                                placeholder="Observaciones" value="<?php echo $res[14] ?>" disabled>  

                    </div>
                    <div class="form-group form-inline" style="margin-left:25px;">
                        <!--Espacio Habitable-->

                        <label for="sltParqueadero" class="col-sm-2 control-label">
                            <strong class="obligado"></strong>Espacio (Parqueadero):
                        </label>

                        <input style="width: 465px;height: 30px" type="text" id="sltParqueadero" value="<?php echo $res[16] ?>" name="sltParqueadero" title="Ingrese parqueadero" 
                               class="form-control col-sm-1" placeholder="Parqueadero" disabled/> 
                        <input style="width: 200px;height: 30px" type="hidden" id="sltParqueadero2" value="<?php echo $res[15] ?>" name="sltParqueadero2" /> 

                        <script type="text/javascript">
                            
                            var options = {
                                
                                script: "test_parqueadero.php?json=true&limit=8&",
                                varname: "input",
                                json: true,
                                shownoresults: false,
                                maxresults: 8,
                                callback: function (obj) {
                                    document.getElementById('sltParqueadero').value = obj.value;
                                    document.getElementById('sltParqueadero2').value = obj.id;
                                    //window.location = 'registrar_GA_ACUERDO.php?sltTiposelect=' + tp + '&sltContS=' + obj.id + '&fecha_acuerdo=' + fch + '&dis="mostrar"';
                                }
                            };
                            var as_json = new bsn.AutoSuggest('sltParqueadero', options);
                        </script>

                    </div>
                    <!-- ----------------------------------------------------------------------  -->

                    <div class="form-group form-inline" style="margin-top:5px; display:<?php echo $a ?>">
    
                    </div>
                    


                    <!--------------------------------------------------------------------------------------------------- -->                              

                    <div class="form-group form-inline" style="margin-top:-5px">                            

                        <!-- <label for="No" class="col-sm-2 control-label"></label>-->
                        <!--<button id="enviar" type="submit" class="btn btn-primary sombra col-sm-1" 
                                style="margin-top:0px; width:40px; margin-bottom: -10px;margin-left: 800px ; ">
                            <li class="glyphicon glyphicon-floppy-disk"></li></button>-->
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
                    <p>Asegurese que este seleccionado el visitante o en su defecto este diligenciado los campos para crear un nuevo visitante.</p>
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

</body>
</html>