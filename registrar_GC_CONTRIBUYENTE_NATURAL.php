<?php 
    require_once('Conexion/conexion.php');
    require_once 'head.php';  
    $_SESSION['perfil'] = "N"; //Natural.
    $_SESSION['url'] = "registrar_GC_CONTRIBUYENTE_NATURAL.php";

    //Consultas para el listado de los diferentes combos correspondientes.
    //Tipo Identificación.
    $sqlTipoIden = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_identificacion
                    ORDER BY Nombre ASC";
    $tipoIden = $mysqli->query($sqlTipoIden);

    //Tipo Régimen.
    $sqlTipoReg = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_regimen
                    ORDER BY Nombre ASC";
    $tipoReg = $mysqli->query($sqlTipoReg);
 
?>
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
       
            });
        </script>
        <title>Registrar Contribuyente Natural</title>
    </head>
    <body>

        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top:-20px" >
                    <h2  align="center" class="tituloform">Registrar Contribuyente Natural</h2>
                    <div class="client-form contenedorForma">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarContribuyenteNaturalJson.php">
                            <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            
                            <div class="form-group" style="margin-top: -20px;">
                                <label for="tipoIden" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Identificación:</label>
                                <select name="tipoIden" id="tipoIden" class="form-control" title="Ingrese el tipo de identificación" required>
                                    <option value="">Tipo Identificación</option>
                                    <?php   while($rowTI = mysqli_fetch_row($tipoIden))
                                            {  ?>
                                                <option value="<?php echo $rowTI[0] ?>"><?php echo ucwords( (strtolower($rowTI[1]))); ?></option>
                                    <?php
                                            }  ?>
                                </select> 
                            </div>


                            <div class="form-group" style="margin-top: -20px; ">
                                <label for="noIdent" class="col-sm-5 control-label"><strong class="obligado">*</strong>Número Identificación:</label>
                                <input type="number" name="noIdent" id="noIdent" class="form-control" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event,'num')"  placeholder="Número Identificación" onblur="return existente()" required>
                            </div>

                            <div class="form-group" style="margin-top: -20px;">
                                <label for="nomUno" class="col-sm-5 control-label"><strong class="obligado">*</strong>Primer Nombre:</label>
                                <input type="text" name="nomUno" id="nomUno" class="form-control" maxlength="25" title="Ingrese el primer nombre" onkeypress="return txtValida(event,'car')" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Primer Nombre" required>
                            </div>

                            <div class="form-group" style="margin-top: -20px;">
                                <label for="nomDos" class="col-sm-5 control-label">Segundo Nombre:</label>
                                <input type="text" name="nomDos" id="nomDos" class="form-control" maxlength="25" title="Ingrese el segundo nombre" onkeypress="return txtValida(event,'car')" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Segundo Nombre" >
                            </div>

                            <div class="form-group" style="margin-top: -20px;">
                                <label for="apellUno" class="col-sm-5 control-label"><strong class="obligado">*</strong>Primer Apellido:</label>
                                <input type="text" name="apellUno" id="apellUno" class="form-control" maxlength="30" title="Ingrese el primer apellido" onkeypress="return txtValida(event,'car')" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Primer Apellido" required>
                            </div>

                            <div class="form-group" style="margin-top: -20px;">
                                <label for="apellDos" class="col-sm-5 control-label">Segundo Apellido:</label>
                                <input type="text" name="apellDos" id="apellDos" class="form-control" maxlength="30" title="Ingrese el segundo apellido" onkeypress="return txtValida(event,'car')" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Segundo Apellido" >
                            </div>
                            
                            <div class="form-group" style="margin-top: -20px;">
                                <label for="txtCodMat" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código Matrícula:</label>
                                <input type="text" name="txtCodMat" id="txtCodMat" class="form-control" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event,'num')"  placeholder="Código Matrícula" required>
                            </div>

                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("#datepicker").datepicker();
                                });
                            </script>
                        
                            <div class="form-group" style="margin-top: -20px;">
                                <label for="fechaini" class="control-label col-sm-5 col-md-5 col-lg-5" ><strong style="color:#03C1FB;">*</strong>Fecha Inscripción:</label>
                                <input  type="text" name="fechaini"  id="fechaini" class="form-control" readonly title="Ingrese la Fecha de Inscripción" placeholder="Fecha de Inscripción" style="height: 30px" required >
                            </div>
                            
                            <div class="form-group" style="margin-top: -20px;">
                                <label for="txtDirC" class="control-label col-sm-5 col-md-5 col-lg-5" ><strong style="color:#03C1FB;">*</strong>Dirección Correspondencia:</label>
                                <input  type="text" name="txtDirC"  id="txtDirC" class="form-control" title="Ingrese la Dirección" placeholder="Dirección de Correspondencia" style="height: 30px" required >
                            </div>
                            
                            <?php 
                                $estado = "SELECT id_unico , nombre FROM gc_estado_contribuyente ";
                                $esta = $mysqli->query($estado);
                            ?>
                    
                            <div class="form-group" style="margin-top: -20px">
                                <label for="sltEst" class="control-label col-sm-5 "><strong style="color:#03C1FB;">*</strong>Estado:</label>
                                <select name="sltEst" id="sltEst"  style="height: 34%" class="form-control col-sm-1" title="Seleccione Estado" required >
                                    <option value="">Estado</option>
                     
                                        <?php   while($EST=mysqli_fetch_row($esta)){ ?>

                                                    <option value="<?php echo $EST[0] ?>"><?php echo ucwords(mb_strtolower($EST[1] )) ?></option>
                                    
                                        <?php   } ?>
                                </select>
                            </div>

                            <div class="form-group" style="margin-top:-12px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -12px; margin-bottom: -10px; margin-left: 0px;">Guardar</button>
                            </div>

                            <div class="texto" style="display:none"></div>
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div> <!-- Cierra col-sm-7 text-left -->
            </div> <!-- Cierra row content -->
        </div> <!-- Cierra container-fluid text-center -->

        <?php require_once 'footer.php'; ?>

        <!-- Divs clase Modal para notificar la existencia del número de identificación y posible modificación de sus registros  -->
        <div class="modal fade" id="myModal1" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Este número de Identificación  ya existe.¿Desea actualizar la información?</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                        <button type="button" class="btn" style="color: #000; margin-top: 2px"  data-dismiss="modal" id="ver2">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="myModal2" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Seleccione un Tipo Identificación.</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="ver3" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
        </div>
  

        <script type="text/javascript" src="../js/menu.js"></script>
        <link rel="stylesheet" href="../css/bootstrap-theme.min.css">
        <script src="../js/bootstrap.min.js"></script>

        <script type="text/javascript">
            function existente(){
                var tipoD = document.form.tipoIden.value;
                var numI = document.form.noIdent.value;
                var result = '';
                
                if(tipoD == null || tipoD == '' || tipoD == "Tipo Identificación" || numI == null){
                    $("#myModal2").modal('show');
                }else{
                    $.ajax({
                        data: {"num" : numI},
                        type: "POST",
                        url: "consultarTercero.php",
                        success:  function (data) {
                            var res  = data.split(";");

                            if(res[1] == 'true1'){
                                $('.texto').html(data);
                                $("#myModal1").modal('show');
                            }             
                        }
                    });
                }
            }
        </script>

        <script type="text/javascript">
            $('#ver1').click(function(){
                var id = document.form.id.value;
                document.location = 'modificar_GC_CONTRIBUYENTE_NATURAL.php?id_ter_clie_nat='+id;
            });
        </script>

    </body>
</html>
