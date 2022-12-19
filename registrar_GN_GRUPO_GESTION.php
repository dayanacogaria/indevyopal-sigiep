<?php
require_once ('head.php');
require_once ('./Conexion/conexion.php');
#session_start();
?>

   <title>Registrar Grupo Gestión</title>
   <link href="css/select/select2.min.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
    </head>
    <body>
        <div class="container-fluid text-center">
              <div class="row content">
                  <?php require_once 'menu.php'; ?>
                  <div class="col-sm-10 text-left">
                  
                      <h2 id="forma-titulo3" align="center" style="margin-bottom: 20px; margin-right: 4px; margin-left: 4px;">Registrar   Grupo Gestión</h2>
                      
                      <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">
                          
                          <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarGrupoGestionJson.php">
                          
                              <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong class="obligado">*</strong> son obligatorios.</p>
                             <div class="form-group" style="margin-top: -10px;">
                                 <label for="nombre" class="col-sm-5 control-label"><strong class="obligado">*</strong>Nombre:</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="grupoSui" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Grupo SUI:</label>
                                <select name="grupoSui" class="select2_single form-control" id="grupoSui" title="Seleccione Equivalente SUI" style="height: 30px">
                                    <option value="">Seleccione Grupo Sui</option>    
                                    <option value="Personal Administrativo">Personal Administrativo</option>
                                    <option value="Personal Operativo Acueducto">Personal Operativo Acueducto</option>
                                    <option value="Personal Operativo Alcantarillado">Personal Operativo Alcantarillado</option>
                                  
                                </select>
                            </div>
                            <div class="form-group" style="margin-top: -10px;">
                                <label for="servicioSui" class="col-sm-5 control-label"><strong style="color:#03C1FB;"></strong>Grupo SUI:</label>
                                <select name="servicioSui" class="select2_single form-control" id="servicioSui" title="Seleccione Equivalente SUI" style="height: 30px">
                                    <option value="">Seleccione Servicio Sui</option>    
                                    <option value="Alcantarillado">Alcantarillado</option>
                                    <option value="Acueducto">Acueducto</option>
                                    <option value="Aseo">Aseo</option>
                                </select>
                            </div>   
                            <div class="form-group" style="margin-top: 10px;">
                               <label for="no" class="col-sm-5 control-label"></label>
                               <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px;margin-left: 0px  ;">Guardar</button>
                            </div>
                          </form>
                      </div>
                  </div>                  
              </div>
        </div>
        <?php require_once './footer.php'; ?>
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