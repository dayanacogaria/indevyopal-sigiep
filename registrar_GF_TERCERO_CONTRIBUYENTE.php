<?php 
  require_once 'head.php'; 
	require_once('Conexion/conexion.php');

  $_SESSION['perfil'] = "EA"; //Entidad Afiliación
  $_SESSION['url'] = "registrar_GF_TERCERO_ENTIDAD_AFILIACION.php";

  //Consultas para el listado de los diferentes combos correspondientes.
  //Tipo Identificación.
  $sqlTipoIden = "SELECT DISTINCT Id_Unico, Nombre 
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


  //Representante Legal.
  $sqlReprLeg = "SELECT t.Id_Unico, t.NombreUno, t.NombreDos, t.ApellidoUno, t.ApellidoDos, t.NumeroIdentificacion, ti.Nombre 
  FROM gf_tercero t, gf_tipo_identificacion ti, gf_perfil_tercero pt   
  WHERE t.TipoIdentificacion = ti.Id_Unico
  AND t.Id_Unico = pt.Tercero 
  AND pt.Perfil = 10
  ORDER BY t.NombreUno ASC";
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

<title>Registrar Tercero Contribuyente</title>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>

</head>

   <body>
<script src="dist/jquery.validate.js"></script>

   <style>
    label #tr-error,#depto-error,#rl-error {
        display: block;
        color: #155180;
        font-weight: normal;
        font-style: italic;

    }

   </style>


   <script>


    $().ready(function() {
        var validator = $("#form").validate({
            ignore: "",

            errorPlacement: function(error, element) {

                $( element )
                    .closest( "form" )
                    .find( "label[for='" + element.attr( "id" ) + "']" )
                    .append( error );
            },
            rules: {
                sltmes: {
                    required: true
                },
                sltcni: {
                    required: true
                },
                sltAnnio: {
                    required: true
                }
            }
        });

        $(".cancel").click(function() {
            validator.resetForm();
        });
    });
