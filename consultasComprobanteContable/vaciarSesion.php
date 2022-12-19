<?php
session_start();
$id = $_REQUEST['id'];
if(!empty($id)){    
    $_SESSION['idNumeroC'] = "";
}        
?>