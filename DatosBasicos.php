<?php 
# ********************************************************************
# 06/11/18 Opcion en el menú para modificar datos básicos y contraseña
# ********************************************************************
require_once('Conexion/conexion.php');
require_once 'head.php'; 
$compania = $_SESSION['compania'];
$usuario_tercero = $_SESSION['usuario_tercero'];
$id_usuario = $_SESSION['id_usuario'];
//$resultadoTercero = "";

# Consulta para consultar los datos del usuario teniendo en cuanta variables de sesion
$queryUsuario = "SELECT T.id_unico,
					T.razonsocial,
    				T.nombreuno,
             		T.nombredos,
             		T.apellidouno,
             		T.apellidodos,         
             		T.email, 
             		T.tarjeta_profesional,
             		U.id_unico,
             		U.contrasen,
             		U.usuario,
                    ti.nombre,
                    T.numeroidentificacion
				 FROM gf_tercero T, gs_usuario U, gf_tipo_identificacion ti
				 WHERE U.Id_Unico = '$id_usuario'  
				 AND U.tercero = '$usuario_tercero'
				 AND T.compania = '$compania'
				 AND U.tercero = T.id_unico
                 AND T.tipoidentificacion = ti.id_unico";		
$resultado = $mysqli->query($queryUsuario);
//echo $queryUsuario;
$row = mysqli_fetch_row($resultado);

// Variabe que guarda la contraseña que trae de la tabla
$contrasen = $row[9];
#echo $contrasen;

