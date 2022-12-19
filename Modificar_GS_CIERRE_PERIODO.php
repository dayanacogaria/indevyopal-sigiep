<?php
##################MODIFICACIONES#########################
#04/05/2017 | Erica G. | Creación Archivo
######################################################## 
require_once('Conexion/conexion.php');
require_once 'head.php';

###consultas para llenar los campos###
$id =$_GET['id'];
$cp = "SELECT cp.id_unico, pa.id_unico,pa.anno, m.id_unico,m.mes, "
        . "e.id_unico,e.nombre 
        FROM 
            gs_cierre_periodo cp 
        LEFT JOIN 
            gf_parametrizacion_anno pa ON pa.id_unico = cp.anno 
        LEFT JOIN 
            gf_mes m ON m.id_unico = cp.mes 
        LEFT JOIN 
            gs_estado_cierre e ON cp.estado = e.id_unico 
        WHERE md5(cp.id_unico) ='$id'";
$cp=$mysqli->query($cp);
$cp= mysqli_fetch_row($cp);

?>
<title>Modificar Cierre Periodo</title>
<link href="css/select/select2.min.css" rel="stylesheet">
<script src="lib/jquery.js"></script>
<script src="dist/jquery.validate.js"></script>
<style>
label#param-error,#nombre-error, #mes-error, #estado-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}


</style>

<script>


$().ready(function() {
  var validator = $("#form").validate({
        ignore: "",
     
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    rules: {
        param: {
          required: true
        },
        mes: {
          required: true
        },
        sltAnnio: {
          required: true
        }
     }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>

</head>
<body>
<div class="container-fluid text-center">
    <div class="row content">
        <?php require_once 'menu.php'; ?>
        <div class="col-sm-10 text-left">
            <h2 id="forma-titulo3" align="center" style="margin-top: 0px; margin-right: 4px; margin-left: 4px;">Modificar Cierre Periodo</h2>
            <a href="LISTAR_GS_CIERRE_PERIODO.php" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
            <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:-5px;  background-color: #0e315a; color: white; border-radius: 5px"> <?php echo 'Año:'.$cp[2].' - Mes:'.$cp[4] ?></h5>
            <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                <form name="form" id="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="jsonSistema/gs_cierre_periodoJson.php?action=2">
                    <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
                    <div class="form-group">
                        <input type="hidden" name="id" id="id" value="<?php echo $cp[0]?>">
                        <?php 
                            #ESTADO
                            $est="SELECT id_unico, nombre FROM gs_estado_cierre";
                            $est=$mysqli->query($est);?>
                        <label for="param" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Año:</label>
                        <select name="param" id="param" class="select2_single form-control" title="Seleccione Año" required>
                            <?php if(empty($cp[1])) { ?>
                                <option value="">-</option>
                            <?php #AÑO
                                $param = "SELECT id_unico, anno FROM gf_parametrizacion_anno ORDER BY anno DESC";
                                $param = $mysqli->query($param);
                            } else { 
                                #AÑO
                                $param = "SELECT id_unico, anno FROM gf_parametrizacion_anno WHERE id_unico !=$cp[1] ORDER BY anno DESC";
                                $param = $mysqli->query($param);
                                ?>
                                <option value="<?php echo $cp[1]?>"><?php echo $cp[2]?></option>
                            <?php }?>
                          
                          <?php while($rowP = mysqli_fetch_row( $param)){?>
                          <option value="<?php echo $rowP[0] ?>"><?php echo $rowP[1];}?></option>;
                        </select> 
                    </div>
                    <div class="form-group">
                        <label for="mes" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Mes:</label>
                        
                           <?php if(empty($cp[3])) { ?> 
                        <select name="mes" id="mes" class="form-control " title="Seleccione Mes" required>
                            
                        </select>
                           <?php } else { ?>
                        <select name="mes" id="mes" class="form-control select2_single " title="Seleccione Mes" required>
                            <option value="<?php echo $cp[3]?>"><?php echo ucwords(mb_strtolower($cp[4]))?></option>
                           <?php 
                           if(empty($cp[1])){
                               
                           } else {
                            $ms = "SELECT id_unico, mes FROM gf_mes WHERE parametrizacionanno = $cp[1]";
                            $ms =$mysqli->query($ms);
                            while ($rowm = mysqli_fetch_row($ms)) { ?>
                            <option value="<?php echo $rowm[0]?>"><?php echo ucwords(mb_strtolower($rowm[1]))?></option>
                            <?php }
                           }?>
                            </select>
                           <?php } ?>
                         
                    </div>
                    <div class="form-group" >
                        <label for="estado" class="col-sm-5 control-label"><strong style="color:#03C1FB;">*</strong>Estado:</label>
                        <select required="required" name="estado" id="estado" class="form-control select2_single" title="Seleccione Estado" required>
                            <?php if(empty($cp[5])) { ?>
                                <option value="">-</option>
                            <?php #ESTADO
                                $est="SELECT id_unico, nombre FROM gs_estado_cierre";
                                $est=$mysqli->query($est);
                            } else { 
                                #ESTADO
                                $est = "SELECT id_unico, nombre FROM gs_estado_cierre WHERE id_unico !=$cp[5]";
                                $est = $mysqli->query($est);
                                ?>
                                <option value="<?php echo $cp[5]?>"><?php echo ucwords(mb_strtolower($cp[6]))?></option>
                            <?php }?>
                                
                            <?php while($rowE = mysqli_fetch_row($est)){?>
                            <option value="<?php echo $rowE[0] ?>"><?php echo ucwords(mb_strtolower($rowE[1]));}?></option>;
                        </select> 
                    </div>
            
                    <div class="form-group" style="margin-top: 15px;">
                        <label for="no" class="col-sm-5 control-label"></label>
                        <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                    </div>
                    <input type="hidden" name="MM_insert" >
                </form>
            </div>     
        </div>
    </div>
</div>
<script>
    $("#param").change(function(){
        var opcion = '<option value="">Mes</option>';
        if( ($("#param").val() == "") || ($("#param").val() == 0))
        { 
            $('#mes').html(opcion).fadeIn();
        }
        else
        {
          var form_data = {case: 1, param: +$("#param").val()};
          $.ajax({
            type: "POST",
            url: "jsonSistema/consultas.php",
            data: form_data,
            success: function(response)
            {
              if(response != 0)
              {
                
                opcion += response;
                
                $('#mes').html(opcion).fadeIn();
                $("#mes").select2({
                    allowClear: true
                  });
                $('#mes').focus();
              }
              else
              {
                opcion = '<option val="">No hay mes</option>';
                $('#mes').html(opcion).fadeIn();
              }  
            }
          }); 
      }
    });
</script>
<?php require_once 'footer.php' ?>  
<script src="js/select/select2.full.js"></script>
<link rel="stylesheet" href="css/bootstrap-theme.min.css">
<script src="js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
      $(".select2_single").select2({
        
        allowClear: true
      });
     
      
    });
</script>
</body>
</html>