</script>
        <!-- Inicio de Contenedor principal -->
    <div class="container-fluid text-center" >
        <!-- Inicio de Fila de Contenido -->
        <div class="content row">
            <!-- Lllamado de menu -->
            <?php require_once 'menu.php'; ?>
            <!-- Inicio de contenedor de cuerpo contenido -->
            <div class="col-sm-7 text-left" style="margin-top: -20px"> 
                <!-- Titulo de Formulario -->
                <h2 align="center" class="tituloform">Registrar Tercero Contribuyente</h2>
                <a href="LISTAR_GF_TERCERO_CONTRIBUYENTE.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>


                  <h5 id="forma-titulo3a" align="center" style="width:94%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px;color:#0e315a">.</h5> 

                  



                <!-- Contenedor del formulario -->
                <div class="client-form contenedorForma">
                    <!-- Inicio de Formulario -->
                    <form id="form" name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarTerceroContribuyenteJson.php">

                        <!-- Párrafo de texto-->
                        <p align="center" class="parrafoO" >Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                         <div class="form-group form-inline" style="margin-top:-20px">

                            <label for="tipoIdent" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Número Identificación:</label>                            
                            <select name="tipoIdent" id="tipoIdent" class="form-control col-sm-5" style="height: 33px;width:113px" title="Tipo Identificación" required>
                                <option>Tipo Ident.</option>
                                <?php while ($ma = mysqli_fetch_assoc($tipoIden)) { ?>
                                    <option value="<?php echo $ma["Id_Unico"]; ?>">
                                        <?php echo ucwords( (mb_strtolower($ma["Nombre"]))); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            
                            <span class="col-sm-1" style="width:1px; margin-top:8px;"></span>
                            
                            <input type="text" name="noIdent" id="noIdent" class="form-control col-sm-5" maxlength="20" title="Ingrese el número de identificación" onkeypress="return txtValida(event,'num')" placeholder="Número" style="width:95px" style="height: 30px" required onblur="CalcularDv();return existente()" />

                            <span class="col-sm-1" style="width:1px; margin-top:8px;"><strong> - </strong></span>

                            <input type="text" name="digitVerif" id="digitVerif" class="form-control " style="width:30px" maxlength="1" placeholder="0" title="Dígito de verificación" onkeypress="return txtValida(event,'num')" placeholder="" readonly="" style="height: 30px"/>

                        </div>

                        <div class="form-group" style="margin-top: -10px;">
                            <label for="sucursal" class="col-sm-5 control-label">Sucursal:</label>
                            <div class="col-sm-4 col-md-4 col-lg-4">
                            <select name="sucursal" id="s"   class="form-control" title="Seleccione Sucursal" >
                                <option value="">Sucursal</option>
                                <?php while($rowS=mysqli_fetch_row($sucursal)){ ?> 
                                    <option value="<?php echo $rowS[0]?>"><?php echo $rowS[1] ?></option>
                                <?php } ?>
                            </select>
                            </div>
                        </div>


            <!--<div class="form-group" >
              <label for="sucursal" class="col-sm-5 control-label">Sucursal:</label>

              <select name="sucursal" id="s" class="" title="Ingrese el tipo de identificación" >
                <option value="">Sucursal</option>
                   <?php while($rowS = mysqli_fetch_row($sucursal))
                   {  ?>
                <option value="<?php echo $rowS[0] ?>"><?php echo ucwords( (strtolower($rowS[1]))); ?></option>
                   <?php
                   }  ?>
              </select> 
            </div><br>-->


            <div class="form-group" >
              <label for="razoSoci" class="col-sm-5 control-label">Razón Social:</label>
                <div class="col-sm-4 col-md-4 col-lg-4" >

                <input style="width: 100%" type="text" name="razoSoci" id="razoSoci" class="form-control" maxlength="500" title="Ingrese la razón social" onkeypress="return txtValida(event)" onkeyup="javascript:this.value=this.value.toUpperCase();" placeholder="Razón Social" >

                </div>
               
            </div>


            <div class="form-group" style="margin-top: -10px;" >
              <label for="tr" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Régimen:</label>
               
                <div class="col-sm-4 col-md-4 col-lg-4">
                <select name="tipoReg" id="tr" required="true"  class="form-control" title="Seleccione Tipo Régimen"  required="">
                            <option value="">Tipo Régimen</option>
                      <?php while($rowTR = mysqli_fetch_row($tipoReg))
                    {  ?>
                    <option value="<?php echo $rowTR[0] ?>"><?php echo ucwords( (strtolower($rowTR[1]))); ?></option>
                      <?php
                    }  ?>
                </select>
                </div>
            </div>


           <!-- <div class="form-group" style="; ">
              <label for="tipoReg" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Régimen:</label>
              <select name="tipoReg" id="tipoReg" class="form-control" title="Ingrese el tipo de régimen" required>
                <option value="">Tipo Régimen</option>
                  <?php while($rowTR = mysqli_fetch_row($tipoReg))
                {  ?>
                <option value="<?php echo $rowTR[0] ?>"><?php echo ucwords( (strtolower($rowTR[1]))); ?></option>
                  <?php
                }  ?>
              </select> 
            </div>-->

            <div class="form-group" >
              <label for="tipoEmp" class="col-sm-5 control-label">Tipo Empresa:</label>
               
                <div class="col-sm-4 col-md-4 col-lg-4">
                <select name="tipoEmp" id="te"   class="form-control" title="Seleccione el tipo de empresa"  >
                <option value="">Tipo Empresa</option>
                   <?php while($rowTE = mysqli_fetch_row($tipoEmp))
                   {  ?>
                <option value="<?php echo $rowTE[0] ?>"><?php echo ucwords( (strtolower($rowTE[1]))); ?></option>
                   <?php
                }  ?>
                </select>
                </div>
            </div>
           <!-- <div class="form-group" style="; ">
              <label for="tipoEmp" class="col-sm-5 control-label">Tipo Empresa:</label>
              <select name="tipoEmp" id="tipoEmp" class="form-control" title="Ingrese el tipo de empresa" >
                <option value="">Tipo Empresa</option>
                   <?php while($rowTE = mysqli_fetch_row($tipoEmp))
                   {  ?>
                <option value="<?php echo $rowTE[0] ?>"><?php echo ucwords( (strtolower($rowTE[1]))); ?></option>
                   <?php
                }  ?>
              </select> 
            </div>-->
            <div class="form-group" style=";">
              <label for="rl" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Representante Legal:</label>
               
                <div class="col-sm-4 col-md-4 col-lg-4">
                <select name="repreLegal" id="rl" required="true"  class="form-control" title="Seleccione Representante legal"  required="">
                <option value="">Representante Legal</option>
                  <?php while($rowRL = mysqli_fetch_row($repreLegal))
                  {  ?>
                <option value="<?php echo $rowRL[0] ?>">
                  <?php echo ucwords( (strtolower($rowRL[1]." ".$rowRL[2]." ".$rowRL[3]." ".$rowRL[4]." (".$rowRL[6].", ".$rowRL[5].")"))); ?>
                </option>
                  <?php
                }  ?>
                </select>
                </div>
            </div>

            <!-- <div class="form-group" style="; ">
              <label for="repreLegal" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Representante Legal:</label>
              <select name="repreLegal" id="repreLegal" class="form-control" title="Ingrese el representante legal" required>
                <option value="">Representante Legal</option>
                  <?php while($rowRL = mysqli_fetch_row($repreLegal))
                  {  ?>
                <option value="<?php echo $rowRL[0] ?>">
                  <?php echo ucwords( (strtolower($rowRL[1]." ".$rowRL[2]." ".$rowRL[3]." ".$rowRL[4]." (".$rowRL[6].", ".$rowRL[5].")"))); ?>
                </option>
                  <?php
                }  ?>
              </select> 
            </div>-->

            <div class="form-group form-inline" style="">
                            <label for="depto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Ubicación:</label>     
                            
                            <div class="classDepto">
                                
                                <select name="depto" id="depto" class="form-control col-sm-5" style="height: 20%;width:170px" title="Seleccione Departamento" required>
                                </select>
                                <script type="text/javascript">
                                        $(document).ready(function(){
                                        $.ajax({
                                        type: "POST",
                                        url: "Departamento.php",
                                        success: function(response){
                                                $('.classDepto select').html(response).fadeIn();
                                        }
                                        });

                                        });
                                </script>
                            </div>
                            
                            <span class="col-sm-1" style="width:1px"></span>
                            
                            <div class="ClassCiudad">
                                <select name="ciudad" style="height: 24%;width:100px" id="ciudad" class="form-control" title="Seleccione Ciudad" required>
                                    <option value="">Ciudad</option>
                                </select>
                                <script type="text/javascript">
                                        $(document).ready(function(){
                                        $(".classDepto select").change(function(){
                                                var form_data = {
                                                is_ajax: 1,
                                                id_depto: +$(".classDepto select").val()
                                                };
                                                $.ajax({
                                                type: "POST",
                                                url: "Ciudad.php",
                                                data: form_data,
                                                success: function(response){
                                                $('.ClassCiudad select').html(response).fadeIn();
                                        }
                                                });
                                        });
                                        });
                                </script>
                            </div>
                        </div>

            <div class="form-group" style="margin-top: -10px;">
              <label for="contacto" class="col-sm-5 control-label">Contacto:</label>
               
                <div class="col-sm-4 col-md-4 col-lg-4">
                <select name="contacto" id="con"   class="form-control" title="Ingrese el Contacto" >
                 <option value="">Contacto</option>
          <?php while($rowCon = mysqli_fetch_row($contacto))
                {  ?>
                <option value="<?php echo $rowCon[0] ?>">
                <?php echo ucwords( (strtolower($rowCon[1]." ".$rowCon[2]." ".$rowCon[3]." ".$rowCon[4]." (".$rowCon[6].", ".$rowCon[5].")"))); ?>
                </option>
          <?php
                }  ?>
                </select>
                </div>
            </div>

           <!-- <div class="form-group" style="; ">
              <label for="contacto" class="col-sm-5 control-label">Contacto:</label>
              <select name="contacto" id="contacto" class="form-control" title="Ingrese el contacto">
                <option value="">Contacto</option>
          <?php while($rowCon = mysqli_fetch_row($contacto))
                {  ?>
                <option value="<?php echo $rowCon[0] ?>">
                <?php echo ucwords( (strtolower($rowCon[1]." ".$rowCon[2]." ".$rowCon[3]." ".$rowCon[4]." (".$rowCon[6].", ".$rowCon[5].")"))); ?>
                </option>
          <?php
                }  ?>
              </select> 
            </div>-->
            <div class="form-group" style=";">
              <label for="contacto" class="col-sm-5 control-label">Zona:</label>
               
                <div class="col-sm-4 col-md-4 col-lg-4">
                <select name="zona" id="zon"   class="form-control" title="Ingrese el Zona"  >
                <option value="">Zona</option>
          <?php while($rowZ = mysqli_fetch_row($zona))
                {  ?>
                <option value="<?php echo $rowZ[0] ?>"><?php echo ucwords( (strtolower($rowZ[1]))); ?></option>
          <?php
                }  
                echo '<option value=""></option>';
                ?>
                </select>
                </div>
            </div><br>

          <!--  <div class="form-group" style="; ">
              <label for="zona" class="col-sm-5 control-label">Zona:</label>
              <select name="zona" id="zona" class="form-control" title="Ingrese la zona">
                <option value="">Zona</option>
          <?php while($rowZ = mysqli_fetch_row($zona))
                {  ?>
                <option value="<?php echo $rowZ[0] ?>"><?php echo ucwords( (strtolower($rowZ[1]))); ?></option>
          <?php
                }  
                echo '<option value=""></option>';
                ?>
              </select> 
            </div>-->

            <div class="form-group" style="margin-top: 5px">
             <label for="no" class="col-sm-5 control-label"></label>
             <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;"><span class="glyphicon glyphicon-floppy-disk"></span></button>
            </div>

            <div class="texto" style="display:none"></div>
            <input type="hidden" name="MM_insert" >
          
          </form>
        </div>         
   
    </div> <!-- Cierra clase col-sm-7 text-left -->

           <!-- Botones de consulta 
            <div class="col-sm-2 col-sm-1">
                <table class="tablaC table-condensed" style="margin-left: -30px;margin-top:-22px">
                    <thead>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                            <td>
                              <a href="GF_CUENTA_BANCARIA_TERCERO.php" ><button class="btn btn-primary btnInfo" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un  asociado jurídica."';}?> disabled="true" >CUENTA BANCARIA</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                               <a href="GF_TIPO_ACTIVIDAD_TERCERO.php" ><button class="btn btn-primary btnInfo" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un  asociado jurídica."';}?> disabled="true" >TIPO ACTIVIDAD</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="GF_TELEFONO.php" ><button class="btn btn-primary btnInfo" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un  asociado jurídica."';}?> disabled="true" >TELEFONO</button></a><br/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <a href="GF_CONDICION_TERCERO.php" ><button class="btn btn-primary btnInfo" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar un  asociado jurídica."';}?> disabled="true" >CONDICIÓN</button></a><br/>

                            </td>
                        </tr>
                        <tr>
                          <td>
                            <a href="registrar_TERCERO_CONTACTO_NATURAL.php" <?php if (!isset($_SESSION['id_tercero'])){ echo ' disabled title="Debe primero ingresar una compañía."';}?> disabled="true" class="btn btnInfo btn-primary">CONTACTO</a>
                          </td>
                        </tr>
                        <tr>
                          <td>
                            <a disabled="true" class="btn btnInfo btn-primary" style="margin-top:15px">PERFIL CONDICIÓN</a>
                          </td>
                        </tr>
                    </tbody>
                </table>
            </div>-->
            </div>
            </div>
            <!-- Fin de Contenedor Principal -->
            <?php require_once('footer.php'); ?>

<!-- Divs clase Modal para notificar la existencia del número de identificación y su posible modificación  -->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
      <div class="modal-dialog">
          <div class="modal-content">
              <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Este número de identificación  ya existe.¿Desea actualizar la información?</p>
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

  <script src="../js/bootstrap.min.js"></script>

    <script type="text/javascript" src="js/select2.js"></script>

    <script>
        $("#s").select2();
        $("#tr").select2();
        $("#te").select2();
        $("#rl").select2();
        $("#dep").select2();
        $("#ciu").select2();
        $("#con").select2();
        $("#zon").select2();
    </script>


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
            url: "consultarJuridica.php",
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
        document.location = 'modificar_GF_TERCERO_CONTRIBUYENTE.php?id='+id;
      });

  </script>
  
</body>
</html>