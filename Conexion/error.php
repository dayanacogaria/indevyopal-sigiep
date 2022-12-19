<!DOCTYPE html>
<?php
session_start();
//require_once './Conexion/conexion.php';?>
<html lang="en">
    <head>
        <link rel="icon" type="../image/ico" href="../AAA.ico" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta class="viewport" content="width=device-width, initial-scale=1.0, minimun-scalable=1.0"></meta>
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/jquery-ui.css" type="text/css" media="screen" title="default" />
        <script src="../js/jquery.min.js"></script>
        <script type="text/javascript" language="javascript" src="../js/jquery-1.10.2.js"></script>         
        <!-- Link de css de la libreria css -->
        <link rel="stylesheet" href="../css/select/select2.min.css">
        <link rel="stylesheet" href="../css/select2-bootstrap.min.css"/>
        <style>
            /* Remove the navbar's default margin-bottom and rounded borders */
            .navbar {
              margin-bottom: 0;
              border-radius: 0;
            }    
            /* Set height of the grid so .sidenav can be 100% (adjust as needed) */
            .row.content {height: 510px}    
            /* Set gray background color and 100% height */
            .sidenav {
              padding-top: 20px;
              background-color: #f1f1f1;
              height: 100%;
            }    
            /* Set black background color, white text and some padding */
            footer {
              background-color: #555;
              color: white;
              padding: 15px;
            }    
            /* On small screens, set height to 'auto' for sidenav and grid */
            @media screen and (max-width: 767px) {
              .sidenav {
                height: auto;
                padding: 15px;
              }
              .row.content {height:auto;}
            }
        </style>
        <div> 
            <img src="../RECURSOS/TOP/Fondo---Top.png">
            <div align="right" style="margin-top:-86px">
                <img src="../RECURSOS/TOP/Caja---Cliente.png">
            </div>
            <div align="left" style="margin-top:-87px">
                <img src="../RECURSOS/TOP/Caja---Logo.png">
                <img style="margin-left:-200px" src="../RECURSOS/TOP/Logo---Sigiep.png">
            </div>  
        </div>
        <link href="../skins/page.css" rel="stylesheet" />
        <link href="../skins/blue/accordion-menu.css" rel="stylesheet" />
        <script src="../js/accordion-menu.js"></script>
        <link rel="stylesheet" type="text/css" href="../css/custom.css">
        <script type="text/javascript" src="../js/txtValida.js"></script>
        <style>
            ul li{margin:10px 0;}
        </style>
        <style>
            .login {
                max-width: 330px;
                padding: 15px;
                margin: 0 auto;
            }
            #sha{
                max-width: 340px;
                -webkit-box-shadow: 0px 0px 18px 0px rgba(48, 50, 50, 0.48);
                -moz-box-shadow:    0px 0px 18px 0px rgba(48, 50, 50, 0.48);
                box-shadow:         0px 0px 18px 0px rgba(48, 50, 50, 0.48);
                border-radius: 6%;
            }
        </style>	
        <title>Error</title>
    </head>
    <body style="font-size: 14px">
        <div class="container well" id="sha" style="margin-top:50px;margin-bottom:70px; width: 1000px;max-width: 1000px;">
           
            <h3  align="center" style="margin-bottom: 15px; margin-right: 4px; margin-left: 4px;margin-top:-15px"><strong>ERROR</strong></h3>
              <br/>  
                <img src="../images/No_Conexion.png" style="width:50%">               
                <div class="modal-body" style="margin-top: 8px;display: inline-block;">
                    <h4  align="center" style="margin-bottom: 15px; margin-right: 4px; margin-left: 4px;margin-top:-15px">
                    <p>Por favor Contáctese con su proveedor.<br/><!--Celular: 310 2830998--><br/>
                    Gracias.</p></h4>
                </div>
	</div> 	
	
        <div class="footer">
           <div></div>
<img src="../RECURSOS/FOOTER/Footer.png">
<div align="center" style="margin-top:-55px">
	<img src="../RECURSOS/FOOTER/Drechos-reservados-.png">
</div>

    

    <script src="../js/bootstrap.min.js"></script>

        </div>        
        
    </body>
</html>