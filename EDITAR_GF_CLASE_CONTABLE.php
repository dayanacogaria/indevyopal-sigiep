<?php
#05/04/2017 --- Nestor B --- se agrego el atributo mb para que tome las tildes y se agrego la busqueda rápido en el select

require_once('Conexion/conexion.php');

//session_start();

$id_clase = " ";
if (isset($_GET["id_clase"])){ 
  $id_clase = (($_GET["id_clase"]));

  $queryClaseC = "SELECT Cl.Id_Unico, Cl.Nombre,Cl.claseaso
    FROM gf_clase_contable Cl    
    WHERE md5(Cl.Id_Unico) = '$id_clase'";
}

$resultado = $mysqli->query($queryClaseC);
$row = mysqli_fetch_row($resultado);


require_once ('head.php');
?>
<title> Modificar Clase Contable</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<body>

  
<div class="container-fluid text-center">
  <div class="row content">
    
  <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">

      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Clase Contable</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarClaseContableJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" value="<?php echo $row[1] ?>" required>
            </div>

            <div class="form-group">
              <label for="claseC" class="col-sm-5 control-label">Clase asociada:</label>
              <select name="claseC" id="claseC" class="select2_single form-control" title="Seleccione clase asociada" > 

                  <?php    
                    if(!empty($row[2])){
                        $clasesC = "SELECT 
                                            Id_Unico, 
                                            Nombre 
                                    FROM 
                                            gf_clase_contable 
                                    WHERE Id_Unico = $row[2] 
                                    ORDER BY Nombre ASC";
                        $claseC =   $mysqli->query($clasesC);
                        $claseCC = mysqli_fetch_row($claseC);
                      
                        echo '<option value="'.$claseCC[0].'">'.ucwords(mb_strtolower($claseCC[1])).'</option>';
                        $sql1= "SELECT id_unico,nombre FROM gf_clase_contable WHERE id_unico != $row[2] AND id_unico != $row[0] ORDER BY nombre ASC";                        
                        $r = $mysqli->query($sql1);                        
                        while($row1= mysqli_fetch_row($r)){
                            echo '<option value="'.$row1[0].'">'.ucwords(mb_strtolower($row1[1])).'</option>';
                        }
                        echo '<option value=""></option>';
                  }else{
                      echo '<option value=""> - </option>';
                      $sql1= "SELECT id_unico,nombre FROM gf_clase_contable WHERE id_unico != $row[0] ORDER BY nombre ASC";                        
                        $r = $mysqli->query($sql1);                        
                        while($row1= mysqli_fetch_row($r)){
                            echo '<option value="'.$row1[0].'">'.ucwords(mb_strtolower($row1[1])).'</option>';
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
        </div>

      
      
    </div>

  </div>
</div>


  <?php require_once 'footer.php';  ?>
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
