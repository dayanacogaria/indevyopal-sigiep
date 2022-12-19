<?php 
	require_once('Conexion/conexion.php');
	require_once 'head.php'; 

    $_SESSION['perfil'] = "J"; //Jurídica
    $_SESSION['url'] = "registrar_GC_CONTRIBUYENTE_JURIDICA.php";

    //Consultas para el listado de los diferentes combos correspondientes.
    //Tipo Identificación.
    $sqlTipoIden = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_identificacion
                    ORDER BY Nombre ASC"; 
    $tipoIden = $mysqli->query($sqlTipoIden);

    //Sucursal.
    $sqlSucursal = "SELECT Id_Unico, Nombre 
                    FROM gf_sucursal
                    ORDER BY Nombre ASC";
    $sucursal = $mysqli->query($sqlSucursal);

    //Tipo Régimen.
    $sqlTipoReg = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_regimen 
                    ORDER BY Nombre ASC";
    $tipoReg = $mysqli->query($sqlTipoReg);

    //Tipo Empresa.
    $sqlTipoEmp = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_empresa
                    ORDER BY Nombre ASC";
    $tipoEmp = $mysqli->query($sqlTipoEmp);

    //Tipo Entidad.
    $sqlTipoEnt = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_entidad
                    ORDER BY Nombre ASC";
    $tipoEnt = $mysqli->query($sqlTipoEnt);

    //Representante Legal.
    $sqlReprLeg = "SELECT   tr.Id_Unico, 
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
                                tr.apellidodos)) AS NOMBRE,
                            tr.numeroidentificacion


                    FROM gf_tercero tr, gf_tipo_identificacion ti, gf_perfil_tercero pt   
                    WHERE tr.TipoIdentificacion = ti.Id_Unico
                    AND tr.Id_Unico = pt.Tercero";
    $repreLegal = $mysqli->query($sqlReprLeg);

    //Contacto.
    $sqlContacto = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                    FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
                    WHERE t.TipoIdentificacion = ti.Id_Unico
                    AND t.Id_Unico = pt.Tercero 
                    AND pt.Perfil = 10
                    ORDER BY t.NombreUno ASC";
    $contacto = $mysqli->query($sqlContacto);

    //Zona.
    $sqlZona = "SELECT Id_Unico, Nombre 
                FROM gf_zona
                ORDER BY Nombre ASC";
    $zona = $mysqli->query($sqlZona);
  
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
        <!-- Script para calcular el dígito de verificación. -->
        <script type="text/javascript">
            function CalcularDv()
            { 
                var arreglo, x, y, z, i, nit1, dv1;
                nit1=document.form.noIdent.value;
                if (isNaN(nit1))
                {
                    document.form.digitVerif.value="X";
                    alert('Número del Nit no valido, ingrese un número sin puntos, ni comas, ni guiones, ni espacios');   
                }else{
                    arreglo = new Array(16); 
                    x=0 ; y=0 ; z=nit1.length ;
                    arreglo[1]=3;   arreglo[2]=7;   arreglo[3]=13; 
                    arreglo[4]=17;  arreglo[5]=19;  arreglo[6]=23;
                    arreglo[7]=29;  arreglo[8]=37;  arreglo[9]=41;
                    arreglo[10]=43; arreglo[11]=47; arreglo[12]=53;  
                    arreglo[13]=59; arreglo[14]=67; arreglo[15]=71;
                    
                    for(i=0 ; i<z ; i++)
                    { 
                        y=(nit1.substr(i,1));
                        x+=(y*arreglo[z-i]);
                    } 
                    y=x%11
                    if (y > 1){ 
                        dv1=11-y; 
                    }else{ 
                        dv1=y; 
                    }
                    document.form.digitVerif.value=dv1;
                }
            }
        </script><!-- Cierra script dígito verificación-->

        <title>Registrar Contribuyente Jurídica</title>
        <link rel="stylesheet" href="css/select2.css">
        <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    </head>
    <body>

    <div class="container-fluid text-center">
        <div class="row content">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top: -22px; ">
                <h2 class="tituloform" align="center" >Registrar Contribuyente Jurídica</h2>
                    <div class="client-form contenedorForma" style="margin-top: -5px">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/registrarContribuyenteJuridicaJson.php">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            <div class="form-group form-inline" style="margin-bottom: -30px">
                                <label for="noIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                                <select name="tipoIdent" id="tipoIdent" class="form-control col-sm-5" style="height: 33px;width:113px;" title="Tipo Identificación" required>
                                    <option>Tipo Ident.</option>
                                    <?php   while ($ma = mysqli_fetch_assoc($tipoIden)) { ?>
                                                <option value="<?php echo $ma["Id_Unico"]; ?>">
                                                    <?php echo ucwords( (strtolower($ma["Nombre"]))); ?>
                                                </option>
                                    <?php   } ?>
                                </select>
                            
                                <span class="col-sm-1" style="width:1px; margin-top:8px;"></span>
                            
                                <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event,'num')" placeholder="Número" style="width:95px" style="height: 30px;" required onblur="CalcularDv();return existente()" />

                                <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>

                                <input type="text" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event,'num')" placeholder="" readonly="" style="height: 30px;"/>

                            </div>

                            <div class="form-group" style="margin-top: -22px;">
                                <label for="razoSoci" class="col-sm-5 control-label"><strong class="obligado">*</strong>Razón Social:</label>
                                <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event)" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Razón Social" required>
               
                            </div>

                            <div class="form-group" style="margin-top: -22px;">
                                <label for="txtCodMat" class="col-sm-5 control-label"><strong class="obligado">*</strong>Código Matrícula:</label>
                                <input type="text" name="txtCodMat" id="txtCodMat" class="form-control" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event,'num')"  placeholder="Código Matrícula" required>
                            </div>

                            <script type="text/javascript">
                                $(document).ready(function() {
                                    $("#datepicker").datepicker();
                                });
                            </script>
                        
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="fechaini" class="control-label col-sm-5 col-md-5 col-lg-5" ><strong style="color:#03C1FB;">*</strong>Fecha Inscripción:</label>
                                <input  type="text" name="fechaini"  id="fechaini" class="form-control" title="Ingrese la Fecha de Inscripción" placeholder="Fecha de Inscripción" style="height: 30px" required >
                            </div>
                            
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="txtDirC" class="control-label col-sm-5 col-md-5 col-lg-5" ><strong style="color:#03C1FB;">*</strong>Dirección Correspondencia:</label>
                                <input  type="text" name="txtDirC"  id="txtDirC" class="form-control" title="Ingrese la Dirección" placeholder="Dirección de Correspondencia" style="height: 30px" required >
                            </div>
                            
                            <?php 
                                $estado = "SELECT id_unico , nombre FROM gc_estado_contribuyente ";
                                $esta = $mysqli->query($estado);
                            ?>
                    
                            <div class="form-group" style="margin-top: -10px">
                                <label for="sltEst" class="control-label col-sm-5 "><strong style="color:#03C1FB;">*</strong>Estado:</label>
                                <select name="sltEst" id="sltEst"  style="height: 34%" class="form-control col-sm-1" title="Seleccione Estado" required >
                                    <option value="">Estado</option>
                     
                                        <?php   while($EST=mysqli_fetch_row($esta)){ ?>

                                                    <option value="<?php echo $EST[0] ?>"><?php echo ucwords(mb_strtolower($EST[1] )) ?></option>
                                    
                                        <?php   } ?>
                                </select>
                            </div>

                            <div class="form-group" style="margin-top:-5px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -12px; margin-bottom: -10px; margin-left: 0px;">Guardar</button>
                            </div>

                            <div class="texto" style="display:none"></div>

                            <input type="hidden" name="MM_insert" >
                        </form>
                    </div>
                </div> 
            </div> 
        </div> 

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

                var tipoD = document.form.tipoIdent.value;
                var numI = document.form.noIdent.value;
                var result = '';
                if(tipoD == null || tipoD == '' || tipoD == "Tipo Ident." || numI == null){
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
                document.location = 'modificar_GC_CONTRIBUYENTE_JURIDICA.php?id_ter_clie_jur='+id;
            });
        </script>

        <?php require_once 'footer.php'; ?>
        <script type="text/javascript" src="js/select2.js"></script>
        <script>
            $(".select2_single").select2({
                allowClear: true
            });
        </script>

    </body>
</html>