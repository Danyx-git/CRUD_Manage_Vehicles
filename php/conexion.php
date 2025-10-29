<?php
  function Secure($text){return htmlspecialchars((string)$text,ENT_QUOTES,"UTF-8");}

  function Conexion(){

    $host = "localhost";
    $dbname = "bd_vehiculos";
    $user = "root";
    $pass = "";
    
    $conexion = new PDO("");
    $conexion->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    return $conexion;
  }
?>