<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Creación de archivo  : 26/04/2017
// Creado por           : Alexander Numpaque
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Archivos abjuntos
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
require ('head.php');
 ?>
     <title>Registrar Clase Informe</title><!-- ./Titulo  de la pagina -->
   </head><!-- ./head -->
   <body><!-- body -->
     <div class="container-fluid"><!-- container-fluid -->
       <div class="row content"><!-- row content -->
         <?php require ('menu.php'); ?><!-- ./menu -->
         <div class="col-sm-10"><!-- col-sm-10 -->
           <h2 id="forma-titulo3" align="center" style="margin-top:0px">Registrar Clase Informe</h2><!-- ./h2 -->
           <div class="client-form contenedorForma"><!-- client-form contenedorForma -->
              <form name="form" class="form-horizontal" method="POST"  enctype="multipart/form-data" action="json/registrarGNClaseInformeJson.php?action=insert"><!-- form -->
                <p align="center" style="margin-bottom: 25px; margin-top: 25px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p><!--- ./p -->
                <div class="form-group"><!-- form-group -->
                  <label for="nombre" class="col-sm-5 control-label"><!-- col-sm-5 control-label -->
                    <strong class="obligado">*</strong>Nombre:
                  </label><!-- ./col-sm-5 control-label -->
                  <input type="text" name="txtNombre" id="txtNombre" class="form-control" maxlength="100" title="Ingrese el nombre" onkeypress="return txtValida(event,'car')" placeholder="Nombre" required><!-- ./Input -->
                </div><!-- ./form-group -->
                <div class="form-group"><!-- form-group -->
                  <label for="no" class="col-sm-5 control-label"></label><!-- ./col-sm-5 control-label -->
                  <button type="submit" class="btn btn-primary sombra" style=" margin-top: -10px; margin-bottom: 10px; margin-left: 0px;">Guardar</button><!-- ./button -->
                </div><!-- ./form-group -->
              </form><!-- ./form -->
           </div><!-- ./client-form ./contenedorForma -->
         </div><!-- ./col-sm-10 -->
         <?php require ('footer.php'); ?><!-- ./footer -->
       </div><!-- ./row content -->
     </div><!-- ./container-fluid -->
   </body><!-- ./body -->
 </html><!-- ./html -->
