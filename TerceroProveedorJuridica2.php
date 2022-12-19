<?php 
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#02/08/2018 |Erica G. | Correo Electrónico - Arreglar Código 
#######################################################################################################
require_once 'Conexion/conexion.php';
require_once 'head.php'; 

$_SESSION['perfil'] = "J"; //Jurídica.
$_SESSION['url'] = "TerceroProveedorJuridica2.php";
$compania = $_SESSION['compania'];
#****** Tipo Identificación  *********#
$sqlTipoIden = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_identificacion
  ORDER BY Nombre ASC";
$tipoIden = $mysqli->query($sqlTipoIden);

#****** Sucursal *********#
$sqlSucursal = "SELECT Id_Unico, Nombre 
  FROM gf_sucursal
  ORDER BY Nombre ASC";
$sucursal = $mysqli->query($sqlSucursal);

#****** Tipo Régimen *********#
$sqlTipoReg = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_regimen 
  ORDER BY Nombre ASC";
$tipoReg = $mysqli->query($sqlTipoReg);

#****** Tipo Empresa *********#
$sqlTipoEmp = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_empresa
  ORDER BY Nombre ASC";
$tipoEmp = $mysqli->query($sqlTipoEmp);

#****** Tipo Entidad *********#
$sqlTipoEnt = "SELECT Id_Unico, Nombre 
  FROM gf_tipo_entidad
  ORDER BY Nombre ASC";
$tipoEnt = $mysqli->query($sqlTipoEnt);

#****** Representante Legal *********#
$sqlReprLeg = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
  WHERE t.TipoIdentificacion = ti.Id_Unico
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10 
  AND t.compania = $compania 
  ORDER BY t.NombreUno ASC";
$repreLegal = $mysqli->query($sqlReprLeg);

#****** Contacto *********#
$sqlContacto = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
  WHERE t.TipoIdentificacion = ti.Id_Unico
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10 
  AND t.compania = $compania 
  ORDER BY t.NombreUno ASC";
$contacto = $mysqli->query($sqlContacto);

#****** Zona *********#
$sqlZona = "SELECT Id_Unico, Nombre 
  FROM gf_zona
  ORDER BY Nombre ASC";
$zona = $mysqli->query($sqlZona);

#****** Departamento  *********#
$sqlDep = "SELECT Id_Unico, Nombre 
  FROM gf_departamento 
  ORDER BY Nombre ASC";
$dep = $mysqli->query($sqlDep);
 ?>
<script type="text/javascript">
    function CalcularDv()
{ 
 var arreglo, x, y, z, i, nit1, dv1;
 nit1=document.form.noIdent.value;
  if (isNaN(nit1))
  {
  document.form.digitVerif.value="X";
      alert('Número del Nit no valido, ingrese un número sin puntos, ni comas, ni guiones, ni espacios');   
  } else {
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
  if (y > 1){ dv1=11-y; } else { dv1=y; }
  document.form.digitVerif.value=dv1;
  }
}
  </script>
    <title>Registrar Tercero Proveedor Juridica</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <style>
        label #tipoIdent-error,#razoSoci-error,#tipoEntidad-error,#tipoReg-error, #tipoEmp-error, #depto-error, #ciudad-error, #correo-error{
            display: block;
            color: #bd081c;
            font-weight: bold;
            font-style: italic;

        }
    </style>
    <script>
        $().ready(function () {
            var validator = $("#form").validate({
                ignore: "",
                errorPlacement: function (error, element) {
                    $(element)
                        .closest("form")
                        .find("label[for='" + element.attr("id") + "']")
                        .append(error);
                },
            });
            $(".cancel").click(function () {
                validator.resetForm();
            });
        });
    </script>        
