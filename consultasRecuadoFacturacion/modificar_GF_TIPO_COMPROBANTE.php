<?php
//llamado a la clase de conexion 
require_once('Conexion/conexion.php');
session_start();
//declaracion que recibe la variable que recibe el ID
$id = " ";
//validacion preguntando si la variable enviada del listar viene vacia
if (isset($_GET["id"]))
{ 
  $id_tipo = (($_GET["id"]));
  //Query o sql de consulta
  $query = "SELECT tc.id_unico, tc.nombre, tc.retencion, tc.interface, tc.niif, tc.clasecontable, cc.nombre,tc.tipodocumento, f.nombre
            FROM gf_tipo_comprobante tc 
            LEFT JOIN  gf_clase_contable cc ON tc.clasecontable=cc.id_unico 
            LEFT JOIN gf_tipo_documento f ON tc.tipodocumento=f.id_unico
            WHERE md5(tc.Id_Unico) = '$id_tipo'";
}
  /*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
  $resultado = $mysqli->query($query);
  $row = mysqli_fetch_row($resultado);
  //consultas para llenar los campos
  $clase = "SELECT id_unico, nombre FROM gf_clase_contable WHERE id_unico != '$row[5]' ORDER BY nombre ASC";
  $claseC =   $mysqli->query($clase);
?>
<!-- Llamado a la cabecera del formulario -->
  <?php require_once 'head.php'; ?>
  <title>Modificar Tipo Comprobante</title>
</head>
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menú del formulario -->  
    <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;margin-top: 0px">Modificar Tipo Comprobante Contable</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario -->
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoComprobanteJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>


            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


               <div class="form-group" style="margin-top: -20px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" value="<?php echo $row[1] ?>" required>
            </div>

<!---  para los radio Button 1 es verdadero, 2 es falso      -->
           <div class="form-group" style="margin-top: -10px;">
              <label for="reten" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Retención:</label>
              <?php switch ($row[2]) {
                case 1: ?>
                  <input type="radio" name="reten" id="reten"  value="1" checked>SI
                  <input type="radio" name="reten" id="reten" value="2" >NO                  
                <?php
                  break;
                case 2: ?>
                <input type="radio" name="reten" id="reten"  value="1" >SI
                <input type="radio" name="reten" id="reten" value="2" checked>NO
              <?php
                  break;
              } ?>
            </div>

<!---  para los radio Button 1 es verdadero, 2 es falso      -->
           <div class="form-group" style="margin-top: -10px;">
              <label for="inter" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Interfaz:</label>
              <?php switch ($row[3]) {
                case 1: ?>
                  <input type="radio" name="inter" id="inter"  value="1" checked>SI
                  <input type="radio" name="inter" id="inter" value="2" >NO                  
                <?php
                  break;
                case 2: ?>
                <input type="radio" name="inter" id="inter"  value="1" >SI
                <input type="radio" name="inter" id="inter" value="2" checked>NO
              <?php
                  break;
              } ?>
            </div>
          
<!---  para los radio Button 1 es verdadero, 2 es falso      -->
           <div class="form-group" style="margin-top: -10px;">
              <label for="nif" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Retención:</label>
              <?php switch ($row[4]) {
                case 1: ?>
                  <input type="radio" name="nif" id="nif"  value="1" checked>SI
                  <input type="radio" name="nif" id="nif" value="2" >NO                  
                <?php
                  break;
                case 2: ?>
                <input type="radio" name="nif" id="nif"  value="1" >SI
                <input type="radio" name="nif" id="nif" value="2" checked>NO
              <?php
                  break;
              } ?>
            </div>
          

            <div class="form-group " style="margin-top: -10px">
              <label for="claseC" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Contable:</label>
              <select name="claseC" id="claseC" class="form-control" title="Seleccione la clase contable" required>
                <option value="<?php echo $row[5]?>"><?php echo $row[6]?></option>
                <?php while($rowC = mysqli_fetch_assoc($claseC)){?>
                <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords(                                            (strtolower($rowC['nombre'])));}?></option>;
              </select> 
            </div>


         <div class="form-group" style="margin-top: -20px;">
              <label for="formato" class="col-sm-5 control-label">Tipo Documento:</label>
              <select name="formato" id="formato" class="form-control" title="Seleccione el formato">
                <?php  
                if(!empty($row[7])){
                  echo "<option value='".$row[7]."'>".utf8_decode(ucwords(strtolower($row[8])))."</option>";
                  $forma = "SELECT id_unico, nombre FROM gf_tipo_documento WHERE id_unico != '$row[7]' ORDER BY nombre ASC";
                  $formato = $mysqli->query($forma);
                  while ($td= mysqli_fetch_row($formato)) {
                    echo "<option value='".$td[0]."'>".utf8_encode(ucwords(strtolower($td[1])))."</option>";
                  }
                  echo "<option value=''></option>";
                }else{
                  echo "<option value=''></option>";
                  $sqlTD = "SELECT id_unico,nombre FROM gf_tipo_documento";
                  $resultTD = $mysqli->query($sqlTD);
                  while ($td=mysqli_fetch_row($resultTD)) {
                    echo "<option value='".$td[0]."'>".utf8_decode(ucwords(strtolower($td[1])))."</option>";
                  }                  
                }
                ?>                
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
  

</body>
</html>
