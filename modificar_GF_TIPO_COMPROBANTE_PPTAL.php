<?php
#######################################################################################################
# ************************************   Modificaciones   ******************************************* #
#######################################################################################################
#04/07/2018 | Erica G. | Afectado
#04/05/2017 | Erica G. | Diseño, tíldes, búsquedas
#27/02/2017 | Erica G. | Agregar campo vigencia actual
#######################################################################################################
require_once('Conexion/conexion.php');
require_once('Conexion/ConexionPDO.php'); 
$con = new ConexionPDO();
require_once 'head.php'; 
$compania = $_SESSION['compania'];

//declaracion que recibe la variable que recibe el ID
$id = " ";
//validacion preguntando si la variable enviada del listar viene vacia
if (isset($_GET["id"]))
{ 
  $id_tipoComp = (($_GET["id"]));
//Query o sql de consulta
 
    $queryfuente = "SELECT tcp.id_unico, tcp.codigo, tcp.nombre, 
        tcp.obligacionafectacion, tcp.terceroigual, tcp.clasepptal, 
        cp.nombre, tcp.tipodocumento, td.nombre, tcp.tipooperacion, 
        t.nombre, tcp.vigencia_actual, t.id_unico  , tcp.automatico , 
        tcpa.id_unico, tcpa.codigo, tcpa.nombre
    FROM gf_tipo_comprobante_pptal tcp 
    LEFT JOIN gf_clase_pptal cp ON tcp.clasepptal=cp.id_unico 
    LEFT JOIN gf_tipo_documento td ON tcp.tipodocumento = td.id_unico 
    LEFT JOIN gf_tipo_operacion t ON tcp.tipooperacion=t.id_unico
    LEFT JOIN gf_tipo_comprobante_pptal tcpa ON tcpa.id_unico = tcp.afectado  
    WHERE md5(tcp.Id_Unico) = '$id_tipoComp'";
}
/*Variable y proceso en el que se llama de manera embebida con la conexión el cual pérmite realizar el proceso de consulta*/
  $resultado = $mysqli->query($queryfuente);
  $row = mysqli_fetch_row($resultado);

//consultas para llenar los campos
  $clase = "SELECT id_unico, nombre FROM gf_clase_pptal WHERE id_unico != '$row[5]' ORDER BY nombre ASC";
  $claseP =   $mysqli->query($clase);

  $sqlTipDoc = "SELECT id_unico, nombre FROM gf_tipo_documento WHERE id_unico != '$row[7]' AND compania = $compania ORDER BY nombre ASC";
  $tipoDocumento = $mysqli->query($sqlTipDoc);

  $tipo = "SELECT id_unico, nombre FROM gf_tipo_operacion WHERE id_unico != '$row[12]' ORDER BY nombre ASC";
  $tipoO = $mysqli->query($tipo);

?>

<!-- Llamado a la cabecera del formulario -->
<?php ?>
<title>Modificar Tipo Comprobante Presupuestal</title>
</head>
<link href="css/select/select2.min.css" rel="stylesheet">
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menú del formulario -->  
    <?php require_once 'menu.php'; ?>

    <div class="col-sm-10 text-left">
      <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-right: 4px; margin-left: 4px;">Modificar Tipo Comprobante Presupuestal</h2>
      <a href="listar_GF_TIPO_COMPROBANTE_PPTAL.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
      <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px">Tipo Comprobante:<?php echo ((strtoupper($row[1]))); ?></h5>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