?>

	<title>Modificar datos básicos</title>
    <link href="css/select/select2.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.js" rel="stylesheet">
    <link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
    <script src="dist/jquery.validate.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <link rel="stylesheet" href="css/jquery-ui.css">
    <script src="js/jquery-ui.js"></script>
    <style>
        label #correo-error, #primerN-error, #primerA-error, #txtPassNV{
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
<body>
    
    <div class="container-fluid text-center">
        <div class="row content" >				
            <?php require_once 'menu.php'; ?>
            <div class="col-sm-7 text-left" style="margin-left: -16px;margin-top:-20px">
                <h2 class="tituloform" align="center">Modificar Datos Básicos</h2>
                <a href="index2.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Ir la página anterior"></a>
                <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">
                    <?php
                        if ($row[1] != NULL){
                            echo $row[1] . ' (' . $row[11] . ': ' . $row[12] . ')';                    // Razon social (tipo y numero de identificacion)
                        }else{
                            echo $row[3] . ' ' . $row[4] . ' (' . $row[11] . $row[12] . ')';    // Nombre1 y apellido 1 (tipo y numero de identificacion)
                        }       
                    ?>               
                </h5>
                <div class="client-form contenedorForma">
                    <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarCambiosDatosBasicosJson.php">
                        <p align="center" class="parrafoO">Los campos marcados con <strong class="oculto">*</strong> son obligatorios.</p>  

                        <!-- Condicional para controlar que si tiene razon social no pida nombres,
                        apellidos, ni tarjeta profesional, y viceversa -->
                        <?php 
						if ($row[1] != NULL){
						?>
							<!-- RAZÓN SOCIAL -->
							<div class="form-group" style="margin-top: -10px;">
                            	<label for="razoSoci" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Razón Social:</label>
                            	<input type="text" name="razoSoci" id="razoSoci" class="form-control" value="<?php echo ($row[1]); ?>" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event)" onkeyup="javascript:this.value = this.value.toUpperCase();" placeholder="Razón Social" required>
                        	</div>
                        <?php  }
						else {
						?>  
							<!-- PRIMER NOMBRE -->                                   
	                        <div class="form-group" style="margin-top: -15px;">
	                            <label for="primerN" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primer Nombre:</label>
	                            <input type="text" name="primerN" id="primerN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese primer nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $row[2]; ?>"   placeholder="Primer Nombre" required>
	                        </div>
	                        <!-- SEGUNDO NOMBRE -->
	                        <div class="form-group" style="margin-top: -15px;">
	                            <label for="segundoN" class="col-sm-5 control-label">Segundo Nombre:</label>
	                            <input type="text" name="segundoN" id="segundoN" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese segundo nombre" onkeypress="return txtValida(event,'car')" value="<?php echo $row[3]; ?>"  placeholder="Segundo Nombre">
	                        </div>
	                        <!-- PRIMER APELLIDO -->
	                        <div class="form-group" style="margin-top: -15px;">
	                            <label for="primerA" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Primer Apellido:</label>
	                            <input type="text" name="primerA" id="primerA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese primer apellido" onkeypress="return txtValida(event,'car')" value="<?php echo $row[4]; ?>"  placeholder="Primer Apellido" required>
	                        </div>
	                        <!-- SEGUNDO APELLIDO -->
	                        <div class="form-group" style="margin-top: -15px;">
	                            <label for="segundoA" class="col-sm-5 control-label">Segundo Apellido:</label>
	                            <input type="text" name="segundoA" id="segundoA" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();"  maxlength="150" title="Ingrese segundo apellido" onkeypress="return txtValida(event,'car')" value="<?php echo $row[5]; ?>"  placeholder="Segundo Apellido">
	                        </div>
	                        <!-- TARJETA PROFESIONAL -->
	                        <div class="form-group" style="margin-top: -10px;">
	                            <label for="tp" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tarjeta Profesional:</label>
	                            <input type="text" name="tarjetaP" id="tarjetaP" value="<?php echo $row[7]?>" class="form-control" maxlength="500" title="Ingrese Tarjeta Profesional" placeholder="Tarjeta Profesional" >
	                        </div>
	                    <?php  }
	                    ?>   <!-- fin del condicional --> 

	                    <!-- CORREO ELECTRÓNICO -->
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="email" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Correo Electrónico:</label>
                            <input type="email" name="email" id="email" class="form-control" maxlength="500" title="Ingrese Correo Electrónico" placeholder="Correo Electrónico" value="<?php echo $row[6]?>" >
                        </div>
                        <!-- NOMBRE DE USUARIO-->
                        <div class="form-group" style="margin-top:-10px">
                                <label class="control-label col-sm-5"><strong style="color:#03C1FB;">*</strong>Usuario: </label>
                                <input type="text" name="txtUsuario" id="txtUsuario" placeholder="Usuario" maxlength="80" class="form-control input-sm" onkeypress="return txtValida(event,'sin_espcio')" title="Ingrese el nombre del usuario" style="font-size:10px" value="<?php echo $row[10]?>" required/>
                            </div>
                        <!-- CONTRASEÑA ANTERIOR (con consulta a a tabla gs_usuario) -->
                        <div class="form-group" style="margin-top:-10px">
                            <label for="contrasennaanterior" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong> Contraseña Anterior:</label>
                            <input type="password" name="txtPassA" placeholder="Contraseña Anterior" id="txtPassA" maxlength="80" class="form-control input-sm col-sm-1" onkeypress="return txtValida(event,'todas')" title="Ingrese Contraseña Anterior" size="20" style="font-size:10px" value="<?php echo $row[9]?>" required/>
                            <a class="btn col-sm-1" style="width:40px" title="Mostrar Contraseña" onclick="return verPassA()"><i class="glyphicon glyphicon-eye-open"></i></a>
                        </div>
                        <!-- CONTRASEÑA NUEVA -->
                        <div class="form-group" style="margin-top:-10px">
                            <label for="contrasennanueva" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong> Contraseña Nueva:</label>
                            <input type="password" name="txtPassN" placeholder="Contraseña Nueva" id="txtPassN" maxlength="80" class="form-control input-sm col-sm-1" onkeypress="return txtValida(event,'todas')" title="Ingrese Contraseña Nueva" size="20" />
                            <a class="btn col-sm-1" style="width:40px" title="Mostrar Contraseña" onclick="return verPassN()"><i class="glyphicon glyphicon-eye-open"></i></a>
                        </div>
                        <!-- VERIFICACIÓN DE LA CONTRASEÑA NUEVA -->
                        <div class="form-group" style="margin-top:-10px">
                            <label for="contrasennanuevaverificacion" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>  Verificar Contraseña Nueva:</label>
                            <input type="password" name="txtPassNV" placeholder="Verificación" id="txtPassNV" maxlength="80" class="form-control input-sm col-sm-1" onkeypress="return txtValida(event,'todas')" title="Verificar Contraseña Nueva" size="20" />
                            <a class="btn col-sm-1" style="width:40px" title="Mostrar Contraseña" onclick="return verPassNV()"><i class="glyphicon glyphicon-eye-open" ></i></a>
                        </div>
                        <div class="form-group" style="margin-top: -10px;">
                            <label for="no" class="col-sm-5 control-label"></label>
                            <button onclick="return verificacion()" type="submit" class="btn btn-primary sombra" id="guardar" style="margin-top: -10px; margin-bottom:-10px; margin-left: 0px;" >Guardar</button>
                        </div>
                        <input type="hidden" name="MM_insert" >
                    </form>
                    
                </div>
            </div>
        <div class="col-sm-7 col-sm-3" style="margin-top:-22px">
            <table class="tablaC table-condensed"  style="margin-left: -30px">
                <thead>
                    <tr>
                        
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                             <a href="GF_DIRECCION_DB.php" class="btn btn-primary btnInfo">DIRECCIÓN</a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="GF_TELEFONO_DB.php" class="btn btn-primary btnInfo" >TELEFONO</a><br/>
                        </td>
                    </tr>
                </tbody>
            </table>                
        </div>
    </div>	
    
