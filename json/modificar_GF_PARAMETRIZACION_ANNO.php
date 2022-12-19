<?php 
//llamado a la clase de conexion
require_once('Conexion/conexion.php');
  session_start();
//declaracion que recibe la variable que recibe el ID
  $id_param = " ";
//validacion preguntando si la variable enviada del listar viene vacia  
  if (isset($_GET["id_param"]))
  { 
    $id_param = (($_GET["id_param"]));
//Query o sql de consulta     
    $queryParam = "SELECT P.Id_Unico, 
                          P.Anno, 
                          P.SalarioMinimo, 
                          P.MinDepreciacion, 
                          P.UVT, 
                          P.CajaMenor, 
                          E.Id_Unico, 
                          E.Nombre
      FROM gf_parametrizacion_anno P 
      LEFT JOIN gf_estado_anno E ON P.EstadoAnno = E.Id_Unico 
      WHERE md5(P.Id_Unico) ='$id_param'";
  }

/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
  $resultado = $mysqli->query($queryParam);
  $row = mysqli_fetch_row($resultado);


  $estadoAn = "SELECT Id_Unico, Nombre FROM gf_estado_anno ORDER BY Nombre ASC";
  $estadoA =   $mysqli->query($estadoAn);

?>

<!-- Llamado a la cabecera del formulario -->
  <?php require_once 'head.php';  ?>
  <title>Modificar Parametrizacion Año</title>
</head> 

<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">
<!-- Llamado al menú del formulario -->     
    <?php require_once 'menu.php'; ?>
      <div class="col-sm-7 text-left">
        <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Modificar Parametrización Año</h2>

        <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class=" client-form">
<!-- Inicio del formulario --> 
          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarParamAnnoJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


            <div class="form-group" style="margin-top: -10px;">
              <label for="valor" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año:</label>
                <input type="number" name="valor" id="valor" onblur="return existente()"  class="form-control" maxlength="4" title="Ingrese el año" onkeypress="return txtValida(event, 'num')" placeholder="Año" value="<?php echo $row[1]?>"  required>
            </div>

            <div class="form-group" style="margin-top: -10px;">
                <label for="salariom" class="col-sm-5 control-label">Salario Mínimo:</label>
                  <input type="number" name="salariom" id="salariom" onkeyup="this.value = this.value.slice(0,16)" class="form-control" maxlength="150" title="Ingrese el salario mínimo" onkeypress="return txtValida(event, 'dec', 'salariom', '2')" placeholder="Salario mínimo"  value="<?php echo $row[2]?>">
            </div>   

            <div class="form-group" style="margin-top: -10px;">
               <label for="minimod" class="col-sm-5 control-label">Mínimo Depreciación:</label>
               <input type="number" name="minimod" id="minimod" onkeyup="this.value = this.value.slice(0,16)" class="form-control" maxlength="150" title="Ingrese el mínimo depreciación" onkeypress="return txtValida(event, 'dec', 'salariom', '2')" placeholder="Mínimo depreciación" value="<?php echo $row[3] ?>">
            </div>         
              
            <div class="form-group" style="margin-top: -10px;">
              <label for="uvt" class="col-sm-5 control-label">UVT:</label>
              <input type="number" name="uvt" id="uvt" onkeyup="this.value = this.value.slice(0,16)" class="form-control" maxlength="150" title="Ingrese UVT" onkeypress="return txtValida(event, 'dec', 'salariom', '2')" placeholder="UVT" value="<?php echo $row[4] ?>">
            </div>

            <div class="form-group" style="margin-top: -10px;">
              <label for="cajam" class="col-sm-5 control-label">Caja Menor:</label>
              <input type="number" name="cajam" id="cajam" onkeyup="this.value = this.value.slice(0,16)" class="form-control" maxlength="150" title="Ingrese caja menor" onkeypress="return txtValida(event, 'dec', 'salariom', '2')" placeholder="Caja menor"  value="<?php echo $row[5]?>">
            </div>

            <div class="form-group">
              <label for="estadoA" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado Año:</label>
              <select name="estadoA" id="estadoA" class="form-control" title="Seleccione el estado año" required>
                <option value="<?php echo $row[6];?>"><?php echo utf8_encode($row[7]);?></option>
                <?php while($row = mysqli_fetch_assoc($estadoA)){?>
                <option value="<?php echo $row['Id_Unico'] ?>"><?php echo ucwords(utf8_encode(strtolower($row['Nombre'])));}?></option>;
              </select> 
            </div>

          
          <div align="center">
            <div align="center" style="margin-top: -10px;">
                <button type="submit" class="btn btn-primary sombra" >Guardar</button>
            </div>
          </div>

          <div class="texto" style=""></div>

            <input type="hidden" name="MM_insert" >
          </form>
<!-- Fin de división y contenedor del formulario --> 
        </div>
    </div>
        <!-- Botones de consulta -->
            <div class="col-sm-7 col-sm-3">
                <table class="tablaC table-condensed" style="margin-left: -30px">
                    <thead>
                        <th>
                            <h2></h2>
                        </th>
                        <th>
                            <h2 class="titulo" align="center" style=" font-size:17px;">Información adicional</h2>
                        </th>
                    </thead>
                    <tbody>
                        <tr>
                            <td></td>
                            <td>
                                <button class="btn btn-primary btnInfo">ESTADO</button>
                            </td>
                        </tr>
                      </tbody>
                </table>
           </div>    
    </div>
  </div>

<!-- llamado al pie de pagina -->   
<?php require_once 'footer.php'; ?>

<div class="modal fade" id="myModal1" role="dialog" align="center" >
      <div class="modal-dialog">
          <div class="modal-content">
              <div id="forma-modal" class="modal-header">
                <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
            </div>
            <div class="modal-body" style="margin-top: 8px">
                <p>Este año  ya existe.¿Desea actualizar la información?</p>
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
        var anno = document.form.valor.value;     
        //var numI = document.form.noIdent.value;
        var result = '';
        
        if(anno == null || anno == '' || anno == "Año"){

          $("#myModal2").modal('show');
          
        }else{

          $.ajax({
            data: {"anio":anno},
            type: "POST",
            url: "consultarParametrizacion.php",
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
        document.location = 'modificar_GF_PARAMETRIZACION_ANNO.php?id_param='+id;
      });

  </script>


 </body>
</html>