<!-- Inicio del formulario -->

          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/modificarTipoComprobantePptalJson.php">

          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>

           <input type="hidden" name="code" value="<?php echo $row[1];?>"/>
            <input type="hidden" name="id" value="<?php echo $row[0] ?>">


             <div class="form-group" style="margin-top: -22px;">
              <label for="codigo" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código:</label>
              <input type="text" name="codigo" id="codigo" class="form-control" maxlength="10" title="Ingrese el codigo" onkeypress="return txtValida(event, 'car')" onblur="return existente()" placeholder="Código" value="<?php echo mb_strtoupper($row[1])?>" required>
            </div>  
            
            <div class="form-group" style="margin-top: -20px;">
              <label for="nombre" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Nombre:</label>
              <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event, 'car')" placeholder="Nombre" value="<?php echo ucwords(mb_strtolower($row[2])) ?>" required>
            </div>


            <div class="form-group" style="margin-top: -20px;">
              <label for="obli" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Obligación Afectación:</label>
              <?php switch ($row[3]) {
                case 1: ?>
                  <input type="radio" name="obli" id="obli"  value="1" checked>SI
                  <input type="radio" name="obli" id="obli" value="2" >NO                  
                <?php
                  break;
                case 2: ?>
                <input type="radio" name="obli" id="obli"  value="1" >SI
                <input type="radio" name="obli" id="obli" value="2" checked>NO
              <?php
                  break;
              } ?>
            </div>



            <div class="form-group form-horizontal" style="margin-top: -10px">
              <label for="ter" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tercero Igual:</label>
              <?php switch ($row[4]) {
                case 1: ?>
                  <input type="radio" name="ter" id="ter"  value="1" checked>SI
                  <input type="radio" name="ter" id="ter" value="2" >NO                  
                <?php
                  break;
                case 2: ?>
                <input type="radio" name="ter" id="ter"  value="1" >SI
                <input type="radio" name="ter" id="ter" value="2" checked>NO
              <?php
                  break;
              } ?>
            </div>


           <div class="form-group" style="margin-top: -10px">
              <label for="claseP" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Clase Presupuestal:</label>
              <select name="claseP" id="claseP" class="select2_single form-control" title="Seleccione la clase presupuestal" required>
                <option value="<?php echo $row[5]?>"><?php echo $row[6]?></option>
                <?php while($rowC = mysqli_fetch_assoc($claseP)){?>
                <option value="<?php echo $rowC['id_unico'] ?>"><?php echo ucwords((mb_strtolower($rowC['nombre'])));}?></option>
               
              </select> 
            </div>
            <script>
                /**** Function Mostrar Afecados ****/
                $("#claseP").change(function(){
                    var clase = $("#claseP").val();
                    if(clase!=""){
                        var form_data = {estruc:33, clase:clase  };
                        var option ="<option value=''>Afectado</option>";
                        $.ajax({
                            type: "POST",
                            url: "jsonPptal/consultas.php",
                            data: form_data,
                            success: function(response)
                            { 
                                console.log(response);
                                option =option+response;
                                $("#afectado").html(option).focus();   
                            }   
                        }); 
                    }
                })
            </script>
            <div class="form-group" style="margin-top: -10px;">
              <label for="tipDocumento" class="col-sm-5 control-label">Tipo Documento:</label>
              <select name="tipDocumento" id="tipDocumento" class="select2_single form-control" title="Seleccione el tipo de documento">
                <?php 
                  if (empty($row[8]))
                  {
                    echo '<option value="">Tipo Documento</option>';
                  }
                  else
                  {
                    echo '<option value="'.$row[7].'">'.ucwords(mb_strtolower($row[8])).'</option>';?>
                  <option value=''> - </option>
                  <?php }
                 ?>
                <?php  while($rowF = mysqli_fetch_assoc($tipoDocumento)){?>
                <option value="<?php  echo $rowF['id_unico'] ?>"><?php  echo ucwords(mb_strtolower($rowF['nombre']));}?></option>;
              </select> 
            </div> 


            <div class="form-group" style="margin-top: -10px;">
              <label for="tipoO" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Tipo Operación:</label>
              <select name="tipoO" id="tipoO" class="select2_single form-control" title="Seleccione el tipo operacion" required>
                <option value="<?php echo $row[9]?>"><?php echo ucwords(mb_strtolower($row[10]));?></option>
                <?php while($rowT = mysqli_fetch_assoc($tipoO)){?>
                <option value="<?php echo $rowT['id_unico'] ?>"><?php echo ucwords(mb_strtolower($rowT['nombre']));}?></option>
               
              </select> 
            </div>
            <div class="form-group" style="margin-top: -10px;">
              <label for="vigenciaA" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Vigencia Actual:</label>
              <?php switch ($row[11]) {
                case 1: ?>
                  <input type="radio" name="vigenciaA" id="vigenciaA"  value="1" checked>SI
                  <input type="radio" name="vigenciaA" id="vigenciaA" value="2" >NO                  
                <?php
                  break;
                case 2: ?>
                <input type="radio" name="vigenciaA" id="vigenciaA"  value="1" >SI
                <input type="radio" name="vigenciaA" id="vigenciaA" value="2" checked>NO
              <?php
                  break;
              } ?>
            </div>
            <div class="form-group" style="margin-top: -10px;">
                <label for="automatico" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Automático:</label>
                <?php if($row[13]==1) { ?>
                <input type="radio" name="automatico" id="automatico"  value="1" checked>SI
                <input type="radio" name="automatico" id="automatico" value="2" >NO                  
                <?php } else { ?>
                <input type="radio" name="automatico" id="automatico"  value="1" >SI
                <input type="radio" name="automatico" id="automatico" value="2" checked>NO
                <?php } ?>
            </div>
            <div class="form-group" style="margin-top: -10px;">
              <label for="afectado" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Afectado:</label>
              <select name="afectado" id="afectado" class="select2_single form-control" title="Seleccione Afectado">
                   <?php if(empty($row[14])) { 
                        $id_c =0;
                        echo '<option value=""> - </option>';
                   } else {
                       $id_c = $row[14];
                       echo '<option value="'.$row[14].'">'.mb_strtoupper($row[15]).' - '.ucwords(mb_strtolower($row[16])).'</option>';
                       echo '<option value=""> - </option>';
                   }
                   $rowa="";
                   switch ($row[5]){
                       case 15:
                            $rowa = $con->Listar("SELECT id_unico, codigo, nombre 
                                FROM gf_tipo_comprobante_pptal 
                                WHERE id_unico != $id_c 
                                AND clasepptal = 14 
                                AND compania = $compania 
                                AND tipooperacion = 1 
                                AND vigencia_actual = 1");
                        break;
                        case 20:
                            $rowa = $con->Listar("SELECT id_unico, codigo, nombre 
                                FROM gf_tipo_comprobante_pptal 
                                WHERE id_unico != $id_c 
                                AND clasepptal = 15 
                                AND compania = $compania 
                                AND tipooperacion = 1 
                                AND vigencia_actual = 1");
                        break;
                        case 16:
                            $rowa = $con->Listar("SELECT id_unico, codigo, nombre 
                                FROM gf_tipo_comprobante_pptal 
                                WHERE id_unico != $id_c 
                                AND clasepptal = 20   
                                AND compania = $compania 
                                AND tipooperacion = 1 
                                AND vigencia_actual = 1");
                        break;
                        case 17:
                            $rowa = $con->Listar("SELECT id_unico, codigo, nombre 
                                FROM gf_tipo_comprobante_pptal 
                                WHERE id_unico != $id_c 
                                AND clasepptal = 16 
                                AND compania = $compania 
                                AND tipooperacion = 1 
                                AND vigencia_actual = 1");
                        break;
                    }
                    if(count($rowa)>0){
                        for ($i = 0;$i < count($rowa);$i++) {
                            echo '<option value="'.$rowa[$i][0].'">'.mb_strtoupper($rowa[$i][1]).' - '.ucwords(mb_strtolower($rowa[$i][2])).'</option>';
                        }
                    }
                   ?> 
              </select> 
            </div>
<div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
            </div>

<!-- DIV que contiene una clase oculta  -->
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
