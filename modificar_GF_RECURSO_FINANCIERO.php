<!-- Llamado a la cabecera del formulario -->
<?php   require_once 'head.php'; ?>
<?php 
//llamado a la clase de conexion
require_once('Conexion/conexion.php');
//declaracion que recibe la variable que recibe el ID
$id_recurso = " ";
//validacion preguntando si la variable enviada del listar viene vacia
if (isset($_GET["id_recurso"])){ 
  $id_recurso = (($_GET["id_recurso"]));
//Query o sql de consulta 
$queryRecurso = "SELECT R.Id_Unico, R.Nombre, R.codi, R.tiporecursofinanciero, TR.Nombre, TR.id_unico
                 FROM gf_recurso_financiero R
                 LEFT JOIN gf_tipo_recurso_financiero TR ON R.tiporecursofinanciero = TR.Id_Unico
                 WHERE md5(R.Id_Unico) = '$id_recurso'";

}

/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
$resultado = $mysqli->query($queryRecurso);
$row = mysqli_fetch_row($resultado);

//consultas para llenar los combos
  $tipoRecursos = "SELECT id_unico, nombre FROM gf_tipo_recurso_financiero WHERE id_unico != '$row[5]' ORDER BY Nombre ASC";
  $tipoR =   $mysqli->query($tipoRecursos);
?>
<title>Modificar Recurso Financiero</title>
</head>

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">
<!-- Llamado al menú del formulario -->  
    <?php require_once 'menu.php'; ?>
 
    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Recurso Financiero</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarRecursoFinancieroJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

          <input type="hidden" name="dato" value="<?php echo $row[2];?>"/>
          <input type="hidden" name="id" value="<?php echo $row[0] ?>">
       
            <div class="form-group" style="margin-top: -22px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')"  value="<?php echo $row[1] ?>" onkeyup="javascript:this.value=this.value.toUpperCase();"  placeholder="Nombre" required>
            </div>

            <div class="form-group" style="margin-top: -22px;">
              <label for="codigo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código:</label>
              <input type="text" name="codigo" id="codigo" class="form-control" maxlength="15" title="Ingrese el código" value="<?php echo $row[2] ?>"onkeypress="return txtValida(event,'sin_espcio')" placeholder="Codigo"  onblur="return existente()" required>
            </div>


             <div class="form-group" style="margin-top: -22px;">
              <label for="tipoR" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Recurso Financiero:</label>
              <select name="tipoR" id="tipoR" class="form-control" title="Seleccione el tipo recurso financiero" required>
                <option value="<?php echo $row[3]?>"><?php echo $row[4]?></option>
                 <?php while($rowR = mysqli_fetch_row($tipoR)){?>
                <option value="<?php echo $rowR[0] ?>"><?php echo ucwords(                           (strtolower($rowR[1])));}?></option>
                 
              </select> 
            </div>



            <div align="center">
              <button type="submit" class="btn btn-primary sombra">Guardar</button>
            </div>
            </div>

<!-- DIV que contiene una clase oculta  -->
          <div class="texto" style="display: none"></div>
          
            <input type="hidden" name="MM_insert" >
          </form>
<!-- Fin de división y contenedor del formulario -->           
        </div>     
    </div>
<!-- Fin del Contenedor principal -->
</div>
</div> 
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'; ?>
 

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

<!-- validacion de los campos número y tipo de identificacion  -->
<script type="text/javascript">
      function existente(){
        var codi = document.form.codigo.value;     
        var result = '';
        
        if( codi == null ||  codi == '' ||  codi == "Codigo"){

          $("#myModal2").modal('show');//consulta si el campo tiene algun valor, pero como es en el mdificar siempre va tener un dato, no se necesita
          
        }else{ //se hace un envio por POST tomando el valor del camppo y consultando y como resultado me imprime un campo oculto con el ID y un modal preguntando si deseo cargar los datos.

          $.ajax({
            data: {"cod": codi},
            type: "POST",
            url: "consultarRecursoFinan.php",
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
      document.location = 'modificar_GF_RECURSO_FINANCIERO.php?id_recurso='+id;
      });

</script>
<script type="text/javascript">
  $('#ver2').click(function(){
    var dato= document.form.dato.value;
    $("#codigo").val(dato)
  });

</script>
</body>
</html>
