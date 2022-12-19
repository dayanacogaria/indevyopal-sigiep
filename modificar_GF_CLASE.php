<?php
#05/04/2017 --- Nestor B --- se agregó el atributo mb para que tome las tildes


require_once 'head.php';

//llamado a la clase de conexion 
  require_once('Conexion/conexion.php');
 // session_start();
//declaracion que recibe la variable que recibe el ID
$id_clase = " ";
//validacion preguntando si la variable enviada del listar viene vacia
if (isset($_GET["id_clase"]))
{ 
  $id_clase = (($_GET["id_clase"]));
//Query o sql de consulta
  $queryClase = "SELECT Cl.Id_Unico, Cl.Nombre, Cl.ClaseAso, C.Id_Unico,  C.Nombre
    FROM gf_clase Cl
    LEFT JOIN gf_clase C ON Cl.ClaseAso = C.Id_Unico
    WHERE md5(Cl.Id_Unico) = '$id_clase'";
}

/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
$resultado = $mysqli->query($queryClase);
$row = mysqli_fetch_row($resultado);

//consulta para llenar los campos
$clases = "SELECT Id_Unico, Nombre FROM gf_clase WHERE Id_Unico != $row[0] AND id_unico !=$row[2] ORDER BY Nombre ASC";
$clase =   $mysqli->query($clases);
?>

<!-- Llamado a la cabecera del formulario -->

<title>Modificar Clase</title>
<link href="css/select/select2.min.css" rel="stylesheet">
</head>

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menú del formulario -->  
    <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Clase</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario -->
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarClaseJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower ($row[1]));?>" required="required">
            </div>

            <div class="form-group">
              <label for="clase" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Clase Asociada:</label>
              <select name="clase" id="clase" class="select2_single form-control" title="Seleccione clase asociada" >
<!-- validacion para ordenar el dato que muestra los combos de llaves fk de la misma tabla junto con el campo en blanco-->             
                  
                  <?php   
                    if (empty($row[2])) {  
                            echo '<option value="">Clase Asociada</option>';
                            $cl = "SELECT Id_Unico, Nombre FROM gf_clase WHERE id_unico !=$row[0] ORDER BY Nombre ASC";
                            $clas =   $mysqli->query($cl);
                            while($rowCl = mysqli_fetch_row($clas)){?> 
                    <option value="<?php echo $rowCl[0]?>"><?php echo ucwords(mb_strtolower($rowCl[1]));?></option>
                    <?php }  
                    }else { ?>
                    <option value="<?php echo $row[3]?>"><?php echo ucwords(mb_strtolower($row[4])); ?></option> 
                    <?php
                    while($rowR = mysqli_fetch_row($clase)){?> 
                    <option value="<?php echo $rowR[0]?>"><?php echo ucwords(mb_strtolower($rowR[1]));?></option>
                    <?php } ?><option value=""></option><?php } ?>
              </select>
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
<script src="js/select/select2.full.js"></script>
        <script>
         $(document).ready(function() {
         $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
  </script>
  </div>

</body>
</html>
