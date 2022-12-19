<?php 
  require_once 'Conexion/conexion.php';
  session_start();

  $id = $_SESSION['id_tercero'];
  if($_SESSION['perfil'] == "N"){
    $queryTercero = "SELECT CONCAT(t.NombreUno,' ',t.NombreDos,' ', t.ApellidoUno,' ' ,t.ApellidoDos) NOMBRE, CONCAT( ti.Nombre, ': ', t.NumeroIdentificacion) identificacion 
      FROM gf_tercero t 
      LEFT JOIN gf_tipo_identificacion ti ON t.TipoIdentificacion = ti.Id_Unico 
      WHERE t.Id_Unico =$id";
    }
      elseif($_SESSION['perfil'] == "J")
    {
      $queryTercero = "SELECT t.razonsocial, CONCAT( ti.Nombre, ': ', t.NumeroIdentificacion ) identificacion 
      FROM gf_tercero t
      LEFT JOIN gf_tipo_identificacion ti ON t.tipoidentificacion = ti.id_unico 
      LEFT JOIN gf_sucursal s ON t.sucursal = s.id_unico 
      WHERE t.Id_Unico = $id";
    }
   $perfil = $_SESSION['tipo_perfil'];
  $idp= "SELECT id_unico, nombre FROM gf_perfil WHERE nombre = '$perfil'";
  $perfil1 = $mysqli->query($idp);
  $rowP = mysqli_fetch_row($perfil1);
  $idperfil= ($rowP[0]);
  $tercero = $mysqli->query($queryTercero);
  $rowTer = mysqli_fetch_row($tercero);
  $datosTercero= $rowTer[0].'('.$rowTer[1].')';
  require_once 'head_listar.php';

 ?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link href="css/custom1.css" rel="stylesheet">
<script src="dist/jquery.validate.js"></script>
<style>
label#perfil-error, #valoraO-error, #valoranO-error, #valortaO-error, #valornO-error, #valorbO-error, #valorfO-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
</style>

<script>


