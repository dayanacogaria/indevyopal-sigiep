<?php 
########## MODIFICACIONES ##############
#17/02/2017 | Erica G. *Modificación Búsqueda
########################################

 require_once 'head.php';
require_once('Conexion/conexion.php');
$anno = $_SESSION['anno'];

//consulta para cargar la informacion guardada con ese id
$id_concepto_rubro = " ";
$queryRubro="";
if (isset($_GET["id"])){ 
  $id_concepto_rubro = (($_GET["id"]));
  $_SESSION['url'] = 'Modificar_GF_CONCEPTO_RUBRO.php?id='.$id_concepto_rubro;
  
  $queryRubro = "SELECT CR.Id_Unico, CR.Rubro, CR.Concepto,R.Nombre,C.Nombre, R.codi_presupuesto 
    FROM gf_concepto_rubro CR 
    LEFT JOIN gf_rubro_pptal R on CR.Rubro= R.Id_Unico
    LEFT JOIN gf_concepto C on CR.concepto = C.Id_Unico
    WHERE md5(CR.Id_Unico)  = '$id_concepto_rubro'";
}


$resul = $mysqli -> query($queryRubro);

$row = mysqli_fetch_row($resul);

$comb1 = "SELECT id_Unico, nombre, codi_presupuesto "
        . "FROM gf_rubro_pptal WHERE Id_Unico != '$row[1]' "
        . "AND parametrizacionanno = $anno "
        . "ORDER BY codi_presupuesto ASC";
$combRubro =   $mysqli->query($comb1);


$comb2 = "SELECT id_Unico, nombre "
        . "FROM gf_concepto WHERE Id_Unico != '$row[2]' "
        . "AND parametrizacionanno = $anno"
        . "ORDER BY Nombre ASC";
$combConcepto =   $mysqli->query($comb2);

?>
  <!--titulo de  la página-->
<link href="css/select/select2.min.css" rel="stylesheet">
<title>Modificar Concepto Rubro</title>
</head>
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?> 
    <div class="col-sm-8 text-left">
        <!--titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Concepto Rubro</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarConcepto_Rubro.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">

              <!--Carga los datos para la modificación-->
            <div class="form-group" style="margin-top: -10px;">
              <label for="rubro" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Rubro:</label>
                
               <select name="rubro" id="rubro" class="select2_single form-control" title="Seleccione el tipo rubro" required>
                <option value="<?php echo $row[1] ?>"><?php echo ucwords((mb_strtolower($row[5].' - '.$row[3]))); ?></option>
                <?php while($rowC = mysqli_fetch_assoc($combRubro)){?>
                <option value="<?php echo $rowC['id_Unico'] ?>"><?php echo ucwords((mb_strtolower($rowC['codi_presupuesto'].' - '.$rowC['nombre'])));}?></option>;
              </select> 
            </div>

            <div class="form-group">
              <label for="concepto" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Concepto:</label>
              <select name="concepto" id="concepto" class="select2_single form-control" title="Seleccione el tipo concepto" required>
                <option value="<?php echo $row[2] ?>"><?php echo ucwords((strtolower($row[4]))); ?></option>
                <?php while($rowC = mysqli_fetch_assoc($combConcepto)){?>
                <option value="<?php echo $rowC['id_Unico'] ?>"><?php echo ucwords((mb_strtolower($rowC['nombre'])));}?></option>;
              </select> 
            </div>
          
            
            <div class="form-group" style="margin-top: 20px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left:0px">Guardar</button>
            </div>
            
            <input type="hidden" name="MM_insert" >
          </form>
        </div>

      
      
    </div>
      <!--Información adicional -->
        <div class="col-sm-6 col-sm-2" style="margin-top:-2px;" >
            <table class="tablaC table-condensed" style="margin-left: -3px; ">
                <thead>
                    <th>
                        <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                    </th>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="GF_CONCEPTO_RUBRO_CUENTA.php?id=<?php echo md5($row[0]);?>" class="btn btnInfo btn-primary">Cuenta</a><br/>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
  </div>
</div>
<?php require_once 'footer.php';?>
    <script src="js/select/select2.full.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
  </script>
</body>
</html>
