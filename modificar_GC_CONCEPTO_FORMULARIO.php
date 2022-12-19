
<?php 

//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
 // session_start();

$id=$_GET['id'];  
$sql= "SELECT cf.*,cc.codigo,cc.descripcion FROM gc_concepto_formulario cf 
      LEFT JOIN gc_concepto_comercial cc ON cc.id_unico=cf.concepto_comercial
      WHERE md5(cf.id_unico)='$id'";
$resultado= $mysqli->query($sql);
$row=mysqli_fetch_row($resultado);

?>

<!-- Llamado a la cabecera del formulario -->
<?php require_once 'head.php'; ?>
<title>Modificar Concepto Formulario</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<body>
 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menu del formulario -->    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

   
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 10px; margin-right: 4px; margin-left: 4px;">Modificar Concepto Formulario</h2>
      <!--volver-->
      <a href="listar_GC_CONCEPTO_FORMULARIO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:8px;margin-top: -5.5px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>


      <h5 id="forma-titulo3a" align="center" style="width:96.5%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-10px;  background-color: #0e315a; color: white; border-radius: 5px;color:#0e315a;">.</h5> 
      <!---->


      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonComercio/modificarConceptoFormularioJson.php">

          <input type="hidden" name="id" value="<?php echo $row[0] ?>">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>


            <div class="form-group" style="margin-top: -10px;">
              <label for="codigo" class="col-sm-5 control-label">Código:</label>
                <input type="text" name="codigo" id="codigo" class="form-control" maxlength="15" title="Ingrese el Código" onkeypress="return txtValida(event, 'car')" placeholder="Código"  value="<?php echo $row[1] ?>">
            </div>  

             <div class="form-group" style="margin-top: -10px;">
                 <label for="renglon" class="col-sm-5 control-label"><strong class="obligado">*</strong>Renglón:</label>
                <input type="text" name="renglon" id="renglon" class="form-control" maxlength="15" title="Ingrese el Renglón" onkeypress="return txtValida(event,'num')" placeholder="Renglón" value="<?php echo $row[2] ?>" required>
            </div>



            <?php
            $idConceptoComercial=$row[3];
            $s = "SELECT id_unico,codigo,descripcion FROM gc_concepto_comercial WHERE id_unico!=$idConceptoComercial";
            $r = $mysqli->query($s);
            ?>
            <div class="form-group" style="margin-top: -10px;">
                <label for="sector" class="col-sm-5 control-label"><strong class="obligado">*</strong>Concepto Comercial:</label>
                <select  name="conceptoComercial" id="sector"  style="height: auto" class="select2_single form-control" title="Seleccione Sector" required="">

                    <option value="<?php echo $row[3] ?>"><?php echo $row[4]."-".$row[5] ?></option>

                        <?php while($rw=mysqli_fetch_array($r)){ ?>
                                     <option value="<?php echo $rw['id_unico']?>"><?php echo ucwords(mb_strtolower($rw['codigo']."-".$rw['descripcion'])); ?></option>
                        <?php } ?>
                </select>
            </div><br>

              <div class="form-group" style="margin-top: 10px;">
                <label for="no" class="col-sm-5 control-label"></label>
                  <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
              </div>

            <!-- <input type="hidden" name="code" value=""/> -->

             <div class="texto" style="display: none"></div>
            <input type="hidden" name="MM_insert" >
          </form>
<!-- Fin de división y contenedor del formulario -->           
        </div>     
    </div>
  </div>
  <!-- Fin del Contenedor principal -->
</div>
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php' ?>  

<!-- modal para la validacion del código -->
<div class="modal fade" id="myModal1" role="dialog" align="center" >
      <div class="modal-dialog">
          <div class="modal-content">
              <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Este Código ya existe.¿Desea actualizar la información?</p>
            </div>
            <div id="forma-modal" class="modal-footer">
                <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                <button type="button" class="btn" style="color: #000; margin-top: 2px"  data-dismiss="modal" id="ver2">Cancelar</button>
            </div>
          </div>
      </div>
    </div>

<!-- validacion de los codigo  -->
<script type="text/javascript">
      function existente(){
        var codi = document.getElementById("codigo").value;     
        var result = '';
        
        if( codi == null ||  codi == '' ||  codi == "Codigo"){

          $("#myModal2").modal('show');//consulta si el campo tiene algun valor, pero como es en el mdificar siempre va tener un dato, no se necesita
          
        }else{ //se hace un envio por POST tomando el valor del camppo y consultando y como resultado me imprime un campo oculto con el ID y un modal preguntando si deseo cargar los datos.

          $.ajax({
            data: {"cod": codi},
            type: "POST",
            url: "consultarTipoComprobante.php",
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
      var id = document.getElementById("id").value;
      console.log(id);
      document.location = 'modificar_GF_TIPO_COMPROBANTE_PPTAL.php?id='+id;
      });

</script>
<script type="text/javascript">
  $('#ver2').click(function(){
    var dato= document.form.code.value;
    $("#codigo").val(dato)
  });

</script>
     <script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<!-- select2 -->
 

  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
</body>
</html>

