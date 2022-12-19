<?php
require_once ('head_listar.php');
require_once ('Conexion/conexion.php');
?>
<title>Parámetros de Liquidación</title>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.js"></script>
<link rel="stylesheet" href="css/select2.css">
<link rel="stylesheet" href="css/select2-bootstrap.min.css"/>
<style>
.detalle{
		font-size: 11px
	}
	.select2-container .select2-choice{
		height: 30px
	}
    /*Estilos tabla*/
    table.dataTable thead th,table.dataTable thead td{padding:1px 18px;font-size:10px}
    table.dataTable tbody td,table.dataTable tbody td{padding:1px}
    .dataTables_wrapper .ui-toolbar{padding:2px;font-size: 10px;
        font-family: Arial;}
    /*Campos dinamicos*/
    .campoD:focus {
        border-color: #66afe9;
        outline: 0;            
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075), 0 0 8px rgba(102, 175, 233, .6);            
    }
    .campoD:hover{
        cursor: pointer;
    }
    /*Campos dinamicos label*/
    .valorLabel{
        font-size: 8px;
    }
    .valorLabel:hover{
        cursor: pointer;
        color:#1155CC;
    }
    /*td de la tabla*/
    .campos{
        padding: 0px;
        font-size: 8px
    }
	/*Select2*/
    .select2-container .select2-choice{
      height: 30px;
      padding: 0px;
    }
    /*Estilos de campos de erros*/
	label #sltTipoComprobanteInicial-error, #sltTipoComprobanteFinal-error, #txtFechaInicial-error, #txtFechaFinal-error  {
	    display: block;
	    color: #155180;
	    font-weight: normal;
	    font-style: italic;

	}
