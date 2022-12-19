<?php 
require_once 'head.php';
require_once('Conexion/conexion.php');

//obtiene los datos para la consulta
$id_contrato = " ";
if (isset($_GET["id_contrato"])){ 
  $id_contrato = (($_GET["id_contrato"]));
//realiza la consulta con los datos que llegaron
  $queryContrato = "SELECT C.Id_Unico, C.Nombre, TC.Id_Unico, TC.Nombre
    FROM gf_clase_contrato C, gf_tipo_contrato TC
    WHERE C.TipoContrato = TC.Id_Unico
    AND md5(C.Id_Unico) = '$id_contrato'"; 


}

$resultado = $mysqli->query($queryContrato);
$row = mysqli_fetch_row($resultado);

$contratos = "SELECT Id_Unico, Nombre FROM gf_tipo_contrato WHERE Id_Unico != $row[2] ORDER BY Nombre ASC";
$contrato =   $mysqli->query($contratos);


?>
 <!--Titulo de la paginá-->

<title>Modificar Clase Contrato</title>
</head>
<body>


  
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php';?>
    <div class="col-sm-10 text-left">
       <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Clase Contrato</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarContratoJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">

 <!--Cargar la información para la modificación-->
            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" title="Ingrese el nombre" onkeypress="txNombres()" placeholder="Nombre" value="<?php echo ucwords((strtolower($row[1]))); ?>" required>
            </div>

            <div class="form-group">
              <label for="contrato" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Contrato:</label>
              <select name="contrato" id="contrato" class="form-control" title="Seleccione el tipo contrato" required>
                <option value="<?php echo $row[2] ?>"><?php echo ucwords((strtolower($row[3]))) ?></option>
                <?php while($rowC = mysqli_fetch_assoc($contrato)){?>
                  <option value="<?php echo $rowC['Id_Unico'] ?>"><?php echo ucwords((strtolower($row['Nombre'])));?></option> <?php }?>;
              </select> 
            </div>
          
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
            </div>
            
            <input type="hidden" name="MM_insert" >
          </form>
        </div>

      
      
    </div>

  </div>
</div>
<?php require_once 'footer.php'; ?>

</body>
</html>
