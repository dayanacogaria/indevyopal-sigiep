<?php 
    require_once('Conexion/conexion.php');
    require_once 'head.php'; 

    //Captura de ID y consulta del resgistro correspondiente.
    $id_ter_clie_jur = " ";
    if (isset($_GET["id_ter_clie_jur"]))
    { 
        $id_ter_clie_jur = (($_GET["id_ter_clie_jur"]));

        //Consulta general
        $queryTerceroComp = "SELECT t.Id_Unico, t.RazonSocial, t.NumeroIdentificacion, t.DigitoVerficacion, ti.Id_Unico, ti.Nombre, s.Id_Unico, s.nombre, t.RepresentanteLegal, ci.Id_Unico, ci.Nombre, tr.Id_Unico, tr.Nombre, t.Contacto, te.Id_Unico, te.Nombre, tipen.Id_Unico, tipen.Nombre, ci.Departamento, z.Id_Unico, z.Nombre,DP.Id_Unico,DP.Nombre 
                        FROM gf_tercero t  
                        LEFT JOIN gf_tipo_identificacion ti ON  t.TipoIdentificacion = ti.Id_Unico
                        LEFT JOIN gf_sucursal s ON t.Sucursal = s.Id_Unico
                        LEFT JOIN gf_tipo_regimen tr ON t.TipoRegimen = tr.Id_Unico
                        LEFT JOIN gf_tipo_empresa te ON t.TipoEmpresa = te.Id_Unico
                        LEFT JOIN gf_tipo_entidad tipen ON t.TipoEntidad = tipen.Id_Unico
                        LEFT JOIN gf_ciudad ci ON t.CiudadIdentificacion = ci.Id_Unico
                        LEFT JOIN gf_zona z ON t.Zona = z.Id_Unico
                        LEFT JOIN gf_departamento DP ON ci.Departamento = DP.Id_Unico
                        WHERE  md5(t.Id_Unico) = '$id_ter_clie_jur'";

    }

    $resultado = $mysqli->query($queryTerceroComp);
    $row = mysqli_fetch_row($resultado);

    //Variables de sesión para determinar el id del tercero que se está consultando y la url para regresar.
    $_SESSION['id_tercero'] = $row[0];
    $_SESSION['perfil'] = "J"; //Jurídica.
    $_SESSION['url'] = "modificar_GC_CONTRIBUYENTE_JURIDICA.php?id_ter_clie_jur=".(($_GET["id_ter_clie_jur"]));
    $_SESSION['tipo_perfil']='Cliente jurídica';

    //Inicio de consulta para cargar opciones en combos.
    //Tipo Identificación.
    $sqlTipoIden = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_identificacion
                    WHERE Id_Unico != $row[4]
                    ORDER BY Nombre ASC";
    $tipoIden = $mysqli->query($sqlTipoIden);

    //Sucursal.
    $sqlSucursal = "SELECT Id_Unico, Nombre 
                    FROM gf_sucursal
                    WHERE Id_Unico != $row[6]
                    ORDER BY Nombre ASC";
    $sucursal = $mysqli->query($sqlSucursal);

    //Tipo Régimen.
    $sqlTipoReg = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_regimen
                    WHERE Id_Unico != $row[11]
                    ORDER BY Nombre ASC";
    $tipoReg = $mysqli->query($sqlTipoReg);

    //Tipo Empresa.
    $sqlTipoEmp = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_empresa
                    WHERE Id_Unico != $row[14]
                    ORDER BY Nombre ASC";
    $tipoEmp = $mysqli->query($sqlTipoEmp);

    //Tipo Entidad.
    $sqlTipoEnt = "SELECT Id_Unico, Nombre 
                    FROM gf_tipo_entidad
                    WHERE Id_Unico != $row[16]
                    ORDER BY Nombre ASC";
    $tipoEnt = $mysqli->query($sqlTipoEnt);

    //Representante Legal.
    $sqlReprLeg = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                    FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt  
                    WHERE t.TipoIdentificacion = ti.Id_Unico  AND t.Id_Unico = pt.Tercero  AND pt.Perfil = 10 AND t.Id_Unico != $row[8]
                    ORDER BY t.NombreUno ASC";
    $repreLegal = $mysqli->query($sqlReprLeg);

    //Contacto.
    $sqlContacto = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
                    FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt     
                    WHERE t.TipoIdentificacion = ti.Id_Unico AND t.Id_Unico = pt.Tercero AND pt.Perfil = 10 AND t.Id_Unico != $row[13]
                    ORDER BY t.NombreUno ASC";
    $contacto = $mysqli->query($sqlContacto);

    //Zona.
    if($row[19] == 0)
    {
        $sqlZona = "SELECT Id_Unico, Nombre 
                    FROM gf_zona
                    ORDER BY Nombre ASC";
        $zona = $mysqli->query($sqlZona);
    }else{
        $sqlZona = "SELECT Id_Unico, Nombre 
                    FROM gf_zona
                    WHERE Id_Unico != $row[19]
                    ORDER BY Nombre ASC";
        $zona = $mysqli->query($sqlZona);
    }
  
    //Fin de las consultas para combos.

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
            function CalcularDv(){ 
                var arreglo, x, y, z, i, nit1, dv1;
                nit1=document.form.noIdent.value;
                if (isNaN(nit1)){
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
                    
                    for(i=0 ; i<z ; i++){ 
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

        <title>Modificar Contribuyente Jurídica</title>
    </head>
    <body>

        <div class="container-fluid text-center">
            <div class="row content">
                <?php require_once 'menu.php'; ?>
                <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top: -20px; margin-bottom: -15px">
                    <h2 class="tituloform" align="center" >Modificar Contribuyente Jurídica</h2>
                    <div  class="client-form contenedorForma">
                        <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarContribuyenteJuridicaJson.php">
                            <p align="center" class="parrafoO" style="margin-bottom:-0.00005em;">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                            
                            <input type="hidden" name="id" value="<?php echo $row[0];?>">

                            <div class="form-group form-inline" style="margin-bottom: -30px">
                                <label for="noIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                                <select name="tipoIdent" id="tipoIdent" class="form-control col-sm-5" style="height: 33px;width:113px;" title="Tipo Identificación" required>
                                    <option value="<?php echo $row[4]; ?>"><?php echo $row[5]; ?></option>
                                    <?php   while ($ma = mysqli_fetch_assoc($tipoIden)) { ?>
                                                <option value="<?php echo $ma["Id_Unico"]; ?>">
                                    <?php           echo ucwords( (strtolower($ma["Nombre"]))); ?>
                                                </option>
                                    <?php   } ?>
                                </select>
                            
                                <span class="col-sm-1" style="width:1px; margin-top:8px;"></span>
                            
                                <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event,'num')" value="<?php echo $row[2]; ?>" style="width:95px" style="height: 30px;" required onblur="CalcularDv();return existente()" />

                                <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>

                                <input type="text" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" value="<?php echo $row[3]; ?>" title="Dígito de verificación" onkeypress="return txtValida(event,'num')" placeholder="" readonly="" style="height: 30px;"/>
                            </div>

                            <div class="form-group" style="margin-top: -22px; ">
                                <label for="razoSoci" class="col-sm-5 control-label"><strong class="obligado">*</strong>Razón Social:</label>
                                <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" value="<?php echo  ($row[1]);?>" onkeypress="return txtValida(event)"  onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Razón Social" required>
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

                            <div class="form-group" style="margin-top:-12px;">
                                <label for="no" class="col-sm-5 control-label"></label>
                                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: -10px; margin-left: 0px;">Guardar</button>
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