</div>
	<script type="text/javascript">
            //Función en la que se valida el tipo del campo txtPassA (contraseña anterior) y lo convierte a tipo password
            function verPassA(){
                if($("#txtPassA").val()!=0){
                    if($('#txtPassA').is(':text')){
                        $("#txtPassA").attr('type','password');
                    }else{
                        $("#txtPassA").attr('type','text');
                    }
                }
            }
            //Función en la que se valida el tipo del campo txtPassN (contraseña nueva) y lo convierte a tipo password
            function verPassN(){
                if($("#txtPassN").val()!=0){
                    if($('#txtPassN').is(':text')){
                        $("#txtPassN").attr('type','password');
                    }else{
                        $("#txtPassN").attr('type','text');
                    }
                } 
            }
            //Función en la que se valida el tipo del campo txtPassNV (verificacion contraseña nueva) y lo convierte a tipo password
            function verPassNV(){
                if($("#txtPassNV").val()!=0){
                    if($('#txtPassNV').is(':text')){
                        $("#txtPassNV").attr('type','password');
                    }else{
                        $("#txtPassNV").attr('type','text');
                    }
                }                
            }

            // Función que verificar que la  nueva contraseña y la verificación sean iguales
            function verificacion(){ 
   				passN = document.form.txtPassN.value 
   				passNV = document.form.txtPassNV.value

   				if ( $.trim(passN) != $.trim(passNV) ){
      				$("#verificacionPass").modal('show');  
      				return false;
      			}  				
			}

    </script>
    <?php require_once './footer.php'; ?>
    <script src="js/select/select2.full.js"></script>
    <link rel="stylesheet" href="css/bootstrap-theme.min.css">
    <script src="js/bootstrap.min.js"></script>

    <script>
      $(document).ready(function () {
          $(".select2_single").select2({
              allowClear: true
          });
      });
    </script>
    <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <link rel="stylesheet" href="../css/style.css">
 <script src="../js/jquery.min.js"></script>
 <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
 <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>

 	<!--  --> 
 		  <div class="modal fade" id="verificacionPass" role="dialog" align="center" >
            <div class="modal-dialog">
                <div class="modal-content">
                    <div id="forma-modal" class="modal-header">
                        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                    </div>
                    <div class="modal-body" style="margin-top: 8px">
                        <p>Verifique su nueva contraseña</p>
                    </div>
                    <div id="forma-modal" class="modal-footer">
                        <button type="button" id="verdatos" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                    </div>
                </div>
            </div>
    </div>
		
	</body>
</html>   	


