<?php 
#################################################################################
#************************       Modificaciones   **************************#
#################################################################################
#13/12/2017 | Erica G. | Arreglo busqueda Teniendo en Cuenta el Año
#01/02/2017 | 10:30 ERICA GONZÁLEZ. //Archivo Agregado
#################################################################################
require_once('Conexion/ConexionPDO.php');
require_once('head_listar.php');
$anno     = $_SESSION['anno'];
$con = new ConexionPDO();
#***Consulta Cuentas**#
if(empty($_GET['id'])){
    $ctas = $con->Listar("SELECT id_unico, codi_cuenta, LOWER(nombre) 
           FROM gf_cuenta WHERE parametrizacionanno = $anno ORDER BY codi_cuenta ASC");
} else {
    $ctas = $con->Listar("SELECT id_unico, codi_cuenta, LOWER(nombre) 
           FROM gf_cuenta WHERE parametrizacionanno = $anno AND md5(id_unico)!='".$_GET['id']."' ORDER BY codi_cuenta ASC");
    $ctb = $con->Listar("SELECT id_unico, codi_cuenta, LOWER(nombre) 
           FROM gf_cuenta WHERE parametrizacionanno = $anno AND md5(id_unico)='".$_GET['id']."'");
    
}
?>
<link href="css/select/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<script src="js/md5.pack.js" type="text/javascript"></script>

<title>Cuentas</title>
</head>
<body>

 
<div class="container-fluid text-center">
  <div class="row content">
    <?php require_once 'menu.php'; ?>
    <div class="col-sm-8 text-left">
    <!--Titulo del formulario-->
      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Cuentas</h2>

      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">

          <form name="form" id="form" accept-charset=""class="form-horizontal" method="POST"  enctype="multipart/form-data">


          <!--Ingresa la información-->
            <div class="form-group" style="margin-top: 20px;">
                <label for="iduvms" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Código Cuenta:</label>
                <select class="select2_single form-control" name="cuenta" id="cuenta" style="width:400px">
                    <?php 
                    if(empty($_GET['id'])){
                        echo '<option value="">Cuenta</option>';
                    } else {
                        echo '<option value ="'.$ctb[0][0].'">'.$ctb[0][1].' - '.ucwords($ctb[0][2]).'</option>';
                    }
                    for($i=0; $i< count($ctas); $i++) {
                        echo '<option value ="'.$ctas[$i][0].'">'.$ctas[$i][1].' - '.ucwords($ctas[$i][2]).'</option>';
                    } ?>
                </select>
                <script>
                    $("#cuenta").change(function(){
                        var cuenta = $("#cuenta").val();
                        if(cuenta==""){
                            
                        } else {
                            document.location = 'buscarCuenta.php?id='+md5(cuenta);
                        }
                    })
                </script>
              
                
            </div>
            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
              
            </div>
            <input type="hidden" name="MM_insert" >
          </form>
        </div>  
     
    </div>
    <div class="col-sm-2 text-center" align="center" style="margin-top:0px">
        <h2 class="titulo" align="center" style=" font-size:17px;">Acciones</h2>
        <div  align="center">
            <?php if(empty($_GET['id'])) { ?>
                <button class="btn btn-primary btnInfo" disabled="true">ELIMINAR</button>
                <?php }  else { ?>
                <a  onclick="eliminar(<?php echo $ctb[0][0]?>);" class="btn btn-primary btnInfo">ELIMINAR</a>
                <?php } ?>
                
                <?php if(empty($_GET['id'])) { ?>
                <button class="btn btn-primary btnInfo" disabled="true">MODIFICAR</button>
                <?php }  else { ?>
                <a href="modificar_GF_CUENTA_P.php?id=<?php echo $_GET['id']; ?>" class="btn btn-primary btnInfo">
                MODIFICAR
                </a>
                <?php } ?>
            <a href="registrar_GF_CUENTA_P.php" class="btn btn-primary btnInfo">REGISTRAR CUENTA</a>          
            <a href="listar_GF_CUENTA_P.php" class="btn btn-primary btnInfo">LISTAR CUENTAS</a>          
            

        </div>
    </div>
  </div>
</div>
 <script src="js/select/select2.full.js"></script>
  <script>
    $(document).ready(function() {
      $(".select2_single").select2({
        allowClear: true
      });
    });
   
  </script>   
  <div class="modal fade" id="myModalEliminar" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>¿Desea eliminar el registro seleccionado de Cuenta?</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="verEliminar" class="btn" style="color: #000; margin-top: 2px" >Aceptar</button>
                    <button type="button" id="verEliminarCancelar" class="btn" style="color: #000; margin-top: 2px" >Cancelar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal1" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>Información eliminada correctamente.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver1" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="myModal2" role="dialog" align="center" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div id="forma-modal" class="modal-header">
                    <h4 class="modal-title" style="font-size: 24; padding: 3px;">Información</h4>
                </div>
                <div class="modal-body" style="margin-top: 8px">
                    <p>No se pudo eliminar la información, el registro seleccionado está siendo utilizado por otra dependencia.</p>
                </div>
                <div id="forma-modal" class="modal-footer">
                    <button type="button" id="ver2" class="btn" style="" data-dismiss="modal" >Aceptar</button>
                </div>
            </div>
        </div>
    </div>
  
      <script>
       function eliminar(id){
        
        $("#myModalEliminar").modal('show');
        $("#verEliminar").click(function () {
            $("#myModalEliminar").modal('hide');
            $.ajax({
                type: "GET",
                url: "json/eliminarClaseP.php?id=" + id,
                success: function (data) {
                    result = JSON.parse(data);
                        if (result == true) {
                            $("#myModal1").modal('show');
                            $("#ver1").click(function () {
                               <?php $_SESSION['cuenta']='';?> 
                               document.location.reload();
                            });
                        } else { 
                            $("#myModal2").modal('show');
                            $("#ver2").click(function () {
                                document.location.reload();
                            });
                        }
                }
            });
        });  
        $("#verEliminarCancelar").click(function () {
            $("#myModalEliminar").modal('hide');
        
        })
       }
      </script>

    
<?php require_once 'footer.php';?>

</body>
</html>