</head>
<body >
    <div class="container-fluid text-center">
        <div class="content row">
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top: -20px"> 
                <h2 align="center" class="tituloform">Registrar Proveedor Juridica</h2>
                <a href="LISTAR_TERCERO_PROVEEDOR_JURIDICA_2.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: transparent; border-radius: 5px">C</h5>
                <div class="client-form contenedorForma" style="margin-top: -7px">
                    <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTerceroProveedorJuridica2Json.php">
                        <p align="center" class="parrafoO">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                        <div class="form-group form-inline " style="">
                            <label for="tipoIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>
                            <div class="form-group form-inline col-sm-3" style="margin-left:-10px">
                                <select name="tipoIdent" id="tipoIdent" class="select2_single form-control col-sm-5" style="height: 33px;width:150px" title="Tipo Identificación" required>
                                    <option value="">Tipo Ident.</option>
                                    <?php while ($ma = mysqli_fetch_assoc($tipoIden)) { ?>
                                    <option value="<?php echo $ma["Id_Unico"]; ?>">
                                    <?php echo ucwords((mb_strtolower($ma["Nombre"]))); ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-3" style="">
                                <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event, 'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv();    return existente()" />
                                <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>
                                <input type="text" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event, 'num')" placeholder="" readonly="" style="height: 30px"/>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -22px; ">
                            <label for="sucursal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Sucursal:</label>
                            <select name="sucursal" id="sucursal" class="select2_single form-control" title="Seleccione Sucursal" >
                                <option value="">Sucursal</option>
                                <?php while ($rowS = mysqli_fetch_row($sucursal)) { ?>
                                <option value="<?php echo $rowS[0] ?>"><?php echo ucwords((mb_strtolower($rowS[1]))); ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="razoSoci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Razón Social:</label>
                            <input type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event)" onkeyup="javascript:this.value = this.value.toUpperCase();" placeholder="Razón Social" required>
                        </div>
                        <div class="form-group" style="margin-top: -15px; ">
                            <label for="tipoReg" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Régimen:</label>
                            <select name="tipoReg" id="tipoReg" class="select2_single form-control" title="Ingrese el tipo de régimen" >
                                <option value="">Tipo Régimen</option>
                                <?php while ($rowTR = mysqli_fetch_row($tipoReg)) {  ?>
                                <option value="<?php echo $rowTR[0] ?>"><?php echo ucwords((mb_strtolower($rowTR[1]))); ?></option>
                                <?php }  ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEmp" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Empresa:</label>
                            <select name="tipoEmp" id="tipoEmp" class="select2_single form-control" title="Ingrese el tipo de empresa" >
                                <option value="">Tipo Empresa</option>
                                <?php while ($rowTE = mysqli_fetch_row($tipoEmp)) {?>
                                <option value="<?php echo $rowTE[0] ?>"><?php echo ucwords((mb_strtolower($rowTE[1]))); ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="tipoEntidad" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Entidad:</label>
                            <select name="tipoEntidad" id="tipoEntidad" class="select2_single form-control" title="Ingrese el tipo  Entidad" >
                                <option value="">Tipo Entidad</option>
                                <?php while ($rowTEn = mysqli_fetch_row($tipoEnt)) {?>
                                <option value="<?php echo $rowTEn[0] ?>"><?php echo ucwords((mb_strtolower($rowTEn[1]))); ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="repreLegal" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Representante Legal:</label>
                            <select name="repreLegal" id="repreLegal" class="select2_single form-control" title="Ingrese el representante legal">
                                <option value="">Representante Legal</option>
                                <?php while ($rowRL = mysqli_fetch_row($repreLegal)) { ?>
                               <option value="<?php echo $rowRL[0] ?>">
                                <?php echo ucwords((mb_strtolower($rowRL[1]. " - " . $rowRL[2] ))); ?>
                               </option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group form-inline" style="margin-top: -10px">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>     
                            <div class="form-group form-inline col-sm-3"  style="margin-left:-10px">
                                <select name="depto" id="depto" class="select2_single form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                    <option value="">Departamento</option>
                                    <?php while ($rowD = mysqli_fetch_row($dep)) {?>
                                    <option value="<?php echo $rowD[0] ?>"><?php echo ucwords((mb_strtolower($rowD[1]))); ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group form-inline col-sm-1" style=""></div>
                            <div class="form-group form-inline col-sm-3" style="">
                                <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="form-control" title="Seleccione Ciudad" required>
                                    <option value="">Ciudad</option>
                                </select>
                                <script type="text/javascript">
                                    $(document).ready(function () {
                                        $("#depto").change(function () {
                                            var form_data = {
                                                is_ajax: 1,
                                                id_depto: +$("#depto").val()
                                            };
                                            $.ajax({
                                                type: "POST",
                                                url: "Ciudad.php",
                                                data: form_data,
                                                success: function (response) {
                                                    $('#ciudad').html(response).fadeIn();
                                                    $('#ciudad').select2();
                                                }
                                            });
                                        });
                                    });
                                </script>
                                <label for="ciudad" class=""></label>     
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: -15px; ">
                            <label for="contacto" class="col-sm-5 control-label">Contacto:</label>
                            <select name="contacto" id="contacto" class="select2_single form-control" title="Ingrese el contacto">
                                <option value="">Contacto</option>
                                <?php while ($rowCon = mysqli_fetch_row($contacto)) { ?>
                                <option value="<?php echo $rowCon[0] ?>"><?php echo ucwords((mb_strtolower($rowCon[1]))).' - '.$rowCon[2]; ?></option>
                                <?php } ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px; ">
                            <label for="zona" class="col-sm-5 control-label">Zona:</label>
                            <select name="zona" id="zona" class="select2_single form-control" title="Ingrese la zona" >
                                <option value="">Zona</option>
                                <?php while ($rowZ = mysqli_fetch_row($zona)) { ?>
                                    <option value="<?php echo $rowZ[0] ?>"><?php echo ucwords((strtolower($rowZ[1]))); ?></option>
                                <?php }  ?>
                            </select> 
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="correo" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <input type="email" name="correo" id="correo" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Corrreo Electrónico" >
                        </div>
                        <div class="form-group" style="margin-top:-12px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button type="submit" class="btn btn-primary sombra" style=" margin-top: -12px; margin-bottom: -10px; margin-left: 0px;">Guardar</button>
                        </div>
                        <div class="texto" style=""></div>
                        <input type="hidden" name="MM_insert" >
                        <input type="hidden" id="id" name="id">
                    </form>
                </div>	
            </div>
            <div class="col-sm-7 col-sm-3" style="margin-top:-22px">
                <table class="tablaC table-condensed" style="margin-left: -10px">
                    <thead>
                        <tr>
                            <th>
                                <h2 class="titulo" align="center">Consultas</h2>
                            </th>
                            <th>
                                <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="btnConsultas" style="margin-bottom: 1px;">
                                    <a href="#">
					MOVIMIENTO CONTABLE
                                    </a>
				</div>
                            </td>
                            <td>
                                <button class="btn btn-primary btnInfo" disabled="true">DIRECCIÓN</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas" style="margin-bottom: 1px;">
                                    <a href="#"> 
					MOVIMIENTO PRESUPUESTAL
                                    </a>
                                </div>
                            </td>
                            <td>
                               <a href="GF_CUENTA_BANCARIA_TERCERO.php" ><button disabled="true" class="btn btn-primary btnInfo" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un  asociado jurídica."';}?> >CUENTA BANCARIA</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas" style="margin-bottom: 1px;">
                                    <a href="#"> 
					MOVIMIENTO<br/>ALMACEN
                                    </a>
                                </div>
                            </td>
                            <td>
                               <a href="GF_TIPO_ACTIVIDAD_TERCERO.php" ><button disabled="true" class="btn btn-primary btnInfo" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un  asociado jurídica."';}?> >TIPO ACTIVIDAD</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas" style="margin-bottom: 1px;">
                                    <a href="#"> 
                                        TAREAS DE MANTENIMIENTO 
                                    </a>
                                </div>
                            </td>
                            <td>
                                <a href="GF_TELEFONO.php" >
                                    <button class="btn btn-primary btnInfo"  disabled="true" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un proveedor jurídica."';}?> > 
                                        TELÉFONO
                                    </button>
                                </a>
                            <br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="btnConsultas" style="margin-bottom: 1px;">
                                    <a href="#"> 
					RETENCIONES EFECTUADAS
                                    </a>
				</div>
                            </td>
                            <td>
                                <button href="GF_CONDICION_TERCERO.php" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar una compañía."';}?> class="btn btnInfo btn-primary" disabled="true">CONDICIÓN</button>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button href="registrar_TERCERO_CONTACTO_NATURAL.php" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar una compañía."';}?> class="btn btnInfo btn-primary" disabled="true">CONTACTO</button>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btnInfo btn-primary" disabled="true">PERFIL CONDICIÓN</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require_once 'footer.php'; ?>
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
                    <button type="button" class="btn" style="color: #000; margin-top: 2px" 	data-dismiss="modal" id="ver2">Cancelar</button>
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
    <div class="modal fade" id="myModal4" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                  <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
              </div>
              <div class="modal-body" style="margin-top: 8px">
                  <p>Este número de identificación no se puede registrar.<br/> 
                      Tiene un perfil no asociado al perfil a registrar.</p>
              </div>
              <div id="forma-modal" class="modal-footer">
                  <button type="button" id="ver4" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
              </div>
            </div>
        </div>
    </div>
  
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/md5.js"></script>
    <script>
      $(document).ready(function () {
          $(".select2_single").select2({
              allowClear: true
          });
      });
    </script>
    <script type="text/javascript">
    function existente() {
        var tipoD = document.form.tipoIdent.value;
        var numI  = document.form.noIdent.value;
        var perfil= 6;
        var result = '';
        if (tipoD == null || tipoD == '' || tipoD == "Tipo Ident." || numI == null) {
            $("#myModal2").modal('show');
        } else {
            $.ajax({
                data: {"numI": numI,perfil:perfil, action:2},
                type: "POST",
                url: "jsonPptal/gf_tercerosJson.php",
                success: function (data) {
                    var resultado = JSON.parse(data);
                    var rta = resultado["rta"];
                    var id  = resultado["id"];
                    console.log(data);
                    if (rta == 0) {
                        if(id!=0){
                            $("#id").val(md5(id));
                            $("#myModal1").modal('show');
                        }
                    } else {
                        $("#myModal4").modal('show');
                        $('#ver4').click(function () {
                            $("#noIdent").val('');
                            $("#digitVerif").val('');
                        });
                    }                   
                }
            });
        }
    }
    </script>
    <script type="text/javascript">
          $('#ver1').click(function(){
                  var id = document.form.id.value;
          document.location = 'EDITAR_TERCERO_PROVEEDOR_JURIDICA_2.php?id='+id;
        });

    </script>

</body>
</html>