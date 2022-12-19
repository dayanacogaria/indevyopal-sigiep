<!-- Llamado a la cabecera del formulario -->
<?php require_once 'head.php';  ?>
<?php 
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
//declaracion que recibe la variable que recibe el ID
  $id_tipof = " ";
//validacion preguntando si la variable enviada del listar viene vacia
  if (isset($_GET["id_tipof"])){ 
    $id_tipof= (($_GET["id_tipof"]));
//Query o sql de consulta
  $queryTipof = "SELECT Id_Unico, Nombre FROM gf_tipo_fuente  WHERE md5(Id_Unico) ='$id_tipof'";

}

/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
$resultado = $mysqli->query($queryTipof);
$row = mysqli_fetch_row($resultado);

?>
  <title>Modificar Tipo Fuente</title>
</head>

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">
<!-- Llamado al menú del formulario -->   
    <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Tipo Fuente</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario -->
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoFuenteJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="tipof" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="tipof" id="tipof" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo $row[1] ?>" required>
            </div>
          
<div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>
            <input type="hidden" name="MM_insert" >
          </form>
<!-- Fin de división y contenedor del formulario -->          
        </div>     
    </div>
  </div>
<!-- Fin del Contenedor principal -->   
</div>

<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'; ?>
  </div>

<!-- funcion para validar los campos -->
<script>
  function txtValida(elEvento, permitidos)
  {
    var numeros = "0123456789"; 
      var caracteres = " abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
      var numeros_caracteres = numeros + caracteres; 
      var teclas_especiales = [8, 20]; //Ascii retroseso y espacio
      var num = 0;
      
      switch(permitidos)
      {
        case 'num':
          permitidos = numeros;
          num = 1;
          break;
        case 'car':
          permitidos = caracteres;
          break;
        case 'num_car':
          permitidos = numeros_caracteres;
          break;
      }
      
      var evento = elEvento || window.event;
      var codigoCaracter = evento.charCode || evento.keyCode;
      var caracter = String.fromCharCode(codigoCaracter);
      
      var tecla_especial = false;

      if(num == 0)
      {
        for(var i in teclas_especiales)
        {
          if(codigoCaracter == teclas_especiales[i])
          {
            tecla_especial = true;
            break;
          }
        }
      }
         
      return permitidos.indexOf(caracter) != -1 || tecla_especial;  
  }
  
</script> 
</body>
</html>
