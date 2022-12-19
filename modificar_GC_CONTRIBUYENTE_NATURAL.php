<?php 
    require_once('Conexion/conexion.php');
    require_once 'head.php'; 

    $id_ter_clie_nat = " ";
    if (isset($_GET["id_ter_clie_nat"]))
    { 
        $id_ter_clie_nat = (($_GET["id_ter_clie_nat"]));

        $queryTercClieNat ="SELECT t.id_unico, t.tipoidentificacion, ti.nombre, t.numeroidentificacion, t.nombreuno, t.nombredos, t.apellidouno, t.apellidodos, t.tiporegimen, tr.nombre 
                            FROM gf_tercero t
                            JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico LEFT JOIN gf_tipo_regimen tr ON t.tiporegimen = tr.id_unico
                            WHERE  md5(t.id_unico) = '$id_ter_clie_nat'";
    }

    $resultado = $mysqli->query($queryTercClieNat);
    $row = mysqli_fetch_row($resultado);

    //Variables de sesión para determinar el id del tercero que se está consultando y la url para regresar.
    $_SESSION['id_tercero'] = $row[0];
    $_SESSION['perfil'] = "N"; //Natural.
    $_SESSION['url'] = "modificar_TERCERO_CLIENTE_NATURAL.php?id_ter_clie_nat=".(($_GET["id_ter_clie_nat"]));
    $_SESSION['tipo_perfil']='Cliente natural';

    //Inicio de consulta para cargar opciones en combos.
    //Tipo Identificación
    $sqlTipoIden = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_identificacion
                    WHERE Id_Unico != $row[1]
                    ORDER BY Nombre ASC";
    $tipoIden = $mysqli->query($sqlTipoIden);

    //Tipo Régimen
    $sqlTipoReg = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_regimen
                    WHERE Id_Unico != $row[8]
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
        <title>Modificar Contribuyente Natural</title>
    </head>
    <body>

        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top:-20px">
                    <h2 align="center" class="tituloform">Modificar Contribuyente Natural</h2>
                    <div class="client-form contenedorForma">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarContribuyenteNaturalJson.php">
                            <p align="center" class="parrafoO">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            
                            <input type="hidden" name="id" value="<?php echo $row[0];?>">

                            <div class="form-group" style="margin-top: -20px; ">
                                <label for="tipoIden" class="col-sm-5 control-label"><strong class="obligado">*</strong>Tipo Identificación:</label>
                                <select name="tipoIden" id="tipoIden" class="form-control" title="Ingrese el tipo de identificación" required>
                                    <option value="<?php echo $row[1];?>"><?php echo  ($row[2]);?></option>
                                    <?php   while($rowTI = mysqli_fetch_row($tipoIden))
                                            {  
                                    ?>
                                                <option value="<?php echo $rowTI[0] ?>"><?php echo ucwords( (strtolower($rowTI[1]))); ?></option>
                                    <?php
                                            }  ?>
                                </select> 
                            </div>

                            <div class="form-group" style="margin-top: -20px; ">
                                <label for="noIdent" class="col-sm-5 control-label"><strong class="obligado">*</strong>Número Identificación:</label>
                                <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event,'num')" value="<?php echo $row[3];?>" placeholder="Número Identificación" required/>
                            </div>

                            <div class="form-group" style="margin-top: -20px;">
                                <label for="nomUno" class="col-sm-5 control-label"><strong class="obligado">*</strong>Primer Nombre:</label>
                                <input type="text" name="nomUno" id="nomUno" class="form-control" maxlength="25" title="Ingrese el primer nombre" onkeypress="return txtValida(event,'car')" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo  ($row[4]);?>" placeholder="Primer Nombre" required>
                            </div>

                            <div class="form-group" style="margin-top: -20px;">
                                <label for="nomDos" class="col-sm-5 control-label">Segundo Nombre:</label>
                                <input type="text" name="nomDos" id="nomDos" class="form-control" maxlength="25" title="Ingrese el segunfo nombre" onkeypress="return txtValida(event,'car')" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo  ($row[5]);?>" placeholder="Segundo Nombre" >
                            </div>

                            <div class="form-group" style="margin-top: -20px;">
                                <label for="apellUno" class="col-sm-5 control-label"><strong class="obligado">*</strong>Primer Apellido:</label>
                                <input type="text" name="apellUno" id="apellUno" class="form-control" maxlength="30" title="Ingrese el primer apellido" onkeypress="return txtValida(event,'car')" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo  ($row[6]);?>" placeholder="Primer Apellido" required>
                            </div>

                            <div class="form-group" style="margin-top: -20px;">
                                <label for="apellDos" class="col-sm-5 control-label">Segundo Apellido:</label>
                                <input type="text" name="apellDos" id="apellDos" class="form-control" maxlength="30" title="Ingrese el segundo apellido" onkeypress="return txtValida(event,'car')" onkeyup="javascript:this.value=this.value.toUpperCase();" value="<?php echo  ($row[7]);?>" placeholder="Segundo Apellido" >
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

                            <div class="form-group" style="margin-top: -10px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom:-10px; margin-left: 0px;">Guardar</button>
                            </div>  
                            
                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div> <!-- Cierre col-sm-7 text-left -->
            </div> <!-- Cierre row content -->
        </div> <!-- Cierre container-fluid text-center -->

        <?php require_once 'footer.php'; ?>

    </body>
</html>