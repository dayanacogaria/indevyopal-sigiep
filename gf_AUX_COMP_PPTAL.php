<?php 
//llamado a la clase de conexion
  require_once('Conexion/conexion.php');
  //session_start();
?>
<!-- Llamado a la cabecera del formulario -->
<?php require_once 'head.php'; ?>
<title>Auxiliares Comprobantes Presupuestales</title>
</head>
<body>
 
<!-- contenedor principal -->  
<div class="container-fluid text-center">
  <div class="row content">

<!-- Llamado al menu del formulario -->    
  <?php require_once 'menu.php'; ?>
    <div class="col-sm-8 text-left" style="margin-left: -16px;margin-top: -20px"> 
            <h2 align="center" class="tituloform">Auxiliares Comprobantes Presupuestales</h2>
      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
<!-- inicio del formulario --> 
<form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="informes/generar_INF_AUX_PPTALES.php"  target=”_blank”>  
          <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%"></p>
          <input type="hidden" name="id" value="<?php echo $row[0] ?>">

          <div class="form-group">

<!----------Script para invocar Date Picker-->
<script type="text/javascript">
$(document).ready(function() {
   $("#datepicker").datepicker();
});
</script>
<!--- Consulta para Rubro Inicial--->
            <?php 
                $rubroI = "SELECT id_unico,CONCAT(codi_presupuesto,' - ',nombre) AS rubro from gf_rubro_pptal ORDER BY id_unico ASC";
                $rsrubi = $mysqli->query($rubroI);

            ?>
             <div class="form-group" style="margin-top: -5px">
             <label for="Rubi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Rubro Inicial:</label>
             <select name="sltrubi" id="sltrubi" style="height: auto" class="form-control" title="Seleccione Rubro inicial" >
                 <option>Rubro Inicial</option>
             <?php 
                 while ($filarubi= mysqli_fetch_row($rsrubi)) 
             { 
             ?>
                <option value="<?php echo $filarubi[0];?>"><?php echo ucwords($filarubi[1]);?></option>                                
             <?php 
             }
              ?>                                    
             </select>
          </div>
<!--- Fin Consulta para Rubro Inicial--->              
<!--- Consulta para Rubro Final--->              
            <?php 
                $rubroF = "SELECT id_unico,CONCAT(codi_presupuesto,' - ',nombre) AS rubro from gf_rubro_pptal ORDER BY id_unico DESC";
                $rsrubf = $mysqli->query($rubroF);
            ?>
             <div class="form-group" style="margin-top: -5px">
             <label for="Rubf" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Rubro Final:</label>
             <select name="sltrubf" id="sltrubf" style="height: auto" class="form-control" title=
                     "Seleccione Rubro final">
                 <option>Rubro Inicial</option>
             <?php 
                 while ($filarubf = mysqli_fetch_row($rsrubf)) 
             { 
             ?>
                <option value="<?php echo $filarubf[0];?>"><?php echo ucwords($filarubf[1]);?></option>                                
             <?php 
             }
              ?>                                    
             </select>
          </div>
<!-- Fin Consulta para cargar Cuenta Final
<!--Campo para captura de Fecha Inicial-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fechaini" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Inicial:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="fechaini" id="fechaini" step="1" min="2016-01-01" max="2016-12-31" value="<?php echo date("Y-m-d");?>">
           </div>
<!----------Fin Captura de Fecha Inicial-->           
<!--Campo para captura de Fecha Final-->
           <div class="form-group" style="margin-top: -10px;">
                <label for="fechafin" type = "date" class="col-sm-5 control-label"><strong class="obligado"></strong>Fecha Final:</label>
                <input style="width:auto" class="col-sm-2 input-sm" type="date" name="fechafin" id="fechafin" step="1" min="2016-01-01" max="2016-12-31" value="<?php echo date("Y-m-d");?>">
           </div>
<!----------Fin Captura de Fecha Final-->
<!--- Consulta para Cargar Tipo Comprobante Inicial--->
            <?php
                $tci= "SELECT id_unico,CONCAT (codigo,' - ',nombre) AS compp FROM gf_tipo_comprobante_pptal ORDER BY nombre ASC";
                $rsTci = $mysqli->query($tci);
            ?> 
             <div class="form-group" style="margin-top: -5px">
             <label for="Tcompi" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Tipo Comprobante Inicial:</label>
             <select name="sltTci" id="sltTci" class="form-control" title=
                     "Seleccione Tipo comprobante inicial" style="height: 30px">
                 <option value>Tipo Comprobante Inicial</option>
             <?php 
                 while ($filaTci = mysqli_fetch_row($rsTci)) 
             { 
             ?>
                <option value="<?php echo $filaTci[0];?>"><?php echo ucwords($filaTci[1]);?></option>                                
             <?php 
             }
              ?>                                    
             </select>
          </div>
<!------------------------- Consulta para llenar campo Tipo Comprobante final-->
            <?php 
            $tcf= "SELECT id_unico,CONCAT (codigo,' - ',nombre) AS compp FROM gf_tipo_comprobante_pptal ORDER BY nombre DESC";
            $rsTcf = $mysqli->query($tcf);
            ?>
            <div class="form-group" style="margin-top: -5px">
                <label for=Tcompf" class="control-label col-sm-5"><strong class="obligado"></strong>Tipo Comprobante Final:</label>
                <select name="sltTcf" class="form-control" id="sltTcf" title="Seleccione Tipo comprobante final" style="height: 30px" required="">
                <option>Tipo Comprobante Final</option>
                    <?php 
                    while ($filaTcf = mysqli_fetch_row($rsTcf)) { ?>
                    <option value="<?php echo $filaTcf[0];?>"><?php echo ucwords(($filaTcf[1])); ?></option>
                    <?php
                    }
                    ?>
                </select>   
            </div>

<!-- Fin Consultas

            <div class="form-group" style="margin-top: 10px;">
              <label for="no" class="col-sm-5 control-label"></label>
                <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button>
                --> <div class="col-sm-1" style="margin-left:620px">
                    <button type ="submit" class="btn sombra btn-primary" title="Imprimir"><li class="glyphicon glyphicon glyphicon-print"></li></button>
                    </div>

         </form>
<!-- Fin de división y contenedor del formulario -->

        </div>     
    </div>
  </div>
  <!-- Fin del Contenedor principal -->
  <!--Información adicional -->
</div>
<!-- Llamado al pie de pagina -->
<?php require_once 'footer.php'?>  

</body>
</html>