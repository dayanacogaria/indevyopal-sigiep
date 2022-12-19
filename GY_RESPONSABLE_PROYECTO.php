<?php

    require_once './Conexion/conexion.php';
    require_once('Conexion/ConexionPDO.php');
    require_once './head_listar.php';

    $con        = new ConexionPDO();
    $anno       = $_SESSION['anno'];
    $compania   = $_SESSION['compania'];
    #session_start();
    @$idPr = $_REQUEST['id'];
    
                                     
    $proyecto = "SELECT  id_unico,
                        titulo
                    FROM gy_proyecto
                    WHERE md5(id_unico) = '$idPr' ";
    $proyec = $mysqli->query($proyecto);
    $P = mysqli_fetch_row($proyec);
?>


        <title>Responsable Proyecto</title>
        <script src="dist/jquery.validate.js"></script>
        <!-- Librerias de carga para el datapicker -->
        <link rel="stylesheet" href="css/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <!-- select2 -->
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
        
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
        <script src="js/jquery-ui.js"></script>
        <style>
            /*Estilos tabla*/
            table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
            table.dataTable tbody td,table.dataTable tbody td{padding:1px}
            .dataTables_wrapper .ui-toolbar{padding:2px}
            /*Campos dinamicos*/
            .campoD:focus {
                border-color: #66afe9;
                outline: 0;            
                box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
            }
            .campoD:hover{
                cursor: pointer;
            }
            /*Campos dinamicos label*/
            .valorLabel{
                font-size: 10px;
            }
            .valorLabel:hover{
                cursor: pointer;
                color:#1155CC;
            }
            /*td de la tabla*/
            .campos{
                padding: 0px;
                font-size: 10px
            }
            /*cuerpo*/
            body{
                font-size: 10px;
                font-family: Arial;
            }

            .client-form input[type="text"]{
                width: 100%;
            }
            .client-form select{
                width: 100%;
            }

            .client-form input[type="file"]{
                width: 100%;
            }

        </style>  
        <style >
    
            label #sltTer-error, #sltTipo-error   {
                display: block;
                color: #155180;
                font-weight: normal;
                font-style: italic;
                font-size: 10px
            }

            body{
                font-size: 11px;
            } 
            table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
            table.dataTable tbody td,table.dataTable tbody td{padding:1px}
            .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
            font-family: Arial;}
        </style>
        <script>

            $().ready(function() {
              var validator = $("#formM").validate({
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


                $("#fechaini").datepicker({changeMonth: true,})
                $("#fechafin").datepicker({changeMonth: true});


            });
        </script>
    </head>
    
    <body >   

        <div class="container-fluid text-left">
            <div class="row content">
                <?php require_once ('menu.php'); ?>
                <div class="col-sm-8" style="margin-top: 0px">
                    <h2 id="forma-titulo3" align="center" style="margin-top:0px; margin-right: 4px; margin-left: -10px;">Responsable Proyecto</h2>
                    <a href="<?php echo 'modificar_GY_PROYECTO.php?id='.$idPr ;?>" class="glyphicon glyphicon-circle-arrow-left" style="display:<?php echo $a?>;margin-top:-13px; margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                    <h5 id="forma-titulo3a" align="center" style="margin-top:-20px; width:85%;  display:<?php echo $a?>; margin-bottom: 10px; margin-right: 10px; margin-left: 6%;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo "Proyecto: ".ucwords((mb_strtolower($P[1])));?></h5> 
                    <div class="client-form contenedorForma" style="margin-top: -7px;font-size: 13px;  width: 100%; float: right;">
                        <form name="formM" id="formM" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="javascript:registrar()">
                            <p align="center" style="margin-bottom: 25px; margin-top: 0px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>                                         
                            <div class="form-group col-sm-12" style="margin-top: 1%">
                                <input type="hidden" id="txtIP" name="txtIP" value="<?php echo $P[0] ?>">
                                <label for="sltTer" class="col-sm-2 col-md-2 col-lg-2 control-label text-right" style="margin-left: -1%" ><strong class="obligado">*</strong>Tercero:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <select id="sltTer" name="sltTer" class="form-control" title="Seleccione un Tercero" required >
                                        <option value="">Tercero</option>
                                        <?php
                                            $tercero = "SELECT tr.id_unico,
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
                                                         tr.apellidodos)) AS NOMBRE
                                                    FROM gf_tercero tr
                                                    WHERE tr.compania = '$compania'";
                                            $ter = $mysqli->query($tercero);
                                            while ($rowT = mysqli_fetch_row($ter)) {
                                                echo '<option value="'.$rowT[0].'">'.ucwords(mb_strtolower($rowT[1])).'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                <label for="sltTipo" class="col-sm-3 col-md-3 col-lg-3 control-label text-right" style="margin-left: -1%" ><strong class="obligado">*</strong>Tipo Responsable:</label>
                                <div class="col-sm-3 col-md-3 col-lg-3">
                                    <select id="sltTipo" name="sltTipo" class="form-control" title="Seleccione un Tipo de Responsable" required >
                                        <option value="">Tipo Responsable</option>
                                        <?php
                                            $row = $con->Listar("SELECT  id_unico, nombre
                                             FROM gf_tipo_responsable
                                             ORDER BY nombre ASC ");
                                            for ($i = 0; $i < count($row); $i++) {
                                                echo '<option value="'.$row[$i][0].'">'.ucwords(mb_strtolower($row[$i][1])).'</option>';
                                            }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="col-sm-1 col-md-1 col-lg-1">
                                    <button type="submit"  class="btn btn-primary sombra col-sm-1" style="width:40px; margin-top: 1%"><li class="glyphicon glyphicon-floppy-disk"></li></button>
                                </div>
                            </div>  
                        </form>
                    </div>
                </div>

                <!--informacion adicional-->
                <div class="col-sm-2 col-sm-2 " style="margin-top:-22px">
                    <table class="tablaC table-condensed text-center" align="center">
                        <thead>
                            <tr>
                            </tr>    
                            <tr>                                        
                                <th>
                                    <h2 class="titulo" align="center" style=" font-size:17px;">Información Adicional</h2>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>                                    
                            <td>
                                <a class="btn btn-primary btnInfo" href="registrar_GF_TIPO_RESPONSABLE.php">TIPO RESPONSABLE</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
               
                <script>
                    function registrar(){
                        //var tipoP = $("#tipoP").val();
                        // var proye = $("#ProAC").val();
                        //jsShowWindowLoad('Modificando Datos ...');
                        var formData = new FormData($("#formM")[0]);
                        $.ajax({
                            type: 'POST',
                            url: "jsonProyecto/gy_responsable_proyectoJson.php?action=2",
                            data:formData,
                            contentType: false,
                            processData: false,
                            success: function(response)
                            {
                                //jsRemoveWindowLoad();
                                console.log(response);
                                if(response==1){
                                    //$("#modalActividadP").modal("hide");
                                    $("#mensaje").html('Información Guardada Correctamente');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                       //document.location='registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='+tipoP+'&pro='+proye;
                                       document.location.reload();
                                    })
                                } else {
                                    $("#mensaje").html('No Se Ha Podido Guardar Información');
                                    $("#modalMensajes").modal("show");
                                    $("#Aceptar").click(function(){
                                        $("#modalActividadP").modal("hide");
                                        //document.location='registrar_GY_ACTIVIDAD_PROYECTO.php?tipo='+tipoP+'&pro='+proye;
                                    })
                                }
                            }
                        });
                    }
                    
                    function modificarMR(id_MR){
                        var form_data = {
                            id:id_MR
                        }

                        //Envio ajax
                        $.ajax({
                            url:'modal_Modificar_Matriz_Riesgo.php#modalMatrizR',
                            type:'POST',
                            data:form_data,
                            success: function(data,textStatus,jqXHR) {
                                $("#modalMatrizR").html(data);
                                $(".recaMat").modal('show');
                            },error: function(data,textStatus,jqXHR) {
                                alert('Error : D'+data+', status :'+textStatus+', jqXHR : '+jqXHR);
                            } 
                        });

                    }
                    
                    function eliminarM(id) {
                        $("#myModal").modal('show');
                        $("#ver").click(function(){
                            //jsShowWindowLoad('Eliminando Datos ...');
                            $("#mymodal").modal('hide');
                            var form_data = {action:1, id:id};
                            $.ajax({
                                type: 'POST',
                                url: "jsonProyecto/gy_matriz_riesgoJson.php?action=1",
                                data: form_data,
                                success: function(response) {
                                    //jsRemoveWindowLoad();
                                    console.log(response);
                                    if(response==1){
                                        $("#mensaje").html('Información Eliminada Correctamente');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                            document.location.reload();
                                        })
                                    } else if(response == 2){
                                        $("#mensaje").html('No Se Ha Podido Eliminar La Información');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                             $("#modalMensajes").modal("hide");
                                        })
                                    } else {
                                        $("#mensaje").html('No se puede eliminar la información, ya que el seguimiento posee Seguimiento(s)');
                                        $("#modalMensajes").modal("show");
                                        $("#Aceptar").click(function(){
                                             $("#modalMensajes").modal("hide");
                                        })
                                    }
                                }
                            });
                        });
                    }

                </script>
                
            
                <!--listado actividad contribuyente-->
                <div class="form-group form-inline" style="margin-top:5px;">
                    <?php 
                        require_once './menu.php'; 
                        $res_pro = "SELECT   rp.id_unico,
                                        tre.nombre,    
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
                                                 tr.apellidodos)) AS NOMBRE
                                    FROM gy_tercero_proyecto rp
                                    LEFT JOIN gf_tercero tr ON rp.id_tercero = tr.id_unico
                                    LEFT JOIN gf_tipo_responsable tre ON rp.id_tipo_responsable = tre.id_unico
                                    
                                    WHERE md5(rp.id_proyecto) = '$idPr'";
                        
                        $res = $mysqli->query($res_pro);
                    ?>
                    
                    <div class="col-sm-8 col-md-8 col-lg-8" style="margin-top: 1%;">
                        <div class="table-responsive contTabla" >
                            <table id="tabla" class=" col-sm-8 table table-striped table-condensed" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <td style="display: none;">Identificador</td>
                                        <td width="7%" class="cabeza"></td>                                        
                                                                                
                                        <td class="cabeza"><strong>Tercero</strong></td>
                                        <td class="cabeza"><strong>Tipo Responsable</strong></td>
                                    </tr>
                                    <tr>
                                        <th class="cabeza" style="display: none;">Identificador</th>
                                        <th width="7%"></th>                                        
                                        <th class="cabeza">Tercero</th>
                                        <th class="cabeza">Tipo Responsable</th>
                                    </tr>
                                </thead>    
                                <tbody>
                                    <?php 
                                        while ($row = mysqli_fetch_row($res)) { 
                                       
                                            
                                    ?>
                                            <tr>
                                                <td style="display: none;"></td>
                                                <td>
                                                    <a href="#" onclick="javascript:eliminarM(<?php echo $row[0];?>);">
                                                       <i title="Eliminar" class="glyphicon glyphicon-trash"></i>
                                                    </a>
                                                    <!--<a href="#" onclick="javascript:modificarSA(<?php echo $row[0];?>);" >
                                                        <i title="Modificar" class="glyphicon glyphicon-edit"></i>
                                                    </a>-->
                                                    <a href="modificar_GY_MATRIZ_RIESGO.php?id=<?php echo md5($row[0]);?>" >
                                                        <i title="Modificar" class="glyphicon glyphicon-edit"></i>
                                                    </a>  
                                                </td>
                                                <td class="campos" align="left" ><?php echo $row[2]?></td>                
                                                <td class="campos" align="left"><?php echo $row[1]?></td>                
                                            </tr>
                                    <?php 
                                        }
                                    ?>
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
                <div class="modal fade" id="modalMensajes" role="dialog" align="center" >
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div id="forma-modal" class="modal-header">
                                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                            </div>
                            <div class="modal-body" style="margin-top: 8px">
                                <label id="mensaje" name="mensaje" style="font-weight: normal"></label>
                            </div>
                            <div id="forma-modal" class="modal-footer">
                                <button type="button" id="Aceptar" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php require_once ('footer.php'); ?>
            <!--Script que dan estilo al formulario-->

            <!-- <script type="text/javascript" src="js/menu.js"></script>
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/bootstrap.min.js"></script>
            <!--Scrip que envia los datos para la eliminación-->
            <link rel="stylesheet" href="css/bootstrap-theme.min.css">
            <script src="js/bootstrap.min.js"></script>
            

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
            function fechaInicial(){
                var fechain= document.getElementById('sltFechaA').value;
                var fechafi= document.getElementById('sltFechaR').value;
                var fi = document.getElementById("sltFechaR");
                fi.disabled=false;
       
                $( "#sltFechaR" ).datepicker( "destroy" );
                $( "#sltFechaR" ).datepicker({ changeMonth: true, minDate: fechain});
           
            }
        </script>
        <script type="text/javascript" src="js/select2.js"></script>
        <script>
           $("#sltTer").select2();
           $("#sltTipo").select2();
        </script>
    </body>
</html>