$().ready(function() {
  var validator = $("#form").validate({
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "form" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<script>

        $(function(){
       
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: 'Anterior',
            nextText: 'Siguiente',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Enero','Febrero','Marzo','Abril', 'Mayo','Junio','Julio','Agosto','Septiembre', 'Octubre','Noviembre','Diciembre'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado'],
            dayNamesShort: ['Dom','Lun','Mar','Mi�','Juv','Vie','S&aacute;b'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
       
        
        $("#valorf").datepicker({changeMonth: true,}).val();
        $("#valorfM").datepicker({changeMonth: true,}).val();
        
});
</script>
<title>Registrar Condici&oacute;n Tercero</title>
</head>
<body>
	<div class="container-fluid text-center">	
		<div class="row content">		
			<?php require_once 'menu.php'; ?>
			<div class="col-sm-8 text-left">
				<h2 id="forma-titulo3" align="center" style="margin-bottom: 5px; margin-right: 4px; margin-left: 4px; margin-top:5px">Condici&oacute;n Tercero</h2>      
        <a href="<?php echo $_SESSION['url'];?>" class="glyphicon glyphicon-circle-arrow-left" style="display:inline-block;margin-left:10px; font-size:150%; vertical-align:middle;text-decoration:none" title="Volver"></a>
        <h5 id="forma-titulo3a" align="center" style="width:92%; display:inline-block; margin-bottom: 10px; margin-right: 4px; margin-left: 4px; margin-top:5px;  background-color: #0e315a; color: white; border-radius: 5px"><?php echo ucwords((strtolower($datosTercero))); 
          ?></h5>
			  <div style="border: 4px solid #020324; border-radius: 10px; margin-left: 4px; margin-right: 4px;" class="client-form">				 	
				 	<form id="form" name="form" class="form-horizontal form-label-left"  method="POST" enctype="multipart/form-data" action="json/registrarCondicionTerJson.php" novalidate style="margin-left:40px">
  				 	<p align="center" style="margin-bottom: 25px; margin-top:10px; margin-left: 30px; font-size: 80%">Los campos marcados con <strong style="color:#03C1FB;">*</strong> son obligatorios.</p>
            <input type="hidden" name="tercero" value="<?php echo $id ?>">
            <div  class="form-group form-inline" style="width:850px" >
              <label for="perfil" class=" control-label col-sm-2" style="width:160px; margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Perfil Condici&oacute;n:</label>
              <select name="perfil" id="perfil" class="form-control col-sm-2" title="Seleccione el perfil condici&oacute;n" required="required" style="width:200px; margin-top:-10px;" onchange="javaScript:valorI();">
                <option value="">Perfil Condici&oacute;n</option>
                <?php 
                $perfilT= "SELECT pc.id_unico, c.nombre FROM gf_perfil_condicion pc LEFT JOIN gf_condicion c ON pc.condicion=c.id_unico WHERE pc.perfil=$idperfil ORDER BY c.nombre ASC";
                $perfilC = $mysqli->query($perfilT);
                while($rowC = mysqli_fetch_row($perfilC)){?>
                <option value="<?php echo $rowC[0] ?>"><?php echo ucwords((strtolower($rowC[1])));}?></option>;
              </select> 
              <!-- Campo por default-->
              <div id="default" style="display:inline; display: block; " >
              <label for="valord" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valord" id="valord" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:200px; margin-top:-10px" required="required" disabled="true">
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
              <!-- Alfab�tico obligatorio-->
              <div id="alfabeticoO" style="display:inline; display: none; " >
              <label for="valoraO" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valoraO" id="valoraO" title="Ingrese el valor de la condici&oacute;n" class="  form-control col-sm-2"  style="width:200px; margin-top:-10px" onkeypress="return txtValida(event,'car')" maxlength="500" required="required" >
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
              <!-- Alfab�tico No obligatorio-->
              <div id="alfabeticoN" style="display:inline; display: none; " >
              <label for="valoraN" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;"></strong>Valor:</label>
                <input type="text" name="valoraN" id="valoraN" title="Ingrese el valor de la condici&oacute;n" class="  form-control col-sm-2"  style="width:200px; margin-top:-10px" onkeypress="return txtValida(event,'car')" maxlength="500" >
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
               <!-- Alfanum�rico Obligatorio-->
              <div id="alfanumericoO" style="display:inline; display: none; " >
              <label for="valoranO" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valoranO" id="valoranO" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:200px; margin-top:-10px" onkeypress="return txtValida(event,'num_car')" maxlength="500" required="required" >
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
              <!-- Alfanum�rico No Obligatorio-->
              <div id="alfanumericoN" style="display:inline; display: none; " >
              <label for="valoranN" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;"></strong>Valor:</label>
                <input type="text" name="valoranN" id="valoranN" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:200px; margin-top:-10px" onkeypress="return txtValida(event,'num_car')" maxlength="500" >
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
              <!-- Texto abierto Obligatorio-->
              <div id="textoAO" style="display:inline; display: none; " >
              <label for="valortaO" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valortaO" id="valortaO" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:200px; margin-top:-10px" maxlength="500" required="required" >
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
              <!-- Texto abierto No Obligatorio-->
              <div id="textoAN" style="display:inline; display: none; " >
              <label for="valortaN" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;"></strong>Valor:</label>
                <input type="text" name="valortaN" id="valortaN" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:200px; margin-top:-10px" maxlength="500">
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
              <!-- Num�rico Obligatorio-->
              <div id="numericoO" style="display:inline; display: none; " >
              <label for="valornO" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valornO" id="valornO" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:200px; margin-top:-10px" onkeypress="return txtValida(event,'num')" maxlength="500" required="required" >
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
              <!-- Num�rico No Obligatorio-->
              <div id="numericoN" style="display:inline; display: none; " >
              <label for="valornN" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;"></strong>Valor:</label>
                <input type="text" name="valornN" id="valornN" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:200px; margin-top:-10px" onkeypress="return txtValida(event,'num')" maxlength="500">
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
              <!-- Booleano Obligatorio-->
              <div id="booleanoO" style=" display:none; margin-top:-15px" >
              <label for="valorbO" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <div style=" display:inline; margin-top:-10px" >
                  <input  type="radio" name="valorbO" id="valorbO"  value="Si" >SI
                  <input  type="radio" name="valorbO" id="valorbO" value="No" checked>NO 
                </div>
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:1px;" >Guardar</button>  
              </div>
              <!-- Booleano No Obligatorio-->
              <div id="booleanoN" style=" display:none; margin-top:-15px" >
              <label for="valorbN" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;"></strong>Valor:</label>
                <div style=" display:inline; margin-top:-10px" >
                  <input  type="radio" name="valorbN" id="valorbN"  value="Si" >SI
                  <input  type="radio" name="valorbN" id="valorbN" value="No" >NO 
                </div>
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:1px;" >Guardar</button>  
              </div>
              <!-- fecha Obligatorio-->
              <div id="fechaO" style="display:inline; display: none; " >
              <label for="valorfO" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valorfO" id="valorfO" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:200px; margin-top:-10px" maxlength="500" required="required" >
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
              <!-- fecha No Obligatorio-->
              <div id="fechaN" style="display:inline; display: none; " >
              <label for="valorfN" class="control-label col-sm-2" style="width:110px;margin-top:-10px"><strong style="color:#03C1FB;"></strong>Valor:</label>
                <input type="text" name="valorfN" id="valorfN" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:200px; margin-top:-10px" maxlength="500">
                <button  type="submit" class="btn btn-primary sombra" style="margin-top:-11px;" >Guardar</button>
              </div>
              <input type="hidden" name="MM_insert" >
            </div>
          </form>       
        </div>
                              
        <div align="center" class="table-responsive" style="margin-left: 5px; margin-right: 5px; margin-top: 10px; margin-bottom: 5px;">          
          <div class="table-responsive" style="margin-left: 5px; margin-right: 5px;">
            <table id="tabla" class="table table-striped table-condensed" class="display" cellspacing="0" width="100%">
              <thead>
                <tr>
                  <td class="oculto">Identificador</td>
                  <td width="7%"></td>
                  <td class="cabeza"><strong>Perfil Condici&oacute;n</strong></td>
                  <td class="cabeza"><strong>Valor</strong></td>
                </tr>
                <tr>
                  <th class="oculto">Identificador</th>
                  <th width="7%"></th>
                  <th>Perfil Condici�n</th>
                  <th>Valor</th>
                </tr>
              </thead>
              <tbody>   
                <?php
                $tipoA2 = "SELECT A.id_unico,A.condicion,B.nombre, C.valor, C.perfilcondicion, B.id_unico 
                          FROM gf_perfil_condicion A 
                          LEFT JOIN  gf_condicion B ON  A.condicion = B.Id_Unico
                          LEFT JOIN gf_condicion_tercero C ON C.perfilcondicion = A.id_unico 
                          WHERE C.tercero = $id";
                $tipoAct2 = $mysqli->query($tipoA2);
                while ($row = mysqli_fetch_row($tipoAct2)) { ?>
                  <tr>               
                    <td style="display: none;"><?php echo $row[0]?></td>
                    <td align="center" class="campos">
                      <a href="#" onclick="javascript:eliminarItem(<?php echo $row[0];?>,<?php echo $id;?>);"><i title="Eliminar" class="glyphicon glyphicon-trash"></i></a>
                      <a onclick="modificarModal(<?php echo $id;?>,<?php echo $row[4];?>,'<?php echo ($row[3])?>');"><i title="Modificar" class="glyphicon glyphicon-edit" ></i></a>
                    </td>
                    <td class="campos"><?php echo ucwords(strtolower($row[2]));?></td>

                    <td class="campos"><?php 
                        echo ucwords(strtolower($row[3]));
                        ?></td>
                  </tr>
                <?php
                }
                 ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-sm-2 text-center" align="center" style="margin-top:-15px">
        <h2 class="titulo" align="center" style=" font-size:17px;">Adicional</h2>
        <div  align="center">
          <a href="Registrar_GF_CONDICION.php" class="btn btn-primary btnInfo">CONDICI&Oacute;N</a>          
        </div>
      </div>
    </div>
	<?php require_once 'footer.php'; ?>
  </div>
    

  <div class="modal fade" id="myModalUpdate" role="dialog" align="center" >
   <link href="css/custom1.css" rel="stylesheet">
   <script src="js/jquery-ui.js"></script>
<script src="dist/jquery.validate.js"></script>
      <script>
$().ready(function() {
  var validator = $("#formM").validate({
    
    errorPlacement: function(error, element) {
      
      $( element )
        .closest( "formM" )
          .find( "label[for='" + element.attr( "id" ) + "']" )
            .append( error );
    },
    rules: {
        valoranMO:"required",
    }
  });

  $(".cancel").click(function() {
    validator.resetForm();
  });
});
</script>
<style>
label#perfilM-error, #valoraMO-error, #valoranMO-error, #valortaMO-error, #valornMO-error, #valorbMO-error, #valorfMO-error{
    display: block;
    color: #155180;
    font-weight: normal;
    font-style: italic;

}
</style>
    <div class="modal-dialog">
      <div class="modal-content client-form1">
        <div id="forma-modal" class="modal-header">       
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Modificar</h4>
        </div>
        <?php 
        $tipoA3 = "SELECT pc.id_unico, c.nombre FROM gf_perfil_condicion pc LEFT JOIN gf_condicion c ON pc.condicion=c.id_unico WHERE pc.perfil=$idperfil ORDER BY c.nombre ASC";
        $tipoAct3 = $mysqli->query($tipoA3);
         ?>

        <div class="modal-body ">
          <form  name="formM" id="formM" method="POST" action="javascript:modificarItem()" >
            <div class="form-group" style="margin-top: 13px;">
              <label for="perfilM" style="display:inline-block; width:140px"><strong style="color:#03C1FB;">*</strong>Perfil Condici&oacuten:</label>
              <select style="display:inline-block; width:250px; margin-bottom:15px; height:40px" name="perfilM" id="perfilM" required="required" class="form-control" title="Seleccione la condicion" onchange="cambiarM();">
                  <?php while ($m = mysqli_fetch_row($tipoAct3)) { ?>
                        <option value="<?php echo $m[0]; ?>">
                          <?php echo ucwords((strtolower($m[1]))); ?>
                        </option>
                  <?php  

                   } ?>
              </select>                                
            </div>

            <div class="form-group" >
                <!-- Alfabetico obligatorio-->
                <div id="alfabeticoMO" style="display:inline; display: none; margin-top: -30px; " >
                  <label for="valoraMO" style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                  <input type="text" name="valoraMO" id="valoraMO"  required="required"  title="Ingrese el valor de la condici&oacute;n" class="  form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px"  onkeypress="return txtValida(event,'car')" maxlength="500"><br/>
                </div>
                <!-- Alfabetico No obligatorio-->
                <div id="alfabeticoMN" style="display:inline; display: none;  margin-top: -30px; " >
                  <label for="valoraMN"style="display:inline-block; width:140px;margin-right:180px;  margin-top:20px;" >Valor:</label>
                    <input type="text" name="valoraMN" id="valoraMN" title="Ingrese el valor de la condici&oacute;n" class="  form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px"  onkeypress="return txtValida(event,'car')" maxlength="500"><br/>
                </div>
                <!-- Alfanumerico Obligatorio-->
              <div id="alfanumericoMO" style="display:inline; display: none; margin-top: -30px; " >
                <label for="valoranMO" style="display:inline-block; width:140px;margin-right:180px;  margin-top:20px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valoranMO" id="valoranMO"  required="required"  title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" onkeypress="return txtValida(event,'num_car')" maxlength="500" required="required" >
                <br/>
              </div>
              <!-- Alfanumerico No Obligatorio-->
              <div id="alfanumericoMN" style="display:inline; display: none; margin-top: -30px; " >
                <label for="valoranMN"style="display:inline-block; width:140px;margin-right:180px;  margin-top:20px;">Valor:</label>
                <input type="text" name="valoranMN" id="valoranMN" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" onkeypress="return txtValida(event,'num_car')" maxlength="500">
                <br/>
              </div>
              <!-- Texto abierto Obligatorio-->
              <div id="textoAMO" style="display:inline; display: none;  margin-top: -30px; " >
                <label for="valortaMO" style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valortaMO" id="valortaMO" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" maxlength="500" required="required" ><br/>
              </div>
              <!--Texto abierto No obligatorio-->
              <div id="textoAMN" style="display:inline; display: none; margin-top: -30px; " >
                <label for="valortaMN" style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;">Valor:</label>
                <input type="text" name="valortaMN" id="valortaMN" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" maxlength="500" ><br/>
              </div>
              <!--Num�rico Obligatorio-->
              <div id="numericoMO" style="display:inline; display: none; margin-top: -30px; " >
                <label for="valornMO" style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valornMO" id="valornMO" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" onkeypress="return txtValida(event,'num')" maxlength="500" required="required" ><br/>
              </div>
              <!-- Num�rico no obligatorio-->
              <div id="numericoMN" style="display:inline; display: none;  margin-top: -30px; " >
                <label for="valornMN"  style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;">Valor:</label>
                <input type="text" name="valornMN" id="valornMN" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" onkeypress="return txtValida(event,'num')" maxlength="500"><br/>
              </div>
              <!--Booleano Obligatorio-->
              <div id="booleanoMO" style="display:inline; display: none;" >
                <label for="valorbMO" style="display:inline-block; width:140px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <div style=" display:inline; margin-right:110px" >
                  <input  type="radio" name="valorbMO" id="valorbMO"  value="Si" >SI
                  <input  type="radio" name="valorbMO" id="valorbMO" value="No" checked>NO 
                </div>
              </div>
              <!--Booleano No Obligatorio-->
              <div id="booleanoMN" style="display:inline; display: none;  " >
                <label for="valorbMN" style="display:inline-block; width:140px;">Valor:</label>
                <div style=" display:inline; margin-right:110px" >
                  <input  type="radio" name="valorbMN" id="valorbMN"  value="Si" >SI
                  <input  type="radio" name="valorbMN" id="valorbMN" value="No">NO 
                  <a onclick="borrarRadio()"><i title="Borrar" class="glyphicon glyphicon-remove"></i></a>
                </div>
              </div>
              <!--Fecha Obligatorio-->
              <div id="fechaMO" style="display:inline; display: none; ">
                <label for="valorfMO" style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;"><strong style="color:#03C1FB;">*</strong>Valor:</label>
                <input type="text" name="valorfMO" id="valorfMO" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px" readonly="true" maxlength="500" required="required" ><br/>
              </div>
              <!--Fecha No Obligatorio-->
              <div id="fechaMN" style="display:inline; display: none;  margin-top: -30px; ">
                <label for="valorfMN" style="display:inline-block; width:140px;margin-right:180px; margin-top:20px;">Valor:</label>
                <input type="text" name="valorfMN" id="valorfMN" title="Ingrese el valor de la condici&oacute;n" class="form-control col-sm-2"  style="width:250px; height:40px; margin-left:230px; margin-top:-30px"  maxlength="500" ><br/>
              </div>
            </div>
             <input type="hidden" id="tercero" name="tercero">  
             <input type="hidden" id="perfilA" name="perfilA">  
        </div>
<script type="text/javascript">
  function borrarRadio(){
    document.getElementsByName("valorbMN")[0].checked = false;
    document.getElementsByName("valorbMN")[1].checked = false;
  }
</script>

        <div id="forma-modal" class="modal-footer">
            <button type="submit" class="btn" style="color: #000; margin-top: 2px">Guardar</button>
          <button class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>       
        </div>
        </form>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal5" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
            <p>Informaci&oacute;n modificada correctamente.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver5" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal6" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>La informaci&oacute;n no se ha podido modificar.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver6" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal7" role="dialog" align="center" >
    <div class="modal-dialog">
      <div class="modal-content">
        <div id="forma-modal" class="modal-header">
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
           <p>El Perfil Condici&oacute;n ingresado ya existe.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver7" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="myModal" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Confirmar</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">      
        <p> &iquest;Desea eliminar el registro seleccionado de Condici&oacute;n Tercero?</p>
      </div>
      <div id="forma-modal" class="modal-footer">
        <button type="button" id="ver" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal" >Aceptar</button>
        <button type="button" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
  </div>
  <div class="modal fade" id="myModal1" role="dialog" align="center" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div id="forma-modal" class="modal-header">
        <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacuten</h4>
      </div>
      <div class="modal-body" style="margin-top: 8px">
        
          <p>Informaci&oacute;n eliminada correctamente.</p>

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
          <h4 class="modal-title" style="font-size: 24; padding: 3px;">Informaci&oacute;n</h4>
        </div>
        <div class="modal-body" style="margin-top: 8px">
          <p>No se pudo eliminar la informaci&oacute;n, el registro seleccionado est&aacute; siendo utilizado por otra dependencia.</p>
        </div>
        <div id="forma-modal" class="modal-footer">
          <button type="button" id="ver2" class="btn" style="color: #000; margin-top: 2px" data-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>
 	<script type="text/javascript" src="js/menu.js"></script>
  <link rel="stylesheet" href="css/bootstrap-theme.min.css">
  <script src="js/bootstrap.min.js"></script>
  <script type="text/javascript">
    $("#ver5").click(function(){
      document.location = "GF_CONDICION_TERCERO.php";
    });
    
    
    </script>
    <script type="text/javascript">
      function modificarModal(tercero,perfil,valor){
        $("#perfilM").val(perfil);
        document.getElementById('tercero').value = tercero;
        document.getElementById('perfilA').value = perfil;
        $.ajax({
      type:"GET",
      url:"json/buscarCondicion.php?id="+perfil,
      success: function (data) {
        resultado = JSON.parse(data);
        var result = resultado["tipo"];
        var obl = resultado["obl"];
        switch (true){
          case (result=='Alfabetico') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'block';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          document.getElementById('valoraMO').value = valor;
          break;

          case (result=='Alfabetico') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'block';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          document.getElementById('valoraMN').value = valor;
          break;
          case (result=='Alfanumerico') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'block';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          document.getElementById('valoranMO').value = valor;
          break;

          case (result=='Alfanumerico') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'block';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          document.getElementById('valoranMN').value = valor;
          break;
          case (result=='Texto abierto') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'block';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          document.getElementById('valortaMO').value = valor;
          break;

          case (result=='Texto abierto') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'block';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          document.getElementById('valortaMN').value = valor;
          break;
          case (result=='Numerico') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'block';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          document.getElementById('valornMO').value = valor;
          break;

          case (result=='Numerico') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'block';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          document.getElementById('valornMN').value = valor;
          break;
          case (result=='Booleano') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'block';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
           if(valor==='Si'){
                document.getElementsByName("valorbMO")[0].checked = true;
            }else {
                document.getElementsByName("valorbMO")[1].checked = true;
            }
          break;

          case (result=='Booleano') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'block';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
           if(valor==='Si'){
                document.getElementsByName("valorbMN")[0].checked = true;
            }else { 
              if(valor==='No'){
                document.getElementsByName("valorbMN")[1].checked = true;
              }else{
              document.getElementsByName("valorbMN")[0].checked = false;
              document.getElementsByName("valorbMN")[1].checked = false;
            } }
          break;
          case (result=='Fecha') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'block';
          document.getElementById('fechaMN').style.display = 'none';
          document.getElementById('valorfMO').value = valor;
          $('#valorfMO').datepicker();
          break;

          case (result=='Fecha') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'block';
          document.getElementById('valorfMN').value = valor;
          $('#valorfMN').datepicker();
          break;
          
        } 
      }
    });
       
          $("#myModalUpdate").modal('show');
      }
      function modificarItem()
        {
          var tercero= document.getElementById('tercero').value; 
          var perfilM= document.getElementById('perfilM').value;
          
          valor = tomarValor(perfilM);
          var perfilA=document.getElementById('perfilA').value;

          $.ajax({
            type:"GET",
            url:"json/modificarCondicionTerJson.php?p1="+tercero+"&p2="+perfilM+"&p3="+valor+"&p4="+perfilA,
            success: function (data) {
              result = JSON.parse(data);
              if(result==true){
                $("#myModal5").modal('show');
                $("#ver5").click(function(){
                  $("#myModal5").modal('hide');
                   $("#myModalUpdate").modal('hide');
                });
              }else{
                if(result=='3'){
                  $("#myModal7").modal('show');
                $("#ver7").click(function(){
                  $("#myModal7").modal('hide');
                  $("#myModalUpdate").modal('hide');
                });
                }else {
                $("#myModal6").modal('show');
                 $("#ver6").click(function(){
                  $("#myModal6").modal('hide');
                  $("#myModalUpdate").modal('hide');
                });
              }
              }
            }
          });
        }
    </script>
    <script type="text/javascript">
    function tomarValor(perfilM){
      $.ajax({
          async: false,
          type:"GET",
          url:"json/buscarCondicion.php?id="+perfilM,
          success: function (data) {
            resultado = JSON.parse(data);
            var result = resultado["tipo"];
            var obl = resultado["obl"];
            switch (true){
              case (result=='Alfabetico') &&(obl=='0'):
                valor = document.getElementById('valoraMO').value;
              break;
              case (result=='Alfabetico') &&(obl=='1'):
                valor = document.getElementById('valoraMN').value;
              break;
              case (result=='Alfanumerico') &&(obl=='0'):
                valor = document.getElementById('valoranMO').value;
              break;
              case (result=='Alfanumerico') &&(obl=='1'):
                valor = document.getElementById('valoranMN').value;
              break;
              case (result=='Texto abierto') &&(obl=='0'):
                valor = document.getElementById('valortaMO').value;
              break;
              case (result=='Texto abierto') &&(obl=='1'):
                valor = document.getElementById('valortaMN').value;
              break;
              case (result=='Numerico') &&(obl=='0'):
                valor = document.getElementById('valornMO').value;
              break;
              case (result=='Numerico') &&(obl=='1'):
                valor = document.getElementById('valornMN').value;
              break;
              case (result=='Booleano') &&(obl=='0'):
                if(document.getElementsByName("valorbMO")[0].checked){
                  valor='Si';
                }else {

                  valor='No';
                }
                
              break;
              case (result=='Booleano') &&(obl=='1'):
              if(document.getElementsByName("valorbMN")[0].checked){
                  valor='Si';
                }else {
                  if(document.getElementsByName("valorbMN")[1].checked){
                    valor='No';
                  }else {
                    valor=' ';
                  }
                }
              break;
              case (result=='Fecha') &&(obl=='0'):
                valor = document.getElementById('valorfMO').value;
              break;
              case (result=='Fecha') &&(obl=='1'):
                valor = document.getElementById('valorfMN').value;
              break;
              
            } 
            return (valor);
          }

          });
      return (valor);

    }
    </script>
    <script type="text/javascript">
  function eliminarItem(id, tercero)
  {
   var result = '';
   $("#myModal").modal('show');
   $("#ver").click(function(){
    $("#myModal").modal('hide');
    $.ajax({
      type:"GET",
      url:"json/eliminarCondicionTer.php?id="+id+"&terc="+tercero,
      success: function (data) {
        result = JSON.parse(data);
        if(result==true){
          $("#myModal1").modal('show');
          $("#ver1").click(function(){
            $("#myModal1").modal('hide');
          document.location = "GF_CONDICION_TERCERO.php";
          });
          
        } else{
          $("#myModal2").modal('show');
          $("#ver2").click(function(){
             $("#myModal2").modal('hide');
            document.location = "GF_CONDICION_TERCERO.php";
          });
        }
        
      }
    });
  });
 }
    </script>
    <script type="text/javascript">
  function valorI(){
     condicion = document.getElementById("perfil").value;
     resultado ='';
    $.ajax({
      type:"GET",
      url:"json/buscarCondicion.php?id="+condicion,
      datatype:"json",
      success: function (data) {
        resultado = JSON.parse(data);
        var result = resultado["tipo"];
        var obl = resultado["obl"];
        switch (true){
          case (result=='Alfabetico') &&(obl=='0'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'block';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
          case (result=='Alfabetico') &&(obl=='1'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'block';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
          case (result=='Alfanumerico') &&(obl=='0'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'block';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
          case (result=='Alfanumerico') &&(obl=='1'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'block';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
          case (result=='Texto abierto') &&(obl=='0'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'block';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
         case (result=='Texto abierto') &&(obl=='1'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'block';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
          case (result=='Numerico') &&(obl=='0'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'block';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
          case (result=='Numerico') &&(obl=='1'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'block';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
          case (result=='Booleano') &&(obl=='0'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'block';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
          case (result=='Booleano') &&(obl=='1'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'block';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
          case (result=='Fecha') &&(obl=='0'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'block';
          document.getElementById('fechaN').style.display = 'none';
           $('#valorfO').datepicker();
          break;
          case (result=='Fecha') &&(obl=='1'):
          document.getElementById('default').style.display = 'none';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'block';
           $('#valorfN').datepicker();
          break;
          default:
          document.getElementById('default').style.display = 'block';
          document.getElementById('alfabeticoO').style.display = 'none';
          document.getElementById('alfabeticoN').style.display = 'none';
          document.getElementById('alfanumericoO').style.display = 'none';
          document.getElementById('alfanumericoN').style.display = 'none';
          document.getElementById('textoAO').style.display = 'none';
          document.getElementById('textoAN').style.display = 'none';
          document.getElementById('numericoO').style.display = 'none';
          document.getElementById('numericoN').style.display = 'none';
          document.getElementById('booleanoO').style.display = 'none';
          document.getElementById('booleanoN').style.display = 'none';
          document.getElementById('fechaO').style.display = 'none';
          document.getElementById('fechaN').style.display = 'none';
          break;
        } 
      }
    });
  }
    </script>
    <script type="text/javascript">
      function cambiarM(){
        condicion = document.getElementById("perfilM").value;
    $.ajax({
      type:"GET",
      url:"json/buscarCondicion.php?id="+condicion,
      success: function (data) {
        resultado = JSON.parse(data);
        var result = resultado["tipo"];
        var obl = resultado["obl"];
        switch (true){
          case (result=='Alfabetico') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'block';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          break;

          case (result=='Alfabetico') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'block';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          break;
          case (result=='Alfanumerico') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'block';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          break;

          case (result=='Alfanumerico') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'block';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          break;
          case (result=='Texto abierto') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'block';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          break;

          case (result=='Texto abierto') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'block';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          break;
          case (result=='Numerico') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'block';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          break;

          case (result=='Numerico') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'block';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          break;
          case (result=='Booleano') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'block';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          break;

          case (result=='Booleano') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'block';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'none';
          break;
          case (result=='Fecha') &&(obl=='0'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'block';
          document.getElementById('fechaMN').style.display = 'none';
          $('#valorfMO').datepicker();
          break;

          case (result=='Fecha') &&(obl=='1'):
          document.getElementById('alfabeticoMO').style.display = 'none';
          document.getElementById('alfabeticoMN').style.display = 'none';
          document.getElementById('alfanumericoMO').style.display = 'none';
          document.getElementById('alfanumericoMN').style.display = 'none';
          document.getElementById('textoAMO').style.display = 'none';
          document.getElementById('textoAMN').style.display = 'none';
          document.getElementById('numericoMO').style.display = 'none';
          document.getElementById('numericoMN').style.display = 'none';
          document.getElementById('booleanoMO').style.display = 'none';
          document.getElementById('booleanoMN').style.display = 'none';
          document.getElementById('fechaMO').style.display = 'none';
          document.getElementById('fechaMN').style.display = 'block';
          $('#valorfMN').datepicker();
          break;
        } 
      }
    });
      }
    </script>
    
  </body>
</html>					