</style>
<script src="dist/jquery.validate.js"></script>		
</head>
<body>
	<div class="container-fluid">

		<div class="row content">
			<?php 
			require_once('menu.php');
			 ?>
			<div class="col-sm-10 text-left" style="margin-top: -22px">
				<h2 class="tituloform" align="center">Parámetros de Liquidación</h2>
				<div class="client-form contenedorForma" style="margin-top:-7px">
					<form name="form" id="form" method="POST" action="json/registrarParametroLiquidacionJson.php" class="form-horizontal" enctype="multipart/form-data">
						<p align="center" class="parrafoO" style="margin-bottom:-0.00005em">
                            Los campos marcados con <strong class="obligado">*</strong> son obligatorios.
                        </p>
                        <div class="form-group form-inline" style="margin-top: 5px">                            
                            <label for="sltTipoEmpleado" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;"><strong class="obligado">*</strong>Tipo Empleado:</label>
                            <select  class="form-control col-sm-1" id="sltTipoEmpleado" name="sltTipoEmpleado" style="width: 100px;" required="required">
                                <option value="">Tipo Empleado</option>
                                <?php
                                $grupogestion = "SELECT id_unico, nombre FROM gn_tipo_empleado";
                                $grupo = $mysqli->query($grupogestion);
                                while($G=mysqli_fetch_row($grupo)){ ?>
                                    <option value="<?php echo $G[0];?>"><?php echo $G[1]; ?></option>
                                <?php } ?>                                
                            </select>
                            <label for="sltProvision" class="control-label col-sm-2" style="margin-left: -40px; font-size: 12px;"><strong class="obligado"></strong>Tipo Provisión:</label>
                            <select  class="form-control col-sm-1" id="sltProvision" name="sltProvision" style="width: 100px;" >
                                <option value="">Tipo Provisión</option>
                                <?php
                                $provision = "SELECT id_unico, nombre FROM gn_tipo_provision";
                                $pro = $mysqli->query($provision);
                                while($P=mysqli_fetch_row($pro)){ ?>
                                    <option value="<?php echo $P[0];?>"><?php echo $P[1]; ?></option>
                                <?php } ?>                                
                            </select>

                        	<label for="sltsalmin" class="control-label col-sm-2" style="margin-left: -60px; font-size: 12px;">
                        		<strong class="obligado">*</strong>Salario Mínimo:
                        	</label>
                        	<input type="text" class="form-control col-sm-1" id="txtsalmin" name="txtsalmin" style="width: 100px" onkeypress="return txtValida(event,'dec', 'sltsalmin', '2');" onkeyup="formatC('txtsalmin');" placeholder="Salario Mínimo" >
                            <label for="sltauxt" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;">
                                <strong class="obligado">*</strong>Auxilio de Transporte:
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtauxt" name="txtauxt" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txtauxt', '2');" onkeyup="formatC('txtauxt');" placeholder="Auxilio de Transporte">                        	
                        </div>
                        <div class="form-group form-inline" style="margin-top: 5px">
                            <label for="sltprimaA" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;">
                                <strong class="obligado">*</strong>Prima Alimentación:
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtprimaA" name="txtprimaA" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txtprimaA', '2');" onkeyup="formatC('txtprimaA');" placeholder="Prima Alimentación">                            
                            <label for="sltprimaM" class="control-label col-sm-2" style="margin-left: -40px; font-size: 12px;">
                                <strong class="obligado">*</strong>Prima <br> Movilización:
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtprimaM" name="txtprimaM" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txtprimaM', '2');" onkeyup="formatC('txtprimaM');" placeholder="Prima Movilización">
                            <label for="sltasempl" class="control-label col-sm-2" style="margin-left: -60px; font-size: 12px;">
                                Aporte Salud <br> Empleado (%):
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtasempl" name="txtasempl" style="width: 100px" placeholder="Aporte Salud Empleado (%)" onkeypress="return txtValida(event,'dec', 'txtasempl', '2');">
                            <label for="sltasempr" class="control-label col-sm-2" style="margin-left: -19px; font-size: 12px;">
                                Aporte Salud <br> Empresa (%):
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtasempr" name="txtasempr" style="width: 100px" placeholder=" Aporte Salud Empresa (%)" onkeypress="return txtValida(event,'dec', 'txtasempr', '2');">                           
                        </div>                      
                        <div  class="form-group form-inline" style="margin-top: 5px">
                            <label for="sltapempl" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;">
                                Aporte Pensión <br> Empleado (%):
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtapempl" name="txtapempl" style="width: 100px" placeholder="Aporte Pensión Empleado (%)" onkeypress="return txtValida(event,'dec', 'txtapempl', '2');">
                        	<label for="sltapempr" class="control-label col-sm-2" style="margin-left: -40px; font-size: 12px;">
                                Aporte Pensión <br> Empresa (%):
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtapempr" name="txtapempr" style="width: 100px" placeholder="Aporte Pensión Empresa (%)" onkeypress="return txtValida(event,'dec', 'txtapempr', '2');">
                        	<label for="sltafsol" class="control-label col-sm-2" style="margin-left: -60px; font-size: 12px;">
                        		Aporte Fondo <br> Solidaridad (%):
                        	</label>
                        	<input type="text" class="form-control col-sm-1" id="txtafsol" name="txtafsol" style="width: 100px" placeholder="Aporte Fondo Solidaridad (%)" onkeypress="return txtValida(event,'dec', 'txtafsol', '2');">
                            <label for="sltextre" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px; ">
                                Exento  <br> Retención (%):
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtextre" name="txtextre" style="width: 100px" placeholder="Exento Retención (%)" onkeypress="return txtValida(event,'dec', 'txtextre', '2');">
                        </div>
                        <div class="form-group form-inline" style="margin-top: 5px">
                            <label for="sltacacom" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;">
                                Aporte Caja <br> Compensación (%):
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtacacom" name="txtacacom" style="width: 100px" placeholder="Aporte Caja Compensación (%)" onkeypress="return txtValida(event,'dec', 'txtacacom', '2');">
                        	<label for="sltasena" class="control-label col-sm-2" style="margin-left: -40px; font-size: 12px;">
                        		Aporte  <br> SENA (%):</label>
                        	<input type="text" class="form-control col-sm-1" id="txtasena" name="txtasena" style="width: 100px" placeholder="Aporte SENA (%)" onkeypress="return txtValida(event,'dec', 'txtasena', '2');">
                        	<label for="sltaicbf" class="control-label col-sm-2" style="margin-left: -61px; font-size: 12px;">
                        		Aporte <br> ICBF (%):</label>
                        	<input type="text" class="form-control col-sm-1" id="txtaicbf" name="txtaicbf" style="width: 100px" placeholder="Aporte ICBF (%)" onkeypress="return txtValida(event,'dec', 'txtaicbf', '2');">
                            <label for="sltaesap" class="control-label col-sm-2" style="margin-left: -19px; font-size: 12px;">
                                Aporte  <br> ESAP (%):
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtaesap" name="txtaesap" style="width: 100px" placeholder="Aporte ESAP (%)" onkeypress="return txtValida(event,'dec', 'txtaesap', '2');">
                        </div>
                        <div class="form-group form-inline" style="margin-top: 5px">
                            <label for="sltamin" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;">
                                Aporte <br> Ministerio (%): </label>
                            <input type="text" class="form-control col-sm-1" id="txtamin" name="txtamin" style="width: 100px" placeholder="Aporte Ministerio (%)" onkeypress="return txtValida(event,'dec', 'txtamin', '2');">
                        	<label for="sltvuvt" class="control-label col-sm-2" style="margin-left: -40px; font-size: 12px;" >
                        		Valor <br> UVT :
                        	</label>
                        	<input type="text" class="form-control col-sm-1" id="txtvuvt" name="txtvuvt" style="width: 100px" onkeypress="return txtValida(event,'dec', 'valor', '2');" onkeyup="formatC('txtvuvt);" placeholder="Valor UVT">
                        	<label for="slttopal" class="control-label col-sm-2" style="margin-left: -61px; font-size: 12px;">
                        		Tope  <br> Alimentación :
                        	</label>
                        	<input type="text" class="form-control col-sm-1" id="txttopal" name="txttopal" style="width: 100px" onkeypress="return txtValida(event,'dec', 'valor', '2');" onkeyup="formatC('txttopal');" placeholder="Tope Alimentación">                        
                        	<label for="slttopald" class="control-label col-sm-2" style="margin-left: -19px;font-size: 12px;">
                                Tope  Alimentación<br>  Docente :
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txttopald" name="txttopald" style="width: 100px" onkeypress="return txtValida(event,'dec', 'valor', '2');" onkeyup="formatC('txttopald');" placeholder="Tope Alimentación Docente">
                        </div>
                        <div class="form-group form-inline" style="margin-top: 5px">
                            <label for="slttoauxT" class="control-label col-sm-2" style="margin-left: -20px;font-size: 12px;">
                                Tope  Auxilio<br> Transporte :
                            </label>
                            <input type="text" class="form-control col-sm-1" id="slttoauxT" name="slttoauxT" style="width: 100px" onkeypress="return txtValida(event, 'dec', 'slttoauxT', '2');" onkeyup="formatC('slttoauxT');" placeholder="Tope Auxilio Transporte">
                            <label for="txtinca" class="control-label col-sm-2" style="margin-left: -40px;font-size: 12px;">
                                Incapacidad<br>EPS (%) :
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txtinca" name="txtinca" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txtinca', '6');"  placeholder="Incapacidad EPS (%)">
                        	<label for="sltrecnoc" class="control-label col-sm-2" style="margin-left: -60px; font-size: 12px;" >
                        		Recargo Noc<br> (%) :
                        	</label>
                        	<input type="text" class="form-control col-sm-1" id="txtrecnoc" name="txtrecnoc" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txtrecnoc', '2');" onkeyup="formatC('txtvuvt);" placeholder="Recargo Noc (%)">

                        	<label for="sltrecdom" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;">
                        		Recargo D.F.  <br> (%) :
                        	</label>
                        	<input type="text" class="form-control col-sm-1" id="txtrecdom" name="txtrecdom" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txtrecdom', '2');" onkeyup="formatC('txttopal');" placeholder="Recargo D.F. (%)">                        	
                        </div>
                        <div class="form-group form-inline" style="margin-top: 5px">

                            <label for="slthextdo" class="control-label col-sm-2" style="margin-left: -20px;font-size: 12px;">Horas Extras<br>  D.O.(%) :</label>
                            <input type="text" class="form-control col-sm-1" id="txthextdo" name="txthextdo" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txthextdo', '2');" onkeyup="formatC('txttopald');" placeholder="Horas Extras D.O.(%)">

                           <label for="txthextdom" class="control-label col-sm-2" style="margin-left: -40px;font-size: 12px;">
                                Horas Extras<br>D.D.F (%) :
                            </label>
                            <input type="text" class="form-control col-sm-1" id="txthextdom" name="txthextdom" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txthextdom', '6');"  placeholder="Horas Extras D.D.F (%)">                       	
                        	
                        	<label for="slthextno" class="control-label col-sm-2" style="margin-left: -60px; font-size: 12px;" >
                        		Horas Extras<br> N.O. (%) :
                        	</label>
                        	<input type="text" class="form-control col-sm-1" id="txthextno" name="txthextno" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txthextno', '2');" onkeyup="formatC('txtvuvt);" placeholder="Horas Extras N.O. (%)">
                        	<label for="slthextndom" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;">Horas Extras  <br>N.D.F. (%) :</label>
                        	<input type="text" class="form-control col-sm-1" id="txthextndom" name="txthextndom" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txthextndom', '2');" onkeyup="formatC('txttopal');" placeholder="Horas Extras N.D.F. (%)">                        	
                        	
                        </div>
                        <div class="form-group form-inline" style="margin-top: 5px;">
                            <label for="slthextnor" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;">Horas Extras  <br>Noct. Ordinarias (%) :</label>
                            <input type="text" class="form-control col-sm-1" id="slthextnor" name="slthextnor" style="width: 100px" onkeypress="return txtValida(event,'dec', 'slthextnor', '2');" onkeyup="formatC('slthextnor');" placeholder="Horas Extras Noct. Ordinarias(%)">  

                            <label for="txtredondeo" class="control-label col-sm-2" style="margin-left: -40px; font-size: 12px;"> Redondeo: </label>
                            <input type="text" class="form-control col-sm-1" id="txtredondeo" name="txtredondeo" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txtredondeo', '0');" onkeyup="formatC('txttopal');" placeholder="Redondeo">    
                            <label for="txtsaludsena" class="control-label col-sm-2" style="margin-left: -60px;font-size: 12px;">¿Salud SENA?</label>                            
                            <input type="radio"  id="1" name="txtsaludsena" value="1"  style="margin-left: -235px;">SI
                            <input type="radio"  id="2" name="txtsaludsena" value="2" checked>NO

                            <label for="excento" class="control-label col-sm-2" style="margin-left: 80px;font-size: 12px;">¿Exentos <br> Parafiscales?</label>                            
                            <input type="radio"  id="1" name="excento" value="1"  style="margin-left: 175px;">SI
                            <input type="radio"  id="2" name="excento" value="2" checked>NO
                        </div>
                        <div class="form-group form-inline" style="margin-top: 5px;">
                            <label for="diaspv" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;">Días Prima <br>Vacaciones (%) :</label>
                            <input type="text" class="form-control col-sm-1" id="diaspv" name="diaspv" style="width: 100px" onkeypress="return txtValida(event,'dec', 'diaspv', '2');"  placeholder="Días Prima Vacaciones"> 
                            <label for="diasbr" class="control-label col-sm-2" style="margin-left: -20px; font-size: 12px;">Días Bonificación <br>Recreación:</label>
                            <input type="text" class="form-control col-sm-1" id="diasbr" name="diasbr" style="width: 100px" onkeypress="return txtValida(event,'dec', 'diasbr', '2');"  placeholder="Días Bonificación Recreación">   
                            <label for="sltsalmin" class="control-label col-sm-2" style="margin-left: -60px; font-size: 12px;">
                        		<strong></strong>Limite Bonificación<br>  Servicios:
                        	</label>
                        	<input type="text" class="form-control col-sm-1" id="txtlimitbon" name="txtlimitbon" style="width: 100px" onkeypress="return txtValida(event,'dec', 'txtlimitbon', '2');" onkeyup="formatC('txtlimitbon');" placeholder="Limite Bonificación Servicios" >
                            <button type="submit" class="btn btn-primary" id="btnGuardar" name="btnGuardar" title="Guardar Parámetros" style="width: 40px;height:34px;cursor: pointer; margin-left: 162px;" ><span class="glyphicon glyphicon-floppy-disk"></span></button>
                        </div>
                	</form>
                </div>
            </div>
        </div>
